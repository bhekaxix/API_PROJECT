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

// Insert order if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $total_price = $_POST['total_price'];
    $status = "Pending"; // Default status

    try {
        $sql = "INSERT INTO orders (user_id, product_name, quantity, total_price, status, order_date) VALUES (:user_id, :product_name, :quantity, :total_price, :status, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        header("Location: customer_orders.php?success=added");
        exit();
    } catch (PDOException $e) {
        die("Error inserting order: " . $e->getMessage());
    }
}
?>
