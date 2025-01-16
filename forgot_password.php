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

    public function requestReset($email) {
        $this->email = htmlspecialchars(trim($email));

        // Check if the user exists
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $this->email]);

        if ($stmt->rowCount() === 1) {
            $this->generateResetCode();
            $this->saveResetCode();
            $this->sendResetEmail();
        } else {
            echo "Email not found.";
        }
    }

    private function generateResetCode() {
        $this->reset_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->expiration = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    }

    private function saveResetCode() {
        $stmt = $this->conn->prepare("
            INSERT INTO password_resets (email, reset_code, expiration) 
            VALUES (:email, :reset_code, :expiration)
            ON DUPLICATE KEY UPDATE reset_code = :reset_code, expiration = :expiration, used = FALSE
        ");
        $stmt->execute([':email' => $this->email, ':reset_code' => $this->reset_code, ':expiration' => $this->expiration]);
    }

    private function sendResetEmail() {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@gmail.com';
            $mail->Password = 'your_password'; // Use an app-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('your_email@gmail.com', 'Password Reset');
            $mail->addAddress($this->email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body = "Your password reset code is <strong>{$this->reset_code}</strong>. It expires in 15 minutes.";

            $mail->send();
            echo "A reset code has been sent to your email.";
        } catch (Exception $e) {
            echo "Error sending email: {$mail->ErrorInfo}";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();

    $passwordReset = new PasswordReset($conn);
    $passwordReset->requestReset($_POST['email']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <form action="forgot_password.php" method="POST">
        <label for="email">Enter your email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Send Reset Code</button>
    </form>
</body>
</html>
