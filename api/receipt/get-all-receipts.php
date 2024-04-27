<?php

// Get the request method (e.g., GET, POST, etc.)
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Include the Receipts class file
include('../../model/Receipts.php');

// Create a new instance of the Receipts class
$api = new Receipts();

// Switch statement to handle different request methods
switch ($requestMethod) {
    case 'GET':
        // Call the getAllReceipts method of the Receipts class and pass parameters to it
        $api->getAllReceipts();
        break;
    default:
        // If the request method is not GET, return a 405 Method Not Allowed HTTP status code
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
        break;
}
?>
