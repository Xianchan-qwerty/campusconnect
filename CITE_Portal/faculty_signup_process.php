<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $name     = trim($_POST['name']);
    $password = $_POST['password'];

    if (!$username || !$name || !$password) {
        die("All fields are required.");
    }

    // Hash password
    $hash = hash('sha256', $password);

    // Insert into database
    $stmt = $conn->prepare("
        INSERT INTO users (username, name, password_hash, role)
        VALUES (?, ?, ?, 'teacher')
    ");
    $stmt->bind_param("sss", $username, $name, $hash);
    $stmt->execute();
    $stmt->close();

    // Redirect to faculty login
    header("Location: faculty_login.php?success=1");
    exit();
}
?>
