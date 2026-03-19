<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    exit();
}
include "db.php";

$sql = "SELECT m.*, s.name as supplier_name 
        FROM medicines m 
        LEFT JOIN suppliers s ON m.supplier_id = s.id 
        ORDER BY m.expiry_date ASC, m.id ASC";
$result = mysqli_query($conn, $sql);

$medicines = [];
$stats = [
    "total" => 0,
    "low" => 0,
    "expired" => 0,
    "expiring" => 0
];

$today = date("Y-m-d");
$next_7_days = date("Y-m-d", strtotime("+7 days"));
$two_days_ago = date("Y-m-d", strtotime("-2 days"));

while ($row = mysqli_fetch_assoc($result)) {
    $medicines[] = $row;
    $stats["total"]++;

    if ((int)$row["quantity"] < (int)($row["reorder_level"] ?? 20)) {
        $stats["low"]++;
    }

    if ($row["expiry_date"] && $row["expiry_date"] < $today) {
        $stats["expired"]++;
    }
    elseif ($row["expiry_date"] && $row["expiry_date"] <= $next_7_days) {
        $stats["expiring"]++;
    }
}

$recent = [];
$recent_sql = "SELECT * FROM medicines ORDER BY created_at DESC";
$recent_result = mysqli_query($conn, $recent_sql);
if ($recent_result) {
    while ($row = mysqli_fetch_assoc($recent_result)) {
        $status = "Normal";

        if ($row["expiry_date"] && $row["expiry_date"] < $today) {
            $status = "Expired";
        }
        elseif ((int)$row["quantity"] < (int)($row["reorder_level"] ?? 20)) {
            $status = "Low Stock";
        }
        elseif ($row["expiry_date"] && $row["expiry_date"] <= $next_7_days) {
            $status = "Expiring Soon";
        }
        elseif (isset($row["created_at"]) && substr($row["created_at"], 0, 10) >= $two_days_ago) {
            $status = "New Medicine";
        }

        if ($status !== "Normal") {
            $recent[] = [
                "name" => $row["name"],
                "batch_no" => $row["batch_no"],
                "qty" => (int)$row["quantity"],
                "expiry_date" => $row["expiry_date"] ?? "N/A",
                "status" => $status
            ];
            if (count($recent) >= 6)
                break;
        }
    }
}

header("Content-Type: application/json");
echo json_encode([
    "medicines" => $medicines,
    "stats" => $stats,
    "recent" => $recent
]);
?>
