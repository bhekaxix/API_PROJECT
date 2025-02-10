<?php
include 'connect.php';
if($_SERVER['REQUEST_METHOD'] =='POST') {
	$dispatchid= $_POST['dispatchid'];
	$name = $_POST['name'];
	$email = $_POST['email'];
	$password = $_POST['password'];
  
	$sql = "INSERT INTO dispatch (dispatchid,name,email,password) VALUES ('$dispatchid','$name','$email','$password')";
	$result = mysqli_query($connect,$sql);
	if($result){
		//echo "Signup successful";
		header("location:dlogin.php");

	} else{
		echo "not successful";
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="signup.css">
	<script src="signup.js"></script>

	<title>Dspatch Sign Up</title>
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
		DispatchID:<input type="varchar" placeholder="Enter your AminID" name="dispatchid"id="dispatchid" required><div class="error" id="errorContact"></div>
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
			Click <a href="dlogin.php">here</a>
		</div>


	</form>
		
		
			
		
</body>
</html>