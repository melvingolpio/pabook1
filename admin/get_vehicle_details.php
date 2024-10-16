<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require('../dbconn.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']);

    $query = "SELECT plate_number, vehicle_brand, vehicle_type, color, amount, paid
              FROM vehicle
              WHERE user_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $vehicles = [];
            while ($row = $result->fetch_assoc()) {
                $vehicles[] = $row;
            }
            echo json_encode(['vehicles' => $vehicles]);
        } else {
            echo json_encode(['error' => 'Failed to execute query: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['error' => 'Failed to prepare query: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
