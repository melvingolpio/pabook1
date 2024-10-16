<?php 
session_start();
require('../dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $user_id = $_POST['user_id'];

    $undo_query = "UPDATE users SET disabled = 0 WHERE id = ? ";
    $undo_stmt = $conn->prepare($undo_query);
    $undo_stmt->bind_param('i', $user_id);
    $undo_stmt->execute();

    if ($undo_stmt->affected_rows > 0 ) {
        echo '<script>alert("User has been Activited"); window.location.href="user_inactive.php"</script>';
    } else {
        echo 'Failed!';
    }
}

;?>

