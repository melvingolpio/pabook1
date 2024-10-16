<?php 
session_start();
require('../dbconn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['slot_number'])) {

    if (!isset($_SESSION['username'])) {
        echo "User not logged in.";
        exit;
    }

    $slot_number = $_POST['slot_number'];
    $plate_number = $_POST['plate_number'];
    $username_user = $_SESSION['username'];

    // Prepare and execute the query to get user details
    $sql_user = "SELECT id, role FROM users WHERE username = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param('s', $username_user);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $row_user = $result_user->fetch_assoc();
        $user_id = $row_user['id'];
        $user_role = $row_user['role'];

        // Prepare and execute the query to get vehicle type
        $sql_vehicle_type = "SELECT vehicle_type FROM vehicle WHERE plate_number = ?";
        $stmt_vehicle_type = $conn->prepare($sql_vehicle_type);
        $stmt_vehicle_type->bind_param('s', $plate_number);
        $stmt_vehicle_type->execute();
        $result_vehicle_type = $stmt_vehicle_type->get_result();
        $row_vehicle_type = $result_vehicle_type->fetch_assoc();
        
        if (!$row_vehicle_type) {
            echo "Vehicle type not found.";
            exit;
        }

        $vehicle_type = $row_vehicle_type['vehicle_type'];

        // Check if user already has a reservation for the vehicle type
        $sql_check_vehicle_type = "SELECT id FROM reservations WHERE user_id = ? AND vehicle_type = ? AND status IN ('reserved', 'occupied')";
        $stmt_check_vehicle_type = $conn->prepare($sql_check_vehicle_type);
        $stmt_check_vehicle_type->bind_param('is', $user_id, $vehicle_type);
        $stmt_check_vehicle_type->execute();
        $result_check_vehicle_type = $stmt_check_vehicle_type->get_result();

        if ($result_check_vehicle_type->num_rows > 0) {
            echo "You already have a reservation for a vehicle of this type.";
        } else {

            // Check if the plate number is already reserved
            $sql_check_plate = "SELECT r.id, a.id
                                FROM reservations r
                                JOIN activities a ON r.plate_number = a.plate_number
                                WHERE r.plate_number = ? AND (r.status = 'reserved' OR a.status = 'occupied')";
            $stmt_check_plate = $conn->prepare($sql_check_plate);
            $stmt_check_plate->bind_param('s', $plate_number);
            $stmt_check_plate->execute();
            $result_check_plate = $stmt_check_plate->get_result();

            if ($result_check_plate->num_rows > 0) {
                echo "The plate number is already reserved.";
            } else {

                // Check if the slot is already reserved
                $sql_check_slot = "SELECT id, expiry_time, slot_id FROM reservations WHERE slot_number = ? AND status = 'reserved'";
                $stmt_check_slot = $conn->prepare($sql_check_slot);
                $stmt_check_slot->bind_param('i', $slot_number);
                $stmt_check_slot->execute();
                $result_check_slot = $stmt_check_slot->get_result();

                if ($result_check_slot->num_rows > 0) {
                    $row_slot = $result_check_slot->fetch_assoc();
                    $slot_id = $row_slot['slot_id'] ?? $slot_number;

                    // Update the reservation status to occupied
                    $sql_update = "UPDATE reservations SET status = 'occupied', slot_number = NULL, slot_id = ? WHERE slot_number = ? AND status = 'reserved'";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param('ii', $slot_id, $slot_number);

                    if ($stmt_update->execute()) {
                        // Update the parking_slots table status to 'occupied'
                        $sql_update_slot = "UPDATE parking_slots SET status = 'occupied' WHERE slot_id = ?";
                        $stmt_update_slot = $conn->prepare($sql_update_slot);
                        $stmt_update_slot->bind_param('i', $slot_id);

                        if ($stmt_update_slot->execute()) {
                            echo "Slot #$slot_number status updated to occupied and slot number cleared.";
                        } else {
                            echo "Error updating parking slot status: " . htmlspecialchars($stmt_update_slot->error);
                        }

                        $stmt_update_slot->close();
                    } else {
                        echo "Error: " . htmlspecialchars($stmt_update->error);
                    }

                    $stmt_update->close();

                } else {
                    $reservation_time = date('Y-m-d H:i:s');
                    if ($user_role == 'president' || $user_role == 'vice') {
                        $expiry_time = '9999-12-31 23:59:59'; 
                    } else {
                        $expiry_time = date('Y-m-d H:i:s', strtotime($reservation_time . ' +5 minutes'));
                    }

                    // Insert a new reservation
                    $sql_insert = "INSERT INTO reservations (user_id, plate_number, vehicle_type, slot_number, slot_id, status, reservation_date, expiry_time) 
                                   VALUES (?, ?, ?, ?, ?, 'reserved', ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param('issiiss', $user_id, $plate_number, $vehicle_type, $slot_number, $slot_number, $reservation_time, $expiry_time);

                    if ($stmt_insert->execute()) {
                        // Update the parking_slots table status to 'reserved'
                        $sql_update_slot = "UPDATE parking_slots SET status = 'reserved' WHERE slot_id = ?";
                        $stmt_update_slot = $conn->prepare($sql_update_slot);
                        $stmt_update_slot->bind_param('i', $slot_number);

                        if ($stmt_update_slot->execute()) {
                            echo "Reservation made successfully. Slot #$slot_number is now reserved.";
                        } else {
                            echo "Error updating parking slot status to reserved: " . htmlspecialchars($stmt_update_slot->error);
                        }

                        $stmt_update_slot->close();
                        exit();
                    } else {
                        header("Location: reserve_slot.php?error=" . urldecode("Error inserting reservation: " . htmlspecialchars($stmt_insert->error)));
                        exit();
                    }

                    $stmt_insert->close();
                }
            }

            $stmt_check_plate->close();
        }

        $stmt_check_vehicle_type->close();
    } else {
        echo "User not found.";
    }

    $stmt_user->close();
    $conn->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check_expiry'])) {

    $slot_number = $_POST['check_expiry'];

    $sql_check_expiry = "SELECT id, expiry_time, user_id, slot_id FROM reservations WHERE slot_number = ? AND status = 'reserved'";
    $stmt_check_expiry = $conn->prepare($sql_check_expiry);
    $stmt_check_expiry->bind_param('i', $slot_number);
    $stmt_check_expiry->execute();
    $result_check_expiry = $stmt_check_expiry->get_result();

    if ($result_check_expiry->num_rows > 0) {
        $row = $result_check_expiry->fetch_assoc();
        $expiry_time = new DateTime($row['expiry_time']);
        $now = new DateTime();

        if ($expiry_time < $now && !in_array($user_role, ['president', 'vice'])) {
            // Update status to expired and clear the slot
            $sql_update = "UPDATE reservations SET status = 'expired', expiry_time = '0000-00-00 00:00:00' WHERE slot_number = ? AND status = 'reserved'";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param('i', $slot_number);

            if ($stmt_update->execute()) {
                // Update the parking_slots table to available
                $sql_remove_slot = "UPDATE parking_slots SET status = 'available' WHERE slot_id = ?";
                $stmt_remove_slot = $conn->prepare($sql_remove_slot);
                $stmt_remove_slot->bind_param('i', $slot_number);
                $stmt_remove_slot->execute();

                echo "Reservation status updated to expired and slot number cleared.";
            } else {
                echo "Error: " . htmlspecialchars($stmt_update->error);
            }

            $stmt_update->close();
            $stmt_remove_slot->close();
        } else {
            echo "Reservation not yet expired.";
        }
    } else {
        echo "No reservation found.";
    }

    $stmt_check_expiry->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
