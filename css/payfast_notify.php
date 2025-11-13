<?php
require_once __DIR__ . '/../init.php';
$raw = file_get_contents('php://input');
parse_str($raw, $pfData);
$pdo = getPDO();
$payloadJson = json_encode($pfData);
$gatewayRef = $pfData['pf_payment_id'] ?? null;
$orderId = isset($pfData['m_payment_id']) ? (int)$pfData['m_payment_id'] : null;
$pdo->prepare("INSERT INTO payments (order_id, gateway, gateway_payload, status, gateway_reference) VALUES (:order_id, :gateway, :payload, :status, :ref)")
    ->execute(['order_id' => $orderId, 'gateway' => 'payfast', 'payload' => $payloadJson, 'status' => $pfData['payment_status'] ?? 'unknown', 'ref' => $gatewayRef]);
$validateUrl = PAYFAST_VERIFY_URL;
$ch = curl_init($validateUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$validateResponse = curl_exec($ch);
$curlErr = curl_error($ch);
curl_close($ch);
if ($curlErr || trim($validateResponse) !== 'VALID') {
    http_response_code(400);
    echo 'Invalid';
    exit;
}
$merchant_id_ok = (isset($pfData['merchant_id']) && $pfData['merchant_id'] == PAYFAST_MERCHANT_ID);
if (!$merchant_id_ok) {
    http_response_code(400);
    echo 'Invalid merchant';
    exit;
}
$payment_status = strtolower($pfData['payment_status'] ?? '');
if ($payment_status === 'complete' || $payment_status === 'paid') {
    $pdo->prepare("UPDATE orders SET status = 'paid', payfast_payment_id = :pf WHERE id = :id")
        ->execute(['pf' => $gatewayRef, 'id' => $orderId]);
} else {
    $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id")
        ->execute(['status' => $payment_status ?: 'pending', 'id' => $orderId]);
}
http_response_code(200);
echo 'OK';
