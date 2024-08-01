<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Max-Age: 3600");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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

// Get the posted data
$data = json_decode(file_get_contents("php://input"), true);

// Validate the received data
if (isset($data['user_id']) && isset($data['total_amount']) && isset($data['items']) && is_array($data['items'])) {
    $user_id = $conn->real_escape_string($data['user_id']);
    $total_amount = $conn->real_escape_string($data['total_amount']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert order
        $order_query = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
        $order_stmt = $conn->prepare($order_query);
        $order_stmt->bind_param("id", $user_id, $total_amount);
        $order_stmt->execute();

        $order_id = $conn->insert_id;

        // Insert order items
        $items_query = "INSERT INTO order_items (order_id, coffee_id, quantity, price) VALUES (?, ?, ?, ?)";
        $items_stmt = $conn->prepare($items_query);

        foreach ($data['items'] as $item) {
            $coffee_id = $conn->real_escape_string($item['coffee_id']);
            $quantity = $conn->real_escape_string($item['quantity']);
            $price = $conn->real_escape_string($item['price']);

            $items_stmt->bind_param("iiid", $order_id, $coffee_id, $quantity, $price);
            $items_stmt->execute();
        }

        // Commit transaction
        $conn->commit();

        echo json_encode(array("message" => "Order created successfully", "order_id" => $order_id));
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(array("message" => "Error creating order: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("message" => "Invalid data provided"));
}

$conn->close();
?>