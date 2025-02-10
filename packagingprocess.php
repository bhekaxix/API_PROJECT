<?php
include 'dbconnection.php'; // Ensure this contains the DatabaseConnection class

class OrderManager {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function updateOrderStatus($orderId) {
        try {
            $sql = "UPDATE checkedorders SET status = :status WHERE orderid = :orderid";
            $stmt = $this->conn->prepare($sql);
            $status = "Ready for Packaging";

            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':orderid', $orderId);

            if ($stmt->execute()) {
                return "Order is now ready for packaging.";
            } else {
                return "Error updating order status.";
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
}

// Initialize database connection
$dbConnection = new DatabaseConnection('localhost', '2fa', 'root', '');
$conn = $dbConnection->connect();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['readyForPackaging'])) {
    $orderId = $_POST['orderid'];
    $orderManager = new OrderManager($conn);
    
    echo $orderManager->updateOrderStatus($orderId);
}
?>
