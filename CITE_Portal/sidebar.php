<?php
// sidebar.php
require_once 'auth.php';
$user = current_user();
?>
<aside class="sidebar">
    <nav>
        <a href="index.php" class="nav-link">Announcements</a>
        <a href="events.php" class="nav-link">Events Calendar</a>

        <?php if ($user && ($user['role'] === 'teacher' || $user['role'] === 'admin')): ?>
            <a href="teacher_panel.php" class="nav-link">Teacher Panel</a>
        <?php endif; ?>

        <?php if ($user && $user['role'] === 'admin'): ?>
            <a href="admin_panel.php" class="nav-link">Admin Panel</a>
        <?php endif; ?>
    </nav>
</aside>
