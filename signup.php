<?php
session_start();
require('dbconn.php'); 


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/smenu.css">
    <link rel="stylesheet" href="assets/css/resp-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
                    <div class="nav-option <?php echo (basename($_SERVER['PHP_SELF']) == 'qr-scanner.php') ? 'active' : ''; ?>">
                        <i class="fas fa-camera-retro    nav-img" alt="scanner"></i>
                        <a href="qr-scanner.php" class="nav-link"><h3>Scanner</h3></a>
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

            <h1>Create Account for User's</h1>
            <br>

            <div class="signup-container">
                

            <form method="POST" action="process_signup.php" enctype="multipart/form-data">

            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value=""></option>
                <option value="ceo">CEO</option>
                <option value="headAd">Head Administration</option>
                <option value="security">Security</option>
                <option value="faculty">Faculty</option>
                <option value="staff">staff</option>
                <option value=""></option>
                <option value=""></option>
            </select>

            <label for="contact_number">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="type">Type:</label>
            <select id="type" name="type" required>
                <option value="User">User</option>
                <option value="User">Admin</option>
            </select>
            <label>Default image:</label>
            <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="file-input-label">


            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Default Password:</label>
            <input type="password" id="password" name="password" required>

            <div class="button-container">
                <button type="button" class="cancel-button" onclick="window.history.back();">Cancel</button>
                <button type="submit" class="submit-button">Create</button>
                
            </div>

        </form>
    </div>
        </div>
    </div>

    <script src="assets/script/index.js"></script>
</body>
</html>
