<?php
require_once __DIR__ . '/../init.php';
$pdo = getPDO();
$stmt = $pdo->query("SELECT id, sku, name, description, price, stock, image FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
jsonResponse(['products' => $products]);
