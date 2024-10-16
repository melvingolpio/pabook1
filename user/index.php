<?php
session_start();
require('../dbconn.php');

if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role'];
}else {
    echo "";
    exit();
}

$shutdown_status = file_get_contents('../admin/shutdown_status.txt');
if ($shutdown_status !== '0') { 
    header('Location: homepage.php');
    exit();
}

$payment_link = ($_SESSION['role'] !== 'president' && $_SESSION['role'] !== 'vice_president');

if ($_SESSION['type'] == 'User') {
    $user_id = $_SESSION['id'];

    $Mquery = "SELECT image FROM users WHERE id = ?";
    
    $Mstmt = $conn->prepare($Mquery);
    $Mstmt->bind_param('i', $user_id);
    $Mstmt->execute();
    $result = $Mstmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image = $row['image'];
    }

    
    //activities
    $act_query = "SELECT plate_number, date, time_in, time_out, status, slot_id FROM activities WHERE user_id = ?";
    $act_stmt = $conn->prepare($act_query);
    $act_stmt->bind_param('i', $user_id);
    $act_stmt->execute();
    $act_result = $act_stmt->get_result();

    //reservations
    $query = "SELECT plate_number, user_id, reservation_date, slot_id, status FROM reservations WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/newindexx.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    body {
        user-select: none;
    }
    
.nav-option {
    width: 250px;
    height: 50px;
    display: flex;
    align-items: center;
    margin-right: 10px;
    padding: 0 30px 0 20px;
    transition: all 0.2s ease-in-out;
    
  }

.nav-option:hover {
    border: none;
}
.nav {
    z-index: 999;
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

    /*START para sa header */
    @media (max-width: 300px){
        .name {
            display: none;
        }
        .logo {
            padding-left: 80px;
        }

    }
    @media (max-width: 301px){
        .name {
            display: none;
        }
        .logo {
            padding-left: 80px;
        }
    }
    @media (max-width: 400px) {
        .name {
            display: none;
        }
        .logo {
            padding-left: 75px;
        }
        input {
            width: 20vh;
            font-size: 10px;
        }
    }


    @media (max-width: 768px) {
        .menuicn {
            opacity: 1;
        }
        .logo {
            padding-left: 55px;
        }
    }
    @media (min-width: 769px) and (max-width: 850px) {
        .menuicn {
            opacity: 1;
        }
    }
     /*END */

</style> 
    
</head>

<body>

<header>
    <div class="picture">     
        <div class="dp">
            <img src="../img/<?php echo htmlspecialchars($image); ?>" class="dpicn" alt="Picture">
        </div>
        <div class="name">
            <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
        </div>
        <div id="fullImageModal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="fullImage">
        </div>
    </div>
    <a href="qr-scanner.php">QR Scanner</a><!-- use for verifying function of booking/reservation. Remove if not needed-->
    <div class="logosec">
        <div class="logo">PaBook</div>
        <i class="fas fa-bars icn menuicn" id="menuicn"></i>
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
                    <?php if ($payment_link): ?>
                        <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'payment.php') ? 'active' : ''; ?>">
                            <i class="fas fa-money-bill-wave"></i>
                            <a href="payment.php" class="nav-link"><h3>Payment</h3></a>
                        </div>
                    <?php endif;?>

                    <!--<div class="nav-option ?php echo (basename($_SERVER['PHP_SELF']) == 'qr-scanner.php') ? 'active' : ''; ?>">
                        <i class="fas fa-camera-retro    nav-img" alt="scanner"></i>
                        <a href="qr-scanner.php" class="nav-link"><h3>Scanner</h3></a>
                    </div>-->
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'receipt.php') ? 'active' : ''; ?>">
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
        <div class="animated-container">
            <img src="../img/jeep.gif" class="animated-box car" onclick="stopAnimation(this)">
            
        </div>

        <div class="report-container">
            <div class="report-header">
                <h1 class="recent-Articles">Activities</h1>
                <div style="position: relative;">
                    <input type="text" id="searchBox" class="search-box" placeholder="Search date">
                    <span class="fa fa-search search-icon"></span>
                </div>
            </div>
            <br>
            <div class="report-body">
                <table class="table" id="activitiesTable">
                    <thead>
                        <tr>
                            <th>Plate number</th>
                            <th>Date</th>
                            <th>Time-in</th>
                            <th>Slot Number</th>
                            <th>Time-out</th>           
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row_activity = $act_result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row_activity['plate_number']; ?></td>
                            <td><?php echo $row_activity['date']; ?></td>
                            <td><?php echo $row_activity['time_in']; ?></td>
                            <td><?php echo $row_activity['slot_id']; ?></td>
                            <td><?php echo $row_activity['time_out']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="report-container">
            <div class="report-header">
                <h1 class="recent-Articles">Reservations</h1>
                <div style="position: relative;">
                    <input type="text" id="searchBox2" class="search-box" placeholder="Search date">
                    <span class="fa fa-search search-icon"></span>
                </div>
            </div>
            <br>
            <div class="report-body"> 
                <table class="table" id="reservationsTable">
                    <thead>
                        <tr>
                            <th>Plate number</th>
                            <th>Date</th>
                            <th>Slot Number</th>                         
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row_reservation = $result->fetch_assoc()) : ?>
                        <tr>  
                            <td><?php echo $row_reservation['plate_number']; ?></td>
                            <td><?php echo $row_reservation['reservation_date']; ?></td>    
                            <td><?php echo $row_reservation['slot_id']; ?></td>
                            <td><?php echo $row_reservation['status']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="assets/script/search.js"></script>
<script src="assets/script/index.js"></script>

</body>
</html>

<?php
} else {
    header("Location: ../logout.php");
    exit();
}
?>
