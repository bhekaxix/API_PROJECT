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
        $stmt = $this->conn->prepare("SELECT expiration FROM password_resets WHERE email = :email AND reset_code = :reset_code AND used = FALSE");
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
        $this->markCodeAsUsed($email);
    }

    private function markCodeAsUsed($email) {
        $stmt = $this->conn->prepare("UPDATE password_resets SET used = TRUE WHERE email = :email");
        $stmt->execute([':email' => $email]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    $passwordReset = new PasswordReset($conn);

    if (!empty($_POST['email']) && !empty($_POST['reset_code'])) {
        $email = htmlspecialchars(trim($_POST['email']));
        $reset_code = htmlspecialchars(trim($_POST['reset_code']));

        if ($passwordReset->verifyResetCode($email, $reset_code)) {
            if (!empty($_POST['new_password'])) {
                $newPassword = $_POST['new_password'];
                $passwordReset->updatePassword($email, $newPassword);
                
                // Redirect to login page after updating password
                header("Location: login.php");
                exit();
            }
        } else {
            echo "<script>alert('Invalid or expired reset code.'); window.location.href='reset_password.php';</script>";
        }
    }
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
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="reset_code">Reset Code:</label>
        <input type="text" id="reset_code" name="reset_code" required><br><br>

        <button type="submit">Verify Reset Code</button>
    </form>
    
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_code'])): ?>
        <form action="reset_password.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required><br><br>
            <button type="submit">Update Password</button>
        </form>
    <?php endif; ?>
</body>
</html>
