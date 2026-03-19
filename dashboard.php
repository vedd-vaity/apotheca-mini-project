<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Apotheca</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="brand">💊 Apotheca</div>
        <ul class="nav-links">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="stock.php">Stock</a></li>
            <li><a href="admin.php">Admin</a></li>
            <li><a href="suppliers.php">Suppliers</a></li>
            <li><a href="logout.php" class="text-danger">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>Dashboard</h1>
            <p>Overview of hospital medicine inventory.</p>
        </div>

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

        <div class="mt-20">
            <h3>Recent Changes</h3>
            <div class="card mt-10">
                <ul class="stock-list" id="recentChangesList">
                    <li class="stock-item loading">Loading recent changes...</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
