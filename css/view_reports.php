<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include __DIR__ . '/../db_connect.php';

// Example: Replace these with actual queries
$total_sales = 12580; // You can replace this with real sales logic later
$new_orders = 42;
$pending_events = 18;

// Count unique visits (unique IPs)
$result = $conn->query("SELECT COUNT(DISTINCT ip_address) AS total_visits FROM visits");
$row = $result->fetch_assoc();
$total_visits = $row['total_visits'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reports - Admin</title>
<link rel="stylesheet" href="css/style.css">
<style>
body { font-family: Arial, sans-serif; background:#00b7eb; color:white; padding:2rem; }
.report-container { background:rgba(255,255,255,0.9); color:#333; padding:2rem; border-radius:10px; max-width:600px; margin:auto; }
h1,h2 { color:#ff6b35; margin-bottom:1rem; }
.stat { margin-bottom:1rem; font-weight:bold; }
a { color:#ff6b35; text-decoration:none; }
</style>
</head>
<body>

<div class="report-container">
    <h1>Reports</h1>

    <div class="stat">Total Sales: R<?= number_format($total_sales,2) ?></div>
    <div class="stat">New Orders: <?= number_format($new_orders) ?></div>
    <div class="stat">Pending Events: <?= number_format($pending_events) ?></div>
    <div class="stat">Website Visitors: <?= number_format($total_visits) ?></div>

    <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
</div>

</body>
</html>
