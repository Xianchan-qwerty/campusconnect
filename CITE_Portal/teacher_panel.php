<?php
require_once 'auth.php';
require_role('teacher');
$user = current_user();

// -------------------------
// SAVE ANNOUNCEMENT
// -------------------------
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $category = trim($_POST['category']);
    $teacher_id = $user['id'];

    if ($title && $body) {
        $stmt = $conn->prepare("
            INSERT INTO announcements (title, body, category, created_by, status)
            VALUES (?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param("sssi", $title, $body, $category, $teacher_id);
        $stmt->execute();

        $success = "Your announcement has been submitted for approval!";
    }
}

// Fetch announcements created by this teacher
$stmt = $conn->prepare("SELECT * FROM announcements WHERE created_by = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$announcements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teacher Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="panel-shell">

    <!-- SUCCESS POPUP -->
    <?php if (!empty($success)): ?>
    <div id="popupSuccess"
         style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: #111827;
            color: white;
            padding: 16px 22px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
            font-size: 0.95rem;
            z-index: 9999;
            animation: fadeIn 0.3s ease;">
        <?= $success ?>
    </div>

    <script>
        setTimeout(() => {
            const pop = document.getElementById("popupSuccess");
            if (pop) pop.style.display = "none";
        }, 3000);
    </script>
    <?php endif; ?>


    <!-- TOP CONTROL ROW (CLEAN VERSION) -->
    <div style="
        display:flex;
        justify-content:flex-end;
        align-items:center;
        gap:18px;
        margin-bottom:20px;
    ">

        <!-- Clean welcome text -->
        <div style="
            font-size:0.9rem;
            font-weight:500;
            color:#374151;
        ">
            Welcome, <?= htmlspecialchars($user['name']) ?>
        </div>

        <a href="index.php" class="btn-login">Home</a>

        <a href="faculty_login.php"
           class="btn-login"
           style="background:#b91c1c;">
            Logout
        </a>

    </div>


    <!-- PAGE HEADER -->
    <div class="dashboard-main-header">
        <h1 class="dashboard-title">Teacher Announcements</h1>
        <p class="dashboard-subtitle">Create or manage your announcements</p>
    </div>


    <!-- CREATE ANNOUNCEMENT -->
    <section class="dashboard-section card">
        <div class="dashboard-section-header">
            <h2>Create Announcement</h2>
            <span class="dashboard-section-tag">Faculty</span>
        </div>

        <form method="POST">

            <div class="teacher-form-grid">
                <div class="teacher-input-block">
                    <label>Title</label>
                    <input type="text" name="title" class="input" required>
                </div>

                <div class="teacher-input-block">
                    <label>Category</label>
                    <select name="category" class="input">
                        <option value="academic">Academic</option>
                        <option value="events">Events</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <div class="teacher-input-block">
                <label>Body</label>
                <textarea name="body" class="input textarea" required></textarea>
            </div>

            <div class="teacher-form-actions">
                <button class="btn-submit">Create</button>
            </div>

        </form>
    </section>


    <!-- LIST OF ANNOUNCEMENTS -->
    <section class="dashboard-section card teacher-list-card">
        <h2 class="teacher-subtitle">Your Announcements</h2>

        <?php if (empty($announcements)): ?>
            <p class="muted">You have not created any announcements yet.</p>

        <?php else: ?>
            <?php foreach ($announcements as $a): ?>
                <div class="teacher-ann-item">
                    <div class="teacher-ann-header">
                        <h3><?= htmlspecialchars($a['title']) ?></h3>

                        <div class="teacher-ann-actions">
                            <a class="btn-edit" href="teacher_edit.php?id=<?= $a['id'] ?>">Edit</a>
                            <a class="btn-delete"
                               href="teacher_delete.php?id=<?= $a['id'] ?>"
                               onclick="return confirm('Delete this announcement?')">
                                Delete
                            </a>
                        </div>
                    </div>

                    <div class="teacher-ann-meta">
                        <?= date('M d, Y h:i A', strtotime($a['created_at'])) ?>
                        · <?= ucfirst(htmlspecialchars($a['category'])) ?>
                        · Status: <strong><?= ucfirst($a['status']) ?></strong>
                    </div>

                    <div class="teacher-ann-body">
                        <?= nl2br(htmlspecialchars($a['body'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </section>

</div>

</body>
</html>
