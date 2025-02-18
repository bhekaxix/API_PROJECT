<?php
include 'dbconnection.php';

class Dispatch {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        try {
            $sql = "SELECT * FROM dispatch WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['dispatchid'] = $user['dispatchid'];
                $_SESSION['name'] = $user['name'];
                header("Location: dispatchpanel.php");
                exit();
            } else {
                return "Invalid email or password.";
            }
        } catch (PDOException $e) {
            return "Login failed. Please try again.";
        }
    }
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $dispatch = new Dispatch($conn);
    $error = $dispatch->login($email, $password);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/api_project/css/signup.css">
    <script src="login.js"></script>
    <title>Dispatch Login</title>
</head>
<body>
    <div class="nav">
        <div class="logo"><h2>oIlR</h2></div>
        <div class="navlinks">
            <ul>
                <li><a href="home.html" class="navborder">Home</a></li>
                <li><a href="signup.php" class="navborder">Sign Up</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <form method="post" id="loginForm" onsubmit="return validateForm()" class="signup-form">
            <h2>Login</h2>
            <hr>
            <br>
            <?php if ($error): ?>
                <p style="color: red;"> <?= htmlspecialchars($error) ?> </p>
            <?php endif; ?>
            <br>
            Email:<input type="email" placeholder="Enter your Email" name="email" id="email" required><div class="error" id="errorEmail"></div>
            Password:<input type="password" placeholder="Enter your Password" name="password" id="password" required><div class="error" id="errorPassword"></div>
            <input type="submit" class="signup" name="Login" value="Login">
            <br>
            <br>
            <hr>
            <br>
            <div class="forget">
                <div><p>Don't have an account?</p>
                Click <a href="signup.php">here</a>
            </div>
        </form>
    </div>
</body>
</html>
