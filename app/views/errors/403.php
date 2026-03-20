<?php $noLayout = true; http_response_code(403); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>403 — BonSai</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --void:   #0e0a0a;
      --ember:  #1a0a06;
      --coal:   #2a1008;
      --fire-1: #c0392b;
      --fire-2: #e67e22;
      --fire-3: #f39c12;
      --ash:    #8a7070;
      --smoke:  #d4c4c0;
      --glow:   rgba(192,57,43,0.4);
    }

    html, body {
      height: 100%;
      background: var(--void);
      color: var(--smoke);
      font-family: 'DM Sans', sans-serif;
      overflow: hidden;
    }

    /* ── EMBER GLOW GROUND ── */
    .ground {
      position: fixed;
      bottom: 0; left: 0; right: 0;
      height: 200px;
      background: radial-gradient(ellipse 80% 60% at 50% 100%, rgba(192,57,43,0.2) 0%, transparent 70%);
      z-index: 0;
    }

    /* ── SMOKE PARTICLES ── */
    .smoke-wrap {
      position: fixed;
      bottom: 100px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1;
    }
    .smoke {
      position: absolute;
      bottom: 0;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(80,60,60,0.4) 0%, transparent 70%);
      animation: rise var(--dur) ease-out infinite;
      animation-delay: var(--delay);
    }
    @keyframes rise {
      0%   { transform: translateX(0) scale(0.5); opacity: 0.6; }
      100% { transform: translateX(var(--dx)) translateY(-300px) scale(3); opacity: 0; }
    }

    /* ── LOCK ICON ── */
    .lock-wrap {
      position: relative;
      z-index: 10;
      animation: pulse-glow 2s ease-in-out infinite;
    }
    @keyframes pulse-glow {
      0%,100% { filter: drop-shadow(0 0 10px rgba(192,57,43,0.4)); }
      50%     { filter: drop-shadow(0 0 25px rgba(230,126,34,0.7)); }
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
      text-align: center;
      padding: 0 24px;
    }

    .code {
      font-family: 'Playfair Display', serif;
      font-size: clamp(100px, 18vw, 180px);
      font-weight: 700;
      line-height: 0.9;
      letter-spacing: -4px;
      background: linear-gradient(135deg, var(--fire-1) 0%, var(--fire-2) 50%, var(--fire-3) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: flicker 3s ease-in-out infinite, fadeIn 1s ease both;
      position: relative;
    }
    @keyframes flicker {
      0%,100% { opacity: 1; }
      92%     { opacity: 1; }
      93%     { opacity: 0.7; }
      94%     { opacity: 1; }
      96%     { opacity: 0.8; }
      97%     { opacity: 1; }
    }

    .divider {
      width: 60px;
      height: 1px;
      background: linear-gradient(90deg, transparent, var(--fire-1), transparent);
      margin: 20px auto;
      animation: fadeIn 1s 0.2s ease both;
    }

    .title {
      font-family: 'Playfair Display', serif;
      font-style: italic;
      font-size: clamp(18px, 3vw, 26px);
      color: var(--smoke);
      letter-spacing: 1px;
      animation: fadeIn 1s 0.3s ease both;
    }

    .desc {
      margin-top: 12px;
      font-size: 13px;
      color: var(--ash);
      font-weight: 300;
      letter-spacing: 2px;
      text-transform: uppercase;
      animation: fadeIn 1s 0.4s ease both;
    }

    .btn-group {
      display: flex;
      gap: 12px;
      margin-top: 40px;
      flex-wrap: wrap;
      justify-content: center;
      animation: fadeIn 1s 0.6s ease both;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 28px;
      border-radius: 2px;
      font-size: 13px;
      letter-spacing: 3px;
      text-transform: uppercase;
      text-decoration: none;
      transition: all 0.3s;
    }
    .btn-fire {
      border: 1px solid rgba(192,57,43,0.4);
      color: var(--fire-2);
      background: rgba(192,57,43,0.08);
    }
    .btn-fire:hover {
      background: rgba(192,57,43,0.18);
      border-color: var(--fire-1);
      box-shadow: 0 0 20px rgba(192,57,43,0.2);
      color: var(--fire-3);
    }
    .btn-ghost {
      border: 1px solid rgba(138,112,112,0.3);
      color: var(--ash);
      background: transparent;
    }
    .btn-ghost:hover {
      border-color: var(--ash);
      color: var(--smoke);
    }

    /* ── WARNING BADGE ── */
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 16px;
      border: 1px solid rgba(192,57,43,0.3);
      border-radius: 20px;
      font-size: 11px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--fire-1);
      background: rgba(192,57,43,0.06);
      margin-bottom: 24px;
      animation: fadeIn 0.8s ease both;
    }
    .badge-dot {
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--fire-1);
      animation: blink 1.5s ease-in-out infinite;
    }
    @keyframes blink {
      0%,100% { opacity: 1; }
      50%     { opacity: 0.2; }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(12px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── CRACKS ── */
    .crack {
      position: fixed;
      pointer-events: none;
      opacity: 0.06;
    }
  </style>
</head>
<body>

<div class="ground"></div>

<!-- Smoke particles -->
<div class="smoke-wrap">
  <div class="smoke" style="width:60px;height:60px;left:-10px;--dur:4s;--delay:0s;--dx:20px;"></div>
  <div class="smoke" style="width:80px;height:80px;left:20px;--dur:5s;--delay:-1s;--dx:-15px;"></div>
  <div class="smoke" style="width:50px;height:50px;left:5px;--dur:3.5s;--delay:-2s;--dx:10px;"></div>
  <div class="smoke" style="width:70px;height:70px;left:-5px;--dur:4.5s;--delay:-0.5s;--dx:-20px;"></div>
</div>

<!-- Corner cracks decorative -->
<svg class="crack" style="top:0;left:0;width:200px;height:200px;" viewBox="0 0 200 200">
  <path d="M0 0 L60 80 L30 100 L80 160 L50 180" stroke="#c0392b" stroke-width="1.5" fill="none"/>
  <path d="M0 20 L40 60 L20 80 L55 130" stroke="#c0392b" stroke-width="0.8" fill="none" opacity="0.5"/>
</svg>
<svg class="crack" style="top:0;right:0;width:200px;height:200px;" viewBox="0 0 200 200" style="transform:scaleX(-1)">
  <path d="M0 0 L60 80 L30 100 L80 160 L50 180" stroke="#c0392b" stroke-width="1.5" fill="none"/>
</svg>

<div class="content">
  <div class="badge">
    <div class="badge-dot"></div>
    Truy cập bị từ chối
  </div>

  <div class="lock-wrap">
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" style="margin-bottom:8px;">
      <rect x="12" y="28" width="40" height="28" rx="4" fill="#2a1008" stroke="#c0392b" stroke-width="1.5"/>
      <path d="M20 28 V20 C20 11.2 44 11.2 44 20 V28" stroke="#c0392b" stroke-width="2.5" stroke-linecap="round" fill="none"/>
      <circle cx="32" cy="42" r="5" fill="#c0392b" opacity="0.8"/>
      <rect x="30" y="44" width="4" height="6" rx="1" fill="#c0392b" opacity="0.8"/>
    </svg>
  </div>

  <div class="code">403</div>
  <div class="divider"></div>
  <div class="title">Vùng đất bị phong ấn</div>
  <div class="desc">Bạn không có quyền truy cập trang này</div>

  <div class="btn-group">
    <a href="../app/index.php?url=admin-login" class="btn btn-ghost">Đăng nhập</a>
  </div>
</div>

</body>
</html>
