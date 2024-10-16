<?php 
session_start();
require('../dbconn.php');
require '../vendor/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SESSION['type'] != 'Admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receipt_id = $_POST['receipt_id'];

    $query = "UPDATE receipts SET expiration_date = DATE_ADD(expiration_date, INTERVAL 1 YEAR) WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $receipt_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Receipt expiration date updated successfully!';

        $user_query = "SELECT u.email, u.username FROM users u JOIN receipts r ON u.id = r.user_id WHERE r.id = ?";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param('i', $receipt_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();

        if ($user_row = $user_result->fetch_assoc()) {
            $email = $user_row['email'];
            $username = $user_row['username'];

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'pabookna@gmail.com';
                $mail->Password   = 'tmab nwhu qlmp rczz';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('pabookna@gmail.com', 'PaBook');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Your PaBook Account';
                $mail->Body    = "Dear $username,<br><br>Your renewal has been successful.<br><br>Drive safely,<br>PaBook Team";

                $mail->send();
                echo '<script>alert("Renewal successful! Verification has been sent to your email. Redirecting..."); window.location.href = "user_account.php";</script>';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $_SESSION['error_message'] = 'Failed to fetch user details!';
        }
    } else {
        $_SESSION['error_message'] = 'Failed to update receipt expiration date!';
    }

    header('Location: renew_verification.php');
    exit();
} else {
    header('Location: renew_verification.php');
    exit();
}
?>