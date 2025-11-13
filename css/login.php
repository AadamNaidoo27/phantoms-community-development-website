<?php
session_start();
include __DIR__ . '/../db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        // Fetch user by email only
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check password
            if (password_verify($password, $user['password_hash'])) {

                // Determine role based on email
                if ($email === 'msjattiem16@gmail.com') {
                    $user_role = 'admin';
                } else {
                    $user_role = 'user';
                }

                // Set session variables
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . " " . $user['last_name'];
                $_SESSION['user_role'] = $user_role;

                // Redirect based on role
                if ($user_role === 'admin') {
                    header("Location: admin_dashboard.php");
                    exit;
                } else {
                    header("Location: index.php"); // regular user
                    exit;
                }

            } else {
                $error = "Invalid password!";
            }

        } else {
            $error = "No account found with that email!";
        }

        $stmt->close();
    } else {
        $error = "Please enter both email and password!";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Phantoms Kaaps se Klops</title>
<link rel="stylesheet" href="css/style.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Arial', sans-serif; background: #00b7eb; color: white; min-height: 100vh; line-height: 1.6; }
.navbar { background: #2c3e50; padding: 1rem 0; position: fixed; width: 100%; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
.nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 2rem; }
.nav-logo h2 { color: #ff6b35; font-size: 1.8rem; font-weight: bold; }
.nav-menu { display: flex; list-style: none; gap: 2rem; }
.nav-link { color: white; text-decoration: none; transition: color 0.3s; font-weight: 500; }
.nav-link:hover { color: #ff6b35; }
.form-container { margin-top: 120px; padding: 2rem; max-width: 400px; margin-left: auto; margin-right: auto; background: rgba(255,255,255,0.9); color: #333; border-radius: 10px; }
.form-group { margin-bottom: 1rem; }
label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #ff6b35; }
input, select { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px; }
.form-button { background: #ff6b35; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: background 0.3s; }
.form-button:hover { background: #e55a2b; }
.footer { background: #2c3e50; color: white; text-align: center; padding: 2rem; margin-top: 3rem; }
.error { color: red; text-align: center; margin-bottom: 1rem; }
</style>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo"><h2>Phantoms Kaaps se Klops</h2></div>
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="shop.php" class="nav-link">Shop</a></li>
            <li><a href="events.php" class="nav-link">Events</a></li>
            <li><a href="login.php" class="nav-link">Login</a></li>
        </ul>
    </div>
</nav>

<div class="form-container">
    <h1>Login to Your Account</h1>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="role">Login As:</label>
            <select name="role" id="role" required>
                <option value="">Select Role</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" class="form-button">Login</button>
    </form>

    <p style="text-align:center; margin-top:1rem;">
        Don't have an account? <a href="register.php">Register here</a>
    </p>
</div>

<footer class="footer">
    <p>&copy; 2025 Phantoms Kaaps se Klops. All rights reserved.</p>
</footer>
</body>
</html>
