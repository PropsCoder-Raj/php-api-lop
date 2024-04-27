<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../../model/Sections.php');
$api = new Sections();

// Handle the actual request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // This is a preflight request
    exit();
}

switch($requestMethod) {
	case 'DELETE':
		$sectionId = '';	
		if($_GET['id']) {
			$sectionId = $_GET['id'];
		}
		$api->deleteSection($sectionId);
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
	break;
}
?>