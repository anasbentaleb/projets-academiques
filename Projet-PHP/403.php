<?php
    session_set_cookie_params(0);
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>403 Forbidden</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding-top: 50px; }
        h1 { font-size: 50px; color: #dc3545; }
        p { font-size: 20px; }
        a { text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <h1>403</h1>
    <h2>Access Denied</h2>
    <p>You do not have permission to view this page.</p>
    <p>Return to <a href="index.php">Home Page</a></p>
</body>
</html>