<?php
session_start();
include "db.php";

$email = mysqli_real_escape_string($conn, $_POST["email"]);
$password = $_POST["password"];

$sql = "SELECT * FROM users WHERE email = '$email' AND is_active = 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user["password_hash"])) {
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["role"] = $user["role"];
    header("Location: stock.html");
} else {
    header("Location: registration-form.html?error=1");
}
?>
