<?php
session_start();
require('../dbconn.php'); 

$shutdown = file_get_contents('../admin/shutdown_status.txt');
if ($shutdown !== '0'){
    header('Location: homepage.php');
    exit();
}

if ($_SESSION['type'] != 'User') {
    header("Location: index.php"); 
    exit();
} 

if ($_SESSION['restricted'] === 1) {
    header("Location: loading.php"); 
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

$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $model = $_POST['model'];
    $color = $_POST['color'];
    $vehicle_type = $_POST['vehicle_type'];
    $plate_numbers = $_POST['plate_numbers'];

    $vehicle_picture = '';

    if (isset($_FILES['vehicle_picture']) && $_FILES['vehicle_picture']['error'] == UPLOAD_ERR_OK) {
        $vehicle_picture = $upload_dir . basename($_FILES['vehicle_picture']['name']);
        move_uploaded_file($_FILES['vehicle_picture']['tmp_name'], $vehicle_picture);
    }

    $query = "INSERT IGNORE INTO vehicle (user_id, vehicle_brand, plate_number, vehicle_type, color, vehicle_picture) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isssss', $user_id, $model, $plate_numbers, $vehicle_type, $color, $vehicle_picture);


    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $success_message = "Succesful Registration!";
        } else {         
            $error_message = "Vehicle plate number is already used";       
        }
    } else {    
        $error_message= "ERROR" . $stmt->error;
    }
    
    $stmt->close();
    
}

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
    <title>Register Vehicle</title>
    <link rel="stylesheet" href="assets/css/uregister.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<style>
    ..main {
            width: 100%;  
            max-width: 1200px; 
            margin: 0 auto;  
            padding: 20px; 
            box-sizing: border-box; 
            
        }
        body {
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .main {
                width: 100vw;
                padding: 20px;
                box-sizing: border-box; 
            }

            .signup-container {
                margin-bottom: 20px;
                overflow-x: auto; 
                border: 1px solid #ddd;
                border-radius: 10px;
                padding: 15px; 
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
                background-color: #f9f9f9;
            }

            label {
                margin-top: 10px;
                display: block;
                font-weight: bold; 
                color: #333; 
            }

            input[type="text"], select, input[type="file"] {
                width: 30vh;
                box-sizing: border-box; 
                margin-bottom: 10px; 
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 16px; 
                color: #555; 
            }

            button {
                padding: 10px 20px;
                border: none;
                border-radius: 5px; 
                background-color: #3498db;
                color: white;
                cursor: pointer; 
                transition: background-color 0.3s; 
            }

            button:hover {
                background-color: #2980b9; 
            }

            .button-container {
                display: flex;   
                
            }
            .submit-button {
                padding-right: 20px;
            }

            .name {
                display: none;
            }
            .logo {
                padding-left: 80px;
            }
            .menuicn {
                opacity: 1;
            }
        
    }
</style>

<body>

    <header>
        <div class="picture">
            <div class="dp">
                <img src="../img/<?php echo htmlspecialchars($image); ?>" class="dpicn" alt="Picture">
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
            <h1>Register Vehicles</h1>
            <br>
            <div class="signup-container">
                
                <?php if (isset($success_message)) { ?>
                    <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
                <?php } ?>
                <?php if (isset($error_message)) { ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
                <?php } ?>

                <form method="POST" action="register.php" id="register-form" enctype="multipart/form-data" onsubmit="confirmSend(event)">

                    <label for="vehicle_picture">Vehicle Picture:</label>
                    <input type="file" id="vehicle_picture" name="vehicle_picture" accept="image/jpg, image/jpeg, image/png" class="file-input-label">

                    <label for="model">Vehicle Brand:</label>
                    <input type="text" id="model" name="model" required>

                    <label for="color">Color:</label>
                    <input type="text" id="color" name="color" required>

                    <label for="vehicle_type">Vehicle Type:</label>
                    <select id="vehicle_type" name="vehicle_type" required>
                        <option value=""></option>
                        <option value="4_wheel">4 wheel</option>
                        <option value="3_wheel">3 wheel</option>
                        <option value="2_wheel">2 wheel</option>
                    </select>

                    <label for="plate_numbers">Plate Numbers:</label>
                    <input type="text" id="plate_numbers" name="plate_numbers" required>

                    <div class="button-container">
                        <button type="button" class="cancel-button" onclick="window.history.back();">Cancel</button>
                        <button type="submit" class="submit-button">Register</button>
                    </div>
                </form>

                <div class="modal-overlay" id="modalOverlay"></div>
                <div class="center-message" id="centerMessage">
                    <p>Processing...</p>
                </div>

            </div>
        </div>
    </div>

    <script>
        function confirmSend(event) {
            event.preventDefault();

            document.getElementById('modalOverlay').classList.add('show');
            document.getElementById('centerMessage').classList.add('show');

            setTimeout(function() {
                
                document.getElementById('register-form').submit();
            }, 2000);   
        }

        function hideMessages() {
            let successMessage = document.querySelector('.success-message');
            let errorMessage = document.querySelector('.error-message');

            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                    removeUrlParams();
                    
                }, 3000);
            }

            if (errorMessage) {
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                    removeUrlParams();
                }, 3000);
            }
        }

        function removeUrlParams() {
            if (history.replaceState) {
                let newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({path: newUrl}, '', newUrl);
            }
        }

        document.addEventListener('DOMContentLoaded', hideMessages);

    </script>

    <script src="assets/script/index.js"></script>
    
</body>
</html>
