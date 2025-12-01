<?php
require_once 'config.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$message) {
    header("Location: feedback.php?error=1");
    exit();
}

$stmt = $conn->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);
$stmt->execute();
$stmt->close();

header("Location: feedback.php?success=1");
exit();
