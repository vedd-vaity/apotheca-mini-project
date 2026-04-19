<?php
session_start();

// If user already logged in, redirect to dashboard
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}

include "db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and sanitize input
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password_raw = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check if passwords match
    if ($password_raw !== $confirm_password) {
        $error = "Passwords do not match.";
    } 
    // Validate password length (added improvement)
    elseif (strlen($password_raw) < 6) {
        $error = "Password must be at least 6 characters.";
    } 
    else {
        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email='$email'";
        $check_result = mysqli_query($conn, $check_sql);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $error = "Email already registered.";
        } else {

            // Hash password securely
            $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);
<<<<<<< HEAD
            // Determine role: Admin (1) if email starts with 'admin', otherwise Pharmacist (2)
            $role = (preg_match('/^admin/i', $email)) ? 1 : 2;

            // Insert new user with determined role
            $sql = "INSERT INTO users (email, password, role, is_active) 
                    VALUES ('$email', '$hashed_password', $role, 1)";
=======

            // Insert new user
            $sql = "INSERT INTO users (email, password, is_active) 
                    VALUES ('$email', '$hashed_password', 1)";

>>>>>>> 07abd109756d9000d37e6f9c3c6638a4b0eaace0
            if (mysqli_query($conn, $sql)) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                // Show actual DB error (useful for debugging)
                $error = "Registration failed: " . mysqli_error($conn);
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

    <!-- Prevent caching issues during development -->
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>

<body class="login-body">
    <div class="login-card">
        <h2>Apotheca</h2>
        <p>Create an Account</p>

        <?php 
        // Display error message
        if ($error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }

        // Display success message
        if ($success) {
            echo "<div class='alert alert-success'>$success</div>";
        }
        ?>

        <!-- Registration Form -->
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

            <!-- Redirect to login -->
            <div style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
                <p>
                    Already have an account? 
                    <a href="login.php" style="color: var(--primary); font-weight: 500; text-decoration: none;">
                        Login here
                    </a>
                </p>
            </div>

        </form>
    </div>
</body>
</html>