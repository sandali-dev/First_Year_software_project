<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medi – ආහාර</title>
<link rel="icon" href="images/titleLogo.png" type="image/png">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&family=Noto+Sans+Sinhala:wght@400;600&display=swap" rel="stylesheet">
<style>
/* ── RESET ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{width:100%;height:100%;overflow:hidden;-webkit-font-smoothing:antialiased}

/* ── TOKENS ── */
:root{
  --cream:    #faf7f2;
  --cream2:   #f3ede3;
  --sage:     #7a9e7e;
  --sage-d:   #4a7a50;
  --sage-l:   #d4e4d0;
  --terra:    #c4785a;
  --terra-l:  #f0d8cc;
  --gold:     #c8a96e;
  --gold-l:   #f0e4c8;
  --char:     #2d3028;
  --gray:     #8a8578;
  --muted:    #b8b2a8;
  --white:    #ffffff;
  --ff-d:     'Playfair Display', Georgia, serif;
  --ff-b:     'DM Sans', sans-serif;
  --ff-s:     'Noto Sans Sinhala', sans-serif;
}

body{
  font-family:var(--ff-b);
  color:var(--char);
  background:var(--cream);
}

/* ════════════════════════════════
   SLIDE BACKGROUNDS
════════════════════════════════ */
.bg-wrap{position:fixed;inset:0;z-index:0}
.bg-slide{
  position:absolute;inset:0;opacity:0;
  transition:opacity 1.4s ease;
}
.bg-slide.on{opacity:1}
.bg-0{background:radial-gradient(ellipse 65% 80% at 75% 50%,#e8dac8 0%,transparent 65%),radial-gradient(ellipse 50% 55% at 18% 78%,#d0e0cc 0%,transparent 60%),var(--cream)}
.bg-1{background:radial-gradient(ellipse 60% 75% at 72% 44%,#f0dece 0%,transparent 65%),radial-gradient(ellipse 45% 50% at 15% 65%,#ddd0c4 0%,transparent 60%),var(--cream2)}
.bg-2{background:radial-gradient(ellipse 70% 70% at 78% 52%,#d4e4d0 0%,transparent 65%),radial-gradient(ellipse 40% 48% at 22% 22%,#ede4d8 0%,transparent 55%),#f6f2ec}
.bg-3{background:radial-gradient(ellipse 58% 78% at 70% 48%,#ead8c0 0%,transparent 65%),radial-gradient(ellipse 48% 44% at 12% 58%,#c8d8c4 0%,transparent 55%),var(--cream)}

/* Grain texture */
.grain{
  position:fixed;inset:0;z-index:1;pointer-events:none;opacity:0.025;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  background-size:160px 160px;
}

/* ════════════════════════════════
   LAYOUT — full viewport grid
   Desktop: left content | right plate
   Mobile:  stacked, plate above text
════════════════════════════════ */
.page{
  position:fixed;inset:0;z-index:2;
  display:grid;
  grid-template-rows:auto 1fr auto;
  /* nav / main / footer-dots */
}

/* ── NAV ── */
.nav{
  padding:clamp(14px,2.5vh,24px) clamp(20px,4vw,52px);
  display:flex;align-items:center;justify-content:space-between;
  gap:12px;
}
.nav-logo{
  display:flex;align-items:center;gap:9px;flex-shrink:0;
}
.nav-logo img{
  height:clamp(28px,3.5vw,42px);width:auto;
  filter:drop-shadow(0 2px 6px rgba(122,158,126,0.22));
}
.nav-brand{
  font-family:var(--ff-d);
  font-size:clamp(1rem,2.2vw,1.4rem);
  font-weight:700;color:var(--char);
  letter-spacing:-0.3px;white-space:nowrap;
}
.nav-brand .s{
  font-family:var(--ff-s);
  font-size:0.68em;color:var(--sage);margin-left:3px;
}
.nav-right{display:flex;align-items:center;gap:clamp(10px,2vw,18px);flex-shrink:0}
.nav-link{
  font-size:clamp(0.78rem,1.3vw,0.88rem);font-weight:500;
  color:var(--gray);text-decoration:none;
  transition:color 0.2s;white-space:nowrap;
}
.nav-link:hover{color:var(--sage-d)}
.nav-btn{
  background:var(--sage-d);color:var(--white);
  border:none;padding:clamp(7px,1.2vh,10px) clamp(16px,2.2vw,24px);
  border-radius:999px;
  font-family:var(--ff-b);font-size:clamp(0.78rem,1.3vw,0.88rem);font-weight:500;
  cursor:pointer;text-decoration:none;white-space:nowrap;
  transition:all 0.22s;
  box-shadow:0 4px 16px rgba(74,122,80,0.22);
}
.nav-btn:hover{background:var(--sage);transform:translateY(-1px);box-shadow:0 6px 22px rgba(74,122,80,0.3)}
.user-menu{position:relative}
.user-menu:focus-within .user-dropdown,
.user-menu:hover .user-dropdown{opacity:1;visibility:visible;transform:translateY(0)}
.nav-user{
  display:flex;align-items:center;gap:8px;background:var(--sage-l);color:var(--char);
  border:none;border-radius:999px;padding:clamp(7px,1.2vh,10px) clamp(14px,2vw,20px);
  font-family:var(--ff-b);font-size:clamp(0.78rem,1.3vw,0.88rem);font-weight:500;cursor:pointer;
}
.nav-user::before{content:'👤';font-size:0.88rem}
.nav-user::after{content:''}
.user-dropdown{
  position:absolute;right:0;top:calc(100% + 8px);min-width:160px;background:var(--white);
  border:1px solid var(--sage-l);border-radius:14px;box-shadow:0 8px 28px rgba(74,122,80,0.18);
  padding:8px;opacity:0;visibility:hidden;transform:translateY(-6px);transition:all 0.18s;z-index:20;
}
.user-dropdown a{
  display:block;padding:10px 12px;border-radius:10px;color:var(--char);text-decoration:none;
  font-size:0.86rem;font-weight:500;white-space:nowrap;
}
.user-dropdown a:hover{background:var(--sage-l);color:var(--sage-d)}

/* ── MAIN AREA ── */
.main{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:0;
  min-height:0; /* allow shrinking inside grid row */
  overflow:hidden;
}

/* LEFT — text content */
.left{
  display:flex;flex-direction:column;justify-content:center;
  padding:clamp(16px,3vh,40px) clamp(20px,4vw,64px);
  overflow:hidden;
}

/* RIGHT — plate */
.right{
  display:flex;align-items:center;justify-content:center;
  padding:clamp(12px,2vh,32px) clamp(12px,3vw,48px) clamp(12px,2vh,32px) 0;
  overflow:hidden;
}

/* ── TAG ── */
.slide-tag{
  display:inline-flex;align-items:center;gap:7px;
  font-size:clamp(0.6rem,1vw,0.72rem);font-weight:500;
  color:var(--terra);letter-spacing:2px;text-transform:uppercase;
  margin-bottom:clamp(8px,1.5vh,16px);
  opacity:0;transform:translateY(7px);
  transition:all 0.55s ease 0.1s;
  position:absolute; /* stacked */
}
.slide-tag.on{opacity:1;transform:translateY(0);position:relative}
.tag-line{width:20px;height:1.5px;background:var(--terra);border-radius:1px;flex-shrink:0}

/* ── HEADLINE ── */
.tags-wrap,.heads-wrap,.subs-wrap{position:relative;overflow:visible}
.tags-wrap{margin-bottom:clamp(6px,1vh,12px)}
.heads-wrap{
  margin-bottom:clamp(18px,3vh,30px);
}
/* .subs-wrap{margin-bottom:clamp(200px,3.5vh,36px)} */

.slide-head{
  font-family:var(--ff-d);
  font-size:clamp(1.7rem,4.5vw,3.8rem);
  font-weight:700;line-height:1.12;letter-spacing:-0.9px;
  color:var(--char);
  opacity:0;transform:translateY(14px);
  transition:all 0.65s cubic-bezier(0.34,1.1,0.64,1) 0.15s;
  position:absolute;
}
.slide-head.on{opacity:1;transform:translateY(0);position:relative}
.slide-head em{color:var(--sage-d);font-style:italic}
.slide-head .t{color:var(--terra)}

/* ── SUBTEXT ── */
.slide-sub{
  font-size:clamp(0.82rem,1.5vw,0.98rem);
  color:var(--gray);line-height:1.72;font-weight:300;
  max-width:440px;
  opacity:0;transform:translateY(10px);
  transition:all 0.6s ease 0.25s;
  position:absolute;
}
.slide-sub.on{opacity:1;transform:translateY(0);position:relative}

/* ── CTA ── */
.cta{
  display:flex;align-items:center;gap:clamp(10px,2vw,16px);
  flex-wrap:wrap;
  animation:fadeUp 0.7s ease 0.9s both;
}
@keyframes fadeUp{
  from{opacity:0;transform:translateY(10px)}
  to{opacity:1;transform:translateY(0)}
}
.btn-main{
  background:var(--sage-d);color:var(--white);
  border:none;
  padding:clamp(11px,1.8vh,15px) clamp(22px,3vw,38px);
  border-radius:999px;
  font-family:var(--ff-b);
  font-size:clamp(0.82rem,1.4vw,0.96rem);font-weight:500;
  cursor:pointer;text-decoration:none;
  display:inline-flex;align-items:center;gap:9px;
  transition:all 0.22s;
  box-shadow:0 6px 22px rgba(74,122,80,0.26);
  white-space:nowrap;
}
.btn-main:hover{background:var(--sage);transform:translateY(-2px);box-shadow:0 10px 28px rgba(74,122,80,0.32)}
.btn-main:active{transform:translateY(0)}
.arrow-wrap{
  width:24px;height:24px;border-radius:50%;
  background:rgba(255,255,255,0.22);
  display:flex;align-items:center;justify-content:center;
  font-size:0.82rem;transition:transform 0.18s;
  flex-shrink:0;
}
.btn-main:hover .arrow-wrap{transform:translateX(3px)}
.btn-ghost{
  font-size:clamp(0.78rem,1.3vw,0.88rem);font-weight:500;
  color:var(--gray);text-decoration:none;
  border-bottom:1px solid var(--muted);padding-bottom:1px;
  transition:all 0.2s;white-space:nowrap;
}
.btn-ghost:hover{color:var(--char);border-color:var(--char)}

/* ── PLATE ── */
.plate-wrap{
  width:min(clamp(180px,32vw,380px),55vh);
  height:min(clamp(180px,32vw,380px),55vh);
  position:relative;
  flex-shrink:0;
}
.plate-slide{
  position:absolute;inset:0;
  display:flex;align-items:center;justify-content:center;
  opacity:0;transform:scale(0.88) rotate(-5deg);
  transition:all 0.95s cubic-bezier(0.34,1.1,0.64,1);
}
.plate-slide.on{opacity:1;transform:scale(1) rotate(0deg)}
.plate-slide.bye{opacity:0;transform:scale(0.92) rotate(5deg)}

.plate-disc{
  width:78%;height:78%;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:clamp(3rem,8vw,6.5rem);
  animation:bobble 7s ease-in-out infinite;
  position:relative;z-index:1;
  box-shadow:0 20px 56px rgba(0,0,0,0.1),inset 0 2px 5px rgba(255,255,255,0.55);
}
.p0 .plate-disc{background:radial-gradient(circle at 34% 32%,var(--gold-l),var(--terra-l) 72%)}
.p1 .plate-disc{background:radial-gradient(circle at 34% 32%,#e4f0e0,#c8dcc4 72%)}
.p2 .plate-disc{background:radial-gradient(circle at 34% 32%,#f6ece0,#e8d4bc 72%)}

.plate-shadow{
  position:absolute;bottom:-10px;left:16%;right:16%;height:20px;
  background:radial-gradient(ellipse,rgba(45,48,40,0.1),transparent 70%);
  border-radius:50%;
}
.plate-ring{
  position:absolute;inset:-12px;border-radius:50%;
  border:1.5px dashed rgba(122,158,126,0.22);
  animation:bobble 7s ease-in-out infinite reverse;
}
.plate-ring2{
  position:absolute;inset:-26px;border-radius:50%;
  border:1px dashed rgba(122,158,126,0.1);
  animation:bobble 9s ease-in-out infinite;
}

/* orbiting bits */
.orbit{
  position:absolute;inset:-18px;
  border-radius:50%;
  animation:spin 20s linear infinite;
}
.p1 .orbit,.p2 .orbit{animation-direction:reverse;animation-duration:18s}
.orb{
  position:absolute;
  font-size:clamp(0.85rem,1.8vw,1.25rem);
  filter:drop-shadow(0 1px 3px rgba(0,0,0,0.07));
}
.orb:nth-child(1){top:0;left:50%;transform:translateX(-50%)}
.orb:nth-child(2){right:0;top:50%;transform:translateY(-50%)}
.orb:nth-child(3){bottom:0;left:50%;transform:translateX(-50%)}
.orb:nth-child(4){left:0;top:50%;transform:translateY(-50%)}

@keyframes bobble{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}

/* ── BOTTOM BAR ── */
.bottom-bar{
  padding:clamp(10px,2vh,20px) clamp(20px,4vw,52px);
  display:flex;align-items:center;justify-content:space-between;
  gap:12px;
}
.dots-row{display:flex;align-items:center;gap:7px}
.dot{
  width:6px;height:6px;border-radius:999px;
  background:var(--muted);cursor:pointer;
  transition:all 0.35s ease;
}
.dot.on{width:22px;background:var(--sage-d)}
.slide-num{
  font-size:clamp(0.68rem,1.1vw,0.75rem);
  color:var(--muted);letter-spacing:2px;font-weight:400;
  display: none;
}
.slide-num strong{color:var(--gray);font-size:0.9rem}

/* progress */
.prog-bar{
  position:fixed;bottom:0;left:0;height:2px;
  background:linear-gradient(90deg,var(--sage),var(--terra));
  z-index:10;transition:width 0.08s linear;
  border-radius:0 2px 2px 0;
}

/* ════════════════
   RESPONSIVE
════════════════ */

/* Tablet landscape — still side by side but tighter */
@media (max-width:900px){
  .slide-head{font-size:clamp(1.5rem,5vw,2.8rem)}
}

/* Tablet portrait & mobile — stack vertically */
@media (max-width:640px){
    .page{
    grid-template-rows:auto 1fr auto;
    }

    .main{
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    min-height:100%;
    padding-bottom:20px;
    }

    .right{
    flex:0 0 auto;
    }

    .left{
    flex:1;
    display:flex;
    flex-direction:column;
    justify-content:center;
    text-align:center;
    align-items:center;
    }

  .plate-wrap{
    width:clamp(120px,42vw,200px);
    height:clamp(120px,42vw,200px);
  }
  
  .slide-head{font-size:clamp(1.4rem,6.5vw,2.2rem);letter-spacing:-0.4px}
  .slide-sub{font-size:clamp(0.78rem,3.5vw,0.9rem)}
  .cta{gap:10px}
  .btn-main{font-size:clamp(0.78rem,3.5vw,0.9rem);padding:11px 22px}
  .bottom-bar{justify-content:center}
  .slide-num{display:none}
  .plate-disc{font-size:clamp(2.4rem,12vw,4rem)}
}


</style>
</head>
<body>

<!-- Slide backgrounds -->
<div class="bg-wrap">
  <div class="bg-slide bg-0 on"></div>
  <div class="bg-slide bg-1"></div>
  <div class="bg-slide bg-2"></div>
</div>

<!-- Grain -->
<div class="grain"></div>

<!-- Progress bar -->
<div class="prog-bar" id="prog"></div>

<!-- Page grid -->
<div class="page">

  <!-- NAV -->
  <nav class="nav">
    <div class="nav-logo">
      <img src="images/logo-removebg-preview.png" alt="MediAhara" onerror="this.style.display='none'">
      <span class="nav-brand">Medi<span class="s">ආහාර</span></span>
    </div>
    <div class="nav-right">
      <?php if(isset($_SESSION['user_name'])): ?>
        <div class="user-menu">
          <button type="button" class="nav-user"><?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'); ?></button>
          <div class="user-dropdown">
            <a href="userProfile.php">User Profile</a>
            <a href="logout.php">Logout</a>
          </div>
        </div>
      <?php else: ?>
        <a href="login.php" class="nav-link">Sign in</a>
        <a href="login.php" class="nav-btn">Get started</a>
      <?php endif; ?>
    </div>
  </nav>

  <!-- MAIN -->
  <div class="main">

    <!-- LEFT — text -->
    <div class="left">

      <!-- Tags -->
      <!-- <div class="tags-wrap">
        <div class="slide-tag on" id="tag-0"><div class="tag-line"></div>Locally rooted</div>
        <div class="slide-tag"    id="tag-1"><div class="tag-line"></div>Wisdom meets AI</div>
        <div class="slide-tag"    id="tag-2"><div class="tag-line"></div>Instant · Free</div>
      </div> -->

      <!-- Headlines -->
    <div class="heads-wrap">

        <h1 class="slide-head on" id="h-0">
            Personalized meals<br>
            for your <em>condition</em><br>
            <span class="t">and your lifestyle</span>
        </h1>

        <h1 class="slide-head" id="h-1">
            Sri Lankan foods<br>
            chosen for <em>your health</em><br>
            <span class="t">not random diets</span>
        </h1>

        <h1 class="slide-head" id="h-2">
            Get your full meal plan<br>
            in <em>seconds</em><br>
            <span class="t">safe, simple and smart</span>
        </h1>

    </div>

      <!-- CTA -->
      <div class="cta">
        <a href="Front_End.php" class="btn-main">
          Get my meal plan
          <span class="arrow-wrap">→</span>
        </a>
      </div>

    </div>

    <!-- RIGHT — plate -->
    <div class="right">
      <div class="plate-wrap">

        <div class="plate-slide p0 on" id="plate-0">
          <div class="plate-ring2"></div>
          <div class="plate-ring"></div>
          <div class="orbit">
            <span class="orb">🥬</span>
            <span class="orb">🌱</span>
            <span class="orb">🫘</span>
            <span class="orb">🌿</span>
          </div>
          <div class="plate-disc">🥗</div>
          <div class="plate-shadow"></div>
        </div>

        <div class="plate-slide p1" id="plate-1">
          <div class="plate-ring2"></div>
          <div class="plate-ring"></div>
          <div class="orbit">
            <span class="orb">🫖</span>
            <span class="orb">🌿</span>
            <span class="orb">🌸</span>
            <span class="orb">🍃</span>
          </div>
          <div class="plate-disc">🥘</div>
          <div class="plate-shadow"></div>
        </div>

        <div class="plate-slide p2" id="plate-2">
          <div class="plate-ring2"></div>
          <div class="plate-ring"></div>
          <div class="orbit">
            <span class="orb">🧄</span>
            <span class="orb">🫚</span>
            <span class="orb">🌾</span>
            <span class="orb">🍃</span>
          </div>
          <div class="plate-disc">🍵</div>
          <div class="plate-shadow"></div>
        </div>

      </div>
    </div>

  </div>

  <!-- BOTTOM BAR -->
  <div class="bottom-bar">
    <div class="dots-row">
      <div class="dot on" onclick="goTo(0)"></div>
      <div class="dot"    onclick="goTo(1)"></div>
      <div class="dot"    onclick="goTo(2)"></div>
    </div>
    <div class="slide-num"><strong id="cur">01</strong></div>
  </div>

</div>

<script>
const N=3, DUR=3200;
let c=0, pct=0, pt=null;
const prog=document.getElementById('prog');
const curEl=document.getElementById('cur');

function hide(i){
  ['tag','h','s'].forEach(p=>document.getElementById(`${p}-${i}`)?.classList.remove('on'));
  const pl=document.getElementById(`plate-${i}`);
  pl?.classList.remove('on'); pl?.classList.add('bye');
  document.querySelectorAll('.bg-slide')[i]?.classList.remove('on');
  document.querySelectorAll('.dot')[i]?.classList.remove('on');
  setTimeout(()=>pl?.classList.remove('bye'),1000);
}
function show(i){
  ['tag','h','s'].forEach(p=>document.getElementById(`${p}-${i}`)?.classList.add('on'));
  document.getElementById(`plate-${i}`)?.classList.add('on');
  document.querySelectorAll('.bg-slide')[i]?.classList.add('on');
  document.querySelectorAll('.dot')[i]?.classList.add('on');
  curEl.textContent=String(i+1).padStart(2,'0');
}
function goTo(n){
  hide(c);
  c=((n%N)+N)%N;
  show(c);
  clearInterval(pt); pct=0; prog.style.width='0%';
  tick();
}
function tick(){
  const step=100/(DUR/60);
  pt=setInterval(()=>{
    pct=Math.min(pct+step,100);
    prog.style.width=pct+'%';
    if(pct>=100){clearInterval(pt);goTo(c+1)}
  },60);
}
tick();
document.addEventListener('keydown',e=>{
  if(e.key==='ArrowRight')goTo(c+1);
  if(e.key==='ArrowLeft')goTo(c-1);
});
let tx=0;
document.addEventListener('touchstart',e=>tx=e.touches[0].clientX,{passive:true});
document.addEventListener('touchend',e=>{
  const d=tx-e.changedTouches[0].clientX;
  if(Math.abs(d)>44)d>0?goTo(c+1):goTo(c-1);
});
</script>
</body>
</html>
