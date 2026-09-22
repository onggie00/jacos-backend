
<script type="text/javascript">
</script>

<style>
  .infobox-row { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
  .infobox-card {
    flex: 1; min-width: 200px; padding: 18px 20px; border-radius: 6px;
    color: #fff; position: relative; overflow: hidden;
  }
  .infobox-card .infobox-icon { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-size: 40px; opacity: 0.3; }
  .infobox-card .infobox-label { font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
  .infobox-card .infobox-value { font-size: 26px; font-weight: 700; }
  .bg-infobox-blue { background: linear-gradient(135deg, #3c8dbc, #2c6e9e); }
  .bg-infobox-green { background: linear-gradient(135deg, #00a65a, #00874a); }
  .bg-infobox-orange { background: linear-gradient(135deg, #f39c12, #d4890e); }
  .bg-infobox-purple { background: linear-gradient(135deg, #605ca8, #4b4792); }

  .chart-container { position: relative; height: 320px; margin-bottom: 20px; padding: 15px; background: #fff; border-radius: 6px; border: 1px solid #e0e0e0; }
  .chart-toggle-btn { margin-bottom: 10px; }

  .table-modern thead th { background: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; font-size: 13px; color: #555; white-space: nowrap; }
  .table-modern tbody td { vertical-align: middle; font-size: 13px; }
  .table-modern tbody tr:hover { background: #f5f8fc; }
  .nominal-cell { font-weight: 600; color: #2c6e9e; white-space: nowrap; }
  .badge-bank { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .badge-bni { background: #e8f4fd; color: #1a6fb5; }
  .badge-bri { background: #fff3e0; color: #e65100; }
  .badge-default { background: #eee; color: #666; }
  .action-btn-group a { margin-right: 3px; }
  .filter-section { background: #fff; padding: 12px 15px; border-radius: 6px; border: 1px solid #e0e0e0; margin-bottom: 15px; }
  .item-count-badge { background: #f39c12; color: #fff; padding: 3px 10px; border-radius: 12px; font-size: 12px; margin-left: 8px; }
  .filter-badge-bar { background: #e8f4fd; border: 1px solid #b8daef; border-radius: 6px; padding: 8px 14px; margin-bottom: 12px; font-size: 13px; color: #2c6e9e; }
  .filter-badge-bar .fa-filter { margin-right: 4px; }
  .filter-badge-item { display: inline-block; background: #fff; border: 1px solid #b8daef; border-radius: 12px; padding: 2px 10px; margin: 0 4px; font-size: 12px; }
  .filter-badge-clear { color: #c23321; margin-left: 8px; text-decoration: none; font-weight: 600; }
  .filter-badge-clear:hover { color: #a02010; text-decoration: underline; }
</style>

<!-- Content Header -->
<section class="content-header">
  <h1>
    Manajemen Tagihan FT <small>Daftar Semua Tagihan</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Manajemen Tagihan FT</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">

    <!-- Infobox Stats -->
    <div class="col-md-12">
      <div class="infobox-row">
        <div class="infobox-card bg-infobox-blue">
          <div class="infobox-label">Total Tagihan</div>
          <div class="infobox-value"><?= number_format($stats->total_tagihan ?: 0, 0, ',', '.'); ?></div>
          <i class="fa fa-file-text-o infobox-icon"></i>
        </div>
        <div class="infobox-card bg-infobox-green">
          <div class="infobox-label">Total Nominal</div>
          <div class="infobox-value">Rp <?= number_format($stats->total_nominal ?: 0, 0, ',', '.'); ?></div>
          <i class="fa fa-money infobox-icon"></i>
        </div>
        <div class="infobox-card bg-infobox-orange">
          <div class="infobox-label">Jumlah Kelas Terdampak</div>
          <div class="infobox-value"><?= number_format($stats->total_kelas ?: 0, 0, ',', '.'); ?></div>
          <i class="fa fa-users infobox-icon"></i>
        </div>
        <div class="infobox-card bg-infobox-purple">
          <div class="infobox-label">Data Ditampilkan</div>
          <div class="infobox-value"><?= number_format($transaksi_lain_ft_manajemen_counts, 0, ',', '.'); ?></div>
          <i class="fa fa-list infobox-icon"></i>
        </div>
      </div>
    </div>

    <!-- Chart Toggle -->
    <div class="col-md-12">
      <button type="button" class="btn btn-flat btn-default btn-sm chart-toggle-btn" id="chartToggle">
        <i class="fa fa-bar-chart"></i> Tampilkan Chart
      </button>
      <div class="chart-container" id="chartBox" style="display:none;">
        <canvas id="tagihanChart"></canvas>
      </div>
    </div>

    <!-- Main Table -->
    <div class="col-md-12">
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-list-alt"></i> Daftar Tagihan Lain SD</h3>
          <div class="box-tools pull-right">
            <?php is_allowed('transaksi_lain_ft_manajemen_add', function(){ ?>
            <a class="btn btn-flat btn-success btn-sm" id="btn_add_new" title="Tambah Tagihan Baru (Ctrl+a)" href="<?= site_url('administrator/transaksi_lain_ft_manajemen/add'); ?>">
              <i class="fa fa-plus"></i> Tambah Baru
            </a>
            <?php }) ?>
          </div>
        </div>

        <div class="box-body">

          <!-- Filter Section -->
          <form name="form_transaksi_lain_ft_manajemen" id="form_transaksi_lain_ft_manajemen" action="<?= base_url('administrator/transaksi_lain_ft_manajemen/index'); ?>">
          <div class="filter-section">
            <div class="row">
              <div class="col-md-5 col-sm-12">
                <div class="input-group">
                  <input type="text" class="form-control" name="q" id="filter" placeholder="Ketik kata kunci pencarian..." value="<?= htmlspecialchars($this->input->get('q')); ?>">
                  <span class="input-group-btn">
                    <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Cari</button>
                    <a class="btn btn-flat btn-default" href="<?= base_url('administrator/transaksi_lain_ft_manajemen'); ?>" title="Reset Filter"><i class="fa fa-undo"></i></a>
                  </span>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <select class="form-control chosen chosen-select" name="f" id="field">
                  <option value="">-- Semua Kolom --</option>
                  <option <?= $this->input->get('f') == 'nama_transaksi' ? 'selected' : ''; ?> value="nama_transaksi">Nama Tagihan</option>
                  <option <?= $this->input->get('f') == 'keterangan' ? 'selected' : ''; ?> value="keterangan">Keterangan</option>
                  <option <?= $this->input->get('f') == 'nominal' ? 'selected' : ''; ?> value="nominal">Nominal</option>
                  <option <?= $this->input->get('f') == 'tahun_ajaran' ? 'selected' : ''; ?> value="tahun_ajaran">Tahun Ajaran</option>
                  <option <?= $this->input->get('f') == 'nama_kelas' ? 'selected' : ''; ?> value="nama_kelas">Nama Kelas</option>
                  <option <?= $this->input->get('f') == 'nama_siswa' ? 'selected' : ''; ?> value="nama_siswa">Nama Siswa</option>
                  <option <?= $this->input->get('f') == 'tipe_bank' ? 'selected' : ''; ?> value="tipe_bank">Bank</option>
                </select>
              </div>
              <div class="col-md-4 col-sm-6 text-right">
                <div class="btn-group">
                  <?php is_allowed('transaksi_lain_ft_manajemen_export', function(){ ?>
                  <a class="btn btn-flat btn-success btn-sm" title="Export Excel" id="btnExportXls" href="<?= site_url('administrator/transaksi_lain_ft_manajemen/export'); ?>">
                    <i class="fa fa-file-excel-o"></i> Export XLS
                  </a>
                  <a class="btn btn-flat btn-danger btn-sm" title="Export PDF" href="<?= site_url('administrator/transaksi_lain_ft_manajemen/export_pdf'); ?>">
                    <i class="fa fa-file-pdf-o"></i> Export PDF
                  </a>
                  <?php }) ?>
                </div>
              </div>
            </div>
          </div>
          </form>

          <?php
            $filter_q = $this->input->get('q');
            $filter_f = $this->input->get('f');
            $field_labels = array(
              'nama_siswa' => 'Nama Siswa',
              'nama_kelas' => 'Nama Kelas',
              'nama_transaksi' => 'Nama Tagihan',
              'keterangan' => 'Keterangan',
              'nominal' => 'Nominal',
              'tahun_ajaran' => 'Tahun Ajaran',
              'tipe_bank' => 'Bank'
            );
          ?>
          <?php if (!empty($filter_q)): ?>
          <div class="filter-badge-bar">
            <i class="fa fa-filter"></i>
            Filter aktif:
            <?php if (!empty($filter_f) && isset($field_labels[$filter_f])): ?>
              <span class="filter-badge-item">Kolom: <strong><?= $field_labels[$filter_f]; ?></strong></span>
            <?php else: ?>
              <span class="filter-badge-item">Kolom: <strong>Semua</strong></span>
            <?php endif; ?>
            <span class="filter-badge-item">Kata kunci: <strong>"<?= htmlspecialchars($filter_q); ?>"</strong></span>
            <a href="<?= base_url('administrator/transaksi_lain_ft_manajemen'); ?>" class="filter-badge-clear" title="Hapus filter"><i class="fa fa-times"></i> Hapus</a>
          </div>
          <?php endif; ?>

          <!-- Bulk Actions -->
          <div class="row" style="margin-bottom:10px;">
            <div class="col-md-8">
              <div class="input-group input-group-sm" style="max-width:350px;">
                <select class="form-control" name="bulk" id="bulk">
                  <option value="">-- Bulk Action --</option>
                  <option value="delete">Delete</option>
                </select>
                <span class="input-group-btn">
                  <button type="button" class="btn btn-flat btn-default" name="apply" id="apply">Apply</button>
                </span>
              </div>
            </div>
            <div class="col-md-4 text-right">
              <span class="item-count-badge"><?= $transaksi_lain_ft_manajemen_counts; ?> item</span>
            </div>
          </div>

          <!-- Table -->
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-modern dataTable">
              <thead>
                <tr>
                  <th width="30"><input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all"></th>
                  <th>Kategori</th>
                  <th>Nama Tagihan</th>
                  <th>Keterangan</th>
                  <th>Nominal</th>
                  <th>Tahun Ajaran</th>
                  <th>Tingkatan</th>
                  <th>Kelas</th>
                  <th>Siswa</th>
                  <th>Tagihan Mulai</th>
                  <th>Tagihan Selesai</th>
                  <th>Bank</th>
                  <th width="160">Aksi</th>
                </tr>
              </thead>
              <tbody id="tbody_transaksi_lain_ft_manajemen">
              <?php foreach($transaksi_lain_ft_manajemens as $row): ?>
                <tr>
                  <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $row->id; ?>"></td>
                  <td>
                    <?php if ($row->id_kategori): ?>
                      <span class="label label-info"><?= _ent($row->transaksi_lain_kategori_nama_kategori); ?></span>
                    <?php endif; ?>
                  </td>
                  <td><strong><?= _ent($row->nama_transaksi); ?></strong></td>
                  <td><?= _ent($row->keterangan); ?></td>
                  <td class="nominal-cell">Rp <?= number_format($row->nominal, 0, ',', '.'); ?></td>
                  <td><?= _ent($row->tahun_ajaran_label); ?></td>
                  <td><?= _ent($row->tingkatan_ft_label); ?></td>
                  <td><?= join_multi_select($row->id_kelas, 'kelas_ft', 'id_kelas_ft', 'nama_kelas'); ?></td>
                  <td><?= join_multi_select($row->id_siswa, 'siswa_ft_aktif', 'id_siswa_ft_aktif', 'nama_lengkap'); ?></td>
                  <td><i class="fa fa-calendar"></i> <?= _ent($row->tanggal_tagihan_mulai); ?></td>
                  <td><i class="fa fa-calendar"></i> <?= _ent($row->tanggal_tagihan_selesai); ?></td>
                  <td>
                    <?php
                      $bank_class = 'badge-default';
                      if (strtoupper($row->tipe_bank) == 'BNI') $bank_class = 'badge-bni';
                      elseif (strtoupper($row->tipe_bank) == 'BRI') $bank_class = 'badge-bri';
                    ?>
                    <span class="badge-bank <?= $bank_class; ?>"><?= _ent($row->tipe_bank); ?></span>
                  </td>
                  <td>
                    <div class="action-btn-group">
                      <?php is_allowed('transaksi_lain_ft_manajemen_view', function() use ($row){ ?>
                        <a href="<?= site_url('administrator/transaksi_lain_ft_manajemen/single_pdf/' . $row->id); ?>" class="btn btn-xs btn-default" title="PDF"><i class="fa fa-file-pdf-o"></i></a>
                        <a href="<?= site_url('administrator/transaksi_lain_ft_manajemen/view/' . $row->id); ?>" class="btn btn-xs btn-info" title="Detail"><i class="fa fa-eye"></i></a>
                      <?php }) ?>
                      <?php is_allowed('transaksi_lain_ft_manajemen_update', function() use ($row){ ?>
                        <a href="<?= site_url('administrator/transaksi_lain_ft_manajemen/edit/' . $row->id); ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-edit"></i></a>
                      <?php }) ?>
                      <?php is_allowed('transaksi_lain_ft_manajemen_delete', function() use ($row){ ?>
                        <a href="javascript:void(0);" data-href="<?= site_url('administrator/transaksi_lain_ft_manajemen/delete/' . $row->id); ?>" class="btn btn-xs btn-danger remove-data" title="Hapus"><i class="fa fa-trash"></i></a>
                      <?php }) ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if ($transaksi_lain_ft_manajemen_counts == 0): ?>
                <tr>
                  <td colspan="13" class="text-center" style="padding:30px;">
                    <i class="fa fa-inbox" style="font-size:40px;color:#ccc;"></i><br>
                    <strong>Tidak ada data tagihan ditemukan</strong><br>
                    <small class="text-muted">Coba ubah filter atau kata kunci pencarian</small>
                  </td>
                </tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="row" style="margin-top:15px;">
            <div class="col-md-8">
              <small class="text-muted">Menampilkan <?= $transaksi_lain_ft_manajemen_counts > 0 ? ($this->uri->segment(4) + 1) : 0; ?> - <?= min($this->uri->segment(4) + $this->limit_page, $transaksi_lain_ft_manajemen_counts); ?> dari <?= $transaksi_lain_ft_manajemen_counts; ?> data</small>
            </div>
            <div class="col-md-4">
              <div class="dataTables_paginate paging_simple_numbers pull-right">
                <?= $pagination; ?>
              </div>
            </div>
          </div>

        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
  </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<script>
$(document).ready(function(){

  // === Chart Toggle ===
  var chartInstance = null;
  $('#chartToggle').click(function(){
    var $box = $('#chartBox');
    if ($box.is(':visible')) {
      $box.slideUp(200);
      $(this).html('<i class="fa fa-bar-chart"></i> Tampilkan Chart');
    } else {
      $box.slideDown(200);
      $(this).html('<i class="fa fa-bar-chart"></i> Sembunyikan Chart');
      if (!chartInstance) initChart();
    }
  });

  function initChart() {
    var chartData = <?= json_encode($chart_data); ?>;
    if (!chartData || chartData.length === 0) return;

    var labels = chartData.map(function(d){ return d.nama_kategori || 'Tanpa Kategori'; });
    var nominals = chartData.map(function(d){ return parseInt(d.total_nominal) || 0; });
    var counts = chartData.map(function(d){ return parseInt(d.jumlah_tagihan) || 0; });

    var colors = [
      '#3c8dbc','#00a65a','#f39c12','#dd4b39','#605ca8',
      '#00c0ef','#d2d6de','#39cccc','#ff851b','#0073b7',
      '#01ff70','#f012be','#3d9970','#bd0022','#6610f2'
    ];

    var ctx = document.getElementById('tagihanChart').getContext('2d');
    chartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Total Nominal (Rp)',
            data: nominals,
            backgroundColor: colors.slice(0, labels.length),
            borderWidth: 1
          },
          {
            label: 'Jumlah Tagihan',
            data: counts,
            backgroundColor: colors.slice(0, labels.length).map(function(c){ return c + '66'; }),
            borderWidth: 1,
            yAxisID: 'y1'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top' },
          title: { display: true, text: 'Distribusi Tagihan per Kategori' }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Nominal (Rp)' },
            ticks: {
              callback: function(v){ return 'Rp ' + v.toLocaleString('id-ID'); }
            }
          },
          y1: {
            beginAtZero: true,
            position: 'right',
            title: { display: true, text: 'Jumlah' },
            grid: { drawOnChartArea: false }
          }
        }
      }
    });
  }

  // === Delete ===
  $('.remove-data').click(function(){
    var url = $(this).attr('data-href');
    swal({
      title: "<?= cclang('are_you_sure'); ?>",
      text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
      cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
      closeOnConfirm: true,
      closeOnCancel: true
    }, function(isConfirm){
      if (isConfirm) { document.location.href = url; }
    });
    return false;
  });

  // === Bulk ===
  $('#apply').click(function(){
    var bulk = $('#bulk');
    var serialize_bulk = $('#form_transaksi_lain_ft_manajemen').serialize();
    if (bulk.val() == 'delete') {
      swal({
        title: "<?= cclang('are_you_sure'); ?>",
        text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
        cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
        closeOnConfirm: true,
        closeOnCancel: true
      }, function(isConfirm){
        if (isConfirm) {
          document.location.href = BASE_URL + '/administrator/transaksi_lain_ft_manajemen/delete?' + serialize_bulk;
        }
      });
      return false;
    } else if(bulk.val() == '') {
      swal({ title: "Upss", text: "<?= cclang('please_choose_bulk_action_first'); ?>", type: "warning", confirmButtonText: "Okay!" });
      return false;
    }
    return false;
  });

  // === Check All ===
  var checkAll = $('#check_all');
  var checkboxes = $('input.check');
  checkAll.on('ifChecked ifUnchecked', function(event) {
    if (event.type == 'ifChecked') { checkboxes.iCheck('check'); }
    else { checkboxes.iCheck('uncheck'); }
  });
  checkboxes.on('ifChanged', function(event){
    if(checkboxes.filter(':checked').length == checkboxes.length) {
      checkAll.prop('checked', 'checked');
    } else {
      checkAll.removeProp('checked');
    }
    checkAll.iCheck('update');
  });

  // === Export with filter ===
  $('#btnExportXls').on('click', function(e){
    e.preventDefault();
    var q = $('input[name="q"]').val() || '';
    var f = $('select[name="f"]').val() || '';
    var url = $(this).attr('href');
    var params = [];
    if (q) params.push('q=' + encodeURIComponent(q));
    if (f) params.push('f=' + encodeURIComponent(f));
    if (params.length > 0) url += '?' + params.join('&');
    window.location.href = url;
  });

});
</script>
