<link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.css">
<style>
/* Top Buttons */
.btn-top { margin-right: 5px; border-radius: 3px; }

/* Table Styling */
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; text-align: center; }
.table td { font-size: 13px; vertical-align: middle; }

/* Status Labels */
.label-hadir { background: #27ae60; }
.label-sakit { background: #f39c12; }
.label-izin { background: #3498db; }
.label-alpa { background: #e74c3c; }

/* Action Buttons */
.btn-aksi { margin: 2px 1px; padding: 4px 0; font-size: 11px; border-radius: 3px; width: 48%; display: inline-block; text-align: center; }
.btn-aksi i { margin-right: 3px; }
.btn-aksi-full { margin: 2px 1px; padding: 4px 0; font-size: 11px; border-radius: 3px; width: 98%; display: block; text-align: center; }
.btn-aksi-full i { margin-right: 3px; }

/* Filter Section */
.filter-builder { margin-bottom: 15px; padding: 12px; background: #f8f9fa; border-radius: 5px; border: 1px solid #e0e0e0; }

/* Predikat Badge */
.badge-predikat { padding: 3px 8px; border-radius: 3px; font-weight: bold; font-size: 11px; color: #fff; }
.badge-a { background: #27ae60; }
.badge-b { background: #3498db; }
.badge-c { background: #f39c12; }
.badge-d { background: #e67e22; }
.badge-e { background: #e74c3c; }

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

.bg-grad-blue { background: linear-gradient(135deg, #3c8dbc 0%, #367fa9 100%); }
.bg-grad-green { background: linear-gradient(135deg, #00a65a 0%, #008d4c 100%); }
.bg-grad-red { background: linear-gradient(135deg, #dd4b39 0%, #c0432f 100%); }
.bg-grad-yellow { background: linear-gradient(135deg, #f39c12 0%, #db8b0a 100%); }

/* Chart Container */
.chart-container { margin-bottom: 20px; padding: 15px; background: #fff; border: 1px solid #e0e0e0; border-radius: 4px; }
.chart-container h4 { margin: 0 0 15px 0; font-size: 15px; cursor: pointer; }
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
   <h1><i class="fa fa-book"></i> <?= cclang('presensi_tadarus') ?> <small><?= cclang('list_all'); ?></small></h1>
   <ol class="breadcrumb"><li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li><li class="active"><?= cclang('presensi_tadarus') ?></li></ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">

            <!-- Box Header -->
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-graduation-cap"></i> Data Presensi Tadarus
                  <span class="label bg-yellow" style="margin-left:10px"><?= $presensi_tadarus_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('presensi_tadarus_add', function () { ?>
                  <a class="btn btn-sm btn-success btn-top" title="Tambah Presensi Tadarus (Ctrl+a)" href="<?= site_url('administrator/presensi_tadarus/' . (!empty($is_locked) ? $url_method : 'index') . '/add'); ?>">
                     <i class="fa fa-plus"></i> Tambah
                  </a>
                  <?php }) ?>
                  <?php is_allowed('presensi_tadarus_export', function(){?>
                  <a class="btn btn-sm btn-success btn-top" title="Export XLS" href="javascript:void(0)" onclick="exportData('xls')">
                     <i class="fa fa-file-excel-o"></i> XLS
                  </a>
                  <a class="btn btn-sm btn-warning btn-top" title="Export PDF" href="javascript:void(0)" onclick="exportData('pdf')">
                     <i class="fa fa-file-pdf-o"></i> PDF
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">

               <?php if (!empty($is_locked)): ?>
               <!-- Lock Indicator -->
               <div style="margin-bottom:15px;padding:10px 15px;background:linear-gradient(135deg,#f39c12,#e67e22);color:#fff;border-radius:4px;box-shadow:0 2px 4px rgba(0,0,0,0.08)">
                  <i class="fa fa-lock" style="margin-right:8px"></i>
                  <strong>Terkunci pada Jenjang <?= htmlspecialchars($lock_label); ?></strong>
                  <span style="margin-left:10px;opacity:0.9">— Data dan operasi hanya untuk jenjang ini.</span>
                  <?php if (!empty($is_admin)): ?>
                  <a href="<?= base_url('administrator/presensi_tadarus'); ?>" style="margin-left:10px;color:#fff;text-decoration:underline">Lihat semua jenjang</a>
                  <?php endif; ?>
               </div>
               <?php endif; ?>

               <!-- 1. Info Boxes (TOP) -->
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-blue">
                        <i class="fa fa-database info-icon"></i>
                        <div>
                           <span class="info-label">Total Data</span>
                           <span class="info-value"><?= number_format($summary['total_data']); ?></span>
                           <span class="info-detail">Seluruh data presensi</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-green">
                        <i class="fa fa-check-circle info-icon"></i>
                        <div>
                           <span class="info-label">Hadir</span>
                           <span class="info-value"><?= number_format($summary['total_hadir']); ?></span>
                           <span class="info-detail">Siswa hadir tadarus</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-red">
                        <i class="fa fa-times-circle info-icon"></i>
                        <div>
                           <span class="info-label">Tidak Hadir</span>
                           <span class="info-value"><?= number_format($summary['total_sakit'] + $summary['total_izin'] + $summary['total_alpa']); ?></span>
                           <span class="info-detail">Sakit: <?= $summary['total_sakit']; ?> | Izin: <?= $summary['total_izin']; ?> | Alfa: <?= $summary['total_alpa']; ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-yellow">
                        <i class="fa fa-star info-icon"></i>
                        <div>
                           <span class="info-label">Rata-rata Nilai</span>
                           <span class="info-value"><?= number_format($summary['rata_rata_nilai'], 1); ?></span>
                           <?php $total = $summary['rata_rata_nilai'];
                           if ($total >= 90) { $p = 'A - Sangat Baik'; }
                           elseif ($total >= 80) { $p = 'B - Baik'; }
                           elseif ($total >= 70) { $p = 'C - Cukup'; }
                           elseif ($total >= 60) { $p = 'D - Perlu Bimbingan'; }
                           else { $p = 'E - Perlu Pembinaan'; } ?>
                           <span class="info-detail">Predikat: <?= $p ?></span>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- 2. Filter & Search (ABOVE TABLE) -->
               <form name="form_presensi_tadarus" id="form_presensi_tadarus" action="<?= base_url('administrator/presensi_tadarus/' . (!empty($is_locked) ? $url_method : 'index')); ?>">
               <div class="filter-builder">
                  <div style="display:flex;align-items:center;margin-bottom:10px">
                     <i class="fa fa-filter" style="color:#3498db;margin-right:8px"></i>
                     <strong style="color:#2c3e50;font-size:14px">Filter & Pencarian</strong>
                  </div>

                  <div class="row">
                     <div class="col-md-2">
                        <div class="form-group" style="margin-bottom:8px">
                           <label style="font-size:12px;color:#555">Jenjang</label>
                           <?php if (!empty($is_locked) && count($list_jenjang) == 1): ?>
                              <input type="text" class="form-control" value="<?= $list_jenjang[0]['label']; ?>" disabled>
                              <input type="hidden" name="jenjang" value="<?= $list_jenjang[0]['code']; ?>">
                           <?php else: ?>
                              <select class="form-control chosen chosen-select" name="jenjang" id="filter_jenjang">
                                 <option value="">Semua (lock)</option>
                                 <?php foreach ($list_jenjang as $j): ?>
                                 <option value="<?= $j['code']; ?>" <?= (!empty($filters['jenjang']) && $filters['jenjang'] == $j['code']) ? 'selected' : ''; ?>><?= $j['label']; ?></option>
                                 <?php endforeach; ?>
                              </select>
                           <?php endif; ?>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group" style="margin-bottom:8px">
                           <label style="font-size:12px;color:#555">Tingkatan</label>
                           <select class="form-control chosen chosen-select" name="tingkatan" id="filter_tingkatan">
                              <option value="">Semua Tingkatan</option>
                              <?php if (!empty($list_tingkatan)): ?>
                              <?php foreach ($list_tingkatan as $t): ?>
                              <option value="<?= $t->id; ?>" data-jenjang="<?= $t->jenjang; ?>" <?= (!empty($filters['id_tingkatan']) && $filters['id_tingkatan'] == $t->id) ? 'selected' : ''; ?>><?= $t->label; ?> (<?= strtoupper($t->jenjang); ?>)</option>
                              <?php endforeach; ?>
                              <?php endif; ?>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group" style="margin-bottom:8px">
                           <label style="font-size:12px;color:#555">Kelas</label>
                           <select class="form-control chosen chosen-select" name="kelas" id="filter_kelas">
                              <option value="">Semua Kelas</option>
                              <?php if (!empty($list_kelas)): ?>
                              <?php foreach ($list_kelas as $k): ?>
                              <option value="<?= $k->label; ?>" data-tingkatan="<?= $k->id_tingkatan; ?>" data-jenjang="<?= $k->jenjang; ?>" <?= (!empty($filters['kelas']) && $filters['kelas'] == $k->label) ? 'selected' : ''; ?>><?= $k->label; ?> (<?= strtoupper($k->jenjang); ?>)</option>
                              <?php endforeach; ?>
                              <?php endif; ?>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group" style="margin-bottom:8px">
                           <label style="font-size:12px;color:#555">Tanggal Mulai</label>
                           <input type="text" class="form-control datepicker" name="start_date" id="filter_start_date" value="<?= !empty($filters['start_date']) ? $filters['start_date'] : ''; ?>" placeholder="YYYY-MM-DD">
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group" style="margin-bottom:8px">
                           <label style="font-size:12px;color:#555">Tanggal Akhir</label>
                           <input type="text" class="form-control datepicker" name="end_date" id="filter_end_date" value="<?= !empty($filters['end_date']) ? $filters['end_date'] : ''; ?>" placeholder="YYYY-MM-DD">
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group" style="margin-bottom:8px">
                           <label style="font-size:12px;color:#555">Status</label>
                           <select class="form-control chosen chosen-select" name="status_hadir" id="filter_status">
                              <option value="">Semua Status</option>
                              <option value="Hadir" <?= (!empty($filters['status_hadir']) && $filters['status_hadir'] == 'Hadir') ? 'selected' : ''; ?>>Hadir</option>
                              <option value="Sakit" <?= (!empty($filters['status_hadir']) && $filters['status_hadir'] == 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                              <option value="Izin" <?= (!empty($filters['status_hadir']) && $filters['status_hadir'] == 'Izin') ? 'selected' : ''; ?>>Izin</option>
                              <option value="Alfa" <?= (!empty($filters['status_hadir']) && $filters['status_hadir'] == 'Alfa') ? 'selected' : ''; ?>>Alfa</option>
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-4">
                        <div class="input-group">
                           <input type="text" class="form-control" name="q" id="filter" placeholder="Cari nama, NIS, kelas..." value="<?= !empty($filters['q']) ? htmlspecialchars($filters['q']) : ''; ?>">
                           <span class="input-group-btn">
                              <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Cari</button>
                              <?php if(!empty($filters)): ?>
                              <a class="btn btn-flat btn-default" href="<?= base_url('administrator/presensi_tadarus/' . (!empty($is_locked) ? $url_method : 'index')); ?>" title="Reset filter"><i class="fa fa-times"></i></a>
                              <?php endif; ?>
                           </span>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- 3. Chart (ABOVE TABLE, DEFAULT CLOSED) -->
               <div class="chart-container">
                  <h4 id="chartToggle" style="cursor:pointer">
                     <i class="fa fa-bar-chart"></i> Chart Mingguan Tadarus (8 Minggu Terakhir)
                     <i class="fa fa-chevron-down" id="chartIcon" style="font-size:12px;margin-left:5px"></i>
                  </h4>
                  <div id="chartBody" style="display:none">
                     <canvas id="chartTadarus" width="100%" height="30"></canvas>
                  </div>
               </div>

               <!-- 4. Data Table (BOTTOM) -->
               <div class="table-responsive"> 
                  <table class="table table-bordered table-striped table-hover dataTable">
                     <thead>
                        <tr>
                           <th width="30">
                              <input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all">
                           </th>
                           <th>Tanggal</th>
                           <th>Nama Siswa</th>
                           <th>Kelas</th>
                           <th>Jenjang</th>
                           <th>Status</th>
                           <th>Total</th>
                           <th>Predikat</th>
                           <th width="280">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_presensi_tadarus">
                     <?php if($presensi_tadarus_counts > 0): ?>
                     <?php foreach($presensi_tadaruss as $pt): 
                        $total = (float) $pt->total_nilai;
                        if ($total >= 90) { $pred = 'A'; $desc = 'Sangat Baik'; $badge = 'badge-a'; }
                        elseif ($total >= 80) { $pred = 'B'; $desc = 'Baik'; $badge = 'badge-b'; }
                        elseif ($total >= 70) { $pred = 'C'; $desc = 'Cukup'; $badge = 'badge-c'; }
                        elseif ($total >= 60) { $pred = 'D'; $desc = 'Perlu Bimbingan'; $badge = 'badge-d'; }
                        else { $pred = 'E'; $desc = 'Perlu Pembinaan Intensif'; $badge = 'badge-e'; }
                     ?>
                        <tr>
                           <td>
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $pt->id_presensi_tadarus; ?>">
                           </td>
                           <td style="white-space:nowrap"><?= _ent($pt->tanggal); ?></td>
                           <td>
                              <i class="fa fa-user"></i> <?= _ent($pt->nama_lengkap); ?>
                           </td>
                           <td style="text-align:center"><?= _ent($pt->kelas); ?></td>
                           <td style="text-align:center"><span class="label label-info"><?= _ent($pt->jenjang); ?></span></td>
                           <td style="text-align:center">
                              <?php if ($pt->status_hadir == 'Hadir'): ?>
                              <span class="label label-hadir"><?= _ent($pt->status_hadir); ?></span>
                              <?php elseif ($pt->status_hadir == 'Sakit'): ?>
                              <span class="label label-sakit"><?= _ent($pt->status_hadir); ?></span>
                              <?php elseif ($pt->status_hadir == 'Izin'): ?>
                              <span class="label label-izin"><?= _ent($pt->status_hadir); ?></span>
                              <?php else: ?>
                              <span class="label label-alpa"><?= _ent($pt->status_hadir); ?></span>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center"><strong><?= _ent($pt->total_nilai); ?></strong></td>
                           <td style="text-align:center">
                              <span class="badge-predikat <?= $badge; ?>" title="<?= $desc; ?>"><?= $pred; ?></span>
                              <br><small class="text-muted"><?= $desc; ?></small>
                           </td>
                           <td style="text-align:center">
                              <div style="margin-bottom:3px">
                                 <?php is_allowed('presensi_tadarus_view', function() use ($pt){?>
                                 <a href="javascript:void(0);" class="btn btn-sm btn-info btn-aksi btn-detail-modal" data-id="<?= $pt->id_presensi_tadarus; ?>" title="Lihat detail">
                                    <i class="fa fa-eye"></i> Detail
                                 </a>
                                 <?php }) ?>
                              </div>
                              <div style="margin-bottom:3px">
                                 <?php is_allowed('presensi_tadarus_update', function() use ($pt){?>
                                 <a href="<?= site_url('administrator/presensi_tadarus/edit/' . $pt->id_presensi_tadarus); ?>" class="btn btn-sm btn-success btn-aksi" title="Edit">
                                    <i class="fa fa-edit"></i> Edit
                                 </a>
                                 <?php }) ?>
                                 <?php is_allowed('presensi_tadarus_delete', function() use ($pt){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/presensi_tadarus/' . (!empty($is_locked) ? $url_method : 'index') . '/delete/' . $pt->id_presensi_tadarus); ?>" class="btn btn-sm btn-danger btn-aksi remove-data" title="Hapus">
                                    <i class="fa fa-trash"></i> Hapus
                                 </a>
                                 <?php }) ?>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="9" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">
                                 <?php if(!empty($filters)): ?>
                                    Data presensi tadarus tidak ditemukan untuk filter yang dipilih
                                 <?php else: ?>
                                    Data presensi tadarus belum tersedia
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

<!-- Modal: Filter Required -->
<div class="modal fade" id="modalFilterRequired" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content" style="border-radius:8px;overflow:hidden">
         <div class="modal-header" style="background:linear-gradient(135deg,#f39c12,#e67e22);color:#fff;border:none">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8"><span>&times;</span></button>
            <h4 class="modal-title"><i class="fa fa-filter"></i> Filter Diperlukan</h4>
         </div>
         <div class="modal-body text-center" style="padding:30px 20px">
            <div style="margin-bottom:15px">
               <i class="fa fa-exclamation-circle" style="font-size:60px;color:#f39c12"></i>
            </div>
            <h5 style="color:#2c3e50;margin-bottom:10px;font-weight:600">Data Terlalu Besar</h5>
            <p style="color:#7f8c8d;font-size:13px;margin-bottom:0">Silakan pilih minimal satu filter (tanggal, jenjang, kelas, atau kata kunci) sebelum mengekspor data.</p>
         </div>
         <div class="modal-footer" style="border:none;padding:10px 20px 20px">
            <button type="button" class="btn btn-warning btn-block" data-dismiss="modal" style="border-radius:4px;font-weight:600">
               <i class="fa fa-check"></i> Mengerti
            </button>
         </div>
      </div>
   </div>
</div>

<!-- Modal: Detail Presensi Tadarus -->
<div class="modal fade" id="modalDetailPresensi" tabindex="-1" role="dialog" aria-labelledby="modalDetailPresensiLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document" style="width:90%;max-width:1000px;margin-top:30px">
      <div class="modal-content" style="border-radius:8px;overflow:hidden;border:none;box-shadow:0 10px 40px rgba(0,0,0,0.2)">
         <div class="modal-header" style="background:linear-gradient(135deg,#3c8dbc,#367fa9);color:#fff;border:none;padding:15px 20px">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:0.8;font-size:24px"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalDetailPresensiLabel" style="margin:0;font-weight:600"><i class="fa fa-eye" style="margin-right:8px"></i>Detail Presensi Tadarus</h4>
         </div>
         <div class="modal-body" style="padding:20px;max-height:70vh;overflow-y:auto">
            <!-- Content loaded via AJAX -->
            <div class="text-center" style="padding:40px 20px">
               <i class="fa fa-spinner fa-spin fa-3x" style="color:#3498db"></i>
               <p class="text-muted" style="margin-top:15px">Memuat detail presensi...</p>
            </div>
         </div>
         <div class="modal-footer" style="border:none;padding:15px 20px;background:#f8f9fa">
            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius:4px"><i class="fa fa-times"></i> Tutup</button>
         </div>
      </div>
   </div>
</div>

<!-- Page script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
$(document).ready(function(){

   // Chart Toggle (default closed)
   $('#chartToggle').click(function(){
      $('#chartBody').slideToggle(200);
      var icon = $('#chartIcon');
      icon.toggleClass('fa-chevron-down fa-chevron-up');
   });

   // Cascade filter: Jenjang -> Tingkatan
   $('#filter_jenjang').change(function(){
      var jenjang = $(this).val().toLowerCase();
      var $tingkatan = $('#filter_tingkatan');
      var $kelas = $('#filter_kelas');

      $tingkatan.html('<option value="">Semua Tingkatan</option>');
      $kelas.html('<option value="">Semua Kelas</option>');

      if (!jenjang) {
         <?php if (!empty($list_tingkatan)): ?>
         <?php foreach ($list_tingkatan as $t): ?>
         $tingkatan.append('<option value="<?= $t->id; ?>" data-jenjang="<?= $t->jenjang; ?>"><?= $t->label; ?> (<?= strtoupper($t->jenjang); ?>)</option>');
         <?php endforeach; ?>
         <?php endif; ?>
         <?php if (!empty($list_kelas)): ?>
         <?php foreach ($list_kelas as $k): ?>
         $kelas.append('<option value="<?= $k->label; ?>" data-tingkatan="<?= $k->id_tingkatan; ?>" data-jenjang="<?= $k->jenjang; ?>"><?= $k->label; ?> (<?= strtoupper($k->jenjang); ?>)</option>');
         <?php endforeach; ?>
         <?php endif; ?>
         $tingkatan.trigger('chosen:updated');
         $kelas.trigger('chosen:updated');
         return;
      }

      $.get(BASE_URL + '/administrator/presensi_tadarus/get_tingkatan', {jenjang: jenjang}, function(data){
         var items = JSON.parse(data);
         $.each(items, function(i, item){
            $tingkatan.append('<option value="' + item.id + '">' + item.label + '</option>');
         });
         $tingkatan.trigger('chosen:updated');
      });
   });

   // Cascade: Tingkatan -> Kelas
   $('#filter_tingkatan').change(function(){
      var id_tingkatan = $(this).val();
      var jenjang_val = $('#filter_jenjang');
      var jenjang = jenjang_val.length ? jenjang_val.val().toLowerCase() : '';
      var $kelas = $('#filter_kelas');

      $kelas.html('<option value="">Semua Kelas</option>');

      // If jenjang hidden (locked), find first allowed from list_jenjang
      if (!jenjang) {
         <?php if (!empty($is_locked) && !empty($list_jenjang)): ?>
         jenjang = '<?= strtolower($list_jenjang[0]['code']); ?>';
         <?php endif; ?>
      }

      if (!jenjang) {
         <?php if (!empty($list_kelas)): ?>
         <?php foreach ($list_kelas as $k): ?>
         $kelas.append('<option value="<?= $k->label; ?>" data-tingkatan="<?= $k->id_tingkatan; ?>" data-jenjang="<?= $k->jenjang; ?>"><?= $k->label; ?> (<?= strtoupper($k->jenjang); ?>)</option>');
         <?php endforeach; ?>
         <?php endif; ?>
         $kelas.trigger('chosen:updated');
         return;
      }

      $.get(BASE_URL + '/administrator/presensi_tadarus/get_kelas', {jenjang: jenjang, id_tingkatan: id_tingkatan}, function(data){
         var items = JSON.parse(data);
         $.each(items, function(i, item){
            $kelas.append('<option value="' + item.label + '">' + item.label + '</option>');
         });
         $kelas.trigger('chosen:updated');
      });
   });

   // Delete single
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
         if (isConfirm) {
            document.location.href = url;            
         }
      });
      return false;
   });

   // Bulk action
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_presensi_tadarus').serialize();

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
               document.location.href = BASE_URL + '/administrator/presensi_tadarus/<?= !empty($is_locked) ? $url_method : 'index'; ?>/delete?' + serialize_bulk;
            }
         });
         return false;
      } else if(bulk.val() == '') {
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

   // Check all
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

   // Chart
   var ctx = document.getElementById('chartTadarus').getContext('2d');
   var chartData = <?= json_encode(!empty($chart_data) ? $chart_data : array()); ?>;
   
   var labels = [];
   var hadirData = [];
   var nilaiData = [];

   if (chartData.length > 0) {
      for (var i = 0; i < chartData.length; i++) {
         labels.push(chartData[i].tanggal_presensi);
         hadirData.push(parseInt(chartData[i].total_hadir) || 0);
         nilaiData.push(parseFloat(chartData[i].rata_rata_nilai) || 0);
      }
   } else {
      labels.push('Belum ada data');
      hadirData.push(0);
      nilaiData.push(0);
   }

   new Chart(ctx, {
      type: 'bar',
      data: {
         labels: labels,
         datasets: [
            {
               label: 'Jumlah Hadir',
               type: 'bar',
               backgroundColor: 'rgba(39, 174, 96, 0.6)',
               borderColor: '#27ae60',
               borderWidth: 1,
               data: hadirData,
               yAxisID: 'y-hadir'
            },
            {
               label: 'Rata-rata Nilai',
               type: 'line',
               borderColor: '#3498db',
               backgroundColor: 'rgba(52, 152, 219, 0.1)',
               borderWidth: 2,
               pointRadius: 4,
               pointBackgroundColor: '#3498db',
               fill: false,
               data: nilaiData,
               yAxisID: 'y-nilai'
            }
         ]
      },
      options: {
         responsive: true,
         legend: { position: 'top' },
         scales: {
            yAxes: [
               {
                  id: 'y-hadir',
                  type: 'linear',
                  position: 'left',
                  ticks: { beginAtZero: true, stepSize: 1 },
                  scaleLabel: { display: true, labelString: 'Jumlah Hadir' }
               },
               {
                  id: 'y-nilai',
                  type: 'linear',
                  position: 'right',
                  ticks: { beginAtZero: true, max: 100 },
                  gridLines: { drawOnChartArea: false },
                  scaleLabel: { display: true, labelString: 'Rata-rata Nilai' }
               }
            ]
         }
      }
   });

}); /*end doc ready*/

// ============================================================
// DETAIL MODAL FUNCTIONALITY
// ============================================================

$(document).on('click', '.btn-detail-modal', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var $modal = $('#modalDetailPresensi');
    $modal.find('.modal-body').html(
        '<div class="text-center" style="padding:40px 20px">' +
        '<i class="fa fa-spinner fa-spin fa-3x" style="color:#3498db"></i>' +
        '<p class="text-muted">Memuat detail presensi...</p></div>'
    );
    $modal.modal('show');
    $.get(BASE_URL + '/administrator/presensi_tadarus/get_detail/' + id, function(response) {
        if (response.success && response.data) {
            renderDetailModal(response.data, $modal);
        } else {
            $modal.find('.modal-body').html(
                '<div class="text-center" style="padding:40px 20px">' +
                '<i class="fa fa-exclamation-triangle fa-3x" style="color:#e74c3c"></i>' +
                '<p class="text-danger">' + (response.message || 'Data tidak ditemukan') + '</p></div>'
            );
        }
    }).fail(function() {
        $modal.find('.modal-body').html(
            '<div class="text-center" style="padding:40px 20px">' +
            '<i class="fa fa-exclamation-triangle fa-3x" style="color:#e74c3c"></i>' +
            '<p class="text-danger">Gagal memuat data.</p></div>'
        );
    });
});

function renderDetailModal(data, $modal) {
    var statusClass = 'label-default', statusLabel = data.status_hadir;
    switch (data.status_hadir.toLowerCase()) {
        case 'hadir': statusClass = 'label-success'; break;
        case 'sakit': statusClass = 'label-warning'; break;
        case 'izin': statusClass = 'label-info'; break;
        case 'alpa': case 'tidak hadir': statusClass = 'label-danger'; break;
    }
    var total = parseFloat(data.total_nilai) || 0;
    var predikat = '', predikatDesc = '', predikatClass = '';
    if (total >= 90) { predikat = 'A'; predikatDesc = 'Sangat Baik'; predikatClass = 'badge-a'; }
    else if (total >= 80) { predikat = 'B'; predikatDesc = 'Baik'; predikatClass = 'badge-b'; }
    else if (total >= 70) { predikat = 'C'; predikatDesc = 'Cukup'; predikatClass = 'badge-c'; }
    else if (total >= 60) { predikat = 'D'; predikatDesc = 'Perlu Bimbingan'; predikatClass = 'badge-d'; }
    else { predikat = 'E'; predikatDesc = 'Perlu Pembinaan Intensif'; predikatClass = 'badge-e'; }

    var html =
        '<div class="row" style="margin-bottom:15px">' +
        '  <div class="col-md-4">' +
        '    <div class="info-box-custom bg-grad-blue">' +
        '      <i class="fa fa-calendar info-icon"></i>' +
        '      <div>' +
        '        <span class="info-label">Tanggal</span>' +
        '        <span class="info-value">' + formatDateIndo(data.tanggal) + '</span>' +
        '        <span class="info-detail">' + esc(data.hari) + '</span></div></div></div>' +
        '  <div class="col-md-4">' +
        '    <div class="info-box-custom bg-grad-green">' +
        '      <i class="fa fa-user info-icon"></i>' +
        '      <div>' +
        '        <span class="info-label">Siswa</span>' +
        '        <span class="info-value">' + esc(data.nama_lengkap) + '</span>' +
        '        <span class="info-detail">NIS: ' + esc(data.nis || '-') + '</span></div></div></div>' +
        '  <div class="col-md-4">' +
        '    <div class="info-box-custom bg-grad-yellow">' +
        '      <i class="fa fa-graduation-cap info-icon"></i>' +
        '      <div>' +
        '        <span class="info-label">Kelas / Jenjang</span>' +
        '        <span class="info-value">' + esc(data.kelas) + '</span>' +
        '        <span class="info-detail">' + esc(data.jenjang) + '</span></div></div></div>' +
        '</div>' +
        '<div class="row" style="margin-bottom:15px">' +
        '  <div class="col-md-6">' +
        '    <div class="box box-solid box-primary">' +
        '      <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-check-circle"></i> Status Kehadiran</h3></div>' +
        '      <div class="box-body text-center"><span class="label ' + statusClass + '" style="font-size:16px;padding:10px 20px">' + esc(statusLabel) + '</span></div></div></div>' +
        '  <div class="col-md-6">' +
        '    <div class="box box-solid box-info">' +
        '      <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-star"></i> Predikat Nilai</h3></div>' +
        '      <div class="box-body text-center"><span class="badge-predikat ' + predikatClass + '" style="font-size:14px;padding:8px 16px">' + predikat + '</span>' +
        '        <br><br><strong style="font-size:18px">Total Nilai: ' + total + '</strong>' +
        '        <br><small class="text-muted">' + predikatDesc + '</small></div></div></div></div>' +
        '<div class="box box-solid">' +
        '  <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-list-alt"></i> Detail Penilaian Tadarus</h3></div>' +
        '  <div class="box-body table-responsive no-padding">' +
        '    <table class="table table-bordered table-striped mb-0">' +
        '      <thead style="background:#f8f9fa"><tr>' +
        '        <th style="width:40%">Aspek Penilaian</th>' +
        '        <th style="width:20%" class="text-center">Nilai (1-4)</th>' +
        '        <th style="width:40%">Keterangan</th></tr></thead>' +
        '      <tbody>' +
        '        <tr><td><strong>Kehadiran</strong></td><td class="text-center"><strong>' + esc(data.kehadiran) + '</strong></td><td>' + nilaiDesc(data.kehadiran) + '</td></tr>' +
        '        <tr><td><strong>Kelengkapan</strong></td><td class="text-center"><strong>' + esc(data.kelengkapan) + '</strong></td><td>' + nilaiDesc(data.kelengkapan) + '</td></tr>' +
        '        <tr><td><strong>Adab</strong></td><td class="text-center"><strong>' + esc(data.adab) + '</strong></td><td>' + nilaiDesc(data.adab) + '</td></tr>' +
        '        <tr><td><strong>Keaktifan</strong></td><td class="text-center"><strong>' + esc(data.keaktifan) + '</strong></td><td>' + nilaiDesc(data.keaktifan) + '</td></tr>' +
        '      </tbody></table></div></div>' +
        '<div class="row" style="margin-top:15px">' +
        '  <div class="col-md-6">' +
        '    <div class="box box-solid box-default">' +
        '      <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-clock-o"></i> Informasi Waktu</h3></div>' +
        '      <div class="box-body">' +
        '        <table class="table table-bordered mb-0">' +
        '          <tr><th style="width:40%">Dibuat</th><td>' + esc(data.created_at) + '</td></tr>' +
        '          <tr><th>Diupdate</th><td>' + esc(data.updated_at) + '</td></tr>' +
        '          <tr><th>Oleh</th><td>' + esc(data.updated_by) + '</td></tr>' +
        '        </table></div></div></div>' +
        '  <div class="col-md-6">' +
        '    <div class="box box-solid box-default">' +
        '      <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-cogs"></i> Aksi</h3></div>' +
        '      <div class="box-body text-center" style="padding:20px">' +
        '        <?php is_allowed("presensi_tadarus_update", function() { ?>' +
        '        <a href="<?= site_url("administrator/presensi_tadarus/edit/") ?>' + data.id_presensi_tadarus + '" class="btn btn-sm btn-success" style="margin-right:5px" target="_blank">' +
        '          <i class="fa fa-edit"></i> Edit</a><?php }) ?>' +
        '        <a href="<?= site_url("administrator/presensi_tadarus/single_pdf/") ?>' + data.id_presensi_tadarus + '" class="btn btn-sm btn-warning" target="_blank">' +
        '          <i class="fa fa-file-pdf-o"></i> PDF</a></div></div></div></div>';
    $modal.find('.modal-body').html(html);
}

function nilaiDesc(nilai) {
    var n = parseInt(nilai);
    if (isNaN(n)) return '<span class="text-muted">-</span>';
    var d = {1:'Kurang',2:'Cukup',3:'Baik',4:'Sangat Baik'},
        c = {1:'#e74c3c',2:'#f39c12',3:'#3498db',4:'#27ae60'}[n] || '#7f8c8d';
    return '<span style="color:' + c + ';font-weight:600">' + (d[n] || '-') + '</span>';
}

function formatDateIndo(dateStr) {
    if (!dateStr) return '-';
    var m = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],
        d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return d.getDate() + ' ' + m[d.getMonth()] + ' ' + d.getFullYear();
}

function esc(t) {
    if (t === null || t === undefined) return '';
    var m = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
    return t.toString().replace(/[&<>"']/g, function(x) { return m[x]; });
}

/**
 * Export data with filter validation.
 * Checks if at least one filter is set before exporting.
 */
function exportData(type) {
   // Get form elements directly
   var start_date = document.getElementById('filter_start_date');
   var end_date = document.getElementById('filter_end_date');
   var jenjang = document.getElementById('filter_jenjang');
   var tingkatan = document.getElementById('filter_tingkatan');
   var kelas = document.getElementById('filter_kelas');
   var status = document.getElementById('filter_status');
   var q = document.getElementById('filter');

   var hasFilter = false;

   // Check regular select/text inputs
   if (start_date && start_date.value.trim() !== '') hasFilter = true;
   if (end_date && end_date.value.trim() !== '') hasFilter = true;
   if (tingkatan && tingkatan.value !== '') hasFilter = true;
   if (kelas && kelas.value !== '') hasFilter = true;
   if (status && status.value !== '') hasFilter = true;
   if (q && q.value.trim() !== '') hasFilter = true;

   // Check jenjang select (only if it's a select, not hidden input)
   if (jenjang && jenjang.tagName === 'SELECT' && jenjang.value !== '') {
      hasFilter = true;
   }

   // Check hidden jenjang field (for locked single jenjang)
   var hiddenJenjang = document.querySelector('#form_presensi_tadarus input[name="jenjang"][type="hidden"]');
   if (hiddenJenjang && hiddenJenjang.value) {
      hasFilter = true;
   }

   if (!hasFilter) {
      // Show modal
      $('#modalFilterRequired').modal('show');
      return false;
   }

   // Build query string from form
   var form = document.getElementById('form_presensi_tadarus');
   var params = new URLSearchParams(new FormData(form)).toString();
   var baseUrl = '<?= site_url('administrator/presensi_tadarus/' . (!empty($is_locked) ? $url_method : 'index')); ?>';

   if (type === 'xls') {
      window.location.href = baseUrl + '/export?' + params;
   } else if (type === 'pdf') {
      window.location.href = baseUrl + '/export_pdf?' + params;
   }

   return false;
}
</script>
