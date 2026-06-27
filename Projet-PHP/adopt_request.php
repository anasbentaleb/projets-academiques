<?php
session_set_cookie_params(0);
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pet_id'])) {
    
    $user_id = $_SESSION['user_id'];
    $pet_id = $_POST['pet_id'];

    $checkSql = "SELECT * FROM adoptions WHERE user_id = '$user_id' AND pet_id = '$pet_id' AND status = 'pending'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows == 0) {
        $sql = "INSERT INTO adoptions (user_id, pet_id, status) VALUES ('$user_id', '$pet_id', 'pending')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Adoption request sent successfully!'); window.location.href='pets.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "<script>alert('You have already requested this pet.'); window.location.href='pets.php';</script>";
    }
} else {
    header("Location: pets.php");
}
?>