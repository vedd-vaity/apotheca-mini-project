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
    $password_raw = $_POST["password"];
    // Fetch user data with role ID
    $sql = "SELECT id, password, role FROM users WHERE email='$email' AND is_active=1";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password_raw, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["role"] = (int)$user["role"] ?? 2;

            // Set remember me cookie
            if (isset($_POST["remember"])) {
                setcookie(
                    "remember_user",
                    $user["id"],
                    time() + 60 * 60 * 24 * 1,
                    "/",
                );
            }
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
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
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
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
            <div style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
                <p>Don't have an account? <a href="register.php" style="color: var(--primary); font-weight: 500; text-decoration: none;">Register here</a></p>
            </div>
        </form>
    </div>
</body>
</html>
