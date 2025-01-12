<?php
require 'db.php'; // Ensure this includes your PDO connection setup
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pin = $_POST['pin'];

    // Verify email and pin
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND pin = :pin");
    $stmt->execute(['email' => $email, 'pin' => $pin]);
    $user = $stmt->fetch();

    if ($user) {
        // User exists and pincode is correct
        session_start();
        $_SESSION['reset_user_id'] = $user['id'];
        header("Location: reset_password.php");
        exit;
    } else {
        $error = "Invalid email or pincode.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Forgot Password</title>
</head>
<body>
<div class="form-container">
    <form method="POST">
        <h2>Reset your Password</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="pin" placeholder="Pincode" required>
        <button type="submit">Reset Password</button>
        <p>Already have an account? <a href="index.php">Login</a></p>
    </form>
</div>
</body>
</html>
