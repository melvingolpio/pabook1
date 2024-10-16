<?php
session_start();
require('../dbconn.php');

// Fetch current status of all parking slots
$query = "SELECT slot_id, status FROM reservations";
$result = $conn->query($query);

$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[$row['slot_id']] = [
        'status' => $row['status']
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($slots);
?>
