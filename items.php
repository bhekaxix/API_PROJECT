<?php
require_once 'dbconnection.php'; // Ensure PDO connection

// Handle restock request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restock_item_id'], $_POST['restock_quantity'])) {
    $item_id = $_POST['restock_item_id'];
    $restock_quantity = (int)$_POST['restock_quantity'];

    if ($restock_quantity > 0) {
        try {
            $sql = "UPDATE items SET stock = stock + :restock_quantity WHERE item_id = :item_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'restock_quantity' => $restock_quantity,
                'item_id' => $item_id
            ]);

            echo "<script>alert('Stock updated successfully!'); window.location.href = 'items.php';</script>";
        } catch (PDOException $e) {
            die("Error updating stock: " . $e->getMessage());
        }
    } else {
        echo "<script>alert('Invalid restock quantity.'); window.location.href = 'items.php';</script>";
    }
}

// Fetch inventory items
try {
    $sql = "SELECT * FROM items";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Items</title>
    <link rel="stylesheet" href="/api_project/css/styles.css">
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
        <h1 class="header">Items Management</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Restock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['stock']) ?></td>
                        <td><?= htmlspecialchars($item['price']) ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="restock_item_id" value="<?= $item['item_id'] ?>">
                                <input type="number" name="restock_quantity" min="1" required>
                                <button type="submit">Restock</button>
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
