<?php
require_once __DIR__ . '/../init.php';
requireAuth();
requireAdmin();
$data = json_decode(file_get_contents('php://input'), true) ?? [];
$id = (int)($data['id'] ?? 0);
if (!$id) jsonResponse(['error' => 'Missing id'], 400);
$fields = [];
$params = ['id' => $id];
$allowed = ['sku','name','description','price','stock','image'];
foreach ($allowed as $col) {
    if (isset($data[$col])) {
        $fields[] = "`$col` = :$col";
        $params[$col] = $data[$col];
    }
}
if (empty($fields)) jsonResponse(['error' => 'No fields to update'], 400);
$sql = "UPDATE products SET " . implode(",", $fields) . " WHERE id = :id";
$pdo = getPDO();
$pdo->prepare($sql)->execute($params);
jsonResponse(['success' => true]);
