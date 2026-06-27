<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pet_sanctuary_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_errno) {
    echo "Connection failed :" . $conn->connect_error;
    exit();
}
?>