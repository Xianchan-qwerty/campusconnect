<?php
// index.php
require_once 'auth.php'; // includes config + session
global $conn;

// Search + category
$search   = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? 'all';

$sql = "SELECT a.*, u.name AS author_name
        FROM announcements a
        LEFT JOIN users u ON a.created_by = u.id
        WHERE 1";

$params = [];
$types  = "";

if ($search !== '') {
    $sql     .= " AND (a.title LIKE ? OR a.body LIKE ?)";
    $like     = '%'.$search.'%';
    $params[] = $like;
    $params[] = $like;
    $types   .= "ss";
}

$validCategories = ['academic', 'events', 'sports', 'urgent'];

if ($category !== 'all' && in_array($category, $validCategories)) {
    $sql     .= " AND a.category = ?";
    $params[] = $category;
    $types   .= "s";
}

$sql .= " ORDER BY a.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$announcements = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Events for sidebar
$events_result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $events_result ? $events_result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Announcements | UPHS-Isabela Communication Platform</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-shell">
    <main class="page-main">
        <section class="card announcements-card">
            <div class="ann-header-row">
                <h1 class="ann-title">Announcements</h1>
            </div>

            <!-- Search -->
            <form method="get" class="ann-search-row">
                <div class="search-input-wrapper">
                    <span class="search-icon">🔍</span>
                    <input
                        type="text"
                        name="q"
                        placeholder="Search announcements..."
                        value="<?= htmlspecialchars($search) ?>"
                    >
                </div>

                <button type="submit" class="search-hidden-submit">Search</button>
            </form>

            <!-- Tabs -->
            <div class="ann-tabs">
                <?php
                $tabs = [
                    'all'      => 'All',
                    'academic' => 'Academic',
                    'events'   => 'Events',
                    'urgent'   => 'Urgent'
                ];
                foreach ($tabs as $key => $label):
                    $active = ($category === $key) || ($category === 'all' && $key === 'all');
                ?>
                    <a
                        href="?<?= http_build_query(['q' => $search, 'category' => $key]) ?>"
                        class="ann-tab <?= $active ? 'ann-tab-active' : '' ?>"
                    >
                        <?= htmlspecialchars($label) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Content -->
            <div class="ann-content">
                <?php if (empty($announcements)): ?>
                    <div class="ann-empty">
                        <div class="ann-empty-title">No announcements yet.</div>
                        <div class="ann-empty-subtitle">Check back later for updates!</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($announcements as $a): ?>
                        <article class="ann-item">
                            <div class="ann-item-header">
                                <h2><?= htmlspecialchars($a['title']) ?></h2>
                                <span class="ann-chip ann-chip-<?= htmlspecialchars($a['category']) ?>">
                                    <?= htmlspecialchars(ucfirst($a['category'])) ?>
                                </span>
                            </div>
                            <div class="ann-item-meta">
                                Posted by
                                <?= htmlspecialchars($a['author_name'] ?? 'Unknown') ?>
                                · <?= date('M d, Y h:i A', strtotime($a['created_at'])) ?>
                            </div>
                            <p class="ann-item-body"><?= nl2br(htmlspecialchars($a['body'])) ?></p>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <aside class="page-sidebar">
        <!-- Calendar card -->
        <section class="card calendar-card">
            <div class="calendar-header">
                <div class="calendar-title-wrap">
                    <span class="calendar-icon">📅</span>
                    <span class="calendar-title">Calendar</span>
                </div>
                <div class="calendar-month-nav">
                    <button type="button">&lt;</button>
                    <span class="calendar-month-label">
                        <?= date('F Y') ?>
                    </span>
                    <button type="button">&gt;</button>
                </div>
            </div>

            <div class="calendar-grid">
                <?php
                // simple static calendar grid (Sun-Sat)
                $daysShort = ['Su','Mo','Tu','We','Th','Fr','Sa'];
                foreach ($daysShort as $d): ?>
                    <div class="calendar-day-head"><?= $d ?></div>
                <?php endforeach; ?>

                <?php
                $year  = (int)date('Y');
                $month = (int)date('m');
                $firstDayOfMonth = strtotime("$year-$month-01");
                $startWeekday = (int)date('w', $firstDayOfMonth);
                $daysInMonth  = (int)date('t', $firstDayOfMonth);
                $todayDay     = (int)date('j');

                for ($i = 0; $i < $startWeekday; $i++): ?>
                    <div class="calendar-day empty"></div>
                <?php endfor; ?>

                <?php for ($d = 1; $d <= $daysInMonth; $d++):
                    $isToday = ($d === $todayDay);
                ?>
                    <div class="calendar-day <?= $isToday ? 'today' : '' ?>">
                        <?= $d ?>
                    </div>
                <?php endfor; ?>
            </div>
        </section>

        <!-- Upcoming events card -->
        <section class="card upcoming-card">
            <h2 class="upcoming-title">Upcoming Events</h2>

            <?php if (empty($events)): ?>
                <p class="muted">No upcoming events.</p>
            <?php else: ?>
                <ul class="upcoming-list">
                    <?php foreach ($events as $e): ?>
                        <li class="upcoming-item">
                            <div class="upcoming-dot"></div>
                            <div class="upcoming-content">
                                <div class="upcoming-event-title">
                                    <?= htmlspecialchars($e['title']) ?>
                                </div>
                                <div class="upcoming-event-meta">
                                    <?= $e['event_date'] ? date('M d, Y', strtotime($e['event_date'])) : '' ?>
                                    · <?= htmlspecialchars($e['location']) ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </aside>
</div>

</body>
</html>
