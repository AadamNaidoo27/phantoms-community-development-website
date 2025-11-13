<?php
session_start();
include __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $email) {
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $email, $hashed_password, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
            $stmt->bind_param("ssi", $username, $email, $user_id);
        }

        if ($stmt->execute()) {
            $success = "Account updated successfully!";
            $_SESSION['username'] = $username;
        } else {
            $error = "Error updating account: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "Username and Email cannot be empty!";
    }
}

// Fetch current info
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_username, $current_email);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Account Management</title>
<style>
body { font-family: Arial, sans-serif; background:#00b7eb; color:white; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
.form-container { background:white; color:#333; padding:2rem; border-radius:10px; width:100%; max-width:400px; box-shadow:0 5px 15px rgba(0,0,0,0.2); }
input { width:100%; padding:10px; margin-bottom:1rem; border:2px solid #ddd; border-radius:5px; }
button { background:#27ae60; color:white; padding:12px 24px; border:none; border-radius:5px; cursor:pointer; font-weight:bold; width:100%; }
button:hover { background:#1e8449; }
.error { color:red; margin-bottom:1rem; }
.success { color:green; margin-bottom:1rem; }
h2 { text-align:center; margin-bottom:1rem; color:#ff6b35; }
</style>
</head>
<body>

<div class="form-container">
    <h2>Manage Account</h2>
    <?php if($error) echo "<p class='error'>$error</p>"; ?>
    <?php if($success) echo "<p class='success'>$success</p>"; ?>
    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($current_username) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($current_email) ?>" required>

        <label>New Password (leave blank to keep current):</label>
        <input type="password" name="password">

        <button type="submit">Update Account</button>
    </form>

    <p><a href="<?= $_SESSION['user_role'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php' ?>" style="color:#ff6b35;">Back to Dashboard</a></p>
    <p><a href="logout.php" style="color:red;">Logout</a></p>
</div>

</body>
</html>
