<?php
// Database connection settings
$host = '127.127.126.50';
$db   = 'baseball_club';
$user = 'root';
$pass = '';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    die("Database connection failed: " . $e->getMessage());
}
?>
