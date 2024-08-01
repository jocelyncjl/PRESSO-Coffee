<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database connection information
$servername = "localhost";
$username = "root";
$password = "Cjl-19960518";
$dbname = "coffee_shop";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(array("message" => "Connection failed: " . $conn->connect_error)));
}

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS=0");

// SQL to truncate tables
$truncate_order_items = "TRUNCATE TABLE order_items";
$truncate_orders = "TRUNCATE TABLE orders";

if ($conn->query($truncate_order_items) === TRUE && $conn->query($truncate_orders) === TRUE) {
    // Enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
    echo json_encode(array("message" => "Tables cleared successfully"));
} else {
    // Enable foreign key checks if truncation fails
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
    echo json_encode(array("message" => "Error clearing tables: " . $conn->error));
}

$conn->close();
?>
