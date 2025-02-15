<?php
include 'dbconnection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID
$conn = (new DatabaseConnection('localhost', '2fa', 'root', ''))->connect();

// INSERT ORDER (insert_order.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert_order'])) {
    try {
        $product_name = $_POST['product_name'];
        $quantity = $_POST['quantity'];
        $total_price = $_POST['total_price'];
        $status = 'Pending';
        $order_date = date('Y-m-d H:i:s');

        $sql = "INSERT INTO orders (user_id, product_name, quantity, total_price, status, order_date) 
                VALUES (:user_id, :product_name, :quantity, :total_price, :status, :order_date)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':product_name' => $product_name,
            ':quantity' => $quantity,
            ':total_price' => $total_price,
            ':status' => $status,
            ':order_date' => $order_date
        ]);
        header("Location: customer_orders.php");
    } catch (PDOException $e) {
        die("Error inserting order: " . $e->getMessage());
    }
}
?>