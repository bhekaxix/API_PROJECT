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
    $dbConnection = new DatabaseConnection('localhost', 'assignmentii', 'root', '');
    $conn = $dbConnection->connect();

    $userLogin = new UserLogin($conn);

    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = $userLogin->authenticate($username, $password);

    if ($user) {
        $otp = $userLogin->updateOTP($user['id']);
        $userLogin->sendOtpEmail($user['email'], $user['username'], $otp);

        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
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
    <title>Login Page</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
    <div id="login">
        <h3 class="text-center text-white pt-5">Login form</h3>
        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12">
                        <form id="login-form" class="form" action="" method="post">
                            <h3 class="text-center text-info">Login</h3>
                            <div class="form-group">
                                <label for="username" class="text-info">Username:</label><br>
                                <input type="text" name="username" id="username" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="password" class="text-info">Password:</label><br>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="remember-me" class="text-info"><span>Remember me</span> <span><input id="remember-me" name="remember-me" type="checkbox"></span></label><br>
                                <input type="submit" name="submit" class="btn btn-info btn-md" value="Submit">
                            </div>
                            <div id="register-link" class="text-right">
                                <a href="forgot_password.php" class="text-info">Forgot Password?</a><br>
                                <a href="reset_password.php" class="text-info">Reset Password?</a><br>
                                <a href="signup.php" class="text-info">Register here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
