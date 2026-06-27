<?php
session_set_cookie_params(0);
session_start();

if (isset($_GET['type']) && !empty($_GET['type'])) {
    $typeFilter = $_GET['type'];
    setcookie('fav_category', $typeFilter, time() + (86400 * 30), "/");
}
require_once 'db_connect.php';
require_once 'classes/Pet.php';

$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$search = isset($_GET['search']) ? $_GET['search'] : "";
$type   = isset($_GET['type']) ? $_GET['type'] : "";
$status = isset($_GET['status']) ? $_GET['status'] : "available";

$sql = "SELECT p.*, 
        (SELECT COUNT(*) FROM adoptions a 
         WHERE a.pet_id = p.id 
         AND a.user_id = ? 
         AND a.status = 'pending') as my_request
        FROM pets p 
        WHERE 1=1";

$types = "i"; 
$params = [$current_user_id];


if ($status != 'all') {
    $sql .= " AND p.status = ?";
    $types .= "s";
    $params[] = $status;
}


if (!empty($search)) {
    $sql .= " AND p.name LIKE ?";
    $types .= "s";
    $params[] = "%" . $search . "%";
}

if (!empty($type)) {
    $sql .= " AND p.type = ?";
    $types .= "s";
    $params[] = $type;
}

$stmt = $conn->stmt_init();
if ($stmt->prepare($sql)) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Pets</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
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
        <h2 class="section-title">Meet Our Animals</h2>

        <div style="text-align:center; margin-bottom:2rem;">
            <form method="GET" action="pets.php" class="hero-search" style="box-shadow: 0 5px 15px rgba(0,0,0,0.05); border:1px solid #eee;">
                
                <input type="text" name="search" placeholder="Search name..." value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="type">
                    <option value="">All Types</option>
                    <option value="Dog" <?php if($type=='Dog') echo 'selected'; ?>>Dogs</option>
                    <option value="Cat" <?php if($type=='Cat') echo 'selected'; ?>>Cats</option>
                </select>

                <select name="status" style="border-left:1px solid #eee;">
                    <option value="available" <?php if($status=='available') echo 'selected'; ?>>Available</option>
                    <option value="adopted" <?php if($status=='adopted') echo 'selected'; ?>>Adopted</option>
                    <option value="all" <?php if($status=='all') echo 'selected'; ?>>Show All</option>
                </select>

                <button type="submit">Filter</button>
            </form>
        </div>

        <div class="grid-3">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    
                    $petId = $row['id'];
                    $petName = $row['name'];
                    $petType = $row['type'];
                    $petAge = $row['age'];
                    $petDesc = htmlspecialchars($row['description']);
                    $petImg = $row['image'];
                    $petStatus = $row['status'];
                    
                    $hasRequested = $row['my_request']; 

                    $badge = "🐾";
                    $badgeColor = "#e0f2fe"; 
                    $badgeTextColor = "#0284c7";

                    if ($petStatus == 'adopted') {
                        $badge = "Adopted";
                        $badgeColor = "#fee2e2";
                        $badgeTextColor = "#991b1b";
                    } elseif ($hasRequested > 0) {
                        $badge = "Requested";
                        $badgeColor = "#fef3c7";
                        $badgeTextColor = "#92400e";
                    }

                    echo "
                    <div class='card'>
                        <div class='card-badge' style='background:$badgeColor; color:$badgeTextColor; width:auto; padding:0 10px; font-size:0.9rem;'>$badge</div>
                        <img src='$petImg' alt='$petName'>
                        
                        <h3>$petName</h3>
                        <p>$petType • $petAge years old</p>
                        
                        <button class='btn-card' onclick=\"openModal(
                            '$petName', 
                            '$petType', 
                            '$petAge', 
                            '$petDesc', 
                            '$petImg', 
                            '$petId',
                            '$petStatus',
                            '$hasRequested'
                        )\">View Details</button>
                    </div>";
                }
            } else {
                echo "<p style='text-align:center; width:100%; grid-column:1/-1;'>No pets found matching your criteria.</p>";
            }
            ?>
        </div>
    </div>

    <div id="petModal" class="modal" style="display:none;">
        <div class="auth-box" style="margin: 5% auto; position:relative;">
            <span class="close" onclick="closeModal()" style="position:absolute; right:20px; top:20px; cursor:pointer; font-size:1.5rem;">&times;</span>
            
            <img id="modalImg" src="" style="width:100%; height:250px; object-fit:cover; border-radius:15px; margin-bottom:1rem;">
            
            <h2 id="modalName"></h2>
            <p><span id="modalType"></span> • <span id="modalAge"></span> years</p>
            <p id="modalDesc" style="color:#666; margin:1rem 0; background:#f9f9f9; padding:10px; border-radius:8px;"></p>

            <?php if(isset($_SESSION['user_id'])): ?>
                <form action="adopt_request.php" method="POST" id="adoptForm">
                    <input type="hidden" name="pet_id" id="modalPetId">
                    
                    <button type="submit" id="modalBtn">Request Adoption</button>
                    
                    <p id="modalMsg" style="color:red; display:none; margin-top:10px; font-weight:bold;"></p>
                </form>
            <?php else: ?>
                <a href="login.php" class="btn-card" style="background:var(--primary-gradient); display:block; text-align:center;">Login to Adopt</a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>