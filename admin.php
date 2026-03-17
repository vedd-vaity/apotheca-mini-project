<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: stock.html");

    exit();
}
include "db.php";

$message = "";

// ADD
if (isset($_POST["action"]) && $_POST["action"] === "add") {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $unit_cost = mysqli_real_escape_string($conn, $_POST["unit_cost"]);
    $category = mysqli_real_escape_string($conn, $_POST["category"]);
    $quantity = mysqli_real_escape_string($conn, $_POST["quantity"]);

    $sql = "INSERT INTO medicines (name, unit_cost, category, quantity) VALUES ('$name', '$unit_cost', '$category', '$quantity')";
    $message = mysqli_query($conn, $sql)
        ? "Medicine added."
        : "Error: " . mysqli_error($conn);
}

// UPDATE
if (isset($_POST["action"]) && $_POST["action"] === "update") {
    $id = mysqli_real_escape_string($conn, $_POST["id"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $unit_cost = mysqli_real_escape_string($conn, $_POST["unit_cost"]);
    $category = mysqli_real_escape_string($conn, $_POST["category"]);
    $quantity = mysqli_real_escape_string($conn, $_POST["quantity"]);

    $sql = "UPDATE medicines SET name='$name', unit_cost='$unit_cost', category='$category', quantity='$quantity' WHERE id='$id'";
    $message = mysqli_query($conn, $sql)
        ? "Medicine updated."
        : "Error: " . mysqli_error($conn);
}

// DELETE
if (isset($_POST["action"]) && $_POST["action"] === "delete") {
    $id = mysqli_real_escape_string($conn, $_POST["id"]);
    $sql = "DELETE FROM medicines WHERE id='$id'";
    $message = mysqli_query($conn, $sql)
        ? "Medicine deleted."
        : "Error: " . mysqli_error($conn);
}

// FETCH ALL for table display
$result = mysqli_query($conn, "SELECT * FROM medicines");
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
</head>
<body>
    <h2>Admin Panel — Medicine Management</h2>
    <a href="logout.php">Logout</a>

    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <!-- ADD FORM -->
    <fieldset>
        <legend>Add Medicine</legend>
        <form method="POST" action="admin.php">
            <input type="hidden" name="action" value="add">
            Name: <input type="text" name="name" required /><br /><br />
            Unit Cost: <input type="number" name="unit_cost" required /><br /><br />
            Category:
            <select name="category">
                <option value="tablet">Tablet</option>
                <option value="syrup">Syrup</option>
                <option value="injection">Injection</option>
            </select><br /><br />
            Quantity: <input type="number" name="quantity" required /><br /><br />
            <input type="submit" value="Add Medicine" />
        </form>
    </fieldset>

    <br />

    <!-- MEDICINES TABLE -->
    <h3>Current Medicines</h3>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Unit Cost</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <!-- UPDATE FORM per row -->
            <form method="POST" action="admin.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $row["id"] ?>">
                <td><?= $row["id"] ?></td>
                <td><input type="text" name="name" value="<?= $row[
                    "name"
                ] ?>" /></td>
                <td><input type="number" name="unit_cost" value="<?= $row[
                    "unit_cost"
                ] ?>" /></td>
                <td>
                    <select name="category">
                        <option value="tablet" <?= $row["category"] === "tablet"
                            ? "selected"
                            : "" ?>>Tablet</option>
                        <option value="syrup" <?= $row["category"] === "syrup"
                            ? "selected"
                            : "" ?>>Syrup</option>
                        <option value="injection" <?= $row["category"] ===
                        "injection"
                            ? "selected"
                            : "" ?>>Injection</option>
                    </select>
                </td>
                <td><input type="number" name="quantity" value="<?= $row[
                    "quantity"
                ] ?>" /></td>
                <td><input type="submit" value="Update" /></td>
            </form>

            <!-- DELETE FORM per row -->
            <td>
                <form method="POST" action="admin.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $row["id"] ?>">
                    <input type="submit" value="Delete" />
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
