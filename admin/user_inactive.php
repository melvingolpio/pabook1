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

    $user_query = "SELECT id, username, role, contact_number, email, image FROM users WHERE disabled = 1";
             $query_stmt = $conn->prepare($user_query);
             $query_stmt->execute();
             $result_stmt = $query_stmt->get_result();
             

    $image_query = "SELECT image FROM users WHERE id = 5";
    $image_stmt = $conn->prepare($image_query);
    $image_stmt->execute();
    $image_result = $image_stmt->get_result();

    if ($image_row = $image_result->fetch_assoc()) {
        $admin_image = $image_row['image'];
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
        .report-container {
        width: 97%;
       }
       .btn-activate{
        width: 100px;
        height: 50px;
        border-radius: 4px;
        border: none;
        background: radial-gradient(circle, #1560bd, #114ff8);
        color: white;
        text-align: center;
        cursor: pointer;
        font-size: 15px;
       }
       .btn-activate    :hover{
        background: radial-gradient(circle, #0a71ee, #071ff5);
       }
       /* */
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
    .report-container{
        margin-top: 50vh;
    }
    .report-body{
        padding-bottom: 50vh;
    }
    .table{
        padding-bottom: 50vh;
    }
    .main-body {
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden; 
    width: 80%; 
    }
    .main-body ::-webkit-scrollbar {
    display: none; 
    }
    .main-container {
        height: 80vh; 
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
                <img src="../img/<?php echo htmlspecialchars($admin_image); ?>" class="dpicn" alt="Picture">
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
       
            <div class="main-body">
                <div class="report-container">
                    <div class="report-header">
                    <a href="user_account.php" class="nav-btn">
                        <i class="fas fa-arrow-left nav-img"></i>
                    </a>
                        <h1 class="recent-Articles">Disable Users</h1>
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
                                    <th>Actions</th>
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
                                            <div class="btn-holdS">
                                                <button class="btn-activate" onclick="openUnDisable(<?php echo $row_result['id']; ?>)">Activate</button>
                                            </div>
                                        </td>
                                        <div id="UndisableModal" class="modal">
                                            <div class="modal-content">
                                                <span class="close-button">&times;</span>
                                                <p>Activite this user?</p>
                                                <form action="undo_inactive.php" method="POST" style="display: inline-block;">
                                                    <input type="hidden" name="user_id" value="<?php echo $row_result['id']; ?>">
                                                    <div class="btn-decision">
                                                        <button type="submit" class="btn-go">Confirm</button>
                                                        <a href="user_inactive.php">
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

            </div>
        </div>
        <script>
            function openUnrestrict(userId){
                const modal = document.getElementById('unrestrictModal' + userId);
                const closeButton = document.querySelector('.close-button');
                modal.style.display = 'block';

                closeButton.addEventListener('click', function(){
                    modal.style.display = 'none';
                });

                window.addEventListener('click', function(event){
                    if (event.target === modal){
                        modal.style.display = 'none';
                    }
                });
            }
//Second search
            function openUnrestrict(userId){
                const modal = document.getElementById('unrestrictModal' + userId);
                const closeButton = document.querySelector('.close-button');
                modal.style.display = 'block';

                closeButton.addEventListener('click', function(){
                    modal.style.display = 'none';
                });

                window.addEventListener('click', function(event){
                    if (event.target === modal){
                        modal.style.display = 'none';
                    }
                });
            }
            
            
        </script>

        <script src="assets/script/restrict-disable.js"></script>
        <script src="assets/script/index.js"></script>
    </body>
</html>
<?php
} else {
    echo "You do not have access to this page.";
}
?>
