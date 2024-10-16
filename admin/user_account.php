<?php
session_start();
require('../dbconn.php'); 

if ($_SESSION['type'] == 'Admin') {

    $count_sql = "SELECT COUNT(*) AS total_users FROM users";
    $count_result = $conn->query($count_sql);
    $total_users = 0;

    if ($count_result->num_rows > 0) {
        $count_row = $count_result->fetch_assoc();
        $total_users = $count_row['total_users'];
    }

    $user_query = "SELECT users.id, username, role, contact_number, email, gender, image, restricted,
            GROUP_CONCAT(v.plate_number SEPARATOR ', ') AS plate_number,
            GROUP_CONCAT(v.vehicle_brand SEPARATOR ', ') AS vehicle_brand,
            GROUP_CONCAT(v.vehicle_type SEPARATOR ', ') AS vehicle_type,
            GROUP_CONCAT(v.color SEPARATOR ', ') AS color,
            GROUP_CONCAT(v.amount SEPARATOR ', ') AS amount,
            GROUP_CONCAT(v.paid SEPARATOR ', ') AS paid
            FROM users 
            LEFT JOIN vehicle v ON users.id = v.user_id
            WHERE disabled != '1'
            GROUP BY users.id;
            ";

    $query_stmt = $conn->prepare($user_query);
    $query_stmt->execute();
    $result_stmt = $query_stmt->get_result();
    
    if ($row_result = $result_stmt->fetch_assoc()) {
        $image = $row_result['image'];
        $gender = $row_result['gender'];
        
    }
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Accounts</title>
        <link rel="stylesheet" href="assets/css/user_account.css">
        <link rel="stylesheet" href="assets/css/responsive.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            .btnUpdate {
                cursor: pointer;
            }
            .modal-content3 {
                background-color: #ffffff;
                margin: 10% auto;
                padding: 30px;
                border: 1px solid #ddd;
                width: 90%;
                max-width: 600px;
                border-radius: 12px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                position: relative;
                z-index: 999;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }

            .modal-content3 label {
                display: inline-block;
                white-space: nowrap;
                width: 130px; 
                text-align: right;
                margin-right: 15px;
            }

            .modal-content3 input {
                display: inline-block;
                width: calc(100% - 170px); 
                padding: 8px;
                box-sizing: border-box;
                margin-bottom: 10px;
            }

            .controls {
                position: relative;
                width: calc(100% - 170px); 
                display: inline-block;
            }

            .controls .bx {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
            }

            .btn-update4 {
                background-color: #1a74e2;
                color: white;
                border: none;
                margin-left: 15vh;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 6px;
                width: 100%;
                text-align: center;
                font-size: 16px;
            }

            .btn-update4:hover {
                background-color: #155bb5;
            }



        .card-profile {
            cursor: pointer;
        }
        .img-lst {
            width: 40px;
            border-radius: 50%;
            border: 2px solid #1560bd;
        }
        .non-d {
            border: none;
        }
        .imageJs {
            border: 2px solid #1560bd;
        }
        .modal::-webkit-scrollbar {
            width: 12px;
            }
            .modal::-webkit-scrollbar-track {
            background-color: #f1f1f1;
            border-radius: 10px;
            }
            .modal::-webkit-scrollbar-thumb {
            background-color: #3498db;
            border-radius: 10px;
            border: 3px solid #ffffff;
            }
            .modal::-webkit-scrollbar-thumb:hover {
            background-color: #2980b9;
            }
            .btn-holdS {
                display: flex;
                justify-content: flex-start;
            }
            .btn-restrict {
                margin-right: 10px;
                width: 100px;
                height: 50px;
                border-radius: 4px;
                border: none;
                background-color: orange;
                color: white;
                text-align: center;
                cursor: pointer;
                font-size: 15px;
                transition: background-color 0.3 ease;
            }
            .btn-restrict:hover{
                background: radial-gradient(circle, rgba(255, 200, 100, 1) 0%, rgba(255, 140, 0, 1) 100%);
            }
            .btn-disable {
                margin-right: 10px;
                width: 100px;
                height: 50px;
                border-radius: 4px;
                border: none;
                background: red;
                color: white;
                text-align: center;
                cursor: pointer;
                font-size: 15px;
            }
            .btn-disable:hover{
                background: radial-gradient(circle, rgba(255, 100, 100, 1) 0%, rgba(255, 0, 0, 1) 100%);
            }
            td {
                padding: 5px;
                vertical-align: middle;
                text-align: left;
            }
            tr {
                height: 50px;
                
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
    z-index: 999; 
    }
    .close-button2,
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
    .close-button1:focus {
    color: #333;
    text-decoration: none;
    }
    .close-button:hover,
    .close-button:focus {
    color: #333;
    text-decoration: none;
    }
    .close-button2:hover,
    .close-button2:focus {
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

    /**/

    .btn-decision{
        display: flex;
        justify-content: center;
        align-items: center;
        
    }
    .btn-go {
        width: 100px;
        height: 50px;
        background-color: green;
        border-radius: 10px;
        border: 1px solid lightgreen;
        color: white;
        margin-right:20px;
        cursor: pointer;
        transform: translateY(-10px);
        font-weight: bold;
    }
    .btn-back{
        width: 100px;
        height: 50px;
        background-color: #f8f8f8;
        border-radius: 10px;
        border: 1px solid gray;
        color: #474849;  
        cursor: pointer;
        transform: translateY(-10px);
        font-weight: bold;
    }
    .btn-go:hover{
        background-color: darkgreen;
        transition: all 0.5s ease-in-out;
    }
    .btn-back:hover{
        background-color: #aaa;
        transition: all 0.5s ease-in-out;
    }
    .signup-container {
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    text-align: left;
    color: rgb(56, 53, 53);
}

.signup-container h2 {
    margin-bottom: 20px;
    font-size: 28px;
    color: #333;
}

.file-input-label {
    display: block;
    margin-bottom: 20px;
}

.profile {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.profile label {
    font-weight: bold;
    color: #333;
}

.profile input,
.profile select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    width: 100%;
    box-sizing: border-box;
}

.btn-update3 {
    justify-content: center;
    align-items: center;
    display: flex;
    background-color: #007BFF;
    width: 100px;
}
.btn-update2{
    padding: 10px 200px;
    background-color: #007BFF;
    border: none;
    border-radius: 4px;
    color: white;
    font-size: 16px;    
    cursor: pointer;
}
.btn-update2:hover {
    background-color: #0056b3;
}

.password-container {
    justify-content: center;
    align-items: center;
    display: flex;
}
.btn-update5 {
    width: 100%;
}

.password-change label {
    font-weight: bold;
    color: #333;
}

.password-change input,
.password-change select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    width: 100%;
    box-sizing: border-box;
}
.close-button3{
color: #888;
position: absolute;
top: 10px;
right: 10px;
font-size: 24px;
font-weight: bold;
cursor: pointer;
}
.close-button3:hover{
color: #333;
text-decoration: none;
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
                    </div>
                </nav>
            </div>
            <div class="main">
                <div class="box-container">
                    <div class="box box1">
                        <div class="text">
                            <center><h2 class="topic-heading"><?php echo $total_users; ?></h2></center>
                            <h2 class="topic">User's count</h2>
                        </div>
                        <i class="fas fa-users user-icon" alt="Views"></i>
                    </div>

                    <div class="box box3">
                        <a href="user_inactive.php">
                            <h2 style="font-size: 22px;" class="topic-heading">Inactive</h2>
                            <h2 class="topic">View user's</h2>
                        </a>
                        <i class="fas fa-user add-icon"></i>
                    </div>

                    <div class="box box4">
                        <a href="user_restricted.php">
                            <h2 style="font-size: 22px;" class="topic-heading">Restricted</h2>
                            <h2 class="topic"> View user's</h2>
                        </a>
                        <i class="fas fa-user add-icon" ></i>
                    </div>

                    <div class="box box2">
                        <a href="signup.php">
                            <h2 class="topic-heading">ADD</h2>
                            <h2 class="topic">Create User Account</h2>
                        </a>
                        <i class="fas fa-add add-icon" alt="slot"></i>
                    </div>
                </div>

                <div class="main-body">
                    <div class="report-container">
                        <div class="report-header">
                            <h1 class="recent-Articles">Users Account</h1>
                            <div class="search-box">
                                <input type="text" class="search" id="searchBox" placeholder="Search by ID or username...">
                                <i class="fa fa-search"></i>
                            </div>
                        </div>
                        <br>
                        <div class="report-body">
                            <table class="table" id="tables">
                                <thead>
                                    <tr>
                                        <th class="non-d"></th>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Contact Number</th>
                                        <th>Email</th>
                                        <th>Restricted</th>
                                        <th>Details</th>                                     
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row_result = $result_stmt->fetch_assoc()): ?>
                                        <tr>
                                            <td><img src="../img/<?php echo htmlspecialchars($row_result['image']); ?>" class="img-lst" alt="Picture"></td>
                                            <td><?php echo htmlspecialchars($row_result['id']); ?></td>
                                            <td><?php echo htmlspecialchars($row_result['username']); ?></td>
                                            <td><?php echo htmlspecialchars($row_result['role']); ?></td>
                                            <td><?php echo htmlspecialchars($row_result['contact_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row_result['email']); ?></td>
                                            <td>
                                                <?php if ($row_result['restricted'] == 1): ?>
                                                    <span style="color: red; font-weight: bold;">Restricted</span>
                                                <?php else: ?>
                                                    <span style="color: green;">Active</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn-detail" onclick="openModal(<?php echo $row_result['id']; ?>)">Details</button>
                                            </td>
                                            <td>
                                                <div class="btn-holdS">
                                                    <button class="btn-restrict" onclick="restricOpen(<?php echo $row_result['id'] ;?>)"
                                                    <?php echo $row_result['restricted'] == 1 ? 'disabled style="background-color: gray; cursor: not-allowed;"' : '';?>>Restrict</button>
                                                    <button class="btn-disable" onclick="disableOpen(<?php echo $row_result['id']; ?>)">Disable</button>
                                                </div>
                                            </td>
                                            <div id="restricModal<?php echo $row_result['id'];?>" class="modal" style="display: none;">
                                                <div class="modal-content">
                                                    <span class="close-button1">&times;</span>
                                                    <p>Restrict This user?</p>
                                                    <form action="restrict_user.php" method="POST" style="display: inline-block;">
                                                        <input type="hidden" name="user_id" value="<?php echo $row_result['id']; ?>">
                                                        <div class="btn-decision">
                                                            <button type="submit" class="btn-go">Confirm</button>
                                                            <a href="user_account.php">
                                                                <button type="button" class="btn-back">Cancel</button>
                                                            </a>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <div id="disableModal<?php echo $row_result['id'];?>" class="modal" style="display: none;">
                                                <div class="modal-content">
                                                    <span class="close-button2">&times;</span>
                                                    <p>Disable This user?</p>
                                                    <form action="disable_user.php" method="POST" style="display: inline-block;">
                                                        <input type="hidden" name="user_id" value="<?php echo $row_result['id']; ?>">
                                                        <div class="btn-decision">
                                                            <button type="submit" class="btn-go">Confirm</button>
                                                            <a href="user_account.php">
                                                                <button type="button" class="btn-back">Cancel</button>
                                                            </a>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div id="reservationModal" class="modal">
                        <div class="modal-content">     
                            <span class="close-button">&times;</span>
                            <h2>Profile Details</h2>

                            <h6 class="btnUpdate"><i>Update Profile</i></h6>

                            <!--start ng update-->
                                <div id="updateModal" class="modal">
                                    <div class="modal-content3">
                                    <span class="close-button3">&times;</span>
                                        <form class="update-change" action="licenseUpdate.php" method="POST" enctype="multipart/form-data">  
                                            <input type="hidden" name="user_id" id="userIdField" value="">

                                            <label for="lto_registration"><strong>LTO Registration: </strong></label>
                                            <input type="file" id="lto_registration" name="lto_registration" accept="image/jpg, image/jpeg, image/png" class="file-input-label">
                                        
                                            <label for="license"><b>License Number:</b></label>
                                            <input type="text" id="licenseId" name="license">

                                            <label for="licenseImg"><b>License Image:</b></label>
                                            <input type="file" id="licenseImg" name="license_img" accept="image/*">

                                            <label for="birthDate"><b>Birth Date:</b></label>
                                            <input type="date" id="birthDate" name="birth_date" required>

                                            <label class="confirmPassword" for="confirmPassword"><b>Confirm Password:</b></label>
                                                <div class="controls">
                                                    <input type="password" id="confirmPassword" name="confirmPassword" required class="span8">
                                                    <i class='bx bx-show toggle-password' onclick="togglePassword('confirmPassword', this)"></i>
                                                </div>
                                            <br>    
                                            <input type="submit" name="submitChange" value="Update" class="btn-update4">
                                        </form>
                                    </div>
                                </div>
                            <!--end of ewan-->

                            <div id="modalContent" class="profile-details">
                                <div class="vehicles-list">
                                    
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>


           
  
            </script>
            
            <script src="assets/script/index.js"></script>
            <script src="assets/script/restrict-disable.js"></script>
        </body>
    </html>
    <?php
    } else {
        echo "You do not have access to this page.";
    }
    ?>
