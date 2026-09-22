<style>
/* Page header */
.content-header > h1 { font-size:22px; font-weight:600; }
.content-header > h1 > small { font-size:13px; color:#777; }
.breadcrumb { background:transparent; }

/* Box */
.box.box-warning { border-top-color:#f39c12; }

/* Table */
.table { margin-bottom:0; }
.table th { background:#f8f9fa; font-weight:600; font-size:12px; vertical-align:middle; text-align:center; white-space:nowrap; }
.table td { font-size:13px; vertical-align:middle; }
.table-striped > tbody > tr:nth-of-type(odd) { background:#fafbfc; }
.label { padding:3px 8px; border-radius:3px; color:#fff; font-size:11px; display:inline-block; }
.text-muted { color:#999; }

/* Filter form */
.filter-bar { margin-bottom:15px; padding:12px 15px; background:#f8f9fa; border-radius:5px; border:1px solid #e0e0e0; }
.filter-bar .form-group { margin-bottom:8px; margin-right:8px; }
.filter-bar label { font-weight:600; font-size:12px; color:#555; margin-right:5px; }
.filter-bar .form-control { font-size:13px; }

/* Info box rekap - 5 kolom equal width */
.info-box-col { width:20%; float:left; padding-left:8px; padding-right:8px; position:relative; min-height:1px; }
@media (max-width:1199px) { .info-box-col { width:33.333333%; } }
@media (max-width:767px) { .info-box-col { width:50%; } }
@media (max-width:480px) { .info-box-col { width:100%; } }
.rekap-info-box { color:#fff; min-height:100px; border-radius:4px; margin-bottom:12px; box-shadow:0 1px 1px rgba(0,0,0,0.1); }
.rekap-info-box .info-box-icon { background:rgba(0,0,0,0.15); color:#fff; height:100px; line-height:100px; width:100px; font-size:32px; text-align:center; }
.rekap-info-box .info-box-content { padding:12px 12px 12px 110px; }
.rekap-info-box .info-box-text { color:#fff; font-size:13px; font-weight:600; opacity:0.95; display:block; word-wrap:break-word; line-height:1.3; }
.rekap-info-box .info-box-number { color:#fff; font-size:22px; font-weight:700; margin-top:6px; display:block; line-height:1.1; }

/* Catatan badge */
.btn-catatan { white-space:nowrap; }

/* Active filter chips */
.filter-chip { display:inline-block; padding:3px 8px; margin:2px; font-size:11px; background:#3498db; color:#fff; border-radius:3px; }
</style>

<script type="text/javascript">
<?php if ($this->session->flashdata('success')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('success'))); ?>
   toastr.success("<?= $msg; ?>", "Berhasil", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } else if ($this->session->flashdata('error')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('error'))); ?>
   toastr.error("<?= $msg; ?>", "Error", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } ?>
</script>

<!-- Content Header -->
<section class="content-header">
   <h1><i class="fa fa-calendar-check-o"></i> Presensi SMA <small>Daftar Presensi Harian Siswa</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Presensi SMA</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-list"></i> Data Presensi <span class="label bg-yellow" style="margin-left:8px"><?= (int)$value_counts; ?> Data</span></h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('presensi_sma_add', function(){ ?>
                  <a class="btn btn-sm btn-success" title="<?= cclang('add_new_button', [cclang('presensi_sma')]); ?> (Ctrl+a)" href="<?= site_url('administrator/presensi_sma/add'); ?>">
                     <i class="fa fa-plus"></i> Tambah Presensi
                  </a>
                  <?php }) ?>
                  <?php is_allowed('presensi_sma_export', function() use ($filter){
                     $qfilter = isset($filter) ? http_build_query($filter) : '';
                  ?>
                  <a class="btn btn-sm btn-info" title="Export Harian" href="<?= site_url('administrator/presensi_sma/export_harian?'.$qfilter); ?>"><i class="fa fa-file-excel-o"></i> Harian</a>
                  <a class="btn btn-sm btn-info" title="Export Bulanan" href="<?= site_url('administrator/presensi_sma/export_bulanan?'.$qfilter); ?>"><i class="fa fa-file-excel-o"></i> Bulanan</a>
                  <a class="btn btn-sm btn-info" title="Export Periode" href="<?= site_url('administrator/presensi_sma/export_periode?'.$qfilter); ?>"><i class="fa fa-file-excel-o"></i> Periode</a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">

               <!-- Filter & Search -->
               <form method="get" action="<?= base_url('administrator/presensi_sma/index'); ?>" class="form-inline filter-bar">
                  <div class="form-group">
                     <label>Cari:</label>
                     <input type="text" id="filter_q" name="q" value="<?= htmlspecialchars(isset($filter['q']) ? $filter['q'] : ''); ?>" class="form-control input-sm" placeholder="Nama / NIS">
                  </div>
                  <div class="form-group">
                     <label>Tahun Ajaran:</label>
                     <select name="id_tahun_ajaran" class="form-control input-sm">
                        <option value="">- Semua -</option>
                        <?php foreach($list_tahun_ajaran as $ta): ?>
                        <option value="<?= $ta->id_tahun_ajaran; ?>" <?= (isset($filter['id_tahun_ajaran']) && $filter['id_tahun_ajaran']==$ta->id_tahun_ajaran) ? 'selected' : ''; ?>><?= htmlspecialchars($ta->label); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="form-group">
                     <label>Tingkatan:</label>
                     <select name="id_tingkatan" class="form-control input-sm">
                        <option value="">- Semua -</option>
                        <?php foreach($list_tingkatan as $t): ?>
                        <option value="<?= $t->id_tingkatan_sma; ?>" <?= (isset($filter['id_tingkatan']) && $filter['id_tingkatan']==$t->id_tingkatan_sma) ? 'selected' : ''; ?>><?= htmlspecialchars($t->label); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="form-group">
                     <label>Kelas:</label>
                     <select name="id_kelas" class="form-control input-sm">
                        <option value="">- Semua -</option>
                        <?php foreach($list_kelas as $k): ?>
                        <option value="<?= $k->id_kelas_sma; ?>" <?= (isset($filter['id_kelas']) && $filter['id_kelas']==$k->id_kelas_sma) ? 'selected' : ''; ?>><?= htmlspecialchars($k->label); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="form-group">
                     <label>Dari:</label>
                     <input type="date" name="start_date" value="<?= isset($filter['start_date']) ? $filter['start_date'] : ''; ?>" class="form-control input-sm">
                  </div>
                  <div class="form-group">
                     <label>Sampai:</label>
                     <input type="date" name="end_date" value="<?= isset($filter['end_date']) ? $filter['end_date'] : ''; ?>" class="form-control input-sm">
                  </div>
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Terapkan</button>
                  <a href="<?= base_url('administrator/presensi_sma'); ?>" class="btn btn-default btn-sm"><i class="fa fa-times"></i> Reset</a>
               </form>

               <!-- Active filter chips -->
               <?php
               $chips = array();
               if (!empty($filter['q'])) $chips[] = 'Pencarian: "'.htmlspecialchars($filter['q']).'"';
               if (!empty($filter['id_tahun_ajaran'])) {
                  $ta_label = '';
                  foreach($list_tahun_ajaran as $ta) { if ($ta->id_tahun_ajaran==$filter['id_tahun_ajaran']) { $ta_label = $ta->label; break; } }
                  if ($ta_label) $chips[] = 'Tahun: '.$ta_label;
               }
               if (!empty($filter['id_tingkatan'])) {
                  $t_label = '';
                  foreach($list_tingkatan as $t) { if ($t->id_tingkatan_sma==$filter['id_tingkatan']) { $t_label = $t->label; break; } }
                  if ($t_label) $chips[] = 'Tingkat: '.$t_label;
               }
               if (!empty($filter['id_kelas'])) {
                  $k_label = '';
                  foreach($list_kelas as $k) { if ($k->id_kelas_sma==$filter['id_kelas']) { $k_label = $k->label; break; } }
                  if ($k_label) $chips[] = 'Kelas: '.$k_label;
               }
               if (!empty($filter['start_date'])) $chips[] = 'Dari: '.$filter['start_date'];
               if (!empty($filter['end_date'])) $chips[] = 'Sampai: '.$filter['end_date'];
               ?>
               <?php if (!empty($chips)): ?>
               <div style="margin-bottom:12px;padding:8px 12px;background:#e8f4fc;border-radius:3px;border:1px solid #bee5eb">
                  <i class="fa fa-info-circle" style="color:#3498db"></i>
                  <small style="color:#2c3e50"><strong>Filter aktif:</strong>
                  <?php foreach($chips as $c): ?>
                     <span class="filter-chip"><?= $c; ?></span>
                  <?php endforeach; ?>
                  </small>
               </div>
               <?php endif; ?>

               <!-- Info Box Rekap -->
               <div class="row" style="margin-bottom:15px">
                  <div class="info-box-col">
                     <div class="info-box rekap-info-box" style="background:#27ae60;color:#fff">
                        <span class="info-box-icon" style="background:rgba(0,0,0,0.15);color:#fff;height:100px;line-height:100px;width:100px;font-size:32px"><i class="fa fa-check"></i></span>
                        <div class="info-box-content" style="padding:12px 12px 12px 110px">
                           <span class="info-box-text" style="color:#fff;font-size:13px;font-weight:600;opacity:0.95;display:block">Hadir</span>
                           <span class="info-box-number" style="color:#fff;font-size:22px;font-weight:700;margin-top:6px;display:block"><?= isset($status_summary->total_hadir) ? (int)$status_summary->total_hadir : 0; ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="info-box-col">
                     <div class="info-box rekap-info-box" style="background:#f39c12;color:#fff">
                        <span class="info-box-icon" style="background:rgba(0,0,0,0.15);color:#fff;height:100px;line-height:100px;width:100px;font-size:32px"><i class="fa fa-clock-o"></i></span>
                        <div class="info-box-content" style="padding:12px 12px 12px 110px">
                           <span class="info-box-text" style="color:#fff;font-size:13px;font-weight:600;opacity:0.95;display:block">Terlambat</span>
                           <span class="info-box-number" style="color:#fff;font-size:22px;font-weight:700;margin-top:6px;display:block"><?= isset($status_summary->total_terlambat) ? (int)$status_summary->total_terlambat : 0; ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="info-box-col">
                     <div class="info-box rekap-info-box" style="background:#3498db;color:#fff">
                        <span class="info-box-icon" style="background:rgba(0,0,0,0.15);color:#fff;height:100px;line-height:100px;width:100px;font-size:32px"><i class="fa fa-pencil-square-o"></i></span>
                        <div class="info-box-content" style="padding:12px 12px 12px 110px">
                           <span class="info-box-text" style="color:#fff;font-size:13px;font-weight:600;opacity:0.95;display:block">Izin</span>
                           <span class="info-box-number" style="color:#fff;font-size:22px;font-weight:700;margin-top:6px;display:block"><?= isset($status_summary->total_izin) ? (int)$status_summary->total_izin : 0; ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="info-box-col">
                     <div class="info-box rekap-info-box" style="background:#5dade2;color:#fff">
                        <span class="info-box-icon" style="background:rgba(0,0,0,0.15);color:#fff;height:100px;line-height:100px;width:100px;font-size:32px"><i class="fa fa-medkit"></i></span>
                        <div class="info-box-content" style="padding:12px 12px 12px 110px">
                           <span class="info-box-text" style="color:#fff;font-size:13px;font-weight:600;opacity:0.95;display:block">Sakit</span>
                           <span class="info-box-number" style="color:#fff;font-size:22px;font-weight:700;margin-top:6px;display:block"><?= isset($status_summary->total_sakit) ? (int)$status_summary->total_sakit : 0; ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="info-box-col">
                     <div class="info-box rekap-info-box" style="background:#e74c3c;color:#fff">
                        <span class="info-box-icon" style="background:rgba(0,0,0,0.15);color:#fff;height:100px;line-height:100px;width:100px;font-size:32px"><i class="fa fa-times"></i></span>
                        <div class="info-box-content" style="padding:12px 12px 12px 110px">
                           <span class="info-box-text" style="color:#fff;font-size:13px;font-weight:600;opacity:0.95;display:block">Alfa</span>
                           <span class="info-box-number" style="color:#fff;font-size:22px;font-weight:700;margin-top:6px;display:block"><?= isset($status_summary->total_alfa) ? (int)$status_summary->total_alfa : 0; ?></span>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Data Table -->
               <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr>
                           <th width="40">No</th>
                           <th>Siswa</th>
                           <th>NIS</th>
                           <th>Kelas</th>
                           <th>Hari</th>
                           <th>Tanggal</th>
                           <th>Waktu</th>
                           <th>Status</th>
                           <th>Keterangan / Alasan</th>
                           <th>Catatan Pelajaran</th>
                           <th>Izin</th>
                           <th width="90">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php if (!empty($data_presensi)): $no = (int)$offset + 1; foreach($data_presensi as $p): ?>
                        <tr>
                           <td style="text-align:center"><?= $no++; ?></td>
                           <td>
                              <i class="fa fa-user text-muted"></i>
                              <?php if (!empty($p->id_siswa_aktif)): ?>
                                 <?= anchor('administrator/siswa_sma_aktif/view/'.$p->id_siswa_aktif.'?popup=show', $p->nama_lengkap, ['class' => 'popup-view']); ?>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center"><?= htmlspecialchars($p->nis); ?></td>
                           <td style="text-align:center"><?= htmlspecialchars($p->nama_kelas); ?></td>
                           <td style="text-align:center"><?= _ent($p->hari_absen); ?></td>
                           <td style="text-align:center"><?= _ent($p->tanggal_absen); ?></td>
                           <td style="text-align:center"><?= _ent($p->waktu_absen); ?></td>
                           <td style="text-align:center">
                              <?php
                                 $status = $p->status_absen;
                                 $color = '#999';
                                 if ($status == 'Hadir') $color = '#27ae60';
                                 elseif ($status == 'Terlambat') $color = '#f39c12';
                                 elseif ($status == 'Izin') $color = '#3498db';
                                 elseif ($status == 'Sakit') $color = '#5dade2';
                                 elseif ($status == 'Alfa' || $status == 'Tidak Hadir') $color = '#e74c3c';
                              ?>
                              <span class="label" style="background:<?= $color; ?>"><?= _ent($status); ?></span>
                           </td>
                           <td><?= _ent($p->alasan_terlambat); ?></td>
                           <td style="text-align:center">
                              <?php if (!empty($p->jml_catatan)): ?>
                                 <a href="javascript:void(0);" class="btn btn-xs btn-warning btn-catatan" data-id-siswa="<?= $p->id_siswa_aktif; ?>" data-tanggal="<?= $p->tanggal_absen; ?>" title="Lihat catatan pelajaran">
                                    <i class="fa fa-sticky-note"></i> <?= (int)$p->jml_catatan; ?> catatan
                                 </a>
                              <?php else: ?>
                                 <span class="text-muted">-</span>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php if (!empty($p->id_izin)): ?>
                                 <?= anchor('administrator/izin_siswa_sma/view/'.$p->id_izin.'?popup=show', $p->jenis_izin, ['class' => 'popup-view']); ?>
                              <?php else: ?>
                                 <span class="text-muted">-</span>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php is_allowed('presensi_sma_update', function() use ($p){?>
                              <a href="<?= site_url('administrator/presensi_sma/edit/' . $p->id); ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-edit"></i></a>
                              <?php }) ?>
                              <?php is_allowed('presensi_sma_delete', function() use ($p){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/presensi_sma/delete/' . $p->id); ?>" class="btn btn-xs btn-danger remove-data" title="Hapus"><i class="fa fa-trash"></i></a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; else: ?>
                        <tr>
                           <td colspan="12" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">
                                 <?php if (!empty($filter) && (isset($filter['q']) || isset($filter['id_tahun_ajaran']) || isset($filter['id_tingkatan']) || isset($filter['id_kelas']) || isset($filter['start_date']) || isset($filter['end_date']))): ?>
                                    Data presensi tidak ditemukan untuk filter yang dipilih.
                                 <?php else: ?>
                                    Data presensi SMA belum tersedia.
                                 <?php endif; ?>
                              </span>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <!-- Pagination -->
               <div class="row" style="margin-top:12px">
                  <div class="col-md-6">
                     <small class="text-muted">Menampilkan <?= count($data_presensi); ?> dari <?= (int)$value_counts; ?> data</small>
                  </div>
                  <div class="col-md-6 text-right">
                     <div class="dataTables_paginate paging_simple_numbers">
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- ============ MODAL DETAIL CATATAN =============== -->
<div class="modal fade" id="modal_detail_catatan" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius:5px;overflow:hidden">
         <div class="modal-header" style="background:linear-gradient(135deg,#f39c12,#e67e22);color:#fff;padding:15px 20px">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8">&times;</button>
            <h4 class="modal-title" style="font-weight:600"><i class="fa fa-sticky-note"></i> Catatan Pelajaran</h4>
         </div>
         <div class="modal-body" id="detail_catatan_content" style="padding:20px;background:#f8f9fa">
            <div class="text-center" style="padding:40px">
               <i class="fa fa-spinner fa-spin" style="font-size:40px;color:#f39c12"></i>
               <p style="margin-top:10px;color:#666">Memuat data catatan...</p>
            </div>
         </div>
         <div class="modal-footer" style="background:#fff;padding:12px 20px">
            <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
         </div>
      </div>
   </div>
</div>

<script>
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
      }, function(isConfirm){
         if (isConfirm) {
            document.location.href = url;
         }
      });
      return false;
   });

   $('.btn-catatan').click(function(){
      var id_siswa = $(this).data('id-siswa');
      var tanggal = $(this).data('tanggal');
      var content = $('#detail_catatan_content');
      content.html('<div class="text-center" style="padding:40px"><i class="fa fa-spinner fa-spin" style="font-size:40px;color:#f39c12"></i><p style="margin-top:10px;color:#666">Memuat data catatan...</p></div>');
      $('#modal_detail_catatan').modal('show');
      $.ajax({
         url: '<?= site_url('administrator/presensi_sma/detail_catatan'); ?>',
         type: 'POST',
         data: {
            id_siswa_aktif: id_siswa,
            tanggal_absen: tanggal,
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
         },
         success: function(html){
            content.html(html);
         },
         error: function(){
            content.html('<div class="alert alert-danger" style="margin:0"><i class="fa fa-exclamation-triangle"></i> Gagal memuat data catatan.</div>');
         }
      });
   });
});
</script>
