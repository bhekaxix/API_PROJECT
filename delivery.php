<?php
session_start();
require 'dbconnection.php'; // Ensure database connection is included

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = $_POST['address'];
    $buildingname = $_POST['buildingname'];
    $instructions = $_POST['instructions'];
    $description = $_POST['description'];

    try {
        // Ensure $conn is available
        if (!isset($conn)) {
            throw new Exception("Database connection not found.");
        }

        // Prepare SQL statement
        $sql = "INSERT INTO delivery (address, buildingname, instructions, description) 
                VALUES (:address, :buildingname, :instructions, :description)";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':buildingname', $buildingname, PDO::PARAM_STR);
        $stmt->bindParam(':instructions', $instructions, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);

        // Execute query
        if ($stmt->execute()) {
            header("Location: home2.php"); // Redirect to home2.php on success
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        input[type="submit"] {
            background-color: crimson;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: darkred;
        }
             .navbar {
            display: flex;
            justify-content: center;
            background-color: crimson;
            color: #fff;
            padding: 10px 0;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
        }
    </style>
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
            <input type="text" placeholder="House Number,Location Details,etc." id="address" name="address" required>

            <input type="text" placeholder="Business or building name" id="buildingname" name="buildingname" required>

        <select type="text" name ="instructions" id="instructions" required>
        <option value="">Delivery instructions</option>
        <option value="Call when arrived">Call when arrived</option>
        <option value="Message when arrived">Message when arrived</option>
        </select><br><br>
            Description:
            <textarea id="description" name="description" rows="4" required></textarea>

            <input type="submit" value="Save">
        </form>
    </div>
</body>
</html>
