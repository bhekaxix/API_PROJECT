<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

class DatabaseConnection {
    private $host;
    private $dbname;
    private $username;
    private $password;
    public $conn;

    public function __construct($host, $dbname, $username, $password) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $this->conn;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}

class UserLogin {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function authenticate($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => htmlspecialchars(trim($username))]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify(htmlspecialchars(trim($password)), $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    public function updateOTP($userId) {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpExpiration = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $stmt = $this->conn->prepare("UPDATE users SET otp_code = :otp_code, otp_expiration = :otp_expiration WHERE id = :id");
        $stmt->execute([
            ':otp_code' => $otp,
            ':otp_expiration' => $otpExpiration,
            ':id' => $userId
        ]);

        return $otp;
    }

    public function sendOtpEmail($recipientEmail, $recipientName, $otp) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'taongabp@gmail.com'; // Replace with your email
            $mail->Password   = 'xjguxbwosrfxpkop'; // Replace with your app-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Recipient settings
            $mail->setFrom('taongabp@gmail.com', 'BBIT Exempt');
            $mail->addAddress($recipientEmail, $recipientName); // Use user's email


            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Your Verification Code';
            $mail->Body    = "Your OTP code is: <strong>$otp</strong>. It expires in 5 minutes.";

            return $mail->send();
        } catch (Exception $e) {
            throw new Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}

// Main Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbConnection = new DatabaseConnection('localhost', '2fa', 'root', '');
    $conn = $dbConnection->connect();

    $userLogin = new UserLogin($conn);

    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = $userLogin->authenticate($username, $password);

    if ($user) {
        $otp = $userLogin->updateOTP($user['id']);
        $userLogin->sendOtpEmail($user['email'], $user['username'], $otp);


        $_SESSION['user_id'] = $user['id'];
        header('Location: verification_login.php');
        exit();
    } else {
        echo "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/api_project/css/signup.css">
</head>
<body>
<div class="nav">
		<div class="logo"><h2>oIlR</h2></div>
			<div class="navlinks">
	<ul>
		<li><a href="home.php" class="navborder">Home</a></li>
		<li><a href="signup.php" class="navborder">Sign Up</a></li>
  </ul>
 </div>
</div>
    
    <div class="container">
        <div class="signup-form">
            <h1 style="color: crimson; text-align: center;">Login</h1>
            <hr><br>

            <form action="login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <button type="submit" class="signup">Login</button>
            </form>

            <p style="text-align: center;">
                Don't have an account? <a href="signup.php" class="btn-login">Sign up here</a>.
            </p>
            <br>
            <div class="forget">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
        </div>
    </div>

</body>
</html>


