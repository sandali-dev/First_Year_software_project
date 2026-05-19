<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}

$userEmail   = $_SESSION['user_email']      ?? '';
$memberSince = $_SESSION['profile_created'] ?? date('Y-m-d');
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
<title>Medi - ආහාර · Profile</title>
<link rel="icon" href="images/titleLogo.png" type="image/png">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&family=Noto+Sans+Sinhala:wght@400;600&display=swap" rel="stylesheet">

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;-webkit-font-smoothing:antialiased}

/* ── TOKENS — identical to Front_End.php ── */
:root{
  --cream:    #f2f7f0;
  --sage:     #5f8f65;
  --sage-d:   #3a6b40;
  --sage-l:   #b8d8bc;
  --sage-ll:  #dff0de;
  --sage-lll: #eef8ed;
  --terra:    #c4785a;
  --terra-l:  #f0d8cc;
  --terra-ll: #fdf3ee;
  --gold:     #c8a96e;
  --gold-l:   #f0e4c8;
  --char:     #1e2e20;
  --gray:     #6a7d6c;
  --muted:    #9ab09c;
  --white:    #ffffff;
  --ff-d:     'Playfair Display', Georgia, serif;
  --ff-b:     'DM Sans', sans-serif;
  --ff-s:     'Noto Sans Sinhala', sans-serif;
  --radius-sm:10px; --radius-md:16px; --radius-lg:22px; --radius-xl:32px;
  --shadow-sm:0 2px 8px rgba(45,48,40,0.07);
  --shadow-md:0 6px 24px rgba(45,48,40,0.09);
  --shadow-lg:0 16px 48px rgba(45,48,40,0.13);
}

body{
  font-family:var(--ff-b);
  color:var(--char);
  background:#edf5eb;
  display:flex;
  flex-direction:column;
  min-height:100vh;
  overflow-x:hidden;
}

/* ── AMBIENT BG ── */
.bg-ambient{
  position:fixed;inset:0;z-index:0;pointer-events:none;
  background:
    radial-gradient(ellipse 65% 60% at 92% 8%,  #c8dfc8 0%, transparent 65%),
    radial-gradient(ellipse 55% 50% at 5%  75%,  #b8d8bc 0%, transparent 62%),
    radial-gradient(ellipse 40% 38% at 50% 52%,  #d4e8d0 0%, transparent 58%),
    radial-gradient(ellipse 30% 28% at 78% 88%,  #e0eed8 0%, transparent 55%),
    #edf5eb;
}

/* ── FLOATING PARTICLES ── */
.particle-field{position:fixed;inset:0;z-index:1;pointer-events:none;overflow:hidden}
.ptc{
  position:absolute;
  font-size:clamp(0.9rem,1.8vw,1.4rem);
  opacity:0;
  animation:ptcDrift linear infinite;
  will-change:transform;
}
@keyframes ptcDrift{
  0%  {opacity:0;transform:translateY(0) rotate(0deg) scale(0.7)}
  8%  {opacity:0.45}
  88% {opacity:0.25}
  100%{opacity:0;transform:translateY(var(--ptc-y)) translateX(var(--ptc-x)) rotate(var(--ptc-r)) scale(1.1)}
}

/* ── GRAIN ── */
.grain{
  position:fixed;inset:0;z-index:2;pointer-events:none;opacity:0.022;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  background-size:160px 160px;
}

/* ── NAV — identical to Front_End.php ── */
.nav{
  position:sticky;top:0;z-index:50;
  padding:clamp(14px,2.5vh,22px) clamp(20px,4vw,52px);
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  background:rgba(237,245,235,0.88);
  backdrop-filter:blur(20px);
  border-bottom:1px solid rgba(184,216,188,0.35);
  animation:navSlide 0.7s ease both;
}
@keyframes navSlide{from{opacity:0;transform:translateY(-14px)}to{opacity:1;transform:translateY(0)}}
.nav-logo{display:flex;align-items:center;gap:9px;flex-shrink:0;text-decoration:none}
.nav-logo img{height:clamp(26px,3.2vw,40px);width:auto;filter:drop-shadow(0 2px 6px rgba(122,158,126,0.22))}
.nav-brand{font-family:var(--ff-d);font-size:clamp(1rem,2.2vw,1.4rem);font-weight:700;color:var(--char);letter-spacing:-0.3px}
.nav-brand .s{font-family:var(--ff-s);font-size:0.68em;color:var(--sage);margin-left:3px}
.nav-right{display:flex;align-items:center;gap:clamp(10px,2vw,16px);flex-shrink:0}
.nav-user{
  display:flex;align-items:center;gap:7px;font-size:0.82rem;font-weight:500;color:var(--char);
  background:var(--sage-lll);border:1px solid var(--sage-l);border-radius:999px;padding:6px 14px 6px 10px;
}
.nav-user::before{content:'👤';font-size:0.88rem}
.user-menu{position:relative}
.user-menu:focus-within .user-dropdown,
.user-menu:hover .user-dropdown{opacity:1;visibility:visible;transform:translateY(0)}
.user-menu .nav-user{padding:7px 14px;border:none;font-family:var(--ff-b);cursor:pointer}
.user-menu .nav-user::before{content:'👤';font-size:0.88rem}
.user-dropdown{
  position:absolute;right:0;top:calc(100% + 8px);min-width:160px;background:var(--white);
  border:1px solid rgba(184,216,188,0.7);border-radius:14px;box-shadow:var(--shadow-md);
  padding:8px;opacity:0;visibility:hidden;transform:translateY(-6px);transition:all 0.18s;z-index:80;
}
.user-dropdown a{
  display:block;padding:10px 12px;border-radius:10px;color:var(--char);text-decoration:none;
  font-size:0.86rem;font-weight:500;white-space:nowrap;
}
.user-dropdown a:hover{background:var(--sage-lll);color:var(--sage-d)}

/* ── PAGE WRAP ── */
.page-wrap{
  position:relative;z-index:10;
  max-width:860px;margin:0 auto;
  padding:clamp(32px,5vh,56px) clamp(20px,4vw,40px) clamp(80px,12vh,140px);
  flex:1;
  width : 100%;
}

/* ── PAGE HEADER ── */
.page-header{
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;
  margin-bottom:clamp(24px,4vh,40px);
  animation:fadeUp 0.7s ease 0.1s both;
}
.page-title{
  font-family:var(--ff-d);
  font-size:clamp(1.8rem,4vw,2.8rem);
  font-weight:700;line-height:1.08;letter-spacing:-0.5px;color:var(--char);
}
.page-title em{color:var(--sage-d);font-style:italic}
.back-btn{
  display:inline-flex;align-items:center;gap:7px;
  background:var(--sage-lll);border:1.5px solid var(--sage-l);color:var(--sage-d);
  border-radius:999px;padding:9px 20px;
  font-family:var(--ff-b);font-size:0.86rem;font-weight:500;
  text-decoration:none;transition:all 0.2s;
}
.back-btn:hover{background:var(--sage-ll);transform:translateY(-1px)}

/* ── PROFILE HERO CARD ── */
.profile-hero{
  background:rgba(255,255,255,0.88);
  border-radius:var(--radius-xl);
  border:1px solid var(--sage-l);
  box-shadow:var(--shadow-md),0 0 0 4px rgba(184,216,188,0.14);
  padding:clamp(22px,3.5vh,36px) clamp(22px,4vw,40px);
  display:flex;align-items:center;gap:24px;flex-wrap:wrap;
  margin-bottom:20px;
  backdrop-filter:blur(12px);
  animation:fadeUp 0.7s ease 0.2s both;
}
.avatar{
  width:72px;height:72px;border-radius:20px;
  display:grid;place-items:center;
  background:linear-gradient(135deg,var(--sage-d),var(--sage));
  color:var(--white);font-size:2rem;font-weight:800;flex-shrink:0;
  box-shadow:0 8px 24px rgba(58,107,64,0.28);
}
.profile-name{
  font-family:var(--ff-d);font-size:clamp(1.3rem,3vw,1.9rem);
  font-weight:700;color:var(--char);margin-bottom:4px;
}
.profile-email{font-size:0.88rem;color:var(--gray);font-weight:300}

/* ── STATS GRID ── */
.stats-grid{
  display:grid;grid-template-columns:repeat(3,1fr);gap:14px;
  margin-bottom:20px;
  animation:fadeUp 0.7s ease 0.3s both;
}
.stat-card{
  background:rgba(255,255,255,0.82);
  border-radius:var(--radius-lg);
  border:1.5px solid var(--sage-l);
  box-shadow:var(--shadow-sm);
  padding:20px 18px;
  backdrop-filter:blur(8px);
  transition:transform 0.2s,box-shadow 0.2s;
}
.stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
.stat-card.accent{border-top:3px solid var(--sage);background:var(--sage-lll)}
.stat-label{
  font-size:0.65rem;font-weight:500;text-transform:uppercase;letter-spacing:2px;
  color:var(--sage-d);margin-bottom:10px;display:flex;align-items:center;gap:6px;
}
.stat-label::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--sage);flex-shrink:0}
.stat-value{font-size:1.6rem;font-weight:800;color:var(--char);line-height:1}
.stat-value.sm{font-size:1rem;padding-top:4px;font-weight:600}

/* ── SECTION HEADING — matches res-heading in Front_End.php ── */
.section-heading{
  font-family:var(--ff-d);
  font-size:clamp(0.95rem,2vw,1.15rem);
  font-weight:700;font-style:italic;color:var(--char);
  display:flex;align-items:center;gap:10px;
  margin:clamp(20px,3vh,32px) 0 clamp(12px,2vh,20px);
}
.section-heading .icon{font-style:normal;font-size:1rem}
.section-heading::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--sage-l),transparent)}

/* ── HISTORY ── */
.history-wrap{display:flex;flex-direction:column;gap:10px;animation:fadeUp 0.5s ease 0.4s both}

.history-item{
  background:rgba(255,255,255,0.88);
  border-radius:var(--radius-lg);
  border:1.5px solid var(--sage-l);
  box-shadow:var(--shadow-sm);
  padding:16px 20px;
  cursor:pointer;
  backdrop-filter:blur(8px);
  transition:transform 0.2s,box-shadow 0.2s,background 0.18s;
  animation:slideIn 0.4s ease both;
}
.history-item:hover{transform:translateX(4px);box-shadow:var(--shadow-md);background:rgba(255,255,255,0.96)}
@keyframes slideIn{from{opacity:0;transform:translateX(-14px)}to{opacity:1;transform:translateX(0)}}

.history-top{display:flex;justify-content:space-between;align-items:center;gap:16px}
.history-meta{display:flex;flex-direction:column;gap:6px}
.history-date{font-size:0.78rem;color:var(--gray);font-weight:400}
.history-tag{
  display:inline-flex;align-items:center;gap:6px;
  background:var(--sage-ll);border:1.5px solid var(--sage-l);color:var(--sage-d);
  border-radius:999px;padding:4px 13px;font-size:0.78rem;font-weight:600;width:fit-content;
}
.history-toggle{
  color:var(--sage-d);font-size:0.82rem;font-weight:600;white-space:nowrap;flex-shrink:0;
  background:var(--sage-lll);border:1px solid var(--sage-l);border-radius:999px;padding:4px 12px;
  transition:background 0.18s;
}
.history-item:hover .history-toggle{background:var(--sage-ll)}

.history-details{margin-top:14px;padding-top:14px;border-top:1px dashed rgba(184,216,188,0.6)}
.history-details-label{
  font-size:0.65rem;font-weight:500;text-transform:uppercase;letter-spacing:2px;
  color:var(--sage-d);margin-bottom:10px;
}
.food-list{display:flex;flex-wrap:wrap;gap:7px}
.food-tag{
  display:inline-flex;align-items:center;gap:5px;
  background:var(--sage-ll);border:1px solid var(--sage-l);color:var(--sage-d);
  border-radius:999px;padding:5px 12px;font-size:0.78rem;font-weight:400;
  animation:chipPop 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
}

/* ── EMPTY STATE ── */
.empty-state{
  background:rgba(255,255,255,0.82);
  border-radius:var(--radius-xl);border:1.5px solid var(--sage-l);
  box-shadow:var(--shadow-sm);
  padding:clamp(36px,6vh,56px) 24px;
  text-align:center;backdrop-filter:blur(8px);
}
.empty-icon{font-size:2.8rem;display:block;margin-bottom:14px}
.empty-title{font-family:var(--ff-d);font-size:1.1rem;font-style:italic;color:var(--char);margin-bottom:8px}
.empty-sub{font-size:0.88rem;color:var(--gray);font-weight:300;margin-bottom:22px}
.cta-btn{
  display:inline-flex;align-items:center;gap:8px;
  background:var(--sage-d);color:var(--white);border:none;border-radius:999px;
  padding:11px 26px;font-family:var(--ff-b);font-size:0.9rem;font-weight:500;
  text-decoration:none;transition:all 0.22s;box-shadow:0 4px 16px rgba(74,122,80,0.22);
}
.cta-btn:hover{background:var(--sage);transform:translateY(-1px);box-shadow:0 6px 22px rgba(74,122,80,0.3)}

/* ── FOOTER ── */
footer{
  position:relative;z-index:10;text-align:center;
  padding:clamp(18px,3vh,28px) 24px;font-size:0.75rem;color:var(--muted);
  border-top:1px solid var(--sage-l);background:rgba(223,240,222,0.5);
}
.footer-brand{color:var(--sage-d);font-family:var(--ff-d);font-style:italic}

/* ── KEYFRAMES ── */
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
@keyframes chipPop{from{transform:scale(0.72);opacity:0}to{transform:scale(1);opacity:1}}

/* ── RESPONSIVE ── */
@media(max-width:640px){
  .stats-grid{grid-template-columns:1fr 1fr}
  .profile-hero{flex-direction:column;align-items:flex-start}
}
@media(max-width:420px){.stats-grid{grid-template-columns:1fr}}
</style>
</head>

<body>

<div class="bg-ambient"></div>
<div class="particle-field"></div>
<div class="grain"></div>

<!-- NAV -->
<nav class="nav">
  <a href="home.php" class="nav-logo">
    <img src="images/logo-removebg-preview.png" alt="" onerror="this.style.display='none'">
    <span class="nav-brand">Medi<span class="s">ආහාර</span></span>
  </a>
  <div class="nav-right">
    <div class="user-menu">
      <button type="button" class="nav-user"><?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'); ?></button>
      <div class="user-dropdown">
        <a href="userProfile.php">User Profile</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>
</nav>

<!-- PAGE -->
<div class="page-wrap">

  <div class="page-header">
    <h1 class="page-title">Your <em>Profile</em></h1>
    <a href="Front_End.php" class="back-btn">← Back to Meal Planner</a>
  </div>

  <!-- Profile card -->
  <div class="profile-hero">
    <div class="avatar"><?php echo strtoupper(substr(htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'), 0, 1)); ?></div>
    <div>
      <div class="profile-name"><?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'); ?></div>
      <div class="profile-email"><?php echo $userEmail ? htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') : 'No email on record'; ?></div>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card accent">
      <div class="stat-label">Total Searches</div>
      <div class="stat-value"><?php echo $totalSearches; ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Account Status</div>
      <div class="stat-value sm">Active</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Member Since</div>
      <div class="stat-value sm"><?php
        $ts = strtotime($memberSince);
        echo htmlspecialchars($ts ? date('M j, Y', $ts) : $memberSince, ENT_QUOTES, 'UTF-8');
      ?></div>
    </div>
  </div>

  <!-- History -->
  <div class="section-heading"><span class="icon">🍽️</span> Recommendation History</div>

  <?php if (!empty($recommendationHistory)): ?>
    <div class="history-wrap">
      <?php foreach ($recommendationHistory as $i => $entry): ?>
        <div class="history-item"
             id="item-<?php echo $i; ?>"
             onclick="toggleHistory(<?php echo $i; ?>)"
             role="button" tabindex="0"
             onkeydown="if(event.key==='Enter'||event.key===' ')toggleHistory(<?php echo $i; ?>)"
             aria-expanded="false"
             style="animation-delay:<?php echo $i * 0.07; ?>s">
          <div class="history-top">
            <div class="history-meta">
              <span class="history-date">🕐 <?php echo htmlspecialchars(date('M j, Y — g:i A', strtotime($entry['timestamp'])), ENT_QUOTES, 'UTF-8'); ?></span>
              <span class="history-tag">🦠 <?php echo htmlspecialchars(implode(', ', $entry['conditions']), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <span class="history-toggle" id="toggle-<?php echo $i; ?>">View ▾</span>
          </div>
          <div class="history-details" id="details-<?php echo $i; ?>" hidden>
            <div class="history-details-label">Recommended Foods</div>
            <div class="food-list">
              <?php foreach ($entry['foods'] as $fi => $food): ?>
                <span class="food-tag" style="animation-delay:<?php echo $fi * 0.05; ?>s"><?php echo htmlspecialchars($food, ENT_QUOTES, 'UTF-8'); ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  <?php else: ?>
    <div class="empty-state">
      <span class="empty-icon">🌿</span>
      <div class="empty-title">No meal plans yet</div>
      <p class="empty-sub">Your recommendation history will appear here after your first search.</p>
      <a href="Front_End.php" class="cta-btn">Create your first meal plan →</a>
    </div>
  <?php endif; ?>

</div>

<footer>
  <span class="footer-brand">Medi<span style="font-family:var(--ff-s);font-size:0.9em;color:var(--muted)">ආහාර</span></span>
  &nbsp;·&nbsp; © 2026 MedMeal. All rights reserved.
</footer>

<script>
function toggleHistory(i) {
  const details = document.getElementById('details-' + i);
  const toggle  = document.getElementById('toggle-' + i);
  const item    = document.getElementById('item-' + i);
  const hidden  = details.hasAttribute('hidden');
  if (hidden) {
    details.removeAttribute('hidden');
    toggle.textContent = 'Hide ▴';
    item.setAttribute('aria-expanded', 'true');
  } else {
    details.setAttribute('hidden', '');
    toggle.textContent = 'View ▾';
    item.setAttribute('aria-expanded', 'false');
  }
}

/* Floating particles — same as Front_End.php */
window.addEventListener('DOMContentLoaded', () => {
  const field = document.querySelector('.particle-field');
  const items = ['🥬','🌿','🍃','🥑','🥗','🌱','🫚','🧄','🫘','🌾','🍌','🍉','🍗','🥝'];
  function spawn() {
    const el = document.createElement('div');
    el.className = 'ptc';
    el.textContent = items[Math.floor(Math.random() * items.length)];
    const x   = Math.random() * 100;
    const dur = 12 + Math.random() * 14;
    const dy  = -(60 + Math.random() * 40);
    const dx  = (Math.random() - 0.5) * 30;
    const rot = (Math.random() - 0.5) * 360;
    el.style.cssText = `left:${x}%;bottom:${-5+Math.random()*10}%;--ptc-y:${dy}vh;--ptc-x:${dx}vw;--ptc-r:${rot}deg;animation-duration:${dur}s;animation-delay:${Math.random()*dur}s;font-size:${0.8+Math.random()*0.7}rem`;
    field.appendChild(el);
    setTimeout(() => el.remove(), (dur + 4) * 1000);
  }
  for (let i = 0; i < 22; i++) setTimeout(spawn, i * 800);
  setInterval(spawn, 1800);
});
</script>

</body>
</html>