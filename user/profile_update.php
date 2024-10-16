<?php
session_start();
require('../dbconn.php');

if ($_SESSION['type'] == 'User') {
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

    if (isset($_POST['submit_profile'])) {
        // Handle Profile Update
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $contact_number = $_POST['contact_number'];
        $email = $_POST['email'];
        $provided_password = $_POST['password'];
    
        // Check if the provided password is correct
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
    
            // Update the profile without changing the password
            $query = "UPDATE users SET first_name = ?, last_name = ?, age = ?, gender = ?, contact_number = ?, email = ?, image = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssissssi", $first_name, $last_name, $age, $gender, $contact_number, $email, $image_path, $user_id);
    
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
            echo "<script type='text/javascript'>alert('Incorrect password. Profile update failed.')</script>";
        }
    }
    
    if (isset($_POST['submit_password'])) {
        // Handle Password Change
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
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Update</title>
    <link rel="stylesheet" href="assets/css/ppupdate.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<style>
::-webkit-scrollbar-thumb {
    background-image: 
        linear-gradient(to bottom, rgb(0, 0, 85), rgb(0, 0, 50));
}
::-webkit-scrollbar {
    width: 5px;
}
::-webkit-scrollbar-track {
    background-color: #9e9e9eb2;
}
.modal {
display: none;
position: fixed;
z-index: 1000;
left: 0;
top: 0;
width: 100%;
height: 100%;
overflow: auto;
background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
background-color: #ffffff;
margin: 10% auto;
padding: 30px;
border: 1px solid #ddd;
width: 90%;
max-width: 600px;
border-radius: 12px;
box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
position: relative;
}

.close-button {
color: #888;
position: absolute;
top: 10px;
right: 10px;
font-size: 24px;
font-weight: bold;
cursor: pointer;
}

.close-button:hover,
.close-button:focus {
color: #333;
text-decoration: none;
}

.modal-content h2 {
margin-top: 0;
font-size: 1.5rem;
color: #333;
border-bottom: 1px solid #eee;
padding-bottom: 10px;
}

.modal-content p {
font-size: 1rem;
color: #666;
margin: 10px 0;
display: flex;
justify-content: space-between;
}

.modal-content p strong {
flex-basis: 30%;
text-align: left;
}

.modal-content p span {
flex-basis: 70%;
text-align: right;
}


</style>
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


        <div class="card-bdy">
            <h5>Chane password</h5>
        </div>
        <div id="reservationModal" class="modal" style="display: hidden;">
            <div class="modal-content">
                
                <form class="password-change" action="profile_update.php" method="POST">
                    <div class="prof-foot">
                        <div class="profile-details">
                            <div class="profile-body">
                                <label class="oldPassword" for="oldPassword"><b>Old Password:</b></label>
                                <div class="controls">
                                    <input type="password" id="oldPassword" name="oldPassword" required class="span8">
                                </div>
                                <label class="newPassword" for="newPassword"><b>New Password:</b></label>
                                <div class="controls">
                                    <input type="password" id="newPassword" name="newPassword" required class="span8">
                                    
                                </div>
                                <label class="confirmPassword" for="confirmPassword"><b>Confirm Password:</b></label>
                                <div class="controls">
                                    <input type="password" id="confirmPassword" name="confirmPassword" required class="span8">
                                    <i class='bx bx-show toggle-password' onclick="togglePassword('confirmPassword', this)"></i>
                                </div>
                            </div>
                            <div class="submit-btn">
                                <input type="submit" name="submit_password" value="Change Password" class="btn-update">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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

        function togglePassword(fieldId, icon) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = icon;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bx-show');
                toggleIcon.classList.add('bx-hide');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bx-hide');
                toggleIcon.classList.add('bx-show');
            }

        }

    </script>
<script src="assets/script/popReserved.js"></script>
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
