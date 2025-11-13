<?php
session_start();
include __DIR__ . '/../db_connect.php'; // Adjust path if needed

// --- Load Products ---
// Static products
$staticProducts = [
    ['id'=>'static_1','name'=>'T-shirt','price'=>100,'image'=>'tshirt.jpg'],
    ['id'=>'static_2','name'=>'Cap','price'=>60,'image'=>'cap1.jpg'],
    ['id'=>'static_3','name'=>'Hat','price'=>100,'image'=>'hat.jpg'],
    ['id'=>'static_4','name'=>'Jacket','price'=>450,'image'=>'jacket.jpg'],
    ['id'=>'static_5','name'=>'Tracksuit','price'=>450,'image'=>'Tracksuit.jpeg'],
    ['id'=>'static_6','name'=>'Gear','price'=>250,'image'=>'Gear.jpeg']
];

// Fetch DB products
$dbProducts = [];
$stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
if($result){
    while($row = $result->fetch_assoc()){
        if(!isset($row['id'])) $row['id'] = 'db_' . uniqid();
        $dbProducts[$row['id']] = $row;
    }
}
$stmt->close();
$conn->close();

// Merge static + DB products and key by ID
$allProducts = [];
foreach(array_merge($staticProducts, $dbProducts) as $p){
    $allProducts[$p['id']] = $p;
}

// --- Cart Calculations ---
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$subtotal = 0;
foreach($_SESSION['cart'] as $id => $item){
    if(!isset($allProducts[$id])) continue;
    $product = $allProducts[$id];
    $subtotal += $product['price'] * $item['quantity'];
}

$shipping = 50.00;
$total = $subtotal + $shipping;
$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));

// --- Handle Order Submission ---
$orderSuccess = false;
$whatsappLink = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(!empty($_SESSION['cart'])){
        $orderSuccess = true;

        // Build WhatsApp message
        $customerName = htmlspecialchars($_POST['fullName']);
        $customerPhone = htmlspecialchars($_POST['phone']);
        $totalAmount = number_format($total, 2);

        $orderDetails = "Order Details:\n";
        foreach ($_SESSION['cart'] as $id => $item) {
            if (!isset($allProducts[$id])) continue;
            $product = $allProducts[$id];
            $name = $product['name'];
            $qty = $item['quantity'];
            $price = number_format($product['price'] * $qty, 2);
            $orderDetails .= "- $name (x$qty): R$price\n";
        }

        $orderDetails .= "\nSubtotal: R" . number_format($subtotal, 2);
        $orderDetails .= "\nShipping: R" . number_format($shipping, 2);
        $orderDetails .= "\nTotal: R$totalAmount";

        $msg = "Hi $customerName! ✅\nThank you for your order with Phantoms Community Development.\n\n$orderDetails\n\nWe'll contact you soon with delivery details.";
        $msgEncoded = urlencode($msg);

        // Replace with your business WhatsApp number (no +, include country code)
        $whatsappNumber = "27660848345"; // Example: 27 = South Africa
        $whatsappLink = "https://wa.me/$whatsappNumber?text=$msgEncoded";

        // Clear cart
        $_SESSION['cart'] = [];
        $subtotal = 0;
        $total = $shipping;
        $cartCount = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - Phantoms Community Development</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:Arial,sans-serif;background:#00b7eb;color:white;line-height:1.6;min-height:100vh;}
.navbar{background:#2c3e50;padding:1rem;position:fixed;width:100%;top:0;z-index:1000;box-shadow:0 2px 10px rgba(0,0,0,0.3);}
.nav-container{max-width:1200px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;padding:0 2rem;}
.nav-logo h2{color:#ff6b35;font-size:1.8rem;font-weight:bold;}
.nav-menu{display:flex;list-style:none;gap:2rem;}
.nav-link{color:white;text-decoration:none;transition:color 0.3s;font-weight:500;}
.nav-link:hover{color:#ff6b35;}
.checkout-container{max-width:1200px;margin:100px auto 0;padding:2rem;display:grid;grid-template-columns:1fr 400px;gap:3rem;}
.checkout-form, .order-summary{background:white;color:#333;padding:2rem;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.2);}
h1{color:#ff6b35;margin-bottom:2rem;text-align:center;}
h2{color:#ff6b35;margin-bottom:1.5rem;border-bottom:2px solid #00b7eb;padding-bottom:0.5rem;}
.form-group{margin-bottom:1.5rem;}
label{display:block;margin-bottom:0.5rem;font-weight:bold;color:#ff6b35;}
input, select{width:100%;padding:12px;border:2px solid #ddd;border-radius:5px;font-size:1rem;transition:border-color 0.3s;}
input:focus, select:focus{outline:none;border-color:#00b7eb;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
.order-item{display:flex;justify-content:space-between;align-items:center;padding:1rem 0;border-bottom:1px solid #eee;}
.order-totals{margin-top:2rem;padding-top:1rem;border-top:2px solid #00b7eb;}
.total-row{display:flex;justify-content:space-between;margin-bottom:0.5rem;font-weight:bold;}
.final-total{font-size:1.3rem;color:#ff6b35;border-top:1px solid #ddd;padding-top:0.5rem;margin-top:0.5rem;}
.submit-button{background:#ff6b35;color:white;border:none;padding:15px 30px;border-radius:6px;cursor:pointer;font-weight:bold;font-size:1.1rem;width:100%;transition:background 0.3s;margin-top:1rem;}
.submit-button:hover{background:#e55a2b;}
.footer{background:#2c3e50;color:white;text-align:center;padding:2rem;margin-top:3rem;}
.order-summary .order-item{flex-direction:row;justify-content:space-between;}
.whatsapp-btn{display:inline-block;background:#25D366;color:white;padding:12px 20px;border-radius:5px;text-decoration:none;font-weight:bold;margin-top:1rem;transition:background 0.3s;}
.whatsapp-btn:hover{background:#1ebe57;}
</style>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo"><h2>Phantoms Community Development</h2></div>
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="shop.php" class="nav-link">Shop</a></li>
            <li><a href="cart.php" class="nav-link">Cart (<?= $cartCount ?>)</a></li>
        </ul>
    </div>
</nav>

<div class="checkout-container">
    <div class="checkout-form">
        <h1>Checkout</h1>
        <?php if($orderSuccess): ?>
            <p style="color:green;font-weight:bold;text-align:center;">✅ Order placed successfully! Thank you for your purchase.</p>
            <div style="text-align:center;">
                <a href="<?= $whatsappLink ?>" target="_blank" class="whatsapp-btn">📲 Send WhatsApp Confirmation</a>
            </div>
        <?php endif; ?>

        <form method="POST">
            <h2>Shipping Information</h2>
            <div class="form-group"><label for="fullName">Full Name</label><input type="text" id="fullName" name="fullName" required></div>
            <div class="form-group"><label for="email">Email Address</label><input type="email" id="email" name="email" required></div>
            <div class="form-group"><label for="phone">Phone Number</label><input type="tel" id="phone" name="phone" required></div>
            <div class="form-group"><label for="address">Street Address</label><input type="text" id="address" name="address" required></div>
            <div class="form-row">
                <div class="form-group"><label for="city">City</label><input type="text" id="city" name="city" required></div>
                <div class="form-group"><label for="postalCode">Postal Code</label><input type="text" id="postalCode" name="postalCode" required></div>
            </div>
            <h2>Payment Information</h2>
            <div class="form-group"><label for="cardNumber">Card Number</label><input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" required></div>
            <div class="form-row">
                <div class="form-group"><label for="expiryDate">Expiry Date</label><input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY" required></div>
                <div class="form-group"><label for="cvv">CVV</label><input type="text" id="cvv" name="cvv" placeholder="123" required></div>
            </div>
            <div class="form-group"><label for="cardName">Name on Card</label><input type="text" id="cardName" name="cardName" required></div>
            <button type="submit" class="submit-button">Complete Order - R<?= number_format($total,2) ?></button>
        </form>
    </div>

    <div class="order-summary">
        <h2>Order Summary</h2>
        <?php if(empty($_SESSION['cart'])): ?>
            <div class="order-item"><span>Your cart is empty</span></div>
        <?php else: ?>
            <?php foreach($_SESSION['cart'] as $id => $item):
                if(!isset($allProducts[$id])) continue;
                $product = $allProducts[$id];
                $itemTotal = $product['price'] * $item['quantity'];
            ?>
            <div class="order-item">
                <span><?= htmlspecialchars($product['name']) ?> (x<?= $item['quantity'] ?>)</span>
                <span>R<?= number_format($itemTotal,2) ?></span>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="order-totals">
            <div class="total-row"><span>Subtotal:</span><span>R<?= number_format($subtotal,2) ?></span></div>
            <div class="total-row"><span>Shipping:</span><span>R<?= number_format($shipping,2) ?></span></div>
            <div class="total-row final-total"><span>Total:</span><span>R<?= number_format($total,2) ?></span></div>
        </div>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2025 Phantoms Community Development. All rights reserved.</p>
</footer>
</body>
</html>
