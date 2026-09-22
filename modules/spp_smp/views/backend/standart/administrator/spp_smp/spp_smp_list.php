
<link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.css">
<style>
/* Action Buttons */
.btn-action {
  border: 1px solid;
  background: transparent;
  transition: all 0.2s ease;
  margin: 2px 1px;
  padding: 4px 10px;
  font-size: 12px;
  border-radius: 3px;
}
.btn-action i { margin-right: 4px; }
.btn-action-view { border-color: #3498db; color: #3498db !important; }
.btn-action-view:hover { background: #3498db; color: #fff !important; }
.btn-action-edit { border-color: #f39c12; color: #f39c12 !important; }
.btn-action-edit:hover { background: #f39c12; color: #fff !important; }
.btn-action-delete { border-color: #e74c3c; color: #e74c3c !important; }
.btn-action-delete:hover { background: #e74c3c; color: #fff !important; }
.btn-action-pdf { border-color: #9b59b6; color: #9b59b6 !important; }
.btn-action-pdf:hover { background: #9b59b6; color: #fff !important; }
.btn-action-generate { border-color: #27ae60; color: #27ae60 !important; }
.btn-action-generate:hover { background: #27ae60; color: #fff !important; }
.btn-action-sync { border-color: #1abc9c; color: #1abc9c !important; }
.btn-action-sync:hover { background: #1abc9c; color: #fff !important; }
.btn-action-export { border-color: #2ecc71; color: #2ecc71 !important; }
.btn-action-export:hover { background: #2ecc71; color: #fff !important; }
.btn-action-tunggakan { border-color: #e67e22; color: #e67e22 !important; }
.btn-action-tunggakan:hover { background: #e67e22; color: #fff !important; }

/* Top Buttons */
.btn-top { margin-right: 5px; border-radius: 3px; }

/* Table Styling */
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; text-align: center; }
.table td { font-size: 13px; vertical-align: middle; }

/* Status Labels */
.label-lunas { background: #27ae60; }
.label-belum { background: #e74c3c; }
.label-skip { background: #f39c12; }
</style>

<script type="text/javascript">
<?php if ($this->session->flashdata('success')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('success'))); ?>
   toastr.success("<?= $msg; ?>", "Berhasil", {
      timeOut: 0,
      extendedTimeOut: 0,
      closeButton: true,
      tapToDismiss: false,
      escapeHtml: false
   });
<?php } else if ($this->session->flashdata('error')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('error'))); ?>
   toastr.error("<?= $msg; ?>", "Error", {
      timeOut: 0,
      extendedTimeOut: 0,
      closeButton: true,
      tapToDismiss: false,
      escapeHtml: false
   });
<?php } else if ($this->session->flashdata('warning')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('warning'))); ?>
   toastr.warning("<?= $msg; ?>", "Peringatan", {
      timeOut: 0,
      extendedTimeOut: 0,
      closeButton: true,
      tapToDismiss: false,
      escapeHtml: false
   });
<?php } else if ($this->session->flashdata('info')) { ?>
   toastr.info("<?= addslashes($this->session->flashdata('info')); ?>");
<?php } ?>
</script>

<!-- Content Header -->
<section class="content-header">
   <h1>
      <i class="fa fa-money"></i> <?= cclang('spp_smp') ?> <small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('spp_smp') ?></li>
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
                  <i class="fa fa-credit-card"></i> Data SPP SMP
                  <span class="label bg-yellow" style="margin-left:10px"><?= $spp_smp_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('transaksi_spp_add', function () { ?>
                  <a class="btn btn-sm btn-action btn-action-generate btn-top" id="generate-spp" title="Generate SPP" data-toggle="modal" data-target="#modal_add_new">
                     <i class="fa fa-plus-square-o"></i> Generate SPP
                  </a>
                  <?php }) ?>
                  <a class="btn btn-sm btn-action btn-action-sync btn-top" id="sync-spp" title="Synchronize SPP" data-toggle="modal" data-target="#modal_confirm_sync">
                     <i class="fa fa-refresh"></i> Sync Data
                  </a>
                  <?php is_allowed('spp_smp_export', function(){?>
                  <a class="btn btn-sm btn-action btn-action-export btn-top" title="<?= cclang('export'); ?> XLS" href="<?= site_url('administrator/spp_smp/export') . '?' . http_build_query($_GET); ?>">
                     <i class="fa fa-file-excel-o"></i> Export XLS
                  </a>
                  <a class="btn btn-sm btn-action btn-action-tunggakan btn-top" title="Export Tunggakan" href="javascript:void(0)" onclick="$('#modalExportTunggakan').modal('show')">
                     <i class="fa fa-download"></i> Tunggakan
                  </a>
                  <a class="btn btn-sm btn-action btn-action-pdf btn-top" title="<?= cclang('export'); ?> PDF" href="<?= site_url('administrator/spp_smp/export_pdf'); ?>">
                     <i class="fa fa-file-pdf-o"></i> PDF
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">

               <!-- Search Form -->
               <form name="form_spp_smp" id="form_spp_smp" action="<?= base_url('administrator/spp_smp/index'); ?>">
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-4">
                     <div class="input-group">
                        <input type="text" class="form-control" name="q" id="filter" placeholder="Cari nama, NIS, kelas..." value="<?= htmlspecialchars($this->input->get('q')); ?>">
                        <span class="input-group-btn">
                           <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Cari</button>
                           <?php if(!empty($this->input->get('q'))): ?>
                           <a class="btn btn-flat btn-default" href="<?= base_url('administrator/spp_smp'); ?>" title="Reset pencarian"><i class="fa fa-times"></i></a>
                           <?php endif; ?>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control chosen chosen-select" name="f" id="field">
                        <option value="">Semua Kolom</option>
                        <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' : ''; ?> value="nama_lengkap">Nama Siswa</option>
                        <option <?= $this->input->get('f') == 'nis' ? 'selected' : ''; ?> value="nis">NIS</option>
                        <option <?= $this->input->get('f') == 'nama_kelas' ? 'selected' : ''; ?> value="nama_kelas">Nama Kelas</option>
                        <option <?= $this->input->get('f') == 'tahun_ajaran' ? 'selected' : ''; ?> value="tahun_ajaran">Tahun Ajaran</option>
                        <option <?= $this->input->get('f') == 'nominal' ? 'selected' : ''; ?> value="nominal">Nominal</option>
                     </select>
                  </div>
                  <div class="col-md-5 text-right">
                     <?php if(!empty($this->input->get('q'))): ?>
                     <span class="text-muted" style="line-height:34px">
                        <i class="fa fa-filter"></i> Hasil pencarian: <strong>"<?= htmlspecialchars($this->input->get('q')); ?>"</strong>
                        <?php if(!empty($this->input->get('f'))): ?>
                           di kolom <strong><?= ucfirst(str_replace('_', ' ', $this->input->get('f'))); ?></strong>
                        <?php endif; ?>
                     </span>
                     <?php endif; ?>
                  </div>
               </div>

               <!-- Data Table -->
               <div class="table-responsive"> 
                  <table class="table table-bordered table-striped table-hover dataTable">
                     <thead>
                        <tr>
                           <th width="30">
                              <input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all">
                           </th>
                           <th>Nama Siswa</th>
                           <th>NIS</th>
                           <th>Kelas</th>
                           <th>Tahun Ajaran</th>
                           <th>Nominal</th>
                           <th>Jul</th>
                           <th>Agu</th>
                           <th>Sep</th>
                           <th>Okt</th>
                           <th>Nov</th>
                           <th>Des</th>
                           <th>Jan</th>
                           <th>Feb</th>
                           <th>Mar</th>
                           <th>Apr</th>
                           <th>Mei</th>
                           <th>Jun</th>
                           <th width="200">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_spp_smp">
                     <?php if($spp_smp_counts > 0): ?>
                     <?php foreach($spp_smps as $spp_smp): ?>
                        <tr>
                           <td>
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $spp_smp->id; ?>">
                           </td>
                           <td>
                              <?php if ($spp_smp->id_siswa_aktif): ?>
                                 <a href="<?= site_url('administrator/siswa_smp_aktif/view/'.$spp_smp->id_siswa_aktif.'?popup=show'); ?>" class="popup-view" title="Lihat data siswa">
                                    <i class="fa fa-user"></i> <?= _ent($spp_smp->siswa_smp_aktif_nama_lengkap); ?>
                                 </a>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center"><?= _ent($spp_smp->nis); ?></td>
                           <td style="text-align:center"><?= _ent($spp_smp->kelas); ?></td>
                           <td style="text-align:center"><?= _ent($spp_smp->tahun_ajaran); ?></td>
                           <td style="text-align:right">Rp <?= number_format($spp_smp->nominal, 0, ',', '.'); ?></td>
                           
                           <!-- Status Bulan -->
                           <?php 
                           $bulan_cols = ['juli','agustus','september','oktober','november','desember','januari','februari','maret','april','mei','juni'];
                           foreach ($bulan_cols as $bln):
                              $val = $spp_smp->$bln;
                              if ($val === 'Keluar') {
                                 // Siswa keluar
                                 $icon = '<span style="color:#e74c3c;font-size:22px" title="Keluar"><i class="fa fa-times-circle"></i></span>';
                                 $tgl = '<small style="color:#e74c3c;font-weight:bold">Keluar</small>';
                                 $title = 'Siswa keluar';
                              } else if ($val === '-') {
                                 // Skip / tidak berlaku
                                 $icon = '<span class="label" style="background:#f39c12"><i class="fa fa-minus"></i></span>';
                                 $tgl = '<small class="text-muted">-</small>';
                                 $title = 'Tidak berlaku';
                              } else if ($val == null || $val === '') {
                                 // Belum lunas
                                 $icon = '<span class="label label-belum"><i class="fa fa-times"></i></span>';
                                 $tgl = '<small class="text-muted">-</small>';
                                 $title = 'Belum lunas';
                              } else {
                                 // Sudah lunas
                                 $icon = '<span class="label label-lunas"><i class="fa fa-check"></i></span>';
                                 if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $val, $matches)) {
                                    $tgl = '<small>' . date('d/m/Y', strtotime($matches[1])) . '</small>';
                                 } else {
                                    $tgl = '<small>' . $val . '</small>';
                                 }
                                 $title = 'Lunas: ' . $val;
                              }
                           ?>
                           <td style="text-align:center" title="<?= $title; ?>"><?= $icon; ?><br><?= $tgl; ?></td>
                           <?php endforeach; ?>

                           <!-- Aksi -->
                           <td style="white-space:nowrap;text-align:center">
                              <?php is_allowed('spp_smp_view', function() use ($spp_smp){?>
                              <a href="<?= site_url('administrator/spp_smp/single_pdf/' . $spp_smp->id); ?>" class="btn btn-action btn-action-pdf btn-sm" title="Download PDF">
                                 <i class="fa fa-file-pdf-o"></i> PDF
                              </a>
                              <a href="<?= site_url('administrator/spp_smp/view/' . $spp_smp->id); ?>" class="btn btn-action btn-action-view btn-sm" title="Lihat detail">
                                 <i class="fa fa-eye"></i> Detail
                              </a><br>
                              <?php }) ?>
                              <?php is_allowed('spp_smp_update', function() use ($spp_smp){?>
                              <a href="<?= site_url('administrator/spp_smp/edit/' . $spp_smp->id); ?>" class="btn btn-action btn-action-edit btn-sm" title="Edit data">
                                 <i class="fa fa-edit"></i> Edit
                              </a>
                              <?php }) ?>
                              <?php is_allowed('spp_smp_delete', function() use ($spp_smp){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/spp_smp/delete/' . $spp_smp->id); ?>" class="btn btn-action btn-action-delete btn-sm remove-data" title="Hapus data">
                                 <i class="fa fa-trash"></i> Hapus
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="19" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">
                                 <?php if(!empty($this->input->get('q'))): ?>
                                    Data SPP tidak ditemukan untuk pencarian "<?= htmlspecialchars($this->input->get('q')); ?>"
                                 <?php else: ?>
                                    Data SPP SMP belum tersedia
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

<!-- ============ MODAL GENERATE SPP =============== -->
<div class="modal fade" id="modal_add_new" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#27ae60;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-plus-square-o"></i> Generate SPP</h4>
         </div>
         <form action="<?= base_url('administrator/spp_smp/add_save/'); ?>" method="post" class="form-horizontal" id="form-generate">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Siswa</label>
                  <div class="col-xs-8">
                     <select class="form-control chosen chosen-select-deselect" name="id_siswa_smp_aktif[]" id="id_siswa_smp_aktif" data-placeholder="Pilih siswa (opsional)" multiple>
                        <option value=""></option>
                        <?php foreach (db_get_all_data('siswa_smp_aktif') as $row) : ?>
                           <option value="<?= $row->id_siswa_smp_aktif ?>"><?= $row->nama_lengkap; ?></option>
                        <?php endforeach; ?>
                     </select>
                     <small class="text-muted">Pilih siswa atau kelas (salah satu)</small>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Kelas</label>
                  <div class="col-xs-8">
                     <select class="form-control chosen chosen-select-deselect" name="id_kelas" id="id_kelas" data-placeholder="Pilih kelas (opsional)">
                        <option value=""></option>
                        <?php foreach (db_get_all_data('kelas_smp') as $row) : ?>
                           <option value="<?= $row->id_kelas_smp ?>"><?= $row->label; ?></option>
                        <?php endforeach; ?>
                     </select>
                     <small class="text-muted">Pilih siswa atau kelas (salah satu)</small>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Custom Nominal SPP <span class="text-muted">(Opsional)</span></label>
                  <div class="col-xs-8">
                     <input type="number" class="form-control" name="custom_nominal_spp" id="custom_nominal_spp" placeholder="Kosongkan jika menggunakan nominal default" min="0" step="1000">
                     <small class="text-muted">Jika diisi, nominal akan disesuaikan dengan spp_type masing-masing siswa. Pastikan data spp_type siswa sudah benar.</small>
                     <div class="help-block" style="margin-top:5px;padding:8px;background:#f8f9fa;border-left:3px solid #27ae60;font-size:12px">
                        <b>Skema Perhitungan:</b><br>
                        • <b>FULL</b> → Nominal penuh (contoh: input 1jt → tagihan 1jt)<br>
                        • <b>HALF</b> → 50% dari input (contoh: input 1jt → tagihan 500rb)<br>
                        • <b>FREE</b> → Rp 0 (contoh: input 1jt → tagihan 0)
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Tahun Ajaran <i class="required">*</i></label>
                  <div class="col-xs-8">
                     <select class="form-control chosen chosen-select-deselect" name="tahun_ajaran" id="tahun_ajaran" data-placeholder="Pilih Tahun Ajaran" required>
                        <option value=""></option>
                        <?php foreach (db_get_all_data('tahun_ajaran') as $row) : ?>
                           <option value="<?= $row->code ?>"><?= $row->label; ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label class="col-xs-3 control-label">Bulan</label>
                  <div class="col-xs-8">
                     <div class="row">
                        <div class="col-xs-4">
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="01"> Januari</label><br>
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="02"> Februari</label><br>
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="03"> Maret</label><br>
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="04"> April</label>
                        </div>
                        <div class="col-xs-4">
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="05"> Mei</label><br>
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="06"> Juni</label><br>
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="07"> Juli</label><br>
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="08"> Agustus</label>
                        </div>
                        <div class="col-xs-4">
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="09"> September</label><br>
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="10"> Oktober</label><br>
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="11"> November</label><br>
                           <label class="checkbox-inline"><input name="bulan[]" type="checkbox" class="checkbox bulan" value="12"> Desember</label>
                        </div>
                     </div>
                     <div style="margin-top:5px">
                        <label class="checkbox-inline" for="selectAllBulan">
                           <input onClick="toggle(this)" type="checkbox" id="selectAllBulan"> <b>Pilih Semua Bulan</b>
                        </label>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
               <button class="btn btn-success btn-flat"><i class="fa fa-play"></i> Generate</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- ============ MODAL CONFIRM SYNC =============== -->
<div class="modal fade" id="modal_confirm_sync" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#1abc9c;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-refresh"></i> Sinkronisasi Data SPP</h4>
         </div>
         <form action="<?= base_url('administrator/spp_smp/sync_spp_data/'); ?>" method="post" id="form-sync">
            <div class="modal-body">
               <div id="loader" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:url('<?= base_url()."/uploads/loader.gif"; ?>') no-repeat center center #ffffff;z-index:9999">
                  <div class="text-center" style="padding-top:200px"><h2>Mohon tunggu sebentar...</h2></div>
               </div>
               <div class="text-center" style="padding:20px">
                  <i class="fa fa-refresh" style="font-size:50px;color:#1abc9c"></i>
                  <h4>Apakah anda yakin ingin melakukan sinkronisasi?</h4>
                  <p class="text-muted">Proses akan membandingkan data SPP dengan riwayat transaksi pembayaran.</p>
                  <small class="text-muted"><i class="fa fa-clock-o"></i> Proses ini mungkin memakan waktu beberapa menit.</small>
               </div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
               <button class="btn btn-success btn-flat"><i class="fa fa-refresh"></i> Sinkron Sekarang</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- ============ MODAL EXPORT TUNGGAKAN =============== -->
<div class="modal fade" id="modalExportTunggakan" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header" style="background:#e67e22;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-download"></i> Export Rekapitulasi Tunggakan SPP</h4>
         </div>
         <div class="modal-body">
            <div class="form-group">
               <label><i class="fa fa-calendar"></i> Tahun Ajaran</label>
               <select class="form-control" id="filter_tahun_ajaran" name="filter_tahun_ajaran">
                  <option value="">-- Semua Tahun Ajaran --</option>
                  <?php 
                  $list_ta = $this->mymodel->withquery("SELECT id_tahun_ajaran, label FROM tahun_ajaran ORDER BY label DESC", "result");
                  foreach ($list_ta as $ta): ?>
                     <option value="<?= $ta->id_tahun_ajaran ?>"><?= $ta->label ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
            <div class="form-group">
               <label><i class="fa fa-calendar-o"></i> Pilih Bulan yang Dihitung</label>
               <div>
                  <label class="checkbox-inline">
                     <input type="checkbox" id="checkAllBulan" onclick="toggleAllBulan(this)"> <b>Semua</b>
                  </label>
               </div>
               <div class="row" style="margin-top:8px;">
                  <?php 
                  $bulan_opts = ['juli','agustus','september','oktober','november','desember','januari','februari','maret','april','mei','juni'];
                  foreach ($bulan_opts as $b): ?>
                     <div class="col-xs-4">
                        <label class="checkbox-inline">
                           <input type="checkbox" class="cb-bulan" name="bulan_export[]" value="<?= $b ?>"> <?= ucfirst($b) ?>
                        </label>
                     </div>
                  <?php endforeach; ?>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
            <button type="button" class="btn btn-warning btn-flat" onclick="doExportTunggakan()">
               <i class="fa fa-download"></i> Export
            </button>
         </div>
      </div>
   </div>
</div>

<!-- Page script -->
<script>
function toggleAllBulan(el) {
   $('.cb-bulan').prop('checked', el.checked);
}

$(document).on('change', '.cb-bulan', function() {
   var total = $('.cb-bulan').length;
   var checked = $('.cb-bulan:checked').length;
   $('#checkAllBulan').prop('checked', total === checked);
});

function doExportTunggakan() {
   var bulan_checked = [];
   $('.cb-bulan:checked').each(function() {
      bulan_checked.push($(this).val());
   });

   if (bulan_checked.length === 0) {
      swal('Peringatan', 'Pilih minimal 1 bulan!', 'warning');
      return;
   }

   var tahun = $('#filter_tahun_ajaran').val();
   var url = '<?= site_url("administrator/spp_smp/export_tunggakan") ?>'
            + '?bulan=' + bulan_checked.join(',')
            + '&id_tahun_ajaran=' + tahun;

   window.location.href = url;
   $('#modalExportTunggakan').modal('hide');
}

function toggle(source) {
   var checkboxes = document.getElementsByName('bulan[]');
   for (var i = 0; i < checkboxes.length; i++) {
      checkboxes[i].checked = source.checked;
   }
}

$(document).ready(function(){
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
      var serialize_bulk = $('#form_spp_smp').serialize();

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
               document.location.href = BASE_URL + '/administrator/spp_smp/delete?' + serialize_bulk;      
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

   // Sync form submit - show loader
   $('#form-sync').submit(function(){
      $('#loader').show();
   });
});
</script>
