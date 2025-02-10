<?php
include 'dbconnection.php'; // Ensure this contains the DatabaseConnection class

class OrderManager {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function checkOrder($orderid, $name, $orderdate, $amount, $oiltype) {
        try {
            // Begin Transaction
            $this->conn->beginTransaction();

            // Insert into checked orders
            $insertQuery = "INSERT INTO checkedorders (orderid, name, orderdate, amount, oiltype) 
                            VALUES (:orderid, :name, :orderdate, :amount, :oiltype)";
            $stmt = $this->conn->prepare($insertQuery);
            $stmt->execute([
                ':orderid' => $orderid,
                ':name' => $name,
                ':orderdate' => $orderdate,
                ':amount' => $amount,
                ':oiltype' => $oiltype
            ]);

            // Delete from orders
            $deleteQuery = "DELETE FROM orders WHERE orderid = :orderid";
            $stmt = $this->conn->prepare($deleteQuery);
            $stmt->execute([':orderid' => $orderid]);

            // Commit transaction
            $this->conn->commit();

            return "Order checked and moved to the checked orders database.";
        } catch (PDOException $e) {
            // Rollback transaction on error
            $this->conn->rollBack();
            return "Error: " . $e->getMessage();
        }
    }
}

// Initialize Database Connection
$dbConnection = new DatabaseConnection('localhost', '2fa', 'root', '');
$conn = $dbConnection->connect();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderManager = new OrderManager($conn);
    $message = $orderManager->checkOrder(
        $_POST['orderid'],
        $_POST['name'],
        $_POST['orderdate'],
        $_POST['amount'],
        $_POST['oiltype']
    );

    echo $message;
}
?>
