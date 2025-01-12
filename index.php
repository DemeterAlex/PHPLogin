<?php
session_start();
require 'db.php';

$error = ''; // Initialize the error variable to avoid undefined variable warning

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Safely access the username and password inputs
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // Prepare the query to select user by username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);

        // Fetch the user data from the database
        $user = $stmt->fetch();

        // Check if user exists, verify password, and check if account is active
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'Active') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on user role
                if ($user['role'] === 'Admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit;
            } else {
                $error = "Your account is banned or inactive.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
<div class="form-container">
    <form method="POST" action="">
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="register.php">Register</a></p>
        <p>Forgotten password? <a href="forgot.php">Forgot</a></p>
    </form>
</div>
</body>
</html>
