    <?php
    session_start();
    require('../dbconn.php');

    $shutdown = file_get_contents('shutdown_status.txt');

    if ($_SESSION['type'] == 'Admin') {

        $sql = "SELECT COUNT(*) AS total_users FROM users";
        $result = $conn->query($sql);
        $total_users = 0;
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $total_users = $row['total_users'];
        }

        
        if (isset($_SESSION['username'])) {
            $user_id = $_SESSION['id'];

            $query = "SELECT id, username, birth_date, gender, contact_number, type, image FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                $id = $user['id'];
                $username = $user['username'];
                $birth_date = $user['birth_date'];
                $gender = $user['gender'];
                $contact_number = $user['contact_number'];
                $type = $user['type'];
                $image = $user['image'];
            }

            $act_query = "SELECT plate_number, slot_id, date, time_in, time_out FROM activities";
            $act_stmt = $conn->prepare($act_query);
            $act_stmt->execute();
            $act_result = $act_stmt->get_result();

            

            $booking_query = "SELECT plate_number, slot_id, reservation_date, status FROM reservations";
            $booking_stmt = $conn->prepare($booking_query);
            $booking_stmt->execute();
            $booking_result = $booking_stmt->get_result();
            $booking_activities = $booking_result->fetch_all(MYSQLI_ASSOC);
        }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <link rel="stylesheet" href="assets/css/indistylee.css">
        <link rel="stylesheet" href="assets/css/responsive.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .btn-shutdown {
        width: 250px;
        height: 60px;
        display: flex;
        align-items: center;
        margin-right: 10px;
        padding: 0 30px 0 20px;
        gap: 20px;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    
    .btn-shutdown:hover {
        transform: translateY(-2px);
    }
    .btn-shutdown button{
    background-color: transparent;
    border: none;
    cursor: pointer;
    }
    .btn-shutdown h3{
        font-size: 20px;
        margin-left: 10px;
    }
    .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
    background-color: #ffffff;
    margin: 10% auto;
    padding: 30px;
    border: 1px solid #ddd;
    width: 90%;
    max-width: 600px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: relative;
    }
    .close-button1,
    .close-button {
    color: #888;
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    }
    .close-button1:hover,
    .close-button:focus {
    color: #333;
    text-decoration: none;
    }
    .close-button:hover,
    .close-button:focus {
    color: #333;
    text-decoration: none;
    }

    .modal-content h2 {
    margin-top: 0;
    font-size: 1.5rem;
    color: #333;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
    }

    .modal-content p {
    font-size: 1rem;
    color: #666;
    margin: 10px 0;
    display: flex;
    justify-content: space-between;
    }

    .modal-content p strong {
    flex-basis: 30%;
    text-align: left;
    }

    .modal-content p span {
    flex-basis: 70%;
    text-align: right;
    }
    .close { 
    color: #aaa; 
    float: right; 
    font-size: 28px; 
    z-index: 999;
    font-weight: bold; 
    }
    .close:hover, .close:focus { 
    color: black; 
    text-decoration: none; 
    cursor: pointer; 
    }
    .btn-shutdown {
    width: 250px;
    height: 50px;
    display: flex;
    align-items: center;
    margin-right: 10px;
    padding: 30px 20px 20px 0;
    gap: 20px;
    transition: all 0.2s ease-in-out;
    }
    .btn-shutdown h5{
        font-size: 15px;
    }
    .btn-confirmation-shutdown {
    width: 100%;
    height: 50px;
    display: flex;
    align-items: center;
    background-color: red;
    color: white;
    gap: 2px;
    padding-left: 20px;
    transition: all 0.2s ease-in-out;
    }
    .btn-confirmation-shutdown p{
        display: inline;
        font-size: 100px;
        z-index: 999;
    background-color: black;
    }
   
    .btn-live {
    width: 100%;
    height: 50px;
    display: flex;
    align-items: center;
    background-color: green;
    color: white;
    padding-left: 65px;
    gap: 20px;
    transition: all 0.2s ease-in-out;
    }
    .btn-live p{
        display:inline;
        font-size: 15px;
    }
    .btn-live h5{
        font-size: 15px;
    }
    
    button {
    font-family: 'Arial', sans-serif;
    font-size: 16px;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 12px 24px;
    cursor: pointer;
    transition: background-color 0.3s, box-shadow 0.3s;
}

button[name="action"][value="On"] {
    background-color: #4caf50;
}

button[name="action"][value="On"]:hover {
    background-color: #45a049;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

button[name="action"][value="shutDown"] {
    background-color: #f44336;
}

button[name="action"][value="shutDown"]:hover {
    background-color: #e53935;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

button + button {
    margin-left: 10px;
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
                    <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                </div>
            </div>
            <div class="three-container">
                <input class="radio" type="radio" name="section" onclick="scrollToSection('boxContainer')">
                <input class="radio" type="radio" name="section" onclick="scrollToSection('usersAct')">
                <input class="radio" type="radio" name="section" onclick="scrollToSection('bookHistory')">  
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
                            <i class="fas fa-camera-retro nav-img" alt="scanner"></i>
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
                        
                        <div class="btn-shutdown">   
                               
                            <?php if ($shutdown === '1'): ?>                      
                                    <span class="btn-confirmation-shutdown"><i class="fas fa-power-off"><p>&nbsp;&nbsp;SHUTDOWNghhthththt</p></i> </span>
                            <?php else: ?>
                                <span class="btn-live"><i class="fa-solid fa-door-open"><p>&nbsp;&nbsp;Live</p></i></span>   
                            <?php endif; ?>
                        </div>
                            
                        
                        <div id="shutDownModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <span class="close-button">&times;</span>
                                    <form action="shutdown.php" method="POST">
                                        <center>
                                            <button type="submit" name="action" value="On">On</button>
                                            <button type="submit" name="action" value="shutDown">Shut Down</button>
                                        <center>
                                    </form>
                            </div>
                        </div> 

                    </div>
                </nav>
            </div>
            <div class="main">
                <div class="box-container" id="boxContainer">

                <div class="box-box box1">
                        <div class="text">
                            <a href="4wheel.php">
                            <h2 class="topic-heading">4 Wheel</h2>
                            <h2 class="topic">Parking Space</h2>
                            </a>
                        </div>

                            <i class="fas fa-parking"></i>
                        </div>

                        <div class="box-box box2">
                        <div class="text">
                            <a href="3wheel.php">
                            <h2 class="topic-heading">3 Wheel</h2>
                            <h2 class="topic">Parking Space</h2>
                            </a>
                        </div>
                            <i class="fas fa-parking"></i>
                        </div>
                        
                        <div class="box-box box3">
                        <div class="text">
                            <a href="2wheel.php">
                            <h2 class="topic-heading">2 Wheel</h2>
                            <h2 class="topic">Parking Space</h2>
                            </a>
                        </div>
                            <i class="fas fa-parking"></i>
                        </div>  

                    </div>

                <div class="report-container" id="usersAct">
                    <div class="report-header">
                        <h1 class="recent-Articles">Users Activities</h1>
                        <div style="position: relative;">
                            <input type="text" id="searchBox" class="search-box" placeholder="Search date">
                            <span class="fa fa-search search-icon"></span>
                        </div>
                    </div>
                    <br>
                    <div class="report-body">
                        <table class="table" id="activitiesTable">
                            <thead>
                                <tr>
                                    <th>Plate Number</th>
                                    <th>Slot ID</th>
                                    <th>Date</th>
                                    <th>Time-In</th>
                                    <th>Time-Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $act_result->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['plate_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['slot_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['time_in']); ?></td>
                                        <td><?php echo htmlspecialchars($row['time_out']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="report-container" id="bookHistory">
                    <div class="report-header">
                        <h1 class="recent-Articles">Booking History</h1>
                        <div style="position: relative;">
                            <input type="text" id="searchBox2" class="search-box" placeholder="Search date">
                            <span class="fa fa-search search-icon"></span>
                        </div>
                    </div>
                    <br>
                    <div class="report-body">
                        <table class="table" id="reservationsTable">
                            <thead>
                                <tr>
                                    <th>Plate Number</th>
                                    <th>Slot ID</th>
                                    <th>Reservation Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($booking_activities as $booking) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking['plate_number']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['slot_id']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['reservation_date']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>

        <script>
            function scrollToSection(sectionId) {
                document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth' });
            }

            document.addEventListener('DOMContentLoaded', function(){
                const modal = document.getElementById('shutDownModal');
                const shutDwn = document.querySelector('.btn-shutdown');    
                const closeButton = document.querySelector('.close-button');

                
                shutDwn.addEventListener('click', function() {
                    modal.style.display = 'block';
                });

                closeButton.addEventListener('click', function() {
                    modal.style.display = 'none';
                });

                window.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });
        </script>

<script src="assets/script/index.js"></script>
<script src="assets/script/search.js"></script>

    </body>
    </html>

    <?php
    } else {
        header('Location: ../index.php');
        exit();
    }
    ?>
