<?php
session_start();
require('../dbconn.php');

if ($_SESSION['type'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

$query = "SELECT 
          v.user_id, 
          v.plate_number, 
          v.vehicle_type,
          v.amount, 
          r.receipt_token,
          u.role
          FROM vehicle v
          LEFT JOIN receipts r ON v.plate_number = r.plate_number
          LEFT JOIN users u ON v.user_id = u.id
          ORDER BY r.id ASC";

$result = $conn->query($query);

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verification</title>
    <link rel="stylesheet" href="assets/css/paymentstyle.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .report-container {
            overflow-x: auto; /
            max-width: 100%;
        }

        .table-wrapper {
            overflow-x: auto; 
        }

        .table {
            width: 100%; 
            min-width: 800px; 
            border-collapse: collapse; 
        }

        .table th, .table td {
            padding: 8px; 
            text-align: left; 
            overflow: hidden; 
            text-overflow: ellipsis; 
            white-space: nowrap; 
            border: 1px solid #ddd; 
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
@media (max-width: 850px) {
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
            <p><?php echo $_SESSION['username']; ?>!</p>
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
                <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt nav-img" alt="dashboard"></i>
                    <a href="index.php" class="nav-link"><h3>Dashboard</h3></a>
                </div>
                <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'user_account.php') ? 'active' : ''; ?>">
                    <i class="fas fa-user nav-img" alt="account's"></i>
                    <a href="user_account.php" class="nav-link"><h3>Account's</h3></a>
                </div>
                <!--<div class="nav-option ?php echo (basename($_SERVER['PHP_SELF']) == 'qr-scanner.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-qrcode nav-img" alt="scanner"></i>
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
                <h1 class="recent-Articles">Payment Verification</h1>
                <div class="search-box">
                    <input type="text" class="search" id="searchBox" placeholder="Search by ID or username...">
                    <i class="fa fa-search search-icon"></i>
                </div>
            </div>
            <br>
            <div class="report-body-2">
                <div class="table-wrapper">
                    <table class="table" id="tables">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Plate Number</th>
                                <th>Role</th>
                                <th>Car Type</th>
                                <th>Paid</th>
                                <th>Token</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="userTable">
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['plate_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['role']); ?></td>              
                                <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                <td><?php echo htmlspecialchars($row['receipt_token']); ?></td>
                                <td>
                                    <center>
                                        <form action="send_receipt.php" method="POST" onsubmit="return confirmSend(event)">
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
                                            <input type="hidden" name="plate_number" value="<?php echo htmlspecialchars($row['plate_number']); ?>">
                                            <button class="btn-send" type="submit">Send</button>
                                        </form>
                                    </center>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalOverlay"></div>
<div class="center-message" id="centerMessage">
    <p>QR code Processing...</p>
</div>

<script>
function confirmSend(event) {
    event.preventDefault();

    if (confirm("Would you like to send the QR code?")) {
        setTimeout(function() {
            document.getElementById('centerMessage').classList.add('show');
            document.getElementById('modalOverlay').classList.add('show');

            setTimeout(function() {
                document.getElementById('centerMessage').classList.remove('show');
                document.getElementById('modalOverlay').classList.remove('show');

                event.target.submit();
            }, 3000);
        }, 1000);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.getElementById('searchBox');
    const rows = document.querySelectorAll('#userTable tr');

    searchBox.addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();

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
