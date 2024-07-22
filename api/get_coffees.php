<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Database connection information
$servername = "localhost";
$username = "root";
$password = "Cjl-19960518";
$dbname = "coffee_shop";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM coffees";
$result = $conn->query($sql);

$coffees = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $coffees[] = array(
            "id" => $row["id"],
            "name" => $row["name"],
            "description" => $row["description"],
            "price" => floatval($row["price"]),
            "image_url" => $row["image_url"]
        );
    }
}

echo json_encode($coffees);

$conn->close();
?>




