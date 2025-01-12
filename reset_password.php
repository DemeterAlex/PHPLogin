<?php
require 'db.php';
session_start();

if (!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pin = $_POST['pin'];
    $new_password = $_POST['new_password'];

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pin, $user['pin'])) { // Match the hashed PIN
        // Hash the new password
        $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

        // Update password
        $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
        $updateStmt->execute(['password' => $new_password_hash, 'email' => $email]);

        $success = "Password reset successfully!";
    } else {
        $error = "Invalid email or PIN.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Set New Password</title>
</head>
<body>
<div class="form-container">
    <form method="POST">
        <h2>Set a New Password</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php elseif ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <input type="password" name="password" placeholder="New Password" required>
        <button type="submit">Set Password</button>
    </form>
</div>
</body>
</html>
