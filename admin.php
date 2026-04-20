<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
// Restrict to Admin only (Role 1)
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 1) {
    header("Location: dashboard.php");
    exit();
}
include "db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";
    
    if ($action === "add") {
        $name = mysqli_real_escape_string($conn, $_POST["name"]);
        $category = mysqli_real_escape_string($conn, $_POST["category"]);
        $batch_no = mysqli_real_escape_string($conn, $_POST["batch_no"]);
        $quantity = (int)$_POST["quantity"];
        $expiry_date = trim($_POST["expiry_date"]) ? "'".$_POST["expiry_date"]."'" : "NULL";
        $supplier_id = !empty($_POST["supplier_id"]) ? (int)$_POST["supplier_id"] : "NULL";
        
        $sql = "INSERT INTO medicines (name, category, batch_no, quantity, expiry_date, supplier_id) 
                VALUES ('$name', '$category', '$batch_no', $quantity, $expiry_date, $supplier_id)";
                
        if (mysqli_query($conn, $sql)) {
            $new_id = mysqli_insert_id($conn);
            mysqli_query($conn, "INSERT INTO stock_logs (medicine_id, change_qty, action, date) VALUES ($new_id, $quantity, 'added_new', CURDATE())");
            $msg = "Medicine added successfully.";
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    }

    elseif ($action === "delete") {
        $id = (int)$_POST["id"];
        $sql = "DELETE FROM medicines WHERE id=$id";
        $msg = mysqli_query($conn, $sql) ? "Medicine deleted." : "Error: " . mysqli_error($conn);
    }
    elseif ($action === "add_stock") {
        $id = (int)$_POST["id"];
        $change_qty = (int)$_POST["change_qty"];
        if ($change_qty > 0) {
            mysqli_query($conn, "UPDATE medicines SET quantity = quantity + $change_qty WHERE id=$id");
            mysqli_query($conn, "INSERT INTO stock_logs (medicine_id, change_qty, action, date) VALUES ($id, $change_qty, 'stock_in', CURDATE())");
            $msg = "Stock added successfully.";
        }
    }
    elseif ($action === "remove_stock") {
        $id = (int)$_POST["id"];
        $change_qty = (int)$_POST["change_qty"];
        if ($change_qty > 0) {
            mysqli_query($conn, "UPDATE medicines SET quantity = GREATEST(0, quantity - $change_qty) WHERE id=$id");
            mysqli_query($conn, "INSERT INTO stock_logs (medicine_id, change_qty, action, date) VALUES ($id, $change_qty, 'stock_out', CURDATE())");
            $msg = "Stock removed successfully.";
        }
    }
}

// Fetch all suppliers for dropdown
$suppResult = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY name ASC");
$suppliers = [];
while ($s = mysqli_fetch_assoc($suppResult)) $suppliers[] = $s;

// Fetch all medicines for table
$medResult = mysqli_query($conn, "SELECT m.*, s.name as supplier_name FROM medicines m LEFT JOIN suppliers s ON m.supplier_id=s.id ORDER BY m.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Apotheca</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>
    <nav class="navbar">
        <div class="brand">💊 Apotheca</div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="stock.php">Stock</a></li>
            <li><a href="stock_update.php">Stock Update</a></li>
            <li><a href="admin.php" class="active">Admin</a></li>
            <li><a href="suppliers.php">Suppliers</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="logout.php" class="text-danger">Logout</a></li>
        </ul>
    </nav>

    <div class="container container-wide">
        <div class="header-section">
            <h1>Admin Panel</h1>
            <p>Manage medicine inventory.</p>
        </div>
        
        <?php if ($msg) echo "<div class='alert ". (strpos($msg, 'Error') === false ? 'alert-success' : 'alert-danger') ."'>$msg</div>"; ?>
        
        <div class="card mb-20">
            <h3>Add New Medicine</h3>
            <form action="admin.php" method="POST" class="add-med-form">
                <input type="hidden" name="action" value="add">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Medicine Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" required>
                            <option value="tablet">Tablet</option>
                            <option value="syrup">Syrup</option>
                            <option value="injection">Injection</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Batch No</label>
                        <input type="text" name="batch_no" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" value="0" required>
                    </div>
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date">
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_id">
                            <option value="">-- No Supplier --</option>
                            <?php foreach($suppliers as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group btn-group align-bottom">
                        <button type="submit" class="btn btn-primary">Add Medicine</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="search-bar mb-20" style="display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" id="adminSearchInput" placeholder="Search medicine by name..." class="search-input" style="flex:1; min-width:200px;">
            <select id="adminCategoryFilter" class="table-input" style="width:150px; padding:12px; border-radius:8px; border: 1px solid var(--border);">
                <option value="">All Categories</option>
                <option value="tablet">Tablet</option>
                <option value="syrup">Syrup</option>
                <option value="injection">Injection</option>
            </select>
            <select id="adminSupplierFilter" class="table-input" style="width:150px; padding:12px; border-radius:8px; border: 1px solid var(--border);">
                <option value="">All Suppliers</option>
                <?php foreach($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="card">
            <h3>Current Medicines</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Batch</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Expiry</th>
                            <th>Supplier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="adminTableBody">
                        <?php while ($row = mysqli_fetch_assoc($medResult)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['batch_no'] ?? '--') ?></td>
                            <td><span class="badge badge-info"><?= ucfirst(htmlspecialchars($row['category'] ?? '')) ?></span></td>
                            <td><?= $row['quantity'] ?></td>
                            <td><?= $row['expiry_date'] ? date('M d, Y', strtotime($row['expiry_date'])) : '--' ?></td>
                            <td><?= htmlspecialchars($row['supplier_name'] ?? '--') ?></td>
                            <td class="action-cell">
                                <form action="admin.php" method="POST" onsubmit="return confirm('Delete this medicine?');" style="margin:0;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if(mysqli_num_rows($medResult) == 0): ?>
                            <tr><td colspan="9" class="text-center">No medicines found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="script.js?v=<?= time() ?>"></script>
</body>
</html>
