<?php
// include('../../services/JWT.php');

// $jwt = new JWT();

// $paylod = [
//     'iat' => time(),
//     'iss' => 'localhost',
//     'exp' => time() + (30),
//     'userId' => 1
// ];

// $token = $jwt->encode($paylod, 'MM');
// echo $token;

// $payload = $jwt->decode('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MTM5MzEyMTAsImlzcyI6ImxvY2FsaG9zdCIsImV4cCI6MTcxMzkzMTI0MCwidXNlcklkIjoxfQ.djuMaV-jU0RTQWRaB6K0qMhMi9iPQN9sJLz2CRlovdg', 'MM', ['HS256']);
// echo json_encode($payload);

?>

<?php

header('Access-Control-Allow-Origin: http://localhost:4200/');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Credentials: true');

// Get the request method (e.g., GET, POST, etc.)
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Include the Users class file
include('../../model/Users.php');
include('../../services/JWT.php');

// Create a new instance of the Users class
$api = new Users();

// Switch statement to handle different request methods
switch ($requestMethod) {
    case 'POST':
        // Read the raw POST data
        $postData = file_get_contents('php://input');

        // Decode the JSON data
        $requestData = json_decode($postData, true);

        // Validate input data
        if (!isset($requestData['email']) || !isset($requestData['password'])) {
            // If required fields are missing, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Email, and Password are required."));
            break;
        }

        $email = $requestData['email'];
        $password = $requestData['password'];

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // If email format is invalid, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Invalid email format."));
            break;
        }

        // Call the createUser method of the Users class and pass validated data to it
        $result = $api->getUserWithEmailPassword($email, $password);
        if($result['status'] == true){
            $jwt = new JWT();
            
            $paylod = [
                'iat' => time(),
                'iss' => 'localhost',
                'exp' => time() + (24 * 60),
                'userId' => $result['data']['Id'],
                'role' => $result['data']['Role']
            ];

            $token = $jwt->encode($paylod, 'MM');

            $result = array(
                'status' => true,
                'data' => $result['data'],
                'message' => $result['message'],
                'token' => $token
            );
        }else{
            $result['status'] = false;
        }
        echo json_encode($result);
        break;
    default:
        // If the request method is not POST, you can handle it here (optional)
        // For example, you might want to return a 405 Method Not Allowed HTTP status code
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
        break;
}

?>
