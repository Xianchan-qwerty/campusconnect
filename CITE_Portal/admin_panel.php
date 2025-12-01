<?php
require_once 'auth.php';
require_login();

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

global $conn;

// Fetch pending announcements
$pending_stmt = $conn->query("
    SELECT a.*, u.name AS teacher_name
    FROM announcements a
    LEFT JOIN users u ON a.created_by = u.id
    WHERE a.status = 'pending'
    ORDER BY a.created_at DESC
");

// Fetch approved announcements
$approved_stmt = $conn->query("
    SELECT a.*, u.name AS teacher_name
    FROM announcements a
    LEFT JOIN users u ON a.created_by = u.id
    WHERE a.status = 'approved'
    ORDER BY a.created_at DESC
");

// Fetch feedback
$feedback_stmt = $conn->query("
    SELECT * FROM feedback ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="panel-shell">

    <!-- TOP RIGHT BUTTONS -->
    <div style="display:flex; justify-content:flex-end; gap:10px; margin-bottom:20px;">
        <a href="index.php" class="btn-login">Home</a>
        <a href="admin_login.php" class="btn-login" style="background:#b91c1c;">Logout</a>
    </div>

    <div class="dashboard-main-header">
        <h1 class="dashboard-title">Admin Panel</h1>
        <p class="dashboard-subtitle">Manage announcements & monitor teacher activity</p>
    </div>

    <!-- PENDING ANNOUNCEMENTS -->
    <section class="dashboard-section card">
        <div class="dashboard-section-header">
            <h2>Pending Announcements</h2>
            <span class="dashboard-section-tag">Awaiting Review</span>
        </div>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Teacher</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($pending_stmt->num_rows === 0): ?>
                    <tr><td colspan="5" class="muted">No pending announcements.</td></tr>
                <?php else: ?>
                    <?php while ($p = $pending_stmt->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['title']) ?></td>
                            <td><?= htmlspecialchars($p['teacher_name']) ?></td>
                            <td>
                                <span class="badge badge-category-<?= $p['category'] ?>">
                                    <?= ucfirst($p['category']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y h:i A', strtotime($p['created_at'])) ?></td>
                            <td>
                                <a href="process_announcement.php?id=<?= $p['id'] ?>&action=approve"
                                   class="btn-edit">Approve</a> &nbsp;
                                <a href="process_announcement.php?id=<?= $p['id'] ?>&action=reject"
                                   class="btn-delete">Reject</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- APPROVED ANNOUNCEMENTS -->
    <section class="dashboard-section card">
        <div class="dashboard-section-header">
            <h2>Approved Announcements</h2>
            <span class="dashboard-section-tag">Published</span>
        </div>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Teacher</th>
                        <th>Category</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($approved_stmt->num_rows === 0): ?>
                    <tr><td colspan="4" class="muted">No approved announcements yet.</td></tr>
                <?php else: ?>
                    <?php while ($a = $approved_stmt->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['title']) ?></td>
                            <td><?= htmlspecialchars($a['teacher_name']) ?></td>
                            <td>
                                <span class="badge badge-category-<?= $a['category'] ?>">
                                    <?= ucfirst($a['category']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y h:i A', strtotime($a['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- FEEDBACK -->
    <section class="dashboard-section card">
        <div class="dashboard-section-header">
            <h2>Student Feedback</h2>
            <span class="dashboard-section-tag">Viewer Messages</span>
        </div>

 <table class="dashboard-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Message</th>
            <th>Date</th>
        </tr>
    </thead>

    <tbody>
    <?php if ($feedback_stmt->num_rows === 0): ?>
        <tr><td colspan="3" class="muted">No feedback yet.</td></tr>
    <?php else: ?>
        <?php while ($f = $feedback_stmt->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($f['name'] ?: 'Anonymous') ?></td>
                <td><?= nl2br(htmlspecialchars($f['message'])) ?></td>
                <td><?= date('M d, Y h:i A', strtotime($f['created_at'])) ?></td>
            </tr>
        <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
</table>

        </div>
    </section>

</div>

</body>
</html>
