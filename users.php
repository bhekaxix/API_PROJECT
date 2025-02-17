<?php 
require_once 'dbconnection.php'; // Ensure PDO connection

// Fetch users
try {
    $sql = "SELECT * FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Insert User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    try {
        $sql = "INSERT INTO users (firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'firstname' => $_POST['firstname'],
            'lastname' => $_POST['lastname'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_BCRYPT)
        ]);
        header("Location: users.php");
        exit();
    } catch (PDOException $e) {
        die("Insertion failed: " . $e->getMessage());
    }
}

// Remove User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_user'])) {
    try {
        $sql = "DELETE FROM users WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user_id' => $_POST['user_id']]);
        header("Location: users.php");
        exit();
    } catch (PDOException $e) {
        die("Deletion failed: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management</title>
  <link rel="stylesheet" href="/api_project/css/styles.css">
  <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h2>Admin</h2>
        <ul>
            <li><a href="adminpanel.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul> 
    </div>

    <div class="main_content">
        <h1 class="header">User Management</h1>

        <!-- Add User Form -->
        <div class="form-container">
            <h2>Add New User</h2>
            <form method="post" class="add-user-form">
                <input type="text" name="firstname" placeholder="First Name" required>
                <input type="text" name="lastname" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                
                <button type="submit" name="add_user" class="btn btn-success">Add User</button>
            </form>
        </div>
        <br>
        <br>

        <!-- Users Table -->
        <div class="users-table">
            <h2>Existing Users</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']) ?></td>
                            <td><?= htmlspecialchars($user['firstname']) ?></td>
                            <td><?= htmlspecialchars($user['lastname']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td class="actions">
                                <!-- Remove Button -->
                                <form action="users.php" method="POST" style="display:inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                    <button type="submit" name="remove_user" class="btn btn-danger">Remove</button>
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
