<?php
require 'vendor/autoload.php';

class Database {
    private $host = 'localhost';
    private $dbname = '2fa';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

class PasswordReset {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function updatePassword($email, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            if (!$hashedPassword) {
                die("Password hashing failed.");
            }

            $stmt = $this->conn->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true;  // Password updated successfully
            } else {
                return false; // No rows affected, possibly incorrect email
            }
        } catch (PDOException $e) {
            return false; // Error during query execution
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    $passwordReset = new PasswordReset($conn);

    if (isset($_POST['email']) && isset($_POST['new_password'])) {
        $email = $_POST['email'];
        $newPassword = $_POST['new_password'];

        if ($passwordReset->updatePassword($email, $newPassword)) {
            // Redirect to login page after successful password update
            header("Location: login.php");
            exit();
        } else {
            echo "Error updating password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
</head>
<body>
    <h2>Update Password</h2>
    <form action="update_password.php" method="POST">
        <?php $email = htmlspecialchars($_GET['email'] ?? ''); ?>
        <input type="hidden" name="email" value="<?php echo $email; ?>">
        
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required><br><br>
        
        <button type="submit">Update Password</button>
    </form>
</body>
</html>
