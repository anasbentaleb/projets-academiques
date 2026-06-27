<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['change_password'])) {
    $user_id = $_SESSION['user_id'];
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // --- 1. SECURITY CHECK: MINIMUM LENGTH ---
    if (strlen($new_pass) < 6) {
        header("Location: aboutUser.php?error=Password must be at least 6 characters long");
        exit();
    }
    // -----------------------------------------

    // 2. Check if new passwords match
    if ($new_pass !== $confirm_pass) {
        header("Location: aboutUser.php?error=New passwords do not match");
        exit();
    }

    // 3. Get current password from Database
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->stmt_init();
    if ($stmt->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($db_pass);
        $stmt->fetch();
        $stmt->close();

        // 4. Verify Current Password
        if ($current_pass !== $db_pass) {
            header("Location: aboutUser.php?error=Current password is incorrect");
            exit();
        }

        // 5. Update to New Password
        $updateSql = "UPDATE users SET password = ? WHERE id = ?";
        $stmtUpdate = $conn->stmt_init();
        if ($stmtUpdate->prepare($updateSql)) {
            $stmtUpdate->bind_param("si", $new_pass, $user_id);
            
            if ($stmtUpdate->execute()) {
                header("Location: aboutUser.php?success=Password updated successfully!");
            } else {
                header("Location: aboutUser.php?error=Database error: Could not update.");
            }
            $stmtUpdate->close();
        }
    } else {
        header("Location: aboutUser.php?error=Database error.");
    }

} else {
    // If accessed directly without submitting form
    header("Location: aboutUser.php");
}
?>