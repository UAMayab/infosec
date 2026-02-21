<?php
/**
 * Energía Marina - Database Configuration
 * CONFIDENCIAL - No distribuir
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'energia_marina');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");

// Session configuration (INSECURE - for educational purposes)
// Only set session options if session hasn't started yet
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 0);
    ini_set('session.use_strict_mode', 0);
    session_start();
}
?>
