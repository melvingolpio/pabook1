<?php
$host = "us-cluster-east-01.k8s.cleardb.net";
$username = "b5f6a402460fa3";
$password = "83f06a6b"; 
$dbname = "heroku_706906bb621a740";

$conn = new mysqli($host, $username, $password, $dbname);

// Change the request method to POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['slot_id']) && isset($_POST['status'])) {
    $slot_id = intval($_POST['slot_id']);
    $status = $_POST['status'];

    // Prepare the statement for updating the parking slot status
    $sql_update_slot = "UPDATE parking_slots SET status = ? WHERE slot_id = ?";
    $stmt_update_slot = $conn->prepare($sql_update_slot);
    $stmt_update_slot->bind_param('si', $status, $slot_id);

    if ($stmt_update_slot->execute()) {
        // Log the update for parking slot
        error_log("Parking slot status updated to $status for slot ID: $slot_id\n");

        // Handle reservation status update
        if ($status === 'available') {
            // If slot is available, delete corresponding reservation
            error_log("Attempting to delete reservation for slot ID: $slot_id\n");

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
        } else if ($status === 'reserved') {
            // If slot is reserved, update reservation status to 'reserved'
            $sql_update_reservation = "UPDATE reservations SET status = 'reserved' WHERE slot_id = ?";
            $stmt_update_reservation = $conn->prepare($sql_update_reservation);
            $stmt_update_reservation->bind_param('i', $slot_id);

            if ($stmt_update_reservation->execute()) {
                error_log("Reservation status updated to 'reserved' for slot ID: $slot_id\n");
            } else {
                error_log("Error updating reservation status: " . htmlspecialchars($stmt_update_reservation->error) . "\n");
            }
            $stmt_update_reservation->close();
        } else if ($status === 'occupied') {
            // If slot is occupied, update reservation status to 'occupied'
            $sql_update_reservation = "UPDATE reservations SET status = 'occupied' WHERE slot_id = ? AND status = 'reserved'";
            $stmt_update_reservation = $conn->prepare($sql_update_reservation);
            $stmt_update_reservation->bind_param('i', $slot_id);

            if ($stmt_update_reservation->execute()) {
                error_log("Reservation status updated to 'occupied' for slot ID: $slot_id\n");
            } else {
                error_log("Error updating reservation status: " . htmlspecialchars($stmt_update_reservation->error) . "\n");
            }
            $stmt_update_reservation->close();
        }

        echo json_encode(["success" => true, "message" => "Status updated"]);
    } else {
        error_log("Error updating parking slot status: " . htmlspecialchars($stmt_update_slot->error) . "\n");
        echo json_encode(["success" => false, "message" => "Error updating status"]);
    }

    $stmt_update_slot->close();
} else {
    error_log("Invalid request.\n");
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
