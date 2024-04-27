<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../../model/Users.php');
$api = new Users();
switch($requestMethod) {
	case 'DELETE':
		$userId = '';	
		if($_GET['id']) {
			$userId = $_GET['id'];
		}
		$api->deleteUser($userId);
		break;
	default:
		header("HTTP/1.0 405 Method Not Allowed");
		echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
		break;
}
?>