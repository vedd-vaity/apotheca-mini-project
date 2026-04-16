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
    <title>About Us - Apotheca</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>
    <nav class="navbar">
        <div class="brand">💊 Apotheca</div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="stock.php">Stock</a></li>
            <li><a href="stock_update.php">Stock Update</a></li>
            <li><a href="admin.php">Admin</a></li>
            <li><a href="suppliers.php">Suppliers</a></li>
            <li><a href="about.php" class="active">About Us</a></li>
            <li><a href="logout.php" class="text-danger">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>About Us</h1>
            <p>Learn more about Apotheca and our mission.</p>
        </div>

        <div class="card mt-20">
            <h3>Our Mission</h3>
            <p>
                Apotheca is dedicated to making medicine inventory management a breeze for any hospitals wanting to participate. <br>
                By using technologies such as database systems and web applications, we can ensure that your medicines are always in stock and accounted for.<br>
                Any hospital using this will never need another physical log book again.
            </p>
            
            <h3 class="mt-20">Our Team</h3>
            <p>
                The Apotheca team consists of 3 dedicated developers who have worked tirelessly to create this revolutionary medicine inventory management system.
                <ul>
                    <li>Vedd Vaity(16010124155)</li>
                    <li>Sounabha Majumdar(16010124167)</li>
                    <li>Tirth Kotadia(16010124156)</li> 
                </ul>
            </p>

            <h3 class="mt-20">Contact Info</h3>
            <p>
                Email: admin@apotheca.com<br>
                Phone: +91 1234567890
            </p>
        </div>
    </div>

    <script src="script.js?v=<?= time() ?>"></script>
</body>
</html>
