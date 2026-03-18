<?php $noLayout = true; http_response_code(404); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>404 — BonSai</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --soil:   #1a1208;
      --bark:   #2d1f0e;
      --moss:   #3d5c2a;
      --leaf:   #5a8a3c;
      --sprout: #8fc45a;
      --mist:   #c8ddb8;
      --fog:    #e8f0df;
      --stone:  #9aaa8e;
    }

    html, body {
      height: 100%;
      background: var(--soil);
      color: var(--fog);
      font-family: 'DM Sans', sans-serif;
      overflow: hidden;
    }

    /* ── GROUND ── */
    .ground {
      position: fixed;
      bottom: 0; left: 0; right: 0;
      height: 120px;
      background: linear-gradient(to top, var(--bark), transparent);
      z-index: 0;
    }

    /* ── MIST LAYERS ── */
    .mist {
      position: fixed;
      left: -100%; width: 300%;
      height: 60px;
      background: radial-gradient(ellipse at center, rgba(200,221,184,0.08) 0%, transparent 70%);
      animation: drift var(--spd) ease-in-out infinite alternate;
      z-index: 0;
    }
    .mist-1 { bottom: 80px;  --spd: 12s; animation-delay: 0s; }
    .mist-2 { bottom: 140px; --spd: 18s; animation-delay: -6s; opacity: 0.6; }
    .mist-3 { bottom: 200px; --spd: 24s; animation-delay: -12s; opacity: 0.3; }
    @keyframes drift {
      from { transform: translateX(0); }
      to   { transform: translateX(8%); }
    }

    /* ── FIREFLIES ── */
    .firefly {
      position: fixed;
      width: 3px; height: 3px;
      border-radius: 50%;
      background: #c8ddb8;
      box-shadow: 0 0 6px 2px rgba(200,221,184,0.6);
      animation: float var(--dur) ease-in-out infinite, glow var(--gdur) ease-in-out infinite;
    }
    @keyframes float {
      0%,100% { transform: translate(0,0); }
      25%  { transform: translate(var(--dx1), var(--dy1)); }
      50%  { transform: translate(var(--dx2), var(--dy2)); }
      75%  { transform: translate(var(--dx3), var(--dy3)); }
    }
    @keyframes glow {
      0%,100% { opacity: 0.1; }
      50%     { opacity: 0.9; }
    }

    /* ── TREE SVG ── */
    .tree-wrap {
      position: fixed;
      bottom: 60px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1;
      animation: sway 6s ease-in-out infinite;
      transform-origin: bottom center;
    }
    @keyframes sway {
      0%,100% { transform: translateX(-50%) rotate(0deg); }
      30%     { transform: translateX(-50%) rotate(1deg); }
      70%     { transform: translateX(-50%) rotate(-0.8deg); }
    }

    /* ── CONTENT ── */
    .content {
      position: relative;
      z-index: 10;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding-bottom: 160px;
      text-align: center;
    }

    .code {
      font-family: 'Playfair Display', serif;
      font-size: clamp(100px, 18vw, 180px);
      font-weight: 700;
      line-height: 0.9;
      color: transparent;
      -webkit-text-stroke: 1px rgba(143,196,90,0.3);
      letter-spacing: -4px;
      animation: fadeIn 1s ease both;
      position: relative;
    }
    .code::after {
      content: '404';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, var(--sprout), var(--moss));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      opacity: 0.15;
    }

    .divider {
      width: 1px;
      height: 40px;
      background: linear-gradient(to bottom, transparent, var(--stone), transparent);
      margin: 20px auto;
      animation: fadeIn 1s 0.2s ease both;
    }

    .title {
      font-family: 'Playfair Display', serif;
      font-style: italic;
      font-size: clamp(18px, 3vw, 26px);
      color: var(--mist);
      letter-spacing: 1px;
      animation: fadeIn 1s 0.3s ease both;
    }

    .desc {
      margin-top: 12px;
      font-size: 14px;
      color: var(--stone);
      font-weight: 300;
      letter-spacing: 2px;
      text-transform: uppercase;
      animation: fadeIn 1s 0.4s ease both;
    }

    .btn-back {
      margin-top: 40px;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 12px 28px;
      border: 1px solid rgba(143,196,90,0.3);
      border-radius: 2px;
      color: var(--sprout);
      text-decoration: none;
      font-size: 13px;
      letter-spacing: 3px;
      text-transform: uppercase;
      background: rgba(90,138,60,0.05);
      transition: all 0.3s;
      animation: fadeIn 1s 0.6s ease both;
    }
    .btn-back:hover {
      background: rgba(90,138,60,0.15);
      border-color: rgba(143,196,90,0.6);
      box-shadow: 0 0 20px rgba(143,196,90,0.1);
      color: var(--sprout);
      gap: 14px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(12px); }
      to   { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<div class="ground"></div>
<div class="mist mist-1"></div>
<div class="mist mist-2"></div>
<div class="mist mist-3"></div>

<!-- Fireflies -->
<script>
  const ff = [
    {x:'15%',y:'30%',dx1:'20px',dy1:'-30px',dx2:'40px',dy2:'10px',dx3:'10px',dy3:'-50px',dur:'14s',gdur:'3s'},
    {x:'75%',y:'20%',dx1:'-15px',dy1:'20px',dx2:'-30px',dy2:'-10px',dx3:'5px',dy3:'30px',dur:'18s',gdur:'4s'},
    {x:'40%',y:'60%',dx1:'25px',dy1:'15px',dx2:'-10px',dy2:'30px',dx3:'20px',dy3:'-20px',dur:'12s',gdur:'2.5s'},
    {x:'85%',y:'50%',dx1:'-20px',dy1:'-25px',dx2:'10px',dy2:'15px',dx3:'-15px',dy3:'25px',dur:'16s',gdur:'3.5s'},
    {x:'25%',y:'70%',dx1:'15px',dy1:'-15px',dx2:'30px',dy2:'5px',dx3:'-5px',dy3:'20px',dur:'20s',gdur:'5s'},
    {x:'60%',y:'15%',dx1:'-25px',dy1:'20px',dx2:'15px',dy2:'-10px',dx3:'-10px',dy3:'30px',dur:'11s',gdur:'2s'},
  ];
  ff.forEach(f => {
    const el = document.createElement('div');
    el.className = 'firefly';
    el.style.cssText = `left:${f.x};top:${f.y};--dx1:${f.dx1};--dy1:${f.dy1};--dx2:${f.dx2};--dy2:${f.dy2};--dx3:${f.dx3};--dy3:${f.dy3};--dur:${f.dur};--gdur:${f.gdur}`;
    document.body.appendChild(el);
  });
</script>

<!-- Bonsai Tree SVG -->
<div class="tree-wrap">
  <svg width="220" height="260" viewBox="0 0 220 260" fill="none" xmlns="http://www.w3.org/2000/svg">
    <!-- Pot -->
    <path d="M75 240 L85 220 L135 220 L145 240 Z" fill="#2d1f0e" stroke="#4a3520" stroke-width="1.5"/>
    <rect x="78" y="238" width="64" height="8" rx="2" fill="#3d2a14" stroke="#4a3520" stroke-width="1"/>
    <!-- Trunk -->
    <path d="M110 220 C108 190 104 170 100 150 C96 130 98 110 105 95" stroke="#5c3d1e" stroke-width="8" stroke-linecap="round" fill="none"/>
    <path d="M110 220 C112 190 116 165 118 145 C120 125 116 105 110 90" stroke="#4a3015" stroke-width="5" stroke-linecap="round" fill="none" opacity="0.5"/>
    <!-- Branch left -->
    <path d="M100 150 C85 140 70 130 55 125" stroke="#5c3d1e" stroke-width="4" stroke-linecap="round" fill="none"/>
    <path d="M97 130 C82 118 70 108 58 100" stroke="#5c3d1e" stroke-width="3" stroke-linecap="round" fill="none"/>
    <!-- Branch right -->
    <path d="M110 140 C122 130 135 122 148 118" stroke="#5c3d1e" stroke-width="4" stroke-linecap="round" fill="none"/>
    <path d="M108 118 C120 108 132 100 144 96" stroke="#5c3d1e" stroke-width="3" stroke-linecap="round" fill="none"/>
    <!-- Foliage clusters -->
    <ellipse cx="55" cy="118" rx="28" ry="22" fill="#3d5c2a" opacity="0.9"/>
    <ellipse cx="45" cy="110" rx="20" ry="16" fill="#4a7032" opacity="0.8"/>
    <ellipse cx="65" cy="112" rx="18" ry="14" fill="#5a8a3c" opacity="0.7"/>
    <ellipse cx="58" cy="100" rx="22" ry="18" fill="#4a7032" opacity="0.85"/>

    <ellipse cx="148" cy="110" rx="28" ry="22" fill="#3d5c2a" opacity="0.9"/>
    <ellipse cx="158" cy="102" rx="20" ry="16" fill="#4a7032" opacity="0.8"/>
    <ellipse cx="138" cy="106" rx="18" ry="14" fill="#5a8a3c" opacity="0.7"/>
    <ellipse cx="150" cy="94" rx="22" ry="18" fill="#4a7032" opacity="0.85"/>

    <ellipse cx="105" cy="82" rx="35" ry="28" fill="#3d5c2a" opacity="0.95"/>
    <ellipse cx="92" cy="74" rx="25" ry="20" fill="#4a7032" opacity="0.85"/>
    <ellipse cx="118" cy="72" rx="25" ry="20" fill="#4a7032" opacity="0.85"/>
    <ellipse cx="105" cy="62" rx="30" ry="24" fill="#5a8a3c" opacity="0.8"/>
    <ellipse cx="95" cy="56" rx="20" ry="16" fill="#6aaa46" opacity="0.7"/>
    <ellipse cx="116" cy="54" rx="18" ry="15" fill="#6aaa46" opacity="0.65"/>
    <ellipse cx="105" cy="48" rx="22" ry="17" fill="#7abf50" opacity="0.6"/>
  </svg>
</div>

<div class="content">
  <div class="code">404</div>
  <div class="divider"></div>
  <div class="title">Trang này đã lạc vào rừng sâu</div>
  <div class="desc">Không tìm thấy trang bạn yêu cầu</div>
  <a href="/app/index.php" class="btn-back">
    <span>🌱</span> Quay về trang chủ
  </a>
</div>

</body>
</html>
