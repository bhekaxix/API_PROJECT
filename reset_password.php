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
    private $email;
    private $reset_code;
    private $expiration;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function verifyResetCode($email, $reset_code) {
        $this->email = htmlspecialchars(trim($email));
        $this->reset_code = htmlspecialchars(trim($reset_code));

        // Fetch reset code and expiration from database
        $stmt = $this->conn->prepare("SELECT * FROM password_resets WHERE email = :email AND reset_code = :reset_code");
        $stmt->execute([':email' => $this->email, ':reset_code' => $this->reset_code]);
        $resetRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resetRecord) {
            $this->expiration = $resetRecord['expiration'];

            // Check if the reset code is expired
            if (strtotime($this->expiration) > time()) {
                return true; // Reset code is valid and not expired
            } else {
                return false; // Reset code has expired
            }
        }
        return false; // Invalid reset code
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

    // Check if the reset code is correct
    if (isset($_POST['reset_code'])) {
        $reset_code = $_POST['reset_code'];
        $email = $_POST['email'];

        if ($passwordReset->verifyResetCode($email, $reset_code)) {
            // If the reset code is valid, show the form to update the password
            if (isset($_POST['new_password'])) {
                $newPassword = $_POST['new_password'];
                $passwordReset->updatePassword($email, $newPassword);
                
                // Redirect to login page after updating the password
                header("Location: login.php");
                exit(); // Prevent further script execution
            }
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

    <?php
    // Show the new password form if the reset code is valid
    if (isset($_POST['reset_code']) && $_POST['reset_code'] != '') {
        echo '<form action="reset_password.php" method="POST">
                <input type="hidden" name="email" value="' . $_POST['email'] . '">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required><br><br>
                <button type="submit">Update Password</button>
              </form>';
    }
    ?>
</body>
</html>
