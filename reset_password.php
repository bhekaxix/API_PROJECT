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
