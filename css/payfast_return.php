<?php
require_once __DIR__ . '/../init.php';
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if (!$order_id) {
    echo "Invalid order reference.";
    exit;
}
$pdo = getPDO();
$stmt = $pdo->prepare("SELECT id, status FROM orders WHERE id = :id");
$stmt->execute(['id' => $order_id]);
$order = $stmt->fetch();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Payment result</title></head>
<body>
<h1>Thank you</h1>
<p>Order #<?=htmlspecialchars($order_id)?>. Current status: <?=htmlspecialchars($order['status'] ?? 'unknown')?></p>
<p>We will email you when payment is confirmed.</p>
</body>
</html>
