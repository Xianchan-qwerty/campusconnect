<?php
require_once 'auth.php';
require_login();

if ($_SESSION['role'] !== 'admin') {
    exit("Unauthorized");
}

global $conn;

$id = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$id || !$action) {
    header("Location: admin_panel.php");
    exit();
}

if ($action === 'approve') {
    $stmt = $conn->prepare("UPDATE announcements SET status='approved' WHERE id=?");
} else {
    // reject = delete
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id=?");
}

$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: admin_panel.php");
exit();
