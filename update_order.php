<?php
include 'dbconnection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID
$conn = (new DatabaseConnection('localhost', '2fa', 'root', ''))->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    try {
        $order_id = $_POST['order_id'];
        $product_name = $_POST['product_name'];
        $quantity = $_POST['quantity'];
        $total_price = $_POST['total_price'];
        $status = $_POST['status'];

        $sql = "UPDATE orders SET product_name = :product_name, quantity = :quantity, 
                total_price = :total_price, status = :status WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':product_name' => $product_name,
            ':quantity' => $quantity,
            ':total_price' => $total_price,
            ':status' => $status,
            ':order_id' => $order_id,
            ':user_id' => $user_id
        ]);
        header("Location: customer_orders.php");
    } catch (PDOException $e) {
        die("Error updating order: " . $e->getMessage());
    }
}
?>