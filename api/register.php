<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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
echo "Database connection successful";

// Obtain post data
$data = json_decode(file_get_contents("php://input"));
$name = $data->user->name;
$password = password_hash($data->user->password, PASSWORD_DEFAULT);
$email = $data->user->email;
$age = $data->user->age;
$phone = $data->user->phone;
$address = $data->user->address;

// Prepare sql sentences
$sql = "INSERT INTO users (name, password, email, age, phone, address) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssiis", $name, $password, $email, $age, $phone, $address);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(array("message" => "User registration successful."));
} else {
    echo json_encode(array("message" => "User registration failed: " . $conn->error));
}

$stmt->close();
$conn->close();

?>



