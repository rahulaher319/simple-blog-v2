<?php
// includes/db.php
$host = 'localhost';
$db   = 'blog_capstone';
$user = 'root';
$pass = ''; 

try {
    // A simple one-line connection string
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    // Set error mode so you can see if something goes wrong during development
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>