<?php
// config.php
// Database connection + session start

$host = 'localhost';
$db   = 'school_portal'; // change if needed
$user = 'root';          // change if needed
$pass = '';              // change if needed

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
