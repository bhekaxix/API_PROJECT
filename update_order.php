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
    $sql = "SELECT orders.order_id, orders.item_id, orders.quantity, orders.total_price, items.product_name 
            FROM orders
            INNER JOIN items ON orders.item_id = items.item_id
            WHERE orders.order_id = :order_id AND orders.user_id = :user_id";

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

// Fetch available products from the `items` table
try {
    $sql = "SELECT item_id, product_name FROM items";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}

// Update order if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    
    // Calculate total price based on fixed rate
    $total_price = ($quantity / 50) * 400;

    try {
        $sql = "UPDATE orders SET item_id = :item_id, quantity = :quantity, total_price = :total_price 
                WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
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
        <select name="item_id" required>
            <?php foreach ($products as $product): ?>
                <option value="<?= $product['item_id'] ?>" <?= $order['item_id'] == $product['item_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($product['product_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Quantity (Liters):</label>
        <input type="number" name="quantity" value="<?= htmlspecialchars($order['quantity']) ?>" required min="50" step="50">

        <p><strong>Fixed Price:</strong> 400 per 50 Liters</p>

        <button type="submit">Update Order</button>
    </form>
</div>

</body>
</html>
