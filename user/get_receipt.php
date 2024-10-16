<?php 
session_start();
require('../dbconn.php');

if (!isset($_GET['plate_number'])) {
    echo json_encode(['error' => 'Plate number is required']);
    exit();
}

$plate_number = $_GET['plate_number'];

$query = "SELECT r.id, r.plate_number, r.created_at, r.expiration_date, r.qr_code, u.username, u.fullname, u.age, u.gender, u.contact_number, u.type, u.email 
          FROM receipts r
          JOIN users u ON r.user_id = u.id
          WHERE r.plate_number = ?";
          
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('s', $plate_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $data['qr_code'] = base64_encode($data['qr_code']); 
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'No data found']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Failed to prepare the statement: ' . $conn->error]);
}

$conn->close();
?>
