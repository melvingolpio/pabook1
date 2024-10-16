<?php
session_start();
require('../dbconn.php'); 

if ($_SESSION['type'] != 'User') {
    header("Location: ../login.php"); 
    exit();
} 

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/ustyless.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>

    <header>

        <div class="picture">
            <div class="dp">

              <img src="../img/ced.jpg" class="dpicn" alt="Picture">
              </div>

              <div class="name">
            <p><?php echo $_SESSION['username']; ?>!</p>
              </div>
        </div>
        

        <div class="logosec">
            <div class="logo">PaBook</div>
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
                        <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'booking.php') ? 'active' : ''; ?>">
                            <i class="fas fa-calendar-alt nav-img" alt="booking"></i>
                            <a href="booking.php" class="nav-link"><h3>Booking</h3></a>
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

            <div class="box-container">

                <div class="box box1">
                    <div class="text">
                        <a href="receipt.php"> 
                        <h2 class="topic-heading">Receipt</h2>
                        <h2 class="topic">View</h2>
                        </a>
                    </div>

                    <i class="fas fa-users" alt="Views"></i>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/script/index.js"></script>
</body>
</html>
