<?php
require_once 'dbconnection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    try {
        $conn->beginTransaction();

        // Check if the order belongs to the logged-in user
        $sql = "SELECT * FROM orders WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['order_id' => $order_id, 'user_id' => $user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            die("Unauthorized action or order not found.");
        }

        // Delete the order
        $sql = "DELETE FROM orders WHERE order_id = :order_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['order_id' => $order_id]);

        $conn->commit();

        header("Location: customer.php"); // Redirect back to orders page
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        die("Deletion failed: " . $e->getMessage());
    }
} else {
    die("Invalid request.");
}
