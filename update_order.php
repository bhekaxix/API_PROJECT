<?php
include 'dbconnection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$dbConnection = new DatabaseConnection('localhost', '2fa', 'root', '');
$conn = $dbConnection->connect();

if (!$conn) {
    die("Database connection failed.");
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    die("Invalid order ID.");
}

$order_id = $_GET['order_id'];

// Fetch order details
try {
    $sql = "SELECT * FROM orders WHERE order_id = :order_id AND user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found.");
    }
} catch (PDOException $e) {
    die("Error fetching order: " . $e->getMessage());
}

// Update order if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    
    // Calculate total price based on fixed rate
    $total_price = ($quantity / 50) * 400;

    try {
        $sql = "UPDATE orders SET product_name = :product_name, quantity = :quantity, total_price = :total_price WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: customer.php?success=updated");
        exit();
    } catch (PDOException $e) {
        die("Error updating order: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Order</title>
    <link rel="stylesheet" href="/api_project/css/update.css">
</head>
<body>

<nav class="navbar">
    <a href="dashboard.php">Dashboard</a>
    <a href="customer.php">My Orders</a>
</nav>

<div class="content-container">
    <h2>Update Order</h2>
    <form method="POST">
        <label>Product Name:</label>
        <select name="product_name" required>
            <option value="Soya Oil" <?= $order['product_name'] == 'Soya Oil' ? 'selected' : '' ?>>Soya Oil</option>
            <option value="Vegetable Oil" <?= $order['product_name'] == 'Vegetable Oil' ? 'selected' : '' ?>>Vegetable Oil</option>
            <option value="Sunflower Oil" <?= $order['product_name'] == 'Sunflower Oil' ? 'selected' : '' ?>>Sunflower Oil</option>
        </select>

        <label>Quantity (Liters):</label>
        <input type="number" name="quantity" value="<?= htmlspecialchars($order['quantity']) ?>" required min="50" step="50">

        <p><strong>Fixed Price:</strong> 400 per 50 Liters</p>

        <button href="customer.php" type="submit">Update Order</button>
    </form>
</div>

</body>
</html>
