<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>EcoCycle Header – Professional Clean</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style>
    :root{
      --brand-green: #236f27;
      --brand-green-700: #1f5b23; /* slightly deeper for hover */
      --brand-yellow: #ffc107;
      --brand-line: #e7e7e7;
      --brand-text: #1f2937;
      --danger: #ef4444;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', system-ui, -apple-system, Roboto, Arial, sans-serif;
      background: #ffffff; color: var(--brand-text); line-height: 1.55;
    }
    a { text-decoration: none; color: inherit; }
    :focus-visible { outline: 3px solid rgba(46,125,50,.2); outline-offset: 2px; }

    /* Header: plain white with subtle divider */
    header.site-header {
      position: sticky; top: 0; z-index: 50;
      background: #fff; border-bottom: 1px solid var(--brand-line);
    }

    /* Grid layout: left (logo), center (nav), right (actions) */
    .header-grid {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      align-items: center;
      min-height: 76px; /* slightly larger overall */
    }

    /* Left: Logo with 10% left padding */
    .left-slot {
      display: flex; align-items: center;
      padding-left: 10%;
    }
    .brand { display: inline-flex; align-items: center; gap: 10px; }
    .logo-icon { width: 164px; height: auto; } /* slightly larger logo */
    .logo-icon:hover { transform: none; }

    /* Center: Plain nav, centered */
    .center-slot {
      display: flex; justify-content: center; align-items: center;
    }
    nav.primary {
      display: inline-flex; gap: 30px; align-items: center; justify-content: center;
    }
    nav.primary a {
      color: #000;
      font-size: 1.2rem;       /* larger menu text */
      font-weight: 500;        /* normal weight for non-active */
      padding: 10px 2px;
      transition: color .15s ease;
    }
    nav.primary a:hover {
      color: var(--brand-green-700);
    }
    /* Active: bold, slightly larger, with a single-color underline */
    nav.primary a.active {
      color: #000;
      font-weight: 700;
      font-size: 1.26rem;      /* small bump to pop */
      position: relative;
    }
    nav.primary a.active::after {
      content: "";
      position: absolute; left: 0; right: 0; bottom: -8px;
      height: 3px;
      background: var(--brand-green); /* single professional color */
      border-radius: 2px;
    }

    /* Right: Actions with 10% right padding */
    .right-slot {
      display: flex; align-items: center; justify-content: flex-end; gap: 12px;
      padding-right: 10%;
    }

    /* Notification button (no shadows), bigger bell + badge */
    .notification-btn {
      position: relative; background: #fff; color: var(--brand-green);
      border: 1px solid var(--brand-green);
      padding: 12px 16px; /* slight increase to keep balance */
      border-radius: 12px; cursor: pointer;
    }
    .notification-btn .fa-bell { font-size: 26px; } /* bigger bell */
    .notification-badge {
      position: absolute; top: -8px; right: -8px;
      background-color: #FA3E3E; color: #000;
      font-size: 13px; padding: 5px 7px; /* larger badge */
      border-radius: 999px; font-weight: 800; line-height: 1;
      border: 2px solid #fff; /* slightly thicker ring for clarity */
    }

    /* Logout button (kept styled, no shadows) */
    .btn-logout {
      border-radius: 12px; border: 1px solid var(--danger); color: var(--danger);
      padding: 12px 18px; background: #fff; font-weight: 600; cursor: pointer;
      font-size: 1rem; /* slightly larger */
    }
    .btn-logout:hover { background: var(--danger); color: #fff; }

    /* Mobile adjustments */
    .menu-toggle {
      display: none;
      border: 1px solid var(--brand-line);
      background: #fff; color: #000;
      padding: 8px 12px; border-radius: 10px; cursor: pointer;
    }
    .menu-panel { display: none; border-top: 1px solid var(--brand-line); background: #fff; }
    .menu-group { display: grid; gap: 2px; padding: 12px 20px; }
    .menu-group a { display: block; padding: 10px 2px; color: #000; }
    .menu-group a.active { font-weight: 700; }
    .menu-group a.active::after {
      content: ""; display: block; margin-top: 6px; height: 3px; background: var(--brand-green); border-radius: 2px;
    }

    @media (max-width: 960px){
      .logo-icon { width: 148px; }
      nav.primary a { font-size: 1.12rem; }
      nav.primary a.active { font-size: 1.18rem; }
      .left-slot { padding-left: 20px; }
      .right-slot { padding-right: 20px; }
      nav.primary { display: none; }
      .menu-toggle { display: inline-flex; align-items: center; gap: 8px; }
      /* Keep bell readable on small screens */
      .notification-btn .fa-bell { font-size: 24px; }
      .notification-badge { font-size: 12px; padding: 4px 6px; }
    }
  </style>
</head>
<body>

  <header class="site-header">
    <div class="header-grid">
      <!-- Left: Logo with 10% left padding -->
      <div class="left-slot">
        <a href="#" class="brand" aria-label="EcoCycle home">
          <img src="images/logo.svg" alt="EcoCycle Logo" class="logo-icon" />
        </a>
      </div>

      <!-- Center: Clean, larger nav -->
      <div class="center-slot">
        <nav class="primary" aria-label="Primary">
          <a href="user/user_home.php" class="active">Home</a>
          <a href="user/user_dashboard.php">User Dashboard</a>
          <a href="user/user_scraprequest.php">Add Request</a>
          <a href="user/user_settings.php">Profile</a>
        </nav>
      </div>

      <!-- Right: Notification + Logout with 10% right padding -->
      <div class="right-slot">
        <button class="notification-btn" aria-label="Notifications">
          <i class="fa-solid fa-bell"></i>
          <span class="notification-badge" id="demoBadge">9+</span>
        </button>

        <button class="menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="menuPanel">
          <i class="fa-solid fa-bars"></i>
        </button>

        <button class="btn-logout" onclick="confirmLogout()">Logout</button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div class="menu-panel" id="menuPanel">
      <div class="menu-group">
        <a href="user/user_home.php" class="active">Home</a>
        <a href="user/user_dashboard.php">User Dashboard</a>
        <a href="user/user_scraprequest.php">Add Request</a>
        <a href="user/user_settings.php">Profile</a>
        <a href="user/user_notifications.php">Notifications</a>
      </div>
    </div>
  </header>

  <main style="max-width: 1200px; margin: 0 auto; padding: 24px 20px;">
    <div style="height: 500px;"></div>
  </main>

  <script>
    function confirmLogout(){
      const ok = confirm("Are you sure you want to log out?");
      if(ok){ alert("Logged out (demo). Connect this to your real logout."); }
    }

    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const menuPanel = document.getElementById('menuPanel');
    if(menuToggle){
      menuToggle.addEventListener('click', () => {
        const open = menuPanel.style.display === 'block';
        menuPanel.style.display = open ? 'none' : 'block';
        menuToggle.setAttribute('aria-expanded', String(!open));
      });
    }

    // Demo: hide badge if 0
    const badge = document.getElementById('demoBadge');
    if(badge && (badge.textContent.trim() === '0')) {
      badge.style.display = 'none';
    }
  </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9716110926284719',t:'MTc1NTU2ODkyMy4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
