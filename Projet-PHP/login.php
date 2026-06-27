<?php
session_start();
require_once 'db_connect.php';
require_once 'mail_helper.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";
$activeTab = "login"; 

if (isset($_POST['register'])) {
    $activeTab = "register";
    
    $name = htmlspecialchars($_POST['reg_name']);
    $email = $_POST['reg_email'];
    $pass = $_POST['reg_password'];
    $role = 'user';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } 
    elseif (strlen($pass) < 6) {
        $message = "Password must be at least 6 characters.";
    } 
    else {
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->stmt_init();
        if ($stmt->prepare($checkSql)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $message = "Email already registered!";
            } else {
                $stmt->close();
                $insertSql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
                $stmt = $conn->stmt_init();
                if ($stmt->prepare($insertSql)) {
                    $stmt->bind_param('ssss', $name, $email, $pass, $role);
                    
                    if ($stmt->execute()) {
                        $message = "<span style='color:green'>Registration successful! Please login.</span>";
                        $activeTab = "login"; 
                    } else {
                        $message = "Error: " . $stmt->error;
                    }
                }
                
            }
        }
    }
    $subject = "Registration complete";
    $body = "<h1>Congratulations, $name!</h1>
    <p>You have been registered successfully</p>";
    send_mail_notification($email, $subject, $body);
}


if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";
    $stmt = $conn->stmt_init();
    
    if ($stmt->prepare($sql)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($db_id, $db_name, $db_email, $db_pass, $db_role);
        
        if ($stmt->fetch()) {
            if ($password == $db_pass) {
                $visits = isset($_COOKIE['visit_count']) ? $_COOKIE['visit_count'] : 0;
                $visits++;
                setcookie('visit_count', $visits, time() + (86400 * 365), "/");
                $_SESSION['user_id'] = $db_id;
                $_SESSION['user_name'] = $db_name;
                $_SESSION['role'] = $db_role;

                if ($db_role == 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "User not found.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - PetAdopt</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="light-nav">
    <nav>
        <div class="nav-container">
            <a href="index.php" class="logo">PetAdopt.</a>
            <div class="nav-links"><a href="index.php">Back Home</a></div>
        </div>
    </nav>

    <div class="auth-box">
        <h2 style="margin-bottom:1.5rem;">Welcome Back</h2>
        
        <div style="display:flex; justify-content:center; gap:20px; margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:10px;">
            <span onclick="showLogin()" id="btn-login" style="cursor:pointer; font-weight:bold; color:#0083b0;">Login</span>
            <span onclick="showRegister()" id="btn-register" style="cursor:pointer; color:#999;">Register</span>
        </div>

        <?php if($message) echo "<p style='color:red; margin-bottom:10px;'>$message</p>"; ?>

        <form id="login-form" method="POST" style="display: <?php echo ($activeTab == 'login') ? 'block' : 'none'; ?>;">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" style="margin-top: 15px;">Sign In</button>
        </form>

        <form id="register-form" method="POST" style="display: <?php echo ($activeTab == 'register') ? 'block' : 'none'; ?>;">
            <input type="text" name="reg_name" placeholder="Full Name" required>
            <input type="email" name="reg_email" placeholder="Email Address" required>
            <input type="password" name="reg_password" placeholder="Password (Min 6)" required>
            <button type="submit" name="register" style="background:#2ecc71;">Create Account</button>
        </form>
    </div>
    
    <script>
        function showLogin() {
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('btn-login').style.color = '#0083b0';
            document.getElementById('btn-register').style.color = '#999';
        }
        function showRegister() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
            document.getElementById('btn-login').style.color = '#999';
            document.getElementById('btn-register').style.color = '#0083b0';
        }
    </script>
</body>
</html>