<?php
session_start();

// Handle form submission
$rsvpSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $guests = intval($_POST['guests'] ?? 1);
    $comments = trim($_POST['comments'] ?? '');

    if ($name && $email && $phone && $guests > 0) {
        // WhatsApp redirect
        $orgPhone = "27660848345"; // ✅ Replace with Phantoms Community Development’s actual WhatsApp number (no +, no spaces)
        $message = "New RSVP received:%0A".
                   "Name: $name%0A".
                   "Email: $email%0A".
                   "Phone: $phone%0A".
                   "Guests: $guests%0A".
                   "Comments: $comments";

        // Redirect to WhatsApp
        header("Location: https://wa.me/$orgPhone?text=$message");
        exit;

        // If you still want a success message on page (won’t appear due to redirect):
        $rsvpSuccess = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RSVP - Phantoms Kaaps se Klops</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:Arial,sans-serif;background:#00b7eb;color:white;min-height:100vh;line-height:1.6;}
.navbar{background:#2c3e50;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;}
.nav-link{color:white;text-decoration:none;margin-left:15px;}
.nav-link:hover{color:#ff6b35;}
.container{max-width:700px;margin:100px auto;padding:2rem;background:white;color:#333;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.2);}
h1{color:#ff6b35;text-align:center;margin-bottom:2rem;}
.form-group{margin-bottom:1.5rem;}
label{display:block;margin-bottom:0.5rem;font-weight:bold;color:#ff6b35;}
input, textarea, select{width:100%;padding:12px;border:2px solid #ddd;border-radius:5px;font-size:1rem;transition:border-color 0.3s;}
input:focus, textarea:focus, select:focus{outline:none;border-color:#00b7eb;}
textarea{resize:vertical;}
.submit-button{background:#ff6b35;color:white;border:none;padding:15px 30px;border-radius:6px;cursor:pointer;font-weight:bold;font-size:1.1rem;width:100%;transition:background 0.3s;}
.submit-button:hover{background:#e55a2b;}
.success-message{color:green;font-weight:bold;text-align:center;margin-bottom:1rem;}
.footer{background:#2c3e50;color:white;text-align:center;padding:2rem;margin-top:3rem;}
</style>
</head>
<body>
<nav class="navbar">
    <div><h2>Phantoms Community Development</h2></div>
    <div>
        <a href="index.php" class="nav-link">Home</a>
        <a href="shop.php" class="nav-link">Shop</a>
        <a href="events.php" class="nav-link">Events</a>
        <a href="rsvp.php" class="nav-link">RSVP</a>
        <a href="cart.php" class="nav-link">Cart</a>
    </div>
</nav>

<div class="container">
    <h1>RSVP for Our Event</h1>

    <?php if($rsvpSuccess): ?>
        <p class="success-message">Thank you, your RSVP has been received!</p>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input type="tel" id="phone" name="phone" required>
        </div>

        <div class="form-group">
            <label for="guests">Number of Guests *</label>
            <input type="number" id="guests" name="guests" min="1" value="1" required>
        </div>

        <div class="form-group">
            <label for="comments">Comments / Special Requests</label>
            <textarea id="comments" name="comments" rows="4"></textarea>
        </div>

        <button type="submit" class="submit-button">Submit RSVP</button>
    </form>
</div>

<footer class="footer">
    <p>&copy; 2025 Phantoms Kaaps se Klops. All rights reserved.</p>
</footer>
</body>
</html>
