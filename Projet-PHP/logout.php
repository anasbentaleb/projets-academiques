<?php
session_start();

$currentDate = date("F j, Y, g:i a"); 
setcookie('last_seen', $currentDate, time() + (86400 * 30), "/");

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: login.php");
exit();
?>