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
    case 'PUT':
        // Read the raw PUT data
        $postData = file_get_contents('php://input');

        // Decode the JSON data
        $requestData = json_decode($postData, true);

        // Validate input data
        if (!isset($requestData['name']) && !isset($requestData['mobile']) && !isset($requestData['email']) && !isset($requestData['status'])) {
            // If required fields are missing, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Name, Mobile, Email, Status at least one field upated."));
            break;
        }

        if(!isset($requestData['userId'])){
            // If required user Id, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "User Id is Medatory."));
            break;
        }

        // Get input data
        $name = isset($requestData['name']) ? $requestData['name'] : '';
        $mobile = isset($requestData['mobile']) ? $requestData['mobile'] : '';
        $email = isset($requestData['email']) ? $requestData['email'] : '';
        $password = isset($requestData['password']) ? $requestData['password'] : '';
        $status = isset($requestData['status']) ? $requestData['status'] : '';
        $userId = $requestData['userId'] ? $requestData['userId'] : '';

        // Validate mobile number format
        if ($mobile && !preg_match("/^\d{10}$/", $mobile)) {
            // If mobile number format is invalid, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Invalid mobile number format."));
            break;
        }

        // Validate email format
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // If email format is invalid, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Invalid email format."));
            break;
        }

        // Call the updateUserById method of the Users class and pass validated data to it
        $api->updateUserById($userId, $name, $email, $mobile, $password, $status);
        break;
    default:
        // If the request method is not POST, you can handle it here (optional)
        // For example, you might want to return a 405 Method Not Allowed HTTP status code
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
        break;
}

?>
