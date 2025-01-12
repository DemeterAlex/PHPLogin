<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Database connection
require 'db.php'; // Ensure this contains your PDO connection

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch the user's role
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

// Debugging: Check if the user and role are fetched correctly
if (!$user) {
    die("Error: User not found.");
}

// If the user's role is Admin, redirect to the admin dashboard
if ($user['role'] === 'Admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If the "Remove Account" button is clicked
    try {
        // Delete the user from the database using their user ID
        $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $deleteStmt->execute(['id' => $user_id]);

        // Log out and redirect to the homepage
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $error = "Error occurred while deleting account: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>
</head>
<body>
<div class="dashboard-container">
    <h1>Welcome to the Dashboard</h1>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <!-- Form to remove the account -->
    <div class="form-container">
        <h2>Remove Your Account</h2>

        <form method="POST" action="dashboard.php">
            <button type="submit">Remove Account</button>
        </form>
    </div>

    <br>
    <a href="logout.php" class="btn">Logout</a>
</div>
</body>
</html>
