<?php
$conn = mysqli_connect("localhost", "root", "", "medicine_inventory");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
