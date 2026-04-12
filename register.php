<?php
session_start();
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}
include "db.php";
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password_raw = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password_raw !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email='$email'";
        $check_result = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Email already registered.";
        } else {
            $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (email, password, is_active) VALUES ('$email', '$hashed_password', 1)";
            if (mysqli_query($conn, $sql)) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Apotheca</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body class="login-body">
    <div class="login-card">
        <h2>Apotheca</h2>
        <p>Create an Account</p>
        <?php 
        if ($error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        if ($success) {
            echo "<div class='alert alert-success'>$success</div>";
        }
        ?>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <div style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
                <p>Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 500; text-decoration: none;">Login here</a></p>
            </div>
        </form>
    </div>
</body>
</html>
