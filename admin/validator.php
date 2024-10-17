<?php
// validate_qr.php

// Connect to the database
$servername = "us-cluster-east-01.k8s.cleardb.net";
$username = "b5f6a402460fa3";
$password = "83f06a6b";
$dbname = "heroku_706906bb621a740";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    error_log("Connection failed: " . $conn->connect_error);
    exit();
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

        // Use prepared statements to avoid SQL injection
        $stmt = $conn->prepare("SELECT * FROM receipts WHERE receipt_token = ?");
        $stmt->bind_param("s", $receiptToken);
        $stmt->execute();
        $result = $stmt->get_result();

        // Log the query for debugging
        error_log("Executed SQL Query: SELECT * FROM receipts WHERE receipt_token = '$receiptToken'");

        if ($result->num_rows > 0) {
            // QR code found
            echo json_encode(['status' => 'valid']);
        } else {
            // QR code not found
            echo json_encode(['status' => 'invalid']);
        }

        $stmt->close();
    } else {
        // Token not found in the payload
        error_log("Invalid QR Code format: " . $qrCode);
        echo json_encode(['status' => 'invalid', 'message' => 'Invalid QR Code format']);
    }
} else {
    // No QR code received
    error_log("No QR code received");
    echo json_encode(['status' => 'error', 'message' => 'No code provided']);
}

$conn->close();
?>
