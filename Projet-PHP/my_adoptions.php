<?php

session_set_cookie_params(0);
session_start();

require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: 403.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT a.request_date, a.status, p.name, p.image, p.type 
        FROM adoptions a 
        JOIN pets p ON a.pet_id = p.id 
        WHERE a.user_id = '$user_id' 
        ORDER BY a.request_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Adoptions - PetAdopt</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="light-nav">

    <nav>
        <div class="nav-container">
            <a href="index.php" class="logo">PetAdopt.</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="pets.php">Browse</a>
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="my_adoptions.php">My Requests</a>
                <a href="aboutUser.php">Account</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="section-title" style="margin-top: 2rem;">My Adoption Requests</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Pet</th>
                            <th>Name</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td style="width: 100px;">
                                    <img src="<?php echo $row['image']; ?>" 
                                         alt="<?php echo $row['name']; ?>" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;">
                                </td>
                                
                                <td style="font-weight: 600;">
                                    <?php echo $row['name']; ?> 
                                    <span style="font-size:0.8rem; color:#888; font-weight:normal;">(<?php echo $row['type']; ?>)</span>
                                </td>
                                
                                <td style="color:#666;">
                                    <?php echo date("M d, Y", strtotime($row['request_date'])); ?>
                                </td>
                                
                                <td>
                                    <?php 
                                    $status = $row['status'];
                                    $badgeColor = "#fef3c7";
                                    $textColor = "#92400e";
                                    $icon = "🕒";

                                    if($status == 'approved') {
                                        $badgeColor = "#d1fae5";
                                        $textColor = "#065f46";
                                        $icon = "✅";
                                    } elseif($status == 'rejected') {
                                        $badgeColor = "#fee2e2";
                                        $textColor = "#991b1b";
                                        $icon = "❌";
                                    }
                                    ?>
                                    <span style="background: <?php echo $badgeColor; ?>; color: <?php echo $textColor; ?>; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.9rem;">
                                        <?php echo $icon . " " . ucfirst($status); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="auth-box" style="margin-top: 2rem; max-width: 600px;">
                <h3>No requests yet!</h3>
                <p style="color: #666; margin-bottom: 2rem;">You haven't sent any adoption requests yet.</p>
                <a href="pets.php" class="btn-card" style="background: var(--primary-gradient);">Browse Pets</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>