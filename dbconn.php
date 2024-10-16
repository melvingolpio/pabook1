<?php
$servername = "us-cluster-east-01.k8s.cleardb.net";
$username = "b5f6a402460fa3";
$password = "83f06a6b";
$dbname = "heroku_706906bb621a740";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
