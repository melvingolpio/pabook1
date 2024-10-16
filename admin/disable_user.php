<?php 
session_start();
require('../dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    
    $query = "UPDATE users SET disabled = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0){
        echo '<script>alert("User has been disabled"); window.location.href="user_account.php"</script>';
    } else {
        echo '<script>alert("Error")</script>';
    }

    $stmt->close();
    $conn->close();
}
?>