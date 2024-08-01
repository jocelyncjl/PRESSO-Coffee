<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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

// Get order_id from query parameter
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id === 0) {
    echo json_encode(array("message" => "No order ID provided"));
    exit;
}

// Prepare SQL to get order details
$order_query = "SELECT o.id, o.user_id, o.total_amount, o.order_date, u.name as customer_name 
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ?";

$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows === 0) {
    echo json_encode(array("message" => "Order not found"));
    exit;
}

$order = $order_result->fetch_assoc();

// Get order items
$items_query = "SELECT oi.coffee_id, c.name as coffee_name, oi.quantity, oi.price 
                FROM order_items oi 
                JOIN coffees c ON oi.coffee_id = c.id 
                WHERE oi.order_id = ?";

$items_stmt = $conn->prepare($items_query);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

$order['items'] = array();
while ($item = $items_result->fetch_assoc()) {
    $order['items'][] = $item;
}

echo json_encode($order);

$conn->close();
?>