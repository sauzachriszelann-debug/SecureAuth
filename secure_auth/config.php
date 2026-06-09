<?php
// ===========================================
// Security Configuration
// ===========================================

// PEPPER: A secret constant added to all passwords before hashing.
// - Stored ONLY in the source code, NEVER in the database.
// - Adds an extra layer of security even if the database is compromised.
// - Change this value to a long, random, unpredictable string in production.
define('PEPPER', '$ecur3P3pper!#Security1');

// Hashing algorithm used: SHA-256 via hash()
// For production, consider using password_hash() with PASSWORD_BCRYPT.
define('HASH_ALGO', 'sha256');
?>
