<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include "db.php";
$suppResult = mysqli_query($conn, "SELECT id, name FROM suppliers ORDER BY name ASC");
$suppliers = [];
if($suppResult) {
    while ($s = mysqli_fetch_assoc($suppResult)) {
        $suppliers[] = $s;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock - Apotheca</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="brand">💊 Apotheca</div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="stock.php" class="active">Stock</a></li>
            <li><a href="admin.php">Admin</a></li>
            <li><a href="suppliers.php">Suppliers</a></li>
            <li><a href="logout.php" class="text-danger">Logout</a></li>
        </ul>
    </nav>

    <div class="container container-wide">
        <div class="header-section">
            <h1>Stock Overview</h1>
            <p>Live search and medicine status.</p>
        </div>

        <div class="search-bar mb-20" style="display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" id="searchInput" placeholder="Search medicines by name..." class="search-input" style="flex:1; min-width:200px;">
            <select id="tagFilter" class="table-input" style="width:150px; padding:12px; border-radius:8px; border: 1px solid var(--border);">
                <option value="">All Tags</option>
                <option value="Expired">Expired</option>
                <option value="Low">Low Stock</option>
                <option value="Expiring">Expiring Soon</option>
                <option value="New">New Medicine</option>
            </select>
            <select id="supplierFilter" class="table-input" style="width:150px; padding:12px; border-radius:8px; border: 1px solid var(--border);">
                <option value="">All Suppliers</option>
                <?php foreach($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="categoryFilter" class="table-input" style="width:150px; padding:12px; border-radius:8px; border: 1px solid var(--border);">
                <option value="">All Categories</option>
                <option value="tablet">Tablet</option>
                <option value="syrup">Syrup</option>
                <option value="injection">Injection</option>
            </select>
        </div>

        <div class="card">
            <ul class="stock-list" id="stockList">
                <li class="stock-item loading">Loading stock...</li>
            </ul>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
