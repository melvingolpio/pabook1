<?php
session_start();
require('../dbconn.php'); 

if ($_SESSION['type'] != 'Admin') {
    header("Location: login.php"); 
    exit();
} 

$user_id = $_SESSION['id'];

$query = "SELECT id, image FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $image = $row['image'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/smenue.css">
    <link rel="stylesheet" href="assets/css/resp-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
.dp {
    height: 40px;
    width: 40px;
    background-color: #626262;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 2px solid white;
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

              <img src="../img/<?php echo htmlspecialchars($image);?>" class="dpicn" alt="Picture">
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
                    
                    <!--<div class="nav-option ?php echo (basename($_SERVER['PHP_SELF']) == 'qr-scanner.php') ? 'active' : ''; ?>">
                        <i class="fas fa-camera-retro    nav-img" alt="scanner"></i>
                        <a href="qr-scanner.php" class="nav-link"><h3>Scanner</h3></a>
                    </div>-->
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'report.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-chart-line"></i>
                        <a href="report.php" class="nav-link"><h3>Report</h3></a>
                    </div>
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'transaction.php') ? 'active' : ''; ?>">
                        <i class="fas fa-money-check-alt nav-img" alt="institution"></i>
                        <a href="transaction.php" class="nav-link"><h3>Transaction</h3></a>
                    </div>
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
                        <i class="fas fa-user-shield nav-img" alt="profile"></i>
                        <a href="profile.php" class="nav-link"><h3>Profile</h3></a>
                    </div>
                    <div class="nav-option">
                        <i class="fas fa-sign-out-alt nav-img" alt="logout"></i>
                        <a href="logout.php"><h3>Logout</h3></a>
                    </div>

                </div>
            </nav>
        </div>
        <div class="main">

            <h1>Create Account for User's</h1>
            <br>

            <div class="signup-container">
                

            <form method="POST" action="process_signup.php" enctype="multipart/form-data">

            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="birthdate"><b>Birth-date:</b></label>
            <input type="date" id="birthdate" name="birthdate"min="1935-01-01">

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value=""></option>
                <option value="president">President</option>
                <option value="vice_president">Vice President</option>
                <option value="security">Security</option>
                <option value="faculty">Faculty</option>
                <option value="staff">staff</option>
                <option value=""></option>
                <option value=""></option>
            </select>

            <label for="contact_number">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="type">Type:</label>
            <select id="type" name="type" required>
                <option value="User">User</option>
            </select>
            <label>Default image:</label>
            <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="file-input-label">

            <label for="license">Driver's License Number:</label>
            <input type="number" id="license" name="license" min="5" required>

            <label for="license_img">Driver's License Image:</label>
            <input type="file" id="license_img" name="license_img" accept="image/jpg, image/jpeg, image/png" class="file-input-label">


            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Default Password:</label>
            <input type="password" id="password" name="password" required>

            <div class="button-container">
                <button type="button" class="cancel-button" onclick="window.history.back();">Cancel</button>
                <button type="submit" class="submit-button">Create</button>
                
            </div>

        </form>
    </div>
        </div>
    </div>

    <script src="assets/script/index.js"></script>
</body>
</html>
