<?php
include "db.php";

$sql = "SELECT name, unit_cost, category, in_stock, quantity FROM medicines";
$result = mysqli_query($conn, $sql);

$medicines = [];
while ($row = mysqli_fetch_assoc($result)) {
    $medicines[] = $row;
}

header("Content-Type: application/json; charset=utf-8");
echo json_encode($medicines);
?>
