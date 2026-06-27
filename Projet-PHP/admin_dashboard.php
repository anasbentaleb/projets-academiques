<?php
session_set_cookie_params(0);
session_start();
require_once 'db_connect.php';
require_once 'classes/Pet.php'; 


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: 403.php");
    exit();
}


$petsSql = "SELECT * FROM pets";
$petsResult = $conn->query($petsSql);


$adoptSql = "SELECT adoptions.id as adopt_id, users.name as user_name, pets.name as pet_name, adoptions.request_date 
             FROM adoptions 
             JOIN users ON adoptions.user_id = users.id 
             JOIN pets ON adoptions.pet_id = pets.id 
             WHERE adoptions.status = 'pending'";
$adoptResult = $conn->query($adoptSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="light-nav">

    <nav>
        <div class="nav-container">
            <a href="index.php" class="logo">AdminPanel</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="pets.php">Browse</a>
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin: 2rem 0;">
            <h1 style="font-size:2rem;">Dashboard</h1>
            <a href="admin_add_pet.php" class="btn-card" style="background:var(--primary-gradient);">+ Add New Pet</a>
        </div>

        <h3 style="margin-bottom:1rem;">Pending Adoptions</h3>
        <div class="table-wrapper">
            <?php if ($adoptResult->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr><th>User</th><th>Pet</th><th>Date</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                    <?php while($row = $adoptResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['user_name']; ?></td>
                            <td><?php echo $row['pet_name']; ?></td>
                            <td><?php echo $row['request_date']; ?></td>
                            <td>
                                <form action="admin_process_adopt.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="adopt_id" value="<?php echo $row['adopt_id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" style="padding:5px 10px; font-size:0.8rem; background:#2ecc71;">Approve</button>
                                </form>
                                <form action="admin_process_adopt.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="adopt_id" value="<?php echo $row['adopt_id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" style="padding:5px 10px; font-size:0.8rem; background:#e74c3c;">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No pending requests.</p>
            <?php endif; ?>
        </div>

        <h3 style="margin:2rem 0 1rem;">Manage Pets</h3>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php while($row = $petsResult->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['type']; ?></td>
                        <td>
                            <?php 
                            $s = $row['status'];
                            $color = ($s == 'available') ? 'green' : (($s == 'adopted') ? 'red' : 'orange');
                            echo "<span style='color:$color; font-weight:600;'>".ucfirst($s)."</span>";
                            ?>
                        </td>
                        <td>
                            <a href="admin_edit_pet.php?id=<?php echo $row['id']; ?>" style="color:#0083b0; margin-right:10px;">Edit</a>
                            <a href="admin_delete_pet.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete?');" style="color:#e74c3c;">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>