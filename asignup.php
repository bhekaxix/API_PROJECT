<?php
include 'dbconnection.php';

class Admin {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($adminid, $name, $email, $password) {
        try {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the statement
            $sql = "INSERT INTO admins (adminid, name, email, password) VALUES (:adminid, :name, :email, :password)";
            $stmt = $this->conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(":adminid", $adminid);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashedPassword);

            // Execute the query
            if ($stmt->execute()) {
                header("Location: alogin.php");
                exit();
            } else {
                echo "Signup failed.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Handle the signup request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin = new Admin($conn);
    $admin->register($_POST['adminid'], $_POST['name'], $_POST['email'], $_POST['password']);
}
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/api_project/css/signup.css">
	<script src="signup.js"></script>

	<title> Admin Sign Up</title>
</head>
<body>
	<div class="nav">
		<div class="logo"><h2>oIlR</h2></div>
			<div class="navlinks">
	<ul>
		<li><a href="home.php" class="navborder">Home</a></li>
		<li><a href="alogin.php" class="navborder">Login</a></li>
  </ul>
 </div>
</div>

<div class="container">
	<form method="post" id="signupForm" onsubmit="return validateForm()" class="signup-form">
		<h2>Sign up</h2>
		<hr>
		<br>
		<br>
		AdminID:<input type="varchar" placeholder="Enter your AminID" name="adminid"id="adminid" required><div class="error" id="errorContact"></div>
		Name:<input type="text" placeholder="Enter your Name" name="name" id="name" required><div class="error" id="errorName"></div>
		Email:<input type="email" placeholder="Enter your Email" name="email" id="email" required> <div class="error" id="errorEmail"></div>
		Password:<input type="password" placeholder="Enter your Password" name="password" id="password"><div class="error" id="errorPassword" required></div>
		<input type="submit" class ="signup" name="SignUp" value="Sign Up">
		<br>
		<br>
		<hr>
		<br>
		<div class="forget">
			<div><p>Already have an account</p>
			Click <a href="alogin.php">here</a>
		</div>


	</form>
		
		
			
		
</body>
</html>