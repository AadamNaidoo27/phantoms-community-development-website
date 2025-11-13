<?php
require_once __DIR__ . '/../init.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) jsonResponse(['error' => 'Missing product id'], 400);
$stmt = getPDO()->prepare("SELECT id, sku, name, description, price, stock, image FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();
if (!$product) jsonResponse(['error' => 'Not found'], 404);
jsonResponse(['product' => $product]);
