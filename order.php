<?php

require 'dbconnection.php'; // Ensure this file is included to establish the connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $orderdate = $_POST['orderdate'];
    $amount = $_POST['amount'];
    $oiltype = $_POST['oiltype'];

    try {
        // Ensure $conn is set from connect.php
        if (!isset($conn)) {
            throw new Exception("Database connection not found.");
        }

        // Prepare the SQL query
        $sql = "INSERT INTO orders (name, orderdate, amount, oiltype) VALUES (:name, :orderdate, :amount, :oiltype)";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':orderdate', $orderdate, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindParam(':oiltype', $oiltype, PDO::PARAM_STR);

        // Execute query
        if ($stmt->execute()) {
            header("Location: delivery.php");
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
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/api_project/css/order.css">
	<script src="order.js"></script>
	<title>Order Your Product</title>
</head>
<body bgcolor="white">
	<div class="navbar">
        <a href="dashboard.php">Home</a>
        <a href="#">About Us</a>
        <a href="#">Contact Us</a>
    </div>
    <br>
    
    <div class="container">
	<h1>Order Now!</h1>
	<form method="post" name="order" onsubmit="return validateForm()">
		Customer Name: <input type="text" size ="20" name="name" id="name" required><br><br>
		Order Date: <input type="date" name="orderdate" id="oildate" required><br><br>
		<table align="center">
			<tr>
				<th></th>
				<th>Oil Type</th>
				<th>Amount</th>
			    <th></th>
			</tr>
			<tr>
                <td><img src="sunflower.jpg" width="150" height="150"></td>
                <td><input type="radio" value="sunflower oil" name="oiltype" id="oiltype_sunflower" required>Sunflower oil</td>
                <td><input type="text" size="10" name="amount" id="amount_sunflower" value="400" readonly></td>
                
            </tr>
            <tr>
                <td><img src="soya.jpg" width="150" height="150"></td>
                <td><input type="radio" value="soya oil" name="oiltype" id="oiltype_soya" required>Soya oil</td>
                <td><input type="text" size="10" name="amount" id="amount_soya" value="400" readonly></td>
                
            </tr>
            <tr>
                <td><img src="vegetable.jpg" width="150" height="150"></td>
                <td><input type="radio" value="vegetable oil" name="oiltype" id="oiltype_vegetable" required>Vegetable oil</td>
                <td><input type="text" size="10" name="amount" id="amount_vegetable" value="400" readonly></td>
                
            </tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td><input type="submit" value="Order"></input></td>
			</tr>
			
		</table>

</div>

</body>
</html>