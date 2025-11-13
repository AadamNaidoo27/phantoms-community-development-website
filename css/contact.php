<?php
session_start();

$feedback = "";
$whatsappNumber = "27660848345"; // Replace with your WhatsApp number including country code (no + or spaces)

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $subject && $message) {
        // Encode message for WhatsApp URL
        $text = "New feedback from Phantoms Kaaps se Klops website:\n\n";
        $text .= "Name: " . $name . "\n";
        $text .= "Email: " . $email . "\n";
        $text .= "Subject: " . $subject . "\n";
        $text .= "Message: " . $message;

        $encodedText = urlencode($text);

        // WhatsApp link
        $whatsappLink = "https://wa.me/{$whatsappNumber}?text={$encodedText}";

        // Feedback message
        $feedback = "✅ Thank you, " . htmlspecialchars($name) . "! Click the button below to send your message via WhatsApp.";
    } else {
        $feedback = "⚠️ Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact – Phantoms Kaaps se Klops</title>
<link rel="stylesheet" href="css/style.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Arial', sans-serif; background: #00b7eb; color: white; line-height: 1.6; min-height: 100vh; }

/* Navbar */
.navbar {
    background: #2c3e50;
    padding: 1rem 0;
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}
.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 2rem;
}
.nav-logo { display:flex; align-items:center; gap:10px; }
.nav-logo h2 { color: #ff6b35; font-size: 1.8rem; font-weight: bold; }
.nav-logo img { height:40px; border-radius:50%; border:2px solid #ff6b35; }

.nav-menu { display: flex; list-style: none; gap: 2rem; }
.nav-link { color: white; text-decoration: none; font-weight: 500; transition: color 0.3s; }
.nav-link:hover { color: #ff6b35; }

/* Contact form */
main, .contact-container { margin-top: 100px; padding: 2rem; max-width: 800px; margin-left: auto; margin-right: auto; }
.contact-container { background: white; color: #333; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); padding: 2rem; }
h1, h2 { color: #ff6b35; margin-bottom: 1rem; }

.form-group { margin-bottom: 1rem; }
label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #ff6b35; }
input, textarea { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px; background: white; color: #333; }
textarea { height: 120px; resize: none; }

.submit-button, .whatsapp-button { 
    background: #ff6b35; 
    color: white; 
    border: none; 
    padding: 12px 24px; 
    border-radius: 6px; 
    cursor: pointer; 
    font-weight: bold; 
    transition: background 0.3s; 
    margin-top: 1rem;
    text-decoration: none;
    display: inline-block;
}
.submit-button:hover, .whatsapp-button:hover { background: #e55a2b; }

.footer { background: #2c3e50; color: white; text-align: center; padding: 2rem; margin-top: 3rem; }
.feedback { text-align: center; font-weight: bold; margin-bottom: 1rem; color: #2c3e50; }
</style>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <img src="images/official logo.jpg" alt="Phantoms Logo">
            <h2>Phantoms community development</h2>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="shop.php" class="nav-link">Shop</a></li>
            <li><a href="events.php" class="nav-link">Events</a></li>
            <li><a href="login.php" class="nav-link">Login</a></li>
            <li><a href="cart.php" class="nav-link">Cart</a></li>
        </ul>
    </div>
</nav>

<div class="contact-container">
    <h1>Contact Us & Feedback</h1>
    <p>We'd love to hear from you! Send us a message and we'll respond as soon as possible.</p>

    <?php if ($feedback): ?>
        <div class="feedback"><?= $feedback ?></div>
        <?php if (!empty($whatsappLink)): ?>
            <a href="<?= $whatsappLink ?>" target="_blank" class="whatsapp-button">Send via WhatsApp</a>
        <?php endif; ?>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Your Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>
        </div>
        <div class="form-group">
            <label for="message">Your Message:</label>
            <textarea id="message" name="message" required></textarea>
        </div>
        <button type="submit" class="submit-button">Send Message</button>
    </form>
</div>

<footer class="footer">
    <p>&copy; 2025 Phantoms Kaaps se Klops. All rights reserved.</p>
</footer>
</body>
</html>
