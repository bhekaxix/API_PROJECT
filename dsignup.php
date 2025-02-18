<?php
include 'dbconnection.php';

class Dispatch {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($name, $email, $password) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO dispatch (name, email, password) VALUES (:name, :email, :password)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $dispatch = new Dispatch($conn);
    if ($dispatch->register($name, $email, $password)) {
        header("Location: dlogin.php");
        exit();
    } else {
        echo "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/api_project/css/signup.css">
    <script src="signup.js"></script>
    <title>Dispatch Sign Up</title>
</head>
<body>
    <div class="nav">
        <div class="logo"><h2>oIlR</h2></div>
        <div class="navlinks">
            <ul>
                <li><a href="home.html" class="navborder">Home</a></li>
                <li><a href="dlogin.html" class="navborder">Login</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <form method="post" id="signupForm" onsubmit="return validateForm()" class="signup-form">
            <h2 style="color: crimson;">Sign up</h2>
            <hr>
            <br>
            <br>
            Name:<input type="text" placeholder="Enter your Name" name="name" id="name" required><div class="error" id="errorName"></div>
            Email:<input type="email" placeholder="Enter your Email" name="email" id="email" required><div class="error" id="errorEmail"></div>
            Password:<input type="password" placeholder="Enter your Password" name="password" id="password" required><div class="error" id="errorPassword"></div>
            <input type="submit" class ="signup" name="SignUp" value="Sign Up">
            <br>
            <br>
            <hr>
            <br>
            <div class="forget">
                <div><p>Already have an account?</p>
                Click <a href="dlogin.php">here</a>
            </div>
        </form>
    </div>
</body>
</html>
