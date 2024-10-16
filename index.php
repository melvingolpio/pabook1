<?php 
session_start();
require('dbconn.php');

if (isset($_POST['signin'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $username = mysqli_real_escape_string($conn, $username);

        $stmt = $conn->prepare("SELECT id, username, role, password, type, restricted, disabled FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];
            $role_type = $row['type'];
            $user_id = $row['id'];
            $user_role = $row['role'];
            $restricted = $row['restricted'];
            $disabled = $row['disabled'];

            if (password_verify($password, $stored_password)) {
                $_SESSION['username'] = $username;
                $_SESSION['type'] = $role_type;
                $_SESSION['id'] = $user_id;
                $_SESSION['role'] = $user_role;
                $_SESSION['restricted'] = $restricted;
                $_SESSION['disabled'] = $disabled;

                if ($role_type === 'User' && $disabled === 0) {
                    header("Location: user/homepage.php");
                } elseif ($role_type === 'Admin' && $disabled === 0) {
                    header("Location: admin/index.php");
                } else {
                    echo "<script>alert('Account is disabled');</script>";
                    exit();
                }
        
            } else {
                echo "<script>alert('Incorrect Username/Password!');</script>";
            }
        } else {
            echo "<script>alert('User does not exist!');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Username/Password not provided!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Login</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="log.css">
</head>
<body>
    <div class="wrapper">
        <form action="index.php" method="post">
            <h1 class="h2">Login</h1>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" id="password" placeholder="Password" required>            
                <i class='bx bx-show toggle-password' onclick="togglePassword()"></i>
            </div>
            <button type="submit" name="signin" class="btn">Login</button>
        </form>
    </div>
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById('password');
            var toggleIcon = document.querySelector('.toggle-password');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bx-show');
                toggleIcon.classList.add('bx-hide');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bx-hide');
                toggleIcon.classList.add('bx-show');
            }
        }
    </script>
</body>
</html>
