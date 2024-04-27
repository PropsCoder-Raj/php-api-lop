<?php

// Get the request method (e.g., GET, POST, etc.)
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Include the Sections class file
include('../../model/Sections.php');

// Create a new instance of the Sections class
$api = new Sections();

// Switch statement to handle different request methods
switch ($requestMethod) {
    case 'GET':
        // Call the getAllSections method of the Sections class and pass parameters to it
        $api->getAllSections();
        break;
    default:
        // If the request method is not GET, return a 405 Method Not Allowed HTTP status code
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
        break;
}
?>
