<?php
require_once 'dbconnection.php'; // Ensure we use the PDO connection

try {
    $sql = "SELECT * FROM checkedorders";
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
            <li><a href="#"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="#"><i class="fas fa-home"></i> Logout</a></li>
        </ul> 
    </div>

    <div class="main_content">
        <div class="info">
            <h1 class="header">List of Checked Orders</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">OrderID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Order Date</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Oil Type</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['orderid']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['orderdate']) ?></td>
                            <td><?= htmlspecialchars($row['amount']) ?></td>
                            <td><?= htmlspecialchars($row['oiltype']) ?></td>
                            <td>
                                <form action="packagingprocess.php" method="post">
                                    <input type="hidden" name="orderid" value="<?= htmlspecialchars($row['orderid']) ?>">
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
