<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include "db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medicine_id = (int)$_POST["medicine_id"];
    $change_qty = (int)$_POST["change_qty"];

    if ($medicine_id > 0 && $change_qty > 0) {
        $medResult = mysqli_query($conn, "SELECT quantity FROM medicines WHERE id=$medicine_id");
        if ($medResult && mysqli_num_rows($medResult) > 0) {
            $med = mysqli_fetch_assoc($medResult);
            $current_qty = (int)$med['quantity'];

            $new_qty = $current_qty - $change_qty;
            if ($new_qty < 0) {
                $msg = "Error: Cannot remove stock. Resulting quantity ($new_qty) would be negative.";
            } else {
                // Update medicine quantity
                mysqli_query($conn, "UPDATE medicines SET quantity = $new_qty WHERE id=$medicine_id");
                
                // Insert into stock_logs
                mysqli_query($conn, "INSERT INTO stock_logs (medicine_id, change_qty, action, date) VALUES ($medicine_id, $change_qty, 'stock_out', CURDATE())");
                
                $msg = "Stock removed successfully. New quantity: $new_qty";
            }
        } else {
            $msg = "Error: Medicine not found.";
        }
    } else {
        $msg = "Error: Please enter a valid quantity to remove.";
    }
}

// Fetch all medicines for dropdown
$medsResult = mysqli_query($conn, "SELECT id, name, batch_no FROM medicines ORDER BY name ASC");
$medicines = [];
if($medsResult){
    while ($m = mysqli_fetch_assoc($medsResult)) {
        $medicines[] = $m;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Stock - Apotheca</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>
    <nav class="navbar">
        <div class="brand">💊 Apotheca</div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="stock.php">Stock</a></li>
            <li><a href="stock_update.php" class="active">Stock Update</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                <li><a href="admin.php">Admin</a></li>
            <?php endif; ?>
            <li><a href="suppliers.php">Suppliers</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="logout.php" class="text-danger">Logout</a></li>
        </ul>
    </nav>

    <div class="container container-wide">
        <div class="header-section">
            <h1>Remove Stock</h1>
            <p>Safely deduct daily dispensed medicine quantities from inventory.</p>
        </div>
        
        <?php if ($msg) echo "<div class='alert ". (strpos($msg, 'Error') === false ? 'alert-success' : 'alert-danger') ."'>$msg</div>"; ?>
        
        <div class="card" style="max-width:500px; margin: 0 auto; padding: 30px;">
            <form action="stock_update.php" method="POST">
                <div class="form-group">
                    <label>Select Medicine</label>
                    <select name="medicine_id" required>
                        <option value="">-- Select a Medicine --</option>
                        <?php foreach($medicines as $m): ?>
                            <option value="<?= $m['id'] ?>">
                                <?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['batch_no'] ?? '--') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Quantity to Remove</label>
                    <input type="number" name="change_qty" min="1" value="1" required>
                </div>
                
                <div class="form-group mt-20" style="margin-top:20px;">
                    <button type="submit" class="btn btn-danger">Remove Stock</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="script.js?v=<?= time() ?>"></script>
</body>
</html>
