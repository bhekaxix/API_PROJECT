<?php  


require 'dbconnection.php'; // Include the database connection class

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// ✅ Fix: Pass the required parameters to DatabaseConnection
try {
    $dbConnection = new DatabaseConnection('localhost', '2fa', 'root', '');
    $conn = $dbConnection->connect();

    $orders = []; // Prevents undefined variable error

    if ($conn) { // Ensure $conn is set before querying
        $sql = "SELECT orders.order_id, items.product_name, orders.quantity, 
                       orders.total_price, orders.status, orders.order_date 
                FROM orders
                JOIN items ON orders.item_id = items.item_id
                WHERE orders.user_id = :user_id
                ORDER BY orders.order_date DESC"; // Ensure proper ordering

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Orders</title>
    <link rel="stylesheet" href="/api_project/css/styles.css">
    <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
    <style>
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .actions button {
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }
        .delete-btn {
            background-color: #FF5733;
            color: white;
        }
        .disabled-btn {
            background-color: gray;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h2>Customer</h2>
        <ul>
            <li><a href="customer.php"><i class="fas fa-box"></i> My Orders</a></li>
            <li><a href="dashboard.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="#"><i class="fas fa-address-book"></i> Contact</a></li>
            <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul> 
    </div>

    <div class="main_content">
        <div class="info">
            <h1 class="header">My Orders</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_id']) ?></td>
                            <td><?= htmlspecialchars($order['product_name']) ?></td>
                            <td><?= htmlspecialchars($order['quantity']) ?></td>
                            <td><?= htmlspecialchars($order['total_price']) ?></td>
                            <td><?= htmlspecialchars($order['status']) ?></td>
                            <td><?= htmlspecialchars($order['order_date']) ?></td>
                            <td class="actions">
                                <?php if ($order['status'] == 'Shipped'): ?>
                                    <!-- Disabled Update Button with Alert -->
                                    <button type="button" class="edit-btn disabled-btn" 
                                        onclick="alert('⚠️ Cannot perform this action. Order has been shipped.');">
                                        Update
                                    </button>

                                    <!-- Disabled Delete Button with Alert -->
                                    <button type="button" class="delete-btn disabled-btn" 
                                        onclick="alert('⚠️ Cannot perform this action. Order has been shipped.');">
                                        Remove
                                    </button>
                                <?php else: ?>
                                    <!-- Update Button -->
                                    <form action="update_order.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <button type="submit" class="edit-btn">Update</button>
                                    </form>

                                    <!-- Delete Button -->
                                    <form action="delete_order.php" method="POST" style="display:inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this order?');">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <button type="submit" class="delete-btn">Remove</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">No orders found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
