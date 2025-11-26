<?php // events placeholder ?><?php
// events.php
require_once 'auth.php';
global $conn;

$events_result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $events_result ? $events_result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events | CITE Portal</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="layout">
    <?php include 'sidebar.php'; ?>

    <main class="content">
        <h1>Events Calendar</h1>
        <?php if (empty($events)): ?>
            <p class="muted">No events scheduled.</p>
        <?php else: ?>
            <ul class="event-list large">
                <?php foreach ($events as $e): ?>
                    <li class="event-item">
                        <h2><?= htmlspecialchars($e['title']) ?></h2>
                        <p><?= nl2br(htmlspecialchars($e['description'])) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($e['location']) ?></p>
                        <p>
                            <strong>Date:</strong>
                            <?= $e['event_date'] ? date('M d, Y', strtotime($e['event_date'])) : '' ?>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
