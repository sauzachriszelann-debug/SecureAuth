<!-- 
            // Generate a unique random salt
            // random_bytes(32) generates 32 cryptographically secure random bytes
            // bin2hex() converts those bytes to a readable 64-character hex string
            // This salt is unique per user, so identical passwords hash differently
            
            $salt = bin2hex(random_bytes(32));
-->
   

<?php

require_once 'db_connect.php';
require_once 'config.php';

$message = "";
$msgType = ""; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if (empty($username) || empty($password) || empty($confirm)) {
        $message = "All fields are required.";
        $msgType = "error";

    // Enforce username length limits (3–50 characters)
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $message = "Username must be 3–50 characters.";
        $msgType = "error";

    // Enforce minimum password length
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $msgType = "error";

    // Ensure both password fields match before hashing
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
        $msgType = "error";

    } else {

        // Check for duplicate username
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $message = "Username already taken. Please choose another.";
            $msgType = "error";

        } else {

            // Generate a unique random salt
            // random_bytes(32) generates 32 cryptographically secure random bytes
            // bin2hex() converts those bytes to a readable 64-character hex string
            // This salt is unique per user, so identical passwords hash differently
            $salt = bin2hex(random_bytes(32));

            // Hash the password
            // Combine: plain password + user's salt + global pepper (from config.php)
            // The pepper adds a second secret layer stored only in source code, not the DB
            $combined    = $password . $salt . PEPPER;
            $hashed_pass = hash(HASH_ALGO, $combined);  // SHA-256

            // Insert the new user into the database
            // Only store: username, hashed password, and salt
            // The plain password and pepper are NEVER stored in the database
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, hashed_pass, salt) VALUES (?, ?, ?)"
            );
            $stmt->execute([$username, $hashed_pass, $salt]);

            $message = "Account created! You may now sign in.";
            $msgType = "success";
        }
    }

}
?>