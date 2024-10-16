<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../dbconn.php');

// Include PHPMailer classes directly
require '../vendor/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data to prevent SQL injection
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $birth_date = htmlspecialchars($_POST['birthdate']);
    $gender = htmlspecialchars($_POST['gender']);
    $role = htmlspecialchars($_POST['role']);
    $contact_number = htmlspecialchars($_POST['contact_number']);
    $type = htmlspecialchars($_POST['type']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password for security
    $email = htmlspecialchars($_POST['email']);
    $license = htmlspecialchars($_POST['license']);

    $license_img = '';
    $image = null;

    // Handle profile image upload
    if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] == UPLOAD_ERR_OK) {
        $image = basename($_FILES['update_image']['name']);
        $uploadDir = '../img/';
        $uploadFile = $uploadDir . $image;

        if (!move_uploaded_file($_FILES['update_image']['tmp_name'], $uploadFile)) {
            echo "Error uploading profile image.";
            exit;
        }
    }

    // Handle license image upload
    if (isset($_FILES['license_img']) && $_FILES['license_img']['error'] == UPLOAD_ERR_OK) {
        $license_img = $upload_dir . basename($_FILES['license_img']['name']);
        if (!move_uploaded_file($_FILES['license_img']['tmp_name'], $license_img)) {
            echo "Error uploading license image.";
            exit;
        }
    }

    // Check if username already exists
    $query_check = "SELECT * FROM users WHERE username = ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        echo '<script>alert("Username \'' . $username . '\' is already taken. Please choose a different username."); window.location.href = "signup.php";</script>';
    } else {
        // Insert user data into the database
        $query_insert = "INSERT INTO users (first_name, last_name, birth_date, gender, role, contact_number, type, username, password, email, image, license, license_img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($query_insert);
        $stmt_insert->bind_param("sssssssssssss", $first_name, $last_name, $birth_date, $gender, $role, $contact_number, $type, $username, $hashed_password, $email, $image, $license, $license_img);

        if ($stmt_insert->execute()) {
            // Send email
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                      // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                  // Enable SMTP authentication
                $mail->Username   = 'your_email@gmail.com';                // SMTP username
                $mail->Password   = 'your_app_password';                   // SMTP password (use App Password for Gmail)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        // Enable TLS encryption
                $mail->Port       = 587;                                   // TCP port to connect to

                // Recipients
                $mail->setFrom('your_email@gmail.com', 'PaBook');
                $mail->addAddress($email);                                 // Add recipient email

                // Email content
                $mail->isHTML(true);                                       // Set email format to HTML
                $mail->Subject = 'Your PaBook Account';
                $mail->Body    = "Dear $first_name $last_name,<br><br>Your account has been created successfully.<br><br>Username: $username<br>Password: $password<br><br>Drive safely,<br>PaBook Team";

                $mail->send();
                echo '<script>alert("Signup successful! Username and password have been sent to your email. Redirecting..."); window.location.href = "user_account.php";</script>';
            } catch (Exception $e) {
                // Log the error if the email could not be sent
                error_log("Mailer Error: " . $mail->ErrorInfo);
                echo "Message could not be sent. Please try again later.";
            }
        } else {
            // Log the error if database insertion fails
            error_log("Database error: " . $stmt_insert->error);
            echo "There was an error signing you up. Please try again later.";
        }

        $stmt_insert->close();
    }

    $stmt_check->close();
    $conn->close();
}
?>
