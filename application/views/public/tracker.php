<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Monitoring Evaluasi - <?= htmlspecialchars($acara->nama_acara) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.8.0/countUp.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background: #f0f2f5;
      color: #333;
      min-height: 100vh;
    }

    /* ===== TOP NAV ===== */
    .top-nav {
      background: #fff;
      padding: 12px 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .top-nav .nav-left { display: flex; align-items: center; gap: 12px; }
    .top-nav .nav-title { font-weight: 700; font-size: 14px; color: #666; }
    .top-nav .nav-subtitle { display: none; }

    /* ===== HERO HEADER ===== */
    .hero-header {
      background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
      border-radius: 12px;
      padding: 28px 30px;
      color: #fff;
      margin-bottom: 20px;
      position: relative;
      overflow: hidden;
    }
    .hero-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 300px;
      height: 300px;
      background: rgba(255,255,255,0.05);
      border-radius: 50%;
    }
    .hero-header::after {
      content: '';
      position: absolute;
      bottom: -60%;
      left: -10%;
      width: 200px;
      height: 200px;
      background: rgba(255,255,255,0.03);
      border-radius: 50%;
    }
    .hero-header .header-icon {
      font-size: 48px;
      opacity: 0.3;
      position: absolute;
      right: 30px;
      top: 50%;
      transform: translateY(-50%);
    }
    .hero-header h1 {
      font-size: 26px;
      font-weight: 700;
      margin: 0 0 8px 0;
      line-height: 1.3;
      position: relative;
    }
    .hero-header .header-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      font-size: 14px;
      opacity: 0.9;
      position: relative;
    }
    .hero-header .header-meta .meta-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .hero-header .header-meta .meta-item i {
      opacity: 0.7;
      font-size: 14px;
    }
    .top-nav .nav-right { display: flex; align-items: center; gap: 10px; }

    .live-indicator {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      font-weight: 600;
      color: #27ae60;
    }
    .live-dot {
      width: 10px;
      height: 10px;
      background: #27ae60;
      border-radius: 50%;
      animation: livePulse 1.5s ease-in-out infinite;
    }
    .live-dot.offline { background: #e74c3c; animation-name: livePulseOffline; }
    .live-dot.loading { background: #f39c12; animation: none; }
    @keyframes livePulse {
      0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(39,174,96,0.5); }
      50% { opacity: 0.6; box-shadow: 0 0 0 6px rgba(39,174,96,0); }
    }
    @keyframes livePulseOffline {
      0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(231,76,60,0.5); }
      50% { opacity: 0.6; box-shadow: 0 0 0 6px rgba(231,76,60,0); }
    }

    .btn {
      padding: 8px 16px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      font-size: 13px;
      font-weight: 500;
      transition: all 0.2s;
    }
    .btn-primary { background: #3498db; color: #fff; }
    .btn-primary:hover { background: #2980b9; }
    .btn-ghost { background: transparent; color: #666; border: 1px solid #ddd; }
    .btn-ghost:hover { background: #f5f5f5; }

    /* ===== CONNECTION STATUS ===== */
    .connection-status {
      display: none;
      font-size: 11px;
      color: #e74c3c;
      padding: 6px 12px;
      background: #ffeaea;
      border-radius: 4px;
    }
    .connection-status.show { display: inline-flex; align-items: center; gap: 6px; }

    /* ===== CONTAINER ===== */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    @media (max-width: 768px) {
      .container { padding: 12px; }
    }

    /* ===== HERO SECTION ===== */
    .hero-section {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 12px;
      padding: 30px;
      color: #fff;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(102,126,234,0.3);
    }
    .hero-grid {
      display: grid;
      grid-template-columns: 180px 1fr 1fr 1fr;
      gap: 20px;
      align-items: center;
    }
    .hero-stat { text-align: center; }
    .hero-stat .stat-number { font-size: 48px; font-weight: 700; line-height: 1; margin-bottom: 8px; }
    .hero-stat .stat-label { font-size: 12px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; }
    .stat-wajib .stat-number { color: #fff; }
    .stat-sudah .stat-number { color: #2ecc71; }
    .stat-belum .stat-number { color: #e74c3c; }

    /* Progress Circle */
    .progress-circle-container { text-align: center; }
    .progress-circle { position: relative; width: 140px; height: 140px; margin: 0 auto; }
    .progress-circle canvas { display: block; }
    .progress-circle-text {
      position: absolute; top: 50%; left: 50%;
      transform: translate(-50%, -50%); text-align: center;
    }
    .progress-circle-text .percentage { font-size: 36px; font-weight: 700; color: #fff; line-height: 1; }
    .progress-circle-text .percentage-label { font-size: 11px; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 1px; }

    /* Progress Bar */
    .progress-bar-container { margin-top: 20px; }
    .progress-bar-track {
      height: 10px; border-radius: 5px;
      background: rgba(255,255,255,0.2); overflow: hidden;
    }
    .progress-bar-fill {
      height: 100%; border-radius: 5px;
      background: linear-gradient(90deg, #2ecc71, #27ae60);
      transition: width 0.8s ease; width: 0%;
    }

    /* ===== COMPLETE BANNER ===== */
    .complete-banner {
      display: none;
      background: linear-gradient(135deg, #27ae60, #2ecc71);
      color: #fff; text-align: center;
      padding: 14px; border-radius: 8px;
      margin-bottom: 20px; font-weight: 600; font-size: 16px;
      animation: fadeInDown 0.5s ease;
    }
    .complete-banner.show { display: block; }
    .complete-banner i { margin-right: 8px; }

    /* ===== CONTENT GRID ===== */
    .content-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 320px;
      gap: 20px;
    }

    /* ===== CARD ===== */
    .card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      overflow: hidden;
    }
    .card-header {
      padding: 14px 18px;
      border-bottom: 1px solid #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .card-header h3 { font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .card-header h3 i { font-size: 16px; }
    .card-header .badge {
      background: #e0e0e0; color: #666;
      padding: 3px 10px; border-radius: 12px;
      font-size: 12px; font-weight: 600;
    }
    .card-header .badge-green { background: #d4edda; color: #27ae60; }
    .card-header .badge-red { background: #fce4e4; color: #e74c3c; }
    .card-body { padding: 0; max-height: 450px; overflow-y: auto; }

    /* ===== LIST ITEMS ===== */
    .list-item {
      display: flex; align-items: center;
      padding: 12px 18px; border-bottom: 1px solid #f5f5f5;
      transition: background 0.2s;
    }
    .list-item:hover { background: #fafafa; }
    .list-item:last-child { border-bottom: none; }
    .list-item .avatar {
      width: 36px; height: 36px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 13px; color: #fff;
      margin-right: 12px; flex-shrink: 0;
    }
    .avatar-sudah { background: #27ae60; }
    .avatar-belum { background: #95a5a6; }
    .list-item .item-info { flex: 1; min-width: 0; }
    .list-item .item-name {
      font-weight: 600; font-size: 13px; color: #333;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .list-item .item-role { font-size: 11px; color: #999; text-transform: uppercase; }
    .list-item .item-time { font-size: 11px; color: #999; white-space: nowrap; margin-left: 10px; }

    .list-item.highlight-new { background: #d4edda !important; animation: highlightFade 2s ease forwards; }
    @keyframes highlightFade {
      0% { background: #d4edda; }
      70% { background: #d4edda; }
      100% { background: transparent; }
    }

    /* ===== ACTIVITY FEED ===== */
    .activity-item {
      display: flex; padding: 12px 18px;
      border-bottom: 1px solid #f5f5f5;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-icon {
      width: 30px; height: 30px; border-radius: 50%;
      background: #27ae60; display: flex;
      align-items: center; justify-content: center;
      margin-right: 10px; flex-shrink: 0;
    }
    .activity-icon i { color: #fff; font-size: 12px; }
    .activity-content { flex: 1; }
    .activity-text { font-size: 13px; color: #333; margin-bottom: 2px; }
    .activity-text strong { color: #27ae60; }
    .activity-time { font-size: 11px; color: #999; }

    /* ===== CHART ===== */
    .chart-container { position: relative; height: 220px; padding: 10px; }

    /* ===== EMPTY STATE ===== */
    .zero-state { text-align: center; padding: 40px 20px; color: #999; }
    .zero-state i { font-size: 48px; margin-bottom: 10px; opacity: 0.3; display: block; }
    .zero-state h4 { font-weight: 600; color: #999; margin-bottom: 5px; }
    .zero-state p { font-size: 13px; color: #bbb; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
      .content-grid { grid-template-columns: 1fr 1fr; }
      .content-grid > .card:last-child { grid-column: span 2; }
    }
    @media (max-width: 768px) {
      .top-nav { padding: 10px 15px; }
      .top-nav .nav-title { font-size: 12px; }
      
      .hero-header {
        padding: 24px 20px;
        border-radius: 10px;
        margin-bottom: 15px;
      }
      .hero-header h1 {
        font-size: 22px;
        line-height: 1.3;
        margin-bottom: 12px;
        padding-right: 50px;
      }
      .hero-header .header-icon {
        font-size: 40px;
        right: 15px;
        top: 25px;
        transform: none;
      }
      .hero-header .header-meta {
        gap: 10px;
        font-size: 12px;
        flex-direction: column;
      }
      .hero-header .header-meta .meta-item {
        padding: 4px 0;
      }
      
      .hero-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
      .hero-stat .stat-number { font-size: 36px; }
      .hero-stat .stat-label { font-size: 10px; }
      .progress-circle { width: 100px; height: 100px; }
      .progress-circle-text .percentage { font-size: 24px; }
      .content-grid { grid-template-columns: 1fr; gap: 15px; }
      .content-grid > .card:last-child { grid-column: span 1; }
    }

    /* ===== TV/PROJECTOR MODE ===== */
    @media (min-width: 1400px) {
      .hero-header { padding: 35px 40px; }
      .hero-header h1 { font-size: 32px; }
      .hero-header .header-meta { font-size: 16px; gap: 25px; }
      .hero-header .header-icon { font-size: 64px; }
      .hero-section { padding: 40px; }
      .hero-stat .stat-number { font-size: 56px; }
      .hero-stat .stat-label { font-size: 14px; }
      .progress-circle { width: 180px; height: 180px; }
      .progress-circle-text .percentage { font-size: 44px; }
      .list-item { padding: 14px 20px; }
      .list-item .item-name { font-size: 15px; }
      .list-item .avatar { width: 44px; height: 44px; font-size: 16px; }
      .card-body { max-height: 550px; }
    }
  </style>
</head>
<body>

  <!-- TOP NAV -->
  <div class="top-nav">
    <div class="nav-left">
      <div>
        <div class="nav-title"><i class="fa fa-tachometer"></i> Monitoring Evaluasi</div>
        <div class="nav-subtitle"><?= htmlspecialchars($acara->nama_acara) ?></div>
      </div>
    </div>
    <div class="nav-right">
      <div class="connection-status" id="connection-status">
        <i class="fa fa-exclamation-triangle"></i> Koneksi terputus, mencoba lagi...
      </div>
      <div class="live-indicator">
        <div class="live-dot" id="live-dot"></div>
        <span id="live-label">LIVE</span>
      </div>
      <button class="btn btn-primary" onclick="TrackerApp.refreshAll()">
        <i class="fa fa-refresh"></i> Refresh
      </button>
    </div>
  </div>

  <div class="container">

    <!-- HERO HEADER -->
    <div class="hero-header">
      <i class="fa fa-graduation-cap header-icon"></i>
      <h1><?= htmlspecialchars($acara->nama_acara) ?></h1>
      <div class="header-meta">
        <span class="meta-item"><i class="fa fa-calendar"></i> <?= date('d M Y', strtotime($acara->waktu_mulai)) ?></span>
        <span class="meta-item"><i class="fa fa-clock-o"></i> <?= date('H:i', strtotime($acara->waktu_mulai)) ?> - <?= date('H:i', strtotime($acara->waktu_selesai)) ?></span>
        <span class="meta-item"><i class="fa fa-map-marker"></i> <?= htmlspecialchars($acara->lokasi) ?></span>
        <span class="meta-item"><i class="fa fa-question-circle"></i> <?= $acara->jumlah_pertanyaan ?> Pertanyaan</span>
        <?php if (!empty($acara->narasumber) && $acara->narasumber != '-'): ?>
        <span class="meta-item"><i class="fa fa-user"></i> <?= htmlspecialchars($acara->narasumber) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <!-- 100% COMPLETE BANNER -->
    <div class="complete-banner" id="complete-banner">
      <i class="fa fa-trophy"></i> Semua peserta sudah mengisi evaluasi!
    </div>

    <!-- HERO SECTION -->
    <div class="hero-section">
      <div class="hero-grid">
        <div class="progress-circle-container">
          <div class="progress-circle">
            <canvas id="progressCanvas" width="180" height="180"></canvas>
            <div class="progress-circle-text">
              <div class="percentage" id="stat-persentase">0%</div>
              <div class="percentage-label">Selesai</div>
            </div>
          </div>
        </div>
        <div class="hero-stat stat-wajib">
          <div class="stat-number" id="stat-wajib">0</div>
          <div class="stat-label">Total Wajib Isi</div>
        </div>
        <div class="hero-stat stat-sudah">
          <div class="stat-number" id="stat-sudah">0</div>
          <div class="stat-label">Sudah Mengisi</div>
        </div>
        <div class="hero-stat stat-belum">
          <div class="stat-number" id="stat-belum">0</div>
          <div class="stat-label">Belum Mengisi</div>
        </div>
      </div>
      <div class="progress-bar-container">
        <div class="progress-bar-track">
          <div class="progress-bar-fill" id="progress-bar-fill"></div>
        </div>
      </div>
    </div>

    <!-- CONTENT GRID -->
    <div class="content-grid">

      <!-- Sudah Mengisi -->
      <div class="card">
        <div class="card-header">
          <h3><i class="fa fa-check-circle" style="color:#27ae60"></i> Sudah Mengisi</h3>
          <span class="badge badge-green" id="badge-sudah">0</span>
        </div>
        <div class="card-body" id="list-sudah-isi">
          <div class="zero-state"><i class="fa fa-spinner fa-spin"></i><h4>Memuat...</h4></div>
        </div>
      </div>

      <!-- Belum Mengisi -->
      <div class="card">
        <div class="card-header">
          <h3><i class="fa fa-times-circle" style="color:#e74c3c"></i> Belum Mengisi</h3>
          <span class="badge badge-red" id="badge-belum">0</span>
        </div>
        <div class="card-body" id="list-belum-isi">
          <div class="zero-state"><i class="fa fa-spinner fa-spin"></i><h4>Memuat...</h4></div>
        </div>
      </div>

      <!-- Activity Feed + Chart -->
      <div>
        <div class="card" style="margin-bottom:20px">
          <div class="card-header">
            <h3><i class="fa fa-rss" style="color:#3498db"></i> Aktivitas Terbaru</h3>
          </div>
          <div class="card-body" id="activity-feed">
            <div class="zero-state"><i class="fa fa-spinner fa-spin"></i><h4>Memuat...</h4></div>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3><i class="fa fa-pie-chart" style="color:#9b59b6"></i> Breakdown per Role</h3>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="chartBreakdown"></canvas>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
  var TrackerApp = (function($) {
    'use strict';

    // ==================== CONFIG ====================
    var CONFIG = {
      id_acara: <?= (int) $id_acara ?>,
      ajaxStatusUrl: '<?= site_url("evaluasi/status/" . $id_acara) ?>',
      ajaxActivityUrl: '<?= site_url("evaluasi/activity/" . $id_acara) ?>',
      ajaxBreakdownUrl: '<?= site_url("evaluasi/breakdown/" . $id_acara) ?>',
      STATUS_POLL_MS: 7000,
      ACTIVITY_POLL_MS: 10000,
      MAX_ACTIVITY_ITEMS: 10
    };

    // ==================== STATE ====================
    var state = {
      currentWajib: 0, currentSudah: 0, currentBelum: 0, currentPersen: 0,
      prevSudahNpps: {}, prevBelumNpps: {}, activityNpps: {},
      initialized: false, confettiTriggered: false, isPolling: false,
      statusFailCount: 0, activityFailCount: 0,
      statusTimer: null, activityTimer: null,
      chartBreakdown: null,
      countUpWajib: null, countUpSudah: null, countUpBelum: null, countUpPersen: null
    };

    var $el = {};

    // ==================== XSS PROTECTION ====================
    function escapeHtml(str) {
      if (!str) return '';
      var div = document.createElement('div');
      div.appendChild(document.createTextNode(str));
      return div.innerHTML;
    }
    function escapeAttr(str) {
      if (!str) return '';
      return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // ==================== DOM CACHE ====================
    function cacheDom() {
      $el.liveDot = $('#live-dot');
      $el.liveLabel = $('#live-label');
      $el.connectionStatus = $('#connection-status');
      $el.completeBanner = $('#complete-banner');
      $el.statWajib = $('#stat-wajib');
      $el.statSudah = $('#stat-sudah');
      $el.statBelum = $('#stat-belum');
      $el.statPersentase = $('#stat-persentase');
      $el.progressBarFill = $('#progress-bar-fill');
      $el.progressCanvas = $('#progressCanvas')[0];
      $el.listSudah = $('#list-sudah-isi');
      $el.listBelum = $('#list-belum-isi');
      $el.badgeSudah = $('#badge-sudah');
      $el.badgeBelum = $('#badge-belum');
      $el.activityFeed = $('#activity-feed');
      $el.chartCanvas = $('#chartBreakdown')[0];
    }

    // ==================== COUNTUP ====================
    function initCountUp() {
      var opts = { duration: 0.8, useGrouping: true, separator: '.' };
      state.countUpWajib = new countUp.CountUp('stat-wajib', 0, opts);
      state.countUpSudah = new countUp.CountUp('stat-sudah', 0, opts);
      state.countUpBelum = new countUp.CountUp('stat-belum', 0, opts);
      state.countUpPersen = new countUp.CountUp('stat-persentase', 0, Object.assign({}, opts, { suffix: '%' }));
    }

    // ==================== PROGRESS CIRCLE ====================
    function drawProgressCircle(percent) {
      var canvas = $el.progressCanvas;
      if (!canvas) return;
      var ctx = canvas.getContext('2d');
      var w = canvas.width, h = canvas.height;
      var cx = w / 2, cy = h / 2, radius = Math.min(cx, cy) - 10, lw = 12;
      ctx.clearRect(0, 0, w, h);
      ctx.beginPath(); ctx.arc(cx, cy, radius, 0, Math.PI * 2);
      ctx.strokeStyle = 'rgba(255,255,255,0.2)'; ctx.lineWidth = lw; ctx.stroke();
      if (percent > 0) {
        ctx.beginPath();
        ctx.arc(cx, cy, radius, -Math.PI / 2, -Math.PI / 2 + (Math.PI * 2 * percent / 100));
        ctx.strokeStyle = '#2ecc71'; ctx.lineWidth = lw; ctx.lineCap = 'round'; ctx.stroke();
      }
    }

    // ==================== UPDATE STATS ====================
    function updateStats(data) {
      var wajib = parseInt(data.total_wajib_isi) || 0;
      var sudah = parseInt(data.total_sudah_isi) || 0;
      var belum = parseInt(data.total_belum_isi) || 0;
      var persen = wajib > 0 ? Math.round((sudah / wajib) * 100) : 0;

      if (state.countUpWajib) state.countUpWajib.update(wajib);
      if (state.countUpSudah) state.countUpSudah.update(sudah);
      if (state.countUpBelum) state.countUpBelum.update(belum);
      if (state.countUpPersen) state.countUpPersen.update(persen);

      drawProgressCircle(persen);
      $el.progressBarFill.css('width', persen + '%');
      $el.badgeSudah.text(sudah);
      $el.badgeBelum.text(belum);

      if (belum === 0 && wajib > 0 && !state.confettiTriggered) {
        state.confettiTriggered = true;
        triggerConfetti();
        $el.completeBanner.addClass('show');
      } else if (belum > 0) {
        state.confettiTriggered = false;
        $el.completeBanner.removeClass('show');
      }

      state.currentWajib = wajib; state.currentSudah = sudah;
      state.currentBelum = belum; state.currentPersen = persen;
    }

    // ==================== HELPERS ====================
    function getInitials(name) {
      if (!name) return '?';
      var parts = name.trim().split(/\s+/);
      return parts.length >= 2 ? (parts[0][0] + parts[1][0]).toUpperCase() : name.substring(0, 2).toUpperCase();
    }

    function formatRelativeTime(dateStr) {
      if (!dateStr) return '';
      var now = new Date();
      var then = new Date(dateStr.replace(' ', 'T') + (dateStr.indexOf('+') === -1 && dateStr.indexOf('Z') === -1 ? '+07:00' : ''));
      var diffSec = Math.floor((now - then) / 1000);
      if (diffSec < 5) return 'baru saja';
      if (diffSec < 60) return diffSec + 'd lalu';
      var diffMin = Math.floor(diffSec / 60);
      if (diffMin < 60) return diffMin + 'm lalu';
      var diffHr = Math.floor(diffMin / 60);
      if (diffHr < 24) return diffHr + 'j lalu';
      return Math.floor(diffHr / 24) + 'h lalu';
    }

    // ==================== BUILD LIST ITEM ====================
    function buildListItem(item, type) {
      var safeName = escapeHtml(item.peserta || '-');
      var safeNpp = escapeAttr(item.npp || '');
      var safeRole = escapeHtml(item.role || '');
      var safeTime = escapeAttr(item.waktu_submit_terakhir || '');
      var avatarClass = type === 'sudah' ? 'avatar-sudah' : 'avatar-belum';
      var timeHtml = '';
      if (type === 'sudah' && item.waktu_submit_terakhir) {
        timeHtml = '<div class="item-time" title="' + safeTime + '">' + formatRelativeTime(item.waktu_submit_terakhir) + '</div>';
      }
      return '<div class="list-item" data-npp="' + safeNpp + '">' +
        '<div class="avatar ' + avatarClass + '">' + escapeHtml(getInitials(item.peserta)) + '</div>' +
        '<div class="item-info"><div class="item-name" title="' + safeName + '">' + safeName + '</div>' +
        '<div class="item-role">' + safeRole + '</div></div>' + timeHtml + '</div>';
    }

    function renderListSudah(list) {
      if (!list || list.length === 0) {
        $el.listSudah.html('<div class="zero-state"><i class="fa fa-inbox"></i><h4>Belum Ada</h4><p>Belum ada peserta yang mengisi evaluasi</p></div>');
        return;
      }
      var html = '';
      for (var i = 0; i < list.length; i++) html += buildListItem(list[i], 'sudah');
      $el.listSudah.html(html);
    }

    function renderListBelum(list) {
      if (!list || list.length === 0) {
        $el.listBelum.html('<div class="zero-state"><i class="fa fa-check-circle" style="color:#27ae60"></i><h4>Semua Sudah!</h4><p>Semua peserta sudah mengisi evaluasi</p></div>');
        return;
      }
      var html = '';
      for (var i = 0; i < list.length; i++) html += buildListItem(list[i], 'belum');
      $el.listBelum.html(html);
    }

    // ==================== DETECT CHANGES ====================
    function detectAndAnimateChanges(data) {
      var newSudahNpps = {}, newBelumNpps = {}, i, npp;
      if (data.list_sudah_isi) for (i = 0; i < data.list_sudah_isi.length; i++) newSudahNpps[data.list_sudah_isi[i].npp] = data.list_sudah_isi[i];
      if (data.list_belum_isi) for (i = 0; i < data.list_belum_isi.length; i++) newBelumNpps[data.list_belum_isi[i].npp] = data.list_belum_isi[i];

      var movedToSudah = [];
      if (state.initialized) {
        for (npp in newSudahNpps) {
          if (newSudahNpps.hasOwnProperty(npp) && state.prevBelumNpps[npp] && !state.prevSudahNpps[npp]) {
            movedToSudah.push(newSudahNpps[npp]);
          }
        }
      }

      renderListSudah(data.list_sudah_isi);
      renderListBelum(data.list_belum_isi);

      if (movedToSudah.length > 0) {
        for (i = 0; i < movedToSudah.length; i++) {
          animateNewSudahItem(movedToSudah[i].npp);
          showToast(movedToSudah[i].peserta);
        }
      }

      state.prevSudahNpps = newSudahNpps;
      state.prevBelumNpps = newBelumNpps;
      state.initialized = true;
    }

    function animateNewSudahItem(npp) {
      var $item = $el.listSudah.find('[data-npp="' + npp + '"]');
      if ($item.length) {
        $item.addClass('animate__animated animate__fadeInUp');
        setTimeout(function() {
          $item.removeClass('animate__animated animate__fadeInUp');
          $item.addClass('highlight-new');
          setTimeout(function() { $item.removeClass('highlight-new'); }, 2000);
        }, 600);
      }
    }

    // ==================== ACTIVITY FEED ====================
    function renderActivityFeed(feed) {
      if (!feed || feed.length === 0) {
        $el.activityFeed.html('<div class="zero-state" style="padding:30px 20px"><i class="fa fa-rss" style="color:#ddd"></i><h4>Menunggu</h4><p>Belum ada aktivitas</p></div>');
        return;
      }
      var newActivityNpps = {}, i, item;
      for (i = 0; i < feed.length; i++) newActivityNpps[feed[i].npp] = feed[i];

      var html = '', count = Math.min(feed.length, CONFIG.MAX_ACTIVITY_ITEMS);
      for (i = 0; i < count; i++) {
        item = feed[i];
        var isNew = !state.activityNpps[item.npp] && state.initialized;
        var animClass = isNew ? ' animate__animated animate__slideInDown' : '';
        html += '<div class="activity-item' + animClass + '" data-npp="' + escapeAttr(item.npp) + '">' +
          '<div class="activity-icon"><i class="fa fa-check"></i></div>' +
          '<div class="activity-content"><div class="activity-text"><strong>' + escapeHtml(item.peserta || '-') + '</strong> mengisi evaluasi</div>' +
          '<div class="activity-time" title="' + escapeAttr(item.waktu_submit || '') + '">' + formatRelativeTime(item.waktu_submit) + '</div></div></div>';
      }
      $el.activityFeed.html(html);
      state.activityNpps = newActivityNpps;
    }

    // ==================== CHART ====================
    function initChart() {
      if (!$el.chartCanvas) return;
      state.chartBreakdown = new Chart($el.chartCanvas.getContext('2d'), {
        type: 'doughnut',
        data: { labels: [], datasets: [{ data: [], backgroundColor: ['#3498db','#2ecc71','#e74c3c','#f39c12','#9b59b6','#1abc9c','#e67e22','#34495e'], borderWidth: 2, borderColor: '#fff' }] },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, font: { size: 11 } } },
            tooltip: { callbacks: { label: function(ctx) { var t = ctx.dataset.data.reduce(function(a,b){return a+b;},0); return ctx.label + ': ' + ctx.parsed + ' (' + (t>0?Math.round(ctx.parsed/t*100):0) + '%)'; } } }
          }
        }
      });
    }

    function updateChart(breakdown) {
      if (!state.chartBreakdown || !breakdown || breakdown.length === 0) return;
      var labels = [], dataBelum = [], dataSudah = [], i;
      for (i = 0; i < breakdown.length; i++) {
        labels.push((breakdown[i].role||'').replace(/_/g,' ').replace(/\b\w/g,function(c){return c.toUpperCase();}));
        dataBelum.push(parseInt(breakdown[i].belum)||0);
        dataSudah.push(parseInt(breakdown[i].sudah)||0);
      }
      state.chartBreakdown.data.labels = labels;
      state.chartBreakdown.data.datasets = [
        { label: 'Sudah', data: dataSudah, backgroundColor: '#27ae60', borderWidth: 0 },
        { label: 'Belum', data: dataBelum, backgroundColor: '#e74c3c', borderWidth: 0 }
      ];
      state.chartBreakdown.update();
    }

    // ==================== TOAST ====================
    function showToast(nama) {
      Toastify({
        text: '<i class="fa fa-check-circle"></i> ' + escapeHtml(nama || 'Peserta') + ' baru saja mengisi evaluasi',
        duration: 5000, gravity: 'top', position: 'right', escapeHtml: false,
        style: { background: 'linear-gradient(135deg, #27ae60, #2ecc71)', borderRadius: '6px', boxShadow: '0 4px 12px rgba(0,0,0,0.15)', fontSize: '13px', padding: '10px 16px' },
        offset: { x: 0, y: 60 }
      }).showToast();
    }

    // ==================== CONFETTI ====================
    function triggerConfetti() {
      if (typeof confetti !== 'function') return;
      var end = Date.now() + 2000, colors = ['#27ae60','#2ecc71','#f1c40f','#3498db'];
      (function frame() {
        confetti({ particleCount: 3, angle: 60, spread: 55, origin: {x:0,y:0.6}, colors: colors });
        confetti({ particleCount: 3, angle: 120, spread: 55, origin: {x:1,y:0.6}, colors: colors });
        if (Date.now() < end) requestAnimationFrame(frame);
      })();
    }

    // ==================== LIVE INDICATOR ====================
    function setLiveOnline() {
      $el.liveDot.removeClass('offline loading');
      $el.liveLabel.text('LIVE').css('color', '#27ae60');
      $el.connectionStatus.removeClass('show');
      state.statusFailCount = 0;
    }
    function setLiveOffline() {
      $el.liveDot.addClass('offline').removeClass('loading');
      $el.liveLabel.text('OFFLINE').css('color', '#e74c3c');
      $el.connectionStatus.addClass('show');
    }
    function setLiveLoading() { $el.liveDot.addClass('loading').removeClass('offline'); }

    // ==================== AJAX ====================
    function fetchStatus() {
      setLiveLoading();
      $.ajax({
        url: CONFIG.ajaxStatusUrl, type: 'GET', dataType: 'json', timeout: 10000,
        success: function(resp) {
          if (resp && resp.status === 1 && resp.data) { setLiveOnline(); updateStats(resp.data); detectAndAnimateChanges(resp.data); }
          else handleStatusError();
        },
        error: function() { handleStatusError(); }
      });
    }
    function handleStatusError() { state.statusFailCount++; if (state.statusFailCount >= 2) setLiveOffline(); }

    function fetchActivity() {
      $.ajax({
        url: CONFIG.ajaxActivityUrl, type: 'GET', dataType: 'json', timeout: 10000,
        success: function(resp) { if (resp && resp.status === 1 && resp.data) { renderActivityFeed(resp.data); state.activityFailCount = 0; } },
        error: function() { state.activityFailCount++; }
      });
    }

    function fetchBreakdown() {
      $.ajax({
        url: CONFIG.ajaxBreakdownUrl, type: 'GET', dataType: 'json', timeout: 10000,
        success: function(resp) { if (resp && resp.status === 1 && resp.data) updateChart(resp.data); }
      });
    }

    // ==================== POLLING ====================
    function setupVisibilityHandler() {
      var hidden, visChange;
      if (typeof document.hidden !== 'undefined') { hidden = 'hidden'; visChange = 'visibilitychange'; }
      else if (typeof document.msHidden !== 'undefined') { hidden = 'msHidden'; visChange = 'msvisibilitychange'; }
      else if (typeof document.webkitHidden !== 'undefined') { hidden = 'webkitHidden'; visChange = 'webkitvisibilitychange'; }
      if (typeof document[hidden] === 'undefined') return;

      document.addEventListener(visChange, function() {
        if (document[hidden]) { stopPolling(); }
        else if (!state.isPolling) { startPolling(true); refreshAll(); }
      }, false);
    }

    function startPolling(skipInitial) {
      if (!skipInitial) { fetchStatus(); fetchActivity(); fetchBreakdown(); }
      state.statusTimer = setInterval(fetchStatus, CONFIG.STATUS_POLL_MS);
      state.activityTimer = setInterval(fetchActivity, CONFIG.ACTIVITY_POLL_MS);
      setInterval(fetchBreakdown, 15000);
      state.isPolling = true;
    }

    function stopPolling() {
      if (state.statusTimer) clearInterval(state.statusTimer);
      if (state.activityTimer) clearInterval(state.activityTimer);
      state.isPolling = false;
    }

    function refreshAll() { fetchStatus(); fetchActivity(); fetchBreakdown(); }

    function init() {
      cacheDom(); initCountUp(); initChart(); setupVisibilityHandler(); startPolling();
      $(document).on('keydown', function(e) { if (e.ctrlKey && e.key === 'r') { e.preventDefault(); refreshAll(); } });
      $(window).on('beforeunload', stopPolling);
    }

    $(function() { init(); });

    return { refreshAll: refreshAll, stopPolling: stopPolling };
  })(jQuery);
  </script>

</body>
</html>
