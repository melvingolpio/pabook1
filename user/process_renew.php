<?php
session_start();
require('../dbconn.php');

if ($_SESSION['type'] != 'User') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['id'];
    $plate_number = $_POST['plate_number'];
    $payment_method = $_POST['payment_method'];

    if (empty($plate_number) || empty($payment_method)) {
        echo "<script>alert('All fields are required.'); window.location.href = 'online_renew.php';</script>";
        exit();
    }

    $query = "UPDATE receipts SET expiration_date = DATE_ADD(expiration_date, INTERVAL 1 YEAR) WHERE plate_number = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $plate_number, $user_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Vehicle renewed successfully!'); window.location.href = 'online_renew.php';</script>";
        } else {
            echo "<script>alert('No record was updated. Please check the plate number.'); window.location.href = 'online_renew.php';</script>";
        }
    } else {
        echo "<script>alert('Failed to update the expiration date. Please try again.'); window.location.href = 'online_renew.php';</script>";
    }
    
    $stmt->close();
    $conn->close();
} else {
  
    header("Location: online_renew.php");
    exit();
}
?>
