<?php
// Include PHPMailer
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

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function verifyResetCode($email, $reset_code) {
        $email = htmlspecialchars(trim($email));
        $reset_code = htmlspecialchars(trim($reset_code));

        // Fetch reset code and expiration from database
        $stmt = $this->conn->prepare("SELECT * FROM password_resets WHERE email = :email AND reset_code = :reset_code");
        $stmt->execute([':email' => $email, ':reset_code' => $reset_code]);
        $resetRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resetRecord && strtotime($resetRecord['expiration']) > time()) {
            return true; // Reset code is valid and not expired
        }
        return false; // Invalid or expired reset code
    }

    public function updatePassword($email, $newPassword) {
        $stmt = $this->conn->prepare("UPDATE users SET password_hash = :password_hash WHERE email = :email");
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt->execute([':password_hash' => $passwordHash, ':email' => $email]);

        // Mark the reset code as used
        $stmt = $this->conn->prepare("UPDATE password_resets SET used = TRUE WHERE email = :email");
        $stmt->execute([':email' => $email]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    $passwordReset = new PasswordReset($conn);

    if (isset($_POST['email']) && isset($_POST['reset_code'])) {
        $email = $_POST['email'];
        $reset_code = $_POST['reset_code'];

        if ($passwordReset->verifyResetCode($email, $reset_code)) {
            // Redirect to the password update form
            header("Location: update_password.php?email=" . urlencode($email));
            exit();
        } else {
            echo "Invalid or expired reset code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Reset Code</title>
</head>
<body>
    <h2>Verify Reset Code</h2>
    <form action="reset_password.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="reset_code">Reset Code:</label>
        <input type="text" id="reset_code" name="reset_code" required><br><br>

        <button type="submit">Verify Reset Code</button>
    </form>
</body>
</html>
