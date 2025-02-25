<?php 
require 'dbconnection.php'; // Ensure database connection


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to place an order.");
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Class for handling orders
class Order {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function placeOrder($user_id, $item_id, $liters) {
        // Ensure amount is in multiples of 50 liters
        if ($liters % 50 !== 0) {
            throw new Exception("Amount must be in multiples of 50 liters.");
        }

        // Convert liters to stock units (50 liters = 1 unit)
        $stock_units = $liters / 50;

        try {
            $this->conn->beginTransaction();

            // Check stock availability
            $stmt = $this->conn->prepare("SELECT stock FROM items WHERE item_id = :item_id");
            $stmt->execute(['item_id' => $item_id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$item) {
                throw new Exception("Item not found.");
            }

            if ($item['stock'] < $stock_units) {
                throw new Exception("Insufficient stock available.");
            }

            // Calculate total price (400 per 50 liters)
            $total_price = $stock_units * 400;

            // Insert order into the orders table
            $stmt = $this->conn->prepare("INSERT INTO orders (user_id, item_id, quantity, total_price, status, order_date) 
                                          VALUES (:user_id, :item_id, :quantity, :total_price, 'Pending', NOW())");
            $stmt->execute([
                'user_id' => $user_id,
                'item_id' => $item_id,
                'quantity' => $liters, 
                'total_price' => $total_price
            ]);

            // Reduce stock based on stock units (not liters)
            $stmt = $this->conn->prepare("UPDATE items SET stock = stock - :stock_units WHERE item_id = :item_id");
            $stmt->execute([
                'stock_units' => $stock_units,
                'item_id' => $item_id
            ]);

            $this->conn->commit();

            header("Location: delivery.php");
            exit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }
}

// Process order submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (!isset($_POST['oiltype']) || !isset($_POST['amount'])) {
            throw new Exception("All fields are required.");
        }

        $oiltype = $_POST['oiltype'];
        $liters = (int)$_POST['amount']; // Convert to integer

        // Mapping oil types to item_id
        $oil_mapping = [
            "Sunflower Oil" => 2,
            "Soya Oil" => 1,
            "Vegetable Oil" => 3
        ];

        if (!array_key_exists($oiltype, $oil_mapping)) {
            throw new Exception("Invalid product selected.");
        }

        $item_id = $oil_mapping[$oiltype];

        // Place order
        $order = new Order($conn);
        $order->placeOrder($user_id, $item_id, $liters);

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Your Product</title>
    <link rel="stylesheet" href="/api_project/css/order.css">
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php">Home</a>
        <a href="#">About Us</a>
        <a href="#">Contact Us</a>
    </div>
    <br>

    <div class="container">
        <h1>Order Now!</h1>
        <form method="post">
            <table align="center">
                <tr>
                    <th></th>
                    <th>Oil Type</th>
                    <th>Amount (Liters)</th>
                    <th></th>
                </tr>
                <tr>
                    <td><img src="sunflower.jpg" width="150" height="150"></td>
                    <td>
                        <input type="radio" value="Sunflower Oil" name="oiltype" required>Sunflower Oil
                    </td>
                    <td><input type="number" name="amount" min="50" step="50" value="50" required></td>
                </tr>
                <tr>
                    <td><img src="soya.jpg" width="150" height="150"></td>
                    <td>
                        <input type="radio" value="Soya Oil" name="oiltype" required>Soya Oil
                    </td>
                    <td><input type="number" name="amount" min="50" step="50" value="50" required></td>
                </tr>
                <tr>
                    <td><img src="vegetable.jpg" width="150" height="150"></td>
                    <td>
                        <input type="radio" value="Vegetable Oil" name="oiltype" required>Vegetable Oil
                    </td>
                    <td><input type="number" name="amount" min="50" step="50" value="50" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><input class="btn" type="submit" value="Order"></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>
