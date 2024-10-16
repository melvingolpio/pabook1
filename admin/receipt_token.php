<?php 
session_start();
require('../dbconn.php');

$query = "SELECT id, receipt_token FROM receipts";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " - Receipt Token: " . $row['receipt_token'] . "<br>";
}
?>