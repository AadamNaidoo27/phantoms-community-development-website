<?php
session_start();
include __DIR__ . '/../db_connect.php'; // Adjust path if needed

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- Load Products ---
// Static products
$staticProducts = [
    ['id'=>'static_1','name'=>'T-shirt','price'=>100,'image'=>'tshirt.jpg'],
    ['id'=>'static_2','name'=>'Cap','price'=>60,'image'=>'cap1.jpg'],
    ['id'=>'static_3','name'=>'Bucket Hat','price'=>100,'image'=>'hat.jpg'],
    ['id'=>'static_4','name'=>'Jacket','price'=>450,'image'=>'jacket.jpg'],
    ['id'=>'static_5','name'=>'Tracksuit','price'=>450,'image'=>'Tracksuit.jpeg'],
    ['id'=>'static_6','name'=>'Gear','price'=>250,'image'=>'Gear.jpeg']
];

// Fetch DB products
$dbProducts = [];
$stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if (!isset($row['id'])) $row['id'] = 'db_' . uniqid();
        $dbProducts[] = $row;
    }
}
$stmt->close();
$conn->close();

// Merge products
$allProducts = array_merge($staticProducts, $dbProducts);

// --- Handle Add to Cart ---
if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['id'] ?? '';
    $productName = $_POST['name'] ?? '';
    $productPrice = $_POST['price'] ?? 0;
    $productImage = $_POST['image'] ?? '';

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'name' => $productName,
            'price' => $productPrice,
            'image' => $productImage,
            'quantity' => 1
        ];
    }

    header("Location: shop.php");
    exit();
}

// Count cart items
$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));

// ✅ Define organization info for footer to fix warnings
$org_name = "Phantoms Community Development";
$current_year = date("Y");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shop - Phantoms Community Development</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
.container { max-width: 1200px; margin: auto; padding: 2rem; }
.products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
.product-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; padding: 1rem; }
.product-card img { width: 100%; height: 200px; object-fit: cover; border-radius: 10px; margin-bottom: 1rem; }
.product-card h3 { color: #ff6b35; margin-bottom: 0.5rem; }
.product-card .price { font-weight: bold; font-size: 1.1rem; margin-bottom: 0.5rem; }
.add-to-cart { background: #ff6b35; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
.add-to-cart:hover { background: #e55a2b; }
header { background: #2c3e50; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
header .logo { display: flex; align-items: center; gap: 10px; }
header .logo img { height: 45px; border-radius: 50%; }
header nav a { color: white; text-decoration: none; margin-left: 1.2rem; font-weight: bold; }
header nav a:hover { color: #ff6b35; }

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
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="images/official logo.jpg" alt="Phantoms Logo">
        <h2>Phantoms Community Development</h2>
    </div>
    <nav>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="shop.php" style="color:#ff6b35;">Shop</a>
        <a href="events.php">Events</a>
        <a href="contact.php">Contact</a>
        <a href="cart.php">Cart (<?= $cartCount ?>)</a>
    </nav>
</header>

<main>
    <div class="container">
        <h1 style="text-align:center; color:#2c3e50; margin-bottom:2rem;">Our Merchandise Store</h1>

        <div class="products-grid">
            <?php foreach ($allProducts as $product): ?>
                <div class="product-card">
                    <?php $imagePath = "images/" . ($product['image'] ?? ''); ?>
                    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="price">R<?= number_format($product['price'], 2) ?></p>

                    <form method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                        <input type="hidden" name="price" value="<?= htmlspecialchars($product['price']) ?>">
                        <input type="hidden" name="image" value="<?= htmlspecialchars($product['image']) ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-content">
            <h3>Follow <?= $org_name ?></h3>
            <div class="social-links">
                <a href="https://www.instagram.com/phantoms_community?utm_source=qr&igsh=MTZnbjl3dWcwM25tMg==" target="_blank" class="social-icon instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://www.tiktok.com/@phantomscommunitydevelop?_r=1&_t=ZS-917gHOLIUdg" target="_blank" class="social-icon tiktok"><i class="fab fa-tiktok"></i></a>
                <a href="https://www.facebook.com/share/14MhVDLjPY5/" target="_blank" class="social-icon facebook"><i class="fab fa-facebook"></i></a>
            </div>
            <p>&copy; <?= $current_year ?> <?= $org_name ?>. All Rights Reserved.</p>
        </div>
    </div>
</footer>


</body>
</html>
