<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Peserta Acara - <?= htmlspecialchars($acara->nama_acara) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: var(--labs-font-body);
      background: var(--labs-bg-page);
      color: var(--labs-text);
      min-height: 100vh;
      margin: 0;
    }

    /* TOP NAV */
    .top-nav {
      background: var(--labs-bg-card);
      padding: var(--labs-space-3) var(--labs-space-5);
      box-shadow: var(--labs-shadow);
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .top-nav .nav-title {
      font-weight: 700;
      font-size: 14px;
      color: var(--labs-text-muted);
      display: flex;
      align-items: center;
      gap: var(--labs-space-2);
    }
    .top-nav .nav-title i { color: var(--labs-primary); }

    /* HERO */
    .hero-header {
      background: linear-gradient(135deg, var(--labs-primary), var(--labs-primary-dark));
      border-radius: var(--labs-radius-lg);
      padding: 28px 30px;
      color: #fff;
      margin-bottom: var(--labs-space-5);
      position: relative;
      overflow: hidden;
      box-shadow: var(--labs-shadow-lg);
    }
    .hero-header::before {
      content: '';
      position: absolute;
      top: -50%; right: -20%;
      width: 300px; height: 300px;
      background: rgba(255,255,255,0.05);
      border-radius: 50%;
    }
    .hero-header h1 {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: var(--labs-space-2);
      position: relative;
    }
    .hero-header .header-meta {
      display: flex; flex-wrap: wrap; gap: var(--labs-space-5);
      font-size: 14px; opacity: 0.9; position: relative;
    }
    .hero-header .meta-item {
      display: flex; align-items: center; gap: var(--labs-space-2);
    }

    /* CONTAINER */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: var(--labs-space-5);
    }

    /* STATS ROW */
    .stats-row {
      display: grid;
      grid-template-columns: 200px 1fr 1fr 1fr;
      gap: var(--labs-space-5);
      align-items: center;
      margin-bottom: var(--labs-space-5);
    }
    .chart-container {
      position: relative;
      width: 180px;
      height: 180px;
      margin: 0 auto;
    }
    .stat-card {
      background: var(--labs-bg-card);
      border-radius: var(--labs-radius-md);
      padding: var(--labs-space-5);
      text-align: center;
      box-shadow: var(--labs-shadow);
      border: 1px solid var(--labs-border);
      transition: var(--labs-transition);
    }
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--labs-shadow-md);
    }
    .stat-card .stat-number {
      font-size: 42px;
      font-weight: 700;
      line-height: 1;
      margin-bottom: 6px;
    }
    .stat-card .stat-label {
      font-size: 12px;
      color: var(--labs-text-muted);
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .stat-total .stat-number { color: var(--labs-info); }
    .stat-hadir .stat-number { color: var(--labs-success); }
    .stat-tidak .stat-number { color: var(--labs-danger); }

    /* PROGRESS BAR */
    .progress-bar-container { margin-top: var(--labs-space-3); }
    .progress-bar-track {
      height: 10px; border-radius: 20px;
      background: var(--labs-bg-soft);
      overflow: hidden;
    }
    .progress-bar-fill {
      height: 100%; border-radius: 20px;
      background: linear-gradient(90deg, var(--labs-success), #166534);
      transition: width 0.8s ease;
      width: 0%;
    }

    /* CONTENT GRID */
    .content-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: var(--labs-space-5);
    }

    /* LIST ITEM */
    .list-item {
      display: flex; align-items: center;
      padding: var(--labs-space-3) var(--labs-space-4);
      border-bottom: 1px solid var(--labs-border);
      transition: var(--labs-transition-fast);
    }
    .list-item:hover { background: var(--labs-bg-soft); }
    .list-item:last-child { border-bottom: none; }
    .list-item .avatar {
      width: 36px; height: 36px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 13px; color: #fff;
      margin-right: var(--labs-space-3); flex-shrink: 0;
    }
    .avatar-green { background: linear-gradient(135deg, var(--labs-success), #166534); }
    .avatar-red { background: linear-gradient(135deg, var(--labs-danger), #991B1B); }
    .list-item .item-info { flex: 1; min-width: 0; }
    .list-item .item-name {
      font-weight: 600; font-size: 13px; color: var(--labs-text);
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .list-item .item-meta {
      font-size: 11px;
      color: var(--labs-text-muted);
      display: flex; gap: var(--labs-space-3);
    }
    .list-item .item-meta span {
      display: flex; align-items: center; gap: 4px;
    }
    .list-item .item-meta i { color: var(--labs-text-light); }
    .list-item .item-time {
      font-size: 11px;
      color: var(--labs-text-muted);
      white-space: nowrap;
      margin-left: var(--labs-space-3);
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .container { padding: var(--labs-space-3); }
      .stats-row { grid-template-columns: 1fr 1fr; gap: var(--labs-space-3); }
      .stats-row .chart-wrap { grid-column: span 2; }
      .stat-card .stat-number { font-size: 32px; }
      .content-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- TOP NAV -->
  <div class="top-nav">
    <div class="nav-title">
      <i class="fa fa-users"></i> Daftar Peserta Acara
    </div>
    <div>
      <button class="labs-btn labs-btn--primary labs-btn--sm" onclick="location.reload()">
        <i class="fa fa-refresh"></i> Refresh
      </button>
    </div>
  </div>

  <div class="container">

    <!-- HERO HEADER -->
    <div class="hero-header">
      <h1><?= htmlspecialchars($acara->nama_acara) ?></h1>
      <div class="header-meta">
        <span class="meta-item"><i class="fa fa-calendar"></i> <?= date('d M Y', strtotime($acara->waktu_mulai)) ?></span>
        <span class="meta-item"><i class="fa fa-clock-o"></i> <?= date('H:i', strtotime($acara->waktu_mulai)) ?> - <?= date('H:i', strtotime($acara->waktu_selesai)) ?></span>
        <span class="meta-item"><i class="fa fa-map-marker"></i> <?= htmlspecialchars($acara->lokasi) ?></span>
      </div>
    </div>

    <!-- STATS ROW -->
    <div class="stats-row">
      <div class="chart-wrap">
        <div class="chart-container">
          <canvas id="chartPie"></canvas>
        </div>
      </div>
      <div class="stat-card stat-total">
        <div class="stat-number"><?= $total_undangan ?></div>
        <div class="stat-label">Total Undangan</div>
      </div>
      <div class="stat-card stat-hadir">
        <div class="stat-number"><?= $total_hadir ?></div>
        <div class="stat-label">Hadir</div>
        <div class="progress-bar-container">
          <div class="progress-bar-track">
            <div class="progress-bar-fill" id="progressBar" style="width:0%"></div>
          </div>
        </div>
      </div>
      <div class="stat-card stat-tidak">
        <div class="stat-number"><?= $total_tidak ?></div>
        <div class="stat-label">Tidak Hadir</div>
      </div>
    </div>

    <!-- FILTER BAR -->
    <div class="labs-card labs-mb-5">
      <div class="labs-card__body" style="padding: var(--labs-space-3) var(--labs-space-4)">
        <div style="display:flex;gap:var(--labs-space-3);align-items:center;flex-wrap:wrap">
          <div style="flex:1;min-width:200px;position:relative">
            <i class="fa fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--labs-text-light);font-size:12px"></i>
            <input type="text" id="searchInput" placeholder="Cari nama atau NPP..." style="width:100%;padding:8px 14px 8px 32px;border:1px solid var(--labs-border-strong);border-radius:var(--labs-radius-sm);font-size:13px;outline:none;height:36px">
          </div>
          <select id="filterRole" style="padding:8px 14px;border:1px solid var(--labs-border-strong);border-radius:var(--labs-radius-sm);font-size:13px;outline:none;background:#fff;height:36px">
            <option value="">Semua Role</option>
            <?php
            $unique_roles = array_unique(array_merge(
                array_column($list_hadir, 'role'),
                array_column($list_tidak, 'role')
            ));
            sort($unique_roles);
            foreach ($unique_roles as $r): ?>
              <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $r))) ?></option>
            <?php endforeach; ?>
          </select>
          <select id="filterStatus" style="padding:8px 14px;border:1px solid var(--labs-border-strong);border-radius:var(--labs-radius-sm);font-size:13px;outline:none;background:#fff;height:36px">
            <option value="">Semua Status</option>
            <option value="hadir">Hadir</option>
            <option value="tidak">Tidak Hadir</option>
          </select>
        </div>
      </div>
    </div>

    <!-- CONTENT GRID -->
    <div class="content-grid">

      <!-- HADIR -->
      <div class="labs-card">
        <div class="labs-card__header">
          <h3 class="labs-card__title"><i class="fa fa-check-circle" style="color:var(--labs-success)"></i> Hadir</h3>
          <span class="labs-badge labs-badge--success labs-badge--solid" id="badgeHadir"><?= $total_hadir ?></span>
        </div>
        <div style="max-height:500px;overflow-y:auto" id="listHadir">
          <?php if (empty($list_hadir)): ?>
            <div class="labs-empty"><i class="fa fa-inbox"></i><p>Belum ada peserta yang hadir</p></div>
          <?php else: ?>
            <?php foreach ($list_hadir as $p): ?>
              <div class="list-item" data-npp="<?= htmlspecialchars($p['npp']) ?>" data-role="<?= htmlspecialchars($p['role']) ?>" data-status="hadir">
                <div class="avatar avatar-green"><?= strtoupper(substr($p['nama'], 0, 2)) ?></div>
                <div class="item-info">
                  <div class="item-name" title="<?= htmlspecialchars($p['nama']) ?>"><?= htmlspecialchars($p['nama']) ?></div>
                  <div class="item-meta">
                    <span><i class="fa fa-id-card"></i> <?= htmlspecialchars($p['npp']) ?></span>
                    <span><i class="fa fa-tag"></i> <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $p['role']))) ?></span>
                  </div>
                </div>
                <?php if (!empty($p['waktu_presensi'])): ?>
                  <div class="item-time"><?= date('H:i', strtotime($p['waktu_presensi'])) ?></div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- TIDAK HADIR -->
      <div class="labs-card">
        <div class="labs-card__header">
          <h3 class="labs-card__title"><i class="fa fa-times-circle" style="color:var(--labs-danger)"></i> Tidak Hadir</h3>
          <span class="labs-badge labs-badge--danger labs-badge--solid" id="badgeTidak"><?= $total_tidak ?></span>
        </div>
        <div style="max-height:500px;overflow-y:auto" id="listTidak">
          <?php if (empty($list_tidak)): ?>
            <div class="labs-empty"><i class="fa fa-check-circle" style="color:var(--labs-success)"></i><p>Semua peserta undangan sudah hadir</p></div>
          <?php else: ?>
            <?php foreach ($list_tidak as $p): ?>
              <div class="list-item" data-npp="<?= htmlspecialchars($p['npp']) ?>" data-role="<?= htmlspecialchars($p['role']) ?>" data-status="tidak">
                <div class="avatar avatar-red"><?= strtoupper(substr($p['nama'], 0, 2)) ?></div>
                <div class="item-info">
                  <div class="item-name" title="<?= htmlspecialchars($p['nama']) ?>"><?= htmlspecialchars($p['nama']) ?></div>
                  <div class="item-meta">
                    <span><i class="fa fa-id-card"></i> <?= htmlspecialchars($p['npp']) ?></span>
                    <span><i class="fa fa-tag"></i> <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $p['role']))) ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>

  <!-- Footer -->
  <div style="text-align:center;padding:var(--labs-space-5);color:var(--labs-text-light);font-size:12px">
    <i class="fa fa-graduation-cap"></i> Labschool Management System
  </div>

  <script>
    // PIE CHART
    var ctx = document.getElementById('chartPie').getContext('2d');
    var chartPie = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Hadir', 'Tidak Hadir'],
        datasets: [{
          data: [<?= $total_hadir ?>, <?= $total_tidak ?>],
          backgroundColor: ['#15803D', '#B91C1C'],
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '65%',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                var total = ctx.dataset.data.reduce(function(a,b){return a+b;}, 0);
                var pct = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
              }
            }
          }
        }
      }
    });

    // PROGRESS BAR
    var totalUndangan = <?= $total_undangan ?>;
    var totalHadir = <?= $total_hadir ?>;
    var pct = totalUndangan > 0 ? Math.round(totalHadir / totalUndangan * 100) : 0;
    setTimeout(function() {
      document.getElementById('progressBar').style.width = pct + '%';
    }, 300);

    // FILTER & SEARCH
    function applyFilter() {
      var search = document.getElementById('searchInput').value.toLowerCase();
      var role = document.getElementById('filterRole').value;
      var status = document.getElementById('filterStatus').value;

      var items = document.querySelectorAll('.list-item');
      var countHadir = 0, countTidak = 0;

      items.forEach(function(item) {
        var name = (item.querySelector('.item-name').textContent || '').toLowerCase();
        var npp = (item.getAttribute('data-npp') || '').toLowerCase();
        var itemRole = item.getAttribute('data-role') || '';
        var itemStatus = item.getAttribute('data-status') || '';

        var matchSearch = !search || name.indexOf(search) >= 0 || npp.indexOf(search) >= 0;
        var matchRole = !role || itemRole === role;
        var matchStatus = !status || itemStatus === status;

        if (matchSearch && matchRole && matchStatus) {
          item.style.display = '';
          if (itemStatus === 'hadir') countHadir++;
          else countTidak++;
        } else {
          item.style.display = 'none';
        }
      });

      document.getElementById('badgeHadir').textContent = countHadir;
      document.getElementById('badgeTidak').textContent = countTidak;
    }

    document.getElementById('searchInput').addEventListener('input', applyFilter);
    document.getElementById('filterRole').addEventListener('change', applyFilter);
    document.getElementById('filterStatus').addEventListener('change', applyFilter);

    // Focus style untuk search input
    var searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('focus', function() {
      this.style.borderColor = 'var(--labs-primary)';
      this.style.boxShadow = '0 0 0 3px rgba(194,65,12,0.12)';
    });
    searchInput.addEventListener('blur', function() {
      this.style.borderColor = 'var(--labs-border-strong)';
      this.style.boxShadow = 'none';
    });
  </script>

</body>
</html>
