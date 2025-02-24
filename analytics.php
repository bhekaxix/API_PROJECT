<?php
require_once 'dbconnection.php'; // Ensure PDO connection

// Fetch analytics data
try {
    $totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $totalRevenue = $conn->query("SELECT SUM(total_price) FROM orders")->fetchColumn() ?: 0;

    $orderTrends = $conn->query("SELECT DATE(order_date) as date, COUNT(*) as count FROM orders GROUP BY DATE(order_date) ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC);
    $stockLevels = $conn->query("SELECT product_name, stock FROM items")->fetchAll(PDO::FETCH_ASSOC);
    
    $revenueTrends = $conn->query("SELECT DATE(order_date) as date, SUM(total_price) as revenue FROM orders GROUP BY DATE(order_date) ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Handle CSV Download
if (isset($_GET['download'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="analytics_data.csv"');

    $output = fopen("php://output", "w");
    fputcsv($output, ['Date', 'Total Orders', 'Total Revenue']);

    foreach ($orderTrends as $key => $trend) {
        $revenue = isset($revenueTrends[$key]['revenue']) ? $revenueTrends[$key]['revenue'] : 0;
        fputcsv($output, [$trend['date'], $trend['count'], $revenue]);
    }
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Analytics</title>
    <link rel="stylesheet" href="/api_project/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .wrapper {
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #041C32;
            color: #fff;
            height: 100vh;
            padding: 20px;
            position: fixed;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: left;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        .sidebar ul li a:hover {
            background: #064663;
            padding-left: 10px;
            transition: 0.3s;
        }
        .main_content {
            margin-left: 270px;
            padding: 30px;
            width: calc(100% - 250px);
        }
        .analytics-box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .analytics-box h2 {
            color: #04293A;
        }
        .chart-container {
            width: 100%;
            max-width: 600px;
            margin: auto;
        }
        .download-btn {
            display: inline-block;
            padding: 10px 15px;
            background: #ECB365;
            color: #041C32;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="adminpanel.php"><i class="fas fa-box"></i> Orders</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="items.php"><i class="fas fa-box-open"></i> Items</a></li>
            <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
            <li><a href="alogin.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main_content">
        <h1>Analytics Dashboard</h1>

        <div class="analytics-box">
            <div class="stats">
                <div>
                    <i class="fas fa-shopping-cart"></i> Total Orders: <?= number_format($totalOrders) ?>
                </div>
                <div>
                    <i class="fas fa-money-bill"></i> Total Revenue: $ <?= number_format($totalRevenue, 2) ?>
                </div>
            </div>
        </div>

        <div class="analytics-box">
            <h2>Order Trends</h2>
            <div class="chart-container">
                <canvas id="orderChart"></canvas>
            </div>
        </div>

        <div class="analytics-box">
            <h2>Revenue Trends</h2>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="analytics-box">
            <h2>Stock Levels</h2>
            <div class="chart-container">
                <canvas id="stockChart"></canvas>
            </div>
        </div>

        <div class="analytics-box">
            <a href="?download=true" class="download-btn">Download Stats as CSV</a>
        </div>
    </div>
</div>

<script>
    // Order Trends Line Chart
    const orderCtx = document.getElementById('orderChart').getContext('2d');
    new Chart(orderCtx, {
        type: 'line',
        data: {
            labels: [<?php foreach ($orderTrends as $trend) { echo "'" . $trend['date'] . "',"; } ?>],
            datasets: [{
                label: 'Orders per Day',
                data: [<?php foreach ($orderTrends as $trend) { echo $trend['count'] . ","; } ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true
            }]
        }
    });

    // Revenue Trends Bar Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: [<?php foreach ($revenueTrends as $trend) { echo "'" . $trend['date'] . "',"; } ?>],
            datasets: [{
                label: 'Revenue per Day',
                data: [<?php foreach ($revenueTrends as $trend) { echo $trend['revenue'] . ","; } ?>],
                backgroundColor: '#ECB365',
                borderColor: '#041C32',
                borderWidth: 2
            }]
        }
    });

    // Stock Levels Pie Chart
    const stockCtx = document.getElementById('stockChart').getContext('2d');
    new Chart(stockCtx, {
        type: 'pie',
        data: {
            labels: [<?php foreach ($stockLevels as $item) { echo "'" . $item['product_name'] . "',"; } ?>],
            datasets: [{
                data: [<?php foreach ($stockLevels as $item) { echo $item['stock'] . ","; } ?>],
                backgroundColor: ['#04293A', '#ECB365', '#064663', '#00A8CC']
            }]
        }
    });
</script>

</body>
</html>
