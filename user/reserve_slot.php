<?php 
session_start();
require('../dbconn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['username'])) {
        echo "User not logged in.";
        exit;
    }

    $username_user = $_SESSION['username'];

    // Prepare and execute the query to get user details
    $sql_user = "SELECT id, role FROM users WHERE username = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param('s', $username_user);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows == 0) {
        echo "User not found.";
        exit;
    }

    $row_user = $result_user->fetch_assoc();
    $user_id = $row_user['id'];
    $user_role = $row_user['role'];

    if (isset($_POST['slot_number']) && isset($_POST['plate_number'])) {
        // Handle slot reservation
        $slot_number = $_POST['slot_number'];
        $plate_number = $_POST['plate_number'];

        // Check vehicle type
        $sql_vehicle_type = "SELECT vehicle_type FROM vehicle WHERE plate_number = ?";
        $stmt_vehicle_type = $conn->prepare($sql_vehicle_type);
        $stmt_vehicle_type->bind_param('s', $plate_number);
        $stmt_vehicle_type->execute();
        $result_vehicle_type = $stmt_vehicle_type->get_result();
        
        if ($result_vehicle_type->num_rows == 0) {
            echo "Vehicle type not found.";
            exit;
        }

        $row_vehicle_type = $result_vehicle_type->fetch_assoc();
        $vehicle_type = $row_vehicle_type['vehicle_type'];

        // Check if the user already has a reservation for the vehicle type
        $sql_check_reservation = "SELECT id FROM reservations WHERE user_id = ? AND vehicle_type = ? AND status IN ('reserved', 'occupied')";
        $stmt_check_reservation = $conn->prepare($sql_check_reservation);
        $stmt_check_reservation->bind_param('is', $user_id, $vehicle_type);
        $stmt_check_reservation->execute();
        $result_check_reservation = $stmt_check_reservation->get_result();

        if ($result_check_reservation->num_rows > 0) {
            echo "You already have a reservation for this vehicle type.";
            exit;
        }

        // Check if the plate number is already reserved or occupied
        $sql_check_plate = "SELECT r.id FROM reservations r 
                            WHERE r.plate_number = ? AND r.status IN ('reserved', 'occupied')";
        $stmt_check_plate = $conn->prepare($sql_check_plate);
        $stmt_check_plate->bind_param('s', $plate_number);
        $stmt_check_plate->execute();
        $result_check_plate = $stmt_check_plate->get_result();

        if ($result_check_plate->num_rows > 0) {
            echo "The plate number is already reserved or occupied.";
            exit;
        }

        // Check if the slot is already reserved
        $sql_check_slot = "SELECT id FROM reservations WHERE slot_number = ? AND status = 'reserved'";
        $stmt_check_slot = $conn->prepare($sql_check_slot);
        $stmt_check_slot->bind_param('i', $slot_number);
        $stmt_check_slot->execute();
        $result_check_slot = $stmt_check_slot->get_result();

        if ($result_check_slot->num_rows > 0) {
            echo "The slot is already reserved.";
            exit;
        }

        // Calculate reservation expiry time based on user role
        $reservation_time = date('Y-m-d H:i:s');
        if ($user_role == 'president' || $user_role == 'vice') {
            $expiry_time = '9999-12-31 23:59:59'; // No expiry for special roles
        } else {
            $expiry_time = date('Y-m-d H:i:s', strtotime($reservation_time . ' +5 minutes'));
        }

        // Insert new reservation
        $sql_insert_reservation = "INSERT INTO reservations (user_id, plate_number, vehicle_type, slot_number, slot_id, status, reservation_date, expiry_time) 
                                   VALUES (?, ?, ?, ?, ?, 'reserved', ?, ?)";
        $stmt_insert_reservation = $conn->prepare($sql_insert_reservation);
        $stmt_insert_reservation->bind_param('issiiss', $user_id, $plate_number, $vehicle_type, $slot_number, $slot_number, $reservation_time, $expiry_time);

        if ($stmt_insert_reservation->execute()) {
            // Update the parking slot status to reserved
            $sql_update_slot = "UPDATE parking_slots SET status = 'reserved' WHERE slot_id = ?";
            $stmt_update_slot = $conn->prepare($sql_update_slot);
            $stmt_update_slot->bind_param('i', $slot_number);

            if ($stmt_update_slot->execute()) {
                echo "Reservation successful. Slot #$slot_number is now reserved.";
            } else {
                echo "Error updating parking slot status: " . htmlspecialchars($stmt_update_slot->error);
            }

            $stmt_update_slot->close();
        } else {
            echo "Error inserting reservation: " . htmlspecialchars($stmt_insert_reservation->error);
        }

        $stmt_insert_reservation->close();
    } elseif (isset($_POST['check_expiry'])) {
        // Handle reservation expiry check
        $slot_number = $_POST['check_expiry'];

        $sql_check_expiry = "SELECT id, expiry_time, user_id FROM reservations WHERE slot_number = ? AND status = 'reserved'";
        $stmt_check_expiry = $conn->prepare($sql_check_expiry);
        $stmt_check_expiry->bind_param('i', $slot_number);
        $stmt_check_expiry->execute();
        $result_check_expiry = $stmt_check_expiry->get_result();

        if ($result_check_expiry->num_rows > 0) {
            $row = $result_check_expiry->fetch_assoc();
            $expiry_time = new DateTime($row['expiry_time']);
            $now = new DateTime();

            if ($expiry_time < $now && !in_array($user_role, ['president', 'vice'])) {
                // Expired, update reservation and free the slot
                $sql_update_expiry = "UPDATE reservations SET status = 'expired', expiry_time = '0000-00-00 00:00:00' WHERE slot_number = ? AND status = 'reserved'";
                $stmt_update_expiry = $conn->prepare($sql_update_expiry);
                $stmt_update_expiry->bind_param('i', $slot_number);

                if ($stmt_update_expiry->execute()) {
                    // Mark the slot as available
                    $sql_update_slot = "UPDATE parking_slots SET status = 'available' WHERE slot_id = ?";
                    $stmt_update_slot = $conn->prepare($sql_update_slot);
                    $stmt_update_slot->bind_param('i', $slot_number);
                    $stmt_update_slot->execute();

                    echo "Reservation expired and slot is now available.";
                } else {
                    echo "Error updating reservation expiry: " . htmlspecialchars($stmt_update_expiry->error);
                }

                $stmt_update_expiry->close();
                $stmt_update_slot->close();
            } else {
                echo "Reservation not yet expired.";
            }
        } else {
            echo "No reservation found for this slot.";
        }

        $stmt_check_expiry->close();
    } else {
        echo "Invalid request.";
    }

    $stmt_user->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
