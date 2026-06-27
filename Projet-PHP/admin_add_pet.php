<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: 403.php");
    exit();
}

$message = "";

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $age = $_POST['age'];
    $desc = $_POST['description'];
    $imageURL = $_POST['image'];
    $status = 'available';
    
    $sql = "INSERT INTO pets (name, type, age, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->stmt_init();
    
    if ($stmt->prepare($sql)) {
        $stmt->bind_param('ssisss', $name, $type, $age, $desc, $imageURL, $status);
        if ($stmt->execute()) {
            $message = "New pet added successfully!";
            header("refresh:2;url=admin_dashboard.php");
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Pet</title>
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
            <h2 class="form-header">Add New Pet</h2>
            
            <?php if($message) echo "<p style='color:green; text-align:center; margin-bottom:1rem; font-weight:bold;'>$message</p>"; ?>

            <form action="" method="POST" class="form-grid">
                
                <div class="form-group full-width">
                    <label>Pet Name</label>
                    <input type="text" name="name" required placeholder="e.g. Buddy">
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <select name="type" required>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Age (Years)</label>
                    <input type="number" name="age" required placeholder="e.g. 2">
                </div>

                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description" rows="4" required placeholder="Tell us about the pet..."></textarea>
                </div>

                <div class="form-group full-width">
                    <label>Image URL</label>
                    <input type="text" name="image" required placeholder="https://example.com/dog-image.jpg">
                </div>

                <div class="form-actions">
                    <a href="admin_dashboard.php" class="btn-secondary">Cancel</a>
                    <button type="submit" name="submit" class="btn-primary">Save Pet</button>
                </div>

            </form>
        </div>
    </div>
</body>
</html>