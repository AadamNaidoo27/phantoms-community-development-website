<?php
session_start();

// Example: Set a default cart count if not already in session
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

// Example: Set a logged-in user name (optional)
$user_name = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phantoms Kaaps se Klops - Official Website</title>

    <!-- Font Awesome for social icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Arial',sans-serif; background:#00b7eb; color:white; line-height:1.6; min-height:100vh; }

        /* Navbar */
        .navbar { background:#2c3e50; padding:1rem 0; position:fixed; width:100%; top:0; z-index:1000; box-shadow:0 2px 10px rgba(0,0,0,0.3); }
        .nav-container { max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; padding:0 2rem; flex-wrap:nowrap; }
        .nav-logo { display:flex; align-items:center; gap:10px; }
        .logo-img { border-radius:50%; object-fit:cover; border:2px solid #ff6b35; transition:transform 0.3s ease; width:60px; height:60px; }
        .logo-text { color:#ff6b35; font-size:1.5rem; font-weight:bold; margin:0; }
        .nav-menu { display:flex; list-style:none; gap:2rem; }
        .nav-link { color:white; text-decoration:none; transition:color 0.3s; font-weight:500; }
        .nav-link:hover { color:#ff6b35; }

        /* Hero */
        .hero { background:linear-gradient(rgba(44,62,80,0.8), rgba(52,73,94,0.8)); height:80vh; display:flex; align-items:center; justify-content:center; text-align:center; color:white; margin-top:60px; }
        .hero-content h1 { font-size:3rem; margin-bottom:1rem; text-shadow:2px 2px 4px rgba(0,0,0,0.5); }

        /* Main sections */
        main, .events-container, .form-container, .contact-container, .admin-container { margin-top:80px; padding:2rem; max-width:1200px; margin-left:auto; margin-right:auto; }

        /* Products */
        .products-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:2rem; margin-top:2rem; }
        .product-card { background:white; color:#333; padding:1.5rem; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.2); text-align:center; transition:transform 0.3s; }
        .product-card:hover { transform:scale(1.03); }
        .product-image { width:180px; height:180px; object-fit:cover; border-radius:10px; margin-bottom:1rem; }
        .product-description { font-size:0.95rem; margin-bottom:1rem; color:#555; }
        .cta-button, .add-to-cart, .rsvp-button, .form-button, .submit-button {
            background:#ff6b35; color:white; border:none; padding:12px 24px;
            border-radius:6px; cursor:pointer; font-weight:bold; transition:background 0.3s;
        }
        .cta-button:hover, .add-to-cart:hover, .rsvp-button:hover, .form-button:hover, .submit-button:hover { background:#e55a2b; }

        /* About Section */
        .about-section { background:white; color:#333; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.2); padding:2rem; margin-top:3rem; text-align:center; }
        .about-section h2 { color:#ff6b35; }
        .about-section p { margin:1rem 0; font-size:1rem; }
        .learn-more { background:#00b7eb; color:white; padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:bold; transition:background 0.3s; }
        .learn-more:hover { background:#009fcc; }

        /* Events */
        .events-list { margin-top:2rem; }
        .event-card { background:white; color:#333; padding:1.5rem; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.2); margin-bottom:1.5rem; }

        /* ===== FOOTER ===== */
        .site-footer {
            background-color:#2c3e50;
            color:white;
            padding:40px 20px;
            text-align:center;
            margin-top:50px;
        }
        .footer-container { max-width:1000px; margin:auto; }
        .footer-content h3 { color:#ff6b35; margin-bottom:10px; font-size:1.6rem; }
        .footer-content p { margin-bottom:20px; font-size:1rem; color:#ccc; }

        /* Social links */
        .social-links { display:flex; justify-content:center; gap:20px; margin-bottom:15px; }
        .social-icon { font-size:2rem; color:white; transition:transform 0.3s, color 0.3s; }
        .social-icon:hover { transform:scale(1.2); }
        .instagram:hover { color:#E4405F; }
        .tiktok:hover { color:#000; }
        .facebook:hover { color:#1877F2; }

        .footer-bottom {
            border-top:1px solid #444;
            margin-top:20px;
            padding-top:10px;
            font-size:0.9rem;
            color:#bbb;
        }

        @media(max-width:768px){
            .nav-menu{ flex-direction:column; gap:1rem; }
            .nav-container{ padding:0 1rem; }
            .hero-content h1{ font-size:2rem; }
            .products-grid{ grid-template-columns:1fr; }
            .logo-img { width:40px; height:40px; }
            .logo-text { font-size:1.1rem; }
        }
    </style>
</head>

<body>
<!-- ===== NAVBAR ===== -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <img src="images/official logo.jpg" alt="Phantoms Logo" class="logo-img">
            <h2 class="logo-text">Phantoms Community Development</h2>
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

<!-- ===== HERO SECTION ===== -->
<section class="hero">
    <div class="hero-content">
        <h1>Welcome to Phantoms Community Development</h1>
        <p>Enchanting Movements, Celebrate The Rhythm</p>
        <button class="cta-button">Explore Music</button>
    </div>
</section>

<!-- ===== FEATURED PRODUCTS ===== -->
<section class="featured-products">
    <h2 style="text-align:center;">Featured Merchandise</h2>
    <div class="products-grid">
        <div class="product-card">
            <img src="images/tshirt.jpg" alt="T-shirt" class="product-image">
            <h3>T-shirt</h3>
            <p class="product-description">Show your support with our premium cotton Phantoms T-shirt.</p>
            <p class="price">R100.00</p>
            <form method="POST" action="add_to_cart.php">
                <input type="hidden" name="product_id" value="1">
                <button type="submit" class="add-to-cart">Add to Cart</button>
            </form>
        </div>

        <div class="product-card">
            <img src="images/cap1.jpg" alt="Cap" class="product-image">
            <h3>Cap</h3>
            <p class="product-description">Keep cool and stylish with the official Phantoms cap.</p>
            <p class="price">R60.00</p>
            <form method="POST" action="add_to_cart.php">
                <input type="hidden" name="product_id" value="2">
                <button type="submit" class="add-to-cart">Add to Cart</button>
            </form>
        </div>

        <div class="product-card">
            <img src="images/Tracksuit.jpeg" alt="Official Tracksuit" class="product-image">
            <h3>Official Tracksuit</h3>
            <p class="product-description">The official tracksuit for the 2025 Phantoms year.</p>
            <p class="price">R450.00</p>
            <form method="POST" action="add_to_cart.php">
                <input type="hidden" name="product_id" value="3">
                <button type="submit" class="add-to-cart">Add to Cart</button>
            </form>
        </div>
    </div>
</section>

<!-- ===== ABOUT SECTION ===== -->
<section class="about-section">
    <h2>About Phantoms Community Development</h2>
    <p>Phantoms Community Development is a non-profit organization dedicated to uplifting and empowering local communities through art, music, and education. We believe in nurturing creativity, building unity, and fostering positive social change through every beat and project we lead.</p>
    <a href="about.php" class="learn-more">Learn More</a>
</section>

<!-- ===== EVENTS SECTION ===== -->
<section class="upcoming-events">
    <h2 style="text-align:center;">Upcoming Events</h2>
    <div class="events-list">
        <div class="event-card">
            <h3>Friday Night Fever</h3>
            <p class="event-date">November 28, 2025</p>
            <p class="event-location">Beacon Hill School, Mitchells Plain</p>
            <button class="rsvp-button">RSVP</button>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
  <div class="footer-container">
    <div class="footer-content">
      <h3>Follow Phantoms Community Development</h3>
      <p>Stay connected with us through our social platforms</p>

      <div class="social-links">
        <a href="https://www.instagram.com/phantoms_community?utm_source=qr&igsh=MTZnbjl3dWcwM25tMg==" 
           target="_blank" 
           class="social-icon instagram" 
           title="Follow us on Instagram">
          <i class="fab fa-instagram"></i>
        </a>

        <a href="https://www.tiktok.com/@phantomscommunitydevelop?_r=1&_t=ZS-917gHOLIUdg" 
           target="_blank" 
           class="social-icon tiktok" 
           title="Follow us on TikTok">
          <i class="fab fa-tiktok"></i>
        </a>

        <a href="https://www.facebook.com/share/14MhVDLjPY5/" 
           target="_blank" 
           class="social-icon facebook" 
           title="Like us on Facebook">
          <i class="fab fa-facebook"></i>
        </a>
      </div>
    </div>
    <p class="footer-bottom">&copy; <?php echo date("Y"); ?> Phantoms Community Development. All Rights Reserved.</p>
  </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cta = document.querySelector('.cta-button');
    cta.addEventListener('click', function() {
        document.querySelector('.featured-products').scrollIntoView({ behavior: 'smooth' });
    });
});
</script>
</body>
</html>
