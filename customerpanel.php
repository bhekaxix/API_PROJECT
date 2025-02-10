<?php 
include 'dbconnection.php';

// Initialize Database Connection
$dbConnection = new DatabaseConnection('localhost', '2fa', 'root', '');
$conn = $dbConnection->connect();

$customers = []; // Prevents undefined variable error

if ($conn) { // Ensure $conn is set before querying
    try {
        $sql = "SELECT id, firstname, lastname, mobile, username, email, password_hash FROM users";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/api_project/css/styles.css">
    <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h2>Admin</h2>
        <ul>
            <li><a href="adminpanel.php"><i class="fas fa-project-diagram"></i> Orders</a></li>
            <li><a href="#"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="customerpanel.php"><i class="fas fa-address-card"></i> Customers</a></li>
            <li><a href="#"><i class="fas fa-address-book"></i> Contact</a></li>
            <li><a href="#"><i class="fas fa-home"></i> Logout</a></li>
        </ul> 
    </div>

    <div class="main_content">
        <div class="info">
            <h1 class="header">Customers Details</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Mobile</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Password Hash</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (!empty($customers)): ?>
                    <?php foreach ($customers as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['firstname']) ?></td>
                            <td><?= htmlspecialchars($row['lastname']) ?></td>
                            <td><?= htmlspecialchars($row['mobile']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['password_hash']) ?></td>
                            <td>
                                <button>
                                    <a href='delete.php?deleteid=<?= urlencode($row['id']) ?>'>Delete</a>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">No customers found.</td></tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>