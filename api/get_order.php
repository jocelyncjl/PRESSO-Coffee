<?php

session_start();

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

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

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array("message" => "User not logged in"));
    exit;
}

$user_id = $_SESSION['user_id'];

/*// Get user_id from query parameter
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id === 0) {
    echo json_encode(array("message" => "No user ID provided"));
    exit;
}*/

// Prepare SQL to get orders
$orders_query = "SELECT id, total_amount, order_date FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$orders_stmt = $conn->prepare($orders_query);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();

$orders = array();

while ($order = $orders_result->fetch_assoc()) {
    // Get order items for each order
    $items_query = "SELECT oi.coffee_id, c.name as coffee_name, oi.quantity, oi.price 
                    FROM order_items oi 
                    JOIN coffees c ON oi.coffee_id = c.id 
                    WHERE oi.order_id = ?";
    $items_stmt = $conn->prepare($items_query);
    $items_stmt->bind_param("i", $order['id']);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();

    $order['items'] = array();
    while ($item = $items_result->fetch_assoc()) {
        $order['items'][] = $item;
    }

    $orders[] = $order;
}

if (count($orders) > 0) {
    echo json_encode(array("orders" => $orders));
} else {
    echo json_encode(array("message" => "No orders found for this user"));
}

$conn->close();
?>