<?php
session_set_cookie_params(0);
session_start();
require_once 'db_connect.php';
require_once 'mail_helper.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: 403.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adopt_id = $_POST['adopt_id'];
    $action = $_POST['action'];

    $sql = "SELECT users.email, users.name as user_name, pets.name as pet_name, pets.id as pet_id 
            FROM adoptions 
            JOIN users ON adoptions.user_id = users.id
            JOIN pets ON adoptions.pet_id = pets.id
            WHERE adoptions.id = '$adopt_id'";
    
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();

    $email = $data['email'];
    $userName = $data['user_name'];
    $petName = $data['pet_name'];
    $petId = $data['pet_id'];

    if ($action == 'approve') {

        $updateAdopt = "UPDATE adoptions SET status = 'approved' WHERE id = '$adopt_id'";
        $conn->query($updateAdopt);

        $updatePet = "UPDATE pets SET status = 'adopted' WHERE id = '$petId'";
        $conn->query($updatePet);

        $subject = "Adoption Approved!";
        $body = "<h1>Congratulations, $userName!</h1>
                 <p>Your request to adopt <strong>$petName</strong> has been approved.</p>
                 <p>Please come to the sanctuary within 48 hours to pick up your new best friend.</p>";
        send_mail_notification($email, $subject, $body);

    } elseif ($action == 'reject') {
        $updateAdopt = "UPDATE adoptions SET status = 'rejected' WHERE id = '$adopt_id'";
        $conn->query($updateAdopt);

        $subject = "Update on your adoption request";
        $body = "<h1>Hello $userName,</h1>
                 <p>We are sorry to inform you that your request to adopt <strong>$petName</strong> was not approved at this time.</p>
                 <p>Please check our website for other animals looking for a home.</p>";
        send_mail_notification($email, $subject, $body);
    }

    header("Location: admin_dashboard.php");
    exit();
}
?>