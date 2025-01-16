<?php
include('dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $reset_code = htmlspecialchars(trim($_POST['reset_code']));
    $new_password = htmlspecialchars(trim($_POST['new_password']));

    // Validate the reset code
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = :email AND reset_code = :reset_code AND used = FALSE AND expiration > NOW()");
    $stmt->execute([':email' => $email, ':reset_code' => $reset_code]);

    if ($stmt->rowCount() === 1) {
        // Hash the new password
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);

        // Update the user's password
        $stmt = $conn->prepare("UPDATE users SET password_hash = :password_hash WHERE email = :email");
        $stmt->execute([':password_hash' => $password_hash, ':email' => $email]);

        // Mark the reset code as used
        $stmt = $conn->prepare("UPDATE password_resets SET used = TRUE WHERE email = :email");
        $stmt->execute([':email' => $email]);

        echo "Password has been updated successfully.";
    } else {
        echo "Invalid or expired reset code.";
    }
}
?>
