<?php
session_start();
include __DIR__ . '/../db_connect.php'; // Adjust path if needed

// Fetch all upcoming events
$stmt = $conn->prepare("SELECT id, name, date, location, ticket_price, description FROM events ORDER BY date ASC");
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Example: default cart count if not set
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

// Optional: user login info
$user_name = $_SESSION['user_name'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Events - Phantoms Kaaps se Klops</title>
<link rel="stylesheet" href="css/style.css">

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: Arial, sans-serif;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background: #00b7eb;
    color: white;
}

.navbar {
    background-color: #2c3e50;
    padding: 10px 20px;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
}

.nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: auto;
}

.nav-logo {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo-img {
    border-radius: 50%;
    border: 2px solid #ff6b35;
    width: 55px;
    height: 55px;
    object-fit: cover;
}

.logo-text {
    color: #ff6b35;
    font-size: 1.4rem;
    font-weight: bold;
}

.nav-menu {
    list-style: none;
    display: flex;
    gap: 15px;
    margin: 0;
    padding: 0;
}

.nav-link {
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.nav-link:hover {
    color: #ff6b35;
}

/* Main content */
.page-content {
    flex: 1; /* pushes footer down */
    margin-top: 120px; /* leave space for fixed navbar */
    padding: 2rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.event-card {
    background:white;
    color:#333;
    padding:1.5rem;
    border-radius:10px;
    box-shadow:0 4px 15px rgba(0,0,0,0.2);
    margin-bottom:1.5rem;
}

h1,h2,h3 { color:#ff6b35; margin-bottom:1rem; }

.rsvp-button {
    background:#ff6b35;
    color:white;
    border:none;
    padding:12px 24px;
    border-radius:6px;
    cursor:pointer;
    font-weight:bold;
    transition: background 0.3s;
}

.rsvp-button:hover { background:#e55a2b; }

/* Footer */
.site-footer {
    background-color:#2c3e50;
    color:white;
    padding:40px 20px;
    text-align:center;
}

.footer-content h3 { color:#ff6b35; margin-bottom:10px; font-size:1.6rem; }
.footer-content p { margin-bottom:20px; font-size:1rem; color:#ccc; }

/* Social icons */
.social-links {
    display:flex;
    justify-content:center;
    gap:20px;
    margin-bottom:15px;
}

.social-icon {
    font-size:2rem;
    color:white;
    transition: transform 0.3s, color 0.3s;
}

.social-icon:hover { transform:scale(1.2); }
.instagram:hover { color:#E4405F; }
.tiktok:hover { color:#000; }
.facebook:hover { color:#1877F2; }

/* Responsive */
@media(max-width:768px){
    .nav-container { flex-direction: column; align-items: flex-start; }
    .nav-menu { flex-direction: column; width: 100%; margin-top: 10px; gap: 10px; }
}
</style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <img src="images/official logo.jpg" alt="Phantoms Logo" class="logo-img">
            <h2 class="logo-text">Phantoms Kaaps se Klops</h2>
        </div>

        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="about.php" class="nav-link">About</a></li>
            <li><a href="shop.php" class="nav-link">Shop</a></li>
            <li><a href="events.php" class="nav-link">Events</a></li>
            <li><a href="contact.php" class="nav-link">Contact</a></li>
            <?php if($user_name): ?>
                <li><a href="account.php" class="nav-link">Hello, <?= htmlspecialchars($user_name) ?></a></li>
                <li><a href="logout.php" class="nav-link">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="nav-link">Login</a></li>
            <?php endif; ?>
            <li><a href="cart.php" class="nav-link">Cart (<?= $_SESSION['cart_count'] ?>)</a></li>
        </ul>
    </div>
</nav>

<div class="page-content">
    <h1>Upcoming Events</h1>

    <div class="event-card">
        <h3>Phantoms Breakfast Club</h3>
        <p><strong>Date:</strong> November 30, 2025</p>
        <p><strong>Location:</strong> Batavia School Hall Laurier RD, Claremont</p>
        <p><strong>Description:</strong> Join us for a delicious buffet style breakfast hosted by phantoms community development.</p>
        <p><strong>Pricing:</strong> R150 a ticket</p>
        <form method="POST" action="rsvp.php">
            <input type="hidden" name="event_id" value="1">
            <button type="submit" class="rsvp-button">RSVP Now</button>
        </form>
    </div>

    <div class="event-card">
        <h3>Friday Night Fever</h3>
        <p><strong>Date:</strong> November 28, 2025</p>
        <p><strong>Location:</strong> Beacon Hill High School, Mitchells Plain</p>
        <p><strong>Description:</strong> Intimate night of music and collaboration with local artists.</p>
        <p><strong>Pricing:</strong> R50 a ticket</p>
        <form method="POST" action="rsvp.php">
            <input type="hidden" name="event_id" value="2">
            <button type="submit" class="rsvp-button">RSVP Now</button>
        </form>
    </div>

    <div class="event-card">
        <h3>Phantoms Pool Party</h3>
        <p><strong>Date:</strong> December 6, 2025</p>
        <p><strong>Location:</strong> San Souci Girls, Claremont</p>
        <p><strong>Description:</strong> Come and enjoy a fun filled family day with phantoms community development.</p>
        <p><strong>Pricing:</strong> R100 a ticket</p>
        <form method="POST" action="rsvp.php">
            <input type="hidden" name="event_id" value="3">
            <button type="submit" class="rsvp-button">RSVP Now</button>
        </form>
    </div>
</div>

<footer class="site-footer">
    <div class="footer-content">
        <h3>Follow Phantoms Kaaps se Klops</h3>
        <div class="social-links">
            <a href="https://www.instagram.com/phantoms_community" target="_blank" class="social-icon instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://www.tiktok.com/@phantomscommunitydevelop" target="_blank" class="social-icon tiktok"><i class="fab fa-tiktok"></i></a>
            <a href="https://www.facebook.com/share/14MhVDLjPY5/" target="_blank" class="social-icon facebook"><i class="fab fa-facebook"></i></a>
        </div>
        <p>&copy; <?= date("Y") ?> Phantoms Kaaps se Klops. All Rights Reserved.</p>
    </div>
</footer>

<script>
document.querySelectorAll('.rsvp-button').forEach(button => {
    button.addEventListener('click', function(e) {
        alert('Thank you for RSVPing! We will send you confirmation details soon.');
    });
});
</script>

</body>
</html>
