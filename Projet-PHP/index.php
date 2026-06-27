<?php 
    session_set_cookie_params(0);
    session_start();    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetAdopt - Discover. Love. Enjoy.</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

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

    <header class="hero-section">
        <div class="nav-container"> <h1 class="hero-title">Discover. Love. Enjoy.</h1>
            <p class="hero-subtitle">Platform for finding your new best friend.</p>

            <form action="pets.php" method="GET" class="hero-search">
                <input type="text" name="search" placeholder="Search...">
                <select name="type">
                    <option value="">All Types</option>
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                </select>
                <button type="submit">Search</button>
            </form>
        </div>
    </header>

    <section class="container">
        <h2 class="section-title">How does it work?</h2>
        <div class="grid-3">
            <div class="card" style="text-align:center;">
                <div style="font-size:3rem; margin-bottom:1rem;">🔍</div>
                <h3>Search</h3>
                <p>Browse our catalog to find the perfect companion that fits your lifestyle.</p>
            </div>
            <div class="card" style="text-align:center;">
                <div style="font-size:3rem; margin-bottom:1rem;">📩</div>
                <h3>Request</h3>
                <p>Send an adoption request. Our team will review it and get back to you.</p>
            </div>
            <div class="card" style="text-align:center;">
                <div style="font-size:3rem; margin-bottom:1rem;">🏠</div>
                <h3>Welcome Home</h3>
                <p>Once approved, bring your new family member home and enjoy life!</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> PetAdopt Project. Design inspired by Topic Listing.</p>
    </footer>

</body>
</html>