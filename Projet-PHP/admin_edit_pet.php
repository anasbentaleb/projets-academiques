<?php
session_start();
require_once 'db_connect.php';
require_once 'classes/Pet.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: 403.php");
    exit();
}

$id = $_GET['id']; 
$message = "";

$sql = "SELECT name, type, age, description, image FROM pets WHERE id = ?";
$stmt = $conn->stmt_init();

if ($stmt->prepare($sql)) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($db_name, $db_type, $db_age, $db_desc, $db_image);
    $stmt->fetch();
    $stmt->close();
}

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $age = $_POST['age'];
    $desc = $_POST['description'];
    $imageURL = $_POST['image'];

    $updateSql = "UPDATE pets SET name=?, type=?, age=?, description=?, image=? WHERE id=?";
    $stmt = $conn->stmt_init();
    
    if ($stmt->prepare($updateSql)) {
        $stmt->bind_param('ssissi', $name, $type, $age, $desc, $imageURL, $id);
        
        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $message = "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Pet</title>
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
        <div class="form-card">
            <h2 class="form-header">Edit Pet Details</h2>
            
            <form action="" method="POST" class="form-grid">
                
                <div class="form-group full-width">
                    <label>Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($db_name); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Type</label>
                    <select name="type">
                        <option value="Dog" <?php if($db_type == 'Dog') echo 'selected'; ?>>Dog</option>
                        <option value="Cat" <?php if($db_type == 'Cat') echo 'selected'; ?>>Cat</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" value="<?php echo htmlspecialchars($db_age); ?>" required>
                </div>
                
                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($db_desc); ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label>Image URL</label>
                    <input type="text" name="image" value="<?php echo htmlspecialchars($db_image); ?>">
                    
                    <div style="display:flex; align-items:center; gap:15px; margin-top:10px; background:#f9f9f9; padding:10px; border-radius:8px;">
                        <img src="<?php echo htmlspecialchars($db_image); ?>" class="img-preview">
                        <span style="color:#666; font-size:0.9rem;">Current Image Preview</span>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="admin_dashboard.php" class="btn-secondary">Cancel</a>
                    <button type="submit" name="update" class="btn-primary">Update Pet</button>
                </div>

            </form>
        </div>
    </div>
</body>
</html>