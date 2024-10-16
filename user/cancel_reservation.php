<?php
session_start();
require('../dbconn.php');

// Check if the user is logged in and has the appropriate role
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'president' && $_SESSION['role'] != 'vice_president')) {
    echo "Unauthorized access.";
    exit();
}

// Check if the slot number is provided
if (isset($_POST['slot_number'])) {
    $slot_number = $_POST['slot_number'];

    // Check if there is a reservation for the provided slot number
    $query = "SELECT * FROM reservations WHERE slot_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $slot_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Delete the reservation for the given slot number
        $delete_query = "DELETE FROM reservations WHERE slot_number = ?";
        $stmt_delete = $conn->prepare($delete_query);
        $stmt_delete->bind_param('i', $slot_number);

        if ($stmt_delete->execute()) {
            echo "Reservation for slot #$slot_number has been successfully removed.";
        } else {
            echo "Error removing the reservation.";
        }
    } else {
        echo "No reservation found for the given slot number.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Slot number not provided.";
}
?>
