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
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['adminid'] = $admin['adminid'];
                header("Location: adminpanel.php");  // Redirect to admin dashboard
                exit();
            } else {
                $GLOBALS['errorMessage'] = "Invalid email or password!";
            }
        } catch (PDOException $e) {
            $GLOBALS['errorMessage'] = "Error: " . $e->getMessage();
        }
    }
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!empty($email) && !empty($password)) {
        $adminLogin = new AdminLogin($conn);
        $adminLogin->login($email, $password);
    } else {
        $GLOBALS['errorMessage'] = "Please enter both email and password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/api_project/css/signup.css">
    <script>
        function validateLogin() {
            let email = document.getElementById("email").value.trim();
            let password = document.getElementById("password").value.trim();
            let valid = true;

            if (email === "") {
                document.getElementById("errorEmail").innerText = "Email is required!";
                valid = false;
            } else {
                document.getElementById("errorEmail").innerText = "";
            }

            if (password === "") {
                document.getElementById("errorPassword").innerText = "Password is required!";
                valid = false;
            } else {
                document.getElementById("errorPassword").innerText = "";
            }

            return valid;
        }
    </script>
    <title>Admin Login</title>
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
        <form method="post" id="loginForm" onsubmit="return validateLogin()" class="signup-form">
            <h2 style="color: crimson;">Admin Login</h2>
            <hr><br><br>

            <?php if (!empty($errorMessage)) { ?>
                <div style="color: red; font-weight: bold;"><?php echo $errorMessage; ?></div>
                <br>
            <?php } ?>

            Email:
            <input type="email" placeholder="Enter your Email" name="email" id="email" required>
            <div class="error" id="errorEmail"></div>

            Password:
            <input type="password" placeholder="Enter your Password" name="password" id="password" required>
            <div class="error" id="errorPassword"></div>

            <input type="submit" class="signup" name="Login" value="Login">
            <br><br><hr><br>

            <div class="forget">
                <p>Don't have an account? Click <a href="asignup.php">here</a> to Sign Up.</p>
            </div>
        </form>
    </div>
</body>
</html>
