<?php 

include 'dbconnection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Initialize Database Connection
$dbConnection = new DatabaseConnection('localhost', '2fa', 'root', '');
$conn = $dbConnection->connect();

$orders = []; // Prevents undefined variable error

if ($conn) { // Ensure $conn is set before querying
    try {
        $sql = "SELECT order_id, product_name, quantity, total_price, status, order_date 
                FROM orders 
                WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    die("Database connection failed.");
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
        .content-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #064663;
            color: #ECB365;
            border-radius: 10px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #04293A;
        }
        .table th {
            background: #04293A;
        }
        .action-icons {
            display: flex;
            gap: 10px;
        }
        .action-icons a {
            color: #ECB365;
            font-size: 18px;
        }
        .order-form {
            margin-top: 30px;
        }
        .order-form input, .order-form button {
            padding: 10px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
        }
        .order-form button {
            background: #ECB365;
            color: #04293A;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h2>Customer</h2>
        <ul>
            <li><a href="customer_orders.php"><i class="fas fa-box"></i> My Orders</a></li>
            <li><a href="dashboard.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="#"><i class="fas fa-address-book"></i> Contact</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul> 
    </div>

    <div class="main_content">
        <div class="content-container">
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
                            <td class="action-icons">
                                <a href="update_order.php?order_id=<?= $order['order_id'] ?>"><i class="fas fa-edit"></i></a>
                                <a href="delete_order.php?order_id=<?= $order['order_id'] ?>" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">No orders found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <form class="order-form" action="insert_order.php" method="POST">
                <input type="text" name="product_name" placeholder="Product Name" required>
                <input type="number" name="quantity" placeholder="Quantity" required>
                <input type="text" name="total_price" placeholder="Total Price" required>
                <button type="submit">Add Order</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>