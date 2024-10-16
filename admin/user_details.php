<?php
session_start();
require('../dbconn.php'); 

if ($_SESSION['type'] == 'Admin') {
    
    $img_sql = "SELECT image FROM users WHERE id = 5";
    $result=$conn->query($img_sql);
    $row=$result->fetch_assoc();

    $i_image = $row['image'];

    $id = $_GET['id'];

    $sql = "SELECT * FROM users WHERE id='$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    $username = $row['username'];
    $fullname = $row['first_name'] . ' ' . $row['last_name'];
    $gender = $row['gender'];
    $age = $row['age'];
    $contact_number = $row['contact_number'];
    $type = $row['type'];
    $email = $row['email'];
    $image = $row['image'];
    $penalty = $row['penalty'];

    $vehicle_sql = "SELECT plate_number, vehicle_brand, vehicle_type, color, amount,paid FROM vehicle";
    $stmt = $conn->prepare($vehicle_sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $vehicles = [];

    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Details</title>
    <link rel="stylesheet" href="assets/css/profiledet.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        
    
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
            <div id="fullImageModal" class="modal">
                <span class="close" onclick="closeModal()">&times;</span>
                <img class="modal-content" id="fullImage">
            </div>
            
            <div class="prof-content">
                <div class="arrow">
                    <a href="user_account.php" class="nav-btn">
                        <i class="fas fa-arrow-left nav-img"></i>
                    </a>
                </div>
                <div class="profile-head">
                    <img src="../img/<?php echo htmlspecialchars($image); ?>" class="prof-pic" onclick="showFullImage('../img/<?php echo htmlspecialchars($image); ?>')">
                    <p class="fullname"><?php echo $username; ?></p>
                </div>
                
                <div class="profile-details">
                    <p><strong>ID:</strong> <?php echo $id; ?></p>
                    <p><strong>Fullname:</strong> <?php echo $fullname; ?></p>
                    <p><strong>Age:</strong> <?php echo $age; ?></p>
                    <p><strong>Gender:</strong> <?php echo $gender; ?></p>
                    <p><strong>Contact Number:</strong> <?php echo $contact_number; ?></p>
                    <p><strong>Type:</strong> <?php echo $type; ?></p>
                    <p><strong>Email:</strong> <?php echo $email; ?></p>
                    <p><strong>Penalty:</strong> <?php echo $penalty; ?></p>
                    <br>
                   
                </div>
                
                <div class="profile-vehicles">
                    <h3>Vehicles:</h3>
                    <div class="vehicles-list">
                        <?php 
                        if (!empty($vehicles)) {
                            foreach ($vehicles as $vehicle) {
                                echo '<div class="vehicle-card">';
                                echo '<p><strong>Plate Number:</strong> ' . htmlspecialchars($vehicle['plate_number']) . '</p>';
                                echo '<p><strong>Brand:</strong> ' . htmlspecialchars($vehicle['vehicle_brand']) . '</p>';
                                echo '<p><strong>Type:</strong> ' . htmlspecialchars($vehicle['vehicle_type']) . '</p>';
                                echo '<p><strong>Color:</strong> ' . htmlspecialchars($vehicle['color']) . '</p>';
                                echo '<p><strong>Paid:</strong> ' . htmlspecialchars($vehicle['paid']) . '</p>';
                                echo '<p><strong>Amount:</strong> ' . htmlspecialchars($vehicle['amount']) . '</p>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                
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
