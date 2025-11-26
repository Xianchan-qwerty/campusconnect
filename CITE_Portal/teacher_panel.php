<?php
require_once 'auth.php';
require_role('teacher');
global $conn;

$user = current_user();
$user_id = $user['id'];

/* -------------------------
   DELETE ANNOUNCEMENT
-------------------------- */
if (isset($_GET['delete'])) {
    $delID = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ? AND created_by = ?");
    $stmt->bind_param("ii", $delID, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: teacher_panel.php");
    exit;
}

/* -------------------------
   EDIT ANNOUNCEMENT SUBMIT
-------------------------- */
$editData = null;

if (isset($_POST['edit_id'])) {
    $eid = intval($_POST['edit_id']);
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $body = trim($_POST['body']);

    $stmt = $conn->prepare("
        UPDATE announcements 
        SET title = ?, category = ?, body = ?, updated_at = NOW() 
        WHERE id = ? AND created_by = ?
    ");
    $stmt->bind_param("sssii", $title, $category, $body, $eid, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: teacher_panel.php");
    exit;
}

/* -------------------------
   LOAD EDIT FORM
-------------------------- */
if (isset($_GET['edit'])) {
    $editID = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ? AND created_by = ?");
    $stmt->bind_param("ii", $editID, $user_id);
    $stmt->execute();
    $editData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

/* -------------------------
   CREATE ANNOUNCEMENT
-------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_id'])) {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $body = trim($_POST['body']);

    if ($title && $body) {
        $stmt = $conn->prepare("
            INSERT INTO announcements (title, body, category, created_by)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("sssi", $title, $body, $category, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: teacher_panel.php");
    exit;
}

/* -------------------------
   GET TEACHER ANNOUNCEMENTS
-------------------------- */
$stmt = $conn->prepare("
    SELECT * FROM announcements
    WHERE created_by = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$my_posts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teacher Panel | CampusConnect</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="panel-shell">
    <!-- Top bar just for faculty (name + logout) -->
    <div class="panel-topbar">
        <div class="panel-title-group">
            <h1 class="panel-page-title">
                <?= $editData ? "Edit Announcement" : "Create Announcement" ?>
            </h1>
            <p class="panel-page-subtitle">
                Post updates that will appear on the CampusConnect announcements page.
            </p>
        </div>

        <div class="panel-user-actions">
            <span class="panel-user-name">
                <?= htmlspecialchars($user['name']) ?>
            </span>
            <a href="faculty_login.php" class="panel-logout-btn">Logout</a>
        </div>
    </div>

    <main class="panel-main">
        <!-- CREATE or EDIT ANNOUNCEMENT CARD -->
        <section class="card teacher-create-card">
            <form method="post" class="teacher-form">

                <?php if ($editData): ?>
                    <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
                <?php endif; ?>

                <div class="teacher-form-grid">
                    <div class="teacher-input-block">
                        <label>Title</label>
                        <input type="text" name="title" class="input"
                               value="<?= $editData ? htmlspecialchars($editData['title']) : '' ?>" required>
                    </div>

                    <div class="teacher-input-block">
                        <label>Category</label>
                        <select name="category" class="input">
                            <option value="academic" <?= $editData && $editData['category']=='academic' ? 'selected':'' ?>>Academic</option>
                            <option value="events"   <?= $editData && $editData['category']=='events' ? 'selected':'' ?>>Events</option>
                            <option value="urgent"   <?= $editData && $editData['category']=='urgent' ? 'selected':'' ?>>Urgent</option>
                        </select>
                    </div>
                </div>

                <div class="teacher-input-block">
                    <label>Body</label>
                    <textarea name="body" class="input textarea" required><?= $editData ? htmlspecialchars($editData['body']) : '' ?></textarea>
                </div>

                <div class="teacher-form-actions">
                    <button type="submit" class="btn-submit">
                        <?= $editData ? "Save Changes" : "Post Announcement" ?>
                    </button>

                    <?php if ($editData): ?>
                        <a href="teacher_panel.php" class="btn-cancel">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <!-- YOUR ANNOUNCEMENTS -->
        <section class="card teacher-list-card">
            <div class="panel-section-header">
                <h2>Your Announcements</h2>
                <span class="panel-section-tag"><?= count($my_posts) ?> posted</span>
            </div>

            <?php if (empty($my_posts)): ?>
                <p class="muted">You haven't posted any announcements yet.</p>
            <?php else: ?>
                <?php foreach ($my_posts as $post): ?>
                    <article class="teacher-ann-item">
                        <div class="teacher-ann-header">
                            <div>
                                <h3><?= htmlspecialchars($post['title']) ?></h3>
                                <div class="teacher-ann-meta">
                                    <?= date('M d, Y h:i A', strtotime($post['created_at'])) ?>
                                    ·
                                    <span class="badge badge-category-<?= htmlspecialchars($post['category']) ?>">
                                        <?= htmlspecialchars(ucfirst($post['category'])) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="teacher-ann-actions">
                                <a href="teacher_panel.php?edit=<?= $post['id'] ?>" class="btn-edit">✏ Edit</a>
                                <a href="teacher_panel.php?delete=<?= $post['id'] ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Delete this announcement?');">
                                   🗑 Delete
                                </a>
                            </div>
                        </div>

                        <p class="teacher-ann-body"><?= nl2br(htmlspecialchars($post['body'])) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>
