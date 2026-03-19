<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    exit();
}
include "db.php";

$query = mysqli_real_escape_string($conn, $_GET["query"] ?? "");
$supplier = (int)($_GET['supplier'] ?? 0);
$category = mysqli_real_escape_string($conn, $_GET['category'] ?? "");
$tag = mysqli_real_escape_string($conn, $_GET['tag'] ?? "");

$where = ["1=1"];
if ($query !== "") $where[] = "m.name LIKE '%$query%'";
if ($supplier > 0) $where[] = "m.supplier_id = $supplier";
if ($category !== "") $where[] = "m.category = '$category'";

$whereClause = implode(" AND ", $where);

$sql = "SELECT m.*, s.name as supplier_name,
            CASE 
                WHEN m.expiry_date IS NOT NULL AND m.expiry_date < CURDATE() THEN 1
                WHEN m.quantity < COALESCE(m.reorder_level, 20) THEN 2
                WHEN m.expiry_date IS NOT NULL AND m.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 3
                WHEN m.created_at IS NOT NULL AND m.created_at >= DATE_SUB(CURDATE(), INTERVAL 2 DAY) THEN 4
                ELSE 5
            END as priority
        FROM medicines m 
        LEFT JOIN suppliers s ON m.supplier_id = s.id 
        WHERE $whereClause 
        ORDER BY m.expiry_date ASC, m.name ASC";
        
$result = mysqli_query($conn, $sql);

$medicines = [];
while ($row = mysqli_fetch_assoc($result)) {
    $status = "Normal";
    if ($row["priority"] == 1) $status = "Expired";
    elseif ($row["priority"] == 2) $status = "Low Stock";
    elseif ($row["priority"] == 3) $status = "Expiring Soon";
    elseif ($row["priority"] == 4) $status = "New Medicine";
    
    $matchTag = true;
    if ($tag !== "") {
        if ($tag === "Expired" && $status !== "Expired") $matchTag = false;
        if ($tag === "Low" && $status !== "Low Stock") $matchTag = false;
        if ($tag === "Expiring" && $status !== "Expiring Soon") $matchTag = false;
        if ($tag === "New" && $status !== "New Medicine") $matchTag = false;
    }
    
    if ($matchTag) {
        $medicines[] = $row;
    }
}

header("Content-Type: application/json");
echo json_encode($medicines);
?>
