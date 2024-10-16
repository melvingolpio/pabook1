<?php
require('dbconn.php');

// Include PHPMailer classes directly
require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $role = $_POST['role'];
    $contact_number = $_POST['contact_number'];
    $type = $_POST['type'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Store the plain password to send via email
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $license = $_POST['license']
    
    $image = null;
    if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] == UPLOAD_ERR_OK) {
        $image = $_FILES['update_image']['name'];
        $uploadDir = '../img/';
        $uploadFile = $uploadDir . basename($image);

        if (!move_uploaded_file($_FILES['update_image']['tmp_name'], $uploadFile)) {
            echo "Error uploading file.";
            exit;
        }
    }
    
    $query_check = "SELECT * FROM users WHERE username = ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        echo '<script>alert("Username \'' . $username . '\' is already taken. Please choose a different username."); window.location.href = "signup.php";</script>';
    } else {
        $query_insert = "INSERT INTO users (first_name, last_name, birth_date, gender, role, contact_number, type, username, password, email, image, license) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($query_insert);
        $stmt_insert->bind_param("ssssssssssss", $first_name, $last_name, $birth_date, $gender, $role, $contact_number, $type, $username, $hashed_password, $email, $image, $license);

        if ($stmt_insert->execute()) {
            // Send email
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'jaspherflores2001@gmail.com';               // SMTP username
                $mail->Password   = 'akhvusecpmralmlv';                  // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
                $mail->Port       = 587;                                    // TCP port to connect to

                //Recipients
                $mail->setFrom('jaspherflores.o01@gmail.com', 'PaBook');
                $mail->addAddress($email);                                  // Add a recipient

                // Content
                $mail->isHTML(true);                                        // Set email format to HTML
                $mail->Subject = 'Your PaBook Account';
                $mail->Body    = "Dear $first_name $last_name,<br><br>Your account has been created successfully.<br><br>Username: $username<br>Password: $password<br><br>Drive safely,<br>PaBook Team";
                
                $mail->send();
                echo '<script>alert("Signup successful! Username and password have been sent to your email. Redirecting..."); window.location.href = "user_account.php";</script>';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error: " . $stmt_insert->error;
        }

        $stmt_insert->close();
    }

    $stmt_check->close();
    $conn->close();
}
?>
