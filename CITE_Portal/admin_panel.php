<?php
// admin_panel.php
require_once 'auth.php';
require_role('admin');
global $conn;

$user = current_user();

// Fetch all announcements
$ann = $conn->query("
    SELECT a.*, u.name AS author_name 
    FROM announcements a 
    LEFT JOIN users u ON a.created_by = u.id
    ORDER BY a.created_at DESC
");
$announcements = $ann ? $ann->fetch_all(MYSQLI_ASSOC) : [];

// Fetch all users
$users_result = $conn->query("
    SELECT id, username, name, role, created_at 
    FROM users 
    ORDER BY created_at DESC
");
$users = $users_result ? $users_result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | CampusConnect</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="panel-shell">
    <!-- Top bar just for admin (name + logout) -->
    <div class="panel-topbar">
        <div class="panel-title-group">
            <h1 class="panel-page-title">Admin Dashboard</h1>
            <p class="panel-page-subtitle">
                Overview of announcements and registered users.
            </p>
        </div>

        <div class="panel-user-actions">
            <span class="panel-user-name">
                <?= htmlspecialchars($user['name']) ?>
            </span>
            <a href="admin_login.php" class="panel-logout-btn">Logout</a>
        </div>
    </div>

    <main class="panel-main">
        <!-- ANNOUNCEMENTS SECTION -->
        <section class="card dashboard-section">
            <div class="panel-section-header">
                <h2>All Announcements</h2>
                <span class="panel-section-tag"><?= count($announcements) ?> total</span>
            </div>

            <?php if (empty($announcements)): ?>
                <p class="muted">No announcements posted yet.</p>
            <?php else: ?>
                <div class="dashboard-table-wrapper">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($announcements as $a): ?>
                                <tr>
                                    <td class="dashboard-cell-title">
                                        <?= htmlspecialchars($a['title']) ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-category-<?= htmlspecialchars($a['category']) ?>">
                                            <?= htmlspecialchars(ucfirst($a['category'])) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($a['author_name'] ?? 'Unknown') ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($a['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- USERS SECTION -->
        <section class="card dashboard-section">
            <div class="panel-section-header">
                <h2>Registered Users</h2>
                <span class="panel-section-tag"><?= count($users) ?> users</span>
            </div>

            <?php if (empty($users)): ?>
                <p class="muted">No users found.</p>
            <?php else: ?>
                <div class="dashboard-table-wrapper">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars($u['username']) ?></td>
                                    <td><?= htmlspecialchars($u['name']) ?></td>
                                    <td>
                                        <span class="badge badge-role-<?= htmlspecialchars($u['role']) ?>">
                                            <?= htmlspecialchars(ucfirst($u['role'])) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>
