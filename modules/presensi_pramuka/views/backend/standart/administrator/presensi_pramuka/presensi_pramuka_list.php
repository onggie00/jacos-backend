<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<style>
/* Status Labels */
.label-hadir { background: #27ae60; }
.label-sakit { background: #f39c12; }
.label-izin { background: #3498db; }
.label-alpa { background: #e74c3c; }
.label-terlambat { background: #e67e22; }

/* Predikat Badge */
.badge-predikat { padding: 3px 8px; border-radius: 3px; font-weight: bold; font-size: 11px; color: #fff; display: inline-block; }
.badge-a { background: #27ae60; }
.badge-b { background: #3498db; }
.badge-c { background: #f39c12; }
.badge-d { background: #e67e22; }
.badge-e { background: #e74c3c; }

/* Filter Section */
.filter-builder { margin-bottom: 15px; padding: 12px; background: #f8f9fa; border-radius: 5px; border: 1px solid #e0e0e0; }
.filter-inline-row { display: flex; align-items: flex-end; gap: 8px; flex-wrap: wrap; }
.filter-inline-row .form-group { margin-bottom: 0; }
.filter-inline-row label { font-size: 11px; color: #666; margin-bottom: 3px; display: block; white-space: nowrap; }
.filter-inline-row .form-control { height: 32px; padding: 4px 8px; font-size: 12px; }
.filter-inline-row .input-daterange { width: 110px; }
.filter-inline-row .input-status { width: 100px; }
.filter-inline-row .input-search { min-width: 180px; }

/* Custom Info Box */
.info-box-custom {
  min-height: 90px;
  border-radius: 6px;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  padding: 15px 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.08);
  color: #fff;
  position: relative;
  overflow: hidden;
}
.info-box-custom .info-icon {
  font-size: 40px;
  opacity: 0.35;
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
}
.info-box-custom .info-label {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.9;
  margin-bottom: 5px;
  display: block;
  font-weight: 500;
}
.info-box-custom .info-value {
  font-size: 30px;
  font-weight: 700;
  line-height: 1.2;
  display: block;
  color: #fff;
}
.info-box-custom .info-detail {
  font-size: 11px;
  opacity: 0.85;
  margin-top: 4px;
  display: block;
  color: #fff;
}
.bg-grad-blue { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
.bg-grad-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.bg-grad-red { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
.bg-grad-yellow { background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); }

/* Chips */
.filter-chips { margin-bottom: 12px; display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
.filter-chips-label { font-size: 12px; color: #666; font-weight: 600; margin-right: 4px; }
.filter-chip { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; background: #e8f4fd; border: 1px solid #b6d4fe; border-radius: 12px; font-size: 12px; color: #084298; }
.filter-chip .chip-remove { cursor: pointer; font-weight: bold; line-height: 1; opacity: 0.6; }
.filter-chip .chip-remove:hover { opacity: 1; }

/* Chart collapsible */
.chart-container { background: #fff; border: 1px solid #e0e0e0; border-radius: 5px; padding: 15px; margin-bottom: 15px; }
.chart-container h4 { margin: 0 0 10px 0; font-size: 14px; font-weight: 600; color: #333; }

/* Avatar / Initial */
.student-avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: #3c8dbc; color: #fff;
  display: inline-flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 13px; margin-right: 8px; flex-shrink: 0;
}
.student-name-wrap { display: flex; align-items: center; }
</style>

<script src="<?= BASE_ASSET; ?>js/chart.min.js"></script>

<script type="text/javascript">
function resetForm() {
    $('#filter_jenjang').val('').trigger('chosen:updated');
    $('#filter_tingkatan').val('').trigger('chosen:updated');
    $('#filter_kelas').val('').trigger('chosen:updated');
    $('#filter_status').val('').trigger('chosen:updated');
    $('#start_date').val('');
    $('#end_date').val('');
    $('#filter').val('');
}

function exportData(type) {
    var params = $('#form_presensi_pramuka').serialize();
    var baseUrl = '<?= site_url("administrator/presensi_pramuka/" . (!empty($is_locked) ? $url_method : "index")); ?>';
    if (type === 'xls') {
        window.location.href = baseUrl + '/export?' + params;
    } else if (type === 'pdf') {
        window.open(baseUrl + '/export_pdf?' + params, '_blank');
    }
}
</script>

<!-- Content Header -->
<section class="content-header">
   <h1><i class="fa fa-compass"></i> <?= cclang('presensi_pramuka') ?> <small><?= cclang('list_all'); ?></small></h1>
   <ol class="breadcrumb"><li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li><li class="active"><?= cclang('presensi_pramuka') ?></li></ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">

            <!-- Box Header -->
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-compass"></i> Data Presensi Pramuka
                  <span class="label bg-yellow" style="margin-left:10px" id="totalData"><?= $presensi_pramuka_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('presensi_pramuka_add', function () use ($is_locked, $url_method) { ?>
                  <a class="btn btn-sm btn-success" title="Tambah Presensi Pramuka (Ctrl+a)" href="<?= site_url('administrator/presensi_pramuka/' . (!empty($is_locked) ? $url_method : 'index') . '/add'); ?>">
                     <i class="fa fa-plus"></i> Tambah
                  </a>
                  <?php }) ?>
                  <?php is_allowed('presensi_pramuka_export', function(){?>
                  <a class="btn btn-sm btn-success" title="Export XLS" href="javascript:void(0)" onclick="exportData('xls')">
                     <i class="fa fa-file-excel-o"></i> XLS
                  </a>
                  <a class="btn btn-sm btn-warning" title="Export PDF" href="javascript:void(0)" onclick="exportData('pdf')">
                     <i class="fa fa-file-pdf-o"></i> PDF
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">

               <?php if (!empty($is_locked)): ?>
               <div style="margin-bottom:15px;padding:10px 15px;background:linear-gradient(135deg,#f39c12,#e67e22);color:#fff;border-radius:4px;box-shadow:0 2px 4px rgba(0,0,0,0.08)">
                  <i class="fa fa-lock" style="margin-right:8px"></i>
                  <strong>Terkunci pada Jenjang <?= htmlspecialchars($lock_label); ?></strong>
                  <span style="margin-left:10px;opacity:0.9">— Data dan operasi hanya untuk jenjang ini.</span>
               </div>
               <?php endif; ?>

               <!-- 1. Info Boxes -->
               <div class="row" style="margin-bottom:15px" id="infoBoxes">
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-blue">
                        <i class="fa fa-database info-icon"></i>
                        <div>
                           <span class="info-label">Total Data</span>
                           <span class="info-value" id="sumTotalData"><?= !empty($summary->total_records) ? number_format($summary->total_records) : 0; ?></span>
                           <span class="info-detail">Seluruh data presensi</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-green">
                        <i class="fa fa-check-circle info-icon"></i>
                        <div>
                           <span class="info-label">Hadir</span>
                           <span class="info-value" id="sumHadir"><?= !empty($summary->total_hadir) ? number_format($summary->total_hadir) : 0; ?></span>
                           <span class="info-detail">Siswa hadir Pramuka</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-red">
                        <i class="fa fa-times-circle info-icon"></i>
                        <div>
                           <span class="info-label">Tidak Hadir / Non-Hadir</span>
                           <?php
                           $sakit = !empty($summary->total_sakit) ? (int)$summary->total_sakit : 0;
                           $izin = !empty($summary->total_izin) ? (int)$summary->total_izin : 0;
                           $alfa = !empty($summary->total_alfa) ? (int)$summary->total_alfa : 0;
                           $tidak_hadir = $sakit + $izin + $alfa;
                           ?>
                           <span class="info-value" id="sumTidakHadir"><?= number_format($tidak_hadir); ?></span>
                           <span class="info-detail" id="sumTidakHadirDetail">S: <?= $sakit; ?> | I: <?= $izin; ?> | A: <?= $alfa; ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-yellow">
                        <i class="fa fa-star info-icon"></i>
                        <div>
                           <span class="info-label">Rata-rata Nilai</span>
                           <?php $avg_val = !empty($summary->avg_total) ? (float)$summary->avg_total : 0.0; ?>
                           <span class="info-value" id="sumRataRata"><?= number_format($avg_val, 1); ?></span>
                           <?php
                           $pred_stat = get_predikat_pramuka($avg_val);
                           ?>
                           <span class="info-detail" id="sumPredikat">Predikat: <?= $pred_stat['predikat'] . ' - ' . $pred_stat['deskripsi']; ?></span>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- 2. Filter & Search -->
               <form name="form_presensi_pramuka" id="form_presensi_pramuka" action="<?= base_url('administrator/presensi_pramuka/' . (!empty($is_locked) ? $url_method : 'index')); ?>" onsubmit="return false;">
               <div class="filter-builder">
                  <div class="filter-inline-row">
                     <div class="form-group">
                        <label>Jenjang</label>
                        <?php if (!empty($is_locked) && count($list_jenjang) == 1): ?>
                           <input type="text" class="form-control" value="<?= $list_jenjang[0]['label']; ?>" disabled style="width:70px">
                           <input type="hidden" name="jenjang" value="<?= $list_jenjang[0]['code']; ?>">
                        <?php else: ?>
                           <select class="form-control chosen chosen-select" name="jenjang" id="filter_jenjang" style="width:80px">
                              <option value="">Semua</option>
                              <?php foreach ($list_jenjang as $j): ?>
                              <option value="<?= $j['code']; ?>" <?= (!empty($active_filters['jenjang']) && $active_filters['jenjang'] == $j['code']) ? 'selected' : ''; ?>><?= $j['label']; ?></option>
                              <?php endforeach; ?>
                           </select>
                        <?php endif; ?>
                     </div>
                     <div class="form-group">
                        <label>Tingkatan</label>
                        <select class="form-control chosen chosen-select" name="id_tingkatan" id="filter_tingkatan" style="width:110px">
                           <option value="">Semua</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Kelas</label>
                        <select class="form-control chosen chosen-select" name="kelas" id="filter_kelas" style="width:110px">
                           <option value="">Semua</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Status</label>
                        <select class="form-control chosen chosen-select input-status" name="status_hadir" id="filter_status">
                           <option value="">Semua</option>
                           <option value="Hadir" <?= (!empty($active_filters['status_hadir']) && $active_filters['status_hadir'] == 'Hadir') ? 'selected' : ''; ?>>Hadir</option>
                           <option value="Terlambat" <?= (!empty($active_filters['status_hadir']) && $active_filters['status_hadir'] == 'Terlambat') ? 'selected' : ''; ?>>Terlambat</option>
                           <option value="Sakit" <?= (!empty($active_filters['status_hadir']) && $active_filters['status_hadir'] == 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                           <option value="Izin" <?= (!empty($active_filters['status_hadir']) && $active_filters['status_hadir'] == 'Izin') ? 'selected' : ''; ?>>Izin</option>
                           <option value="Alfa" <?= (!empty($active_filters['status_hadir']) && $active_filters['status_hadir'] == 'Alfa') ? 'selected' : ''; ?>>Alfa</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" class="form-control input-daterange" name="start_date" id="start_date" value="<?= !empty($active_filters['start_date']) ? $active_filters['start_date'] : ''; ?>">
                     </div>
                     <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" class="form-control input-daterange" name="end_date" id="end_date" value="<?= !empty($active_filters['end_date']) ? $active_filters['end_date'] : ''; ?>">
                     </div>
                     <div class="form-group" style="flex:1;min-width:180px">
                        <label>&nbsp;</label>
                        <div class="input-group">
                           <input type="text" class="form-control input-search" name="q" id="filter" placeholder="Cari nama, kelas..." value="<?= !empty($active_filters['q']) ? htmlspecialchars($active_filters['q']) : ''; ?>">
                           <span class="input-group-btn">
                              <button type="button" class="btn btn-flat btn-primary" id="btnSearch"><i class="fa fa-search"></i></button>
                              <button type="button" class="btn btn-flat btn-default" id="btnReset" title="Reset filter"><i class="fa fa-times"></i></button>
                           </span>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- 3. Chart -->
               <div class="chart-container">
                  <h4 id="chartToggle" style="cursor:pointer">
                     <i class="fa fa-bar-chart"></i> Chart Mingguan Pramuka (8 Minggu Terakhir)
                     <i class="fa fa-chevron-down" id="chartIcon" style="font-size:12px;margin-left:5px"></i>
                  </h4>
                  <div id="chartBody" style="display:none">
                     <canvas id="chartPramuka" width="100%" height="30"></canvas>
                  </div>
               </div>

               <!-- 4. Data Table -->
               <div class="labs-table-wrap">
                  <table class="labs-table table table-bordered table-striped table-hover dataTable">
                     <thead>
                        <tr>
                           <th width="30">
                              <input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all">
                           </th>
                           <th>Tanggal</th>
                           <th>Nama Siswa</th>
                           <th>Jenjang / Kelas</th>
                           <th>Status</th>
                           <th>Kehadiran</th>
                           <th>Atribut</th>
                           <th>Keaktifan</th>
                           <th>Total Nilai</th>
                           <th width="150" class="text-center">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_presensi_pramuka">
                     <?php if($presensi_pramuka_counts > 0): ?>
                     <?php foreach($presensi_pramukas as $pt):
                        $total = (float) $pt->total_nilai;
                        $pred_info = get_predikat_pramuka($total);
                        $pred = $pred_info['predikat'];
                        $desc = $pred_info['deskripsi'];
                        $badge = $pred == 'A' ? 'badge-a' : ($pred == 'B' ? 'badge-b' : ($pred == 'C' ? 'badge-c' : ($pred == 'D' ? 'badge-d' : 'badge-e')));

                        $status_badge = '';
                        switch (strtolower($pt->status_hadir)) {
                           case 'hadir':      $status_badge = '<span class="label label-success">Hadir</span>'; break;
                           case 'terlambat':  $status_badge = '<span class="label label-warning">Terlambat</span>'; break;
                           case 'izin':       $status_badge = '<span class="label label-info">Izin</span>'; break;
                           case 'sakit':      $status_badge = '<span class="label label-primary">Sakit</span>'; break;
                           case 'alfa':       $status_badge = '<span class="label label-danger">Alfa</span>'; break;
                           default:           $status_badge = '<span class="label label-default">'._ent($pt->status_hadir).'</span>'; break;
                        }
                     ?>
                        <tr>
                           <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $pt->id_presensi_pramuka; ?>"></td>
                           <td><?= _ent($pt->tanggal); ?><br><small class="text-muted"><?= _ent($pt->hari); ?></small></td>
                           <td><strong><?= _ent($pt->nama_lengkap); ?></strong></td>
                           <td><span class="label label-default"><?= _ent($pt->jenjang); ?></span> <?= _ent($pt->kelas); ?></td>
                           <td><?= $status_badge; ?></td>
                           <td><strong><?= _ent($pt->kehadiran); ?></strong></td>
                           <td><?= _ent($pt->status_kelengkapan); ?><br><small class="text-muted">(<?= _ent($pt->kelengkapan); ?>)</small></td>
                           <td><?= _ent($pt->status_keaktifan); ?><br><small class="text-muted">(<?= _ent($pt->keaktifan); ?>)</small></td>
                           <td>
                              <strong style="font-size:15px"><?= _ent($pt->total_nilai); ?></strong><br>
                              <span class="badge-predikat <?= $badge; ?>" title="<?= $desc; ?>"><?= $pred; ?></span>
                           </td>
                           <td class="text-center">
                              <?php is_allowed('presensi_pramuka_view', function() use ($pt){?>
                              <a href="javascript:void(0);" class="btn btn-sm btn-info btn-detail-modal" data-id="<?= $pt->id_presensi_pramuka; ?>" title="Lihat detail" style="margin:2px 1px;padding:4px 0;font-size:11px;border-radius:3px;width:48%;display:inline-block;text-align:center">
                                 <i class="fa fa-eye"></i> Detail
                              </a>
                              <?php }) ?>
                              <?php is_allowed('presensi_pramuka_update', function() use ($pt){?>
                              <a href="<?= site_url('administrator/presensi_pramuka/edit/' . $pt->id_presensi_pramuka); ?>" class="btn btn-sm btn-success" title="Edit" style="margin:2px 1px;padding:4px 0;font-size:11px;border-radius:3px;width:48%;display:inline-block;text-align:center">
                                 <i class="fa fa-edit"></i> Edit
                              </a>
                              <?php }) ?>
                              <?php is_allowed('presensi_pramuka_delete', function() use ($pt, $is_locked, $url_method){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/presensi_pramuka/' . (!empty($is_locked) ? $url_method : 'index') . '/delete/' . $pt->id_presensi_pramuka); ?>" class="btn btn-sm btn-danger remove-data" title="Hapus" style="margin:2px 1px;padding:4px 0;font-size:11px;border-radius:3px;width:98%;display:block;text-align:center">
                                 <i class="fa fa-trash"></i> Hapus
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr><td colspan="10" class="text-center text-muted" style="padding:30px">Tidak ada data presensi pramuka.</td></tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <!-- Pagination & Actions -->
               <div class="row" style="margin-top:15px">
                  <div class="col-md-4">
                     <?php is_allowed('presensi_pramuka_delete', function() { ?>
                     <button type="button" class="btn btn-sm btn-danger" id="btn_apply"><i class="fa fa-trash"></i> Hapus Terpilih</button>
                     <?php }) ?>
                  </div>
                  <div class="col-md-8 text-right" id="paginationWrap">
                     <?= $pagination; ?>
                  </div>
               </div>

               </form>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Detail Modal -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-yellow">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><i class="fa fa-compass"></i> Detail Presensi Pramuka</h4>
      </div>
      <div class="modal-body" id="modalDetailBody" style="padding:20px">
        <div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Memuat data...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script>
var ajaxUrl = '<?= site_url('administrator/presensi_pramuka/get_list_ajax'); ?>';
var baseUrlMethod = '<?= site_url('administrator/presensi_pramuka/' . (!empty($is_locked) ? $url_method : 'index')); ?>';
var chartInstance = null;

$(document).ready(function(){

   $('.chosen-select').chosen({width: '100%'});

   $('#chartToggle').on('click', function(){
      $('#chartBody').slideToggle(200, function(){
         if ($('#chartBody').is(':visible')) {
            $('#chartIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            renderChart(window.lastChartData || <?= json_encode($chart_data); ?>);
         } else {
            $('#chartIcon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
         }
      });
   });

   $('#btnReset').on('click', function(){
      resetForm();
      loadData(0);
   });

   $('#btnSearch').on('click', function(){
      loadData(0);
   });

   $('#filter').on('keypress', function(e){
      if (e.which == 13) {
         e.preventDefault();
         loadData(0);
      }
   });

   $('#filter_jenjang, #filter_tingkatan, #filter_kelas, #filter_status, #start_date, #end_date').on('change', function(){
      loadData(0);
   });

   $('#filter_jenjang').on('change', function(){
      var jenjang = $(this).val();
      $('#filter_tingkatan').html('<option value="">Semua</option>').trigger('chosen:updated');
      $('#filter_kelas').html('<option value="">Semua</option>').trigger('chosen:updated');

      if (jenjang) {
         $.get(BASE_URL + '/administrator/presensi_pramuka/get_tingkatan', {jenjang: jenjang}, function(res){
            var resp = JSON.parse(res);
            if (resp.status === 'success') {
               var opts = '<option value="">Semua</option>';
               $.each(resp.data, function(i, item){
                  opts += '<option value="' + item.id + '">' + item.label + '</option>';
               });
               $('#filter_tingkatan').html(opts).trigger('chosen:updated');
            }
         });
         $.get(BASE_URL + '/administrator/presensi_pramuka/get_kelas', {jenjang: jenjang}, function(res){
            var resp = JSON.parse(res);
            if (resp.status === 'success') {
               var opts = '<option value="">Semua</option>';
               $.each(resp.data, function(i, item){
                  opts += '<option value="' + item.label + '">' + item.label + '</option>';
               });
               $('#filter_kelas').html(opts).trigger('chosen:updated');
            }
         });
      }
   });

   $('#filter_tingkatan').on('change', function(){
      var jenjang = $('#filter_jenjang').val();
      var id_tingkatan = $(this).val();
      $('#filter_kelas').html('<option value="">Semua</option>').trigger('chosen:updated');
      if (jenjang) {
         $.get(BASE_URL + '/administrator/presensi_pramuka/get_kelas', {jenjang: jenjang, id_tingkatan: id_tingkatan}, function(res){
            var resp = JSON.parse(res);
            if (resp.status === 'success') {
               var opts = '<option value="">Semua</option>';
               $.each(resp.data, function(i, item){
                  opts += '<option value="' + item.label + '">' + item.label + '</option>';
               });
               $('#filter_kelas').html(opts).trigger('chosen:updated');
            }
         });
      }
   });

   $('#check_all').on('ifClicked', function(){
      var checked = $(this).is(':checked');
      $('input.check').iCheck(checked ? 'uncheck' : 'check');
   });

   $('#btn_apply').on('click', function(){
      var serialize_bulk = $('#form_presensi_pramuka').serialize();
      if ($('input.check:checked').length === 0) {
         swal({title:"Peringatan", text:"Pilih data yang ingin dihapus!", type:"warning"});
         return;
      }
      swal({
         title: "Apakah Anda yakin?",
         text: "Data yang dihapus tidak dapat dikembalikan!",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Ya, Hapus!",
         cancelButtonText: "Batal",
         closeOnConfirm: true
      }, function(isConfirm){
         if (isConfirm) {
            window.location.href = BASE_URL + '/administrator/presensi_pramuka/<?= !empty($is_locked) ? $url_method : 'index'; ?>/delete?' + serialize_bulk;
         }
      });
   });

   $(document).on('click', '.remove-data', function(){
      var url = $(this).attr('data-href');
      swal({
         title: "Apakah Anda yakin?",
         text: "Data yang dihapus tidak dapat dikembalikan!",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Ya, Hapus!",
         cancelButtonText: "Batal",
         closeOnConfirm: true
      }, function(isConfirm){
         if (isConfirm) {
            window.location.href = url;
         }
      });
   });

   $(document).on('click', '.btn-detail-modal', function(){
      var id = $(this).data('id');
      $('#modalDetail').modal('show');
      $('#modalDetailBody').html('<div class="text-center" style="padding:30px"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Memuat detail...</div>');

      $.get(BASE_URL + '/administrator/presensi_pramuka/get_detail/' + id, function(res){
         var resp = typeof res === 'object' ? res : JSON.parse(res);
         if (resp.status === 'success') {
            var d = resp.data;
            var html = '<table class="table table-bordered">'
                     + '<tr><th width="30%">Tanggal</th><td>' + d.tanggal + ' (' + d.hari + ')</td></tr>'
                     + '<tr><th>Nama Siswa</th><td><strong>' + d.nama_lengkap + '</strong> (NIS: ' + d.nis + ')</td></tr>'
                     + '<tr><th>Jenjang / Kelas</th><td>' + d.jenjang + ' - ' + d.kelas + '</td></tr>'
                     + '<tr><th>Status Hadir</th><td>' + d.status_hadir + ' (Nilai: ' + d.kehadiran + ')</td></tr>'
                     + '<tr><th>Kelengkapan Atribut</th><td>' + d.status_kelengkapan + ' (Nilai: ' + d.kelengkapan + ')</td></tr>'
                     + '<tr><th>Keaktifan</th><td>' + d.status_keaktifan + ' (Nilai: ' + d.keaktifan + ')</td></tr>'
                     + '<tr><th>Total Nilai</th><td><strong style="font-size:16px">' + d.total_nilai + '</strong> (' + d.predikat + ' - ' + d.deskripsi + ')</td></tr>'
                     + '<tr><th>Diupdate Oleh</th><td>' + d.updated_by + '</td></tr>'
                     + '</table>'
                     + '<div class="text-center" style="margin-top:15px">'
                     + '<a href="' + BASE_URL + '/administrator/presensi_pramuka/single_pdf/' + d.id_presensi_pramuka + '" target="_blank" class="btn btn-sm btn-warning"><i class="fa fa-file-pdf-o"></i> Cetak PDF</a>'
                     + '</div>';
            $('#modalDetailBody').html(html);
         } else {
            $('#modalDetailBody').html('<div class="alert alert-danger">Gagal memuat detail data.</div>');
         }
      });
   });

   $(document).on('click', '#paginationWrap a', function(e){
      e.preventDefault();
      var href = $(this).attr('href');
      if (href) {
         var segs = href.split('/');
         var offset = segs[segs.length - 1] || 0;
         loadData(offset);
      }
   });
});

function loadData(offset) {
   var params = $('#form_presensi_pramuka').serialize() + '&offset=' + offset;
   $('#tbody_presensi_pramuka').html('<tr><td colspan="10" class="text-center" style="padding:30px"><i class="fa fa-spinner fa-spin fa-2x" style="color:#C2410C"></i><br><small class="text-muted">Memuat data...</small></td></tr>');

   $.get(ajaxUrl + '?' + params, function(res){
      var resp = typeof res === 'object' ? res : JSON.parse(res);
      if (resp.status === 'success') {
         $('#tbody_presensi_pramuka').html(resp.rows_html);
         $('#paginationWrap').html(resp.pagination);
         $('#totalData').text(resp.total_data + ' Data');

         if (resp.summary) {
            $('#sumTotalData').text(Number(resp.summary.total_records || 0).toLocaleString());
            $('#sumHadir').text(Number(resp.summary.total_hadir || 0).toLocaleString());
            var th = Number(resp.summary.total_sakit || 0) + Number(resp.summary.total_izin || 0) + Number(resp.summary.total_alfa || 0);
            $('#sumTidakHadir').text(th.toLocaleString());
            $('#sumTidakHadirDetail').text('S: ' + (resp.summary.total_sakit||0) + ' | I: ' + (resp.summary.total_izin||0) + ' | A: ' + (resp.summary.total_alfa||0));
            var avg = Number(resp.summary.avg_total || 0);
            $('#sumRataRata').text(avg.toFixed(1));
         }

         window.lastChartData = resp.chart_data;
         if ($('#chartBody').is(':visible')) {
            renderChart(resp.chart_data);
         }

         $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
         });
      }
   });
}

function renderChart(chartData) {
   if (!chartData || chartData.length === 0) return;
   var ctx = document.getElementById('chartPramuka');
   if (!ctx) return;

   var labels = [];
   var hadirData = [];
   var tidakHadirData = [];
   var avgNilaiData = [];

   $.each(chartData, function(i, item){
      labels.push('W' + item.yw);
      hadirData.push(item.hadir);
      tidakHadirData.push(Number(item.sakit) + Number(item.izin) + Number(item.alfa));
      avgNilaiData.push(item.avg_nilai);
   });

   if (chartInstance) {
      chartInstance.destroy();
   }

   chartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
         labels: labels,
         datasets: [
            {
               label: 'Hadir',
               data: hadirData,
               backgroundColor: 'rgba(39, 174, 96, 0.7)',
               borderColor: 'rgba(39, 174, 96, 1)',
               borderWidth: 1
            },
            {
               label: 'Tidak Hadir',
               data: tidakHadirData,
               backgroundColor: 'rgba(231, 76, 60, 0.7)',
               borderColor: 'rgba(231, 76, 60, 1)',
               borderWidth: 1
            },
            {
               label: 'Rata-rata Nilai',
               data: avgNilaiData,
               type: 'line',
               fill: false,
               borderColor: 'rgba(243, 156, 18, 1)',
               backgroundColor: 'rgba(243, 156, 18, 1)',
               yAxisID: 'y-axis-2'
            }
         ]
      },
      options: {
         responsive: true,
         scales: {
            yAxes: [
               {
                  id: 'y-axis-1',
                  type: 'linear',
                  position: 'left',
                  ticks: { beginAtZero: true }
               },
               {
                  id: 'y-axis-2',
                  type: 'linear',
                  position: 'right',
                  ticks: { beginAtZero: true, max: 100 },
                  gridLines: { drawOnChartArea: false }
               }
            ]
         }
      }
   });
}
</script>
