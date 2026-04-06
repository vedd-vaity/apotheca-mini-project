<?php
session_start();
if (!isset($_SESSION["user_id"]) && isset($_COOKIE["remember_user"])) {
    $_SESSION["user_id"] = $_COOKIE["remember_user"];
    header("Location: dashboard.php");
    exit();
}
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}
include "db.php";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $sql = "SELECT id FROM users WHERE email='$email' AND password='$password' AND is_active=1";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION["user_id"] = $user["id"];
        if (isset($_POST["remember"])) {
            setcookie(
                "remember_user",
                $user["id"],
                time() + 60 * 60 * 24 * 30,
                "/",
            );
        }
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Apotheca</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <div class="login-card">
        <h2>Apotheca</h2>
        <p>Medicine Inventory System</p>
        <?php if ($error) {
            echo "<div class='alert alert-danger'>$error</div>";
        } ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="remember"> Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
