<?php
class DatabaseConnection {
    private $host;
    private $dbname;
    private $username;
    private $password;
    public $conn;

    public function __construct($host, $dbname, $username, $password) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $this->conn;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}

class OTPVerification {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verifyAndClearOTP($email, $verificationCode) {
        $user = $this->getUserByEmail($email);

        if ($user && $user['otp_code'] === $verificationCode && strtotime($user['otp_expiration']) > time()) {
            $stmt = $this->conn->prepare("UPDATE users SET is_verified = 1, otp_code = NULL, otp_expiration = NULL WHERE email = :email");
            $stmt->execute([':email' => $email]);
            return true;
        }
        return false;
    }
}

// Main Logic
if (isset($_GET['email'])) {
    $email = htmlspecialchars(trim($_GET['email']));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dbConnection = new DatabaseConnection('localhost', 'assignmentii', 'root', '');
        $conn = $dbConnection->connect();

        $otpVerification = new OTPVerification($conn);
        $verificationCode = htmlspecialchars(trim($_POST['verificationCode']));

        if ($otpVerification->verifyAndClearOTP($email, $verificationCode)) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid or expired verification code.";
        }
    }
} else {
    $error = "Invalid request.";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="form-container col-md-6">
            <h1 class="text-center mb-4">Verify Your Account</h1>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="verification.php?email=<?php echo urlencode($email); ?>" method="POST">
                <div class="mb-3">
                    <label for="verificationCode" class="form-label">Verification Code:</label>
                    <input type="text" id="verificationCode" name="verificationCode" class="form-control" placeholder="Enter the code sent to your email" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Verify</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
