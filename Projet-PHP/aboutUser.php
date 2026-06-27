<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Fetch User Details
$sql = "SELECT name, email, password, role FROM users WHERE id = ?";
$stmt = $conn->stmt_init();
if ($stmt->prepare($sql)) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($name, $email, $real_password, $role);
    $stmt->fetch();
    $stmt->close();
}

// 2. Fetch Cookies
// We now look for the specific cookie for THIS user ID
$cookieName = 'visit_count_' . $user_id;

$visit_count = isset($_COOKIE[$cookieName]) ? $_COOKIE[$cookieName] : 1; 
$last_seen   = isset($_COOKIE['last_seen']) ? $_COOKIE['last_seen'] : "Just now";
$fav_pet     = isset($_COOKIE['fav_category']) ? $_COOKIE['fav_category'] : "Not decided yet";

?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="light-nav">

    <nav>
        <div class="nav-container">
            <a href="index.php" class="logo">PetAdopt.</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="pets.php">Browse</a>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role']== 'user'): ?>
                <a href="my_adoptions.php">My Requests</a>
                <a href="aboutUser.php">Account</a>
                <?php   endif;  ?>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role']== 'admin'): ?>
                    <a href="admin_dashboard.php"></a>
                <?php   endif;  ?>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <div class="form-card">
            <h2 class="form-header">My Profile</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background:#d1fae5; color:#065f46; padding:15px; border-radius:8px; margin-bottom:20px; text-align:center;">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div style="background:#fee2e2; color:#991b1b; padding:15px; border-radius:8px; margin-bottom:20px; text-align:center;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <div class="form-grid">
                
                <div class="full-width" style="border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:10px;">
                    <h3 style="color:#0284c7;">Account Details</h3>
                </div>

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($name); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="text" value="<?php echo htmlspecialchars($email); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Account Role</label>
                    <input type="text" value="<?php echo ucfirst($role); ?>" readonly>
                </div>


                <div class="full-width" style="border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:10px; margin-top:20px;">
                    <h3 style="color:#0284c7;">Website Activity (Cookies)</h3>
                </div>

                <div class="form-group">
                    <label>Total Visits</label>
                    <div style="padding:10px; background:#f0f9ff; border-radius:8px; color:#0284c7; font-weight:bold;">
                        <?php echo $visit_count; ?> times
                    </div>
                </div>

                <div class="form-group">
                    <label>Last Seen</label>
                    <div style="padding:10px; background:#f0f9ff; border-radius:8px; color:#0284c7;">
                        <?php echo $last_seen; ?>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Favorite Pet Category</label>
                    <div style="padding:10px; background:#fff7ed; border-radius:8px; color:#c2410c; border:1px solid #ffedd5;">
                        <?php 
                            if ($fav_pet == "Dog") echo "🐶 You prefer Dogs!";
                            elseif ($fav_pet == "Cat") echo "🐱 You prefer Cats!";
                            else echo "❓ " . $fav_pet;
                        ?>
                    </div>
                    <small style="color:#999;">Based on what you filter in the browse page.</small>
                </div>

            </div>
        </div>

        <div class="form-card" style="margin-top: 2rem;">
            <h3 class="form-header" style="font-size:1.5rem; margin-bottom:1.5rem;">Security Settings</h3>
            
            <form action="update_password.php" method="POST" class="form-grid">
                <div class="form-group full-width">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required placeholder="Enter current password">
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required placeholder="New password">
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required placeholder="Confirm new password">
                </div>

                <div class="form-actions">
                    <button type="submit" name="change_password" class="btn-primary" style="background:#059669;">Update Password</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>