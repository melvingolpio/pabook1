<?php
session_start();
require('../dbconn.php'); 

if ($_SESSION['type'] != 'User') {
    header("Location: ../login.php"); 
    exit();
}

$user_id = $_SESSION['id'];

function isPlateNumberPaid($conn, $plate_number, $user_id) {
    $query = "SELECT paid FROM vehicle WHERE plate_number = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $plate_number, $user_id);
    $stmt->execute();
    $stmt->bind_result($paid);
    $stmt->fetch();
    $stmt->close();
    
    return $paid == 1;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = isset($_POST['amount']) ? $_POST['amount'] : null;
    $plate_number = isset($_POST['plate_number']) ? $_POST['plate_number'] : null;

    if ($amount && $plate_number) {

        if (isPlateNumberPaid($conn, $plate_number, $user_id)) {
            echo "<script>alert('This plate number has already been paid.');</script>";
            exit();
        }

        error_log("Amount: " . htmlspecialchars($amount));
        error_log("Plate Number: " . htmlspecialchars($plate_number));

        $query = "UPDATE vehicle SET paid = ?, amount = ? WHERE plate_number = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $paid = 1; 

        $stmt->bind_param('idsi', $paid, $amount, $plate_number, $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('Payment successful'); window.location.href = 'online_payment.php';</script>";
            exit();
        } else {
            error_log("Error updating data: " . $stmt->error);
            echo "Error updating data.";
        }
        $stmt->close();
    } else {
        echo "Invalid data.";
    }
}

$conn->close();
?>
