<?php
include "db.php";

$search = mysqli_real_escape_string($conn, $_GET["query"]);

$sql = "SELECT name, unit_cost, category, in_stock
        FROM medicines
        WHERE name LIKE '%$search%'";

$result = mysqli_query($conn, $sql);

$medicines = [];
while ($row = mysqli_fetch_assoc($result)) {
    $medicines[] = $row;
}

header("Content-Type: application/json; charset=utf-8");
echo json_encode($medicines);
?>
