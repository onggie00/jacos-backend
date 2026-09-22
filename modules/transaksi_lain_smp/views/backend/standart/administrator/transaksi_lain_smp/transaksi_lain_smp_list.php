
<script type="text/javascript">
</script>

<style>
  .infobox-row { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
  .infobox-card {
    flex: 1; min-width: 160px; padding: 16px 18px; border-radius: 6px;
    color: #fff; position: relative; overflow: hidden;
  }
  .infobox-card .infobox-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 36px; opacity: 0.3; }
  .infobox-card .infobox-label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
  .infobox-card .infobox-value { font-size: 22px; font-weight: 700; }
  .infobox-card .infobox-sub { font-size: 11px; opacity: 0.85; margin-top: 2px; }
  .bg-infobox-blue { background: linear-gradient(135deg, #3c8dbc, #2c6e9e); }
  .bg-infobox-green { background: linear-gradient(135deg, #00a65a, #00874a); }
  .bg-infobox-orange { background: linear-gradient(135deg, #f39c12, #d4890e); }
  .bg-infobox-red { background: linear-gradient(135deg, #dd4b39, #c23321); }
  .bg-infobox-purple { background: linear-gradient(135deg, #605ca8, #4b4792); }
  .bg-infobox-teal { background: linear-gradient(135deg, #00c0ef, #00a5c8); }

  .chart-container { position: relative; height: 320px; margin-bottom: 20px; padding: 15px; background: #fff; border-radius: 6px; border: 1px solid #e0e0e0; }
  .chart-toggle-btn { margin-bottom: 10px; }

  .table-modern thead th { background: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; font-size: 13px; color: #555; white-space: nowrap; }
  .table-modern tbody td { vertical-align: middle; font-size: 13px; }
  .table-modern tbody tr:hover { background: #f5f8fc; }
  .nominal-cell { font-weight: 600; color: #2c6e9e; white-space: nowrap; }
  .badge-status { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .status-belum { background: #fce4e4; color: #c23321; }
  .status-menunggu { background: #fff3e0; color: #e65100; }
  .status-lunas { background: #e8f5e9; color: #2e7d32; }
  .status-kadaluarsa { background: #eee; color: #777; }
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
    List Tagihan SMP <small>Daftar Transaksi Tagihan</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">List Tagihan SMP</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">

    <!-- Infobox Stats -->
    <div class="col-md-12">
      <div class="infobox-row">
        <div class="infobox-card bg-infobox-blue">
          <div class="infobox-label">Total Transaksi</div>
          <div class="infobox-value"><?= number_format($stats->total_transaksi ?: 0, 0, ',', '.'); ?></div>
          <i class="fa fa-exchange infobox-icon"></i>
        </div>
        <div class="infobox-card bg-infobox-green">
          <div class="infobox-label">Lunas</div>
          <div class="infobox-value"><?= number_format($stats->lunas ?: 0, 0, ',', '.'); ?></div>
          <div class="infobox-sub">Rp <?= number_format($stats->nominal_lunas ?: 0, 0, ',', '.'); ?></div>
          <i class="fa fa-check-circle infobox-icon"></i>
        </div>
        <div class="infobox-card bg-infobox-orange">
          <div class="infobox-label">Menunggu Bayar</div>
          <div class="infobox-value"><?= number_format($stats->menunggu ?: 0, 0, ',', '.'); ?></div>
          <i class="fa fa-clock-o infobox-icon"></i>
        </div>
        <div class="infobox-card bg-infobox-red">
          <div class="infobox-label">Belum Dibayar</div>
          <div class="infobox-value"><?= number_format($stats->belum_bayar ?: 0, 0, ',', '.'); ?></div>
          <i class="fa fa-times-circle infobox-icon"></i>
        </div>
        <div class="infobox-card bg-infobox-purple">
          <div class="infobox-label">Kadaluarsa</div>
          <div class="infobox-value"><?= number_format($stats->kadaluarsa ?: 0, 0, ',', '.'); ?></div>
          <i class="fa fa-ban infobox-icon"></i>
        </div>
        <div class="infobox-card bg-infobox-teal">
          <div class="infobox-label">Total Nominal</div>
          <div class="infobox-value">Rp <?= number_format($stats->total_nominal ?: 0, 0, ',', '.'); ?></div>
          <i class="fa fa-money infobox-icon"></i>
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
          <h3 class="box-title"><i class="fa fa-list-alt"></i> Daftar Transaksi Tagihan SD</h3>
        </div>

        <div class="box-body">

          <!-- Filter Section -->
          <form name="form_transaksi_lain_smp" id="form_transaksi_lain_smp" action="<?= base_url('administrator/transaksi_lain_smp/index'); ?>">
          <div class="filter-section">
            <div class="row">
              <div class="col-md-5 col-sm-12">
                <div class="input-group">
                  <input type="text" class="form-control" name="q" id="filter" placeholder="Ketik kata kunci pencarian..." value="<?= htmlspecialchars($this->input->get('q')); ?>">
                  <span class="input-group-btn">
                    <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Cari</button>
                    <a class="btn btn-flat btn-default" href="<?= base_url('administrator/transaksi_lain_smp'); ?>" title="Reset Filter"><i class="fa fa-undo"></i></a>
                  </span>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <select class="form-control chosen chosen-select" name="f" id="field">
                  <option value="">-- Semua Kolom --</option>
                  <option <?= $this->input->get('f') == 'nama_siswa' ? 'selected' : ''; ?> value="nama_siswa">Nama Siswa</option>
                  <option <?= $this->input->get('f') == 'nama_kelas' ? 'selected' : ''; ?> value="nama_kelas">Kelas</option>
                  <option <?= $this->input->get('f') == 'nama_transaksi' ? 'selected' : ''; ?> value="nama_transaksi">Nama Tagihan</option>
                  <option <?= $this->input->get('f') == 'va_number' ? 'selected' : ''; ?> value="va_number">VA Number</option>
                  <option <?= $this->input->get('f') == 'kode_tagihan' ? 'selected' : ''; ?> value="kode_tagihan">Kode Tagihan</option>
                  <option <?= $this->input->get('f') == 'status_transaksi' ? 'selected' : ''; ?> value="status_transaksi">Status Transaksi</option>
                </select>
              </div>
              <div class="col-md-4 col-sm-6 text-right">
                <?php is_allowed('transaksi_lain_smp_export', function(){ ?>
                <a class="btn btn-flat btn-success btn-sm" title="Export Excel" id="btnExportXls" href="<?= site_url('administrator/transaksi_lain_smp/export'); ?>">
                  <i class="fa fa-file-excel-o"></i> Export XLS
                </a>
                <a class="btn btn-flat btn-danger btn-sm" title="Export PDF" href="<?= site_url('administrator/transaksi_lain_smp/export_pdf'); ?>">
                  <i class="fa fa-file-pdf-o"></i> Export PDF
                </a>
                <?php }) ?>
              </div>
            </div>
          </div>
          </form>

          <?php
            $filter_q = $this->input->get('q');
            $filter_f = $this->input->get('f');
            $field_labels = array(
              'nama_siswa' => 'Nama Siswa',
              'nama_kelas' => 'Kelas',
              'nama_transaksi' => 'Nama Tagihan',
              'va_number' => 'VA Number',
              'kode_tagihan' => 'Kode Tagihan',
              'status_transaksi' => 'Status'
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
            <a href="<?= base_url('administrator/transaksi_lain_smp'); ?>" class="filter-badge-clear" title="Hapus filter"><i class="fa fa-times"></i> Hapus</a>
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
              <span class="item-count-badge"><?= $transaksi_lain_smp_counts; ?> item</span>
            </div>
          </div>

          <!-- Table -->
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-modern dataTable">
              <thead>
                <tr>
                  <th width="30"><input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all"></th>
                  <th>Siswa</th>
                  <th>Kelas</th>
                  <th>Nama Tagihan</th>
                  <th>Kode Tagihan</th>
                  <th>VA Number</th>
                  <th>Nominal</th>
                  <th>Status</th>
                  <th>Tanggal Bayar</th>
                  <th>Expired</th>
                  <th>Kwitansi</th>
                  <th width="130">Aksi</th>
                </tr>
              </thead>
              <tbody id="tbody_transaksi_lain_smp">
              <?php foreach($transaksi_lain_smps as $row): ?>
                <tr>
                  <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $row->id; ?>"></td>
                  <td>
                    <?php if ($row->id_siswa_aktif): ?>
                      <a href="<?= site_url('administrator/siswa_smp_aktif/view/'.$row->id_siswa_aktif.'?popup=show'); ?>" class="popup-view">
                        <i class="fa fa-user"></i> <?= _ent($row->siswa_smp_aktif_nama_lengkap); ?>
                      </a>
                    <?php endif; ?>
                  </td>
                  <td><span class="label label-default"><?= _ent($row->nama_kelas); ?></span></td>
                  <td>
                    <?php if ($row->id_transaksi_lain): ?>
                      <a href="<?= site_url('administrator/transaksi_lain_smp_manajemen/view/'.$row->id_transaksi_lain.'?popup=show'); ?>" class="popup-view">
                        <?= _ent($row->transaksi_lain_smp_manajemen_nama_transaksi); ?>
                      </a>
                    <?php endif; ?>
                  </td>
                  <td><code><?= _ent($row->kode_tagihan); ?></code></td>
                  <td><code><?= _ent($row->va_number); ?></code></td>
                  <td class="nominal-cell">Rp <?= number_format($row->nominal_bayar, 0, ',', '.'); ?></td>
                  <td>
                    <?php
                      $status_label = 'Unknown';
                      $status_class = 'status-belum';
                      if ($row->status_transaksi == 0) { $status_label = 'Belum Dibayar'; $status_class = 'status-belum'; }
                      elseif ($row->status_transaksi == 1) { $status_label = 'Menunggu'; $status_class = 'status-menunggu'; }
                      elseif ($row->status_transaksi == 2) { $status_label = 'Lunas'; $status_class = 'status-lunas'; }
                      elseif ($row->status_transaksi == 3) { $status_label = 'Kadaluarsa'; $status_class = 'status-kadaluarsa'; }
                    ?>
                    <span class="badge-status <?= $status_class; ?>"><?= $status_label; ?></span>
                  </td>
                  <td><?= $row->tanggal_bayar ? _ent($row->tanggal_bayar) : '<span class="text-muted">-</span>'; ?></td>
                  <td><?= $row->expired_at ? _ent($row->expired_at) : '<span class="text-muted">-</span>'; ?></td>
                  <td>
                    <?php if (!empty($row->file_kwitansi)): ?>
                      <a href="<?= BASE_URL . 'uploads/transaksi_lain_smp/' . $row->file_kwitansi; ?>" target="_blank" class="btn btn-xs btn-default" title="Lihat Kwitansi">
                        <i class="fa fa-file-pdf-o"></i>
                      </a>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="action-btn-group">
                      <?php is_allowed('transaksi_lain_smp_view', function() use ($row){ ?>
                        <a href="<?= site_url('administrator/transaksi_lain_smp/view/' . $row->id); ?>" class="btn btn-xs btn-info" title="Detail"><i class="fa fa-eye"></i></a>
                      <?php }) ?>
                      <?php is_allowed('transaksi_lain_smp_update', function() use ($row){ ?>
                        <a href="<?= site_url('administrator/transaksi_lain_smp/edit/' . $row->id); ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-edit"></i></a>
                      <?php }) ?>
                      <?php is_allowed('transaksi_lain_smp_delete', function() use ($row){ ?>
                        <a href="javascript:void(0);" data-href="<?= site_url('administrator/transaksi_lain_smp/delete/' . $row->id); ?>" class="btn btn-xs btn-danger remove-data" title="Hapus"><i class="fa fa-trash"></i></a>
                      <?php }) ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if ($transaksi_lain_smp_counts == 0): ?>
                <tr>
                  <td colspan="12" class="text-center" style="padding:30px;">
                    <i class="fa fa-inbox" style="font-size:40px;color:#ccc;"></i><br>
                    <strong>Tidak ada data transaksi ditemukan</strong><br>
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
              <small class="text-muted">Menampilkan <?= $transaksi_lain_smp_counts > 0 ? ($this->uri->segment(4) + 1) : 0; ?> - <?= min($this->uri->segment(4) + $this->limit_page, $transaksi_lain_smp_counts); ?> dari <?= $transaksi_lain_smp_counts; ?> data</small>
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

    var labels = chartData.map(function(d){ return d.nama_tagihan || 'Tanpa Nama'; });
    var belum = chartData.map(function(d){ return parseInt(d.belum_bayar) || 0; });
    var menunggu = chartData.map(function(d){ return parseInt(d.menunggu) || 0; });
    var lunas = chartData.map(function(d){ return parseInt(d.lunas) || 0; });
    var kadaluarsa = chartData.map(function(d){ return parseInt(d.kadaluarsa) || 0; });

    var ctx = document.getElementById('tagihanChart').getContext('2d');
    chartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          { label: 'Belum Dibayar', data: belum, backgroundColor: '#dd4b39', borderWidth: 1 },
          { label: 'Menunggu', data: menunggu, backgroundColor: '#f39c12', borderWidth: 1 },
          { label: 'Lunas', data: lunas, backgroundColor: '#00a65a', borderWidth: 1 },
          { label: 'Kadaluarsa', data: kadaluarsa, backgroundColor: '#999', borderWidth: 1 }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top' },
          title: { display: true, text: 'Status Pembayaran per Tagihan' }
        },
        scales: {
          x: { stacked: true },
          y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Jumlah Transaksi' } }
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
    var serialize_bulk = $('#form_transaksi_lain_smp').serialize();
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
          document.location.href = BASE_URL + '/administrator/transaksi_lain_smp/delete?' + serialize_bulk;
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
