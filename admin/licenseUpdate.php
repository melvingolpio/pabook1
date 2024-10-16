<?php
session_start();
require('../dbconn.php');

if (isset($_POST['admin'])) {
    echo "Invalid User login";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $user_id = $_POST['user_id'];
    $license = $_POST['license'];
    $birth_date = $_POST['birth_date'];
    $confirmPassword = $_POST['confirmPassword'];

    $get_pass = "SELECT password FROM users WHERE type = 'Admin' LIMIT 1";
    $stmt = $conn->prepare($get_pass);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($confirmPassword, $hashed_password)) {
        echo "<script>alert('Access denied'); window.location.href='logout.php';</script>";
        exit();
    }

    $update_sql = "UPDATE users SET ";
    $update_fields = [];
    $params = [];
    $types = "";

    if (!empty($license)) {
        $update_fields[] = "license = ?";
        $params[] = $license;
        $types .= 's';
    }


    $update_fields[] = "birth_date = ?";
    $params[] = $birth_date;
    $types .= 's';

    if (isset($_FILES['license_img']) && $_FILES['license_img']['error'] == 0) {
        $image_name = $_FILES['license_img']['name'];
        $image_tmp = $_FILES['license_img']['tmp_name'];
        $image_folder = "../img/licenseNum/";

        move_uploaded_file($image_tmp, $image_folder . $image_name);

        $update_fields[] = "license_img = ?";
        $params[] = $image_name;
        $types .= 's';
    }

    if (isset($_FILES['lto_registration']) && $_FILES['lto_registration']['error'] == 0) {
        $image_name = $_FILES['lto_registration']['name'];
        $image_tmp = $_FILES['lto_registration']['tmp_name'];
        $image_folder = "../img/LTO_img/";

        move_uploaded_file($image_tmp, $image_folder . $image_name);

        $update_fields[] = "lto_registration = ?";
        $params[] = $image_name;
        $types .= 's';
    }

    $update_sql .= implode(", ", $update_fields) . " WHERE id = ?";
    $params[] = $user_id; 
    $types .= 'i';

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully.'); window.location.href='user_account.php';</script>";
    } else {
        echo "Error updating profile: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
