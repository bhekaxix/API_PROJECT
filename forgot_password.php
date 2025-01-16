<?php
include('dbconnection.php');
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));

    // Check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);

    if ($stmt->rowCount() === 1) {
        $reset_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiration = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Insert or update the reset code
        $stmt = $conn->prepare("
            INSERT INTO password_resets (email, reset_code, expiration) 
            VALUES (:email, :reset_code, :expiration)
            ON DUPLICATE KEY UPDATE reset_code = :reset_code, expiration = :expiration, used = FALSE
        ");
        $stmt->execute([':email' => $email, ':reset_code' => $reset_code, ':expiration' => $expiration]);

        // Send email with the reset code
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
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body = "Your password reset code is <strong>$reset_code</strong>. It expires in 15 minutes.";

            $mail->send();
            echo "A reset code has been sent to your email.";
        } catch (Exception $e) {
            echo "Error sending email: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found.";
    }
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
