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

$userId = $_SESSION['id'];

$query = "SELECT 
            v.vehicle_type,
            r.plate_number, 
            r.id, 
            r.receipt_token, 
            r.expiration_date, 
            r.qr_code, 
            r.created_at, 
            u.username, 
            u.birth_date, 
            u.gender,
            u.image, 
            u.contact_number, 
            u.type, 
            u.email, 
            CONCAT(u.first_name, ' ', u.last_name) AS fullname 
          FROM receipts r 
          JOIN users u ON r.user_id = u.id 
          JOIN vehicle v ON r.plate_number = v.plate_number
          WHERE r.user_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION['image'] = $row['image']; 
    } else {
        echo "No receipts found for this user.";
        exit();
    }
} else {
    echo "Failed to prepare statement.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="assets/css/stylesre.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    body {
            user-select: none;
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
    .nav-upper-options {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;  
    }
    .nav-option:hover {
        border: none;
    }
    
    @media (max-width: 850px) {
        .menuicn {
            opacity: 1; 
        }
        
    }
    .box-container {
        display: flex;                 
        flex-direction: column;          
        gap: 15px;                       
        max-height: 150px;              
        overflow-y: auto;               
        background-color: #1a74e2;    
        border: 1px solid darkblue;  
        border-radius: 3px;           
        padding: 10px;                   
    }
    .box-container:hover>:not(:hover) {
        transform: scale(1);
        opacity: 0.1;

    }

    .box {
        background-color: white;
    }
    .wheels432 {
        font-family: 'Arial', sans-serif;
        font-size: 20px;
        font-weight: bold;
        font-variant: small-caps;
    }

    .box-container::-webkit-scrollbar {
        width: 12px;
    }
    .box-container::-webkit-scrollbar-track {
        background-color: #f1f1f1;
        border-radius: 1px;
    }
    .box-container::-webkit-scrollbar-thumb {
        background-color: #3498db;
        border-radius: 1px;
        border: 3px solid #ffffff;
    }
    .box-container::-webkit-scrollbar-thumb:hover {
        background-color: #2980b9;
    }

    .modal-content {
        margin-top: 100px;
        width: 140vw; 
    }

    @media(max-width: 767px) {
        .name {
            display: none;
        }
        .logo {
            margin-left: 80px;
        }
        .qr-code {
            margin-top: 50px;
        }
        body {
            overflow: hidden;
        }
        .main {
            overflow: hidden;
            padding: 0;    
            left: 0;
            margin-top: 30px;
            
        }
        .wheels432 {
            margin-left: 10px;
        }

        body{
            
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
                <img src="../img/<?php echo htmlspecialchars($_SESSION['image']); ?>" class="dpicn" alt="Profile Picture">
            </div>
            <div class="name">
                <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
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
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'receipt.php') ? 'active' : ''; ?>">
                        <i class="fas fa-money-check-alt nav-img" alt="receipt"></i>
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
            <p class="wheels432">2 Wheel Vehicles</p>
            <div class="box-container" id="two-wheel">
                <?php 
                $result->data_seek(0); 
                while ($row = $result->fetch_assoc()): 
                   
                    if (strpos($row['vehicle_type'], '2_wheel') !== false): ?>
                    <div class="box" style="background-color: white;" data-receipt='<?php echo htmlspecialchars(json_encode($row)); ?>'>
                        <div class="text">
                            <a href="#">
                                <h2 class="topic-heading">Receipt</h2>
                                <h2 class="topic"><?php echo htmlspecialchars($row['plate_number']); ?></h2>
                            </a>
                        </div>
                        <i class="fas fa-receipt icon" style="color: #1a74e2;"></i>
                    </div>
                <?php endif; endwhile; ?>
            </div>
<br>
            <p class="wheels432">3 Wheel Vehicles</p>
            <div class="box-container" id="three-wheel">
                <?php 
                $result->data_seek(0); 
                while ($row = $result->fetch_assoc()): 
                    if (strpos($row['vehicle_type'], '3_wheel') !== false): ?>
                    <div class="box" style="background-color: white;" data-receipt='<?php echo htmlspecialchars(json_encode($row)); ?>'>
                        <div class="text">
                            <a href="#">
                                <h2 class="topic-heading">Receipt</h2>
                                <h2 class="topic"><?php echo htmlspecialchars($row['plate_number']); ?></h2>
                            </a>
                        </div>
                        <i class="fas fa-receipt icon" style="color: #1a74e2;"></i>
                    </div>
                <?php endif; endwhile; ?>
            </div>
<br>
            <p class="wheels432">4 Wheel Vehicles</p>
            <div class="box-container" id="four-wheel-container">
                <?php 
                $result->data_seek(0); 
                while ($row = $result->fetch_assoc()): 
                    if (strpos($row['vehicle_type'], '4_wheel') !== false): ?>
                    <div class="box" style="background-color: white;" data-receipt='<?php echo htmlspecialchars(json_encode($row)); ?>'>
                        <div class="text">
                            <a href="#">
                                <h2 class="topic-heading">Receipt</h2>
                                <h2 class="topic"><?php echo htmlspecialchars($row['plate_number']); ?></h2>
                            </a>
                        </div>
                        <i class="fas fa-receipt icon" style="color: #1a74e2;"></i>
                    </div>
                <?php endif; endwhile; ?>
            </div>

            <div id="receiptModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <div class="modal-body">
                        <div class="receipt-info">
                            <h1 class="receipt-title">Payment Receipt</h1>
                            <p><strong>ID:</strong> <span id="receiptId"></span></p>
                            <p><strong>Plate Number:</strong> <span id="plateNumber"></span></p>
                            <p><strong>Username:</strong> <span id="username"></span></p>
                            <p><strong>Fullname:</strong> <span id="fullname"></span></p>
                            <p><strong>birth-date:</strong> <span id="age"></span></p>
                            <p><strong>Gender:</strong> <span id="gender"></span></p>
                            <p><strong>Email:</strong> <span id="email"></span></p>
                            <p><strong>Contact Number:</strong> <span id="contactNumber"></span></p>
                            <p><strong>Expiration Date:</strong> <span id="expirationDate"></span></p>
                        </div>
                        <div class="qr-code">
                            <img id="qrCode" src="" alt="QR Code" class="qr-body">
                        </div>
                    </div>
                    <a id="downloadBtn" class="btn-download" href="#" download>Download Receipt</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.box').forEach(box => {
            box.addEventListener('click', () => {
                const receipt = JSON.parse(box.getAttribute('data-receipt'));
                
                document.getElementById('receiptId').textContent = receipt.id;
                document.getElementById('plateNumber').textContent = receipt.plate_number;
                document.getElementById('username').textContent = receipt.username;
                document.getElementById('fullname').textContent = receipt.fullname;
                document.getElementById('age').textContent = receipt.birth_date;
                document.getElementById('gender').textContent = receipt.gender;
                document.getElementById('email').textContent = receipt.email;
                document.getElementById('contactNumber').textContent = receipt.contact_number;
                document.getElementById('expirationDate').textContent = receipt.expiration_date;
                
                document.getElementById('qrCode').src = `../admin/${receipt.qr_code}`;
                document.getElementById('downloadBtn').href = `../admin/${receipt.qr_code}`;
                document.getElementById('downloadBtn').setAttribute('download', `receipt_${receipt.id}.png`);

                document.getElementById('receiptModal').style.display = 'block';
            });
        });

        document.querySelector('.close').addEventListener('click', () => {
            document.getElementById('receiptModal').style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            const modal = document.getElementById('receiptModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
    <script src="assets/script/index.js"></script>
</body>

</html>
