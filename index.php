<?php

header('Access-Control-Allow-Origin: http://localhost:4200/');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Credentials: true');

// Database connection configuration
$servername = "localhost"; // MySQL server address
$username = "admin"; // MySQL username
$password = ""; // MySQL password

try {
    // Create a new PDO instance to establish a database connection
    $conn = new PDO("mysql:host=$servername;dbname=test", $username, $password);
    
    // Set the PDO error mode to exception to enable error handling
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // If the connection is successful, print a success message
    echo "Connected successfully";
} catch(PDOException $e) {
    // If connection fails, print the error message
    echo "Connection failed: " . $e->getMessage();
}
?>
