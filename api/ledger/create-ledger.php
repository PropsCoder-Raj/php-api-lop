<?php

header('Access-Control-Max-Age: 3600');
header('Content-Type: application/json; charset=UTF-8');

// Get the request method (e.g., GET, POST, etc.)
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Include the Ledgers class file
include('../../model/Ledgers.php');

// Create a new instance of the Ledgers class
$api = new Ledgers();

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
        if (!isset($requestData['name']) || !isset($requestData['series'])) {
            // If required fields are missing, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Ledger Name & Series are required."));
            break;
        }

        // Get input data
        $name = $requestData['name'];
        $series = isset($requestData['series']) ? $requestData['series'] : "";

        // Validate series as integer
        if ($series && !filter_var($series, FILTER_VALIDATE_INT)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Series must be an integer."));
            break;
        }

        // Call the createLedger method of the Ledger class and pass validated data to it
        $api->createLedger($name, $series);
        break;
    default:
        // If the request method is not POST, you can handle it here (optional)
        // For example, you might want to return a 405 Method Not Allowed HTTP status code
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
        break;
}

?>
