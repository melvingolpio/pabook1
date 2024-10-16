<?php
session_start();
require('../dbconn.php');

if (!isset($_SESSION['username']) || $_SESSION['type'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['type'] == 'Admin') {
    $user_id = $_SESSION['id'];
    $query = "SELECT id, first_name, last_name, username, birth_date, gender, contact_number, type, email, penalty, image, password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $id = $user['id'];
        $first_name = $user['first_name'];
        $last_name = $user['last_name'];
        $fullname = $user['first_name'] . ' ' . $user['last_name'];
        $username = $user['username'];
        $birth_date = $user['birth_date'];
        $gender = $user['gender'];
        $contact_number = $user['contact_number'];
        $type = $user['type'];
        $email = $user['email'];
        $penalty = $user['penalty'];
        $image = $user['image'];
        $current_password_hash = $user['password'];
    }

    $vehicle_sql = "SELECT plate_number, vehicle_brand, vehicle_type, color, amount FROM vehicle WHERE user_id = ?";
    $stmt = $conn->prepare($vehicle_sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }

    if (isset($_POST['submit_profile'])) {

        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $birth_date = $_POST['birth_date'];
        $gender = $_POST['gender'];
        $contact_number = $_POST['contact_number'];
        $email = $_POST['email'];
        $provided_password = $_POST['password'];

        if (password_verify($provided_password, $current_password_hash)) {
            if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../img/';
                $uploaded_file = $upload_dir . basename($_FILES['update_image']['name']);
                if (move_uploaded_file($_FILES['update_image']['tmp_name'], $uploaded_file)) {
                    $image_path = basename($_FILES['update_image']['name']);
                } else {
                    echo "<script type='text/javascript'>alert('Image upload failed.')</script>";
                    $image_path = $image;
                }
            } else {
                $image_path = $image;
            }

            $query = "UPDATE users SET first_name = ?, last_name = ?, birth_date = ?, gender = ?, contact_number = ?, email = ?, image = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssssi", $first_name, $last_name, $birth_date, $gender, $contact_number, $email, $image_path, $user_id);

            if ($stmt->execute()) {
                echo "<script type='text/javascript'>
                        alert('Profile update successful!');
                        window.location.href = 'profile_details.php';
                    </script>";
            } else {
                echo $stmt->error;
                echo "<script type='text/javascript'>alert('Error')</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Incorrect password. Profile update Failed.'); window.location.href = 'profile_details.php'</script>";
        }
    }

    if (isset($_POST['submit_password'])) {

        $old_password = $_POST['oldPassword'];
        $new_password = $_POST['newPassword'];
        $confirm_password = $_POST['confirmPassword'];

        if (!empty($old_password) && !empty($new_password) && !empty($confirm_password)) {
            if ($new_password === $confirm_password) {
                if (password_verify($old_password, $current_password_hash)) {
                    $password_pattern = '/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/';
                    if (preg_match($password_pattern, $new_password)) {
                        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $query = "UPDATE users SET password = ? WHERE id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("si", $hashed_new_password, $user_id);

                        if ($stmt->execute()) {
                            echo "<script type='text/javascript'>
                                alert('Password changed successfully!');
                                window.location.href = 'profile_details.php';
                            </script>";
                        } else {
                            echo $stmt->error;
                            echo "<script type='text/javascript'>alert('Error')</script>";
                        }
                        $stmt->close();
                    } else {
                        echo "<script type='text/javascript'>alert('New password must be at least 8 characters long, include an uppercase letter and a number.')</script>";
                    }
                } else {
                    echo "<script type='text/javascript'>alert('Old password is incorrect')</script>";
                }
            } else {
                echo "<script type='text/javascript'>alert('New passwords do not match')</script>";
            }
        } else {
            echo "<script type='text/javascript'>alert('All password fields are required.')</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Details</title>
    <link rel="stylesheet" href="assets/css/profilestyles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @media (max-width: 786px){
            body {
                overflow-y: scroll;
            }
            .profile-head {
                font-size: 11px;
            }
            .card-container {
                position: top;
            }
        }
        .prof-pic {
        border: 2px solid #1560bd;
       }
       .logo {
    font-variant: small-caps;
    font-weight: bold;
    color: white;
    text-shadow: 2px 4px 6px rgb(0, 0, 0);
}
.menuicn {
   opacity: 0;
}
@media (max-width: 786px){
            .prof-pic {
                width: 50px;
                height: 50px;
            }
            .profile-details {
                font-size: 12px;
            }
            .card-profile {
                margin-bottom: -200px;
            }
            .profile-head {
                width: 60%;
            }
            .profile-vehicles {
                width: 40%;
            }
            .card-profile {
                gap:7vh;
            }

        }
        @media (max-width: 850px) {
            .menuicn {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="picture">
            <div class="dp">
                <img src="../img/<?php echo htmlspecialchars($image); ?>" class="dpicn" alt="Picture">
            </div>
            <div class="name">
                <p><?php echo $_SESSION['username']; ?>!</p>
            </div>
        </div>

        <div class="logosec">
            <div class="logo">Pabook</div>
            <i class="fas fa-bars icn menuicn" id="menuicn" alt="menu-icon"></i>
        </div>
    </header>

    <div class="main-container">
        <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt nav-img" alt="dashboard"></i>
                        <a href="index.php" class="nav-link"><h3>Dashboard</h3></a>
                    </div>
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'user_account.php') ? 'active' : ''; ?>">
                        <i class="fas fa-user nav-img" alt="account's"></i>
                        <a href="user_account.php" class="nav-link"><h3>Account's</h3></a>
                    </div>
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'report.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-chart-line"></i>
                        <a href="report.php" class="nav-link"><h3>Report</h3></a>
                    </div>
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'transaction.php') ? 'active' : ''; ?>">
                        <i class="fas fa-money-check-alt nav-img" alt="transaction"></i>
                        <a href="transaction.php" class="nav-link"><h3>Transaction</h3></a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-title">
                    <h2>My Profile</h2>
                </div>
                <button class="edit-btn" onclick="toggleEdit()">Edit Profile</button>
            </div>

            <div class="profile-details">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="birth_date">Birth Date:</label>
                        <input type="date" name="birth_date" value="<?php echo htmlspecialchars($birth_date); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select name="gender" required>
                            <option value="Male" <?php echo ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Contact Number:</label>
                        <input type="text" name="contact_number" value="<?php echo htmlspecialchars($contact_number); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="update_image">Profile Picture:</label>
                        <input type="file" name="update_image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="password">Current Password:</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit_profile">Update Profile</button>
                    </div>
                </form>
            </div>

            <div class="profile-vehicles">
                <h3>Vehicles</h3>
                <?php foreach ($vehicles as $vehicle): ?>
                    <p><?php echo htmlspecialchars($vehicle['plate_number']); ?> - <?php echo htmlspecialchars($vehicle['vehicle_brand']); ?> (<?php echo htmlspecialchars($vehicle['vehicle_type']); ?>)</p>
                <?php endforeach; ?>
            </div>

            <div class="change-password">
                <h3>Change Password</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="oldPassword">Old Password:</label>
                        <input type="password" name="oldPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password:</label>
                        <input type="password" name="newPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password:</label>
                        <input type="password" name="confirmPassword" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit_password">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
