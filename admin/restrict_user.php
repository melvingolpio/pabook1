<?php 
session_start();
require('../dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];

    $query = "UPDATE users SET restricted = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo '<script>alert("User has been restricted"); window.location.href="user_account.php"</script>';
    } else {
        echo "Failed`!";
    }
    $stmt->close();
    $conn->close();

}
?>
