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

$query = "SELECT * FROM vehicle WHERE plate_number = ? AND vehicle_type = '2_wheel'";
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/estylebooks.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">  
    <style>
        body {
            user-select: none;
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
            <?php for ($i = 38; $i <= 49; $i++): ?>
                <?php
                    $is_disabled = isset($reservations[$i]) && in_array($reservations[$i]['status'], ['reserved', 'occupied']);
                    $disabled_class = $is_disabled ? 'disabled' : '';
                ?>
                <div class="box box<?php echo $i; ?> <?php echo $disabled_class; ?>" data-slot="<?php echo $i; ?>">
                    <div class="text">
                        <h2 class="topic-heading">Slot <?php echo $i; ?></h2>
                        <?php if (isset($reservations[$i])): ?>
                            <?php if ($reservations[$i]['plate_number'] == $_SESSION['selected_plate_number']): ?>
                                <h2 class="topic selected-slot">Plate Number: <?php echo htmlspecialchars($reservations[$i]['plate_number']); ?></h2>
                                <?php if ($reservations[$i]['status'] == 'occupied'): ?>
                                    <h2 class="topic selected-slot">Status: Occupied</h2>
                                <?php else: ?>
                                    <h2 class="topic selected-slot">Status: Your Reservation</h2>
                                    <h2 class="topic selected-slot"><span class="timer" data-expiry="<?php echo 300; ?>"></span></h2>
                                <?php endif; ?>
                            <?php else: ?>
                                <h2 class="topic">Status: <?php echo htmlspecialchars($reservations[$i]['status']); ?></h2>
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
            let modal = document.getElementById("confirmationModal");
            let span = document.getElementsByClassName("close")[0];
            let confirmBtn = document.getElementById("confirmBtn");
            let cancelBtn = document.getElementById("cancelBtn");

            let selectedSlot = null;

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
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "reserve_slot.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        let responseText = xhr.responseText;
                        alert(responseText);
                        modal.style.display = "none";
                         
                        let box = document.querySelector('.box' + selectedSlot);
                        //box.querySelector('.topic-heading').nextElementSibling.innerHTML = 'Status: Loading...';
                        let timer = box.querySelector('.timer');
                            if (timer) {
                                timer.setAttribute('data-expiry', 300);
                            }
                        
                        box.querySelector('.fa-car').classList.add('occupied');

                        setTimeout(() => {
                            location.reload();
                        },5000);
                    }
                };
                xhr.send("slot_number=" + selectedSlot + "&plate_number=" + encodeURIComponent("<?php echo $plate_number; ?>"));
            }

            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                  
                }
            }
        });
    </script>

    <script src="assets/script/timer.js"></script>
    
</body>
</html>
        