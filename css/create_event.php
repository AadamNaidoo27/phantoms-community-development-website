<?php
session_start();
include __DIR__ . '/../db_connect.php'; // Adjust path if needed

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = trim($_POST['event_name'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $ticket_price = trim($_POST['ticket_price'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($event_name && $event_date && $location && $ticket_price && $description) {
        // Insert into database safely
        $stmt = $conn->prepare("INSERT INTO events (name, date, location, ticket_price, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssds", $event_name, $event_date, $location, $ticket_price, $description);

        if ($stmt->execute()) {
            $success = "Event '$event_name' created successfully!";
        } else {
            $error = "Error creating event: " . $conn->error;
        }

        $stmt->close();
    } else {
        $error = "Please fill in all fields!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Event - Admin</title>
<link rel="stylesheet" href="css/style.css">
<style>
body { font-family: Arial, sans-serif; background:#00b7eb; color:white; padding:2rem; }
.form-container { background:rgba(255,255,255,0.9); color:#333; padding:2rem; border-radius:10px; max-width:600px; margin:auto; }
input, textarea { width:100%; padding:10px; margin-bottom:1rem; border:2px solid #ddd; border-radius:5px; }
textarea { height:100px; }
button { background:#27ae60; color:white; padding:12px 24px; border:none; border-radius:5px; cursor:pointer; font-weight:bold; }
button:hover { background:#1e8449; }
.error { color:red; margin-bottom:1rem; }
.success { color:green; margin-bottom:1rem; }
</style>
</head>
<body>

<div class="form-container">
    <h1>Create Event</h1>

    <?php if($error) echo "<p class='error'>$error</p>"; ?>
    <?php if($success) echo "<p class='success'>$success</p>"; ?>

    <form method="POST" action="">
        <label>Event Name:</label>
        <input type="text" name="event_name" required>

        <label>Event Date:</label>
        <input type="date" name="event_date" required>

        <label>Location:</label>
        <input type="text" name="location" required>

        <label>Ticket Price (R):</label>
        <input type="number" step="0.01" name="ticket_price" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <button type="submit">Create Event</button>
    </form>

    <p><a href="admin_dashboard.php" style="color:#ff6b35;">Back to Dashboard</a></p>
</div>

</body>
</html>
