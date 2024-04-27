<?php

header('Access-Control-Max-Age: 3600');
header('Content-Type: application/json; charset=UTF-8');

// Get the request method (e.g., GET, POST, etc.)
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Include the LossOfPointers class file
include('../../model/LossOfPointers.php');

// Create a new instance of the LossOfPointers class
$api = new LossOfPointers();

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
        if (!isset($requestData['DV_No']) || !isset($requestData['DV_Date']) || !isset($requestData['Bill_Description']) || !isset($requestData['Amount']) || !isset($requestData['Beneficiary_Name']) || !isset($requestData['IFSC_Code']) || !isset($requestData['Account_No']) || !isset($requestData['Remark'])) {
            // If required fields are missing, throw a 400 Bad Request error
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("status" => false, "error" => "Required fields are missing."));
            break;
        }

        // Get input data
        $dvNo = $requestData['DV_No'];
        $dvDate = $requestData['DV_Date'];
        $billDescription = $requestData['Bill_Description'];
        $amount = $requestData['Amount'];
        $beneficiaryName = $requestData['Beneficiary_Name'];
        $ifscCode = $requestData['IFSC_Code'];
        $accountNo = $requestData['Account_No'];
        $remark = isset($requestData['Remark']) ? $requestData['Remark'] : "";
        $chequeNo = isset($requestData['Cheque_No']) ? $requestData['Cheque_No'] : "";
        $valueDate = isset($requestData['Value_Date']) ? $requestData['Value_Date'] : null;
        $releaseDate = isset($requestData['Release_Date']) ? $requestData['Release_Date'] : null;
        $utrNo = isset($requestData['UTR_No']) ? $requestData['UTR_No'] : "";
        $rejectedReason = isset($requestData['Rejected_Reason']) ? $requestData['Rejected_Reason'] : "";
        $status = isset($requestData['Rejected_Reason']) ? $requestData['Status'] : "PENDING";

        // Call the createLossOfPointers method of the LossOfPointers class and pass validated data to it
        $api->createLossOfPointers($dvNo, $dvDate, $billDescription, $amount, $beneficiaryName, $ifscCode, $accountNo, $remark, $chequeNo, $valueDate, $releaseDate, $utrNo, $rejectedReason, $status);
        break;
    default:
        // If the request method is not POST, you can handle it here (optional)
        // For example, you might want to return a 405 Method Not Allowed HTTP status code
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(array("status" => false, "error" => "Method Not Allowed"));
        break;
}

?>
