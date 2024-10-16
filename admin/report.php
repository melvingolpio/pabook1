<?php
session_start();
require('../dbconn.php'); 

if ($_SESSION['type'] != 'Admin') {
    header("Location: ../login.php"); 
    exit();
}

$user_id = $_SESSION['id'];

$sql = "SELECT id, image FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $image = $row['image'];
}

$currentYear = date("Y");

// Number of Cars Parked
$carsQuery = "SELECT COUNT(*) AS totalCars FROM activities WHERE YEAR(date) = '$currentYear'";
$carsResult = $conn->query($carsQuery);
$carsData = $carsResult->fetch_assoc();
$totalCars = $carsData['totalCars'];

// Total QR Codes Provided
$qrQuery = "SELECT COUNT(*) AS totalQR FROM receipts WHERE YEAR(created_at) = '$currentYear'";
$qrResult = $conn->query($qrQuery);
$qrData = $qrResult->fetch_assoc();
$totalQR = $qrData['totalQR'];

// Total income this year
$amountQuery = "SELECT SUM(amount) AS totalamount FROM vehicle WHERE YEAR(paid_at) = '$currentYear'";
$amountResult = $conn->query($amountQuery);
$amountData = $amountResult->fetch_assoc();
$totalamount = $amountData['totalamount'] ?: 0;


$monthlyProfit = [];
for ($month = 1; $month <= 12; $month++) {
    $amountQuery = "SELECT SUM(amount) AS monthlyProfit FROM vehicle WHERE YEAR(paid_at) = '$currentYear' AND MONTH(paid_at) = '$month'";
    $amountResult = $conn->query($amountQuery);
    $amountData = $amountResult->fetch_assoc();
    $monthlyProfit[] = $amountData['monthlyProfit'] ?: 0;
}

$slotQuery = "SELECT slot_id, COUNT(*) AS usageCount FROM reservations WHERE YEAR(reservation_date) = '$currentYear' GROUP BY slot_id ORDER BY usageCount DESC LIMIT 1";
$slotResult = $conn->query($slotQuery);

if ($slotResult && $slotResult->num_rows > 0) {
    $slotData = $slotResult->fetch_assoc();
    $mostUsedSlot = $slotData['slot_id'];
} else {
    $mostUsedSlot = "No data available"; // Default value if no data
}

// Query to get the number of cars parked per slot
$carSlotsData = [];
$carSlotsQuery = "SELECT slot_id, COUNT(*) as cars_parked FROM reservations WHERE YEAR(reservation_date) = '$currentYear' GROUP BY slot_id";
$carSlotsResult = $conn->query($carSlotsQuery);

while ($row = $carSlotsResult->fetch_assoc()) {
    $carSlotsData['labels'][] = "Slot " . $row['slot_id'];
    $carSlotsData['data'][] = $row['cars_parked'];
}

// Number of User Accounts Created This Year
$usersQuery = "SELECT COUNT(*) AS totalusers FROM users WHERE YEAR(account_created_at) = '$currentYear'";
$usersResult = $conn->query($usersQuery);
$usersData = $usersResult->fetch_assoc();
$totalusers = $usersData['totalusers'];


// Number of registered vehicle This Year
$vehicleQuery = "SELECT COUNT(*) AS totalvehicle FROM vehicle WHERE YEAR(created_at) = '$currentYear'";
$vehicleResult = $conn->query($vehicleQuery);
$vehicleData = $vehicleResult->fetch_assoc();
$totalvehicle = $vehicleData['totalvehicle'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <link rel="stylesheet" href="assets/css/indstyle.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
    <style>

    .sub-header{
        text-align: center;
        color: white;
        margin-top: 20px;
    }


    .report-container{
        width: 100%;
        height: 100px;
        background-color: #209ae7;
        

    }

    .report-cards{
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        padding-left: 30px;
      
       
    }

    .report-card1{
        height: 50%;
        width: 100%;
        margin-right: 50px;
        margin-left: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);
        background-color: white;

       
    }

    .report-card2{
        height: 50%;
        width: 100%;
        margin-right: 50px;
        margin-left: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);
        background-color: white;
       
    }

    h5{
        margin-top: 20px;
    }

    .para1{
        font-size: 2em;
        margin-top: 35px;
        font-weight: bold;

    }
    .para2{
        font-size: 2em;
        margin-top: 20px;
        font-weight: bold;

    }
    .para3{
        font-size: 2em;
        margin-top: 35px;
        font-weight: bold;

    }
    .para4{
        font-size: 2em;
        margin-top: 20px;
        font-weight: bold;

    }
    .para5{
        font-size: 2em;
        margin-top: 20px;
        font-weight: bold;

    }
    .para6{
        font-size: 2em;
        margin-top: 35px;
        font-weight: bold;

    }
    .boxs-1{
        display: flex;
        flex-direction: column;
        
    }

    .sec{
        height: auto;   
        width: 100%;
        background-color: white;
    }

    hr{
        height: 30px;
        border: none;
        background-color: #f5f5f6;
    }

    .chart1{
        height: 500px;
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .chart2{
        height: 500px;
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .excel-btn{
        width: 100px;
        height: 45px;
        display: flex;
        justify-content: center ;
        border: none;
        color: white;
        background-color: #209ae7;
        text-align: center;
        padding-top: 7px;
        border-radius: 3px 3px 3px 3px;
        font-size: 1.0em;

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
                <img src="../img/<?php echo htmlspecialchars($image);?>" class="dpicn" alt="Picture">
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

            <div class="boxs-body">
                <div class="boxs-1">
                    <div class="report-container">
                        <h1 class="sub-header">Report for <?php echo $currentYear; ?></h1>

                        <div class="report-cards">
                            <div class="report-card1">
                                <h5>Total Cars Parked</h5>
                                <p class ="para1"><?php echo $totalCars; ?></p>
                            </div>
                            <div class="report-card1">
                                <h5>Total QR Codes Provided</h5>
                                <p class ="para2"><?php echo $totalQR; ?></p>
                            </div>
                            <div class="report-card1">
                                <h5>Total Profit</h5>
                                <p class ="para3"><a href="../process_payment.php"><?php echo $totalamount; ?></a></p>
                            </div>
                        
                            <div class="report-card2">
                                <h5 >Most Used Parking Slot</h5> 
                                <p class="para4">Slot <?php echo $mostUsedSlot; ?></p>
                            
                            </div>
                            <div class="report-card2">
                                <h5>User Accounts Created</h5>
                                <p class ="para5"><?php echo $totalusers; ?></p>
                            </div>
                            <div class="report-card2">
                                <h5>Registered Vehicle</h5>
                                <p class ="para6"><?php echo $totalvehicle; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
           </div>

            <hr>
        
            <section class="sec" id="">

            <div class="chart1">
                    <canvas id="profitChart"></canvas>
            </div>
            </section>

            <hr>

            <section class="sec" id="">
            <div class="chart2">
                 <canvas id="carChart"></canvas>
            </div>
            </section><br><br>

            <button class="excel-btn" id="exportBtn">Download Report</button><br><br>
        </div>

    </div>

    



    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Get the canvas contexts
            let profitCtx = document.getElementById('profitChart').getContext('2d');
            let carCtx = document.getElementById('carChart').getContext('2d');

            // PHP data passed to JavaScript
            let profitData = <?php echo json_encode($monthlyProfit); ?>;
            let carSlotsData = <?php echo json_encode($carSlotsData); ?>;

            let profitChart = new Chart(profitCtx, {
                type: 'bar',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    datasets: [{
                        label: 'Total Profit',
                        data: profitData,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Car Parked Chart
            var carChart = new Chart(carCtx, {
                type: 'pie',
                data: {
                    labels: carSlotsData.labels,
                    datasets: [{
                        label: 'Cars Parked',
                        data: carSlotsData.data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });
        });
    </script>

    <script>
        document.getElementById('exportBtn').addEventListener('click', function () {
            // Collect data from the report
            let totalCars = "<?php echo $totalCars; ?>";
            let totalQR = "<?php echo $totalQR; ?>";
            let totalAmount = "<?php echo $totalamount; ?>";
            let mostUsedSlot = "<?php echo $mostUsedSlot; ?>";
            let totalUsers = "<?php echo $totalusers; ?>";
            let totalVehicle = "<?php echo $totalvehicle; ?>";
            
            // Create an array of arrays (rows) for the Excel file
            let data = [
                ["Report", "<?php echo $currentYear; ?>"], // Title row
                [],
                ["Metric", "Value"], // Header row
                ["Total Cars Parked", totalCars],
                ["Total QR Codes Provided", totalQR],
                ["Total Profit", totalAmount],
                ["Most Used Parking Slot", "Slot " + mostUsedSlot],
                ["User Accounts Created", totalUsers],
                ["Registered Vehicle", totalVehicle],
                [],
                ["Monthly Profit"], // Section for monthly profit
                ["Month", "Profit"],
                ["January", "<?php echo $monthlyProfit[0]; ?>"],
                ["February", "<?php echo $monthlyProfit[1]; ?>"],
                ["March", "<?php echo $monthlyProfit[2]; ?>"],
                ["April", "<?php echo $monthlyProfit[3]; ?>"],
                ["May", "<?php echo $monthlyProfit[4]; ?>"],
                ["June", "<?php echo $monthlyProfit[5]; ?>"],
                ["July", "<?php echo $monthlyProfit[6]; ?>"],
                ["August", "<?php echo $monthlyProfit[7]; ?>"],
                ["September", "<?php echo $monthlyProfit[8]; ?>"],
                ["October", "<?php echo $monthlyProfit[9]; ?>"],
                ["November", "<?php echo $monthlyProfit[10]; ?>"],
                ["December", "<?php echo $monthlyProfit[11]; ?>"]
            ];

            // Create a new worksheet
            let worksheet = XLSX.utils.aoa_to_sheet(data);
            
            // Create a new workbook
            let workbook = XLSX.utils.book_new();
            
            // Append the worksheet to the workbook
            XLSX.utils.book_append_sheet(workbook, worksheet, "Report");

            // Export the workbook to Excel
            XLSX.writeFile(workbook, "Report_<?php echo $currentYear; ?>.xlsx");
        });
    </script>
<script src="assets/script/index.js"></script>  
</body>
</html>
