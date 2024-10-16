<?php
session_start();
require('../dbconn.php'); 

if (!isset($_SESSION['username']) || $_SESSION['type'] != 'User') {
    header("Location: ../login.php"); 
    exit();
}

$plate_number = $_SESSION['selected_plate_number'] ?? '';

if (empty($plate_number)) {
    echo "No vehicle selected.";
    exit();
}

$query = "SELECT * FROM vehicle WHERE plate_number = ? AND (vehicle_type = '4_wheel' OR vehicle_type = '3_wheel' OR vehicle_type = '2_wheel')";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $plate_number);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();

if (!$vehicle) {
    echo "Vehicle not found.";
    exit();
}

$reservation_query = "SELECT * FROM reservations";
$reservation_result = $conn->query($reservation_query);

$reservations = [];
while ($row = $reservation_result->fetch_assoc()) {
    $reservations[$row['slot_number']] = array('plate_number' => $row['plate_number'], 'status' => $row['status']);
}

$user_role = $_SESSION['role'];
$show_timer = !($user_role === 'president' || $user_role === 'vice_president');
$show_vehicle_type = ($user_role === 'president' || $user_role === 'vice_president');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/pree-wheel.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">  
</head>

<style>
body {
    user-select:none;
}
.box {
    position: relative;
}

.cancel-btn {
    background-color: #1a74e2;
    color: white;
    border: none;
    width: 30px;
    height: 30px;
    font-size: 14px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    position: absolute;
    top: 10px;
    right: 10px;
    transition: all 0.3s ease;
}

.cancel-btn:hover {
    font-size: 16px;
}

.cancel-btn:active {
    transform: scale(0.95);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>

<body>
    <div class="main">
        <h2>Vehicle Details</h2>
        <p><strong>Plate Number:</strong> <?php echo htmlspecialchars($vehicle['plate_number']); ?></p>
        <p><strong>Brand:</strong> <?php echo htmlspecialchars($vehicle['vehicle_brand']); ?></p>
        <div class="arrow">
            <a href="booking.php" class="nav-btn">
                <i class="fas fa-arrow-left nav-img"></i>
            </a>
        </div>
        <div class="box-container">
            <?php for ($i = 1; $i <= 2; $i++): ?>
                <?php 
                    $is_disabled = isset($reservations[$i]) && in_array($reservations[$i]['status'], ['reserved', 'occupied']);
                    $disables_class = $is_disabled ? 'disabled' : '';
                ?>
                <div class="box box<?php echo $i; ?> <?php echo $disables_class; ?>" data-slot="<?php echo $i; ?>">
                    <div class="text">
                        <h2 class="topic-heading">Slot <?php echo $i; ?></h2>
                        <?php if (isset($reservations[$i])): ?>
                            <h2 class="topic selected-slot">Plate Number: <?php echo htmlspecialchars($reservations[$i]['plate_number']); ?></h2>
                            <?php if ($reservations[$i]['plate_number'] == $_SESSION['selected_plate_number']): ?>
                                <?php if ($reservations[$i]['status'] == 'occupied'): ?>
                                    <h2 class="topic selected-slot">Status: Occupied</h2>
                                <?php else: ?>
                                    <?php if ($show_timer): ?>
                                        <h2 class="topic selected-slot"><span class="timer" data-expiry="<?php echo 300; ?>"></span></h2>
                                    <?php else: ?>
                                        <?php if ($show_vehicle_type): ?>
                                            <h2 class="topic selected-slot">Vehicle type: <?php echo htmlspecialchars($vehicle['vehicle_type']); ?></h2>
                                            <?php if ($user_role === 'president' || $user_role === 'vice_president'): ?>
                                                <button class="cancel-btn" data-slot="<?php echo $i; ?>">X</button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <h2 class="topic selected-slot">Status: <?php echo htmlspecialchars($reservations[$i]['status']); ?></h2>
                            <?php endif; ?>
                        <?php else: ?>
                            <h2 class="topic">Status: Available</h2>
                        <?php endif; ?>
                    </div>
                    <i class="fas fa-car <?php echo isset($reservations[$i]) ? ($reservations[$i]['status'] == 'occupied' ? 'occupied' : 'reserved') : 'available'; ?>" alt="car"></i>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div id="confirmationModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="modalText">You want to reserve this slot #?</p>
            <button id="confirmBtn">Confirm</button>
            <button id="cancelBtn">Cancel</button>
        </div>
    </div> 

    <script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById("confirmationModal");
    var span = document.getElementsByClassName("close")[0];
    var confirmBtn = document.getElementById("confirmBtn");
    var cancelBtn = document.getElementById("cancelBtn");
    var selectedSlot = null;

    // Function to fetch and update slot statuses every 1 second
    function fetchSlotStatuses() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "get_slots_status.php?t=" + new Date().getTime(), true);  // Cache-busting
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var slotsStatus = JSON.parse(xhr.responseText);
                slotsStatus.forEach(function (slot) {
                    var slotElement = document.querySelector('.box[data-slot="' + slot.slot_number + '"]');
                    var statusText = slotElement.querySelector('.topic');
                    var carIcon = slotElement.querySelector('.fa-car');

                    // Update the status text and icon
                    if (slot.status === 'available') {
                        statusText.innerHTML = 'Status: Available';
                        carIcon.classList.remove('reserved', 'occupied');
                        carIcon.classList.add('available');
                    } else if (slot.status === 'reserved') {
                        statusText.innerHTML = 'Status: Reserved';
                        carIcon.classList.remove('available', 'occupied');
                        carIcon.classList.add('reserved');
                    } else if (slot.status === 'occupied') {
                        statusText.innerHTML = 'Status: Occupied';
                        carIcon.classList.remove('available', 'reserved');
                        carIcon.classList.add('occupied');
                    }
                });
            }
        };
        xhr.send();
    }

    // Polling the status every 1 second
    setInterval(fetchSlotStatuses, 1000);

    // Existing modal and slot reservation logic
    document.querySelectorAll('.box').forEach(function (box) {
        if (!box.classList.contains('disabled')) { 
            box.addEventListener('click', function () {
                selectedSlot = this.getAttribute('data-slot');
                document.getElementById("modalText").innerText = "You want to reserve this slot #" + selectedSlot + "?";
                modal.style.display = "block";
            });
        }
    });

    span.onclick = function () {
        modal.style.display = "none";
    }

    cancelBtn.onclick = function () {
        modal.style.display = "none";
    }

confirmBtn.onclick = function () {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "reserve_slot.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText);
            fetchSlotStatuses();  // Update the slots after reservation
            modal.style.display = "none";
        }
    };
    xhr.send("slot=" + selectedSlot);
}


    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
    </script>
</body>
</html>
