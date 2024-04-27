<?php

header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Get the request method (e.g., GET, POST, etc.)
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Include the Receipts class file
include('../../model/Receipts.php');

// Create a new instance of the Receipts class
$api = new Receipts();
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
        if (!isset($requestData['receipt_no']) && !isset($requestData['receipt_date']) && !isset($requestData['amount']) && !isset($requestData['amount_in_words']) && !isset($requestData['section']) && !isset($requestData['remark'])) {
            // If required fields are missing, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Receipt No, Receipt Date, Amount, Amount In Words, Section, and Remark at least one required."));
            break;
        }

        if(!isset($requestData['receiptId'])){
            // If required section Id, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Receipt Id is Medatory."));
            break;
        }

        // Get input data
        $receipt_no = isset($requestData['receipt_no']) ? $requestData['receipt_no'] : '';
        $receipt_date = isset($requestData['receipt_date']) ? $requestData['receipt_date'] : '';
        $amount = isset($requestData['amount']) ? $requestData['amount'] : '';
        $amount_in_words = isset($requestData['amount_in_words']) ? $requestData['amount_in_words'] : '';
        $section = isset($requestData['section']) ? $requestData['section'] : '';
        $remark = isset($requestData['remark']) ? $requestData['remark'] : '';
        $receiptId = $requestData['receiptId'] ? $requestData['receiptId'] : '';

                // Validate receipt_no as integer
                if ($receipt_no && !filter_var($receipt_no, FILTER_VALIDATE_INT)) {
                    header("HTTP/1.1 400 Bad Request");
                    echo json_encode(array("status" => false, "error" => "Receipt No must be an integer."));
                    break;
                }
        
                // Validate receipt_date format (YYYY-MM-DD)
                if ($receipt_date && !DateTime::createFromFormat('Y-m-d', $receipt_date)) {
                    header("HTTP/1.1 400 Bad Request");
                    echo json_encode(array("status" => false, "error" => "Invalid Receipt Date format. It should be in YYYY-MM-DD format."));
                    break;
                }
        
                // Validate amount as integer
                if ($amount && !filter_var($amount, FILTER_VALIDATE_INT)) {
                    header("HTTP/1.1 400 Bad Request");
                    echo json_encode(array("status" => false, "error" => "Amount must be an integer."));
                    break;
                }
        
                // Validate section as integer
                if ($section && !filter_var($section, FILTER_VALIDATE_INT)) {
                    header("HTTP/1.1 400 Bad Request");
                    echo json_encode(array("status" => false, "error" => "Section must be an integer."));
                    break;
                }

        // Call the updateReceiptById method of the Receipt class and pass validated data to it
        $api->updateReceiptById($receiptId, $receipt_no, $receipt_date, $amount, $amount_in_words, $section, $remark);
        break;
    default:
        // If the request method is not POST, you can handle it here (optional)
        // For example, you might want to return a 405 Method Not Allowed HTTP status code
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
        break;
}

?>
