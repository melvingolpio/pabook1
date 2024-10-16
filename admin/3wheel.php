<?php
session_start();
require('../dbconn.php'); 

if (!isset($_SESSION['username']) || $_SESSION['type'] != 'Admin') {
    header("Location: ../login.php"); 
    exit();
}

$reservation_query = "SELECT * FROM reservations";
$reservation_result = $conn->query($reservation_query);

$reservations = [];
while ($row = $reservation_result->fetch_assoc()) {
    $reservations[$row['slot_number']] = array('plate_number' => $row['plate_number'], 'status' => $row['status']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/bookstyle.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">  
</head>
<body>
    <div class="main">
        <h2>Slot Overview</h2>
        <div class="arrow">
            <a href="index.php" class="nav-btn">
                <i class="fas fa-arrow-left nav-img"></i>
            </a>
        </div>
        <div class="box-container">
            <?php for ($i = 26; $i <= 37; $i++): ?>
                <div class="box box<?php echo $i; ?>" data-slot="<?php echo $i; ?>">
                    <div class="text">
                        <h2 class="topic-heading">Slot <?php echo $i; ?></h2>
                        <?php if (isset($reservations[$i])): ?>
                            <h2 class="topic">Plate Number: <?php echo htmlspecialchars($reservations[$i]['plate_number']); ?></h2>
                            <h2 class="topic">Status: <?php echo htmlspecialchars($reservations[$i]['status']); ?></h2>
                        <?php else: ?>
                            <h2 class="topic">Status: Available</h2>
                        <?php endif; ?>
                    </div>
                    <i class="fas fa-car <?php echo isset($reservations[$i]) ? ($reservations[$i]['status'] == 'occupied' ? 'occupied' : 'reserved') : 'available'; ?>" alt="car"></i>
                </div>
            <?php endfor; ?>
        </div>
    </div>

</body>
</html>
