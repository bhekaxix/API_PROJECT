<?php
// Include the database connection file
include('dbconnection.php');
require 'vendor/autoload.php'; // Include PHPMailer's autoloader if using Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserRegistration {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function sanitizeInput($input) {
        return htmlspecialchars(trim($input));
    }

    public function isUserExists($email, $username) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
        $stmt->execute([':email' => $email, ':username' => $username]);
        return $stmt->fetch() !== false;
    }

    public function registerUser($firstname, $lastname, $mobile, $username, $email, $password) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otp_expiration = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $stmt = $this->conn->prepare(
            "INSERT INTO users (firstname, lastname, mobile, username, email, password_hash, otp_code, otp_expiration) 
            VALUES (:firstname, :lastname, :mobile, :username, :email, :password_hash, :otp_code, :otp_expiration)"
        );

        $stmt->execute([
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':mobile' => $mobile,
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $password_hash,
            ':otp_code' => $otp,
            ':otp_expiration' => $otp_expiration,
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userRegistration = new UserRegistration($conn);

    $firstname = $userRegistration->sanitizeInput($_POST['firstname']);
    $lastname = $userRegistration->sanitizeInput($_POST['lastname']);
    $mobile = $userRegistration->sanitizeInput($_POST['mobile']);
    $username = $userRegistration->sanitizeInput($_POST['username']);
    $email = $userRegistration->sanitizeInput($_POST['email']);
    $password = $userRegistration->sanitizeInput($_POST['password']);

    try {
        if ($userRegistration->isUserExists($email, $username)) {
            die("Email or username already exists.");
        }

        $otp = $userRegistration->registerUser($firstname, $lastname, $mobile, $username, $email, $password);

        if ($userRegistration->sendOtpEmail($email, "$firstname $lastname", $otp)) {
            header('Location: verification.php?email=' . urlencode($email));
            exit;
        } else {
            echo "Failed to send OTP email.";
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to external CSS -->
    <link href="signup.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="form-container col-md-6" id="signup-form">
            <h1 class="text-center mb-4">Sign Up</h1>
            <form action="signup.php" method="POST">
                <div class="mb-3">
                    <label for="firstname" class="form-label">First Name:</label>
                    <input type="text" id="firstname" name="firstname" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="lastname" class="form-label">Last Name:</label>
                    <input type="text" id="lastname" name="lastname" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="mobile" class="form-label">Mobile Number:</label>
                    <input type="tel" id="mobile" name="mobile" class="form-control" pattern="[0-9]{10}" required>
                    <small class="form-text text-muted">Enter a 10-digit mobile number.</small>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-signup">Sign Up</button>
                </div>
            </form>
            <p class="text-center mt-3">
                Already have an account? 
                <a href="login.php" class="btn btn-login btn-sm">Login here</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
