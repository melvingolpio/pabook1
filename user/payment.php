<?php
session_start();
require('../dbconn.php'); 

$shutdown = file_get_contents('../admin/shutdown_status.txt');
if ($shutdown !== '0'){
    header('Location: homepage.php');
    exit();
}
if ($_SESSION['type'] != 'User') {
    header("Location: ../login.php"); 
    exit();
} 

if ($_SESSION['restricted'] === 1){
    header('Location: loading.php');
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
    <link rel="stylesheet" href="assets/css/payments.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    header {
        background-color: #1a74e2;
    }
    .name {
        color: white;
    }
    .menuicn {
        color: white;
    }
    .logo {
        color: white;
    }
    .dp {
        border: 2px solid white;
    }
    .logo {
        font-variant: small-caps;
        font-weight: bold;
        color: white;
        text-shadow: 2px 4px 6px rgb(0, 0, 0);
    }
    .topic {
        color: #0a326e;
    }
    .menuicn {
        opacity: 0;
    }
    .nav-option:hover {
        border: none;
    }
    .nav-upper-options {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;  
    }

    @media(max-width: 767px) {
            .name {
                display: none;
            }
            .logo {
                margin-left: 80px;
            }

        }
    @media (max-width: 768px) {
        .menuicn {
            opacity: 1;
        }
    }
    @media (min-width: 769px) and (max-width: 850px) {
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
            <p><?php echo $_SESSION['username']; ?></p>
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
                    <div class="nav-option">
                        <i class="fas fa-tachometer-alt nav-img" alt="dashboard"></i>
                        <a href="homepage.php" class="nav-link"><h3>Homepage</h3></a>
                    </div>
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt nav-img" alt="dashboard"></i>
                        <a href="index.php" class="nav-link"><h3>Dashboard</h3></a>
                    </div>
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'register.php') ? 'active' : ''; ?>">
                        <i class="fas fa-file-signature" alt="register"></i>
                        <a href="register.php" class="nav-link"><h3>Register</h3></a>
                    </div>
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-alt nav-img" alt="booking"></i>
                        <a href="booking.php" class="nav-link"><h3>Booking</h3></a>
                    </div>
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'payment.php') ? 'active' : ''; ?>">
                        <i class="fas fa-money-bill-wave"></i>
                        <a href="payment.php" class="nav-link"><h3>Payment</h3></a>
                    </div>

                    <!--<div class="nav-option ?php echo (basename($_SERVER['PHP_SELF']) == 'qr-scanner.php') ? 'active' : ''; ?>">
                        <i class="fas fa-camera-retro    nav-img" alt="scanner"></i>
                        <a href="qr-scanner.php" class="nav-link"><h3>Scanner</h3></a>
                    </div>-->
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'transaction.php') ? 'active' : ''; ?>">
                        <i class="fas fa-money-check-alt nav-img" alt="institution"></i>
                        <a href="receipt.php" class="nav-link"><h3>Receipt</h3></a>
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

                <div class="box-box box1">
                    <div class="text">
                        <a href="online_payment.php">
                        <h2 class="topic-heading">Vehicle Payment</h2>
                        <h2 class="topic">Online</h2>
                        </a>
                    </div>

                    <i class="fas fa-credit-card"></i>

                </div>

                <div class="box-box box2">
                    <div class="text">
                        <a href="online_renew.php">
                        <h2 class="topic-heading">Renew Payment</h2>
                        <h2 class="topic">Online</h2>
                        </a>
                    </div>
                    <i class="fas fa-sync-alt"></i>

                </div>
            </div>

        </div>
    </div>

    <script src="assets/script/index.js"></script>
</body>
</html>
