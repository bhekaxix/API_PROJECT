<?php
include 'dbconnection.php';

class AdminLogin {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        try {
            // Retrieve user by email
            $sql = "SELECT * FROM admins WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if ($admin && password_verify($password, $admin['password'])) {
                session_start();
                $_SESSION['adminid'] = $admin['adminid'];
               
                header("Location: dashboard.php");  // Redirect to admin dashboard
                exit();
            } else {
                echo "Invalid credentials!";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminLogin = new AdminLogin($conn);
    $adminLogin->login($_POST['email'], $_POST['password']);
}
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/api_project/css/signup.css">
	<script src="signup.js"></script>

	<title> Admin Login</title>
</head>
<body>
	<div class="nav">
		<div class="logo"><h2>oIlR</h2></div>
			<div class="navlinks">
	<ul>
		<li><a href="home.php" class="navborder">Home</a></li>
		<li><a href="asignup.php" class="navborder">Sign Up</a></li>
  </ul>
 </div>
</div>

<div class="container">
	<form method="post" id="signupForm" onsubmit="return validateForm()" class="signup-form">
		<h2 style="color: crimson;">Login</h2>
		<hr>
		<br>
		<br>
		AdminID:<input type="varchar" placeholder="Enter your AminID" name="adminid"id="adminid" required><div class="error" id="errorContact"></div>
		
		Password:<input type="password" placeholder="Enter your Password" name="password" id="password"><div class="error" id="errorPassword" required></div>
		<input type="submit" class ="signup" name="SignUp" value="Login">
		<br>
		<br>
		<hr>
		<br>
		<div class="forget">
			<div><p>Sign Up</p>
			Click <a href="asignup.php">here</a>
		</div>


	</form>
		
		
			
		
</body>
</html>