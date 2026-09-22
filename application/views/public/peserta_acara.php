<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Peserta Acara - <?= htmlspecialchars($acara->nama_acara) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background: #f0f2f5;
      color: #333;
      min-height: 100vh;
    }

    /* TOP NAV */
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
    .top-nav .nav-title { font-weight: 700; font-size: 14px; color: #666; }

    /* HERO */
    .hero-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
      top: -50%; right: -20%;
      width: 300px; height: 300px;
      background: rgba(255,255,255,0.05);
      border-radius: 50%;
    }
    .hero-header h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; position: relative; }
    .hero-header .header-meta {
      display: flex; flex-wrap: wrap; gap: 20px;
      font-size: 14px; opacity: 0.9; position: relative;
    }
    .hero-header .meta-item { display: flex; align-items: center; gap: 8px; }

    /* CONTAINER */
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }

    /* STATS ROW */
    .stats-row {
      display: grid;
      grid-template-columns: 200px 1fr 1fr 1fr;
      gap: 20px;
      align-items: center;
      margin-bottom: 20px;
    }
    .chart-container { position: relative; width: 180px; height: 180px; margin: 0 auto; }
    .stat-card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .stat-card .stat-number { font-size: 42px; font-weight: 700; line-height: 1; margin-bottom: 6px; }
    .stat-card .stat-label { font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1px; }
    .stat-total .stat-number { color: #3498db; }
    .stat-hadir .stat-number { color: #27ae60; }
    .stat-tidak .stat-number { color: #e74c3c; }

    /* PROGRESS BAR */
    .progress-bar-container { margin-top: 12px; }
    .progress-bar-track {
      height: 10px; border-radius: 5px;
      background: #e0e0e0; overflow: hidden;
    }
    .progress-bar-fill {
      height: 100%; border-radius: 5px;
      background: linear-gradient(90deg, #27ae60, #2ecc71);
      transition: width 0.8s ease; width: 0%;
    }

    /* FILTER BAR */
    .filter-bar {
      background: #fff;
      border-radius: 10px;
      padding: 14px 18px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      display: flex;
      gap: 12px;
      align-items: center;
      flex-wrap: wrap;
    }
    .filter-bar input[type="text"] {
      flex: 1;
      min-width: 200px;
      padding: 8px 14px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 13px;
      outline: none;
    }
    .filter-bar input[type="text"]:focus { border-color: #3498db; }
    .filter-bar select {
      padding: 8px 14px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 13px;
      outline: none;
      background: #fff;
    }

    /* CONTENT GRID */
    .content-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    /* CARD */
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
    .card-header .badge {
      background: #e0e0e0; color: #666;
      padding: 3px 10px; border-radius: 12px;
      font-size: 12px; font-weight: 600;
    }
    .badge-green { background: #d4edda !important; color: #27ae60 !important; }
    .badge-red { background: #fce4e4 !important; color: #e74c3c !important; }
    .card-body { padding: 0; max-height: 500px; overflow-y: auto; }

    /* LIST ITEM */
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
    .avatar-green { background: #27ae60; }
    .avatar-red { background: #e74c3c; }
    .list-item .item-info { flex: 1; min-width: 0; }
    .list-item .item-name {
      font-weight: 600; font-size: 13px; color: #333;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .list-item .item-meta { font-size: 11px; color: #999; display: flex; gap: 10px; }
    .list-item .item-meta span { display: flex; align-items: center; gap: 4px; }
    .list-item .item-time { font-size: 11px; color: #999; white-space: nowrap; margin-left: 10px; }

    /* EMPTY STATE */
    .zero-state { text-align: center; padding: 40px 20px; color: #999; }
    .zero-state i { font-size: 48px; margin-bottom: 10px; opacity: 0.3; display: block; }
    .zero-state h4 { font-weight: 600; color: #999; margin-bottom: 5px; }
    .zero-state p { font-size: 13px; color: #bbb; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .container { padding: 12px; }
      .stats-row { grid-template-columns: 1fr 1fr; gap: 12px; }
      .stats-row .chart-wrap { grid-column: span 2; }
      .stat-card .stat-number { font-size: 32px; }
      .content-grid { grid-template-columns: 1fr; }
      .filter-bar { flex-direction: column; }
      .filter-bar input[type="text"] { min-width: 100%; }
    }
  </style>
</head>
<body>

  <!-- TOP NAV -->
  <div class="top-nav">
    <div><span class="nav-title"><i class="fa fa-users"></i> Daftar Peserta Acara</span></div>
    <div>
      <button class="btn btn-primary" onclick="location.reload()" style="padding:6px 14px;border:1px solid #3498db;background:#3498db;color:#fff;border-radius:6px;cursor:pointer;font-size:13px;">
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
    <div class="filter-bar">
      <input type="text" id="searchInput" placeholder="🔍 Cari nama atau NPP...">
      <select id="filterRole">
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
      <select id="filterStatus">
        <option value="">Semua Status</option>
        <option value="hadir">Hadir</option>
        <option value="tidak">Tidak Hadir</option>
      </select>
    </div>

    <!-- CONTENT GRID -->
    <div class="content-grid">

      <!-- HADIR -->
      <div class="card">
        <div class="card-header">
          <h3><i class="fa fa-check-circle" style="color:#27ae60"></i> Hadir</h3>
          <span class="badge badge-green" id="badgeHadir"><?= $total_hadir ?></span>
        </div>
        <div class="card-body" id="listHadir">
          <?php if (empty($list_hadir)): ?>
            <div class="zero-state"><i class="fa fa-inbox"></i><h4>Belum Ada</h4><p>Belum ada peserta yang hadir</p></div>
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
      <div class="card">
        <div class="card-header">
          <h3><i class="fa fa-times-circle" style="color:#e74c3c"></i> Tidak Hadir</h3>
          <span class="badge badge-red" id="badgeTidak"><?= $total_tidak ?></span>
        </div>
        <div class="card-body" id="listTidak">
          <?php if (empty($list_tidak)): ?>
            <div class="zero-state"><i class="fa fa-check-circle" style="color:#27ae60"></i><h4>Semua Hadir!</h4><p>Semua peserta undangan sudah hadir</p></div>
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

  <script>
    // PIE CHART
    var ctx = document.getElementById('chartPie').getContext('2d');
    var chartPie = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Hadir', 'Tidak Hadir'],
        datasets: [{
          data: [<?= $total_hadir ?>, <?= $total_tidak ?>],
          backgroundColor: ['#27ae60', '#e74c3c'],
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
  </script>

</body>
</html>
