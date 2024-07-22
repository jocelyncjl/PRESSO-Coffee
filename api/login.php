<?php
// Setting up to allow cross-domain requests
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (isset($_SERVER['HTTP_ORIGIN'])) {
    // You can set a list of allowed origins if needed
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
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
    die("Connection failed: " . $conn->connect_error);
}
echo "Database connection successful";

// Obtain post data
$data = json_decode(file_get_contents("php://input"));

var_dump($data);
// Check if the username and password were received
if (!empty($data->username) && !empty($data->password)) {
    $username = $conn->real_escape_string($data->username);
    $password = $data->password;
    // Query the database
    $sql = "SELECT * FROM users WHERE name = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Successful login
            http_response_code(200);
            echo json_encode(array(
                "message" => "Sign in successful",
                "user" => array(
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "email" => $row['email'],
                    "age" => $row['age'],
                    "phone" => $row['phone'],
                    "address" => $row['address']
                )
            ));
        }else{
            // Wrong password
            http_response_code(401);
            echo json_encode(array("message" => "Invalid password"));
        }
    }else{
        // User does not exist
        http_response_code(404);
        echo json_encode(array("message" => "User not found"));
    }
}else{
    // Incomplete data
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data. Username and password are required."));
}

$conn->close();
?>
