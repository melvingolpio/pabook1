<?php
session_start();
require('../dbconn.php');

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Query the status of the 6 parking slots
$reservation_query = "SELECT * FROM parking_slots";
$reservation_result = $conn->query($reservation_query);

$reservations = [];
while ($row = $reservation_result->fetch_assoc()) {
    $reservations[$row['slot_id']] = array('status' => $row['status']);
}

header('Content-Type: application/json');
echo json_encode($reservations);
?>
