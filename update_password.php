<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'vendor/autoload.php';
    
    $database = new Database();
    $conn = $database->getConnection();
    $passwordReset = new PasswordReset($conn);
    
    if (isset($_POST['email']) && isset($_POST['new_password'])) {
        $email = $_POST['email'];
        $newPassword = $_POST['new_password'];
        
        $passwordReset->updatePassword($email, $newPassword);
        
        // Redirect to login page after successful password update
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
</head>
<body>
    <h2>Update Password</h2>
    <form action="update_password.php" method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
        
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required><br><br>
        
        <button type="submit">Update Password</button>
    </form>
</body>
</html>
