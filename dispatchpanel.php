<?php 
require_once 'dbconnection.php'; // Ensure we use the PDO connection

try {
    // Fetching all checked orders from the database
    $sql = "SELECT order_id, user_id, product_name, quantity, total_price, status, order_date FROM checkedorders";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dispatch Panel</title>
  <link rel="stylesheet" href="/api_project/css/styles.css">
  <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h2>Dispatcher</h2>
        <ul>
            <li><a href="checkedorders.php"><i class="fas fa-project-diagram"></i> Checked Orders</a></li>
            <li><a href="dlogin"><i class="fas fa-home"></i> Logout</a></li>
        </ul> 
    </div>

    <div class="main_content">
        <div class="info">
            <h1 class="header">List of Checked Orders</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">OrderID</th>
                        <th scope="col">UserID</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Total Price</th>
                        <th scope="col">Status</th>
                        <th scope="col">Order Date</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['order_id']) ?></td>
                            <td><?= htmlspecialchars($row['user_id']) ?></td>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                            <td><?= htmlspecialchars($row['total_price']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['order_date']) ?></td>
                            <td>
                                <!-- Form to mark an order as ready for packaging -->
                                <form action="packagingprocess.php" method="post">
                                    <input type="hidden" name="orderid" value="<?= htmlspecialchars($row['order_id']) ?>">
                                    <button type="submit" name="readyForPackaging">Ready for Packaging</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>   
        </div>
    </div>
</div>

</body>
</html>
