<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    exit();
}

include "db.php";

// Fetch medicines with supplier name
$sql = "SELECT m.*, s.name as supplier_name 
        FROM medicines m 
        LEFT JOIN suppliers s ON m.supplier_id = s.id 
        ORDER BY m.expiry_date ASC, m.id ASC";

$result = mysqli_query($conn, $sql);

$medicines = [];

// Initialize stats
$stats = [
    "total" => 0,
    "low" => 0,
    "expired" => 0,
    "expiring" => 0
];

// Date calculations
$today = date("Y-m-d");
$next_7_days = date("Y-m-d", strtotime("+7 days"));
$two_days_ago = date("Y-m-d", strtotime("-2 days"));

// Process medicines data
while ($row = mysqli_fetch_assoc($result)) {
    $medicines[] = $row;
    $stats["total"]++;

    // Check low stock
    if ((int)$row["quantity"] < 20) {
        $stats["low"]++;
    }

    // Check expired
    if ($row["expiry_date"] && $row["expiry_date"] < $today) {
        $stats["expired"]++;
    }
    // Check expiring soon
    elseif ($row["expiry_date"] && $row["expiry_date"] <= $next_7_days) {
        $stats["expiring"]++;
    }
}

$recent = [];

// Fetch recent medicines
$recent_sql = "SELECT * FROM medicines ORDER BY created_at DESC";
$recent_result = mysqli_query($conn, $recent_sql);

if ($recent_result) {
    while ($row = mysqli_fetch_assoc($recent_result)) {

        // Default status
        $status = "Normal";

        // Determine status
        if ($row["expiry_date"] && $row["expiry_date"] < $today) {
            $status = "Expired";
        }
        elseif ((int)$row["quantity"] < 20) {
            $status = "Low Stock";
        }
        elseif ($row["expiry_date"] && $row["expiry_date"] <= $next_7_days) {
            $status = "Expiring Soon";
        }
        elseif (isset($row["created_at"]) && substr($row["created_at"], 0, 10) >= $two_days_ago) {
            $status = "New Medicine";
        }

        // Add only non-normal items
        if ($status !== "Normal") {
            $recent[] = [
                "name" => $row["name"],
                "batch_no" => $row["batch_no"],
                "qty" => (int)$row["quantity"],
                "expiry_date" => $row["expiry_date"] ?? "N/A",
                "status" => $status
            ];

            // Limit to 6 items
            if (count($recent) >= 6) break;
        }
    }
}

// Return JSON response
header("Content-Type: application/json");

echo json_encode([
    "medicines" => $medicines,
    "stats" => $stats,
    "recent" => $recent
]);
?>  