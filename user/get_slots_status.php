<?php
require('../dbconn.php');

$query = "SELECT slot_number, status FROM reservations";
$result = $conn->query($query);

$statuses = [];
while ($row = $result->fetch_assoc()) {
    $statuses[] = $row;
}

echo json_encode($statuses);
?>
