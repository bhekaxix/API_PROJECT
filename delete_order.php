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

// Delete the order
try {
    $sql = "DELETE FROM orders WHERE order_id = :order_id AND user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: customer_orders.php?success=deleted");
    exit();
} catch (PDOException $e) {
    die("Error deleting order: " . $e->getMessage());
}
?>
