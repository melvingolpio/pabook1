<?php
require('../dbconn.php');

// Make sure to fetch slot data for all slots, not just one user
$query = "SELECT slot_number, status FROM reservations";  // Fetch status for all slots
$result = $conn->query($query);

$statuses = [];
while ($row = $result->fetch_assoc()) {
    $statuses[] = $row;  // Store all slot data in an array
}

echo json_encode($statuses);  // Return JSON response for all slots
?>
