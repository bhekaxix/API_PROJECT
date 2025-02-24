<?php
require_once 'dbconnection.php'; // Ensure PDO connection

// Fetch analytics data
try {
    $totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $totalRevenue = $conn->query("SELECT SUM(total_price) FROM orders")->fetchColumn();
    $orderTrends = $conn->query("SELECT DATE(order_date) as date, COUNT(*) as count FROM orders GROUP BY DATE(order_date) ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC);
    $stockLevels = $conn->query("SELECT product_name, stock FROM items")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Analytics</title>
    <link rel="stylesheet" href="/api_project/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h2>Admin</h2>
        <ul>
            <li><a href="adminpanel.php"><i class="fas fa-users"></i> Orders</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="items.php"><i class="fas fa-box"></i> Items</a></li>
            <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
            <li><a href="alogin.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main_content">
        <h1 class="header">Analytics</h1>
        <p>Total Orders: <?= htmlspecialchars($totalOrders) ?></p>
        <p>Total Revenue: Ksh <?= htmlspecialchars($totalRevenue) ?></p>
        
        <h2>Order Trends</h2>
        <canvas id="orderChart"></canvas>

        <h2>Stock Levels</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stockLevels as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['stock']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const ctx = document.getElementById('orderChart').getContext('2d');
    const orderChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?php foreach ($orderTrends as $trend) { echo "'" . $trend['date'] . "',"; } ?>],
            datasets: [{
                label: 'Orders per Day',
                data: [<?php foreach ($orderTrends as $trend) { echo $trend['count'] . ","; } ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
            }]
        }
    });
</script>

</body>
</html>
