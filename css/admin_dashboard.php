<?php
session_start();
include __DIR__ . '/../db_connect.php'; // Adjust path if needed

// Check if admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['user_name'] ?? 'Admin';

// Fetch events dynamically
$events_result = $conn->query("SELECT * FROM events ORDER BY date ASC");

// Example stats (replace with real queries if needed)
$total_visitors = 1248;
$new_orders = 42;
$total_revenue = 12580;
$pending_events = $events_result->num_rows;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Phantoms Kaaps se Klops</title>
<link rel="stylesheet" href="css/style.css">
<style>
body { font-family:'Arial',sans-serif; background:#00b7eb; color:white; margin:0; padding:0; }
.navbar { background:#2c3e50; padding:1rem; position:fixed; width:100%; top:0; z-index:1000; display:flex; justify-content:space-between; align-items:center; }
.nav-link { color:white; text-decoration:none; margin-left:1rem; }
.nav-link:hover { color:#ff6b35; }
.admin-container { margin-top:120px; padding:2rem; max-width:1200px; margin-left:auto; margin-right:auto; }
.dashboard-card { background:white; color:#333; padding:1.5rem; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.2); display:inline-block; width:23%; margin-right:1%; text-align:center; }
.admin-section { background:white; color:#333; padding:1.5rem; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.2); margin-bottom:1.5rem; }
.dashboard-btn { display:inline-block; padding:10px 20px; margin:5px; color:white; text-decoration:none; border-radius:4px; font-weight:bold; transition:opacity 0.3s; }
.dashboard-btn:hover { opacity:0.8; }
table { width:100%; border-collapse:collapse; }
th, td { padding:10px; border:1px solid #ddd; text-align:left; }
th { background:#ff6b35; color:white; }
button.delete-btn { background:#e74c3c; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer; }
button.edit-btn { background:#3498db; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer; }
button.delete-btn:hover, button.edit-btn:hover { opacity:0.8; }
</style>
</head>
<body>

<nav class="navbar">
    <div class="nav-logo"><h2>Phantoms Admin</h2></div>
    <div>
        <a href="index.php" class="nav-link">View Site</a>
        <a href="logout.php" class="nav-link">Logout</a>
    </div>
</nav>

<div class="admin-container">
    <h1>Welcome, <?= htmlspecialchars($admin_name) ?></h1>

    <div class="dashboard-cards">
        <div class="dashboard-card"><div><?= number_format($total_visitors) ?></div>Total Visitors</div>
        <div class="dashboard-card"><div><?= number_format($new_orders) ?></div>New Orders</div>
        <div class="dashboard-card"><div>R<?= number_format($total_revenue,2) ?></div>Total Revenue</div>
        <div class="dashboard-card"><div><?= number_format($pending_events) ?></div>Upcoming Events</div>
    </div>

    <div class="admin-section">
        <h2>Quick Actions</h2>
        <a href="add_product.php" class="dashboard-btn" style="background:#3498db;">Add New Product</a>
        <a href="create_event.php" class="dashboard-btn" style="background:#27ae60;">Create Event</a>
        <a href="view_reports.php" class="dashboard-btn" style="background:#e74c3c;">View Reports</a>
    </div>

    <div class="admin-section">
        <h2>Upcoming Events</h2>
        <?php if($events_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Price (R)</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($event = $events_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['name']) ?></td>
                        <td><?= htmlspecialchars($event['date']) ?></td>
                        <td><?= htmlspecialchars($event['location']) ?></td>
                        <td><?= number_format($event['ticket_price'],2) ?></td>
                        <td><?= htmlspecialchars($event['description']) ?></td>
                        <td>
                            <a href="edit_event.php?id=<?= $event['id'] ?>" class="edit-btn">Edit</a>
                            <a href="delete_event.php?id=<?= $event['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No upcoming events.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
