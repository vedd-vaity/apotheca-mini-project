<?php
include "db.php";

$email = mysqli_real_escape_string($conn, $_POST["email"]);
$password = password_hash($_POST["password"], PASSWORD_BCRYPT);
$role = mysqli_real_escape_string($conn, $_POST["role"]);

$sql = "INSERT INTO users (email, password_hash, role)
        VALUES ('$email', '$password', '$role')";

if (mysqli_query($conn, $sql)) {
    header("Location: registration-form.html?registered=1");
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
