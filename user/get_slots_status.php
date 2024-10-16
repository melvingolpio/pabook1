<?php
require('../dbconn.php');

// Fetch the parking slot statuses from the database
$query = "SELECT slot_id, status FROM parking_slots";
$result = $conn->query($query);

$slots_status = [];
while ($row = $result->fetch_assoc()) {
    $slots_status[] = $row;
}

// Return the slot statuses as JSON
echo json_encode($slots_status);
?>
