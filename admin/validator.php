<?php
// validate_qr.php

// Connect to the database
$host = 'localhost'; 
$user = 'root';
$pass = ''; 
$db = 'pms'; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the QR code parameter is set
if (isset($_GET['code'])) {
    $qrCode = $_GET['code'];
    // Log the received code for debugging
    error_log("Received QR Code: " . $qrCode);
    
    // Extract the token from the payload
    preg_match('/Receipt Token:\s*([0-9a-f]{32})/', $qrCode, $matches);
    if (isset($matches[1])) {
        $receiptToken = $matches[1];

        // Log the extracted token for debugging
        error_log("Extracted Receipt Token: " . $receiptToken);

        // Query the database to check if the receipt token exists
        $sql = "SELECT * FROM receipts WHERE receipt_token = '$receiptToken'";
        $result = $conn->query($sql);

        // Log the query for debugging
        error_log("Executed SQL Query: " . $sql);

        if ($result->num_rows > 0) {
            // QR code found
            echo "valid";
        } else {
            // QR code not found
            echo "invalid";
        }
    } else {
        // Token not found in the payload
        error_log("Invalid QR Code format: " . $qrCode);
        echo "invalid"; // Or handle this case as needed
    }
} else {
    // No QR code received
    error_log("No QR code received");
    echo "No code provided";
}

$conn->close();
?>