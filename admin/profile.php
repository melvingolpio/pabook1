<?php
session_start();
require('../dbconn.php'); 

if ($_SESSION['type'] != 'Admin') {
    header("Location: login.php"); 
    exit();
} 

$user_id = $_SESSION['id'];
$query = "SELECT id, first_name, last_name,username, email, type, image FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $id = $user['id'];
    $fullname = $user['first_name'] . ' ' . $user['last_name'];
    $username = $user['username'];
    $email = $user['email'];
    $type = $user['type'];
    $image = $user['image'];
} 
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/prof-style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
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
        
        <div class="prof-container">
            <div class="prof-head">
                <h2>Profile Information</h2>
                 <img src="../img/<?php echo htmlspecialchars($image);?>" class="prof-pic">
            </div>

            <div class="prof-foot">
                <div class="profile-details">
                    
                    <p  class="fullname"> <?php echo $username; ?></p>
                    <hr></hr>
                    <br>
                    <p><strong>Fullname:</strong> <?php echo $fullname; ?></p>
                    <p><strong>Email:</strong> <?php echo $email; ?></p>
                    <p><strong>Type:</strong> <?php echo $type; ?></p>
                    <br>
                    <button class="btn-update"><a href="profile_details.php">Details</a></button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/script/index.js"></script>
</body>
</html>
