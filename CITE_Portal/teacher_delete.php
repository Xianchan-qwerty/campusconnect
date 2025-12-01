<?php
require_once 'auth.php';
require_role('teacher');

if (!isset($_GET['id'])) {
    header("Location: teacher_panel.php");
    exit();
}

$announcement_id = intval($_GET['id']);
$user = current_user();
$teacher_id = $user['id'];

// DELETE only if announcement belongs to this teacher
$stmt = $conn->prepare("
    DELETE FROM announcements
    WHERE id = ? AND created_by = ?
");
$stmt->bind_param("ii", $announcement_id, $teacher_id);
$stmt->execute();

header("Location: teacher_panel.php?deleted=1");
exit();
?>
