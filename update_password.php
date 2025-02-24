<?php
require 'vendor/autoload.php';
session_start(); // Start session for CSRF protection

// Database Connection Class
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

// Password Reset Class
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

            $stmt = $this->conn->prepare("UPDATE users SET password_hash = :password WHERE email = :email");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return $stmt->rowCount() > 0; // Returns true if the password was updated
        } catch (PDOException $e) {
            return false;
        }
    }
}

// CSRF Token Check
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token mismatch.");
    }

    $database = new Database();
    $conn = $database->getConnection();
    $passwordReset = new PasswordReset($conn);

    if (isset($_POST['email']) && isset($_POST['new_password'])) {
        $email = $_POST['email'];
        $newPassword = $_POST['new_password'];

        if ($passwordReset->updatePassword($email, $newPassword)) {
            session_destroy(); // Destroy session after successful reset
            header("Location: login.php?status=success");
            exit();
        } else {
            echo "Error updating password.";
        }
    }
}

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required><br><br>
        
        <button type="submit">Update Password</button>
    </form>
</body>
</html>
