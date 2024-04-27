<?php

header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Get the request method (e.g., GET, POST, etc.)
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Include the Sections class file
include('../../model/Sections.php');

// Create a new instance of the Sections class
$api = new Sections();

// Switch statement to handle different request methods
switch ($requestMethod) {
    case 'GET':
        // Default values for pagination, searching, sorting, and date range
        $page = 1;
        $limit = 10;
        $search = null;
        $sortBy = 'CreatedAt';
        $sortOrder = 'DESC';
        $fromDate = null;
        $toDate = null;

        // Check and override default values if parameters are present in the URL
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }

        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        if (isset($_GET['search'])) {
            $search = $_GET['search'];
        }

        if (isset($_GET['sortBy'])) {
            $sortBy = $_GET['sortBy'];
        }

        if (isset($_GET['sortOrder'])) {
            $sortOrder = $_GET['sortOrder'];
        }

        if (isset($_GET['fromDate'])) {
            $fromDate = $_GET['fromDate'];
        }

        if (isset($_GET['toDate'])) {
            $toDate = $_GET['toDate'];
        }

        // Call the getUsers method of the Users class and pass parameters to it
        $api->getSections($page, $limit, $search, $sortBy, $sortOrder, $fromDate, $toDate);
        break;
    default:
        // If the request method is not GET, return a 405 Method Not Allowed HTTP status code
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
        break;
}
?>
