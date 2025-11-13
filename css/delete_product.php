<?php
require_once __DIR__ . '/../init.php';
requireAuth();
requireAdmin();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) jsonResponse(['error' => 'Missing id'], 400);
$pdo = getPDO();
$pdo->prepare("DELETE FROM products WHERE id = :id")->execute(['id' => $id]);
jsonResponse(['success' => true]);
