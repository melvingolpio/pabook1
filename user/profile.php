<?php
session_start();
require('../dbconn.php');

$shutdown = file_get_contents('../admin/shutdown_status.txt');
if ($shutdown !== '0'){
    header('Location: homepage.php');
}

if ($_SESSION['type'] != 'User') {
    header("Location: ../login.php"); 
    exit();
} 

if ($_SESSION['restricted'] === '1'){
    header('Location: loading.php');
    exit();
}
if ($_SESSION['disabled'] === '1'){
    header('Location: homepage.php');
    exit();
}

if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role'];
}else {
    echo "";
    exit();
}
$payment_link = ($_SESSION['role'] !== 'president' && $_SESSION['role'] !== 'vice_president');

if ($_SESSION['type'] == 'User') {
    $user_id = $_SESSION['id'];
    $query = "SELECT id, first_name, last_name, username, birth_date, gender, contact_number, type, email, image FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $id = $user['id'];
        $fullname = $user['first_name'] . ' ' . $user['last_name'];
        $username = $user['username'];
        $birth_date = $user['birth_date'];
        $gender = $user['gender'];
        $contact_number = $user['contact_number'];
        $type = $user['type'];
        $email = $user['email'];
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
        <link rel="stylesheet" href="assets/css/uprofilestyles.css">
        <link rel="stylesheet" href="assets/css/responsive.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        html, body {
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            overflow: hidden;
            user-select: none;
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

            .prof-container {
                width: 100%;
                overflow: hidden;
                height: auto;
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
                    <img src="../img/<?php echo htmlspecialchars($image); ?>" class="dpicn" alt="Picture">
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
                    <p><strong>Email:</strong> <?php echo $email ?></p>
                    <p><strong>Type:</strong> <?php echo $type; ?></p>
                    <br>
                    <button class="btn-update"><a href="profile_details.php">Details</a></button>
                </div>
            </div>  
        </div>

<script src="assets/script/image.js"></script>
<script src="assets/script/index.js"></script>
</body>
</html>
    <?php
} else {
    header("Location: ../login.php"); 
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
    exit();

}
?>
