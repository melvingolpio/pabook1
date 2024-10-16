<?php
session_start();
require('../dbconn.php'); 

if ($_SESSION['type'] != 'Admin') {
    header("Location: ../login.php"); 
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
    <link rel="stylesheet" href="assets/css/ttstyles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt nav-img" alt="dashboard"></i>
                        <a href="index.php" class="nav-link"><h3>Dashboard</h3></a>
                    </div>
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'user_account.php') ? 'active' : ''; ?>">
                        <i class="fas fa-user nav-img" alt="account's"></i>
                        <a href="user_account.php" class="nav-link"><h3>Account's</h3></a>
                    </div>
                    
                    <!--<div class="nav-option ?php echo (basename($_SERVER['PHP_SELF']) == 'qr-scanner.php') ? 'active' : ''; ?>">
                        <i class="fas fa-scanner nav-img" alt="scanner"></i>
                        <a href="qr-scanner.php" class="nav-link"><h3>Scanner</h3></a>-->
                    </div>
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

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: "Poppins", sans-serif;
                text-decoration: none;
                color: inherit;
            }
            :root {
                --background-color1: #fafaff;
                --background-color2: #ffffff;
                --background-color3: #ededed;
                --background-color4: #f5f5f5;
                --primary-color: #4b49ac;
                --secondary-color: #0c007d;
                --one-use-color: #1560bd;
                --two-use-color: #1560bd;
            }
            body {
                background-color: var(--background-color4);
                max-width: 100%;
                overflow-x: hidden;
            }
            .container {
                width: 50%;
                margin: auto;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .section:before {
                content: "";
                height: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .section:after {
                background-color: whitesmoke;
                padding: 50px 30px;
                border: 1.5px solid #b2b2b2;
                border-radius: 0.25em;
                box-shadow: 0 20px 25px rgba(0, 0, 0, 0.25);
            }
            #my-qr-reader {
                padding: 20px !important;
                border: 1.5px solid #b2b2b2 !important;
                border-radius: 8px;
            }
            #my-qr-reader img[alt="Info icon"] {
                display: none;
            }
            #my-qr-reader img[alt="Camera based scan"] {
                width: 100% !important;
                height: 100% !important;
            }
            button {
                padding: 10px 20px;
                border: 1px solid #b2b2b2;
                outline: none;
                border-radius: 0.25em;
                color: white;
                font-size: 15px;
                cursor: pointer;
                margin-top: 15px;
                margin-bottom: 10px;
                background-color: #1560bd;
                transition: 0.3s background-color;
            }
            button:hover {
                background-color: rgb(23, 41, 128);
            }
            #html5-qrcode-anchor-scan-type-change {
                text-decoration: none !important;
                color: #1d9bf0;
            }
            video {
                width: 100% !important;
                border: 1px solid #b2b2b2 !important;
                border-radius: 0.25em;
            }
        </style>

        <div class="container">
            <div class="section">
                <div id="my-qr-reader"></div>
            </div>
        </div>

        <script src="assets/script/html5-qrcode.min.js"></script>
        <script>
            function domReady(fn) {
                if (document.readyState === "complete" || document.readyState === "interactive") {
                    setTimeout(fn, 1000);
                } else {
                    document.addEventListener("DOMContentLoaded", fn);
                }
            }

            domReady(function () {
                // If found you qr code
                function onScanSuccess(decodeText, decodeResult) {
                    alert("Your QR is : " + decodeText, decodeResult);
                }

                let htmlscanner = new Html5QrcodeScanner(
                    "my-qr-reader",
                    { fps: 10, qrbox: 250 }
                );
                htmlscanner.render(onScanSuccess);
            });
        </script>
    </div>
</body>

</html>
