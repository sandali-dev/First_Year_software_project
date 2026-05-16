<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medi – ආහාර · Meal Plan</title>
<link rel="icon" href="images/titleLogo.png" type="image/png">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&family=Noto+Sans+Sinhala:wght@400;600&display=swap" rel="stylesheet">

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;-webkit-font-smoothing:antialiased}

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

/* ══════════════════════════════════════
   FIXED AMBIENT LAYER
══════════════════════════════════════ */
.bg-ambient{
  position:fixed;inset:0;z-index:0;pointer-events:none;
  background:
    radial-gradient(ellipse 65% 60% at 92% 8%,  #c8dfc8 0%, transparent 65%),
    radial-gradient(ellipse 55% 50% at 5%  75%,  #b8d8bc 0%, transparent 62%),
    radial-gradient(ellipse 40% 38% at 50% 52%,  #d4e8d0 0%, transparent 58%),
    radial-gradient(ellipse 30% 28% at 78% 88%,  #e0eed8 0%, transparent 55%),
    #edf5eb;
}


/* ── keyframes still needed for particles ── */
@keyframes spinCW  { from{transform:rotate(0deg)} to{transform:rotate(360deg)}  }
@keyframes spinCCW { from{transform:rotate(0deg)} to{transform:rotate(-360deg)} }

/* ── FLOATING PARTICLES (small food emojis) ── */
.particle-field{
  position:fixed;inset:0;z-index:1;pointer-events:none;
  overflow:hidden;
}
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

/* grain */
.grain{
  position:fixed;inset:0;z-index:2;pointer-events:none;opacity:0.022;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  background-size:160px 160px;
}

/* ══════════════════════════════════════
   NAV
══════════════════════════════════════ */
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
.nav-btn{
  background:var(--sage-d);color:var(--white);border:none;
  padding:clamp(7px,1.2vh,10px) clamp(16px,2.2vw,24px);border-radius:999px;
  font-family:var(--ff-b);font-size:0.88rem;font-weight:500;cursor:pointer;
  text-decoration:none;transition:all 0.22s;box-shadow:0 4px 16px rgba(74,122,80,0.22);
}
.nav-btn:hover{background:var(--sage);transform:translateY(-1px);box-shadow:0 6px 22px rgba(74,122,80,0.3)}
.user-menu{position:relative}
.user-menu:focus-within .user-dropdown,
.user-menu:hover .user-dropdown{opacity:1;visibility:visible;transform:translateY(0)}
.user-menu .nav-user{
  padding:7px 14px;border:none;font-family:var(--ff-b);cursor:pointer;
}
.user-menu .nav-user::before{content:'👤';font-size:0.88rem}
.user-menu .nav-user::after{content:''}
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

/* ══════════════════════════════════════
   HERO STRIP — above the form
══════════════════════════════════════ */
.hero-strip{
  position:relative;z-index:10;
  max-width:820px;margin:0 auto;
  padding:clamp(48px,8vh,100px) clamp(20px,4vw,40px) 0;
  animation:fadeUp 0.8s ease 0.2s both;
}
.hero-text{}
.hero-title{
  font-family:var(--ff-d);
  font-size:clamp(2rem,5vw,3.6rem);
  font-weight:700;line-height:1.08;letter-spacing:-1px;
  color:var(--char);margin-bottom:12px;
}
.hero-title em{color:var(--sage-d);font-style:italic}
.hero-sub{
  font-size:clamp(0.85rem,1.5vw,0.98rem);
  color:var(--gray);line-height:1.75;font-weight:300;max-width:620px;
}
.hero-sub .sin{
  font-family:var(--ff-s);font-size:0.88em;color:var(--muted);display:block;margin-top:4px;
}


/* ══════════════════════════════════════
   PAGE WRAP
══════════════════════════════════════ */
.page-wrap{
  position:relative;z-index:10;
  max-width:860px;margin:0 auto;
  padding:clamp(24px,4vh,40px) clamp(20px,4vw,40px) clamp(80px,12vh,140px);
  flex: 1;
}

.hero-strip,
.page-wrap,
footer{
  width:100%;
}

/* ══════════════════════════════════════
   FORM CARD
══════════════════════════════════════ */
.form-card{
  background:rgba(255,255,255,0.88);
  border-radius:var(--radius-xl);
  border:1px solid var(--sage-l);
  box-shadow:var(--shadow-md),0 0 0 4px rgba(184,216,188,0.14);
  padding:clamp(24px,4vh,40px) clamp(22px,4vw,40px);
  margin-bottom:clamp(12px,2.5vh,24px);
  animation:fadeUp 0.7s ease 0.3s both;
  backdrop-filter:blur(12px);
}
.form-section-label{
  font-size:0.68rem;font-weight:500;letter-spacing:2px;text-transform:uppercase;
  color:var(--sage-d);display:flex;align-items:center;gap:8px;
  margin-bottom:clamp(14px,2vh,22px);
}
.form-section-label::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--sage-l),transparent)}

/* toggle */
.toggle-row{display:flex;align-items:center;gap:10px;margin-bottom:clamp(18px,2.5vh,28px);flex-wrap:wrap}
.mode-btn{
  padding:8px 20px;border-radius:999px;border:1.5px solid var(--sage-l);background:transparent;
  font-family:var(--ff-b);font-size:0.82rem;font-weight:500;color:var(--gray);cursor:pointer;transition:all 0.22s;
}
.mode-btn:hover{border-color:var(--sage);color:var(--sage-d)}
.mode-btn.active{background:var(--sage-d);color:var(--white);border-color:var(--sage-d);box-shadow:0 4px 14px rgba(74,122,80,0.22)}

/* inputs */
.input-area{display:none;animation:fadeUp 0.4s ease both}
.input-area.visible{display:block}
.field-label{font-size:0.78rem;font-weight:500;color:var(--gray);letter-spacing:0.4px;margin-bottom:7px;display:block}
.text-input{
  width:100%;padding:12px 16px;border-radius:var(--radius-md);border:1.5px solid var(--sage-l);
  background:var(--sage-lll);font-family:var(--ff-b);font-size:0.9rem;color:var(--char);outline:none;
  transition:border-color 0.2s,box-shadow 0.2s;
}
.text-input:focus{border-color:var(--sage);box-shadow:0 0 0 3px rgba(95,143,101,0.14);background:var(--white)}
.text-input::placeholder{color:var(--muted)}
.select-input{
  width:100%;padding:12px 16px;border-radius:var(--radius-md);border:1.5px solid var(--sage-l);
  background:var(--sage-lll);font-family:var(--ff-b);font-size:0.88rem;color:var(--char);outline:none;
  appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%235f8f65' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:right 14px center;cursor:pointer;transition:border-color 0.2s,box-shadow 0.2s;
}
.select-input:focus{border-color:var(--sage);box-shadow:0 0 0 3px rgba(95,143,101,0.14);background-color:var(--white)}

.add-btn{
  display:inline-flex;align-items:center;gap:7px;margin-top:10px;
  background:var(--sage-d);color:var(--white);border:none;border-radius:999px;
  padding:9px 22px;font-family:var(--ff-b);font-size:0.84rem;font-weight:500;cursor:pointer;
  transition:all 0.22s;box-shadow:0 4px 14px rgba(74,122,80,0.2);
}
.add-btn:hover{background:var(--sage);transform:translateY(-1px);box-shadow:0 6px 18px rgba(74,122,80,0.28)}
.add-btn svg{width:14px;height:14px;flex-shrink:0}

.filters-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:clamp(14px,2vh,22px)}
@media(max-width:560px){.filters-grid{grid-template-columns:1fr}}

.disease-list{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
.disease-tag{
  display:inline-flex;align-items:center;gap:7px;
  background:var(--sage-ll);border:1.5px solid var(--sage-l);color:var(--sage-d);
  border-radius:999px;padding:5px 12px 5px 14px;font-size:0.8rem;font-weight:500;
  animation:chipPop 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
}
.disease-tag .remove-x{
  cursor:pointer;font-size:0.72rem;width:18px;height:18px;border-radius:50%;
  background:rgba(74,122,80,0.12);display:inline-flex;align-items:center;justify-content:center;
  transition:background 0.2s;flex-shrink:0;
}
.disease-tag .remove-x:hover{background:var(--terra-l);color:var(--terra)}

/* ══════════════════════════════════════
   DIVIDER
══════════════════════════════════════ */
.result-divider{
  display:none;align-items:center;gap:12px;
  margin:clamp(24px,4vh,40px) 0 clamp(16px,2.5vh,26px);
  animation:fadeUp 0.5s ease both;
}
.result-divider .rd-label{
  font-family:var(--ff-d);font-size:clamp(1.1rem,2.5vw,1.5rem);
  font-weight:700;font-style:italic;color:var(--char);white-space:nowrap;
}
.result-divider .rd-label span{color:var(--sage-d)}
.result-divider::before{content:'';flex:1;height:1px;background:linear-gradient(90deg,transparent,var(--sage-l))}
.result-divider::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--sage-l),transparent)}

/* ══════════════════════════════════════
   LOADING
══════════════════════════════════════ */
.loading-card{
  background:rgba(255,255,255,0.82);border-radius:var(--radius-xl);
  border:1px solid var(--sage-l);box-shadow:var(--shadow-sm);
  padding:clamp(28px,4vh,44px) clamp(22px,4vw,40px);
  display:flex;flex-direction:column;align-items:center;gap:20px;
  backdrop-filter:blur(12px);
}
.loading-headline{font-family:var(--ff-d);font-size:1.05rem;font-style:italic;color:var(--gray)}
.loading-steps{display:flex;flex-direction:column;gap:12px;width:100%;max-width:340px}
.loading-step{
  display:flex;align-items:center;gap:13px;font-size:0.86rem;font-weight:400;color:var(--char);
  opacity:0;transform:translateX(-12px);animation:stepIn 0.4s ease forwards;
}
.loading-step:nth-child(1){animation-delay:0.1s}
.loading-step:nth-child(2){animation-delay:0.55s}
.loading-step:nth-child(3){animation-delay:1.0s}
.loading-step:nth-child(4){animation-delay:1.45s}
@keyframes stepIn{to{opacity:1;transform:translateX(0)}}
.step-node{
  width:32px;height:32px;border-radius:50%;background:var(--sage-lll);border:1.5px solid var(--sage-l);
  display:flex;align-items:center;justify-content:center;font-size:0.9rem;flex-shrink:0;
  animation:nodeRing 1.4s ease infinite;
}
@keyframes nodeRing{
  0%,100%{box-shadow:0 0 0 0 rgba(74,122,80,0.28)}
  50%{box-shadow:0 0 0 6px rgba(74,122,80,0)}
}

/* ══════════════════════════════════════
   RESULTS
══════════════════════════════════════ */
#results{animation:fadeUp 0.45s ease both}
.profile-bar{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:clamp(18px,3vh,28px)}
.profile-chip{
  display:inline-flex;align-items:center;gap:5px;background:var(--sage-lll);
  border:1.5px solid var(--sage-l);color:var(--sage-d);border-radius:999px;padding:4px 13px;
  font-size:0.75rem;font-weight:500;
}
.profile-chip b{color:var(--sage-d);font-weight:600}

.res-heading{
  font-family:var(--ff-d);font-size:clamp(0.95rem,2vw,1.15rem);font-weight:700;font-style:italic;
  color:var(--char);display:flex;align-items:center;gap:10px;
  margin:clamp(20px,3vh,32px) 0 clamp(12px,2vh,20px);
}
.res-heading .rh-icon{font-style:normal;font-size:1rem}
.res-heading::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--sage-l),transparent)}

.skipped-notice{
  background:var(--gold-l);border:1.5px solid var(--gold);border-radius:var(--radius-md);
  padding:10px 16px;font-size:0.82rem;color:var(--char);margin-bottom:16px;line-height:1.6;
}

/* meal timeline */
.meal-timeline{position:relative;display:flex;flex-direction:column;margin-bottom:clamp(20px,3vh,32px)}
.meal-timeline::before{
  content:'';position:absolute;left:27px;top:36px;bottom:36px;width:1.5px;
  background:linear-gradient(180deg,var(--sage-l),var(--sage) 50%,var(--sage-l));border-radius:2px;z-index:0;
}
.meal-row{display:flex;align-items:flex-start;position:relative;z-index:1;padding:7px 0;animation:slideIn 0.45s cubic-bezier(0.34,1.56,0.64,1) both}
.meal-row:nth-child(1){animation-delay:0.05s}.meal-row:nth-child(2){animation-delay:0.12s}
.meal-row:nth-child(3){animation-delay:0.19s}.meal-row:nth-child(4){animation-delay:0.26s}
.meal-row:nth-child(5){animation-delay:0.33s}
@keyframes slideIn{from{opacity:0;transform:translateX(-14px)}to{opacity:1;transform:translateX(0)}}
.meal-node{
  width:54px;height:54px;border-radius:50%;display:flex;align-items:center;justify-content:center;
  font-size:1.3rem;flex-shrink:0;border:2.5px solid var(--white);box-shadow:var(--shadow-sm);
  position:relative;z-index:2;
}
.meal-bubble{
  flex:1;background:rgba(255,255,255,0.9);border-radius:var(--radius-md);
  padding:11px 16px;margin-left:14px;box-shadow:var(--shadow-sm);border-left:3px solid var(--sage-l);
  transition:transform 0.2s,box-shadow 0.2s;
}
.meal-bubble:hover{transform:translateX(4px);box-shadow:var(--shadow-md)}
.meal-time-label{font-size:0.62rem;font-weight:500;text-transform:uppercase;letter-spacing:1.8px;color:var(--sage-d);margin-bottom:3px}
.meal-name-text{font-size:0.9rem;font-weight:500;color:var(--char);line-height:1.45}

/* food guide */
.food-cols{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:clamp(16px,2.5vh,24px)}
@media(max-width:580px){.food-cols{grid-template-columns:1fr}}
.food-card{border-radius:var(--radius-lg);padding:18px 16px;border:1.5px solid transparent;position:relative;overflow:hidden;animation:fadeUp 0.4s ease both}
.food-card::after{content:attr(data-mark);position:absolute;bottom:-14px;right:4px;font-size:5rem;line-height:1;opacity:0.05;pointer-events:none}
.food-card.fc-green{background:linear-gradient(145deg,var(--sage-lll) 0%,var(--white) 55%);border-color:var(--sage-l)}
.food-card.fc-red{background:linear-gradient(145deg,var(--terra-ll) 0%,var(--white) 55%);border-color:var(--terra-l)}
.food-card-hdr{display:flex;align-items:center;gap:9px;margin-bottom:14px;padding-bottom:10px;border-bottom:1px dashed rgba(184,178,168,0.4)}
.food-card-ico{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:0.95rem;flex-shrink:0}
.fc-green .food-card-ico{background:var(--sage-l)}.fc-red .food-card-ico{background:var(--terra-l)}
.food-card-ttl{font-family:var(--ff-d);font-size:0.78rem;font-weight:700;font-style:italic}
.fc-green .food-card-ttl{color:var(--sage-d)}.fc-red .food-card-ttl{color:var(--terra)}
.tag-wrap{display:flex;flex-wrap:wrap;gap:6px}
.food-tag{display:inline-flex;align-items:flex-start;gap:5px;padding:5px 11px;border-radius:999px;font-size:0.77rem;font-weight:400;line-height:1.35;animation:chipPop 0.3s cubic-bezier(0.34,1.56,0.64,1) both}
.fc-green .food-tag{background:var(--sage-ll);color:var(--sage-d);border:1px solid var(--sage-l)}
.fc-red   .food-tag{background:var(--terra-ll);color:var(--terra);border:1px solid var(--terra-l)}
.tag-dot{margin-top:5px;width:4px;height:4px;border-radius:50%;flex-shrink:0}
.fc-green .tag-dot{background:var(--sage-d)}.fc-red .tag-dot{background:var(--terra)}
.food-tag:nth-child(1){animation-delay:0.04s}.food-tag:nth-child(2){animation-delay:0.09s}
.food-tag:nth-child(3){animation-delay:0.14s}.food-tag:nth-child(4){animation-delay:0.19s}
.food-tag:nth-child(5){animation-delay:0.24s}.food-tag:nth-child(6){animation-delay:0.29s}

/* nutrition */
.nutr-box{
  background:var(--sage-lll);border-radius:var(--radius-lg);
  padding:clamp(16px,2.5vh,24px) clamp(14px,2.5vw,24px);margin-bottom:clamp(16px,2.5vh,24px);
  box-shadow:var(--shadow-sm);border:1.5px solid var(--sage-l);border-top:3px solid var(--sage);
  animation:fadeUp 0.4s ease 0.15s both;
}
.nutr-dis-label{font-size:0.65rem;font-weight:500;text-transform:uppercase;letter-spacing:2px;color:var(--sage-d);margin:0 0 10px;display:flex;align-items:center;gap:6px}
.nutr-dis-label::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--sage);flex-shrink:0}
.nutr-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:8px;margin-bottom:14px}
.nutr-grid:last-child{margin-bottom:0}
.nutr-pill{background:rgba(255,255,255,0.78);border:1.5px solid var(--sage-l);border-radius:var(--radius-md);padding:10px 12px;display:flex;flex-direction:column;gap:2px;animation:chipPop 0.3s cubic-bezier(0.34,1.56,0.64,1) both}
.nutr-lbl{font-size:0.62rem;font-weight:500;text-transform:uppercase;letter-spacing:1.5px;color:var(--sage-d)}
.nutr-val{font-size:0.84rem;font-weight:500;color:var(--char);line-height:1.3}

/* tip banner */
.tip-banner{
  background:linear-gradient(135deg,var(--sage-d) 0%,var(--sage) 100%);
  border-radius:var(--radius-lg);padding:clamp(16px,2.5vh,22px) clamp(18px,3vw,24px);
  color:var(--white);font-size:0.9rem;font-weight:300;line-height:1.7;margin-bottom:20px;
  display:flex;gap:14px;align-items:flex-start;animation:fadeUp 0.4s ease 0.2s both;
}
.tip-banner strong{display:block;margin-bottom:3px;font-size:0.68rem;font-weight:600;text-transform:uppercase;letter-spacing:2px;opacity:0.8}
.tip-icon{font-size:1.6rem;flex-shrink:0;line-height:1;margin-top:2px}

/* error */
.err-box{background:var(--terra-ll);border:1.5px solid var(--terra-l);border-radius:var(--radius-lg);padding:28px 22px;text-align:center;color:var(--terra);font-size:0.92rem}
.err-box .err-icon{font-size:2rem;margin-bottom:10px;display:block}

/* footer */
footer{
  position:relative;z-index:10;text-align:center;
  padding:clamp(18px,3vh,28px) 24px;font-size:0.75rem;color:var(--muted);
  border-top:1px solid var(--sage-l);background:rgba(223,240,222,0.5);
}
.footer-brand{color:var(--sage-d);font-family:var(--ff-d);font-style:italic}

/* shared keyframes */
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
@keyframes chipPop{from{transform:scale(0.72);opacity:0}to{transform:scale(1);opacity:1}}

/* responsive */
@media(max-width:640px){
  .deco-avocado-wrap{width:clamp(120px,38vw,180px);height:auto}
  .meal-timeline::before{left:20px}
  .meal-node{width:42px;height:42px;font-size:1.05rem}
  .meal-bubble{margin-left:10px;padding:9px 13px}
  .tip-banner{flex-direction:column;gap:8px}
}
</style>

<script>
function navigateToSignIn(){window.location.href='login.php'}
function setActive(id){document.querySelectorAll('.mode-btn').forEach(b=>b.classList.toggle('active',b.dataset.id===id))}
function showText(){
  document.getElementById('area-text').classList.add('visible');
  document.getElementById('area-combo').classList.remove('visible');
  document.getElementById('additionalFilters').style.display='grid';
  setActive('textBtn');
}
function showCombo(){
  document.getElementById('area-combo').classList.add('visible');
  document.getElementById('area-text').classList.remove('visible');
  document.getElementById('additionalFilters').style.display='grid';
  setActive('comboBtn');
}
let diseases=[],debounceTimer=null;
function AddDisease(){
  const tv=document.getElementById('diseaseInput').value.trim();
  const cv=document.getElementById('diseaseComboBox')?.value;
  const val=tv||cv||'';
  if(!val){alert('Please enter or select a condition');return}
  if(diseases.includes(val))return;
  diseases.push(val);
  const tag=document.createElement('div');tag.className='disease-tag';
  tag.innerHTML=`${val}<span class="remove-x" onclick="removeDisease('${val.replace(/'/g,"\\'")}',this)">✕</span>`;
  document.getElementById('diseaseList').appendChild(tag);
  document.getElementById('diseaseInput').value='';
  if(document.getElementById('diseaseComboBox'))document.getElementById('diseaseComboBox').value='';
  diseaseDescription();
}
function removeDisease(d,el){diseases=diseases.filter(x=>x!==d);el.parentElement.remove();diseaseDescription()}
function diseaseDescription(){
  const age=document.getElementById('Age_Group').value;
  const pref=document.getElementById('VegORnonVeg').value;
  const allergy=document.getElementById('allergies').value;
  if(diseases.length>0&&age&&pref){
    clearTimeout(debounceTimer);debounceTimer=setTimeout(()=>autoGenerate(diseases,age,pref,allergy),700);
  } else {
    document.getElementById('result-divider').style.display='none';
    document.getElementById('results').innerHTML='';
  }
}
async function autoGenerate(dList,age,pref,allergy){
  const el=document.getElementById('results');const div=document.getElementById('result-divider');
  div.style.display='flex';
  el.innerHTML=`<div class="loading-card">
    <div class="loading-headline">Crafting your personal meal plan…</div>
    <div class="loading-steps">
      <div class="loading-step"><div class="step-node">🔍</div><span>Reading your condition(s)</span></div>
      <div class="loading-step"><div class="step-node">🧠</div><span>Running nutrition rules</span></div>
      <div class="loading-step"><div class="step-node">🌿</div><span>Matching safe Sri Lankan meals</span></div>
      <div class="loading-step"><div class="step-node">✅</div><span>Preparing your meal plan</span></div>
    </div></div>`;
  div.scrollIntoView({behavior:'smooth',block:'start'});
  try{
    const res=await fetch('recommend.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({diseases:dList,age_group:age,preference:pref,allergy:allergy||''})});
    const data=await res.json();renderResults(data,el);
  }catch(e){
    el.innerHTML=`<div class="err-box"><span class="err-icon">⚠️</span><p>Connection error — make sure XAMPP is running.</p></div>`;
  }
}
function renderResults(data,el){
  if(!data.success){el.innerHTML=`<div class="err-box"><span class="err-icon">🩺</span><p>${esc(data.message)}</p></div>`;return}
  const ageMap={child:'Child',young:'Teenager',adult:'Adult',elderly:'Senior'};
  const prefMap={veg:'Vegetarian',nonveg:'Non-Vegetarian'};
  let h='';
  if(data.skipped?.length)h+=`<div class="skipped-notice">ℹ️ <strong>"${esc(data.skipped.join(', '))}"</strong> not yet supported — showing plan for supported condition(s) only.</div>`;
  h+=`<div class="profile-bar">`;
  (data.labels||[]).forEach(l=>h+=`<span class="profile-chip">🦠 <b>Condition:</b> ${esc(l)}</span>`);
  h+=`<span class="profile-chip">👤 <b>Age:</b> ${esc(ageMap[data.age]||data.age)}</span>`;
  h+=`<span class="profile-chip">🥗 <b>Diet:</b> ${esc(prefMap[data.preference]||data.preference)}</span>`;
  if(data.allergy)h+=`<span class="profile-chip">⚠️ <b>Allergy:</b> ${esc(data.allergy)}</span>`;
  h+=`</div>`;
  const mealDefs=[
    {icon:'🌅',label:'Breakfast',nodeBg:'linear-gradient(135deg,#f0e4c8,#f6ecd8)',lc:'#c8a96e',val:data.meals?.breakfast},
    {icon:'☀️',label:'Lunch',nodeBg:'linear-gradient(135deg,#d4e4d0,#e8f2e4)',lc:'#4a7a50',val:data.meals?.lunch},
    {icon:'🍎',label:'Evening Snack',nodeBg:'linear-gradient(135deg,#f0d8cc,#faeae0)',lc:'#c4785a',val:data.meals?.snack},
    {icon:'🌙',label:'Dinner',nodeBg:'linear-gradient(135deg,#d4e0f0,#e8eefc)',lc:'#5a7ab0',val:data.meals?.dinner},
    {icon:'🍵',label:'Beverage',nodeBg:'linear-gradient(135deg,#e0d4f0,#eee8fc)',lc:'#8a6aaa',val:data.meals?.beverage},
  ].filter(m=>m.val);
  if(mealDefs.length){
    h+=`<div class="res-heading"><span class="rh-icon">🍽️</span> Your Daily Meal Plan</div><div class="meal-timeline">`;
    mealDefs.forEach(m=>{h+=`<div class="meal-row"><div class="meal-node" style="background:${m.nodeBg}">${m.icon}</div><div class="meal-bubble" style="border-left-color:${m.lc}"><div class="meal-time-label" style="color:${m.lc}">${m.label}</div><div class="meal-name-text">${esc(m.val)}</div></div></div>`});
    h+=`</div>`;
  }
  if(data.recommended?.length||data.avoid?.length){
    h+=`<div class="res-heading"><span class="rh-icon">🥦</span> Food Guide</div><div class="food-cols">`;
    if(data.recommended?.length)h+=`<div class="food-card fc-green" data-mark="✓"><div class="food-card-hdr"><div class="food-card-ico">✅</div><span class="food-card-ttl">Highly Recommended</span></div><div class="tag-wrap">${data.recommended.map(r=>`<span class="food-tag"><span class="tag-dot"></span>${esc(r)}</span>`).join('')}</div></div>`;
    if(data.avoid?.length)h+=`<div class="food-card fc-red" data-mark="✕"><div class="food-card-hdr"><div class="food-card-ico">🚫</div><span class="food-card-ttl">Foods to Avoid</span></div><div class="tag-wrap">${data.avoid.map(a=>`<span class="food-tag"><span class="tag-dot"></span>${esc(a)}</span>`).join('')}</div></div>`;
    h+=`</div>`;
  }
  if(data.nutrition?.length){
    h+=`<div class="res-heading"><span class="rh-icon">📊</span> Nutrition Targets</div><div class="nutr-box">`;
    data.nutrition.forEach(row=>{
      if(row.disease)h+=`<div class="nutr-dis-label">${esc(row.disease)}</div>`;
      h+=`<div class="nutr-grid">`;
      (row.chips||[]).forEach((chip,i)=>{h+=`<div class="nutr-pill" style="animation-delay:${i*0.05}s">${chip.label?`<span class="nutr-lbl">${esc(chip.label)}</span>`:''}<span class="nutr-val">${esc(chip.value)}</span></div>`});
      h+=`</div>`;
    });
    h+=`</div>`;
  }
  if(data.tip)h+=`<div class="tip-banner"><span class="tip-icon">💡</span><div><strong>Health Tip</strong>${esc(data.tip)}</div></div>`;
  el.innerHTML=h;
}
function esc(s){if(!s)return'';return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;')}

/* ── Spawn floating emoji particles ── */
window.addEventListener('DOMContentLoaded',()=>{
  const field=document.querySelector('.particle-field');
  const items=['🥬','🌿','🍃','🥑','🥗','🌱','🫚','🧄','🫘','🌾','🍌', '🍉', '🍗','🥝'];
  function spawn(){
    const el=document.createElement('div');
    el.className='ptc';
    el.textContent=items[Math.floor(Math.random()*items.length)];
    const x=Math.random()*100;
    const dur=12+Math.random()*14;
    const dy=-(60+Math.random()*40);
    const dx=(Math.random()-0.5)*30;
    const rot=(Math.random()-0.5)*360;
    el.style.cssText=`left:${x}%;bottom:${-5+Math.random()*10}%;--ptc-y:${dy}vh;--ptc-x:${dx}vw;--ptc-r:${rot}deg;animation-duration:${dur}s;animation-delay:${Math.random()*dur}s;font-size:${0.8+Math.random()*0.7}rem`;
    field.appendChild(el);
    setTimeout(()=>el.remove(),(dur+4)*1000);
  }
  for(let i=0;i<22;i++)setTimeout(spawn,i*800);
  setInterval(spawn,1800);
});
</script>
</head>

<body>

<!-- Ambient background -->
<div class="bg-ambient"></div>

<!-- Floating emoji particles -->
<div class="particle-field"></div>

<!-- Grain -->
<div class="grain"></div>

<!-- NAV -->
<nav class="nav">
  <a href="home.php" class="nav-logo">
    <img src="images/logo-removebg-preview.png" alt="" onerror="this.style.display='none'">
    <span class="nav-brand">Medi<span class="s">ආහාර</span></span>
  </a>
  <div class="nav-right">
    <?php if(isset($_SESSION['user_name'])): ?>
      <div class="user-menu">
        <button type="button" class="nav-user"><?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'); ?></button>
        <div class="user-dropdown">
          <a href="userProfile.php">User Profile</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    <?php elseif(isset($_SESSION['user_email'])): ?>
      <div class="user-menu">
        <button type="button" class="nav-user"><?php echo htmlspecialchars(substr($_SESSION['user_email'], 0, 8), ENT_QUOTES, 'UTF-8'); ?>...</button>
        <div class="user-dropdown">
          <a href="userProfile.php">User Profile</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    <?php else: ?>
      <a href="login.php" class="nav-btn">Sign in</a>
    <?php endif; ?>
  </div>
</nav>

<!-- HERO STRIP -->
<div class="hero-strip">
  <div class="hero-text">
    <h1 class="hero-title">Find Your<br><em>meal plan </em></h1>
    <p class="hero-sub">
      Healthy Sri Lankan meals designed around your body, condition and daily life.
      <span class="sin">ඔබගේ සෞඛ්‍යයට ගැළපෙන ආහාර සැලසුම්</span>
    </p>
  </div>

  <!-- hero plate removed — avocado corner handles visual weight -->

</div>

<!-- FORM AREA -->
<div class="page-wrap">
  <div class="form-card">
    <div class="form-section-label">Search by:</div>
    <div class="toggle-row">
      <button class="mode-btn" data-id="comboBtn" onclick="showCombo()">Select from list</button>
      <button class="mode-btn" data-id="textBtn" onclick="showText()">Type a disease</button>
    </div>

    <div class="input-area" id="area-text">
      <label class="field-label" for="diseaseInput">Disease name or keyword</label>
      <input class="text-input" id="diseaseInput" type="text"
             placeholder="e.g. Diabetes, High Blood Pressure…"
             oninput="diseaseDescription()"
             onkeydown="if(event.key==='Enter'){AddDisease()}">
      <button class="add-btn" onclick="AddDisease()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
        Add Condition
      </button>
    </div>

    <div class="input-area" id="area-combo">
      <label class="field-label" for="diseaseComboBox">Select your condition</label>
      <select class="select-input" id="diseaseComboBox" onchange="diseaseDescription()">
        <option value="">-- Choose a condition --</option>
        <option value="Diabetes">Diabetes · දියවැඩියාව</option>
        <option value="High Blood Pressure">High Blood Pressure · අධි රුධිර පීඩනය</option>
        <option value="Heart Disease">Heart Disease · හෘද රෝග</option>
        <option value="Kidney Disease">Kidney Disease (CKD) · වකුගඩු රෝග</option>
        <option value="Cholesterol">Cholesterol · කොලෙස්ටරෝල්</option>
        <optgroup label="── Coming soon ──">
          <option disabled>Osteoporosis (coming soon)</option>
          <option disabled>Cancer (coming soon)</option>
          <option disabled>Liver Disease (coming soon)</option>
        </optgroup>
      </select>
      <button class="add-btn" onclick="AddDisease()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
        Add Condition
      </button>
    </div>

    <div class="disease-list" id="diseaseList"></div>

    <div class="filters-grid" id="additionalFilters" style="display:none;margin-top:22px">
      <div>
        <label class="field-label">Age Group</label>
        <select class="select-input" id="Age_Group" onchange="diseaseDescription()">
          <option value="">-- Age Group --</option>
          <option value="child">Child (0–12)</option>
          <option value="teen">Teenager (13–19)</option>
          <option value="adult">Adult (20–59)</option>
          <option value="senior">Senior (60+)</option>
        </select>
      </div>
      <div>
        <label class="field-label">Food Preference</label>
        <select class="select-input" id="VegORnonVeg" onchange="diseaseDescription()">
          <option value="">-- Food Type --</option>
          <option value="Vegetarian">Vegetarian · නිර්මාංශ</option>
          <option value="Non-Vegetarian">Non-Vegetarian · මාංසහාරී</option>
        </select>
      </div>
      <div>
        <label class="field-label">Allergy (optional)</label>
        <select class="select-input" id="allergies" onchange="diseaseDescription()">
          <option value="">-- No Allergy --</option>
          <option value="sesame">Sesame · තල</option>
          <option value="eggs">Eggs · බිත්තර</option>
          <option value="milk">Milk / Dairy · කිරි</option>
          <option value="peanuts">Peanuts · රටකජු</option>
          <option value="soy">Soy · සෝයා</option>
          <option value="wheat">Wheat · තිරිඟු</option>
          <option value="fish">Fish · මාළු</option>
          <option value="tree_nuts">Tree Nuts</option>
        </select>
      </div>
    </div>
  </div>

  <div id="diseaseDescription" style="display:none"></div>

  <div id="result-divider" class="result-divider">
    <div class="rd-label">Your <span>Meal Plan</span></div>
  </div>

  <div id="results"></div>
</div>

<footer>
  <span class="footer-brand">Medi<span style="font-family:var(--ff-s);font-size:0.9em;color:var(--muted)">ආහාර</span></span>
  &nbsp;·&nbsp; © 2026 MedMeal. All rights reserved.
</footer>

</body>
</html>
