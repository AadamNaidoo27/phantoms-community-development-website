<?php
session_start();
include __DIR__ . '/../db_connect.php'; // Adjust path if needed

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (!$email || strlen($password) < 8) {
        $error = "Invalid email or password (minimum 8 characters).";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        // Check if user already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$check) {
            die("Prepare failed (SELECT): (" . $conn->errno . ") " . $conn->error);
        }
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            // Hash password before saving
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Prepare failed (INSERT): (" . $conn->errno . ") " . $conn->error);
            }
            $stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $hashedPassword);

            if ($stmt->execute()) {
                $success = "✅ Registration successful! You can now log in.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
        $check->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Phantoms Kaaps se Klops</title>
<link rel="stylesheet" href="css/style.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Arial', sans-serif; background: #00b7eb; color: white; line-height: 1.6; min-height: 100vh; }
.navbar { background: #2c3e50; padding: 1rem 0; position: fixed; width: 100%; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
.nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 2rem; }
.nav-logo h2 { color: #ff6b35; font-size: 1.8rem; font-weight: bold; }
.nav-menu { display: flex; list-style: none; gap: 2rem; }
.nav-link { color: white; text-decoration: none; transition: color 0.3s; font-weight: 500; }
.nav-link:hover { color: #ff6b35; }
.form-container { margin-top: 120px; padding: 2rem; max-width: 400px; margin-left: auto; margin-right: auto; background: rgba(255,255,255,0.95); color: #333; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
.form-group { margin-bottom: 1rem; }
label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #ff6b35; }
input { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px; }
.form-button { background: #ff6b35; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: background 0.3s; }
.form-button:hover { background: #e55a2b; }
.footer { background: #2c3e50; color: white; text-align: center; padding: 2rem; margin-top: 3rem; }
.error { color: red; text-align: center; margin-bottom: 1rem; }
.success { color: green; text-align: center; margin-bottom: 1rem; }
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
    <h1>Create Account</h1>
    <?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <?php if($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

    <form method="POST" action="">
        <div class="form-group"><label for="firstName">First Name:</label><input type="text" id="firstName" name="firstName" required></div>
        <div class="form-group"><label for="lastName">Last Name:</label><input type="text" id="lastName" name="lastName" required></div>
        <div class="form-group"><label for="email">Email:</label><input type="email" id="email" name="email" required></div>
        <div class="form-group"><label for="phone">Phone Number:</label><input type="tel" id="phone" name="phone"></div>
        <div class="form-group"><label for="password">Password:</label><input type="password" id="password" name="password" required></div>
        <div class="form-group"><label for="confirmPassword">Confirm Password:</label><input type="password" id="confirmPassword" name="confirmPassword" required></div>
        <button type="submit" class="form-button">Register</button>
    </form>

    <p style="text-align: center; margin-top: 1rem;">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>
<footer class="footer">
    <p>&copy; 2025 Phantoms Kaaps se Klops. All rights reserved.</p>
</footer>
</body>
</html>
