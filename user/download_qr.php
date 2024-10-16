<?php 
session_start();
require('../dbconn.php');

$userId = $_SESSION['id'];

$query = "SELECT qr_code FROM receipts WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $qrCodeData = $row['qr_code'];

    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="qr_code.png"');
    echo $qrCodeData;
    exit(); 
} else {
    echo "No QR code";
}
?>
