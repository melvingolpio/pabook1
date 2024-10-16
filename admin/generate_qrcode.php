<?php
session_start();
require('../dbconn.php');
require('../phpqrcode/qrlib.php'); 

if ($_SESSION['type'] !== 'Admin') {
    echo "Access denied.";
    exit();
}

$id = $_GET['id'];

$query = "SELECT username, first_name, last_name, type, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username, $first_name, $last_name, $type, $email);
$stmt->fetch();
$stmt->close();

$current_date = date("Y-m-d");
$expiration_date = date("Y-m-d", strtotime("+1 year"));
$fullname = $first_name . ' ' . $last_name;

$qr_content = "Pabook\nUsername: $username\nCurrent Date: $current_date\nExpiration Date: $expiration_date";
$qr_file = 'qrcodes/' . $username . '_qrcode.png';
QRcode::png($qr_content, $qr_file, QR_ECLEVEL_L, 10);
?>
