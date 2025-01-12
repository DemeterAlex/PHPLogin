<?php
session_start();
require 'db.php';

// Check if the user is logged in and if they are an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    // If the user is not an Admin or not logged in, redirect to the index page
    header("Location: index.php");
    exit;
}

// Fetch all users
$stmt = $pdo->query("SELECT id, username, email, role, status FROM users");
$users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $user_id = $_POST['user_id'];

    if ($action === 'ban') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'Inactive' WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
    } elseif ($action === 'unban') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'Active' WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
    } elseif ($action === 'make_admin') {
        $stmt = $pdo->prepare("UPDATE users SET role = 'Admin' WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
    }
    header("Location: admin_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
</head>
<body>
<h1>Admin Dashboard</h1>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td><?php echo htmlspecialchars($user['status']); ?></td>
            <td>
                <?php if ($user['status'] === 'Active'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <input type="hidden" name="action" value="ban">
                        <button type="submit">Ban</button>
                    </form>
                <?php else: ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <input type="hidden" name="action" value="unban">
                        <button type="submit">Unban</button>
                    </form>
                <?php endif; ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit">Delete</button>
                </form>
                <?php if ($user['role'] !== 'Admin'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <input type="hidden" name="action" value="make_admin">
                        <button type="submit">Make Admin</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
