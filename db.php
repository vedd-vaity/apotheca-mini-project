<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "medicine_inventory";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
