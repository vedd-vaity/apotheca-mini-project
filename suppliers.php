<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include "db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";
    
    if ($action === "add") {
        $name = mysqli_real_escape_string($conn, $_POST["name"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $phone = mysqli_real_escape_string($conn, $_POST["phone"]);
        $address = mysqli_real_escape_string($conn, $_POST["address"]);
        
        $sql = "INSERT INTO suppliers (name, email, phone, address) 
                VALUES ('$name', '$email', '$phone', '$address')";
                
        $msg = mysqli_query($conn, $sql) ? "Supplier added successfully." : "Error: " . mysqli_error($conn);
    }
    elseif ($action === "delete") {
        $id = (int)$_POST["id"];
        $sql = "DELETE FROM suppliers WHERE id=$id";
        $msg = mysqli_query($conn, $sql) ? "Supplier deleted." : "Error: " . mysqli_error($conn);
    }
}

// Fetch all suppliers
$result = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers - Apotheca</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>
    <nav class="navbar">
        <div class="brand">💊 Apotheca</div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="stock.php">Stock</a></li>
            <li><a href="stock_update.php">Stock Update</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                <li><a href="admin.php">Admin</a></li>
            <?php endif; ?>
            <li><a href="suppliers.php" class="active">Suppliers</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="logout.php" class="text-danger">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>Supplier Management</h1>
            <p>Add and manage medicine suppliers.</p>
        </div>
        
        <?php if ($msg) echo "<div class='alert ". (strpos($msg, 'Error') === false ? 'alert-success' : 'alert-danger') ."'>$msg</div>"; ?>
        
        <div class="card mb-20">
            <h3>Add New Supplier</h3>
            <form action="suppliers.php" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Supplier Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address">
                    </div>
                    <div class="form-group btn-group align-bottom">
                        <button type="submit" class="btn btn-primary">Add Supplier</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h3>Supplier List</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td>
                                <form action="suppliers.php" method="POST" onsubmit="return confirm('Delete this supplier? medicines from this supplier will have supplier set to NULL.');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if(mysqli_num_rows($result) == 0): ?>
                            <tr><td colspan="6" class="text-center">No suppliers found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
