<?php
require_once 'dbconnection.php';


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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    try {
        $conn->beginTransaction();

        // Check if the order belongs to the logged-in user
        $sql = "SELECT orders.order_id 
                FROM orders
                INNER JOIN items ON orders.item_id = items.item_id
                WHERE orders.order_id = :order_id AND orders.user_id = :user_id";

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

        header("Location: customer.php?success=deleted");
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        die("Error: Deletion failed. " . $e->getMessage());
    }
} else {
    die("Invalid request.");
}
