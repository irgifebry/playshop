<?php
// config/database.php
$host = 'localhost';
$dbname = 'playshop_db';
$username = 'root';
$password = ''; // Kosongkan jika menggunakan XAMPP default

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>