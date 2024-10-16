<?php 
session_start();
require('../dbconn.php');

if ($_SESSION['type'] != 'Admin') {
    header('Location: ../login.php');
    exit();
}

$query = "SELECT 
          r.id,
          r.user_id,
          r.plate_number, 
          r.receipt_token, 
          r.expiration_date,
          r.created_at,
          u.username,
          u.role
          FROM receipts r
          JOIN users u
          ON r.user_id = u.id"; 
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$user_id = $_SESSION['id'];

$picture_query = "SELECT image FROM users WHERE id = ?";
$picture_stmt = $conn->prepare($picture_query);
$picture_stmt->bind_param('i', $user_id);
$picture_stmt->execute();
$picture_result = $picture_stmt->get_result();

if ($row = $picture_result->fetch_assoc()) {
    $image = $row['image'];
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Renew Verification</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/paymentstyle.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
    .logo {
    font-variant: small-caps;
    font-weight: bold;
    color: white;
    text-shadow: 2px 4px 6px rgb(0, 0, 0);
}
.menuicn {
   opacity: 0;
} 
@media (max-width: 850px) {
    .menuicn {
        opacity: 1; 
    }
}
</style>
<body>
    <header>
        <div class="picture">
            <div class="dp">
                <img src="../img/<?php echo htmlspecialchars($image); ?>" class="dpicn" alt="Profile Picture">
            </div>
            <div class="name">
                <p><?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            </div>
        </div>
        <div class="logosec">
            <div class="logo">Pabook</div>
            <i class="fas fa-bars icn menuicn" id="menuicn"></i>
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
        <div class="main">
            <div class="report-container">
                
                <div class="report-header">
                <div class="arrow">
            <a href="transaction.php" class="nav-btn">
                <i class="fas fa-arrow-left nav-img"></i>
            </a>
        </div>
                    <h1 class="recent-Articles">Renew Verification</h1>
                    <div class="search-box">  
                        <input type="text" class="search" id="searchBox" placeholder="Search by ID or username...">
                        <i class="fa fa-search"></i>
                        </input>
                    </div>
                </div>
                <br>
                <div class="report-body">
                    <?php
                    if (isset($_SESSION['success_message'])) {
                        echo '<p class="success-message">' . $_SESSION['success_message'] . '</p>';
                        unset($_SESSION['success_message']);
                    }
                    if (isset($_SESSION['error_message'])) {
                        echo '<p class="error-message">' . $_SESSION['error_message'] . '</p>';
                        unset($_SESSION['error_message']);
                    }
                    ?>
                    
                    <table class="table" id="tables">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Plate Number</th>
                                <th>Token</th>
                                <th>Created</th>
                                <th>Expiration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTable">
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                                    <td><?php echo htmlspecialchars($row['plate_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['receipt_token']); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($row['expiration_date']); ?></td>
                                    <td>
                                        <center>
                                            <form onsubmit="confirmSend(event, <?php echo htmlspecialchars($row['id']); ?>)">
                                                <input type="hidden" name="receipt_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                <button class="btn-send" type="submit">Renew</button>
                                            </form>
                                        </center>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="center-message" id="centerMessage">
        <p>Renew Processing...</p>
    </div>
    <script>
    function confirmSend(event, receiptId) {
        event.preventDefault();

        if (confirm("Would you like to renew this receipt?")) {
            setTimeout(function() {
                document.getElementById('centerMessage').classList.add('show');
                document.getElementById('modalOverlay').classList.add('show');

                setTimeout(function() {
                   
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'renew.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            
                            document.getElementById('centerMessage').classList.remove('show');
                            document.getElementById('modalOverlay').classList.remove('show');
                            alert('Receipt renewed successfully!');
                            location.reload();
                        } else if (xhr.readyState === 4) {
                           
                            document.getElementById('centerMessage').classList.remove('show');
                            document.getElementById('modalOverlay').classList.remove('show');
                            alert('Failed to renew receipt.');
                        }
                    };
                    xhr.send('receipt_id=' + receiptId);
                }, 3000);
            }, 1000);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchBox = document.getElementById('searchBox');
        searchBox.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#userTable tr');
            rows.forEach(row => {
                let id = parseInt(row.cells[0].textContent);
                let username = row.cells[1].textContent.toLowerCase();
                if (id.toString().includes(filter) || username.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    </script>
    <script src="assets/script/index.js"></script>
</body>
</html>
