<?php
session_start();
require('../dbconn.php'); 

if (!isset($_SESSION['username']) || $_SESSION['type'] != 'Admin') {
    header("Location: ../login.php"); 
    exit();
}

// Query the status of the 6 parking slots
$reservation_query = "SELECT * FROM parking_slots";
$reservation_result = $conn->query($reservation_query);

$reservations = [];
while ($row = $reservation_result->fetch_assoc()) {
    $reservations[$row['slot_id']] = array('status' => $row['status']);
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
    <style>
        .available { color: green; }
        .reserved { color: yellow; }
        .occupied { color: red; }
    </style>
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
            <?php for ($i = 1; $i <= 25; $i++): ?>
                <div class="box box<?php echo $i; ?>" data-slot="<?php echo $i; ?>">
                    <div class="text">
                        <h2 class="topic-heading">Slot <?php echo $i; ?></h2>
                        <h2 class="topic" id="status-<?php echo $i; ?>">Status: <?php echo isset($reservations[$i]) ? htmlspecialchars($reservations[$i]['status']) : 'Available'; ?></h2>
                    </div>
                    <i class="fas fa-car <?php echo isset($reservations[$i]) ? ($reservations[$i]['status'] == 'occupied' ? 'occupied' : ($reservations[$i]['status'] == 'reserved' ? 'reserved' : 'available')) : 'available'; ?>" alt="car"></i>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <script src="assets/script/index.js"></script>
    <script>
    function updateSlotStatus() {
        // Use AJAX to fetch the latest status
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "get_parking_status.php", true);
        xhr.onload = function () {
            if (xhr.status == 200) {
                console.log("Server response:", xhr.responseText);  // Log the raw response
                
                try {
                    var data = JSON.parse(xhr.responseText);  // Try parsing JSON
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    return;
                }
                
                // Update slot icons and status text based on the new data
                for (var i = 1; i <= 6; i++) {
                    var slotElement = document.querySelector(".box" + i + " .fa-car");
                    var statusTextElement = document.getElementById("status-" + i);
                    
                    if (data[i] && data[i].status === "occupied") {
                        slotElement.className = "fas fa-car occupied";
                        statusTextElement.innerHTML = "Status: occupied";
                    } else if (data[i] && data[i].status === "reserved") {
                        slotElement.className = "fas fa-car reserved";
                        statusTextElement.innerHTML = "Status: reserved";
                    } else {
                        slotElement.className = "fas fa-car available";
                        statusTextElement.innerHTML = "Status: available";
                    }
                }
            } else {
                console.error("Error fetching data from server:", xhr.status);
            }
        };
        xhr.onerror = function () {
            console.error("Network error occurred.");
        };
        xhr.send();
    }

    setInterval(updateSlotStatus, 0);  // Poll every 5 seconds
    </script>
</body>
</html>
