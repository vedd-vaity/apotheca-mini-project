<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <!-- Responsive design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Dashboard - Apotheca</title>

    <!-- Prevent caching during development -->
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>

    <!-- Navigation bar -->
    <nav class="navbar">
        <div class="brand">💊 Apotheca</div>
        <ul class="nav-links">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="stock.php">Stock</a></li>
            <li><a href="stock_update.php">Stock Update</a></li>
            <li><a href="admin.php">Admin</a></li>
            <li><a href="suppliers.php">Suppliers</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="logout.php" class="text-danger">Logout</a></li>
        </ul>
    </nav>

    <!-- Main container -->
    <div class="container">

        <!-- Header section -->
        <div class="header-section">
            <h1>Dashboard</h1>
            <p>Overview of hospital medicine inventory.</p>
        </div>

        <!-- Stats cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Medicines</h3>
                <p class="stat-value" id="totalMedicines">...</p>
            </div>
            <div class="stat-card">
                <h3>Low Stock</h3>
                <p class="stat-value" id="lowStock">...</p>
            </div>
            <div class="stat-card">
                <h3>Expired</h3>
                <p class="stat-value text-danger" id="expired">...</p>
            </div>
            <div class="stat-card">
                <h3>Expiring Soon</h3>
                <p class="stat-value text-warning" id="expiringSoon">...</p>
            </div>
        </div>

        <!-- Recent changes section -->
        <div class="mt-20">
            <div class="card mt-10" style="margin-top: 25px;">
                <h3>Recent Changes</h3>
                <ul class="stock-list" id="recentChangesList">
                    <li class="stock-item loading">Loading recent changes...</li>
                </ul>
            </div>
        </div>

    </div>

    <!-- JS file -->
    <script src="script.js?v=<?= time() ?>"></script>
</body>
</html>