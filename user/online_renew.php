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

$user_id = $_SESSION['id'];

$query = "SELECT r.plate_number, 
                 r.created_at, 
                 r.expiration_date,
                 v.vehicle_type 
                 FROM receipts r
                 JOIN vehicle v ON r.plate_number = v.plate_number
                 WHERE v.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$vehicles = [];
while ($row = $result->fetch_assoc()) {
    $vehicles[] = $row;
}

$query = "SELECT id, image, username FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $image = $row['image'];
    $user_name = $row['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renew Vehicle</title>
    <link rel="stylesheet" href="assets/css/putngnapaycss.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            user-select: none;
        }
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 999; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.4); 
        }
        .modal-content { 
            background-color: #fefefe; 
            margin-top: 180px;
            padding: 20px; 
            border: 1px solid #888; 
            width: 80%; 
        }
        .close { 
            color: #aaa; 
            float: right; 
            font-size: 28px; 
            font-weight: bold; 
        }
        .close:hover, .close:focus { 
            color: black; 
            text-decoration: none; 
            cursor: pointer; 
        }
        .modal-overlay { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0, 0, 0, 0.5); 
            z-index: 998; 
        }
        .center-message { 
            display: none; 
            position: fixed; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            background: #fff; 
            padding: 20px; 
            border-radius: 5px; 
            z-index: 999; 
        }
        .center-message.show, .modal-overlay.show { 
            display: block; 
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
            <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'payment.php') ? 'active' : ''; ?>">
                <i class="fas fa-money-bill-wave"></i>
                <a href="payment.php" class="nav-link"><h3>Payment</h3></a>
            </div>

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
        <div class="payment-container">
        <div class="arrow" class="arrow">
                    <a href="payment.php">
                        <i class="fas fa-arrow-left nav-img"></i>
                    </a>
                </div>
            <h2>Select Vehicle and Make Renew</h2>             
            <form id="vehicle-form" method="POST" action="process_renew.php">
                <div class="form-group">
                    <label for="vehicle">Select Registered Vehicle:</label>
                    <select name="plate_number" id="vehicle" required>
                        <option value="">Select a vehicle</option>
                        <?php foreach ($vehicles as $vehicle) : ?>
                            <option value="<?php echo htmlspecialchars($vehicle['plate_number']); ?>"
                                    data-type="<?php echo htmlspecialchars($vehicle['vehicle_type']); ?>">
                                <?php echo htmlspecialchars($vehicle['plate_number']) . ' - ' . htmlspecialchars($vehicle['created_at']) . ' | Expiration: (' . htmlspecialchars($vehicle['expiration_date']) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount-display">Amount:</label>
                    <input type="text" id="amount-display" readonly>
                </div>
                <button type="button" id="proceed-to-payment">Proceed to Renew</button>
            </form>

            <div class="modal-overlay" id="modalOverlay"></div>
            <div class="center-message" id="centerMessage">
                <p>Processing...</p>
            </div>

            <div id="payment-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <form action="process_renew.php" method="POST" id="payment-form">
                        <input type="hidden" name="amount" id="amount">
                        <input type="hidden" name="plate_number" id="plate_number">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user_name); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Payment Method:</label>
                            <div class="payment-methods">
                                <label>
                                    <input type="radio" name="payment_method" value="credit" checked> Credit Card
                                </label>
                                <label>
                                    <input type="radio" name="payment_method" value="debit"> Debit Card
                                </label>
                            </div>
                        </div>
                        <button type="submit" onclick="return confirmSend(event)">Pay Now</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('vehicle').addEventListener('change', function() {
    var vehicleSelect = this;
    var vehicleType = vehicleSelect.options[vehicleSelect.selectedIndex].getAttribute('data-type');
    var amount = 0;

    if (vehicleType === '4_wheel') {
        amount = 1200; 
    } else if (vehicleType === '2_wheel' || vehicleType === '3_wheel') {
        amount = 600; 
    }

    document.getElementById('amount-display').value = amount;
});

document.getElementById('proceed-to-payment').addEventListener('click', function() {
    var vehicleSelect = document.getElementById('vehicle');
    var vehicle = vehicleSelect.value;

    if (vehicle) {
        var amount = document.getElementById('amount-display').value;
        var modal = document.getElementById('payment-modal');

        document.querySelector('.close').onclick = function() {
            modal.style.display = "none";
        };
        
        document.getElementById('amount').value = amount;
        document.getElementById('plate_number').value = vehicle;

        modal.style.display = "block";
    } else {
        alert('Please select a vehicle.');
    }
});

function confirmSend(event) {
    event.preventDefault(); 



    document.getElementById('payment-modal').style.display = "none";

    document.getElementById('modalOverlay').classList.add('show');
    document.getElementById('centerMessage').classList.add('show');

    setTimeout(function() {
        document.getElementById('payment-form').submit();
    }, 2000); 
}
</script>
<script src="assets/script/index.js"></script>


</body>
</html>

