<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('../dbconn.php');

header('Content-Type: application/json');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']);
    
    $query = "SELECT id, username, CONCAT(first_name, ' ', last_name) AS full_name, gender, birth_date, contact_number, role AS type, email, lto_registration, image, penalty, restricted, license
              FROM users
              WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $user = $userResult->fetch_assoc();

    if ($user) {

        $query = "SELECT plate_number, vehicle_brand, vehicle_type, color, amount, paid
                  FROM vehicle
                  WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $vehicleResult = $stmt->get_result();
                $vehicles = $vehicleResult->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'user' => $user,
            'vehicles' => $vehicles
        ]);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
