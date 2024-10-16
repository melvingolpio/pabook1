<?php
session_start();
require('../dbconn.php');

if ($_SESSION['type'] == 'Admin') {
    $user_id = $_SESSION['id'];
    $query = "SELECT id, first_name, last_name, username, age, gender, contact_number, type, email, password, image FROM users WHERE id = ?";
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
        $age = $user['age'];
        $gender = $user['gender'];
        $contact_number = $user['contact_number'];
        $type = $user['type'];
        $email = $user['email'];
        $current_password_hash = $user['password'];
        $image = $user['image'];
    }

    if (isset($_POST['submit'])) {
        $user_id = $_POST['user_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $contact_number = $_POST['contact_number'];
        $email = $_POST['email'];
        $old_password = $_POST['oldPassword'];
        $new_password = $_POST['newPassword'];
        $confirm_password = $_POST['confirmPassword'];

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

        if (!empty($old_password) && !empty($new_password) && !empty($confirm_password)) {
            if ($new_password === $confirm_password) {
                if (password_verify($old_password, $current_password_hash)) {
                    $password_pattern = '/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/';
                    if (preg_match($password_pattern, $new_password)) {
                        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $query = "UPDATE users SET first_name = ?, last_name = ?, age = ?, gender = ?, contact_number = ?, email = ?, password = ?, image = ? WHERE id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("ssisssssi", $first_name, $last_name, $age, $gender, $contact_number, $email, $hashed_new_password, $image_path, $user_id);

                        if ($stmt->execute()) {
                            echo "<script type='text/javascript'>
                                alert('Update successful!');
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
            $query = "UPDATE users SET first_name = ?, last_name = ?, age = ?, gender = ?, contact_number = ?, email = ?, image = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssissssi", $first_name, $last_name, $age, $gender, $contact_number, $email, $image_path, $user_id);

            if ($stmt->execute()) {
                echo "<script type='text/javascript'>
                        alert('Update successful!');
                        window.location.href = 'profile_details.php';
                    </script>";  
            } else {
                echo $stmt->error;
                echo "<script type='text/javascript'>alert('Error')</script>";
            }
            $stmt->close();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Update</title>
    <link rel="stylesheet" href="assets/css/pUpdatess.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <header>
        <div class="picture">
            <div class="dp">
                <img src="../img/<?php echo htmlspecialchars($image); ?>" class="dpicn" alt="Profile Picture">
            </div>
            <div class="name">
                <p><?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            </div>
        </div>
        <div class="logosec">
            <div class="logo">PaBook</div>
        </div>
    </header>

    <div class="prof-container">
        <div class="arrow">
            <a href="profile_details.php" class="nav-btn">
                <i class="fas fa-arrow-left nav-img"></i>
            </a>
        </div>
        <form class="profile" action="profile_update.php" method="POST" enctype="multipart/form-data">
            <div class="prof-head">
                <h2>Profile Information</h2>
                <img src="../img/<?php echo htmlspecialchars($image); ?>" class="prof-pic1">
                <br><br>
                <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="file-input-label">
            </div>
            <div class="prof-foot">
                <div class="profile-details">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                    <div class="profile-body">
                        <label class="first" for="first_name"><b>First Name:</b></label>
                        <div class="controls">
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" class="span8">
                        </div>
                        <label class="last" for="last_name"><b>Last Name:</b></label>
                        <div class="controls">
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" class="span8">
                        </div>
                        <label class="age" for="age"><b>Age:</b></label>
                        <div class="controls">
                            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($age); ?>" class="span8">
                        </div>
                        <div class="controls">
                            <label class="gender" for="gender"><b>Gender:</b></label>
                            <select id="gender" name="gender">     
                                <option value="<?php echo htmlspecialchars($gender); ?>"></option>                          
                                <option value="male" <?php echo ($gender == 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo ($gender == 'female') ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>

                        <label class="contact_number" for="contact_number"><b>Contact Number:</b></label>
                        <div class="controls">
                            <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($contact_number); ?>" class="span8">
                        </div>

                        <label class="email" for="email"><b>Email:</b></label>
                        <div class="controls">
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="span8">
                        </div>

                        <label class="oldPassword" for="oldPassword"><b>Old Password:</b></label>
                        <div class="controls">
                            <input type="password" id="oldPassword" name="oldPassword" class="span8">
                        </div>

                        <label class="newPassword" for="newPassword"><b>New Password:</b></label>
                        <div class="controls">
                            <input type="password" id="newPassword" name="newPassword" class="span8">
                        </div>

                        <label class="confirmPassword" for="confirmPassword"><b>Confirm Password:</b></label>
                        <div class="controls">
                            <input type="password" id="confirmPassword" name="confirmPassword" class="span8">
                        </div>
                    </div>
                    <div class="submit-btn">
                        <input type="submit" name="submit" value="Update Profile" class="btn-update">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function validatePasswords() {
            const oldPassword = document.getElementById('oldPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const passwordError = document.getElementById('passwordError');
    
            if (oldPassword === '' || newPassword === '' || confirmPassword === '') {
                passwordError.textContent = 'All password fields are required.';
                return false;
            }
    
            if (newPassword !== confirmPassword) {
                passwordError.textContent = 'New passwords do not match.';
                return false;
            }
    
            const passwordPattern = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;
            if (!passwordPattern.test(newPassword)) {
                passwordError.textContent = 'New password must be at least 8 characters long, include an uppercase letter and a number.';
                return false;
            }
    
            return true;
        }

    //togglepass
        function togglePassword() {
            var passwordInput = document.getElementById('   Password');
            var toggleIcon = document.querySelector('.toggle-password');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bx-show');
                toggleIcon.classList.add('bx-hide');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bx-hide');
                toggleIcon.classList.remove('bx-show');
                toggleIcon.classList.add('bx-show');
            }
        }
    </script>

<script src="assets/script/image.js"></script>
</body>
</html>

<?php
} else {
    header("Location: ../login.php"); 
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
    exit();
}
?>
