<?php 
session_start();
require('../dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    
    $lift_query = "UPDATE users SET restricted = 0 WHERE id = ?";
    $lift_stmt = $conn->prepare($lift_query);
    $lift_stmt->bind_param('i', $user_id);
    $lift_stmt->execute();
    
    if ($lift_stmt->affected_rows > 0) {
        echo '<script>alert("User has been Unrestricted"); window.location.href="user_restricted.php"</script>';
    } else {
        echo "Failed`!";
    }

    $stmt->close();
    $conn->close(); 
}
;?>