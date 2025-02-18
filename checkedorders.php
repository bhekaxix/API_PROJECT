<?php
require_once 'dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['markShipped'])) {
    $order_id = $_POST['order_id'];

    try {
        // 1️⃣ Get the order details from "orders"
        $sql = "SELECT * FROM orders WHERE order_id = :order_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // 2️⃣ Insert into "checkedorders"
            $insert_sql = "INSERT INTO checkedorders (order_id, user_id, product_name, quantity, total_price, status, order_date) 
                           VALUES (:order_id, :user_id, :product_name, :quantity, :total_price, 'Shipped', :order_date)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bindParam(':order_id', $order['order_id'], PDO::PARAM_INT);
            $insert_stmt->bindParam(':user_id', $order['user_id'], PDO::PARAM_INT);
            $insert_stmt->bindParam(':product_name', $order['product_name'], PDO::PARAM_STR);
            $insert_stmt->bindParam(':quantity', $order['quantity'], PDO::PARAM_INT);
            $insert_stmt->bindParam(':total_price', $order['total_price'], PDO::PARAM_STR);
            $insert_stmt->bindParam(':order_date', $order['order_date'], PDO::PARAM_STR);
            $insert_stmt->execute();

            // 3️⃣ Update status in "orders"
            $update_sql = "UPDATE orders SET status = 'Shipped' WHERE order_id = :order_id";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $update_stmt->execute();

            // Redirect to the orders page with success
            header("Location: admin_orders.php?status=success");
            exit();
        } else {
            // Redirect if the order was not found
            header("Location: admin_orders.php?status=notfound");
            exit();
        }
    } catch (PDOException $e) {
        // Handle the error and show message
        die("Error processing order: " . $e->getMessage());
    }
} else {
    // Redirect if not a POST request or markShipped is not set
    header("Location: admin_orders.php");
    exit();
}
?>
