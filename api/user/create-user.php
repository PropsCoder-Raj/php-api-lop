<?php

// Get the request method (e.g., GET, POST, etc.)
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Include the Users class file
include('../../model/Users.php');

// Create a new instance of the Users class
$api = new Users();

// Handle the actual request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // This is a preflight request
    exit();
}

// Switch statement to handle different request methods
switch ($requestMethod) {
    case 'POST':
        // Read the raw POST data
        $postData = file_get_contents('php://input');

        // Decode the JSON data
        $requestData = json_decode($postData, true);

        // Validate input data
        if (!isset($requestData['name']) || !isset($requestData['mobile']) || !isset($requestData['email']) || !isset($requestData['password'])) {
            // If required fields are missing, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Name, Mobile, Email, and Password are required."));
            break;
        }

        // Get input data
        $name = $requestData['name'];
        $mobile = $requestData['mobile'];
        $email = $requestData['email'];
        $password = $requestData['password'];

        // Validate mobile number format
        if (!preg_match("/^\d{10}$/", $mobile)) {
            // If mobile number format is invalid, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Invalid mobile number format."));
            break;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // If email format is invalid, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Invalid email format."));
            break;
        }

        // Encrypt password
        $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Call the createUser method of the Users class and pass validated data to it
        $api->createUser($name, $mobile, $email, $encryptedPassword);
        break;
    default:
        // If the request method is not POST, you can handle it here (optional)
        // For example, you might want to return a 405 Method Not Allowed HTTP status code
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
        break;
}

?>
