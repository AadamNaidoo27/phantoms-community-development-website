<?php
session_start();

// Only allow admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include __DIR__ . '/../db_connect.php'; // adjust path if needed

$error = '';
$success = '';

// ✅ Handle product deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    // Get image filename first
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($imageName);
    $stmt->fetch();
    $stmt->close();

    // Delete from DB
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        // Delete image file
        $imagePath = __DIR__ . '/images/' . $imageName;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $success = "Product deleted successfully!";
    } else {
        $error = "Failed to delete product.";
    }
    $stmt->close();
}

// ✅ Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
    $product_name = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $image = $_FILES['image'] ?? null;

    if ($product_name && $description && $price && $category && $image && $image['error'] === 0) {
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array(strtolower($ext), $allowed_exts)) {
            $error = "Only JPG, JPEG, PNG, GIF images are allowed!";
        } else {
            $new_name = uniqid('prod_', true) . '.' . $ext;
            $upload_dir = __DIR__ . '/images/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($image['tmp_name'], $upload_dir . $new_name)) {
                $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdss", $product_name, $description, $price, $new_name, $category);

                if ($stmt->execute()) {
                    $success = "Product '$product_name' added successfully!";
                } else {
                    $error = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "Failed to upload image!";
            }
        }
    } else {
        $error = "Please fill in all fields and select an image!";
    }
}

// ✅ Fetch all products
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add & Manage Products - Admin</title>
<link rel="stylesheet" href="style.css">
<style>
body { font-family: Arial, sans-serif; background:#00b7eb; color:white; padding:2rem; }
.form-container { background:rgba(255,255,255,0.9); color:#333; padding:2rem; border-radius:10px; max-width:600px; margin:auto; margin-bottom:3rem; }
input, textarea, select { width:100%; padding:10px; margin-bottom:1rem; border:2px solid #ddd; border-radius:5px; }
button { background:#3498db; color:white; padding:12px 24px; border:none; border-radius:5px; cursor:pointer; font-weight:bold; }
button:hover { background:#2980b9; }
.error { color:red; margin-bottom:1rem; }
.success { color:green; margin-bottom:1rem; }

.products-table { width:100%; border-collapse:collapse; background:white; color:#333; margin-top:2rem; border-radius:10px; overflow:hidden; }
.products-table th, .products-table td { padding:12px; border-bottom:1px solid #ddd; text-align:center; }
.products-table th { background:#2c3e50; color:white; }
.products-table img { width:80px; height:80px; object-fit:cover; border-radius:8px; }
.delete-btn { background:red; color:white; border:none; padding:8px 12px; border-radius:5px; cursor:pointer; font-weight:bold; }
.delete-btn:hover { background:darkred; }
</style>
</head>
<body>

<div class="form-container">
    <h1>Add New Product</h1>

    <?php if($error) echo "<p class='error'>$error</p>"; ?>
    <?php if($success) echo "<p class='success'>$success</p>"; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <label>Product Name:</label>
        <input type="text" name="product_name" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Price (R):</label>
        <input type="number" step="0.01" name="price" required>

        <label>Category:</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Caps">Caps</option>
            <option value="Bucket Hats">Bucket Hats</option>
            <option value="Tracksuits">Tracksuits</option>
            <option value="Gears">Gears</option>
        </select>

        <label>Product Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit">Add Product</button>
    </form>

    <p><a href="admin_dashboard.php" style="color:#ff6b35;">Back to Dashboard</a></p>
</div>

<!-- ✅ Product List -->
<div class="form-container" style="max-width:900px;">
    <h2>Manage Products</h2>

    <?php if (empty($products)): ?>
        <p>No products available.</p>
    <?php else: ?>
        <table class="products-table">
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price (R)</th>
                <th>Action</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['category']) ?></td>
                    <td><?= number_format($product['price'], 2) ?></td>
                    <td>
                        <a href="?delete_id=<?= $product['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?');">
                            <button class="delete-btn">Delete</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
