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

$reservation_query = "SELECT * FROM parking_slots";
$reservation_result = $conn->query($reservation_query);

$reservations = [];
while ($row = $reservation_result->fetch_assoc()) {
    $reservations[$row['slot_id']] = array('status' => $row['status']);
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
    <style>
        .box {
            position: relative;
        }
        .cancel-btn {
            background-color: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border: 2px solid #c0392b;
        }
        .cancel-btn:hover {
            background-color: darkred;
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        .cancel-btn:active {
            background-color: maroon;
            transform: scale(0.95);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
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
            <?php for ($i = 1; $i <= 25; $i++): ?>
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
                                        <?php else: ?>
                                            <h2 class="topic selected-slot"></h2>
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

            document.querySelectorAll('.box').forEach(function (box) {
                box.addEventListener('click', function () {
                    document.querySelectorAll('.box').forEach(b => b.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedSlot = this.getAttribute('data-slot');
                    document.getElementById("modalText").innerText = "You want to reserve this slot #" + selectedSlot + "?";
                    modal.style.display = "block";
                });
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
                        var responseText = xhr.responseText;
                        alert(responseText);
                        modal.style.display = "none";
                        location.reload(); // This will trigger the polling again
                    }
                };
                xhr.send("slot_number=" + selectedSlot + "&plate_number=" + encodeURIComponent("<?php echo $plate_number; ?>"));
            }

            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            document.querySelectorAll('.cancel-btn').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.stopPropagation(); 
                    var slot = this.getAttribute('data-slot');
                    if (confirm("Are you sure you want to cancel the reservation for slot #" + slot + "?")) {
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "cancel_reservation.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function () {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                alert("Reservation for slot #" + slot + " has been canceled.");
                                location.reload(); // This will trigger the polling again
                            }
                        };
                        xhr.send("slot_number=" + slot);
                    }
                });
            });

            // Start polling every 5 seconds
            setInterval(fetchParkingStatus, 1000);
        });

        function fetchParkingStatus() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_parking_status.php", true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var slots = JSON.parse(xhr.responseText);
                    updateSlotStatuses(slots);
                }
            };
            xhr.send();
        }

        function updateSlotStatuses(slots) {
            for (var slotNumber in slots) {
                var slotInfo = slots[slotNumber];
                var box = document.querySelector('.box[data-slot="' + slotNumber + '"]');
                if (box) {
                    // Update plate number if exists
                    var plateNumberElem = box.querySelector('.selected-slot');
                    if (slotInfo.plate_number) {
                        plateNumberElem.innerHTML = 'Plate Number: ' + slotInfo.plate_number;
                    } else {
                        plateNumberElem.innerHTML = '';
                    }

                    // Update status
                    var statusElem = box.querySelector('.topic.selected-slot:last-of-type');
                    statusElem.innerHTML = 'Status: ' + slotInfo.status.charAt(0).toUpperCase() + slotInfo.status.slice(1);
                    var carIcon = box.querySelector('.fa-car');
                    
                    // Change icon class based on status
                    if (slotInfo.status === 'occupied') {
                        carIcon.className = 'fas fa-car occupied';
                    } else if (slotInfo.status === 'reserved') {
                        carIcon.className = 'fas fa-car reserved';
                    } else {
                        carIcon.className = 'fas fa-car available';
                    }
                }
            }
        }
    </script>
</body>
</html>
