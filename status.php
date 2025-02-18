<?php
require_once 'dbconnection.php'; // Ensure PDO connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Update order status
    try {
        $sql = "UPDATE orders SET status = :status WHERE order_id = :order_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        header('Location: adminpanel.php'); // Redirect back to the admin panel
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}
?>
