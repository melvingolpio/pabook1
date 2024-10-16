<?php 
session_start();
require('../dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'shutDown'){
        $new_status = '1';
        $message = 'SHUTDOOOWN';
    } elseif ($action === 'On'){
        $new_status = '0';
        $message = 'OOOOON';
    }

    file_put_contents('shutdown_status.txt', $new_status);

    // Output JavaScript to show an alert and then redirect
    echo '<script>
        alert("' . $message . '");
        window.location.href = document.referrer;
    </script>';
}
?>
