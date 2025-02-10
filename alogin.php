<?php
include 'connect.php';
if($_SERVER['REQUEST_METHOD'] =='POST') {
  $adminid= $_POST['adminid'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  $sql = "SELECT * FROM admins WHERE adminid='$adminid' AND name ='$name'AND email ='$email'AND password = '$password'";
  $result = mysqli_query($connect,$sql);
  if($result) {
    $rownumber = mysqli_num_rows($result);
    if($rownumber > 0 ) {
      //echo "login successful";
      
      //session

      session_start();
       $_SESSION['adminid'] = $adminid;
       $_SESSION['name'] = $name;
       $_SESSION['email'] = $email;
       $_SESSION['password'] = $password;
      header("location:adminpanel.php");
    }else{
      echo "incorrect credentials";
    }
  }
 // else {
   // echo "not sucessful";
  //}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="signup.css">
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
		Name:<input type="text" placeholder="Enter your Name" name="name" id="name" required><div class="error" id="errorName"></div>
		Email:<input type="email" placeholder="Enter your Email" name="email" id="email" required> <div class="error" id="errorEmail"></div>
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