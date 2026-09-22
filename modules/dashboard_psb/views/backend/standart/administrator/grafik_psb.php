<style type="text/css">
   .widget-user-header { padding-left: 20px !important; }
   .info-box-psb { min-height: 78px; padding: 10px; }
   .info-box-psb .info-box-icon { width: 60px; height: 60px; line-height: 60px; font-size: 26px; }
   .info-box-psb .info-box-content { margin-left: 70px; padding: 4px 0 0 0; }
   .info-box-psb .info-box-text { font-size: 12px; }
   .info-box-psb .info-box-number { font-size: 22px; font-weight: 600; }
   .info-box-custom { min-height: 64px; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: center; padding: 8px 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.08); color: #fff; position: relative; overflow: hidden; }
   .info-box-custom .info-icon { font-size: 30px; opacity: 0.35; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); }
   .info-box-custom .info-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 2px; display: block; font-weight: 600; }
   .info-box-custom .info-value { font-size: 22px; font-weight: 700; line-height: 1.1; display: block; color: #fff; }
   .bg-grad-blue   { background: linear-gradient(135deg, #3c8dbc 0%, #367fa9 100%); }
   .bg-grad-green  { background: linear-gradient(135deg, #00a65a 0%, #008d4c 100%); }
   .bg-grad-yellow { background: linear-gradient(135deg, #f39c12 0%, #db8b0a 100%); }
   .bg-grad-red    { background: linear-gradient(135deg, #dd4b39 0%, #c0432f 100%); }
   .modal-detail-siswa .modal-header { background: linear-gradient(135deg, #3c8dbc 0%, #367fa9 100%); color: #fff; }
   .modal-detail-siswa .modal-header .close { color: #fff; opacity: 0.8; }
   .modal-detail-siswa .modal-title { font-weight: 600; }
   .modal-detail-siswa .table-detail th { background: #f4f4f4; width: 22%; font-weight: 600; }
   .modal-detail-siswa .table-detail td { vertical-align: middle; }
   .chart-psb-header { background: linear-gradient(135deg, #3c8dbc 0%, #367fa9 100%); color: #fff; }
   .chart-psb-header .box-title { color: #fff; font-weight: 600; }
   .chart-psb-header .btn-box-tool { color: #fff; }
   .box-grafik { border-top: 3px solid #3c8dbc; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 4px; }
   .box-grafik-gelombang { border-top: 3px solid #00a65a; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 4px; }
   .box-grafik-gelombang .chart-psb-header { background: linear-gradient(135deg, #00a65a 0%, #008d4c 100%); }
   .table-psb thead th { background: #f4f4f4; font-weight: 600; text-align: center; }
   .table-psb tbody td { vertical-align: middle; }
   .table-psb td.col-truncate { max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
   .chart-wrap { position: relative; }
   .chart-loading { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.75); z-index: 10; display: none; align-items: center; justify-content: center; }
   .chart-loading.active { display: flex; }
   .chart-empty { padding: 40px 10px; text-align: center; color: #999; font-style: italic; display: none; }
   .chart-empty.active { display: block; }
   .date-error { color: #a94442; font-size: 11px; margin-top: 4px; display: none; }
   .date-error.active { display: block; }
   .badge { display: inline-block; padding: 4px 8px; font-size: 12px; font-weight: 600; border-radius: 3px; color: #fff; }
   .badge.bg-green  { background: #00a65a; }
   .badge.bg-yellow { background: #f39c12; }
   .badge.bg-red    { background: #dd4b39; }
   .badge.bg-grey, .badge.bg-gray { background: #6c757d; }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.css">

<section class="content-header">
    <h1>
        <?= cclang('dashboard') ?>
        <small><?= cclang('chart') ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> <?= cclang('home') ?></a></li>
        <li><?= cclang('dashboard') ?></li>
        <li class="active"><?= 'Grafik PSB' ?></li>
    </ol>
</section>

<section class="content">

  <div class="row">
    <!-- CHART 1: Filter Tanggal -->
    <div class="col-md-6">
      <div class="box box-info box-grafik">
        <div class="box-header with-border chart-psb-header">
          <h3 class="box-title"><i class="fa fa-line-chart"></i> Grafik Pendaftar (berdasarkan tanggal daftar)</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-sm-6">
              <div class="info-box-custom bg-grad-blue">
                <i class="fa fa-users info-icon"></i>
                <div>
                  <span class="info-label">Total Pendaftar</span>
                  <span class="info-value" id="info-psb-pendaftar">0</span>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="info-box-custom bg-grad-green">
                <i class="fa fa-money info-icon"></i>
                <div>
                  <span class="info-label">Sudah Membayar</span>
                  <span class="info-value" id="info-psb-bayar">0</span>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="info-box-custom bg-grad-yellow">
                <i class="fa fa-graduation-cap info-icon"></i>
                <div>
                  <span class="info-label">Calon Siswa (Lolos)</span>
                  <span class="info-value" id="info-psb-lolos">0</span>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="info-box-custom bg-grad-red">
                <i class="fa fa-check-circle info-icon"></i>
                <div>
                  <span class="info-label">Sudah Daftar Ulang</span>
                  <span class="info-value" id="info-psb-daftar-ulang">0</span>
                </div>
              </div>
            </div>
          </div>

          <form id="form_filter" method="post" class="form-inline" style="margin: 10px 0 5px 0;">
            <input id="jenjang" type="hidden" name="jenjang" value="<?= $jenjang; ?>">
            <div class="form-group" style="margin-right: 8px;">
              <label style="display:block; font-size:11px; color:#666;">Start Date</label>
              <input type="date" name="start_date" id="start_date" class="form-control input-sm">
            </div>
            <div class="form-group" style="margin-right: 8px;">
              <label style="display:block; font-size:11px; color:#666;">End Date</label>
              <input type="date" name="end_date" id="end_date" class="form-control input-sm">
            </div>
            <div class="form-group" style="vertical-align: bottom;">
              <button id="btn_filter" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
              <button id="btn_reset" class="btn btn-default btn-sm"><i class="fa fa-undo"></i> Reset</button>
              <button id="btn_export" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Export XLS</button>
            </div>
          </form>
          <div class="date-error" id="date-error">Start date tidak boleh melebihi end date.</div>

          <div class="chart-wrap">
            <div class="chart-loading" id="chart-psb-loading"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
            <div class="chart-empty" id="chart-psb-empty">Tidak ada data untuk filter ini.</div>
            <div class="chart" id="chart-psb" style="height: 280px;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- CHART 2: Filter Gelombang -->
    <div class="col-md-6">
      <div class="box box-info box-grafik-gelombang">
        <div class="box-header with-border chart-psb-header">
          <h3 class="box-title"><i class="fa fa-bar-chart"></i> Grafik Pendaftar (berdasarkan tahun ajaran &amp; gelombang)</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-sm-6">
              <div class="info-box-custom bg-grad-blue">
                <i class="fa fa-users info-icon"></i>
                <div>
                  <span class="info-label">Total Pendaftar</span>
                  <span class="info-value" id="info-gel-pendaftar">0</span>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="info-box-custom bg-grad-green">
                <i class="fa fa-money info-icon"></i>
                <div>
                  <span class="info-label">Sudah Membayar</span>
                  <span class="info-value" id="info-gel-bayar">0</span>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="info-box-custom bg-grad-yellow">
                <i class="fa fa-graduation-cap info-icon"></i>
                <div>
                  <span class="info-label">Calon Siswa (Lolos)</span>
                  <span class="info-value" id="info-gel-lolos">0</span>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="info-box-custom bg-grad-red">
                <i class="fa fa-check-circle info-icon"></i>
                <div>
                  <span class="info-label">Sudah Daftar Ulang</span>
                  <span class="info-value" id="info-gel-daftar-ulang">0</span>
                </div>
              </div>
            </div>
          </div>

          <form id="form_filter_gelombang" method="post" class="form-inline" style="margin: 10px 0 15px 0;">
            <input id="jenjang_gelombang" type="hidden" name="jenjang" value="<?= $jenjang; ?>">
            <div class="form-group" style="margin-right: 8px;">
              <label style="display:block; font-size:11px; color:#666;">Tahun Ajaran</label>
              <?php
                $get_tahun_ajaran = $this->db->order_by('tanggal_mulai', 'desc')->get('tahun_ajaran')->result();
                // ponytail: batas filter TA = TA saat ini + 1 (tahun depan); ganti logika bulan jika cutoff berbeda
                $ta_awal = ((int) date("n") >= 7) ? (int) date("Y") : (int) date("Y") - 1;
                $ta_max = ($ta_awal + 1)."/".($ta_awal + 2);
                echo "<select class='form-control input-sm' id='tahun_ajaran' name='tahun_ajaran'>";
                echo "<option value=''>Pilih Tahun Ajaran</option>";
                if (!empty($get_tahun_ajaran)) {
                  $ada_max = false;
                  foreach ($get_tahun_ajaran as $key => $value) {
                    if (strcmp($value->label, $ta_max) > 0) { continue; }
                    if ($value->label == $ta_max) { $ada_max = true; }
                    echo "<option value='".$value->label."'".($value->label == $ta_max ? " selected" : "").">".$value->label."</option>";
                  }
                  if (!$ada_max) {
                    echo "<option value='".$ta_max."' selected>".$ta_max."</option>";
                  }
                }
                echo "</select>";
              ?>
            </div>
            <div class="form-group" style="margin-right: 8px;">
              <label style="display:block; font-size:11px; color:#666;">Gelombang</label>
              <select name="gelombang" id="gelombang" class="form-control input-sm">
                <option value="">Pilih Gelombang</option>
                <option value="1">Gelombang 1</option>
                <option value="2">Gelombang 2</option>
                <option value="3">Gelombang 3</option>
              </select>
            </div>
            <div class="form-group" style="vertical-align: bottom;">
              <button id="btn_filter_gelombang" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
              <button id="btn_reset_gelombang" class="btn btn-default btn-sm"><i class="fa fa-undo"></i> Reset</button>
              <button id="btn_export_gelombang" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Export XLS</button>
            </div>
          </form>

          <div class="chart-wrap">
            <div class="chart-loading" id="chart-gel-loading"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
            <div class="chart-empty" id="chart-gel-empty">Tidak ada data untuk filter ini.</div>
            <div class="chart" id="chart-psb-gelombang" style="height: 280px;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12" style="margin-top: 20px;">
      <div class="box box-info" style="border-top: 3px solid #f39c12; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 4px;">
        <div class="box-header with-border" style="background: #f39c12; color: #fff;">
          <h3 class="box-title" style="color: #fff; font-weight: 600;"><i class="fa fa-table"></i> Detail Pendaftar</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" style="color:#fff;"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove" style="color:#fff;"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <table id="detail_table" class="table table-striped table-bordered table-hover table-psb" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Nomor Peserta</th>
                <th>Jenis Kelamin</th>
                <th>No. Telepon Ibu</th>
                <th>No. Telepon Ayah</th>
                <th>Email</th>
                <th>Asal Sekolah</th>
                <th>Status Kelulusan</th>
                <th>Pembayaran Pendaftaran</th>
                <th>Pembayaran Daftar Ulang</th>
                <th>TA</th>
                <th>Gel</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</section>

<!-- Modal Detail Siswa -->
<div class="modal fade modal-detail-siswa" id="modal-detail-siswa" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><i class="fa fa-user"></i> Detail Siswa</h4>
      </div>
      <div class="modal-body" id="modal-detail-siswa-body">
        <div class="text-center text-muted" style="padding: 30px 0;"><i class="fa fa-spinner fa-spin"></i> Memuat data...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.min.js"></script>
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.bootstrap.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.bootstrap.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.html5.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.print.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.colVis.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script>
$(function () {
  "use strict";

  // --- Helper: render badge untuk status ---
  function badgeLulus(val) {
    if (val === 'Lulus')        return '<span class="badge bg-green"><i class="fa fa-check"></i> Lulus</span>';
    if (val === 'Cadangan')     return '<span class="badge bg-yellow"><i class="fa fa-clock-o"></i> Cadangan</span>';
    if (val === 'Tidak Lulus')  return '<span class="badge bg-red"><i class="fa fa-times"></i> Tidak Lulus</span>';
    if (val === 'Mutasi')       return '<span class="badge bg-grey"><i class="fa fa-exchange"></i> Mutasi</span>';
    return '<span class="badge bg-gray">Menunggu</span>';
  }
  function badgeBayar(val) {
    if (val === 'Lunas')  return '<span class="badge bg-green"><i class="fa fa-check"></i> Lunas</span>';
    if (val === 'Mutasi') return '<span class="badge bg-grey"><i class="fa fa-exchange"></i> Mutasi</span>';
    return '<span class="badge bg-yellow"><i class="fa fa-clock-o"></i> Belum</span>';
  }

  // --- Helper: update 4 info-boxes dari data chart ---
  function updateInfoBoxes(prefix, json) {
    var data = (typeof json === 'string') ? JSON.parse(json) : json;
    var row = (data && data.length) ? data[0] : {};
    $('#info-' + prefix + '-pendaftar').text(row.total_siswa || 0);
    $('#info-' + prefix + '-bayar').text(row.total_siswa_daftar || 0);
    $('#info-' + prefix + '-lolos').text(row.total_siswa_daftar_ulang || 0);
    $('#info-' + prefix + '-daftar-ulang').text(row.total_sudah_daftar_ulang || 0);
  }

  // --- Chart 1: tanggal ---
  function loadChartTanggal(startDate, endDate) {
    var params = { jenjang: $('#jenjang').val() };
    if (startDate) params.start_date = startDate;
    if (endDate)   params.end_date   = endDate;
    $('#chart-psb-loading').addClass('active');
    $('#chart-psb-empty').removeClass('active');
    $.ajax({
      type: "POST",
      url: "<?= site_url('administrator/dashboard_psb/chart_psb'); ?>",
      data: params,
      success: function(data) {
        var parsed = JSON.parse(data);
        updateInfoBoxes('psb', parsed);
        $("#chart-psb").empty();
        if (!parsed || !parsed.length || !parsed[0].total_siswa) {
          $('#chart-psb-empty').addClass('active');
        } else {
          new Morris.Bar({
            element: 'chart-psb',
            resize: true,
            data: parsed,
            barColors: ['#00A65A', '#4285F4', '#EA4335', '#FF8000'],
            xkey: 'keterangan',
            ykeys: ['total_siswa', 'total_siswa_daftar', 'total_siswa_daftar_ulang', 'total_sudah_daftar_ulang'],
            labels: ['Total Pendaftar', 'Sudah Membayar', 'Calon Siswa (Lolos)', 'Sudah Daftar Ulang'],
            xLabelsAngle: 30,
            hideHover: 'false'
          });
        }
      },
      error: function() { alert('Gagal memuat grafik'); },
      complete: function() { $('#chart-psb-loading').removeClass('active'); }
    });
  }

  // --- Chart 2: gelombang ---
  function loadChartGelombang(gelombang, tahunAjaran) {
    var params = { jenjang: $('#jenjang_gelombang').val() };
    if (gelombang)    params.gelombang    = gelombang;
    if (tahunAjaran)  params.tahun_ajaran = tahunAjaran;
    $('#chart-gel-loading').addClass('active');
    $('#chart-gel-empty').removeClass('active');
    $.ajax({
      type: "POST",
      url: "<?= site_url('administrator/dashboard_psb/chart_psb_gelombang'); ?>",
      data: params,
      success: function(data) {
        var parsed = JSON.parse(data);
        updateInfoBoxes('gel', parsed);
        $("#chart-psb-gelombang").empty();
        if (!parsed || !parsed.length || !parsed[0].total_siswa) {
          $('#chart-gel-empty').addClass('active');
        } else {
          new Morris.Bar({
            element: 'chart-psb-gelombang',
            resize: true,
            data: parsed,
            barColors: ['#00A65A', '#4285F4', '#EA4335', '#FF8000'],
            xkey: 'keterangan',
            ykeys: ['total_siswa', 'total_siswa_daftar', 'total_siswa_daftar_ulang', 'total_sudah_daftar_ulang'],
            labels: ['Total Pendaftar', 'Sudah Membayar', 'Calon Siswa (Lolos)', 'Sudah Daftar Ulang'],
            xLabelsAngle: 30,
            hideHover: 'false'
          });
        }
      },
      error: function() { alert('Gagal memuat grafik'); },
      complete: function() { $('#chart-gel-loading').removeClass('active'); }
    });
  }

  // --- Initial load ---
  loadChartTanggal();
  loadChartGelombang();

  // --- DataTable init ---
  var currentFilter = { tipe: 'tanggal' };
  function buildTableParams() {
    if (currentFilter.tipe === 'gelombang') {
      return {
        jenjang: $('#jenjang_gelombang').val(),
        gelombang: $('#gelombang').val(),
        tahun_ajaran: $('#tahun_ajaran').val(),
        tipe: "gelombang",
      };
    }
    return {
      jenjang: $('#jenjang').val(),
      start_date: $('#start_date').val(),
      end_date: $('#end_date').val(),
      tipe: "tanggal",
    };
  }

  var detailTable = $('#detail_table').DataTable({
    ajax: {
      url: "<?= site_url('administrator/dashboard_psb/detail_table'); ?>",
      type: 'POST',
      data: function(d) { return buildTableParams(); }
    },
    columns: [
      { data: 'no', name: 'no' },
      { data: 'nama_lengkap', name: 'nama_lengkap' },
      { data: 'no_peserta', name: 'no_peserta' },
      { data: 'jenis_kelamin', name: 'jenis_kelamin' },
      { data: 'notelp_ibu', name: 'notelp_ibu' },
      { data: 'notelp_ayah', name: 'notelp_ayah' },
      { data: 'email', name: 'email' },
      { data: 'sekolah_asal', name: 'sekolah_asal' },
      { data: 'status_lulus', name: 'status_lulus' },
      { data: 'status_pendaftaran', name: 'status_pendaftaran' },
      { data: 'status_daftar_ulang', name: 'status_daftar_ulang' },
      { data: 'tahun_ajaran', name: 'tahun_ajaran' },
      { data: 'gelombang', name: 'gelombang' },
      { data: 'id_siswa', name: 'id_siswa', visible: false, searchable: false },
      { data: null, name: 'aksi', orderable: false, searchable: false, defaultContent: '' },
    ],
    columnDefs: [
      { targets: 0, orderable: false, className: 'text-center' },
      { targets: [2,3,4,5,8,9,10,11,12,14], className: 'text-center' },
      { targets: [4,5,6,7], className: 'text-center col-truncate' },
      { targets: '_all', className: 'dt-head-center' },
      { targets: 8,  render: function(d) { return badgeLulus(d); } },
      { targets: 9,  render: function(d) { return badgeBayar(d); } },
      { targets: 10, render: function(d) { return badgeBayar(d); } },
      { targets: 14, render: function(d, t, row) {
        var id = row.id_siswa || '';
        if (!id) return '';
        return '<button class="btn btn-xs btn-info btn-detail-siswa" data-id="' + id + '" title="Lihat Detail"><i class="fa fa-eye"></i></button>';
      } },
    ],
    order: [[0, 'asc']],
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    scrollX: true,
    responsive: true,
    processing: true,
    language: { url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" }
  });

  // --- Filter tanggal ---
  function isValidDateRange(s, e) {
    if (!s || !e) return true;
    return new Date(s) <= new Date(e);
  }

  $("#btn_filter").click(function(e) {
    e.preventDefault();
    if (!isValidDateRange($('#start_date').val(), $('#end_date').val())) {
      $('#date-error').addClass('active');
      return;
    }
    $('#date-error').removeClass('active');
    currentFilter.tipe = 'tanggal';
    loadChartTanggal($('#start_date').val(), $('#end_date').val());
    detailTable.ajax.reload();
  });

  $("#btn_reset").click(function(e) {
    e.preventDefault();
    $('#start_date').val('');
    $('#end_date').val('');
    $('#date-error').removeClass('active');
    currentFilter.tipe = 'tanggal';
    loadChartTanggal();
    detailTable.ajax.reload();
  });

  $("#btn_export").click(function(e) {
    e.preventDefault();
    if (!isValidDateRange($('#start_date').val(), $('#end_date').val())) {
      $('#date-error').addClass('active');
      return;
    }
    window.location.href = '<?= base_url('administrator/dashboard_psb/export_chart_psb'); ?>?jenjang='+$('#jenjang').val()+'&start_date='+$('#start_date').val()+'&end_date='+$('#end_date').val();
  });

  // --- Filter gelombang ---
  $("#btn_filter_gelombang").click(function(e) {
    e.preventDefault();
    currentFilter.tipe = 'gelombang';
    loadChartGelombang($('#gelombang').val(), $('#tahun_ajaran').val());
    detailTable.ajax.reload();
  });

  $("#btn_reset_gelombang").click(function(e) {
    e.preventDefault();
    $('#tahun_ajaran').val('');
    $('#gelombang').val('');
    currentFilter.tipe = 'gelombang';
    loadChartGelombang();
    detailTable.ajax.reload();
  });

  $("#btn_export_gelombang").click(function(e) {
    e.preventDefault();
    var tahun_ajaran_url = $('#tahun_ajaran').val().replace(/\//g, '_');
    window.location.href = '<?= base_url('administrator/dashboard_psb/export_chart_psb_gelombang'); ?>?jenjang='+$('#jenjang_gelombang').val()+'&gelombang='+$('#gelombang').val()+'&tahun_ajaran='+tahun_ajaran_url;
  });

  // --- Modal Detail Siswa ---
  $(document).on('click', '.btn-detail-siswa', function() {
    var id = $(this).data('id');
    if (!id) return;
    $('#modal-detail-siswa-body').html('<div class="text-center text-muted" style="padding: 30px 0;"><i class="fa fa-spinner fa-spin"></i> Memuat data...</div>');
    $('#modal-detail-siswa').modal('show');
    $.ajax({
      url: '<?= site_url('administrator/dashboard_psb/detail_siswa'); ?>',
      type: 'POST',
      dataType: 'json',
      data: { id_siswa: id, jenjang: '<?= $jenjang; ?>' },
      success: function(resp) {
        if (!resp || !resp.status) {
          $('#modal-detail-siswa-body').html('<div class="alert alert-warning">' + (resp && resp.message ? resp.message : 'Data tidak ditemukan') + '</div>');
          return;
        }
        var s = resp.data;
        var rows = [
          ['Nama Lengkap',    s.nama_lengkap],
          ['No Peserta',      s.no_peserta],
          ['NISN',            s.nisn],
          ['Jenis Kelamin',   s.jenis_kelamin],
          ['Tempat Lahir',    s.tempat_lahir],
          ['Tanggal Lahir',   s.tgl_lahir],
          ['Agama',           s.agama],
          ['Email',           s.email],
          ['No HP Ayah',      s.notelp_ayah],
          ['No HP Ibu',       s.notelp_ibu],
          ['Nama Ayah',       s.nama_ayah],
          ['Nama Ibu',        s.nama_ibu],
          ['Alamat',          s.alamat],
          ['Asal Sekolah',    resp.sekolah_nama],
          ['Tahun Ajaran',    s.tahun_ajaran],
          ['Gelombang',       s.gelombang],
          ['Sumber Informasi',s.sumber_informasi],
          ['Alasan Tertarik', s.alasan_tertarik],
        ];
        var html = '<table class="table table-bordered table-striped table-detail"><tbody>';
        for (var i = 0; i < rows.length; i += 2) {
          html += '<tr>';
          html += '<th>' + rows[i][0] + '</th><td>' + (rows[i][1] || '-') + '</td>';
          if (rows[i+1]) {
            html += '<th>' + rows[i+1][0] + '</th><td>' + (rows[i+1][1] || '-') + '</td>';
          } else {
            html += '<th></th><td></td>';
          }
          html += '</tr>';
        }
        html += '</tbody></table>';
        $('#modal-detail-siswa-body').html(html);
        $('.modal-detail-siswa .modal-title').html('<i class="fa fa-user"></i> ' + (s.nama_lengkap || 'Detail Siswa'));
      },
      error: function() {
        $('#modal-detail-siswa-body').html('<div class="alert alert-danger">Gagal memuat data siswa.</div>');
      }
    });
  });
});
</script>
