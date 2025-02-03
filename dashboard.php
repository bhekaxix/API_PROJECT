<?php
session_start();

class Dashboard {
    private $session;

    public function __construct($session) {
        $this->session = $session;
    }

    public function checkSession() {
        if (!isset($this->session['user_id'])) {
            header('Location: dashboard.php');
            exit();
        }
    }

    public function displayWelcomeMessage() {
        echo "<h1>Welcome to Your Dashboard!</h1>";
        echo "<p>Your account has been successfully verified.</p>";
    }
}

// Instantiate the Dashboard class and execute methods
$dashboard = new Dashboard($_SESSION);
$dashboard->checkSession();
$dashboard->displayWelcomeMessage();

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet"  href="dashboard.css">
	<title>Oil Refinery</title>
</head>
<body>
	<div class="nav">
		<div class="logo"><h2>oIlR</h2></div>
		<div class="navlinks">
			<ul>
				<li><a class="navborder" href="home.php">Home</a></li>
				<li><a class="navborder" href="#">About Us</a></li>
        <li><a class="navborder" href="#">Contact Us</a></li>
				<li><a class="navborder" href="usersignup.php">Sign Up</a></li>
        <li><a class="navborder" href="userlogin.php">Login</a></li>
			</ul>
		</div>
	</div>
	<div class="container">
		<h1>Order your product</h1>
		<p>
			Our oil refinery company, which specialises in the extraction and purification of oils from various seeds,<br><br>
      including soybeans, sunflower, and more, is a significant participant in the agricultural and food industries.<br><br>
      This refinery uses high-tech methods such as solvent extraction, pressing, and refining to produce high-quality edible oils that adhere to strict health and safety regulations. <br><br>
      This Seed oil refinery creates oils that are not only necessary for cooking and food production but also act as crucial components in a variety of consumer goods, from cosmetics to biofuels, by methodically removing contaminants, unfavourable flavours, and guaranteeing nutritional integrity. In a time of increased health concern, as a company we are essential for providing goods that satisfy consumer preferences and spur innovation.
		</p> 
        <button class="order"><a href="usersignup.php">Order Now</a></button>
  </div>

   <!--<!div class="container2">
       <!h1>About Oil<!/h1>
       <!p>The Oilrefining process has four main steps: degumming,<br><br> deacidification (neutralization), bleaching, and deodorization. For the refining of crude vegetable oils,<br><br> there are two main routes: the chemical and the physical refining.<1br><br> Some by-products of low commercial value are obtained through these refining processes.</p>
      <img class="about" src="pt.jpg" width="500px">
   <div class="aboutoil"></div>

     </div>-->
    <div class="container3">
    	<h1>Our Types of Oil</h1>
    	<div class="oils">
    		<div class="oil1">
    			<h2>$400</h2>
    			<img src="oil.jpg" width="300px" style="margin:30px 0 30px 0">
    			<ul>
    				<li>Sunflower Oil</li>
    				<button class="buy"><a href="usersignup.php">Order Now</a></button>
    			</ul>
    		</div>


    		<div class="oil2">
    			<h2>$400</h2>
    			<img src="soya.jpg" width="300px" style="margin:30px 0 30px 0">
    			<ul>
    				<li>Soya Oil</li>
    				<button class="buy"><a href="usersignup.php">Order Now</a></button>
    			</ul>
    		</div>


    		
    		<div class="oil3">
    			<h2>$400</h2>
    			<img src="vegetable.jpg" width="300px" style="margin:30px 0 30px 0">
    			<ul>
    				<li>Vegetable Oil</li>
    				<button class="buy"><a href="usersignup.php">Order Now</a></button>
    			</ul>
    		</div>
    	</div>
    </div>
    <div class="contact">
    	<h1>GOT SOME QUESTIONS?</h1>
    	<form class="form">
    		<input type="text" placeholder="Name"  is="Name">
    		<input type="email" placeholder="email"  is="email">
    		<input type="number" placeholder=" Mobile No"  is="Mobile No">
    		<textarea placeholder="Message here" id="text-area" rows="5" cols="10"></textarea>
    		<br>
    		<br>
    		<button class="query"><a href="#">Submit</a></button>
    	</form>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <div class="footer">
    	<div class="footer1"><h1>oIlR</h1></div>
    	<br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

    	<div class="footer2">
    		<h2>Services</h2>
    		<ul>
    			<li><a href="usersignup.php">Sign Up</a></li>
    			<li><a href="userlogin.php">Login</a></li>
    			<li><a href="usersignup.php">Order</a></li>


    		</ul>
    	</div>
    	<div class="footer2">
    		<h2>Our Pages</h2>
    		<ul>
    			<li><a href="#">About Us</a></li>
    			<li><a href="#">Contact Us</a></li>
    			
    			

    		</ul>
    	</div>
    	

    </div>






</body>
</html>