<?php

require 'dbconnection.php'; // Ensure database connection is included

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = $_POST['address'];
    $buildingname = $_POST['buildingname'];
    $instructions = $_POST['instructions'];
    $description = $_POST['description'];
    $payment_method = $_POST['payment_method'];

    try {
        // Ensure $conn is available
        if (!isset($conn)) {
            throw new Exception("Database connection not found.");
        }

        // Prepare SQL statement
        $sql = "INSERT INTO delivery (address, buildingname, instructions, description, payment_method) 
                VALUES (:address, :buildingname, :instructions, :description, :payment_method)";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':buildingname', $buildingname, PDO::PARAM_STR);
        $stmt->bindParam(':instructions', $instructions, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);

        // Execute query
        if ($stmt->execute()) {
            header("Location: dashboard.php"); 
            exit();
        } else {
            echo "Order submission failed.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Form</title>
    <link rel="stylesheet" href="/api_project/css/delivery.css">
</head>
<body>
    <div class="navbar">
        <a href="#">Home</a>
        <a href="#">Form</a>
        <a href="#">About Us</a>
        <a href="#">Contact Us</a>
    </div>

    <div class="container">
        <h2>Delivery Form</h2>
        <form method="post">
            <label>Address details:</label>
            <input type="text" placeholder="House Number, Location Details, etc." id="address" name="address" required>

            <input type="text" placeholder="Business or building name" id="buildingname" name="buildingname" required>

            <select name="instructions" id="instructions" required>
                <option value="">Delivery instructions</option>
                <option value="Call when arrived">Call when arrived</option>
                <option value="Message when arrived">Message when arrived</option>
            </select>

            <label>Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label>Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="">Select Payment Method</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
                <option value="Mobile Payment">Mobile Payment</option>
                <option value="Credit/Debit Card">Credit/Debit Card</option>
            </select>

            <input type="submit" value="Save">
        </form>
    </div>
</body>
</html>
