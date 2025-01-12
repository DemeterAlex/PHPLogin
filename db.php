<?php
$host = 'localhost';       // Hostname for the database server
$dbname = 'luxury_login';  // Name of the database you created
$user = 'root';            // Default username for XAMPP MySQL
$pass = '';                // No password for XAMPP MySQL by default
$port = 3307;              // Port for MySQL on XAMPP

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

