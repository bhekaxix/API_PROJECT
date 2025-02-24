<?php
require_once 'dbconnection.php'; // Ensure PDO connection

// Fetch orders with product names
try {
    $sql = "SELECT orders.order_id, users.firstname, users.lastname, 
                   orders.item_id, items.product_name, 
                   orders.quantity, orders.total_price, orders.status, orders.order_date
            FROM orders
            JOIN users ON orders.user_id = users.user_id
            LEFT JOIN items ON orders.item_id = items.item_id"; // Use LEFT JOIN to avoid missing data

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$orders) {
        die("No orders found.");
    }
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="/api_project/css/styles.css">
  <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h2>Admin</h2>
        <ul>
            <li><a href="adminpanel.php"><i class="fas fa-box"></i> Orders</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="items.php"><i class="fas fa-boxes"></i> Items</a></li>
            <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
            <li><a href="alogin.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul> 
    </div>

    <div class="main_content">
        <h1 class="header">Orders Management</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Total Price</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr style="background-color: <?= $order['status'] == 'Shipped' ? 'lightgreen' : ($order['status'] == 'Checked' ? 'lightblue' : 'lightyellow') ?>;">
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= htmlspecialchars($order['firstname']) . " " . htmlspecialchars($order['lastname']) ?></td>
                        <td><?= htmlspecialchars($order['order_date']) ?></td>
                        <td><?= htmlspecialchars($order['total_price']) ?></td>
                        <td><?= htmlspecialchars($order['product_name'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($order['quantity']) ?></td> <!-- Added Quantity Column -->
                        <td class="actions">
                            <!-- Status Dropdown -->
                            <form action="status.php" method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Shipped" <?= $order['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
