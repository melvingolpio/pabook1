<?php
session_start();
require('../dbconn.php');
include('../vendor/phpqrcode/qrlib.php');
require '../vendor/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SESSION['type'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

function generateReceipt($userId, $plateNumber, $conn) {
    $uniqueToken = md5(uniqid($plateNumber, true));
    $expirationDate = date('Y-m-d H:i:s', strtotime('+1 year'));
    $createdAt = date('Y-m-d H:i:s');
    $receiptUrl = "http://localhost/pms-sample/user/transaction.php?token=$uniqueToken";

    $qr_content = "Receipt Token: $uniqueToken\nCreated At: $createdAt";
    $qr_dir = 'qrcodes2/';
    if (!file_exists($qr_dir)) {
        mkdir($qr_dir, 0755, true);
    }

    $qr_file = $qr_dir . $plateNumber . '_qrcode.png';
    QRcode::png($qr_content, $qr_file, QR_ECLEVEL_L, 10);

    if (!file_exists($qr_file)) {
        die("QR code generation failed.");
    }

    $stmt = $conn->prepare("INSERT INTO receipts (user_id, plate_number, receipt_token, expiration_date, qr_code, created_at) VALUES (?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE receipt_token = VALUES(receipt_token), expiration_date = VALUES(expiration_date), qr_code = VALUES(qr_code), created_at = VALUES(created_at)");
    $stmt->bind_param('isssss', $userId, $plateNumber, $uniqueToken, $expirationDate, $qr_file, $createdAt);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function sendReceiptEmail($userId, $plateNumber, $conn) {
    $stmt = $conn->prepare("SELECT qr_code FROM receipts WHERE user_id = ? AND plate_number = ?");
    $stmt->bind_param('is', $userId, $plateNumber);
    $stmt->execute();
    $stmt->bind_result($qr_code_data);
    $stmt->fetch();
    $stmt->close();

    if (empty($qr_code_data)) {
        return false;
    }

    $sql_user_info = "SELECT email, username FROM users WHERE id = ?";
    $stmt_user_info = $conn->prepare($sql_user_info);
    $stmt_user_info->bind_param('i', $userId);
    $stmt_user_info->execute();
    $result_user_info = $stmt_user_info->get_result();
    $user_info = $result_user_info->fetch_assoc();

    $email = $user_info['email'];
    $username = $user_info['username'];

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'pabookna@gmail.com';
        $mail->Password   = 'tmab nwhu qlmp rczz';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('pabookna@gmail.com', 'Pabook');
        $mail->addAddress($email);

        if (file_exists($qr_code_data)) {
            $mail->addAttachment($qr_code_data);
        }

        $mail->isHTML(true);
        $mail->Subject = 'Your PaBook Reservation';
        $mail->Body    = "Dear $username,<br><br>We successfully verified your payment. Below is the QR code.<br><br><img src='cid:qrCodeImage' alt='QR Code'><br><br>Drive safely,<br>PaBook Team";
        $mail->AltBody = "Dear $username,\n\nWe successfully verified your payment. Please find the QR code attached.\n\nDrive safely,\nPaBook Team";

        $mail->AddEmbeddedImage($qr_code_data, 'qrCodeImage');

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $plateNumber = $_POST['plate_number'];

    $stmt = $conn->prepare("SELECT expiration_date FROM receipts WHERE user_id = ? AND plate_number = ?");
    $stmt->bind_param('is', $userId, $plateNumber);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($expirationDate);
        $stmt->fetch();

        if (strtotime($expirationDate) > time()) {
            echo "<script>alert('Your QR code was already sent and the token has not expired.');</script>";
            echo "<script>window.location.href = 'payment_verification.php';</script>";
            exit();
        }
    }

    if (generateReceipt($userId, $plateNumber, $conn)) {
        if (sendReceiptEmail($userId, $plateNumber, $conn)) {
            echo "<script>alert('QR code generated and email sent successfully!');</script>";
        } else {
            echo "<script>alert('Failed to send email.');</script>";
        }
    } else {
        echo "<script>alert('Failed to generate receipt.');</script>";
    }

    echo "<script>window.location.href = 'payment_verification.php';</script>";
    exit();
}

$user_id = $_SESSION['id'];
$image_query = "SELECT image FROM users WHERE id = ?";
$stmt = $conn->prepare($image_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$image_result = $stmt->get_result();

$image = '';
if ($image_result && $row = $image_result->fetch_assoc()) {
    $image = $row['image'];
}
?>
