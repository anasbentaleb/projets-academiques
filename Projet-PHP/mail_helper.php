<?php
function send_mail_notification($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@petsanctuary.com" . "\r\n";
    if(mail($to, $subject, $message, $headers)) {
        return true;
    } else {
        return false;
    }
}
?>