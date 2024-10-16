<?php 
session_start();
require('../dbconn.php');

if ($_SESSION['type'] != 'User') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scannedData = trim($_POST['scanned_data']); 

    list($tokenLine, $createdAtLine) = explode("\n", $scannedData);
    $token = trim(str_replace('Receipt Token: ', '', $tokenLine));
    $createdAt = trim(str_replace('Created At: ', '', $createdAtLine));

    $stmt = $conn->prepare("SELECT plate_number, expiration_date FROM receipts WHERE receipt_token = ? AND created_at = ?");
    $stmt->bind_param('ss', $token, $createdAt);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($plateNumber, $expirationDate);
        $stmt->fetch();

        if (strtotime($expirationDate) < time()) {
            echo "Your QR code has expired. Please scan a valid QR code.";
            echo "<a href='qr-scanner.php'>Return to Scanner</a>";
            exit();
        }

        $stmt = $conn->prepare("SELECT user_id FROM vehicle WHERE plate_number = ?");
        $stmt->bind_param('s', $plateNumber);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId);
            $stmt->fetch();
        } else {
            echo "User not found.";
            echo "<a href='qr-scanner.php'>Return to Scanner</a>";
            exit();
        }

        $stmt = $conn->prepare("SELECT slot_number FROM reservations WHERE plate_number = ? AND (status = 'reserved' OR status = 'occupied')");
        $stmt->bind_param('s', $plateNumber);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($slotNumber);
            $stmt->fetch();

            $date = date('Y-m-d');
            $timeIn = date('H:i:s');
            $timeOut = null;

            $slotId = $slotNumber;

            $stmt = $conn->prepare("SELECT time_out FROM activities WHERE plate_number = ? AND date = ? AND slot_number = ? AND user_id = ?");
            $stmt->bind_param('sssi', $plateNumber, $date, $slotNumber, $userId);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($existingTimeOut);
                $stmt->fetch();

                if ($existingTimeOut === null) {
                    $timeOut = $timeIn; 
                    $status = 'out';

                    $stmt = $conn->prepare("UPDATE activities SET time_out = ?, status = ?, slot_number = NULL WHERE plate_number = ? AND date = ? AND slot_number = ? AND user_id = ?");
                    $stmt->bind_param('sssssi', $timeOut, $status, $plateNumber, $date, $slotNumber, $userId);
                    if ($stmt->execute()) {
                        echo "Scan ";
                    } else {
                        echo "Error updating activity: " . $stmt->error;
                    }

                    $stmt = $conn->prepare("UPDATE reservations SET status = NULL, slot_number = NULL WHERE plate_number = ? AND slot_number = ?");
                    $stmt->bind_param('ss', $plateNumber, $slotNumber);
                    if ($stmt->execute()) {
                        echo "successful.";
                    } else {
                        echo "Error updating reservation: " . $stmt->error;
                    }
                } else {
                    echo "Activity already completed.";
                }
            } else {
                $stmt = $conn->prepare("INSERT INTO activities (plate_number, date, time_in, time_out, status, slot_number, slot_id, user_id) VALUES (?, ?, ?, ?, 'occupied', ?, ?, ?)");
                $stmt->bind_param('ssssiii', $plateNumber, $date, $timeIn, $timeOut, $slotNumber, $slotId, $userId);
                if ($stmt->execute()) {
                    echo "Scan ";
                } else {
                    echo "Error recording activity: " . $stmt->error;
                }

                $stmt = $conn->prepare("UPDATE reservations SET status = 'occupied', expiry_time = 0 WHERE plate_number = ? AND slot_number = ?");
                $stmt->bind_param('ss', $plateNumber, $slotNumber);
                if ($stmt->execute()) {
                    echo "successful.";
                } else {
                    echo "Error updating reservation: " . $stmt->error;
                }
            }
        } else {
            echo "No reservation found.";
        }
    } else {
        echo "Invalid QR code.";
    }

    exit();
}
?>
