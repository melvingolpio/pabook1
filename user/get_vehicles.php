<?php
include '../dbconn.php';

session_start();

if (!isset($_GET['type'])) {
    error_log("Type parameter missing");
    echo json_encode([]);
    exit();
}

if (!isset($_SESSION['id'])) {
    error_log("User ID missing");
    echo json_encode([]);
    exit();
}

$type = $_GET['type'];
$user_id = $_SESSION['id'];
$role = $_SESSION['role'];

// Check if the user has any valid receipt tokens for the requested vehicle type
$token_query = "
    SELECT v.plate_number
    FROM vehicle v
    INNER JOIN receipts r ON v.plate_number = r.plate_number
    WHERE v.user_id = ? AND v.vehicle_type = ? AND r.receipt_token IS NOT NULL
";
$token_stmt = $conn->prepare($token_query);
$token_stmt->bind_param('is', $user_id, $type);
$token_stmt->execute();
$token_result = $token_stmt->get_result();

$valid_plate_numbers = [];
while ($row = $token_result->fetch_assoc()) {
    $valid_plate_numbers[] = $row['plate_number'];
}

if (empty($valid_plate_numbers)) {
    error_log("No valid receipt tokens found for User ID: " . $user_id . " with Vehicle Type: " . $type);
    echo json_encode([]);
    exit();
}

$previlage_role = ($role === 'president' || $role === 'vice_president');

try {
    if ($previlage_role) {
        $sql = "SELECT * FROM vehicle WHERE vehicle_type = ? AND user_id = ?";
    } else {
        $sql = "SELECT * FROM vehicle WHERE vehicle_type = ? AND user_id = ? AND paid IS NOT NULL";
    }

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Prepare Failed: " . $conn->error);
        echo json_encode([]);
        exit(); 
    }

    $stmt->bind_param('si', $type, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        error_log("Query Failed: " . $stmt->error);
        echo json_encode([]);
        exit();
    }

    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        // Only add vehicles with plate numbers that have valid receipt tokens
        if (in_array($row['plate_number'], $valid_plate_numbers)) {
            $vehicles[] = $row;
        }
    }

    error_log("Vehicles Found: " . count($vehicles));

    header('Content-Type: application/json');
    echo json_encode($vehicles);
    
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo json_encode([]);
}
