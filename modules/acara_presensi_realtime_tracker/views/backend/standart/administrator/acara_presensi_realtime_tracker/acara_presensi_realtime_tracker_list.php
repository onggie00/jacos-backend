<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<!-- CDN Libraries -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.8.0/countUp.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>

<style>
/* ===== LIVE INDICATOR ===== */
.live-dot {
  display: inline-block;
  width: 10px;
  height: 10px;
  background: var(--labs-success);
  border-radius: 50%;
  margin-right: 6px;
  animation: livePulse 1.5s ease-in-out infinite;
  transition: background 0.3s;
}
.live-dot.offline {
  background: var(--labs-danger);
  animation: livePulseOffline 1.5s ease-in-out infinite;
}
.live-dot.loading {
  background: var(--labs-warning);
  animation: none;
}
@keyframes livePulse {
  0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(21,128,61,0.5); }
  50% { opacity: 0.6; box-shadow: 0 0 0 6px rgba(21,128,61,0); }
}
@keyframes livePulseOffline {
  0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(185,28,28,0.5); }
  50% { opacity: 0.6; box-shadow: 0 0 0 6px rgba(185,28,28,0); }
}

/* ===== HERO STATS ===== */
.hero-section {
  background: linear-gradient(135deg, var(--labs-primary), var(--labs-primary-dark));
  border-radius: var(--labs-radius-lg);
  padding: 30px 20px;
  color: #fff;
  margin-bottom: var(--labs-space-5);
  box-shadow: var(--labs-shadow-lg);
}
.hero-section .stat-box {
  text-align: center;
  padding: var(--labs-space-4) var(--labs-space-3);
}
.hero-section .stat-number {
  font-size: 42px;
  font-weight: 700;
  line-height: 1;
  margin-bottom: 5px;
}
.hero-section .stat-label {
  font-size: 13px;
  opacity: 0.85;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.hero-section .stat-wajib .stat-number { color: #fff; }
.hero-section .stat-sudah .stat-number { color: var(--labs-success-soft); }
.hero-section .stat-belum .stat-number { color: var(--labs-danger-soft); }

/* ===== PROGRESS CIRCLE ===== */
.progress-circle-container {
  text-align: center;
  padding: var(--labs-space-4);
}
.progress-circle {
  position: relative;
  width: 160px;
  height: 160px;
  margin: 0 auto;
}
.progress-circle canvas { display: block; }
.progress-circle-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
}
.progress-circle-text .percentage {
  font-size: 36px;
  font-weight: 700;
  color: #fff;
  line-height: 1;
}
.progress-circle-text .percentage-label {
  font-size: 12px;
  color: rgba(255,255,255,0.7);
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* ===== PROGRESS BAR GRADIENT ===== */
.progress-bar-gradient {
  height: 12px;
  border-radius: 20px;
  background: rgba(255,255,255,0.2);
  overflow: hidden;
  margin-top: var(--labs-space-4);
}
.progress-bar-gradient .bar-fill {
  height: 100%;
  border-radius: 20px;
  background: linear-gradient(90deg, var(--labs-success), #166534);
  transition: width 0.8s ease;
  width: 0%;
}

/* ===== LIST PANELS ===== */
.list-panel {
  min-height: 300px;
  max-height: 500px;
  overflow-y: auto;
}
.list-item {
  display: flex;
  align-items: center;
  padding: var(--labs-space-3) var(--labs-space-4);
  border-bottom: 1px solid var(--labs-border);
  transition: var(--labs-transition-fast);
}
.list-item:hover { background: var(--labs-bg-soft); }
.list-item:last-child { border-bottom: none; }
.list-item .avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
  color: #fff;
  margin-right: var(--labs-space-3);
  flex-shrink: 0;
}
.list-item .avatar-sudah { background: linear-gradient(135deg, var(--labs-success), #166534); }
.list-item .avatar-belum { background: linear-gradient(135deg, #94A3B8, #64748B); }
.list-item .item-info { flex: 1; min-width: 0; }
.list-item .item-name {
  font-weight: 600;
  font-size: 13px;
  color: var(--labs-text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.list-item .item-role {
  font-size: 11px;
  color: var(--labs-text-muted);
  text-transform: uppercase;
}
.list-item .item-time {
  font-size: 11px;
  color: var(--labs-text-muted);
  white-space: nowrap;
  margin-left: var(--labs-space-3);
}

/* ===== HIGHLIGHT ANIMATION ===== */
.list-item.highlight-new {
  background: var(--labs-success-soft) !important;
  animation: highlightFade 2s ease forwards;
}
@keyframes highlightFade {
  0% { background: var(--labs-success-soft); }
  70% { background: var(--labs-success-soft); }
  100% { background: transparent; }
}

/* ===== ACTIVITY FEED ===== */
.activity-feed { max-height: 350px; overflow-y: auto; }
.activity-item {
  display: flex;
  padding: var(--labs-space-3) var(--labs-space-4);
  border-bottom: 1px solid var(--labs-border);
}
.activity-item:last-child { border-bottom: none; }
.activity-item .activity-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--labs-success), #166534);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: var(--labs-space-3);
  flex-shrink: 0;
}
.activity-item .activity-icon i { color: #fff; font-size: 14px; }
.activity-item .activity-content { flex: 1; }
.activity-item .activity-text {
  font-size: 13px;
  color: var(--labs-text);
  margin-bottom: 2px;
}
.activity-item .activity-text strong { color: var(--labs-success); }
.activity-item .activity-time {
  font-size: 11px;
  color: var(--labs-text-muted);
}

/* ===== 100% COMPLETE BANNER ===== */
.complete-banner {
  display: none;
  background: linear-gradient(135deg, var(--labs-success), #166534);
  color: #fff;
  text-align: center;
  padding: var(--labs-space-3);
  border-radius: var(--labs-radius);
  margin-bottom: var(--labs-space-4);
  font-weight: 600;
  font-size: 16px;
  box-shadow: var(--labs-shadow);
}
.complete-banner.show { display: block; animation: fadeInDown 0.5s ease; }
.complete-banner i { margin-right: var(--labs-space-2); }

/* ===== CHART ===== */
.chart-container { position: relative; height: 250px; }

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .hero-section .stat-number { font-size: 32px; }
  .progress-circle { width: 120px; height: 120px; }
  .progress-circle-text .percentage { font-size: 28px; }
}

/* ===== TV/PROJECTOR MODE ===== */
@media (min-width: 1400px) {
  .hero-section { padding: 40px 30px; }
  .hero-section .stat-number { font-size: 52px; }
  .hero-section .stat-label { font-size: 15px; }
  .progress-circle { width: 200px; height: 200px; }
  .progress-circle-text .percentage { font-size: 44px; }
  .list-item { padding: var(--labs-space-4) 18px; }
  .list-item .item-name { font-size: 15px; }
  .list-item .avatar { width: 44px; height: 44px; font-size: 16px; }
  .activity-item { padding: var(--labs-space-4) 18px; }
  .activity-item .activity-text { font-size: 14px; }
}
</style>

<!-- Content Header -->
<section class="content-header">
  <h1>
    <i class="fa fa-tachometer"></i> Monitoring Evaluasi
    <small class="labs-text-muted" id="header-acara-name"><?= htmlspecialchars($acara->nama_acara) ?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="<?= site_url('administrator/acara') ?>"><i class="fa fa-calendar"></i> Acara</a></li>
    <li class="active">Monitoring Evaluasi</li>
  </ol>
</section>

<!-- Main Content -->
<section class="content">

  <!-- Top Bar: Acara Info + Controls -->
  <div class="labs-card labs-mb-4">
    <div class="labs-card__body" style="padding: var(--labs-space-3) var(--labs-space-5)">
      <div class="labs-flex-between" style="flex-wrap:wrap;gap:var(--labs-space-3)">
        <div class="labs-flex labs-flex-gap-4" style="flex-wrap:wrap;align-items:center">
          <span class="labs-fs-sm labs-text-muted"><i class="fa fa-calendar labs-text-primary"></i> <span id="info-tanggal"><?= date('d M Y', strtotime($acara->waktu_mulai)) ?></span></span>
          <span class="labs-fs-sm labs-text-muted"><i class="fa fa-clock-o labs-text-primary"></i> <span id="info-waktu"><?= date('H:i', strtotime($acara->waktu_mulai)) ?> - <?= date('H:i', strtotime($acara->waktu_selesai)) ?></span></span>
          <span class="labs-fs-sm labs-text-muted"><i class="fa fa-map-marker labs-text-primary"></i> <span id="info-lokasi"><?= htmlspecialchars($acara->lokasi) ?></span></span>
          <span class="labs-fs-sm labs-text-muted"><i class="fa fa-question-circle labs-text-primary"></i> <span id="info-pertanyaan"><?= $acara->jumlah_pertanyaan ?> Pertanyaan</span></span>
          <span id="connection-status" class="labs-fs-xs labs-text-danger" style="display:none">
            <i class="fa fa-exclamation-triangle"></i> Koneksi terputus
          </span>
        </div>
        <div class="labs-flex labs-flex-gap-2" style="align-items:center">
          <span class="live-dot" id="live-dot"></span>
          <span id="live-label" class="labs-fs-sm labs-fw-semi" style="color:var(--labs-success);margin-right:var(--labs-space-3)">LIVE</span>
          <button class="labs-btn labs-btn--primary labs-btn--sm" onclick="TrackerApp.refreshAll()" title="Refresh (Ctrl+R)">
            <i class="fa fa-refresh"></i> Refresh
          </button>
          <a href="<?= site_url('administrator/acara') ?>" class="labs-btn labs-btn--default labs-btn--sm">
            <i class="fa fa-arrow-left"></i> Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- 100% Complete Banner -->
  <div class="complete-banner" id="complete-banner">
    <i class="fa fa-trophy"></i> Semua peserta sudah mengisi evaluasi!
  </div>

  <!-- HERO SECTION: Progress + Stats -->
  <div class="row">
    <div class="col-md-12">
      <div class="hero-section">
        <div class="row">
          <!-- Progress Circle -->
          <div class="col-md-3 col-sm-6">
            <div class="progress-circle-container">
              <div class="progress-circle">
                <canvas id="progressCanvas" width="160" height="160"></canvas>
                <div class="progress-circle-text">
                  <div class="percentage" id="stat-persentase">0%</div>
                  <div class="percentage-label">Selesai</div>
                </div>
              </div>
            </div>
          </div>
          <!-- Stats -->
          <div class="col-md-3 col-sm-6">
            <div class="stat-box stat-wajib">
              <div class="stat-number" id="stat-wajib">0</div>
              <div class="stat-label">Total Wajib Isi</div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="stat-box stat-sudah">
              <div class="stat-number" id="stat-sudah">0</div>
              <div class="stat-label">Sudah Mengisi</div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="stat-box stat-belum">
              <div class="stat-number" id="stat-belum">0</div>
              <div class="stat-label">Belum Mengisi</div>
            </div>
          </div>
        </div>
        <!-- Progress Bar -->
        <div class="row">
          <div class="col-md-10 col-md-offset-1">
            <div class="progress-bar-gradient">
              <div class="bar-fill" id="progress-bar-fill"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MAIN CONTENT: Lists + Activity Feed -->
  <div class="row">
    <!-- Sudah Mengisi -->
    <div class="col-md-5">
      <div class="labs-card">
        <div class="labs-card__header">
          <h3 class="labs-card__title"><i class="fa fa-check-circle" style="color:var(--labs-success)"></i> Sudah Mengisi</h3>
          <span class="labs-badge labs-badge--success labs-badge--solid" id="badge-sudah">0</span>
        </div>
        <div class="list-panel" style="padding:0">
          <div id="list-sudah-isi">
            <div class="labs-empty">
              <i class="fa fa-spinner fa-spin"></i>
              <p>Memuat data...</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Belum Mengisi -->
    <div class="col-md-4">
      <div class="labs-card">
        <div class="labs-card__header">
          <h3 class="labs-card__title"><i class="fa fa-times-circle" style="color:var(--labs-danger)"></i> Belum Mengisi</h3>
          <span class="labs-badge labs-badge--danger labs-badge--solid" id="badge-belum">0</span>
        </div>
        <div class="list-panel" style="padding:0">
          <div id="list-belum-isi">
            <div class="labs-empty">
              <i class="fa fa-spinner fa-spin"></i>
              <p>Memuat data...</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Activity Feed + Chart -->
    <div class="col-md-3">
      <div class="labs-card labs-mb-4">
        <div class="labs-card__header">
          <h3 class="labs-card__title"><i class="fa fa-rss" style="color:var(--labs-info)"></i> Aktivitas Terbaru</h3>
        </div>
        <div class="activity-feed" style="padding:0" id="activity-feed">
          <div class="labs-empty">
            <i class="fa fa-spinner fa-spin"></i>
            <p>Memuat aktivitas...</p>
          </div>
        </div>
      </div>

      <!-- Chart Breakdown -->
      <div class="labs-card">
        <div class="labs-card__header">
          <h3 class="labs-card__title"><i class="fa fa-pie-chart" style="color:var(--labs-orange)"></i> Breakdown per Role</h3>
        </div>
        <div class="labs-card__body">
          <div class="chart-container">
            <canvas id="chartBreakdown"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>

<!-- Toast Container -->
<div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:9999;"></div>

<script>
/**
 * TrackerApp - Realtime Evaluation Monitor
 * 
 * Polling intervals:
 * - Status: 7 detik (balance antara realtime feel & beban server)
 * - Activity: 10 detik (feed tidak perlu secepat stats counter)
 */
var TrackerApp = (function($) {
  'use strict';

  // ==================== CONFIG ====================
  var CONFIG = {
    id_acara: <?= (int) $id_acara ?>,
    ajaxStatusUrl: '<?= site_url("administrator/acara_presensi_realtime_tracker/ajax_status/" . $id_acara) ?>',
    ajaxActivityUrl: '<?= site_url("administrator/acara_presensi_realtime_tracker/ajax_activity/" . $id_acara) ?>',
    ajaxBreakdownUrl: '<?= site_url("administrator/acara_presensi_realtime_tracker/ajax_breakdown/" . $id_acara) ?>',
    STATUS_POLL_MS: 7000,
    ACTIVITY_POLL_MS: 10000,
    MAX_ACTIVITY_ITEMS: 10
  };

  // ==================== STATE ====================
  var state = {
    currentWajib: 0,
    currentSudah: 0,
    currentBelum: 0,
    currentPersen: 0,
    prevSudahNpps: {},
    prevBelumNpps: {},
    activityNpps: {},
    initialized: false,
    confettiTriggered: false,
    isPolling: false,
    statusFailCount: 0,
    activityFailCount: 0,
    statusTimer: null,
    activityTimer: null,
    chartBreakdown: null,
    countUpWajib: null,
    countUpSudah: null,
    countUpBelum: null,
    countUpPersen: null
  };

  // ==================== DOM REFS ====================
  var $el = {};

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

  // ==================== XSS PROTECTION ====================
  function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }

  function escapeAttr(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  }

  // ==================== COUNTUP INIT ====================
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
    var w = canvas.width;
    var h = canvas.height;
    var cx = w / 2;
    var cy = h / 2;
    var radius = Math.min(cx, cy) - 10;
    var lineWidth = 12;

    ctx.clearRect(0, 0, w, h);

    // Background circle
    ctx.beginPath();
    ctx.arc(cx, cy, radius, 0, Math.PI * 2);
    ctx.strokeStyle = 'rgba(255,255,255,0.2)';
    ctx.lineWidth = lineWidth;
    ctx.stroke();

    // Progress arc
    if (percent > 0) {
      var startAngle = -Math.PI / 2;
      var endAngle = startAngle + (Math.PI * 2 * percent / 100);
      ctx.beginPath();
      ctx.arc(cx, cy, radius, startAngle, endAngle);
      ctx.strokeStyle = '#2ecc71';
      ctx.lineWidth = lineWidth;
      ctx.lineCap = 'round';
      ctx.stroke();
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

    state.currentWajib = wajib;
    state.currentSudah = sudah;
    state.currentBelum = belum;
    state.currentPersen = persen;
  }

  // ==================== RENDER LISTS ====================
  function getInitials(name) {
    if (!name) return '?';
    var parts = name.trim().split(/\s+/);
    if (parts.length >= 2) {
      return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
  }

  function formatRelativeTime(dateStr) {
    if (!dateStr) return '';
    var now = new Date();
    var then = new Date(dateStr.replace(' ', 'T') + (dateStr.indexOf('+') === -1 && dateStr.indexOf('Z') === -1 ? '+07:00' : ''));
    var diffMs = now - then;
    var diffSec = Math.floor(diffMs / 1000);
    if (diffSec < 5) return 'baru saja';
    if (diffSec < 60) return diffSec + ' detik lalu';
    var diffMin = Math.floor(diffSec / 60);
    if (diffMin < 60) return diffMin + ' menit lalu';
    var diffHr = Math.floor(diffMin / 60);
    if (diffHr < 24) return diffHr + ' jam lalu';
    var diffDay = Math.floor(diffHr / 24);
    return diffDay + ' hari lalu';
  }

  function buildListItem(item, type) {
    var safeName = escapeHtml(item.peserta || '-');
    var safeNpp = escapeAttr(item.npp || '');
    var safeRole = escapeHtml(item.role || '');
    var safeTime = escapeAttr(item.waktu_submit_terakhir || '');
    var initials = getInitials(item.peserta);
    var avatarClass = type === 'sudah' ? 'avatar-sudah' : 'avatar-belum';
    var timeHtml = '';
    if (type === 'sudah' && item.waktu_submit_terakhir) {
      timeHtml = '<div class="item-time" title="' + safeTime + '">' + formatRelativeTime(item.waktu_submit_terakhir) + '</div>';
    }
    return '<div class="list-item" data-npp="' + safeNpp + '">' +
      '<div class="avatar ' + avatarClass + '">' + escapeHtml(initials) + '</div>' +
      '<div class="item-info">' +
        '<div class="item-name" title="' + safeName + '">' + safeName + '</div>' +
        '<div class="item-role">' + safeRole + '</div>' +
      '</div>' +
      timeHtml +
    '</div>';
  }

  function renderListSudah(list) {
    if (!list || list.length === 0) {
      $el.listSudah.html('<div class="labs-empty"><i class="fa fa-inbox"></i><p>Belum ada peserta yang mengisi evaluasi</p></div>');
      return;
    }
    var html = '';
    for (var i = 0; i < list.length; i++) {
      html += buildListItem(list[i], 'sudah');
    }
    $el.listSudah.html(html);
  }

  function renderListBelum(list) {
    if (!list || list.length === 0) {
      $el.listBelum.html('<div class="labs-empty"><i class="fa fa-check-circle" style="color:var(--labs-success)"></i><p>Semua peserta sudah mengisi evaluasi</p></div>');
      return;
    }
    var html = '';
    for (var i = 0; i < list.length; i++) {
      html += buildListItem(list[i], 'belum');
    }
    $el.listBelum.html(html);
  }

  // ==================== DETECT CHANGES & ANIMATE ====================
  function detectAndAnimateChanges(data) {
    var newSudahNpps = {};
    var newBelumNpps = {};
    var i, item, npp;

    if (data.list_sudah_isi) {
      for (i = 0; i < data.list_sudah_isi.length; i++) {
        newSudahNpps[data.list_sudah_isi[i].npp] = data.list_sudah_isi[i];
      }
    }
    if (data.list_belum_isi) {
      for (i = 0; i < data.list_belum_isi.length; i++) {
        newBelumNpps[data.list_belum_isi[i].npp] = data.list_belum_isi[i];
      }
    }

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
        item = movedToSudah[i];
        animateNewSudahItem(item.npp);
        showToast(item.peserta);
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
        setTimeout(function() {
          $item.removeClass('highlight-new');
        }, 2000);
      }, 600);
    }
  }

  // ==================== ACTIVITY FEED ====================
  function renderActivityFeed(feed) {
    if (!feed || feed.length === 0) {
      $el.activityFeed.html('<div class="labs-empty" style="padding:30px 20px"><i class="fa fa-rss"></i><p>Menunggu aktivitas evaluasi</p></div>');
      return;
    }

    var newActivityNpps = {};
    var i, item;

    for (i = 0; i < feed.length; i++) {
      newActivityNpps[feed[i].npp] = feed[i];
    }

    var html = '';
    var count = Math.min(feed.length, CONFIG.MAX_ACTIVITY_ITEMS);
    for (i = 0; i < count; i++) {
      item = feed[i];
      var isNew = !state.activityNpps[item.npp] && state.initialized;
      var animClass = isNew ? ' animate__animated animate__slideInDown' : '';
      var safeActName = escapeHtml(item.peserta || '-');
      var safeActNpp = escapeAttr(item.npp || '');
      var safeActTime = escapeAttr(item.waktu_submit || '');
      html += '<div class="activity-item' + animClass + '" data-npp="' + safeActNpp + '">' +
        '<div class="activity-icon"><i class="fa fa-check"></i></div>' +
        '<div class="activity-content">' +
          '<div class="activity-text"><strong>' + safeActName + '</strong> mengisi evaluasi</div>' +
          '<div class="activity-time" title="' + safeActTime + '">' + formatRelativeTime(item.waktu_submit) + '</div>' +
        '</div>' +
      '</div>';
    }

    $el.activityFeed.html(html);
    state.activityNpps = newActivityNpps;
  }

  // ==================== CHART ====================
  function initChart() {
    if (!$el.chartCanvas) return;
    var ctx = $el.chartCanvas.getContext('2d');
    state.chartBreakdown = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: [],
        datasets: [{
          data: [],
          backgroundColor: [
            '#C2410C', '#15803D', '#B91C1C', '#B45309',
            '#0E7490', '#EA580C', '#9A3412', '#1C1917'
          ],
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 12,
              usePointStyle: true,
              font: { size: 11 }
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                var label = context.label || '';
                var value = context.parsed || 0;
                var dataset = context.dataset.data;
                var total = dataset.reduce(function(a, b) { return a + b; }, 0);
                var pct = total > 0 ? Math.round((value / total) * 100) : 0;
                return label + ': ' + value + ' (' + pct + '%)';
              }
            }
          }
        }
      }
    });
  }

  function updateChart(breakdown) {
    if (!state.chartBreakdown || !breakdown || breakdown.length === 0) return;

    var labels = [];
    var dataBelum = [];
    var dataSudah = [];
    var i;

    for (i = 0; i < breakdown.length; i++) {
      var role = breakdown[i].role || 'lainnya';
      var label = role.replace(/_/g, ' ').replace(/\b\w/g, function(c) { return c.toUpperCase(); });
      labels.push(label);
      dataBelum.push(parseInt(breakdown[i].belum) || 0);
      dataSudah.push(parseInt(breakdown[i].sudah) || 0);
    }

    state.chartBreakdown.data.labels = labels;
    state.chartBreakdown.data.datasets = [
      {
        label: 'Sudah',
        data: dataSudah,
        backgroundColor: '#15803D',
        borderWidth: 0
      },
      {
        label: 'Belum',
        data: dataBelum,
        backgroundColor: '#B91C1C',
        borderWidth: 0
      }
    ];
    state.chartBreakdown.update();
  }

  // ==================== TOAST ====================
  function showToast(nama) {
    var safeNama = escapeHtml(nama || 'Peserta');
    Toastify({
      text: '<i class="fa fa-check-circle"></i> ' + safeNama + ' baru saja mengisi evaluasi',
      duration: 5000,
      gravity: 'top',
      position: 'right',
      escapeHtml: false,
      style: {
        background: 'linear-gradient(135deg, #15803D, #166534)',
        borderRadius: '8px',
        boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
        fontSize: '13px',
        padding: '10px 16px'
      },
      offset: { x: 0, y: 60 }
    }).showToast();
  }

  // ==================== CONFETTI ====================
  function triggerConfetti() {
    if (typeof confetti !== 'function') return;
    var end = Date.now() + 2000;
    var colors = ['#15803D', '#166534', '#F59E0B', '#0E7490'];

    (function frame() {
      confetti({
        particleCount: 3,
        angle: 60,
        spread: 55,
        origin: { x: 0, y: 0.6 },
        colors: colors
      });
      confetti({
        particleCount: 3,
        angle: 120,
        spread: 55,
        origin: { x: 1, y: 0.6 },
        colors: colors
      });
      if (Date.now() < end) {
        requestAnimationFrame(frame);
      }
    })();
  }

  // ==================== LIVE INDICATOR ====================
  function setLiveOnline() {
    $el.liveDot.removeClass('offline loading');
    $el.liveLabel.text('LIVE').css('color', 'var(--labs-success)');
    $el.connectionStatus.hide();
    state.statusFailCount = 0;
  }

  function setLiveOffline() {
    $el.liveDot.addClass('offline').removeClass('loading');
    $el.liveLabel.text('OFFLINE').css('color', 'var(--labs-danger)');
    $el.connectionStatus.show();
  }

  function setLiveLoading() {
    $el.liveDot.addClass('loading').removeClass('offline');
  }

  // ==================== AJAX CALLS ====================
  function fetchStatus() {
    setLiveLoading();
    $.ajax({
      url: CONFIG.ajaxStatusUrl,
      type: 'GET',
      dataType: 'json',
      timeout: 10000,
      success: function(resp) {
        if (resp && resp.status === 1 && resp.data) {
          setLiveOnline();
          updateStats(resp.data);
          detectAndAnimateChanges(resp.data);
        } else {
          handleStatusError();
        }
      },
      error: function() {
        handleStatusError();
      }
    });
  }

  function handleStatusError() {
    state.statusFailCount++;
    if (state.statusFailCount >= 2) {
      setLiveOffline();
    }
  }

  function fetchActivity() {
    $.ajax({
      url: CONFIG.ajaxActivityUrl,
      type: 'GET',
      dataType: 'json',
      timeout: 10000,
      success: function(resp) {
        if (resp && resp.status === 1 && resp.data) {
          renderActivityFeed(resp.data);
          state.activityFailCount = 0;
        } else {
          state.activityFailCount++;
        }
      },
      error: function() {
        state.activityFailCount++;
      }
    });
  }

  function fetchBreakdown() {
    $.ajax({
      url: CONFIG.ajaxBreakdownUrl,
      type: 'GET',
      dataType: 'json',
      timeout: 10000,
      success: function(resp) {
        if (resp && resp.status === 1 && resp.data) {
          updateChart(resp.data);
        }
      }
    });
  }

  // ==================== POLLING CONTROL ====================
  function setupVisibilityHandler() {
    var hidden, visibilityChange;
    if (typeof document.hidden !== 'undefined') {
      hidden = 'hidden';
      visibilityChange = 'visibilitychange';
    } else if (typeof document.msHidden !== 'undefined') {
      hidden = 'msHidden';
      visibilityChange = 'msvisibilitychange';
    } else if (typeof document.webkitHidden !== 'undefined') {
      hidden = 'webkitHidden';
      visibilityChange = 'webkitvisibilitychange';
    }

    if (typeof document[hidden] === 'undefined') return;

    document.addEventListener(visibilityChange, function() {
      if (document[hidden]) {
        stopPolling();
        console.log('[Tracker] Tab hidden, polling paused');
      } else {
        if (!state.isPolling) {
          startPolling(true);
          refreshAll();
          console.log('[Tracker] Tab visible, polling resumed');
        }
      }
    }, false);
  }

  function startPolling(skipInitial) {
    if (!skipInitial) {
      fetchStatus();
      fetchActivity();
      fetchBreakdown();
    }

    state.statusTimer = setInterval(function() {
      fetchStatus();
    }, CONFIG.STATUS_POLL_MS);

    state.activityTimer = setInterval(function() {
      fetchActivity();
    }, CONFIG.ACTIVITY_POLL_MS);

    setInterval(function() {
      fetchBreakdown();
    }, 15000);

    state.isPolling = true;
  }

  function stopPolling() {
    if (state.statusTimer) clearInterval(state.statusTimer);
    if (state.activityTimer) clearInterval(state.activityTimer);
    state.isPolling = false;
  }

  // ==================== PUBLIC API ====================
  function refreshAll() {
    fetchStatus();
    fetchActivity();
    fetchBreakdown();
  }

  function init() {
    cacheDom();
    initCountUp();
    initChart();
    setupVisibilityHandler();
    startPolling();

    $(document).on('keydown', function(e) {
      if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        refreshAll();
      }
    });

    $(window).on('beforeunload', function() {
      stopPolling();
    });
  }

  $(function() {
    init();
  });

  return {
    refreshAll: refreshAll,
    stopPolling: stopPolling,
    getState: function() { return state; }
  };

})(jQuery);
</script>
