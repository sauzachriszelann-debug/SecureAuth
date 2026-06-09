<?php

// Database 


$host     = "sql113.infinityfree.com";
$dbname   = "if0_42134275_secure_auth_db";
$username = "if0_42134275  ";
$password = "SecureAuth";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set PDO error mode to exception for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
