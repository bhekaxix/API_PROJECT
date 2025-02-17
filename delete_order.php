<?php

require 'dbconnection.php'; // Ensure database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id']; // Get logged-in user's ID

// Check if order_id is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    die("Invalid order ID.");
}

$order_id = (int)$_GET['order_id']; // Convert to integer for security

try {
    // Ensure database connection
    if (!isset($conn)) {
        throw new Exception("Database connection error.");
    }

    // Check if the order exists and belongs to the logged-in user
    $checkSql = "SELECT * FROM orders WHERE order_id = :order_id AND user_id = :user_id";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $checkStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $checkStmt->execute();
    $order = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found or you do not have permission to delete it.");
    }

    // Delete the order
    $deleteSql = "DELETE FROM orders WHERE order_id = :order_id AND user_id = :user_id";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $deleteStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($deleteStmt->execute()) {
        header("Location: customer.php?success=deleted"); // Redirect after deletion
        exit();
    } else {
        echo "Failed to delete order.";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
