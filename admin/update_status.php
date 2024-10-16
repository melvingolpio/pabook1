<?php
session_start();
require('../dbconn.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['slot_id']) && isset($_GET['status'])) {
    $slot_id = intval($_GET['slot_id']);
    $status = $_GET['status'];

    // Prepare the statement for updating the parking slot status
    $sql_update_slot = "UPDATE parking_slots SET status = ? WHERE slot_id = ?";
    $stmt_update_slot = $conn->prepare($sql_update_slot);
    $stmt_update_slot->bind_param('si', $status, $slot_id);

    if ($stmt_update_slot->execute()) {
        // If status is available, delete corresponding reservation
        if ($status === 'available') {
            // Log the attempt to delete
            error_log("Attempting to delete reservation for slot ID: $slot_id\n");  // Logs to error log

            // Check if there are any reservations for this slot
            $sql_check_reservation = "SELECT id FROM reservations WHERE slot_id = ? AND status = 'reserved'";
            $stmt_check_reservation = $conn->prepare($sql_check_reservation);
            $stmt_check_reservation->bind_param('i', $slot_id);
            $stmt_check_reservation->execute();
            $result_check_reservation = $stmt_check_reservation->get_result();

            if ($result_check_reservation->num_rows > 0) {
                // Proceed to delete the reservation
                $sql_delete_reservation = "DELETE FROM reservations WHERE slot_id = ? AND status = 'reserved'";
                $stmt_delete_reservation = $conn->prepare($sql_delete_reservation);
                $stmt_delete_reservation->bind_param('i', $slot_id);

                if ($stmt_delete_reservation->execute()) {
                    if ($stmt_delete_reservation->affected_rows > 0) {
                        error_log("Reservation deleted for slot ID: $slot_id\n");
                    } else {
                        error_log("No reservation found for slot ID: $slot_id\n");
                    }
                } else {
                    error_log("Error deleting reservation: " . htmlspecialchars($stmt_delete_reservation->error) . "\n");
                }

                $stmt_delete_reservation->close();
            } else {
                error_log("No reservations found for slot ID: $slot_id before deletion.\n");
            }

            $stmt_check_reservation->close();
        }

        error_log("Parking slot status updated to $status for slot ID: $slot_id\n");
    } else {
        error_log("Error updating parking slot status: " . htmlspecialchars($stmt_update_slot->error) . "\n");
    }

    $stmt_update_slot->close();
} else {
    error_log("Invalid request.\n");
}

$conn->close();
?>
