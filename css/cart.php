<?php
session_start();
include __DIR__ . '/../db_connect.php'; // For products DB, if needed

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Load all products (static + DB) to calculate prices
$staticProducts = [
    'static_1'=>['name'=>'T-shirt','price'=>100,'image'=>'tshirt.jpg'],
    'static_2'=>['name'=>'Cap','price'=>60,'image'=>'cap1.jpg'],
    'static_3'=>['name'=>'Bucket Hat','price'=>100,'image'=>'hat.jpg'],
    'static_4'=>['name'=>'Jacket','price'=>450,'image'=>'jacket.jpg'],
    'static_5'=>['name'=>'Tracksuit','price'=>450,'image'=>'Tracksuit.jpeg'],
    'static_6'=>['name'=>'Gear','price'=>250,'image'=>'Gear.jpeg']
];

$dbProducts = [];
$stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if (!isset($row['id'])) $row['id'] = 'db_' . uniqid();
        $dbProducts[$row['id']] = $row;
    }
}
$stmt->close();
$conn->close();

// Merge all products
$allProducts = array_merge($staticProducts, $dbProducts);

// --- Cart actions ---
if(isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $qty = max(1,intval($_POST['quantity']));
    if(isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] = $qty;
    }
    header("Location: cart.php");
    exit();
}

// Calculate total
$total = 0;
foreach($_SESSION['cart'] as $id => $item){
    $price = $allProducts[$id]['price'] ?? $item['price'];
    $total += $price * $item['quantity'];
}
$cartCount = array_sum(array_column($_SESSION['cart'],'quantity'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart - Phantoms Community Development</title>
<style>
body{font-family:Arial,sans-serif;background:#00b7eb;color:white;min-height:100vh;margin:0;padding:0;}

/* Navbar */
.navbar{
    background:#2c3e50;
    padding:1rem 2rem;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.nav-logo{
    display:flex;
    align-items:center;
    gap:10px;
}
.nav-logo img{
    height:50px;
    width:50px;
    border-radius:50%;
    border:2px solid #ff6b35;
    object-fit:cover;
}
.nav-logo h2{
    color:#ff6b35;
    margin:0;
    font-size:1.5rem;
}
.nav-links{
    display:flex;
    gap:1.5rem;
}
.nav-link{color:white;text-decoration:none;font-weight:500;transition:color 0.3s;}
.nav-link:hover{color:#ff6b35;}

/* Main content */
main{padding:2rem;max-width:800px;margin:80px auto 0;}
.cart-item{background:white;color:#333;padding:1rem;margin-bottom:1rem;border-radius:8px;display:flex;justify-content:space-between;align-items:center;}
.quantity-input{width:50px;text-align:center;}
.cta-button{background:#ff6b35;color:white;border:none;padding:10px 15px;border-radius:5px;text-decoration:none;cursor:pointer;}
.cta-button:hover{background:#e55a2b;}
.cart-img{width:60px;height:60px;object-fit:cover;border-radius:5px;margin-right:10px;}
.item-info{display:flex;align-items:center;gap:10px;}
</style>
</head>
<body>

<nav class="navbar">
    <div class="nav-logo">
        <img src="images/official logo.jpg" alt="Phantoms Logo">
        <h2>Phantoms community development</h2>
    </div>
    <div class="nav-links">
        <a href="index.php" class="nav-link">Home</a>
        <a href="shop.php" class="nav-link">Shop</a>
        <a href="cart.php" class="nav-link">Cart (<?= $cartCount ?>)</a>
    </div>
</nav>

<main>
<h1>Your Shopping Cart</h1>

<?php if(empty($_SESSION['cart'])): ?>
    <p>Your cart is empty</p>
<?php else: ?>
    <?php foreach($_SESSION['cart'] as $id => $item):
        $product = $allProducts[$id] ?? $item;
        $price = $product['price'];
        $itemTotal = $price * $item['quantity'];
    ?>
    <div class="cart-item">
        <div class="item-info">
            <img src="images/<?= htmlspecialchars($product['image'] ?? '') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="cart-img">
            <div>
                <strong><?= htmlspecialchars($product['name']) ?></strong><br>
                <small>R<?= number_format($price,2) ?></small>
            </div>
        </div>
        <div>
            <form method="POST" style="display:flex;gap:5px;align-items:center;">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="number" name="quantity" min="1" value="<?= $item['quantity'] ?>" class="quantity-input">
                <button type="submit" name="update" class="cta-button">Update</button>
            </form>
            <a href="cart.php?remove=<?= $id ?>" class="cta-button">Remove</a>
            <strong>R<?= number_format($itemTotal,2) ?></strong>
        </div>
    </div>
    <?php endforeach; ?>
    <h3>Total: R<?= number_format($total,2) ?></h3>
    <a href="checkout.php" class="cta-button">Proceed to Checkout</a>
<?php endif; ?>
</main>

</body>
</html>
