<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../../model/Ledgers.php');
$api = new Ledgers();

// Handle the actual request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // This is a preflight request
    exit();
}

switch($requestMethod) {
	case 'DELETE':
		$receiptId = '';	
		if($_GET['id']) {
			$receiptId = $_GET['id'];
		}
		$api->deleteLedger($receiptId);
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
	break;
}
?>