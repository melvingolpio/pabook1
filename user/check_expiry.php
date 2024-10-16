<?php
session_start();
require('../dbconn.php');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['id'];
$now = date('Y-m-d H:i:s');

$sql = "SELECT slot_number FROM reservations WHERE expiry_time <= '$now' AND status = 'reserved'";
$result = $conn->query($sql);

$expired_slots = [];
while ($row = $result->fetch_assoc()) {
    $expired_slots[] = $row['slot_number'];

    
    $penalty = 50; 
    $sql_penalty = "UPDATE users SET balance = balance - $penalty WHERE id = '$user_id'";
    $conn->query($sql_penalty);

    $sql_delete = "DELETE FROM reservations WHERE slot_number = ? AND expiry_time <= ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param('is', $row['slot_number'], $now);
    $stmt_delete->execute();
}

echo json_encode(['status' => 'success', 'expiredSlots' => $expired_slots]);

$conn->close();
?>
