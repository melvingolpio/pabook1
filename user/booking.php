<?php
session_start();
require('../dbconn.php'); 

$shutdown = file_get_contents('../admin/shutdown_status.txt');
if ($shutdown !== '0'){
    header('Location: homepage.php');
    exit();
}

if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role'];
}else {
    echo "";
    exit();
}

if ($_SESSION['restricted'] === 1) {
    header('Location: loading.php');
    exit();
}

$payment_link = ($_SESSION['role'] !== 'president' && $_SESSION['role'] !== 'vice_president');


if (!isset($_SESSION['username']) || $_SESSION['type'] != 'User') {
    header("Location: ../login.php"); 
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plate_number = $_POST['plate_number'] ?? '';
    $car_type = $_POST['car_type'] ?? '';

    if (empty($plate_number)) {
        echo "No vehicle selected.";
        exit();
    }

    $_SESSION['selected_plate_number'] = $plate_number;
    
    if ($user_role === 'president' || $user_role === 'vice_president') {
        header("Location: pre-wheel.php");
    } else {
        switch ($car_type) {
            case '4_wheel':
                header("Location: 4wheel.php");
                break;
            case '3_wheel':
                header("Location: 3wheel.php");
                break;
            case '2_wheel':
                header("Location: 2wheel.php");
                break;
            default:
                header("Location: booking.php"); 
                break;
        }
        exit();
    }
    
}

$vehicle_types = ['4_wheel', '3_wheel', '2_wheel'];

$user_id = $_SESSION['id']; 

$user_query = "SELECT image FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$result = $user_stmt->get_result();

$image = $result->fetch_assoc()['image'] ?? '';

$reservation_query = "SELECT vehicle_type, slot_number, plate_number, reservation_date, status FROM reservations WHERE user_id = ? AND (status = 'reserved' OR status = 'occupied')";
$reservation_stmt = $conn->prepare($reservation_query);
$reservation_stmt->bind_param('i', $user_id);
$reservation_stmt->execute();
$reservation_result = $reservation_stmt->get_result();
$reservation_details =$reservation_result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <link rel="stylesheet" href="assets/css/bookstyles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            user-select: none;
        }
        .title-vehicle {
            color: white;
            font-size: 50px;
        }
        .card-bdy {
            height: auto;
            width: 300vh;
        }
        .logo {
            font-variant: small-caps;
            font-weight: bold;
            color: white;
            text-shadow: 2px 4px 6px rgb(0, 0, 0);
        }

        .menuicn {
            z-index: 1;
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
            .title-vehicle {
                font-size: 40px;
            }
            .card-container {
                margin-top: 50px;  
                background-color: red;  
            } 
            label {
                font-size: 14px;
            }
            .select-vehicle {
                font-size: px;
            }
            

        }
        @media (max-width: 768px) {

            .navcontainer{
                z-index: 999;
            }

            .card-container {
                margin-top: 50px;
                width: 50%;
                background-color: red;
                
            } 

            .menuicn {
                opacity: 1;
            }

        }
        @media (min-width: 769px) and (max-width: 1024px) {
            .card-container {
                margin-top: 50px;
                width: 50%;
                background-color: red;
            } 

            .menuicn {
                opacity: 0;
            }
        }
        @media (max-width: 850px) {
            .menuicn {
                opacity: 1;
            }
        }

        .hidden {
            display:none;
        }

    </style>
</head>
<body>
<header>
        <div class="picture">
            <div class="dp">
              <img src="../img/<?php echo htmlspecialchars($image); ?>" class="dpicn" alt="Profile Picture">
            </div>
            <div class="name">
              <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
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
    
    
<?php if ($reservation_details): ?>
    <div id="reservationModal" class="modal" style="display: hidden;">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Reservation Details</h2><hr>
            <p><strong>VEHICLE TYPE: </strong><?php echo htmlspecialchars($reservation_details['vehicle_type']); ?></p>
            <p><strong>Slot Number: </strong><?php echo htmlspecialchars($reservation_details['slot_number']); ?></p>
            <p><strong>Plate Number: </strong><?php echo htmlspecialchars($reservation_details['plate_number']); ?></p>
            <p><strong>Reservation Date: </strong><?php echo htmlspecialchars($reservation_details['reservation_date']); ?></p>
            <p><strong>Status: </strong><?php echo htmlspecialchars($reservation_details['status']); ?></p>
        </div>
    </div>
<?php endif;?>  
        
        <div class="card-container">

            <?php if ($reservation_details): ?>
                <div class="card-bdy" id="card-slot">
                    <h5>Your Slot</h5>
                </div>
            <?php endif; ?>

            <p class="title-vehicle">VEHICLE</p>
            <hr style="width: 100%;">
            <br>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="car_type">Select Vehicle Type</label>
                    <select id="car_type" name="car_type" onchange="updateVehicles()" required>
                        <option value="">Select Type</option>
                        <?php foreach ($vehicle_types as $type): ?>
                            <option value="<?php echo $type; ?>"><?php echo ucfirst(str_replace('_', ' ', $type)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="vehicle">Select Vehicle</label>
                    <select id="vehicle" name="plate_number" required>
                        <option value=""  class="select-vehicle">Select Vehicle</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="hidden" name="paid" value="1"> 
                    <button type="submit" id="book_button" disabled>Proceed</button>
                </div>
            </form>

            <script>
                function updateVehicles() {
                    const vehicleType = document.getElementById('car_type').value;
                    const vehicleSelect = document.getElementById('vehicle');
                    const bookButton = document.getElementById('book_button');

                    vehicleSelect.innerHTML = '<option value="">Select Vehicle</option>';

                    if (!vehicleType) {
                        bookButton.disabled = true;
                        return;
                    }

                    fetch('get_vehicles.php?type=' + encodeURIComponent(vehicleType))
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                data.forEach(vehicle => {
                                    const option = document.createElement('option');
                                    option.value = vehicle.plate_number;
                                    option.textContent = `${vehicle.plate_number} - ${vehicle.vehicle_brand}`;
                                    vehicleSelect.appendChild(option);
                                });
                                bookButton.disabled = false;
                            } else {
                                vehicleSelect.innerHTML = '<option value="">No Vehicles Available</option>';
                                bookButton.disabled = true;
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching vehicles:', error);
                        });
                }

                document.getElementById('vehicle').addEventListener('change', function() {
                    const selectedVehicle = this.value;
                    const bookButton = document.getElementById('book_button');
                    bookButton.disabled = !selectedVehicle;
                });

                
                //nav & hide the card booking
                document.querySelector('.menuicn');
                let nav = document.querySelector('.navcontainer');

                menuicn.addEventListener('click', (event) => {
                    nav.classList.toggle('navclose');

                    let cardBooking = document.querySelector('.card-bdy');
                    setTimeout(() => {
                        cardBooking.classList.toggle('hidden');
                    }, 500);
                    
                });
            </script>
        </div>
    </div>

    <script src="assets/script/popReserved.js"></script>
</body>
</html>