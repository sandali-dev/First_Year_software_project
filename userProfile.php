<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}

$userEmail   = $_SESSION['user_email']      ?? '';
$memberSince = $_SESSION['profile_created'] ?? 'Today';
$userId      = (int) ($_SESSION['user_id']  ?? 0);

// ── Load recommendation history from the database ─────────────
$recommendationHistory = [];
$totalSearches         = 0;

if ($userId > 0) {
    $stmt = $conn->prepare(
        "SELECT conditions, foods, created_at
           FROM recommendation_history
          WHERE user_id = ?
          ORDER BY created_at DESC"
    );
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $recommendationHistory[] = [
            'timestamp'  => $row['created_at'],
            'conditions' => array_map('trim', explode(',', $row['conditions'])),
            'foods'      => json_decode($row['foods'], true) ?? [],
        ];
    }
    $totalSearches = count($recommendationHistory);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - MedMeal</title>
    <link rel="icon" href="images/titleLogo.png" type="image/png" sizes="19x19">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&family=Noto+Sans+Sinhala:wght@400;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f2fbf7; color: #1a2e25; font-family: 'DM Sans', sans-serif; margin: 0; min-height: 100vh; }

        /* ── NAV ── */
        .nav { padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; background: rgba(242,251,247,0.92); backdrop-filter: blur(16px); border-bottom: 1px solid #d9f1e3; position: sticky; top: 0; z-index: 30; }
        .nav-logo { display: flex; align-items: center; gap: 9px; text-decoration: none; }
        .nav-logo img { height: 36px; width: auto; }
        .nav-brand { font-family: 'Playfair Display', serif; font-size: 1.25rem; font-weight: 700; color: #0f4f38; }
        .nav-brand .s { font-family: 'Noto Sans Sinhala', sans-serif; font-size: 0.68em; color: #019c78; margin-left: 3px; }
        .user-menu { position: relative; }
        .user-menu:focus-within .user-dropdown,
        .user-menu:hover .user-dropdown { opacity: 1; visibility: visible; transform: translateY(0); }
        .nav-user { display: flex; align-items: center; gap: 8px; background: #eafdf5; color: #0f4f38; border: 1px solid #b7ead5; border-radius: 999px; padding: 10px 16px; font-size: 0.9rem; font-weight: 500; cursor: pointer; font-family: 'DM Sans', sans-serif; }
        .nav-user::before { content: '👤'; font-size: 0.88rem; }
        .user-dropdown { position: absolute; right: 0; top: calc(100% + 8px); min-width: 160px; background: #fff; border: 1px solid #d9f1e3; border-radius: 14px; box-shadow: 0 16px 40px rgba(22,75,62,0.12); padding: 8px; opacity: 0; visibility: hidden; transform: translateY(-6px); transition: all 0.18s; z-index: 20; }
        .user-dropdown a { display: block; padding: 10px 12px; border-radius: 10px; color: #0f4f38; text-decoration: none; font-size: 0.9rem; font-weight: 500; white-space: nowrap; }
        .user-dropdown a:hover { background: #eef8f3; }

        /* ── MAIN CONTAINER ── */
        .profile-container { max-width: 960px; margin: 40px auto; padding: 24px; }

        /* ── HEADER CARD ── */
        .profile-header-card { background: #ffffff; border-radius: 28px; box-shadow: 0 8px 40px rgba(22,75,62,0.07); padding: 32px; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 20px; align-items: center; margin-bottom: 20px; }
        .profile-user { display: flex; align-items: center; gap: 18px; }
        .profile-avatar { width: 72px; height: 72px; border-radius: 20px; display: grid; place-items: center; background: #019c78; color: #fff; font-size: 2rem; font-weight: 800; flex-shrink: 0; }
        .profile-user h1 { margin: 0 0 4px; font-size: 1.8rem; color: #0f4f38; font-family: 'Playfair Display', serif; }
        .profile-summary { font-size: 0.95rem; color: #51655b; }
        .profile-actions { display: flex; flex-wrap: wrap; gap: 12px; }

        /* ── STATS ── */
        .profile-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
        .stat-card { padding: 22px; border-radius: 20px; background: #eef8f3; border: 1px solid #dcefe6; }
        .stat-card--accent { background: #eafdf5; border-color: #b7ead5; }
        .stat-label { margin: 0 0 10px; font-size: 0.9rem; color: #617a6e; }
        .stat-value { margin: 0; font-size: 1.65rem; font-weight: 800; color: #0f4f38; }

        /* ── HISTORY CARD ── */
        .history-card { border-radius: 22px; border: 1px solid #d9f1e3; padding: 28px; background: #f7fffb; }
        .history-card h2 { font-family: 'Playfair Display', serif; font-size: 1.3rem; color: #0f4f38; margin: 0 0 20px; }
        .history-item { display: flex; flex-direction: column; padding: 18px 20px; border-radius: 18px; background: #ffffff; border: 1px solid #e4f5ec; margin-bottom: 12px; width: 100%; text-align: left; cursor: pointer; transition: background 0.18s; }
        .history-item:last-child { margin-bottom: 0; }
        .history-item:hover { background: #f4fdf8; }
        .history-top { display: flex; justify-content: space-between; align-items: center; gap: 16px; width: 100%; }
        .history-meta { display: flex; flex-direction: column; gap: 6px; }
        .history-date { font-size: 0.9rem; color: #4f6b5a; }
        .history-tag { display: inline-flex; align-items: center; gap: 8px; padding: 5px 12px; border-radius: 999px; background: #e1f6e7; color: #17603f; font-size: 0.82rem; font-weight: 700; width: fit-content; }
        .history-toggle { color: #019c78; font-size: 0.88rem; font-weight: 700; white-space: nowrap; flex-shrink: 0; }
        .history-details { margin-top: 14px; padding-top: 14px; border-top: 1px solid #e6f0e8; }
        .food-list { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
        .food-chip { padding: 6px 12px; border-radius: 999px; background: #e1f6e7; color: #17603f; font-size: 0.88rem; }
        .history-details p { font-size: 0.88rem; color: #617a6e; }

        /* ── EMPTY STATE ── */
        .empty-state { text-align: center; padding: 40px 20px; color: #617a6e; font-size: 0.95rem; }
        .empty-state .empty-icon { font-size: 2.5rem; margin-bottom: 12px; }

        /* ── BUTTONS ── */
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 10px 18px; border-radius: 999px; border: none; cursor: pointer; font-size: 0.9rem; font-weight: 500; font-family: 'DM Sans', sans-serif; text-decoration: none; transition: all 0.18s; }
        .btn-primary { background: #019c78; color: #fff; }
        .btn-primary:hover { background: #017a5f; }
        .btn-secondary { background: #f2f7f4; color: #0f4f38; border: 1px solid #d4e7df; }
        .btn-secondary:hover { background: #e6f3ec; }

        @media (max-width: 820px) { .profile-stats-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 600px) {
            .profile-header-card { flex-direction: column; align-items: flex-start; }
            .profile-stats-grid { grid-template-columns: 1fr; }
            .nav { padding: 12px 16px; }
            .profile-container { padding: 16px; margin: 16px auto; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
    <a href="home.php" class="nav-logo">
        <img src="images/logo-removebg-preview.png" alt="" onerror="this.style.display='none'">
        <span class="nav-brand">Medi<span class="s">ආහාර</span></span>
    </a>
    <div class="user-menu">
        <button type="button" class="nav-user"><?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'); ?></button>
        <div class="user-dropdown">
            <a href="userProfile.php">User Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="profile-container">

    <!-- Header card -->
    <div class="profile-header-card">
        <div class="profile-user">
            <div class="profile-avatar"><?php echo strtoupper(substr(htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'), 0, 1)); ?></div>
            <div>
                <h1><?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="profile-summary"><?php echo $userEmail ? htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') : 'No email available'; ?></p>
            </div>
        </div>
        <div class="profile-actions">
            <a href="Front_End.php" class="btn btn-secondary">← Back to meal planner</a>
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="profile-stats-grid">
        <div class="stat-card">
            <p class="stat-label">Total Searches</p>
            <p class="stat-value"><?php echo $totalSearches; ?></p>
        </div>
        <div class="stat-card stat-card--accent">
            <p class="stat-label">Account Status</p>
            <p class="stat-value">Active</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Member Since</p>
            <p class="stat-value" style="font-size:1.1rem;padding-top:6px;"><?php echo htmlspecialchars(date('M j, Y', strtotime($memberSince)), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>

    <!-- Recommendation History -->
    <div class="history-card">
        <h2>Recommendation History</h2>
        <?php if (!empty($recommendationHistory)): ?>
            <?php foreach ($recommendationHistory as $index => $entry): ?>
                <div class="history-item" onclick="toggleHistory(<?php echo $index; ?>)" role="button" tabindex="0"
                     onkeydown="if(event.key==='Enter'||event.key===' ')toggleHistory(<?php echo $index; ?>)"
                     aria-expanded="false" id="item-<?php echo $index; ?>">
                    <div class="history-top">
                        <div class="history-meta">
                            <span class="history-date">
                                <?php echo htmlspecialchars(date('M j, Y — g:i A', strtotime($entry['timestamp'])), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <span class="history-tag">
                                <?php echo htmlspecialchars(implode(', ', $entry['conditions']), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                        <span class="history-toggle" id="toggle-<?php echo $index; ?>">View ▾</span>
                    </div>
                    <div class="history-details" id="details-<?php echo $index; ?>" hidden>
                        <p>Recommended foods:</p>
                        <div class="food-list">
                            <?php foreach ($entry['foods'] as $food): ?>
                                <span class="food-chip"><?php echo htmlspecialchars($food, ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">🍽️</div>
                No recommendation history yet. Create your first meal plan to see it here!
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
function toggleHistory(index) {
    const details = document.getElementById('details-' + index);
    const toggle  = document.getElementById('toggle-' + index);
    const item    = document.getElementById('item-' + index);
    const isHidden = details.hasAttribute('hidden');
    if (isHidden) {
        details.removeAttribute('hidden');
        toggle.textContent = 'Hide ▴';
        item.setAttribute('aria-expanded', 'true');
    } else {
        details.setAttribute('hidden', '');
        toggle.textContent = 'View ▾';
        item.setAttribute('aria-expanded', 'false');
    }
}
</script>

</body>
</html>