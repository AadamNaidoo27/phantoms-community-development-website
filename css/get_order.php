<?php
require_once __DIR__ . '/../init.php';
requireAuth();
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if (!$orderId) jsonResponse(['error' => 'Missing order_id'], 400);
$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :uid");
$stmt->execute(['id' => $orderId, 'uid' => $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) jsonResponse(['error' => 'Not found'], 404);
$stmtItems = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id WHERE oi.order_id = :order_id");
$stmtItems->execute(['order_id' => $orderId]);
$items = $stmtItems->fetchAll();
jsonResponse(['order' => $order, 'items' => $items]);
