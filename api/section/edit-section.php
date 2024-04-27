<?php

header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Get the request method (e.g., GET, POST, etc.)
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Include the Sections class file
include('../../model/Sections.php');

// Create a new instance of the Sections class
$api = new Sections();

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
        if (!isset($requestData['section_name']) && !isset($requestData['section_code'])) {
            // If required fields are missing, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Section Name & Section Code at least one required."));
            break;
        }

        if(!isset($requestData['sectionId'])){
            // If required section Id, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Section Id is Medatory."));
            break;
        }

        // Get input data
        $section_name = isset($requestData['section_name']) ? $requestData['section_name'] : '';
        $section_code = isset($requestData['section_code']) ? $requestData['section_code'] : '';
        $sectionId = $requestData['sectionId'] ? $requestData['sectionId'] : '';

        // Validate section_code as integer
        if (!filter_var($section_code, FILTER_VALIDATE_INT)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Section Code must be an integer."));
            break;
        }

        // Call the updateSectionById method of the Sections class and pass validated data to it
        $api->updateSectionById($sectionId, $section_name, $section_code);
        break;
    default:
        // If the request method is not POST, you can handle it here (optional)
        // For example, you might want to return a 405 Method Not Allowed HTTP status code
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
        break;
}

?>
