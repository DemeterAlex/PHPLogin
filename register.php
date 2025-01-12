<?php
global $pdo;
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $pin = $_POST['pin'];

    // Validate the PIN is a 4-digit number
    if (!preg_match('/^\d{4}$/', $pin)) {
        $error = "Pincode must be a 4-digit number.";
    } else {
        $pin_hashed = password_hash($pin, PASSWORD_BCRYPT); // Hash the PIN

        // Check if a user with the same email or username already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
        $stmt->execute(['email' => $email, 'username' => $username]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            if ($existingUser['email'] == $email) {
                $error = "Registration failed, email is already in use.";
            } elseif ($existingUser['username'] == $username) {
                $error = "Registration failed, username is already taken.";
            }
        } else {
            try {
                // Insert user into the database
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, pin) VALUES (:username, :email, :password, :pin)");
                $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password, 'pin' => $pin_hashed]);
                header("Location: index.php");
                exit;
            } catch (PDOException $e) {
                $error = "Registration failed: " . $e->getMessage();
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
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
</head>
<body>
<div class="form-container">
    <form method="POST">
        <h2>Register</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="pin" placeholder="Pincode (e.g., 1234)" required>
        <button type="submit">Register</button>
        <p>Already have an account? <a href="index.php">Login</a></p>
    </form>
</div>
</body>
</html>
