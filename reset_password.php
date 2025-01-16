<?php
// Include PHPMailer (if needed) and autoload for the vendor packages
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
    private $email;
    private $reset_code;
    private $new_password;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function resetPassword($email, $reset_code, $new_password) {
        $this->email = htmlspecialchars(trim($email));
        $this->reset_code = htmlspecialchars(trim($reset_code));
        $this->new_password = htmlspecialchars(trim($new_password));

        // Validate the reset code
        $stmt = $this->conn->prepare("SELECT * FROM password_resets WHERE email = :email AND reset_code = :reset_code AND used = FALSE AND expiration > NOW()");
        $stmt->execute([':email' => $this->email, ':reset_code' => $this->reset_code]);

        if ($stmt->rowCount() === 1) {
            $this->updatePassword();
            $this->markCodeAsUsed();
            echo "Password has been updated successfully.";
        } else {
            echo "Invalid or expired reset code.";
        }
    }

    private function updatePassword() {
        // Hash the new password
        $password_hash = password_hash($this->new_password, PASSWORD_BCRYPT);

        // Update the user's password
        $stmt = $this->conn->prepare("UPDATE users SET password_hash = :password_hash WHERE email = :email");
        $stmt->execute([':password_hash' => $password_hash, ':email' => $this->email]);
    }

    private function markCodeAsUsed() {
        // Mark the reset code as used
        $stmt = $this->conn->prepare("UPDATE password_resets SET used = TRUE WHERE email = :email");
        $stmt->execute([':email' => $this->email]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();

    $passwordReset = new PasswordReset($conn);
    $passwordReset->resetPassword($_POST['email'], $_POST['reset_code'], $_POST['new_password']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form action="reset_password.php" method="POST">
        <label for="email">Enter your email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="reset_code">Enter reset code:</label>
        <input type="text" id="reset_code" name="reset_code" required>
        
        <label for="new_password">Enter new password:</label>
        <input type="password" id="new_password" name="new_password" required>
        
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
