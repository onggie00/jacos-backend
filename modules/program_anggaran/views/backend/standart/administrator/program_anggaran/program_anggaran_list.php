
<style>
/* Top Buttons */
.btn-top { margin-right: 5px; border-radius: 3px; }

/* Table Styling */
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; text-align: center; }
.table td { font-size: 13px; vertical-align: middle; }

/* Status Labels */
.label-aktif { background: #27ae60; }
.label-nonaktif { background: #e74c3c; }

/* Jenjang Lock Badge */
.jenjang-lock {
   display: inline-block;
   padding: 4px 12px;
   background: #3498db;
   color: #fff;
   border-radius: 3px;
   font-weight: 600;
   font-size: 12px;
   margin-left: 8px;
}

/* Action Buttons */
.btn-aksi { margin: 2px 1px; padding: 4px 0; font-size: 11px; border-radius: 3px; width: 48%; display: inline-block; text-align: center; }
.btn-aksi i { margin-right: 3px; }
.btn-aksi-full { margin: 2px 1px; padding: 4px 0; font-size: 11px; border-radius: 3px; width: 98%; display: block; text-align: center; }
.btn-aksi-full i { margin-right: 3px; }

/* Filter Builder */
.filter-builder { transition: all 0.3s ease; }
.filter-builder:hover { box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
.filter-row { animation: fadeInRow 0.3s ease; }
@keyframes fadeInRow {
   from { opacity: 0; transform: translateY(-5px); }
   to   { opacity: 1; transform: translateY(0); }
}

/* Compact Advanced Filter (override inline styles) */
.filter-builder { padding: 8px !important; }
.filter-builder > div:first-child { margin-bottom: 6px !important; }
.filter-builder > div:first-child strong { font-size: 13px !important; }
.filter-builder #filter_empty { padding: 8px !important; font-size: 12px; }
.filter-builder .filter-row { gap: 6px !important; margin-bottom: 4px !important; }
.filter-builder .filter-row .form-control { font-size: 12px !important; padding: 4px 8px !important; height: auto !important; }
.filter-builder .btn-flat { padding: 4px 10px !important; font-size: 12px !important; }

/* Dashboard Chart Toggle */
.chart-toggle-area {
   max-height: 500px;
   overflow: hidden;
   transition: max-height 0.4s ease-in-out, opacity 0.4s ease-in-out, margin-top 0.4s ease-in-out;
   opacity: 1;
}
.chart-toggle-area.chart-hidden {
   max-height: 0;
   opacity: 0;
   margin-top: 0 !important;
}
.btn-toggle-chart { margin-right: 8px; }

/* Info Boxes */
.info-box-custom { min-height:90px; border-radius:6px; margin-bottom:15px; display:flex; align-items:center; padding:15px 20px; box-shadow:0 2px 4px rgba(0,0,0,0.08); color:#fff; position:relative; overflow:hidden; }
.info-box-custom .info-icon { font-size:40px; opacity:0.35; position:absolute; right:15px; top:50%; transform:translateY(-50%); }
.info-box-custom .info-label { font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.9; margin-bottom:5px; display:block; font-weight:500; }
.info-box-custom .info-value { font-size:24px; font-weight:700; line-height:1.2; display:block; color:#fff; word-break:break-word; }
.info-box-custom .info-detail { font-size:11px; opacity:0.85; margin-top:4px; display:block; color:#fff; }
.bg-grad-blue { background:linear-gradient(135deg,#3c8dbc 0%,#367fa9 100%); }
.bg-grad-green { background:linear-gradient(135deg,#00a65a 0%,#008d4c 100%); }
.bg-grad-purple { background:linear-gradient(135deg,#605ca8 0%,#555299 100%); }
.bg-grad-orange { background:linear-gradient(135deg,#f39c12 0%,#db8b0a 100%); }
</style>

<script type="text/javascript">
<?php if ($this->session->flashdata('success')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('success'))); ?>
   toastr.success("<?= $msg; ?>", "Berhasil", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } else if ($this->session->flashdata('error')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('error'))); ?>
   toastr.error("<?= $msg; ?>", "Error", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } else if ($this->session->flashdata('warning')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('warning'))); ?>
   toastr.warning("<?= $msg; ?>", "Peringatan", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } else if ($this->session->flashdata('info')) { ?>
   toastr.info("<?= addslashes($this->session->flashdata('info')); ?>");
<?php } ?>
</script>

<!-- Content Header -->
<section class="content-header">
   <h1>
      <i class="fa fa-money"></i> <?= cclang('program_anggaran') ?>
      <?php if (!empty($jenjang_context)): ?>
         <span class="jenjang-lock"><i class="fa fa-lock"></i> Jenjang: <?= strtoupper($jenjang_context); ?></span>
      <?php else: ?>
         <small><?= cclang('list_all'); ?></small>
      <?php endif; ?>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('program_anggaran') ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">

            <!-- Box Header -->
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-list-alt"></i> Data Program Anggaran
                  <span class="label bg-yellow" style="margin-left:10px"><?= $program_anggaran_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('program_anggaran_add', function(){?>
                  <a class="btn btn-sm btn-success btn_add_new btn-top" title="<?= cclang('add_new_button', [cclang('program_anggaran')]); ?> (Ctrl+a)" href="<?= site_url('administrator/program_anggaran/add' . (!empty($jenjang_context) ? '?jenjang=' . $jenjang_context : '')); ?>">
                     <i class="fa fa-plus-square-o"></i> <?= cclang('add_new_button', [cclang('program_anggaran')]); ?>
                  </a>
                  <?php }) ?>
                  <?php is_allowed('program_anggaran_export', function(){?>
                  <a class="btn btn-sm btn-success btn-top" title="<?= cclang('export'); ?> XLS" href="<?= site_url('administrator/program_anggaran/export'); ?>">
                     <i class="fa fa-file-excel-o"></i> XLS
                  </a>
                  <?php }) ?>
                  <?php if (empty($jenjang_context)): ?>
                  <a class="btn btn-sm btn-info btn-top" title="Import Excel" data-toggle="modal" data-target="#modal_add_new">
                     <i class="fa fa-upload"></i> Import Program
                  </a>
                  <?php endif; ?>
               </div>
            </div>

            <div class="box-body">

               <!-- Dashboard Widget Ringkasan Dana OKR -->
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-12">
                     <div class="box box-solid" style="border-top:3px solid #3c8dbc;border-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
                        <div class="box-header with-border" style="background:#f8f9fa">
                           <h3 class="box-title" style="font-size:15px;color:#2c3e50">
                              <i class="fa fa-dashboard" style="color:#3c8dbc"></i>
                              <?= cclang('dashboard_chart_title'); ?>
                              <span class="label" style="background:#3c8dbc;margin-left:8px;font-size:11px">
                                 TA: <?= htmlspecialchars(!empty($dashboard_ta) ? $dashboard_ta : '-'); ?>
                              </span>
                           </h3>
                           <div class="box-tools pull-right">
                              <button type="button" class="btn btn-xs btn-default btn-toggle-chart" id="btn_toggle_chart" title="Sembunyikan/Tampilkan Chart">
                                 <i class="fa fa-eye-slash" id="icon_toggle_chart"></i>
                                 <span id="text_toggle_chart">Sembunyikan Chart</span>
                              </button>
                              <form method="get" action="<?= base_url('administrator/program_anggaran' . (!empty($jenjang_context) ? '/' . $jenjang_context : '') . '/index'); ?>" style="display:inline-flex;align-items:center;gap:6px;margin:0">
                                 <?php
                                    // Preserve existing filter params (q, ff, fo, fv) saat ganti TA
                                    $preserved = array('q', 'ff', 'fo', 'fv');
                                    foreach ($preserved as $pk) {
                                       $pv = $this->input->get($pk);
                                       if (is_array($pv)) {
                                          foreach ($pv as $vv) {
                                             echo '<input type="hidden" name="' . $pk . '[]" value="' . htmlspecialchars($vv) . '">';
                                          }
                                       } elseif ($pv !== null && $pv !== '') {
                                          echo '<input type="hidden" name="' . $pk . '" value="' . htmlspecialchars($pv) . '">';
                                       }
                                    }
                                 ?>
                                 <label style="font-weight:normal;margin:0;font-size:12px;color:#555"><?= cclang('dashboard_filter_ta'); ?>:</label>
                                 <select name="ta" class="form-control input-sm" style="width:auto;min-width:140px" onchange="this.form.submit()">
                                    <?php if (empty($dashboard_tahun_ajaran_options)): ?>
                                       <option value="">--</option>
                                    <?php else: ?>
                                       <?php foreach ($dashboard_tahun_ajaran_options as $opt_ta): ?>
                                          <option value="<?= htmlspecialchars($opt_ta); ?>" <?= ($opt_ta === $dashboard_ta) ? 'selected' : ''; ?>>
                                             <?= htmlspecialchars($opt_ta); ?>
                                          </option>
                                       <?php endforeach; ?>
                                    <?php endif; ?>
                                 </select>
                              </form>
                           </div>
                        </div>
                        <div class="box-body" style="padding:15px">
                           <?php
                              $dd = isset($dashboard_data) ? $dashboard_data : array('okr' => 0, 'ajuan' => 0, 'cair' => 0);
                              $fmt = function($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); };
                              $has_data = ((float)$dd['okr'] + (float)$dd['ajuan'] + (float)$dd['cair']) > 0;
                           ?>
                           <?php if (!$has_data): ?>
                              <div class="alert alert-warning" style="margin:0;text-align:center">
                                 <i class="fa fa-info-circle"></i> <?= cclang('dashboard_no_data'); ?> <strong><?= htmlspecialchars(!empty($dashboard_ta) ? $dashboard_ta : '-'); ?></strong>
                              </div>
                           <?php else: ?>
                           <div class="row">
                              <div class="col-md-4">
                                 <div class="info-box-custom bg-grad-blue">
                                    <i class="fa fa-money info-icon"></i>
                                    <div>
                                       <span class="info-label"><?= cclang('dashboard_okr_terkumpul'); ?></span>
                                       <span class="info-value"><?= $fmt($dd['okr']); ?></span>
                                       <span class="info-detail">TA: <?= htmlspecialchars(!empty($dashboard_ta) ? $dashboard_ta : '-'); ?></span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <div class="info-box-custom bg-grad-orange">
                                    <i class="fa fa-send-o info-icon"></i>
                                    <div>
                                       <span class="info-label"><?= cclang('dashboard_okr_diajukan'); ?></span>
                                       <span class="info-value"><?= $fmt($dd['ajuan']); ?></span>
                                       <span class="info-detail">TA: <?= htmlspecialchars(!empty($dashboard_ta) ? $dashboard_ta : '-'); ?></span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <div class="info-box-custom bg-grad-green">
                                    <i class="fa fa-check-circle info-icon"></i>
                                    <div>
                                       <span class="info-label"><?= cclang('dashboard_okr_dicairkan'); ?></span>
                                       <span class="info-value"><?= $fmt($dd['cair']); ?></span>
                                       <span class="info-detail">TA: <?= htmlspecialchars(!empty($dashboard_ta) ? $dashboard_ta : '-'); ?></span>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div id="dashboard_chart_canvas_area" class="chart-toggle-area" style="margin-top:15px">
                              <div class="row">
                                 <div class="col-md-12">
                                    <canvas id="chart_okr" height="80"></canvas>
                                 </div>
                              </div>
                           </div>
                           <?php endif; ?>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- End Dashboard Widget -->

               <!-- Advanced Filter Builder -->
               <form name="form_program_anggaran" id="form_program_anggaran" action="<?= base_url('administrator/program_anggaran' . (!empty($jenjang_context) ? '/' . $jenjang_context : '') . '/index'); ?>" method="get">
               <div class="filter-builder" style="margin-bottom:15px;padding:12px;background:#f8f9fa;border-radius:5px;border:1px solid #e0e0e0">
                  <div style="display:flex;align-items:center;margin-bottom:10px">
                     <i class="fa fa-filter" style="color:#3498db;margin-right:8px"></i>
                     <strong style="color:#2c3e50;font-size:14px">Advanced Filter</strong>
                     <button type="button" class="btn btn-xs btn-success" id="btn_add_filter" style="margin-left:auto">
                        <i class="fa fa-plus"></i> Tambah Filter
                     </button>
                  </div>

                  <div id="filter_rows"></div>

                  <div id="filter_empty" style="text-align:center;padding:15px;color:#999">
                     <i class="fa fa-search" style="font-size:20px;margin-bottom:5px"></i><br>
                     <small>Klik "Tambah Filter" untuk menambah filter pencarian</small>
                  </div>

                  <div style="margin-top:10px;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                     <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Terapkan Filter</button>
                     <a class="btn btn-flat btn-default" href="<?= base_url('administrator/program_anggaran' . (!empty($jenjang_context) ? '/' . $jenjang_context : '')); ?>"><i class="fa fa-times"></i> Reset Semua</a>
                     <div style="margin-left:auto;display:flex;gap:8px;align-items:center">
                        <input type="text" class="form-control input-sm" name="q" id="filter" placeholder="Cari cepat..." value="<?= htmlspecialchars($this->input->get('q')); ?>" style="width:200px">
                     </div>
                  </div>
               </div>

               <?php if(!empty($multi_filters)): ?>
               <div style="margin-bottom:10px;padding:8px 12px;background:#e8f4fc;border-radius:3px;border:1px solid #bee5eb">
                  <i class="fa fa-info-circle" style="color:#3498db"></i>
                  <small style="color:#2c3e50"><strong>Filter aktif:</strong>
                  <?php foreach($multi_filters as $i => $mf): ?>
                     <span class="label label-primary" style="margin:2px;padding:3px 8px;font-size:11px">
                        <?= htmlspecialchars($mf['field']); ?> <?= htmlspecialchars($mf['operator']); ?> "<?= htmlspecialchars($mf['value']); ?>"
                     </span>
                  <?php endforeach; ?>
                  </small>
               </div>
               <?php endif; ?>

               <!-- Data Table -->
               <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr>
                           <th width="30"><input type="checkbox" class="flat-red" id="check_all" name="check_all"></th>
                           <th>Nomor Program</th>
                           <th>Nama Program</th>
                           <th>Tahun Ajaran</th>
                           <th>Jenis Kegiatan</th>
                           <th>Nominal OKR</th>
                           <th>Jenjang</th>
                           <th>Status</th>
                           <th width="280">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_program_anggaran">
                     <?php if($program_anggaran_counts > 0): ?>
                     <?php foreach($program_anggarans as $program_anggaran): ?>
                        <tr>
                           <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $program_anggaran->id; ?>">
                           </td>
                           <td><?= _ent($program_anggaran->nomor_program); ?></td>
                           <td><?= _ent($program_anggaran->nama_program); ?></td>
                           <td style="text-align:center"><?= _ent($program_anggaran->tahun_ajaran); ?></td>
                           <td><?= _ent($program_anggaran->jenis_kegiatan); ?></td>
                           <td style="text-align:right">Rp <?= number_format($program_anggaran->nominal_okr, 0, ',', '.'); ?></td>
                           <td style="text-align:center">
                              <?php
                                 $jenjang_val = strtoupper($program_anggaran->jenjang);
                                 $jenjang_color = '#95a5a6';
                                 if ($jenjang_val == 'SD') $jenjang_color = '#3498db';
                                 elseif ($jenjang_val == 'SMP') $jenjang_color = '#e67e22';
                                 elseif ($jenjang_val == 'SMA') $jenjang_color = '#9b59b6';
                                 elseif ($jenjang_val == 'FT') $jenjang_color = '#1abc9c';
                              ?>
                              <span class="label" style="background:<?= $jenjang_color; ?>"><?= $jenjang_val; ?></span>
                           </td>
                           <td style="text-align:center">
                              <?php if ($program_anggaran->is_active == 1): ?>
                                 <span class="label label-aktif"><i class="fa fa-check"></i> Aktif</span>
                              <?php else: ?>
                                 <span class="label label-nonaktif"><i class="fa fa-times"></i> Non-Aktif</span>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <div style="margin-bottom:3px">
                                 <?php is_allowed('program_anggaran_view', function() use ($program_anggaran){?>
                                 <a href="<?= site_url('administrator/program_anggaran/view/' . $program_anggaran->id); ?>" class="btn btn-sm btn-info btn-aksi" title="Lihat detail">
                                    <i class="fa fa-eye"></i> Detail
                                 </a>
                                 <?php }) ?>
                                 <?php is_allowed('program_anggaran_update', function() use ($program_anggaran){?>
                                 <a href="<?= site_url('administrator/program_anggaran/edit/' . $program_anggaran->id); ?>" class="btn btn-sm btn-warning btn-aksi" title="Edit data">
                                    <i class="fa fa-edit"></i> Edit
                                 </a>
                                 <?php }) ?>
                              </div>
                              <?php is_allowed('program_anggaran_delete', function() use ($program_anggaran){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/program_anggaran/delete/' . $program_anggaran->id); ?>" class="btn btn-sm btn-danger btn-aksi-full remove-data" title="Hapus data">
                                 <i class="fa fa-trash"></i> Hapus
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="9" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">
                                 <?php if(!empty($multi_filters) || !empty($this->input->get('q'))): ?>
                                    Data tidak ditemukan untuk filter/pencarian yang dipilih
                                 <?php elseif(!empty($jenjang_context)): ?>
                                    Belum ada data program anggaran untuk jenjang <?= strtoupper($jenjang_context); ?>
                                 <?php else: ?>
                                    Data program anggaran belum tersedia
                                 <?php endif; ?>
                              </span>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <!-- Bulk Action & Pagination -->
               <div class="row" style="margin-top:15px">
                  <div class="col-md-6">
                     <div class="input-group" style="max-width:300px">
                        <select class="form-control" name="bulk" id="bulk">
                           <option value="">-- Bulk Action --</option>
                           <option value="delete">Hapus Terpilih</option>
                           <option value="not_active">Non-Aktifkan</option>
                           <option value="active">Aktifkan</option>
                        </select>
                        <span class="input-group-btn">
                           <button type="button" class="btn btn-flat btn-default" id="apply">Terapkan</button>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-6 text-right">
                     <div class="dataTables_paginate paging_simple_numbers">
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
               </form>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- ============ MODAL IMPORT =============== -->
<?php if (empty($jenjang_context)): ?>
<div class="modal fade" id="modal_add_new" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#1abc9c;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel"><i class="fa fa-upload"></i> Import Program</h3>
         </div>
         <form action="<?= base_url('administrator/program_anggaran/import/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">File</label>
                  <div class="col-xs-8">
                     <input name="file_program" class="form-control" type="file"  accept=".xls,.xlsx" required>
                     <span>*Silahkan isikan excel sesuai dengan format yang tersedia, berikut contoh format excel
                        <a class="text-danger" href="<?= base_url('uploads/example/format_program_master.xlsx'); ?>">download disini</a> </span>
                  </div>
               </div>
            </div>

            <div class="modal-footer">
               <button class="btn btn-default btn-flat" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> Tutup</button>
               <button class="btn btn-info btn-flat"><i class="fa fa-save"></i> Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<?php endif; ?>
<!--END MODAL IMPORT -->

<!-- Page script -->
<script>
// ===== FILTER BUILDER =====
<?php
   // Bangun opsi jenjang dinamis (exclude SD/SMP/SMA/FT yang dikunci jika ada context)
   $jenjang_options = array(
      array('id' => 'SD', 'text' => 'SD'),
      array('id' => 'SMP', 'text' => 'SMP'),
      array('id' => 'SMA', 'text' => 'SMA'),
      array('id' => 'FT', 'text' => 'FRANCE TRACK')
   );
   $is_active_options = array(
      array('id' => '1', 'text' => 'Aktif'),
      array('id' => '0', 'text' => 'Non-Aktif')
   );
?>
var filterFields = [
   <?php if (empty($jenjang_context)): ?>
   { value: 'jenjang', label: 'Jenjang', type: 'select_jenjang', operators: ['equals'] },
   <?php endif; ?>
   { value: 'nomor_program', label: 'Nomor Program', type: 'text', operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'nama_program', label: 'Nama Program', type: 'text', operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'tahun_ajaran', label: 'Tahun Ajaran', type: 'text', operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'jenis_kegiatan', label: 'Jenis Kegiatan', type: 'text', operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'nominal_okr', label: 'Nominal OKR', type: 'number', operators: ['equals','gt','lt','contains'] },
   { value: 'is_active', label: 'Status', type: 'select_status', operators: ['equals'] }
];
var operatorLabels = { 'contains':'Mengandung','equals':'Sama dengan','starts_with':'Diawali','ends_with':'Diakhiri','gt':'Lebih dari','lt':'Kurang dari' };
var jenjangOptions = <?= json_encode($jenjang_options); ?>;
var statusOptions = <?= json_encode($is_active_options); ?>;
var filterIndex = 0;

function getSelectOptions(f){
   if (f === 'jenjang') return jenjangOptions;
   if (f === 'is_active') return statusOptions;
   return [];
}
function getFieldType(f){
   for (var i = 0; i < filterFields.length; i++) {
      if (filterFields[i].value === f) return filterFields[i].type;
   }
   return 'text';
}
function getFieldOperators(f){
   for (var i = 0; i < filterFields.length; i++) {
      if (filterFields[i].value === f) return filterFields[i].operators;
   }
   return ['contains'];
}
function buildOperatorSelect(ops, sel){
   var h = '<select class="form-control input-sm filter-operator" style="width:130px">';
   for (var i = 0; i < ops.length; i++) {
      var o = ops[i];
      h += '<option value="' + o + '"' + (o === sel ? ' selected' : '') + '>' + operatorLabels[o] + '</option>';
   }
   h += '</select>';
   return h;
}
function buildValueInput(f, v){
   var t = getFieldType(f);
   if (t === 'text' || t === 'number') {
      var it = (t === 'number') ? 'number' : 'text';
      return '<input type="' + it + '" class="form-control input-sm filter-value" style="width:200px" placeholder="Masukkan nilai..." value="' + (v || '') + '">';
   }
   var opts = getSelectOptions(f);
   var h = '<select class="form-control input-sm filter-value" style="width:200px">';
   h += '<option value="">-- Pilih --</option>';
   for (var i = 0; i < opts.length; i++) {
      h += '<option value="' + opts[i].id + '"' + (opts[i].id === v ? ' selected' : '') + '>' + opts[i].text + '</option>';
   }
   h += '</select>';
   return h;
}
function addFilterRow(fv, op, v){
   fv = fv || ''; op = op || ''; v = v || '';
   var fsh = '<select class="form-control input-sm filter-field" style="width:180px">';
   fsh += '<option value="">-- Pilih Field --</option>';
   for (var i = 0; i < filterFields.length; i++) {
      fsh += '<option value="' + filterFields[i].value + '"' + (filterFields[i].value === fv ? ' selected' : '') + '>' + filterFields[i].label + '</option>';
   }
   fsh += '</select>';
   var ops = fv ? getFieldOperators(fv) : ['contains'];
   if (!op || ops.indexOf(op) === -1) op = ops[0];
   var h = '<div class="filter-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px">';
   h += '<input type="hidden" name="ff[]" class="filter-field-hidden" value="' + fv + '">';
   h += '<input type="hidden" name="fo[]" class="filter-operator-hidden" value="' + op + '">';
   h += '<input type="hidden" name="fv[]" class="filter-value-hidden" value="' + v + '">';
   h += '<span style="color:#3498db;font-weight:bold;font-size:12px;min-width:20px">#' + (filterIndex + 1) + '</span>';
   h += fsh;
   h += '<span class="filter-operator-container">' + buildOperatorSelect(ops, op) + '</span>';
   h += '<span class="filter-value-container">' + buildValueInput(fv, v) + '</span>';
   h += '<button type="button" class="btn btn-xs btn-danger btn-remove-filter" title="Hapus"><i class="fa fa-times"></i></button>';
   h += '</div>';
   $('#filter_rows').append(h);
   filterIndex++;
   updateFilterEmpty();
}
function updateFilterEmpty(){
   if ($('#filter_rows .filter-row').length > 0) {
      $('#filter_empty').hide();
   } else {
      $('#filter_empty').show();
   }
}
function updateFilterNumbers(){
   $('#filter_rows .filter-row').each(function(idx){
      $(this).find('span:first').text('#' + (idx + 1));
   });
}

$(document).ready(function(){
   $('#btn_add_filter').click(function(){ addFilterRow(); });
   $(document).on('click', '.btn-remove-filter', function(){
      $(this).closest('.filter-row').remove();
      updateFilterEmpty();
      updateFilterNumbers();
   });
   $(document).on('change', '.filter-field', function(){
      var r = $(this).closest('.filter-row');
      var f = $(this).val();
      r.find('.filter-field-hidden').val(f);
      var ops = f ? getFieldOperators(f) : ['contains'];
      r.find('.filter-operator-container').html(buildOperatorSelect(ops, ops[0]));
      r.find('.filter-operator-hidden').val(ops[0]);
      r.find('.filter-value-container').html(buildValueInput(f, ''));
      r.find('.filter-value-hidden').val('');
   });
   $(document).on('change', '.filter-operator', function(){
      $(this).closest('.filter-row').find('.filter-operator-hidden').val($(this).val());
   });
   $(document).on('change keyup', '.filter-value', function(){
      $(this).closest('.filter-row').find('.filter-value-hidden').val($(this).val());
   });
   <?php if(!empty($multi_filters)): ?>
      <?php foreach($multi_filters as $i => $mf): ?>
         addFilterRow('<?= addslashes($mf['field']); ?>', '<?= addslashes($mf['operator']); ?>', '<?= addslashes($mf['value']); ?>');
      <?php endforeach; ?>
   <?php endif; ?>
   updateFilterEmpty();
});
// ===== END FILTER BUILDER =====

$(document).ready(function(){

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
        },
        function(isConfirm){
          if (isConfirm) {
            document.location.href = url;
          }
        });
      return false;
   });

   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_program_anggaran').serialize();

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
          },
          function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/program_anggaran/delete?' + serialize_bulk;
            }
          });
        return false;
      }
      else if (bulk.val() == 'active') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "Ingin mengaktifkan Program Anggaran?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya",
            cancelButtonText: "Tidak",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/program_anggaran/active?' + serialize_bulk;
            }
          });
        return false;
      }
      else if (bulk.val() == 'not_active') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "Ingin menonaktifkan Program Anggaran?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya",
            cancelButtonText: "Tidak",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/program_anggaran/not_active?' + serialize_bulk;
            }
          });
        return false;
      }
      else if(bulk.val() == '')  {
          swal({
            title: "Upss",
            text: "<?= cclang('please_choose_bulk_action_first'); ?>",
            type: "warning",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Okay!",
            closeOnConfirm: true,
            closeOnCancel: true
          });
        return false;
      }
      return false;
   });

   var checkAll = $('#check_all');
   var checkboxes = $('input.check');

   checkAll.on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            checkboxes.iCheck('check');
        } else {
            checkboxes.iCheck('uncheck');
        }
   });

   checkboxes.on('ifChanged', function(event){
        if(checkboxes.filter(':checked').length == checkboxes.length) {
            checkAll.prop('checked', 'checked');
        } else {
            checkAll.removeProp('checked');
        }
        checkAll.iCheck('update');
   });

});
</script>

<!-- ===== DASHBOARD CHART (Chart.js) ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
(function(){
   if (typeof Chart === 'undefined') {
      document.write('<script src="<?= BASE_ASSET; ?>/admin-lte/plugins/chartjs/Chart.min.js"><\/script>');
   }
})();
</script>
<?php
   $dd_okr  = isset($dashboard_data['okr'])   ? (float)$dashboard_data['okr']   : 0;
   $dd_aj   = isset($dashboard_data['ajuan']) ? (float)$dashboard_data['ajuan'] : 0;
   $dd_cair = isset($dashboard_data['cair'])  ? (float)$dashboard_data['cair']  : 0;
   $has_chart_data = ($dd_okr + $dd_aj + $dd_cair) > 0;
?>
<?php if ($has_chart_data): ?>
<script>
(function(){
   var canvas = document.getElementById('chart_okr');
   if (!canvas || typeof Chart === 'undefined') { return; }

   var fmtRp = function(n) {
      var s = Math.round(n).toString();
      return 'Rp ' + s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
   };

   var data = {
      labels: [
         '<?= cclang("dashboard_okr_terkumpul"); ?>',
         '<?= cclang("dashboard_okr_diajukan"); ?>',
         '<?= cclang("dashboard_okr_dicairkan"); ?>'
      ],
      datasets: [{
         label: '<?= htmlspecialchars(!empty($dashboard_ta) ? $dashboard_ta : "-"); ?>',
         data: [<?= $dd_okr; ?>, <?= $dd_aj; ?>, <?= $dd_cair; ?>],
         backgroundColor: ['#3c8dbc', '#f39c12', '#27ae60'],
         borderColor:     ['#2c6e96', '#c87f0a', '#1e8449'],
         borderWidth: 1
      }]
   };

   var options = {
      responsive: true,
      maintainAspectRatio: true,
      legend: { display: false },
      tooltips: {
         callbacks: {
            label: function(tooltipItem) {
               return fmtRp(tooltipItem.yLabel);
            }
         }
      },
      scales: {
         yAxes: [{
            ticks: {
               beginAtZero: true,
               callback: function(value) {
                  var s = Math.round(value).toString();
                  return s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
               }
            }
         }]
      }
   };

   new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: data,
      options: options
   });
})();
</script>
<?php endif; ?>

<!-- ===== DASHBOARD CHART TOGGLE ===== -->
<script>
(function(){
   var KEY = 'program_anggaran_chart_visible';
   var area = document.getElementById('dashboard_chart_canvas_area');
   var btn  = document.getElementById('btn_toggle_chart');
   var ic   = document.getElementById('icon_toggle_chart');
   var tx   = document.getElementById('text_toggle_chart');
   if (!area || !btn || !ic || !tx) { return; }

   function hasClass(el, name) {
      return (' ' + el.className + ' ').indexOf(' ' + name + ' ') !== -1;
   }
   function addClass(el, name) {
      if (!hasClass(el, name)) { el.className = el.className + ' ' + name; }
   }
   function removeClass(el, name) {
      el.className = (' ' + el.className + ' ').replace(' ' + name + ' ', ' ').replace(/^\s+|\s+$/g, '');
   }

   function apply(state){
      if (state === '0') {
         addClass(area, 'chart-hidden');
         ic.className = 'fa fa-eye';
         tx.textContent = 'Tampilkan Chart';
      } else {
         removeClass(area, 'chart-hidden');
         ic.className = 'fa fa-eye-slash';
         tx.textContent = 'Sembunyikan Chart';
      }
   }

   var stored = null;
   try { stored = window.localStorage.getItem(KEY); } catch(e) { /* localStorage unavailable */ }
   apply(stored === '0' ? '0' : '1');

   btn.addEventListener('click', function(){
      var newState = hasClass(area, 'chart-hidden') ? '1' : '0';
      apply(newState);
      try { window.localStorage.setItem(KEY, newState); } catch(e) { /* ignore */ }
      if (newState === '1' && typeof jQuery !== 'undefined') {
         setTimeout(function() { jQuery(window).trigger('resize'); }, 450);
      }
   });
})();
</script>
<!-- ===== END DASHBOARD CHART ===== -->