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

// Fetch current order details
try {
    $sql = "SELECT orders.order_id, orders.item_id, orders.quantity, orders.total_price, items.product_name, items.stock 
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

// Update order if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $new_quantity = (int)$_POST['quantity']; // Convert to integer
        
        // Ensure quantity is a multiple of 50 liters
        if ($new_quantity % 50 !== 0) {
            throw new Exception("Quantity must be in multiples of 50 liters.");
        }

        // Calculate stock units (50 liters = 1 stock unit)
        $new_stock_units = $new_quantity / 50;
        $old_stock_units = $order['quantity'] / 50;

        // Fetch current stock
        $stmt = $conn->prepare("SELECT stock FROM items WHERE item_id = :item_id");
        $stmt->execute(['item_id' => $order['item_id']]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            throw new Exception("Product not found.");
        }

        $current_stock = $item['stock'];

        $conn->beginTransaction();

        // If increasing quantity, check stock availability
        if ($new_stock_units > $old_stock_units) {
            $stock_needed = $new_stock_units - $old_stock_units;
            if ($current_stock < $stock_needed) {
                throw new Exception("Not enough stock available.");
            }
            // Deduct stock
            $stmt = $conn->prepare("UPDATE items SET stock = stock - :stock_needed WHERE item_id = :item_id");
            $stmt->execute(['stock_needed' => $stock_needed, 'item_id' => $order['item_id']]);
        }

        // Calculate new total price
        $new_total_price = $new_stock_units * 400;

        // Update the order
        $stmt = $conn->prepare("UPDATE orders SET quantity = :quantity, total_price = :total_price 
                                WHERE order_id = :order_id AND user_id = :user_id");
        $stmt->execute([
            'quantity' => $new_quantity,
            'total_price' => $new_total_price,
            'order_id' => $order_id,
            'user_id' => $user_id
        ]);

        $conn->commit();

        header("Location: customer.php?success=updated");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
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
        <input type="text" value="<?= htmlspecialchars($order['product_name']) ?>" disabled>

        <label>Quantity (Liters):</label>
        <input type="number" name="quantity" value="<?= htmlspecialchars($order['quantity']) ?>" required min="50" step="50">

        <p><strong>Fixed Price:</strong> 400 per 50 Liters</p>

        <button type="submit">Update Order</button>
    </form>
</div>

</body>
</html>
