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

.bg-grad-blue { background: linear-gradient(135deg, #3c8dbc 0%, #367fa9 100%); }
.bg-grad-green { background: linear-gradient(135deg, #00a65a 0%, #008d4c 100%); }
.bg-grad-red { background: linear-gradient(135deg, #dd4b39 0%, #c0432f 100%); }
.bg-grad-yellow { background: linear-gradient(135deg, #f39c12 0%, #db8b0a 100%); }

/* Filter Chips */
.filter-chips { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin: 10px 0; min-height: 28px; }
.filter-chip { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; background: #e8f4fd; border: 1px solid #b3d9f2; border-radius: 20px; font-size: 11px; color: #2c3e50; }
.filter-chip strong { color: #3498db; font-weight: 600; }
.filter-chip .chip-remove { cursor: pointer; color: #e74c3c; margin-left: 2px; font-weight: bold; }
.filter-chip .chip-remove:hover { color: #c0392b; }
.filter-chips-label { font-size: 11px; color: #888; margin-right: 4px; }

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
<?php } ?>
</script>

<!-- Content Header -->
<section class="content-header">
   <h1><i class="fa fa-futbol-o"></i> <?= cclang('presensi_pjok') ?> <small><?= cclang('list_all'); ?></small></h1>
   <ol class="breadcrumb"><li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li><li class="active"><?= cclang('presensi_pjok') ?></li></ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">

            <!-- Box Header -->
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-futbol-o"></i> Data Presensi PJOK
                  <span class="label bg-yellow" style="margin-left:10px" id="totalData"><?= $presensi_pjok_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('presensi_pjok_add', function () { ?>
                  <a class="btn btn-sm btn-success" title="Tambah Presensi PJOK (Ctrl+a)" href="<?= site_url('administrator/presensi_pjok/' . (!empty($is_locked) ? $url_method : 'index') . '/add'); ?>">
                     <i class="fa fa-plus"></i> Tambah
                  </a>
                  <?php }) ?>
                  <?php is_allowed('presensi_pjok_export', function(){?>
                  <a class="btn btn-sm btn-success" title="Export XLS" href="javascript:void(0)" onclick="exportData('xls')">
                     <i class="fa fa-file-excel-o"></i> XLS
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
                           <span class="info-value" id="sumTotalData"><?= number_format($summary['total_data']); ?></span>
                           <span class="info-detail">Seluruh data presensi</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-green">
                        <i class="fa fa-check-circle info-icon"></i>
                        <div>
                           <span class="info-label">Hadir</span>
                           <span class="info-value" id="sumHadir"><?= number_format($summary['total_hadir']); ?></span>
                           <span class="info-detail">Siswa hadir PJOK</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-red">
                        <i class="fa fa-times-circle info-icon"></i>
                        <div>
                           <span class="info-label">Tidak Hadir</span>
                           <span class="info-value" id="sumTidakHadir"><?= number_format($summary['total_sakit'] + $summary['total_izin'] + $summary['total_alpa']); ?></span>
                           <span class="info-detail" id="sumTidakHadirDetail">Sakit: <?= $summary['total_sakit']; ?> | Izin: <?= $summary['total_izin']; ?> | Alfa: <?= $summary['total_alpa']; ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-yellow">
                        <i class="fa fa-star info-icon"></i>
                        <div>
                           <span class="info-label">Rata-rata Nilai</span>
                           <span class="info-value" id="sumRataRata"><?= number_format($summary['rata_rata_nilai'], 1); ?></span>
                           <?php $total = $summary['rata_rata_nilai'];
                           if ($total >= 90) { $p = 'A - Sangat Baik'; }
                           elseif ($total >= 80) { $p = 'B - Baik'; }
                           elseif ($total >= 70) { $p = 'C - Cukup'; }
                           elseif ($total >= 60) { $p = 'D - Perlu Bimbingan'; }
                           else { $p = 'E - Perlu Pembinaan'; } ?>
                           <span class="info-detail" id="sumPredikat">Predikat: <?= $p ?></span>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- 2. Filter & Search (Compact 1-line) -->
               <form name="form_presensi_pjok" id="form_presensi_pjok" action="<?= base_url('administrator/presensi_pjok/' . (!empty($is_locked) ? $url_method : 'index')); ?>" onsubmit="return false;">
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
                              <option value="<?= $j['code']; ?>" <?= (!empty($filters['jenjang']) && $filters['jenjang'] == $j['code']) ? 'selected' : ''; ?>><?= $j['label']; ?></option>
                              <?php endforeach; ?>
                           </select>
                        <?php endif; ?>
                     </div>
                     <div class="form-group">
                        <label>Tingkatan</label>
                        <select class="form-control chosen chosen-select" name="tingkatan" id="filter_tingkatan" style="width:110px">
                           <option value="">Semua</option>
                           <?php if (!empty($list_tingkatan)): ?>
                           <?php foreach ($list_tingkatan as $t): ?>
                           <option value="<?= $t->id; ?>" data-jenjang="<?= $t->jenjang; ?>" <?= (!empty($filters['id_tingkatan']) && $filters['id_tingkatan'] == $t->id) ? 'selected' : ''; ?>><?= $t->label; ?> (<?= strtoupper($t->jenjang); ?>)</option>
                           <?php endforeach; ?>
                           <?php endif; ?>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Kelas</label>
                        <select class="form-control chosen chosen-select" name="kelas" id="filter_kelas" style="width:100px">
                           <option value="">Semua</option>
                           <?php if (!empty($list_kelas)): ?>
                           <?php foreach ($list_kelas as $k): ?>
                           <option value="<?= $k->label; ?>" data-tingkatan="<?= $k->id_tingkatan; ?>" data-jenjang="<?= $k->jenjang; ?>" <?= (!empty($filters['kelas']) && $filters['kelas'] == $k->label) ? 'selected' : ''; ?>><?= $k->label; ?> (<?= strtoupper($k->jenjang); ?>)</option>
                           <?php endforeach; ?>
                           <?php endif; ?>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Tgl Mulai</label>
                        <input type="text" class="form-control datepicker input-daterange" name="start_date" id="filter_start_date" value="<?= !empty($filters['start_date']) ? $filters['start_date'] : ''; ?>" placeholder="YYYY-MM-DD">
                     </div>
                     <div class="form-group">
                        <label>Tgl Akhir</label>
                        <input type="text" class="form-control datepicker input-daterange" name="end_date" id="filter_end_date" value="<?= !empty($filters['end_date']) ? $filters['end_date'] : ''; ?>" placeholder="YYYY-MM-DD">
                     </div>
                     <div class="form-group">
                        <label>Status</label>
                        <select class="form-control chosen chosen-select input-status" name="status_hadir" id="filter_status">
                           <option value="">Semua</option>
                           <option value="Hadir" <?= (!empty($filters['status_hadir']) && $filters['status_hadir'] == 'Hadir') ? 'selected' : ''; ?>>Hadir</option>
                           <option value="Terlambat" <?= (!empty($filters['status_hadir']) && $filters['status_hadir'] == 'Terlambat') ? 'selected' : ''; ?>>Terlambat</option>
                           <option value="Sakit" <?= (!empty($filters['status_hadir']) && $filters['status_hadir'] == 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                           <option value="Izin" <?= (!empty($filters['status_hadir']) && $filters['status_hadir'] == 'Izin') ? 'selected' : ''; ?>>Izin</option>
                           <option value="Alfa" <?= (!empty($filters['status_hadir']) && $filters['status_hadir'] == 'Alfa') ? 'selected' : ''; ?>>Alfa</option>
                        </select>
                     </div>
                     <div class="form-group" style="flex:1;min-width:180px">
                        <label>&nbsp;</label>
                        <div class="input-group">
                           <input type="text" class="form-control input-search" name="q" id="filter" placeholder="Cari nama, NIS, kelas..." value="<?= !empty($filters['q']) ? htmlspecialchars($filters['q']) : ''; ?>">
                           <span class="input-group-btn">
                              <button type="button" class="btn btn-flat btn-primary" id="btnSearch"><i class="fa fa-search"></i></button>
                              <button type="button" class="btn btn-flat btn-default" id="btnReset" title="Reset filter"><i class="fa fa-times"></i></button>
                           </span>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Filter Chips -->
               <div class="filter-chips" id="filterChips" style="<?= empty($filters) ? 'display:none;' : '' ?>">
                  <span class="filter-chips-label"><i class="fa fa-filter"></i> Filter aktif:</span>
                  <?php if (!empty($filters['jenjang'])): ?><span class="filter-chip" data-key="jenjang"><strong>Jenjang:</strong> <?= $filters['jenjang']; ?> <span class="chip-remove" data-filter="jenjang">&times;</span></span><?php endif; ?>
                  <?php if (!empty($filters['id_tingkatan'])): ?><span class="filter-chip" data-key="id_tingkatan"><strong>Tingkatan:</strong> <?= $filters['id_tingkatan']; ?> <span class="chip-remove" data-filter="id_tingkatan">&times;</span></span><?php endif; ?>
                  <?php if (!empty($filters['kelas'])): ?><span class="filter-chip" data-key="kelas"><strong>Kelas:</strong> <?= $filters['kelas']; ?> <span class="chip-remove" data-filter="kelas">&times;</span></span><?php endif; ?>
                  <?php if (!empty($filters['start_date'])): ?><span class="filter-chip" data-key="start_date"><strong>Dari:</strong> <?= $filters['start_date']; ?> <span class="chip-remove" data-filter="start_date">&times;</span></span><?php endif; ?>
                  <?php if (!empty($filters['end_date'])): ?><span class="filter-chip" data-key="end_date"><strong>Sampai:</strong> <?= $filters['end_date']; ?> <span class="chip-remove" data-filter="end_date">&times;</span></span><?php endif; ?>
                  <?php if (!empty($filters['status_hadir'])): ?><span class="filter-chip" data-key="status_hadir"><strong>Status:</strong> <?= $filters['status_hadir']; ?> <span class="chip-remove" data-filter="status_hadir">&times;</span></span><?php endif; ?>
                  <?php if (!empty($filters['q'])): ?><span class="filter-chip" data-key="q"><strong>Cari:</strong> <?= $filters['q']; ?> <span class="chip-remove" data-filter="q">&times;</span></span><?php endif; ?>
               </div>

               <!-- 3. Chart -->
               <div class="chart-container">
                  <h4 id="chartToggle" style="cursor:pointer">
                     <i class="fa fa-bar-chart"></i> Chart Mingguan PJOK (8 Minggu Terakhir)
                     <i class="fa fa-chevron-down" id="chartIcon" style="font-size:12px;margin-left:5px"></i>
                  </h4>
                  <div id="chartBody" style="display:none">
                     <canvas id="chartPjok" width="100%" height="30"></canvas>
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
                           <th>Kelas</th>
                           <th>Jenjang</th>
                           <th>Status</th>
                           <th>Total</th>
                           <th>Predikat</th>
                           <th width="220">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_presensi_pjok">
                     <?php if($presensi_pjok_counts > 0): ?>
                     <?php foreach($presensi_pjoks as $pt):
                        $total = (float) $pt->total_nilai;
                        if ($total >= 90) { $pred = 'A'; $desc = 'Sangat Baik'; $badge = 'badge-a'; }
                        elseif ($total >= 80) { $pred = 'B'; $desc = 'Baik'; $badge = 'badge-b'; }
                        elseif ($total >= 70) { $pred = 'C'; $desc = 'Cukup'; $badge = 'badge-c'; }
                        elseif ($total >= 60) { $pred = 'D'; $desc = 'Perlu Bimbingan'; $badge = 'badge-d'; }
                        else { $pred = 'E'; $desc = 'Perlu Pembinaan Intensif'; $badge = 'badge-e'; }

                        $nama = $pt->nama_lengkap;
                        $words = explode(' ', $nama);
                        $initials = '';
                        foreach ($words as $w) {
                            if (strlen($w) > 0) $initials .= strtoupper($w[0]);
                            if (strlen($initials) >= 2) break;
                        }
                     ?>
                        <tr>
                           <td>
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $pt->id_presensi_pjok; ?>">
                           </td>
                           <td style="white-space:nowrap"><?= _ent($pt->tanggal); ?></td>
                           <td>
                              <div class="labs-name-cell">
                                 <span class="labs-avatar"><?= $initials; ?></span>
                                 <span><?= _ent($pt->nama_lengkap); ?></span>
                              </div>
                           </td>
                           <td style="text-align:center"><?= _ent($pt->kelas); ?></td>
                           <td style="text-align:center"><span class="labs-badge labs-badge--info"><?= _ent($pt->jenjang); ?></span></td>
                           <td style="text-align:center">
                              <?php if ($pt->status_hadir == 'Hadir'): ?>
                              <span class="labs-badge labs-badge--success"><?= _ent($pt->status_hadir); ?></span>
                              <?php elseif ($pt->status_hadir == 'Sakit'): ?>
                              <span class="labs-badge labs-badge--warning"><?= _ent($pt->status_hadir); ?></span>
                              <?php elseif ($pt->status_hadir == 'Izin'): ?>
                              <span class="labs-badge labs-badge--info"><?= _ent($pt->status_hadir); ?></span>
                              <?php elseif ($pt->status_hadir == 'Terlambat'): ?>
                              <span class="labs-badge labs-badge--orange"><?= _ent($pt->status_hadir); ?></span>
                              <?php else: ?>
                              <span class="labs-badge labs-badge--danger"><?= _ent($pt->status_hadir); ?></span>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center"><strong><?= _ent($pt->total_nilai); ?></strong></td>
                           <td style="text-align:center">
                              <span class="badge-predikat <?= $badge; ?>" title="<?= $desc; ?>"><?= $pred; ?></span>
                              <br><small class="text-muted"><?= $desc; ?></small>
                           </td>
                           <td style="text-align:center">
                              <div style="margin-bottom:3px">
                                 <?php is_allowed('presensi_pjok_view', function() use ($pt){?>
                                 <a href="javascript:void(0);" class="btn btn-sm btn-info btn-detail-modal" data-id="<?= $pt->id_presensi_pjok; ?>" title="Lihat detail" style="margin:2px 1px;padding:4px 0;font-size:11px;border-radius:3px;width:48%;display:inline-block;text-align:center">
                                    <i class="fa fa-eye"></i> Detail
                                 </a>
                                 <?php }) ?>
                                 <?php is_allowed('presensi_pjok_update', function() use ($pt){?>
                                 <a href="<?= site_url('administrator/presensi_pjok/edit/' . $pt->id_presensi_pjok); ?>" class="btn btn-sm btn-success" title="Edit" style="margin:2px 1px;padding:4px 0;font-size:11px;border-radius:3px;width:48%;display:inline-block;text-align:center">
                                    <i class="fa fa-edit"></i> Edit
                                 </a>
                                 <?php }) ?>
                              </div>
                              <div>
                                 <?php is_allowed('presensi_pjok_delete', function() use ($pt){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/presensi_pjok/' . (!empty($is_locked) ? $url_method : 'index') . '/delete/' . $pt->id_presensi_pjok); ?>" class="btn btn-sm btn-danger remove-data" title="Hapus" style="margin:2px 1px;padding:4px 0;font-size:11px;border-radius:3px;width:98%;display:block;text-align:center">
                                    <i class="fa fa-trash"></i> Hapus
                                 </a>
                                 <?php }) ?>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="9" class="labs-empty">
                              <i class="fa fa-inbox"></i><br>
                              <?php if(!empty($filters)): ?>
                                 Data presensi PJOK tidak ditemukan untuk filter yang dipilih
                              <?php else: ?>
                                 Data presensi PJOK belum tersedia
                              <?php endif; ?>
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
                     <div class="dataTables_paginate paging_simple_numbers" id="paginationWrap">
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
<div class="labs-modal modal fade" id="modalFilterRequired" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content" style="border-radius:8px;overflow:hidden">
         <div class="labs-modal__header" style="background:linear-gradient(135deg,#f39c12,#e67e22)">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8"><span>&times;</span></button>
            <h4 class="modal-title"><i class="fa fa-filter"></i> Filter Diperlukan</h4>
         </div>
         <div class="modal-body text-center" style="padding:30px 20px">
            <div style="margin-bottom:15px">
               <i class="fa fa-exclamation-circle" style="font-size:60px;color:#f39c12"></i>
            </div>
            <h5 style="color:#2c3e50;margin-bottom:10px;font-weight:600">Data Terlalu Besar</h5>
            <p style="color:#7f8c8d;font-size:13px;margin-bottom:0">Silakan pilih minimal satu filter sebelum mengekspor data.</p>
         </div>
         <div class="modal-footer" style="border:none;padding:10px 20px 20px">
            <button type="button" class="btn btn-warning btn-block" data-dismiss="modal" style="border-radius:4px;font-weight:600">
               <i class="fa fa-check"></i> Mengerti
            </button>
         </div>
      </div>
   </div>
</div>

<!-- Modal: Detail Presensi PJOK -->
<div class="labs-modal modal fade" id="modalDetailPresensi" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-lg" style="width:90%;max-width:1000px;margin-top:30px">
      <div class="modal-content" style="border-radius:8px;overflow:hidden;border:none;box-shadow:0 10px 40px rgba(0,0,0,0.2)">
         <div class="labs-modal__header" style="background:linear-gradient(135deg,#C2410C,#9A3412)">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8;font-size:24px"><span>&times;</span></button>
            <h4 class="modal-title"><i class="fa fa-eye" style="margin-right:8px"></i>Detail Presensi PJOK</h4>
         </div>
         <div class="modal-body" style="padding:20px;max-height:70vh;overflow-y:auto">
            <div class="text-center" style="padding:40px 20px">
               <i class="fa fa-spinner fa-spin fa-3x" style="color:#C2410C"></i>
               <p class="text-muted" style="margin-top:15px">Memuat detail presensi...</p>
            </div>
         </div>
         <div class="modal-footer" style="border:none;padding:15px 20px;background:#f8f9fa">
            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius:4px"><i class="fa fa-times"></i> Tutup</button>
         </div>
      </div>
   </div>
</div>

<!-- JS -->
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
var ajaxUrl = '<?= site_url('administrator/presensi_pjok/get_list_ajax'); ?>';
var baseUrlMethod = '<?= site_url('administrator/presensi_pjok/' . (!empty($is_locked) ? $url_method : 'index')); ?>';
var pjokChart = null;

$(document).ready(function(){

   // Chart Toggle
   $('#chartToggle').click(function(){
      $('#chartBody').slideToggle(200);
      $('#chartIcon').toggleClass('fa-chevron-down fa-chevron-up');
   });

   // Search button
   $('#btnSearch').click(function(){ fetchListAjax(); });

   // Enter key on search
   $('#filter').keydown(function(e){ if(e.keyCode===13){ e.preventDefault(); fetchListAjax(); } });

   // Reset button
   $('#btnReset').click(function(){
      $('#form_presensi_pjok')[0].reset();
      $('#filter_jenjang,#filter_tingkatan,#filter_kelas,#filter_status').val('').trigger('chosen:updated');
      fetchListAjax();
   });

   // Chip remove
   $(document).on('click', '.chip-remove', function(){
      var f = $(this).data('filter');
      var $el = $('[name="'+f+'"]');
      if($el.is('select')){ $el.val('').trigger('chosen:updated'); }
      else { $el.val(''); }
      fetchListAjax();
   });

   // Cascade filter: Jenjang -> Tingkatan
   $('#filter_jenjang').change(function(){
      var jenjang = $(this).val().toLowerCase();
      var $tingkatan = $('#filter_tingkatan');
      var $kelas = $('#filter_kelas');
      $tingkatan.html('<option value="">Semua Tingkatan</option>');
      $kelas.html('<option value="">Semua Kelas</option>');
      if (!jenjang) {
         <?php if (!empty($list_tingkatan)): ?><?php foreach ($list_tingkatan as $t): ?>
         $tingkatan.append('<option value="<?= $t->id; ?>" data-jenjang="<?= $t->jenjang; ?>"><?= $t->label; ?> (<?= strtoupper($t->jenjang); ?>)</option>');
         <?php endforeach; ?><?php endif; ?>
         $tingkatan.trigger('chosen:updated'); $kelas.trigger('chosen:updated');
         return;
      }
      $.get(BASE_URL + '/administrator/presensi_pjok/get_tingkatan', {jenjang: jenjang}, function(data){
         $.each(JSON.parse(data), function(i, item){ $tingkatan.append('<option value="'+item.id+'">'+item.label+'</option>'); });
         $tingkatan.trigger('chosen:updated');
      });
   });

   // Cascade: Tingkatan -> Kelas
   $('#filter_tingkatan').change(function(){
      var id_tingkatan = $(this).val();
      var jenjang = $('#filter_jenjang').length ? $('#filter_jenjang').val().toLowerCase() : '';
      var $kelas = $('#filter_kelas');
      $kelas.html('<option value="">Semua Kelas</option>');
      if (!jenjang) {
         <?php if (!empty($is_locked) && !empty($list_jenjang)): ?>
         jenjang = '<?= strtolower($list_jenjang[0]['code']); ?>';
         <?php endif; ?>
      }
      if (!jenjang) {
         <?php if (!empty($list_kelas)): ?><?php foreach ($list_kelas as $k): ?>
         $kelas.append('<option value="<?= $k->label; ?>"><?= $k->label; ?> (<?= strtoupper($k->jenjang); ?>)</option>');
         <?php endforeach; ?><?php endif; ?>
         $kelas.trigger('chosen:updated'); return;
      }
      $.get(BASE_URL + '/administrator/presensi_pjok/get_kelas', {jenjang: jenjang, id_tingkatan: id_tingkatan}, function(data){
         $.each(JSON.parse(data), function(i, item){ $kelas.append('<option value="'+item.label+'">'+item.label+'</option>'); });
         $kelas.trigger('chosen:updated');
      });
   });

   // Delete single (delegated)
   $(document).on('click', '.remove-data', function(){
      var url = $(this).attr('data-href');
      swal({title:"<?= cclang('are_you_sure'); ?>",text:"<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",type:"warning",showCancelButton:true,confirmButtonColor:"#DD6B55",confirmButtonText:"<?= cclang('yes_delete_it'); ?>",cancelButtonText:"<?= cclang('no_cancel_plx'); ?>",closeOnConfirm:true,closeOnCancel:true},function(isConfirm){if(isConfirm){document.location.href=url;}});
      return false;
   });

   // Bulk action
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_presensi_pjok').serialize();
      if (bulk.val() == 'delete') {
         swal({title:"<?= cclang('are_you_sure'); ?>",text:"<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",type:"warning",showCancelButton:true,confirmButtonColor:"#DD6B55",confirmButtonText:"<?= cclang('yes_delete_it'); ?>",cancelButtonText:"<?= cclang('no_cancel_plx'); ?>",closeOnConfirm:true,closeOnCancel:true},function(isConfirm){if(isConfirm){document.location.href=BASE_URL+'/administrator/presensi_pjok/<?= !empty($is_locked) ? $url_method : 'index'; ?>/delete?'+serialize_bulk;}});
      } else if(bulk.val() == '') {
         swal({title:"Upss",text:"<?= cclang('please_choose_bulk_action_first'); ?>",type:"warning",showCancelButton:false,confirmButtonColor:"#DD6B55",confirmButtonText:"Okay!",closeOnConfirm:true,closeOnCancel:true});
      }
      return false;
   });

   // Check all
   var checkAll = $('#check_all');
   var checkboxes = $('input.check');
   checkAll.on('ifChecked ifUnchecked', function(event) {
      checkboxes.iCheck(event.type == 'ifChecked' ? 'check' : 'uncheck');
   });
   checkboxes.on('ifChanged', function(){
      checkAll.prop('checked', checkboxes.filter(':checked').length === checkboxes.length);
      checkAll.iCheck('update');
   });

   // Init chart
   initChart(<?= json_encode(!empty($chart_data) ? $chart_data : array()); ?>);
});

// ============================================================
// AJAX FETCH LIST
// ============================================================
function fetchListAjax(offset) {
   offset = offset || 0;
   var params = $('#form_presensi_pjok').serialize() + '&offset=' + offset;

   // Show loading
   $('#tbody_presensi_pjok').html('<tr><td colspan="9" class="text-center" style="padding:30px"><i class="fa fa-spinner fa-spin fa-2x" style="color:#C2410C"></i><br><small class="text-muted">Memuat data...</small></td></tr>');

   $.get(ajaxUrl, params, function(resp){
      if (!resp.success) return;

      // Update table
      $('#tbody_presensi_pjok').html(resp.rows_html);

      // Update total
      $('#totalData').text(resp.total_data + ' Data');

      // Update summary cards
      $('#sumTotalData').text(numberFormat(resp.summary.total_data));
      $('#sumHadir').text(numberFormat(resp.summary.total_hadir));
      var tidakHadir = parseInt(resp.summary.total_sakit) + parseInt(resp.summary.total_izin) + parseInt(resp.summary.total_alpa);
      $('#sumTidakHadir').text(numberFormat(tidakHadir));
      $('#sumTidakHadirDetail').text('Sakit: '+resp.summary.total_sakit+' | Izin: '+resp.summary.total_izin+' | Alfa: '+resp.summary.total_alpa);
      $('#sumRataRata').text(parseFloat(resp.summary.rata_rata_nilai).toFixed(1));
      var rr = parseFloat(resp.summary.rata_rata_nilai);
      var predText = rr>=90?'A - Sangat Baik':rr>=80?'B - Baik':rr>=70?'C - Cukup':rr>=60?'D - Perlu Bimbingan':'E - Perlu Pembinaan';
      $('#sumPredikat').text('Predikat: '+predText);

      // Update chips
      var $chips = $('#filterChips');
      $chips.empty();
      if (resp.chips.length > 0) {
         $chips.show().append('<span class="filter-chips-label"><i class="fa fa-filter"></i> Filter aktif:</span>');
         $.each(resp.chips, function(i, c){
            $chips.append('<span class="filter-chip" data-key="'+c.key+'"><strong>'+c.label+':</strong> '+escHtml(c.value)+' <span class="chip-remove" data-filter="'+c.key+'">&times;</span></span>');
         });
      } else {
         $chips.hide();
      }

      // Update pagination
      $('#paginationWrap').html(resp.pagination_html);

      // Update chart
      initChart(resp.chart_data);

      // Re-init iCheck for new checkboxes
      $('input.check').iCheck({checkboxClass:'icheckbox_minimal-red',radioClass:'iradio_minimal-red'});

   }, 'json').fail(function(){
      $('#tbody_presensi_pjok').html('<tr><td colspan="9" class="text-center text-danger" style="padding:20px"><i class="fa fa-exclamation-triangle"></i> Gagal memuat data. Coba lagi.</td></tr>');
   });
}

// ============================================================
// CHART
// ============================================================
function initChart(chartData) {
   var ctx = document.getElementById('chartPjok').getContext('2d');
   if (pjokChart) { pjokChart.destroy(); }

   var labels = [], hadirData = [], nilaiData = [];
   if (chartData && chartData.length > 0) {
      for (var i = 0; i < chartData.length; i++) {
         labels.push(chartData[i].tanggal_presensi);
         hadirData.push(parseInt(chartData[i].total_hadir) || 0);
         nilaiData.push(parseFloat(chartData[i].rata_rata_nilai) || 0);
      }
   } else {
      labels.push('Belum ada data'); hadirData.push(0); nilaiData.push(0);
   }

   pjokChart = new Chart(ctx, {
      type: 'bar',
      data: {
         labels: labels,
         datasets: [
            { label: 'Jumlah Hadir', type: 'bar', backgroundColor: 'rgba(194,65,12,0.6)', borderColor: '#C2410C', borderWidth: 1, data: hadirData, yAxisID: 'y-hadir' },
            { label: 'Rata-rata Nilai', type: 'line', borderColor: '#3498db', backgroundColor: 'rgba(52,152,219,0.1)', borderWidth: 2, pointRadius: 4, pointBackgroundColor: '#3498db', fill: false, data: nilaiData, yAxisID: 'y-nilai' }
         ]
      },
      options: {
         responsive: true, legend: { position: 'top' },
         scales: {
            yAxes: [
               { id: 'y-hadir', type: 'linear', position: 'left', ticks: { beginAtZero: true, stepSize: 1 }, scaleLabel: { display: true, labelString: 'Jumlah Hadir' } },
               { id: 'y-nilai', type: 'linear', position: 'right', ticks: { beginAtZero: true, max: 100 }, gridLines: { drawOnChartArea: false }, scaleLabel: { display: true, labelString: 'Rata-rata Nilai' } }
            ]
         }
      }
   });
}

// ============================================================
// DETAIL MODAL
// ============================================================
$(document).on('click', '.btn-detail-modal', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var $modal = $('#modalDetailPresensi');
    $modal.find('.modal-body').html('<div class="text-center" style="padding:40px 20px"><i class="fa fa-spinner fa-spin fa-3x" style="color:#C2410C"></i><p class="text-muted">Memuat detail presensi...</p></div>');
    $modal.modal('show');
    $.get(BASE_URL + '/administrator/presensi_pjok/get_detail/' + id, function(response) {
        if (response.success && response.data) { renderDetailModal(response.data, $modal); }
        else { $modal.find('.modal-body').html('<div class="text-center" style="padding:40px 20px"><i class="fa fa-exclamation-triangle fa-3x" style="color:#e74c3c"></i><p class="text-danger">'+(response.message||'Data tidak ditemukan')+'</p></div>'); }
    }).fail(function() { $modal.find('.modal-body').html('<div class="text-center" style="padding:40px 20px"><i class="fa fa-exclamation-triangle fa-3x" style="color:#e74c3c"></i><p class="text-danger">Gagal memuat data.</p></div>'); });
});

function renderDetailModal(data, $modal) {
    var statusClass='label-default',statusLabel=data.status_hadir;
    switch(data.status_hadir.toLowerCase()){case'hadir':statusClass='label-success';break;case'terlambat':statusClass='label-warning';break;case'sakit':statusClass='label-warning';break;case'izin':statusClass='label-info';break;default:statusClass='label-danger';}
    var total=parseFloat(data.total_nilai)||0,predikat='',predikatDesc='',predikatClass='';
    if(total>=90){predikat='A';predikatDesc='Sangat Baik';predikatClass='badge-a';}else if(total>=80){predikat='B';predikatDesc='Baik';predikatClass='badge-b';}else if(total>=70){predikat='C';predikatDesc='Cukup';predikatClass='badge-c';}else if(total>=60){predikat='D';predikatDesc='Perlu Bimbingan';predikatClass='badge-d';}else{predikat='E';predikatDesc='Perlu Pembinaan Intensif';predikatClass='badge-e';}
    var keaktifanBadge='label-default';if(data.status_keaktifan=='Sangat Aktif')keaktifanBadge='label-success';else if(data.status_keaktifan=='Aktif')keaktifanBadge='label-info';else if(data.status_keaktifan=='Cukup Aktif'||data.status_keaktifan=='Cukup_aktif')keaktifanBadge='label-warning';
    var html='<div class="row" style="margin-bottom:15px"><div class="col-md-4"><div class="info-box-custom bg-grad-blue"><i class="fa fa-calendar info-icon"></i><div><span class="info-label">Tanggal</span><span class="info-value" style="font-size:22px">'+formatDateIndo(data.tanggal)+'</span><span class="info-detail">'+esc(data.hari)+'</span></div></div></div><div class="col-md-4"><div class="info-box-custom bg-grad-green"><i class="fa fa-user info-icon"></i><div><span class="info-label">Siswa</span><span class="info-value" style="font-size:22px">'+esc(data.nama_lengkap)+'</span><span class="info-detail">NIS: '+esc(data.nis||'-')+'</span></div></div></div><div class="col-md-4"><div class="info-box-custom bg-grad-yellow"><i class="fa fa-graduation-cap info-icon"></i><div><span class="info-label">Kelas / Jenjang</span><span class="info-value" style="font-size:22px">'+esc(data.kelas)+'</span><span class="info-detail">'+esc(data.jenjang)+'</span></div></div></div></div>';
    html+='<div class="row" style="margin-bottom:15px"><div class="col-md-6"><div class="box box-solid box-primary"><div class="box-header with-border"><h3 class="box-title"><i class="fa fa-check-circle"></i> Status Kehadiran</h3></div><div class="box-body text-center"><span class="label '+statusClass+'" style="font-size:16px;padding:10px 20px">'+esc(statusLabel)+'</span></div></div></div><div class="col-md-6"><div class="box box-solid box-info"><div class="box-header with-border"><h3 class="box-title"><i class="fa fa-star"></i> Predikat Nilai</h3></div><div class="box-body text-center"><span class="badge-predikat '+predikatClass+'" style="font-size:14px;padding:8px 16px">'+predikat+'</span><br><br><strong style="font-size:18px">Total Nilai: '+total+'</strong><br><small class="text-muted">'+predikatDesc+'</small></div></div></div></div>';
    html+='<div class="box box-solid"><div class="box-header with-border"><h3 class="box-title"><i class="fa fa-list-alt"></i> Detail Penilaian PJOK</h3></div><div class="box-body table-responsive no-padding"><table class="table table-bordered table-striped mb-0"><thead style="background:#f8f9fa"><tr><th style="width:40%">Aspek Penilaian</th><th style="width:20%" class="text-center">Nilai (0-100)</th><th style="width:40%">Keterangan</th></tr></thead><tbody><tr><td><strong>Kehadiran</strong></td><td class="text-center"><strong>'+esc(data.kehadiran)+'</strong></td><td>'+pjokNilaiBar(data.kehadiran)+'</td></tr><tr><td><strong>Status Keaktifan</strong></td><td class="text-center"><span class="label '+keaktifanBadge+'">'+esc(data.status_keaktifan)+'</span></td><td>-</td></tr><tr><td><strong>Keaktifan</strong></td><td class="text-center"><strong>'+esc(data.keaktifan)+'</strong></td><td>'+pjokNilaiBar(data.keaktifan)+'</td></tr></tbody></table></div></div>';
    html+='<div class="row" style="margin-top:15px"><div class="col-md-6"><div class="box box-solid box-default"><div class="box-header with-border"><h3 class="box-title"><i class="fa fa-clock-o"></i> Informasi Waktu</h3></div><div class="box-body"><table class="table table-bordered mb-0"><tr><th style="width:40%">Dibuat</th><td>'+esc(data.created_at)+'</td></tr><tr><th>Diupdate</th><td>'+esc(data.updated_at)+'</td></tr><tr><th>Oleh</th><td>'+esc(data.updated_by)+'</td></tr></table></div></div></div><div class="col-md-6"><div class="box box-solid box-default"><div class="box-header with-border"><h3 class="box-title"><i class="fa fa-cogs"></i> Aksi</h3></div><div class="box-body text-center" style="padding:20px"><a href="<?= site_url("administrator/presensi_pjok/edit/") ?>'+data.id_presensi_pjok+'" class="btn btn-sm btn-success" style="margin-right:5px" target="_blank"><i class="fa fa-edit"></i> Edit</a><a href="<?= site_url("administrator/presensi_pjok/single_pdf/") ?>'+data.id_presensi_pjok+'" class="btn btn-sm btn-warning" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a></div></div></div></div>';
    $modal.find('.modal-body').html(html);
}

function pjokNilaiBar(nilai){var n=parseInt(nilai);if(isNaN(n))return'<span class="text-muted">-</span>';var color=n>=90?'#27ae60':n>=80?'#3498db':n>=70?'#f39c12':n>=60?'#e67e22':'#e74c3c';var label=n>=90?'Sangat Baik':n>=80?'Baik':n>=70?'Cukup':n>=60?'Perlu Bimbingan':'Perlu Pembinaan';return'<div style="display:flex;align-items:center;gap:8px"><div style="flex:1;background:#eee;border-radius:4px;height:8px;overflow:hidden"><div style="width:'+n+'%;background:'+color+';height:100%;border-radius:4px"></div></div><span style="color:'+color+';font-weight:600;font-size:12px">'+label+'</span></div>';}
function formatDateIndo(dateStr){if(!dateStr)return'-';var m=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],d=new Date(dateStr);if(isNaN(d.getTime()))return dateStr;return d.getDate()+' '+m[d.getMonth()]+' '+d.getFullYear();}
function esc(t){if(t===null||t===undefined)return'';var m={'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};return t.toString().replace(/[&<>"']/g,function(x){return m[x];});}
function escHtml(t){if(!t)return'';return t.toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function numberFormat(n){return parseInt(n).toLocaleString('id-ID');}

function exportData(type) {
   var hasFilter = false;
   var form = document.getElementById('form_presensi_pjok');
   var formData = new FormData(form);
   for(var pair of formData.entries()) { if(pair[1] && pair[1].trim && pair[1].trim()!=='') hasFilter=true; if(pair[1] && !pair[1].trim && pair[1]!=='') hasFilter=true; }
   if(!hasFilter){ $('#modalFilterRequired').modal('show'); return false; }
   var params = new URLSearchParams(formData).toString();
   if(type==='xls') window.location.href=baseUrlMethod+'/export?'+params;
   return false;
}
</script>
