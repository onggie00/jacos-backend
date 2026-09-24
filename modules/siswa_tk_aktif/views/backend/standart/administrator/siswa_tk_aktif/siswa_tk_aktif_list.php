
<link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.css">
<style>
/* Top Buttons */
.btn-top { margin-right: 5px; border-radius: 3px; }

/* Table Styling */
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; text-align: center; }
.table td { font-size: 13px; vertical-align: middle; }

/* Status Labels */
.label-aktif { background: #27ae60; }
.label-nonaktif { background: #e74c3c; }

/* Action Buttons */
.btn-aksi { margin: 2px 1px; padding: 4px 0; font-size: 11px; border-radius: 3px; width: 48%; display: inline-block; text-align: center; }
.btn-aksi i { margin-right: 3px; }
.btn-aksi-full { margin: 2px 1px; padding: 4px 0; font-size: 11px; border-radius: 3px; width: 98%; display: block; text-align: center; }
.btn-aksi-full i { margin-right: 3px; }
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
   <h1><i class="fa fa-users"></i> <?= cclang('siswa_tk_aktif') ?> <small><?= cclang('list_all'); ?></small></h1>
   <ol class="breadcrumb"><li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li><li class="active"><?= cclang('siswa_tk_aktif') ?></li></ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">

            <!-- Box Header -->
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-graduation-cap"></i> Data Siswa SD Aktif
                  <span class="label bg-yellow" style="margin-left:10px"><?= $siswa_tk_aktif_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('siswa_tk_aktif_add', function () { ?>
                  <a class="btn btn-sm btn-action btn-action-add btn-top" title="<?= cclang('add_new_button', [cclang('siswa_tk_aktif')]); ?> (Ctrl+a)" href="<?= site_url('administrator/siswa_tk_aktif/add'); ?>">
                     <i class="fa fa-plus"></i> Tambah Siswa
                  </a>
                  <a class="btn btn-sm btn-action btn-action-add btn-top" title="Tambah Raport" href="<?= site_url('administrator/siswa_tk_aktif/add_raport'); ?>">
                     <i class="fa fa-plus"></i> Tambah Raport
                  </a>
                  <?php }) ?>
                  <a class="btn btn-sm btn-action btn-action-generate btn-top" title="Generate NIS" data-toggle="modal" data-target="#modal_generate">
                     <i class="fa fa-users"></i> Generate NIS
                  </a>
                  <a class="btn btn-sm btn-action btn-action-import btn-top" title="Import Data Siswa" data-toggle="modal" data-target="#modal_import">
                     <i class="fa fa-upload"></i> Import Siswa
                  </a>
                  <a class="btn btn-sm btn-action btn-action-import btn-top" title="Import Buat Baru (data historis)" href=".site_url('administrator/siswa_tk_aktif/import_create')." style="color:#e67e22">
                     <i class="fa fa-file-excel-o"></i> Import Buat Baru
                  </a>
                  <?php is_allowed('siswa_tk_aktif_add', function () { ?>
                  <a class="btn btn-sm btn-action btn-action-import btn-top" title="Import Raport" data-toggle="modal" data-target="#modal_import_raport">
                     <i class="fa fa-file-pdf-o"></i> Import Raport
                  </a>
                  <?php }) ?>
                  <?php is_allowed('siswa_tk_aktif_export', function(){?>
                  <a class="btn btn-sm btn-action btn-action-export btn-top" title="<?= cclang('export'); ?> XLS" href="<?= site_url('administrator/siswa_tk_aktif/export') . '?' . http_build_query($_GET); ?>">
                     <i class="fa fa-file-excel-o"></i> XLS
                  </a>
                  <a class="btn btn-sm btn-action btn-action-export btn-top" title="Export Data Lengkap" data-toggle="modal" data-target="#modal_export_lengkap">
                     <i class="fa fa-file-excel-o"></i> Export Data Lengkap
                  </a>
                  <a class="btn btn-sm btn-action btn-action-pdf btn-top" title="<?= cclang('export'); ?> PDF" href="<?= site_url('administrator/siswa_tk_aktif/export_pdf'); ?>">
                     <i class="fa fa-file-pdf-o"></i> PDF
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">

               <!-- Advanced Filter Builder -->
               <form name="form_siswa_tk_aktif" id="form_siswa_tk_aktif" action="<?= base_url('administrator/siswa_tk_aktif/index'); ?>">
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
                     <a class="btn btn-flat btn-default" href="<?= base_url('administrator/siswa_tk_aktif'); ?>"><i class="fa fa-times"></i> Reset Semua</a>
                     <div style="margin-left:auto;display:flex;gap:8px;align-items:center">
                        <select class="form-control input-sm" name="s" id="sort" style="width:130px">
                           <option value="">Urutkan</option>
                           <option <?= $this->input->get('s') == 'nama_lengkap' ? 'selected' : ''; ?> value="nama_lengkap">Nama</option>
                           <option <?= $this->input->get('s') == 'nis' ? 'selected' : ''; ?> value="nis">NIS</option>
                           <option <?= $this->input->get('s') == 'id_siswa_tk_aktif' ? 'selected' : ''; ?> value="id_siswa_tk_aktif">ID</option>
                        </select>
                        <select class="form-control input-sm" name="d" id="sort_type" style="width:80px">
                           <option <?= $this->input->get('d') == 'asc' ? 'selected' : ''; ?> value="asc">A-Z</option>
                           <option <?= $this->input->get('d') == 'desc' ? 'selected' : ''; ?> value="desc">Z-A</option>
                        </select>
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
                  <table class="table table-bordered table-striped table-hover dataTable">
                     <thead>
                        <tr>
                           <th width="30"><input type="checkbox" class="flat-red" id="check_all" name="check_all"></th>
                           <th>Nama Siswa</th>
                           <th>NIS</th>
                           <th>Tahun Ajaran</th>
                           <th>Kelas</th>
                           <th>SPP Type</th>
                           <th>SPP Custom</th>
                           <th>Acc Ujian</th>
                           <th>Status</th>
                           <th>Status Akun</th>
                           <th>Raport</th>
                           <th width="280">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_siswa_tk_aktif">
                     <?php if($siswa_tk_aktif_counts > 0): ?>
                     <?php foreach($siswa_tk_aktifs as $siswa_tk_aktif): ?>
                        <tr>
                           <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $siswa_tk_aktif->id_siswa_tk_aktif; ?>"></td>
                           <td>
                              <i class="fa fa-user"></i> <?= _ent($siswa_tk_aktif->nama_lengkap); ?>
                           </td>
                           <td style="text-align:center"><?= _ent($siswa_tk_aktif->nis); ?></td>
                           <td style="text-align:center">
                              <?php if ($siswa_tk_aktif->id_tahun_ajaran): ?>
                                 <a href="<?= site_url('administrator/tahun_ajaran/view/'.$siswa_tk_aktif->id_tahun_ajaran.'?popup=show'); ?>" class="popup-view" title="Lihat tahun ajaran">
                                    <?= $siswa_tk_aktif->tahun_ajaran_label; ?>
                                 </a>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php if ($siswa_tk_aktif->id_kelas): ?>
                                 <a href="<?= site_url('administrator/kelas_tk/view/'.$siswa_tk_aktif->id_kelas.'?popup=show'); ?>" class="popup-view" title="Lihat kelas">
                                    <?= $siswa_tk_aktif->kelas_tk_label; ?>
                                 </a>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php 
                              $spp_type = $siswa_tk_aktif->spp_type ?: 'FULL';
                              if ($spp_type == 'FULL'): ?>
                                 <span class="label" style="background:#27ae60">FULL</span>
                              <?php elseif ($spp_type == 'HALF'): ?>
                                 <span class="label" style="background:#f39c12">HALF</span>
                              <?php else: ?>
                                 <span class="label" style="background:#3498db">FREE</span>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:right">
                              <?php if (!empty($siswa_tk_aktif->spp_custom) && $siswa_tk_aktif->spp_custom > 0): ?>
                                 Rp <?= number_format($siswa_tk_aktif->spp_custom, 0, ',', '.'); ?>
                              <?php else: ?>
                                 <small class="text-muted">-</small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php if ($siswa_tk_aktif->acc_ujian == 1): ?>
                                 <span class="label" style="background:#27ae60"><i class="fa fa-check"></i> Boleh</span>
                              <?php else: ?>
                                 <span class="label" style="background:#e74c3c"><i class="fa fa-times"></i> Tidak</span>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php if ($siswa_tk_aktif->is_active == 1): ?>
                                 <span class="label label-aktif"><i class="fa fa-check"></i> Aktif</span>
                              <?php else: ?>
                                 <span class="label label-nonaktif"><i class="fa fa-times"></i> Non-Aktif</span>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center;font-size:11px">
                              <?php if (!empty($siswa_tk_aktif->deleted_at_siswa)): ?>
                                 <span class="label label-nonaktif"><i class="fa fa-user-times"></i> Siswa Terhapus</span>
                                 <br><small class="text-danger"><i class="fa fa-calendar"></i> <?= date('d M Y', strtotime($siswa_tk_aktif->deleted_at_siswa)); ?></small>
                                 <?php is_allowed('siswa_tk_aktif_update', function() use ($siswa_tk_aktif){ ?>
                                 <br><a href="<?= site_url('administrator/siswa_tk_aktif/restore/'.$siswa_tk_aktif->id_siswa_tk_aktif); ?>" class="btn btn-xs btn-success restore-link" style="margin-top:3px" title="Pulihkan akun siswa"><i class="fa fa-undo"></i> Pulihkan</a>
                                 <?php }) ?>
                              <?php else: ?>
                                 <span class="label label-aktif"><i class="fa fa-user"></i> Siswa Aktif</span>
                              <?php endif; ?>
                              <hr style="margin:5px 0">
                              <?php if (!empty($siswa_tk_aktif->deleted_at_ortu)): ?>
                                 <span class="label label-nonaktif"><i class="fa fa-user-times"></i> Ortu Terhapus</span>
                                 <br><small class="text-danger"><i class="fa fa-calendar"></i> <?= date('d M Y', strtotime($siswa_tk_aktif->deleted_at_ortu)); ?></small>
                                 <?php is_allowed('siswa_tk_aktif_update', function() use ($siswa_tk_aktif){ ?>
                                 <br><a href="<?= site_url('administrator/siswa_tk_aktif/restore_ortu/'.$siswa_tk_aktif->id_siswa_tk_aktif); ?>" class="btn btn-xs btn-success restore-link" style="margin-top:3px" title="Pulihkan akun ortu"><i class="fa fa-undo"></i> Pulihkan</a>
                                 <?php }) ?>
                              <?php else: ?>
                                 <span class="label label-aktif"><i class="fa fa-users"></i> Ortu Aktif</span>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php if (!empty($siswa_tk_aktif->file_raport)): ?>
                                 <?php if (is_image($siswa_tk_aktif->file_raport)): ?>
                                    <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/siswa_tk_aktif/' . $siswa_tk_aktif->file_raport; ?>">
                                       <img src="<?= BASE_URL . 'uploads/siswa_tk_aktif/' . $siswa_tk_aktif->file_raport; ?>" alt="raport" width="40px" style="border-radius:3px">
                                    </a>
                                 <?php else: ?>
                                    <a href="<?= BASE_URL . 'uploads/siswa_tk_aktif/' . $siswa_tk_aktif->file_raport; ?>" title="Download raport">
                                       <img src="<?= get_icon_file($siswa_tk_aktif->file_raport); ?>" alt="raport" width="30px">
                                    </a>
                                 <?php endif; ?>
                              <?php else: ?>
                                 <small class="text-muted"><i class="fa fa-file-o"></i> -</small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <div style="margin-bottom:3px">
                                 <?php is_allowed('siswa_tk_aktif_view', function() use ($siswa_tk_aktif){?>
                                 <button type="button" class="btn btn-sm btn-info btn-aksi btn_detail_siswa" data-id="<?= $siswa_tk_aktif->id_siswa_tk_aktif; ?>" title="Lihat detail">
                                    <i class="fa fa-eye"></i> Detail
                                 </button>
                                 <?php }) ?>
                                 <?php is_allowed('siswa_tk_aktif_update', function() use ($siswa_tk_aktif){?>
                                 <a href="<?= site_url('administrator/siswa_tk_aktif/edit/'.$siswa_tk_aktif->id_siswa_tk_aktif); ?>" class="btn btn-sm btn-warning btn-aksi" title="Edit data">
                                    <i class="fa fa-edit"></i> Edit
                                 </a>
                                 <?php }) ?>
                              </div>
                              <div style="margin-bottom:3px">
                                 <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-aksi btn_ubah_password" title="Ubah Password Siswa" data-toggle="modal" data-target="#modal_update_password" data-id="<?= $siswa_tk_aktif->id_siswa_tk_aktif; ?>" data-email="<?= $siswa_tk_aktif->email_ms_office; ?>">
                                    <i class="fa fa-key"></i> Ubah Pass Siswa
                                 </a>
                                 <a href="javascript:void(0)" class="btn btn-sm btn-success btn-aksi btn_ubah_password" title="Ubah Password Ortu" data-toggle="modal" data-target="#modal_update_password" data-id="<?= $siswa_tk_aktif->id_siswa_tk_aktif; ?>" data-email="<?= $siswa_tk_aktif->email_ms_office_ortu; ?>">
                                    <i class="fa fa-key"></i> Ubah Pass Ortu
                                 </a>
                              </div>
                              <?php is_allowed('siswa_tk_aktif_delete', function() use ($siswa_tk_aktif){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/siswa_tk_aktif/delete/'.$siswa_tk_aktif->id_siswa_tk_aktif); ?>" class="btn btn-sm btn-danger btn-aksi-full remove-data" title="Hapus data">
                                 <i class="fa fa-trash"></i> Hapus
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="11" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">
                                 <?php if(!empty($multi_filters)): ?>
                                    Data siswa tidak ditemukan untuk filter yang dipilih
                                 <?php elseif(!empty($this->input->get('q'))): ?>
                                    Data siswa tidak ditemukan untuk pencarian "<?= htmlspecialchars($this->input->get('q')); ?>"
                                 <?php else: ?>
                                    Data siswa SD aktif belum tersedia
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

<!-- ============ MODAL GENERATE NIS =============== -->
<div class="modal fade" id="modal_generate" tabindex="-1" role="dialog">
   <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header" style="background:#e67e22;color:#fff">
         <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
         <h4 class="modal-title"><i class="fa fa-users"></i> Generate NIS & Email Ms. Office</h4>
      </div>
      <form action="<?= base_url('administrator/siswa_tk_aktif/generate_nis/'); ?>" method="post">
         <div class="modal-body">
            <div class="form-group">
               <label class="control-label col-xs-3">Tahun Ajaran</label>
               <div class="col-xs-8">
                  <select name="id_tahun_ajaran" class="form-control chosen chosen-select">
                     <?php 
                        $list_tahun_ajaran = $this->mymodel->withquery("select * from tahun_ajaran","result");
                        foreach ($list_tahun_ajaran as $key => $value) {
                           echo "<option value='".$value->id_tahun_ajaran."'>".$value->label."</option>";
                        }
                     ?>
                  </select>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
            <button type="submit" class="btn btn-warning btn-flat"><i class="fa fa-play"></i> Generate</button>
         </div>
      </form>
   </div></div>
</div>

<!-- ============ MODAL IMPORT DATA SISWA =============== -->
<div class="modal fade" id="modal_import" tabindex="-1" role="dialog">
   <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header" style="background:#1abc9c;color:#fff">
         <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
         <h4 class="modal-title"><i class="fa fa-upload"></i> Import Data Siswa</h4>
      </div>
      <form action="<?= base_url('administrator/siswa_tk_aktif/import_siswa/'); ?>" method="post" enctype="multipart/form-data">
         <div class="modal-body">
            <div class="form-group">
               <label class="control-label col-xs-3">File Excel</label>
               <div class="col-xs-8">
                  <input name="file_import" class="form-control" type="file" accept=".xls,.xlsx" required>
               </div>
            </div>
            <div class="clearfix"></div>
            <div style="margin-top:10px;padding:10px;background:#f8f9fa;border-radius:3px">
               <small><i class="fa fa-info-circle text-info"></i> <b>Catatan:</b></small><br>
               <small>- Pastikan seluruh siswa telah mempunyai NIS</small><br>
               <small>- Format file sesuai dengan format export Excel</small><br>
               <small>- Import akan mengubah data berdasarkan NIS siswa</small>
            </div>
         </div>
         <div class="modal-footer">
            <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
            <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-upload"></i> Import</button>
         </div>
      </form>
   </div></div>
</div>

<!-- ============ MODAL IMPORT RAPORT =============== -->
<div class="modal fade" id="modal_import_raport" tabindex="-1" role="dialog">
   <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header" style="background:#9b59b6;color:#fff">
         <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
         <h4 class="modal-title"><i class="fa fa-file-pdf-o"></i> Import Raport Siswa</h4>
      </div>
      <form action="<?= base_url('administrator/siswa_tk_aktif/import_raport_siswa/'); ?>" method="post" enctype="multipart/form-data">
         <div class="modal-body">
            <div class="form-group">
               <label class="control-label col-xs-3">File Raport</label>
               <div class="col-xs-8">
                  <input name="file_raport[]" class="form-control" type="file" accept=".pdf,.doc,.docs,.docx" required multiple>
               </div>
            </div>
            <div class="clearfix"></div>
            <div style="margin-top:10px;padding:10px;background:#f8f9fa;border-radius:3px">
               <small><i class="fa fa-info-circle text-info"></i> <b>Catatan:</b></small><br>
               <small>- Pastikan nama file sesuai dengan NIS siswa</small><br>
               <small>- Format file: PDF, DOC, DOCX</small><br>
               <small>- Maksimal 40 file sekaligus</small>
            </div>
         </div>
         <div class="modal-footer">
            <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
            <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-upload"></i> Upload</button>
         </div>
      </form>
   </div></div>
</div>

<!-- ============ MODAL UPDATE PASSWORD =============== -->
<div class="modal fade" id="modal_update_password" tabindex="-1" role="dialog">
   <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header" style="background:#3498db;color:#fff">
         <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
         <h4 class="modal-title"><i class="fa fa-key"></i> Ubah / Reset Password</h4>
      </div>
      <form action="<?= base_url('administrator/siswa_tk_aktif/update_password/'); ?>" method="post">
         <div class="modal-body">
            <div class="form-group">
               <label class="control-label col-xs-4">User Microsoft ID</label>
               <div class="col-xs-7">
                  <input name="user_microsoft_id" id="user_microsoft_id" class="form-control" type="text" readonly/>
               </div>
            </div>
            <div class="form-group">
               <label class="control-label col-xs-4">User Microsoft Email</label>
               <div class="col-xs-7">
                  <input name="user_microsoft_mail" id="user_microsoft_mail" class="form-control" type="text" readonly/>
               </div>
            </div>
            <div class="form-group">
               <label class="control-label col-xs-4">Password Baru</label>
               <div class="col-xs-7">
                  <input name="password" class="form-control" type="password" required>
               </div>
            </div>
            <div style="padding:10px;background:#f8f9fa;border-radius:3px">
               <small><i class="fa fa-info-circle text-info"></i> <b>Catatan:</b></small><br>
               <small>- Reset Password akan diisi otomatis <b>Labschool123456</b></small><br>
               <small>- Jika token kadaluarsa, silahkan login ulang</small>
            </div>
         </div>
         <div class="modal-footer">
            <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
            <button class="btn btn-primary btn-flat"><i class="fa fa-save"></i> Ubah</button>
            <a id="reset_password" class="btn btn-danger btn-flat" href=""><i class="fa fa-undo"></i> Reset</a>
         </div>
      </form>
   </div></div>
</div>

<!-- ============ MODAL DETAIL SISWA =============== -->
<div class="modal fade" id="modal_detail_siswa" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg"><div class="modal-content" style="border-radius:5px;overflow:hidden">
      <div class="modal-header" style="background:linear-gradient(135deg,#3498db,#2980b9);color:#fff;padding:15px 20px">
         <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8">&times;</button>
         <h4 class="modal-title" style="font-weight:600"><i class="fa fa-user"></i> Detail Siswa</h4>
      </div>
      <div class="modal-body" id="detail_siswa_content" style="padding:20px;background:#f8f9fa">
         <div class="text-center" style="padding:40px">
            <i class="fa fa-spinner fa-spin" style="font-size:40px;color:#3498db"></i>
            <p style="margin-top:10px;color:#666">Memuat data siswa...</p>
         </div>
      </div>
      <div class="modal-footer" style="background:#fff;padding:12px 20px">
         <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
      </div>
   </div></div>
</div>

<!-- ============ MODAL EXPORT DATA LENGKAP =============== -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
   /* teks siswa terpilih (chip) & teks select = hitam */
   #modal_export_lengkap .select2-container--default .select2-selection--multiple .select2-selection__choice,
   #modal_export_lengkap .select2-selection__rendered {
      color: var(--labs-text) !important;
   }
   #modal_export_lengkap .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
      color: var(--labs-text-muted);
      margin-right: 4px;
   }
   #modal_export_lengkap .select2-container--default .select2-selection--multiple {
      border-color: var(--labs-border);
      border-radius: var(--labs-radius-sm);
   }
   #modal_export_lengkap .select2-container--default.select2-container--focus .select2-selection--multiple {
      border-color: var(--labs-primary);
      box-shadow: 0 0 0 3px rgba(194, 65, 12, 0.15);
   }
   /* card grup kolom */
   #modal_export_lengkap .labs-export-card {
      background: var(--labs-bg-card);
      border: 1px solid var(--labs-border);
      border-radius: var(--labs-radius);
      padding: 12px;
      margin-bottom: 10px;
      box-shadow: var(--labs-shadow-sm);
   }
   #modal_export_lengkap .labs-export-card__head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 8px;
      padding-bottom: 8px;
      border-bottom: 1px solid var(--labs-border);
   }
   #modal_export_lengkap .labs-export-card__title {
      font-weight: 600;
      font-size: 13px;
      color: var(--labs-text);
   }
   #modal_export_lengkap .labs-export-cols {
      max-height: 220px;
      overflow-y: auto;
   }
   #modal_export_lengkap .labs-export-cols label {
      display: flex;
      align-items: center;
      gap: 6px;
      font-weight: 400;
      margin: 1px 0;
      padding: 3px 8px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 12px;
      color: var(--labs-text);
   }
   #modal_export_lengkap .labs-export-cols label:hover {
      background: var(--labs-bg-soft);
   }
   #modal_export_lengkap .labs-export-cols input[type="checkbox"] {
      margin: 0;
   }
</style>
<div class="modal fade labs-modal" id="modal_export_lengkap" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title"><i class="fa fa-file-excel-o"></i> Export Data Lengkap</h4>
      </div>
      <form action="<?= base_url('administrator/siswa_tk_aktif/export_lengkap'); ?>" method="post">
         <div class="modal-body">
            <div class="row">
               <div class="col-sm-6">
                  <div class="form-group">
                     <label class="control-label labs-fw-semi">Tingkatan</label>
                     <select name="id_tingkatan" id="export_tingkatan" class="form-control">
                        <option value="">-- Semua Tingkatan --</option>
                        <?php foreach (db_get_all_data('tingkatan_sd') as $t): ?>
                        <option value="<?= $t->id_tingkatan_sd ?>"><?= $t->label ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="form-group">
                     <label class="control-label labs-fw-semi">Kelas</label>
                     <select name="id_kelas" id="export_kelas" class="form-control" disabled>
                        <option value="">-- Pilih Tingkatan Dulu --</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="form-group">
               <label class="control-label labs-fw-semi">Siswa</label>
               <select name="siswa[]" id="export_siswa" class="form-control" multiple style="width:100%">
                  <option value="all">-- Semua Siswa --</option>
               </select>
               <small class="labs-text-muted labs-fs-xs">Ketik untuk mencari siswa. Pilih "-- Semua Siswa --" untuk export semua sesuai filter tingkatan/kelas.</small>
            </div>
            <div class="row">
               <?php $export_defaults = array('nama_lengkap', 'nis', 'kelas'); ?>
               <?php foreach ($export_lengkap_cols as $group): ?>
               <div class="col-sm-6">
                  <div class="labs-export-card">
                     <div class="labs-export-card__head">
                        <span class="labs-export-card__title"><?= $group['title'] ?></span>
                        <span class="pull-right">
                           <button type="button" class="labs-btn labs-btn--default labs-btn--xs btn_export_checkall" data-mode="check"><i class="fa fa-check-square-o"></i> Semua</button>
                           <button type="button" class="labs-btn labs-btn--default labs-btn--xs btn_export_uncheckall" data-mode="uncheck"><i class="fa fa-square-o"></i> Batal</button>
                        </span>
                     </div>
                     <div class="labs-export-cols">
                        <?php foreach ($group['cols'] as $ckey => $cdef): ?>
                        <label>
                           <input type="checkbox" name="cols[]" value="<?= $ckey ?>" class="export-col-cb" <?= in_array($ckey, $export_defaults) ? 'checked' : '' ?>> <?= $cdef[0] ?>
                        </label>
                        <?php endforeach; ?>
                     </div>
                  </div>
               </div>
               <?php endforeach; ?>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="labs-btn labs-btn--ghost" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
            <button type="submit" class="labs-btn labs-btn--success"><i class="fa fa-file-excel-o"></i> Export</button>
         </div>
      </form>
   </div></div>
</div>

<!-- Page script -->
<script>
// ===== EXPORT DATA LENGKAP =====
var exportKelasData = <?= json_encode(db_get_all_data('kelas_tk')); ?>;

$('#export_siswa').select2({
   dropdownParent: $('#modal_export_lengkap'),
   placeholder: 'Cari / pilih siswa',
   allowClear: true,
   ajax: {
      url: '<?= base_url('administrator/siswa_tk_aktif/get_siswa_options'); ?>',
      dataType: 'json',
      delay: 250,
      data: function (params) {
         return {
            q: params.term || '',
            id_tingkatan: $('#export_tingkatan').val(),
            id_kelas: $('#export_kelas').val()
         };
      },
      processResults: function (data) {
         return { results: data.items };
      }
   }
});

$('#export_tingkatan').on('change', function () {
   var idTingkatan = $(this).val();
   var $kelas = $('#export_kelas');
   $kelas.empty();
   if (!idTingkatan) {
      $kelas.append($('<option>', { value: '', text: '-- Pilih Tingkatan Dulu --' }));
      $kelas.prop('disabled', true);
   } else {
      $kelas.append($('<option>', { value: '', text: '-- Semua Kelas --' }));
      var kelasList = [];
      for (var i = 0; i < exportKelasData.length; i++) {
         if (String(exportKelasData[i].id_tingkatan) === String(idTingkatan)) {
            kelasList.push(exportKelasData[i]);
         }
      }
      kelasList.sort(function (a, b) { return a.label < b.label ? -1 : (a.label > b.label ? 1 : 0); });
      for (var j = 0; j < kelasList.length; j++) {
         $kelas.append($('<option>', { value: kelasList[j].id_kelas_tk, text: kelasList[j].label }));
      }
      $kelas.prop('disabled', false);
   }
   // reset pilihan siswa karena filter berubah
   $('#export_siswa').val(null).trigger('change');
});

$('.btn_export_checkall, .btn_export_uncheckall').on('click', function () {
   var check = $(this).data('mode') === 'check';
   $(this).closest('.labs-export-card').find('.export-col-cb').prop('checked', check);
});
</script>
<script>
// ===== FILTER BUILDER =====
var filterFields = [
   { value: 'nama_lengkap', label: 'Nama Siswa', type: 'text', operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'nis', label: 'NIS', type: 'text', operators: ['contains','equals'] },
   { value: 'kelas', label: 'Kelas', type: 'select_kelas', operators: ['equals'] },
   { value: 'tahun_ajaran', label: 'Tahun Ajaran', type: 'select_ta', operators: ['equals'] },
   { value: 'spp_type', label: 'SPP Type', type: 'select_spp_type', operators: ['equals'] },
   { value: 'acc_ujian', label: 'Acc Ujian', type: 'select_acc_ujian', operators: ['equals'] },
   { value: 'is_active', label: 'Status', type: 'select_status', operators: ['equals'] },
   { value: 'spp_custom', label: 'SPP Custom', type: 'number', operators: ['equals','gt','lt'] },
   { value: 'nik', label: 'NIK', type: 'text', operators: ['contains','equals'] },
   { value: 'nomor_peserta_ujian', label: 'No. Peserta Ujian', type: 'text', operators: ['contains','equals'] },
];
var operatorLabels = { 'contains':'Mengandung','equals':'Sama dengan','starts_with':'Diawali','ends_with':'Diakhiri','gt':'Lebih dari','lt':'Kurang dari' };
var kelasOptions = <?= json_encode(array_map(function($k){ return ['id'=>$k->label,'text'=>$k->label]; }, $list_kelas)); ?>;
var taOptions = <?= json_encode(array_map(function($t){ return ['id'=>$t->label,'text'=>$t->label]; }, $list_tahun_ajaran)); ?>;
var sppTypeOptions = [{id:'FULL',text:'FULL'},{id:'HALF',text:'HALF'},{id:'FREE',text:'FREE'}];
var accUjianOptions = [{id:'Boleh',text:'Boleh'},{id:'Tidak Boleh',text:'Tidak Boleh'}];
var statusOptions = [{id:'1',text:'Aktif'},{id:'0',text:'Non-Aktif'}];
var filterIndex = 0;
function getSelectOptions(f){switch(f){case 'kelas':return kelasOptions;case 'tahun_ajaran':return taOptions;case 'spp_type':return sppTypeOptions;case 'acc_ujian':return accUjianOptions;case 'is_active':return statusOptions;default:return[]}}
function getFieldType(f){for(var i=0;i<filterFields.length;i++){if(filterFields[i].value===f)return filterFields[i].type}return'text'}
function getFieldOperators(f){for(var i=0;i<filterFields.length;i++){if(filterFields[i].value===f)return filterFields[i].operators}return['contains']}
function buildOperatorSelect(ops,sel){var h='<select class="form-control input-sm filter-operator" style="width:130px">';for(var i=0;i<ops.length;i++){var o=ops[i];h+='<option value="'+o+'"'+(o===sel?' selected':'')+'>'+operatorLabels[o]+'</option>'}h+='</select>';return h}
function buildValueInput(f,v){var t=getFieldType(f);if(t==='text'||t==='number'){var it=t==='number'?'number':'text';return'<input type="'+it+'" class="form-control input-sm filter-value" style="width:200px" placeholder="Masukkan nilai..." value="'+(v||'')+'">'}var opts=getSelectOptions(f);var h='<select class="form-control input-sm filter-value" style="width:200px">';h+='<option value="">-- Pilih --</option>';for(var i=0;i<opts.length;i++){h+='<option value="'+opts[i].id+'"'+(opts[i].id===v?' selected':'')+'>'+opts[i].text+'</option>'}h+='</select>';return h}
function addFilterRow(fv,op,v){fv=fv||'';op=op||'';v=v||'';var fsh='<select class="form-control input-sm filter-field" style="width:180px">';fsh+='<option value="">-- Pilih Field --</option>';for(var i=0;i<filterFields.length;i++){fsh+='<option value="'+filterFields[i].value+'"'+(filterFields[i].value===fv?' selected':'')+'>'+filterFields[i].label+'</option>'}fsh+='</select>';var ops=fv?getFieldOperators(fv):['contains'];if(!op||ops.indexOf(op)===-1)op=ops[0];var h='<div class="filter-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px">';h+='<input type="hidden" name="ff[]" class="filter-field-hidden" value="'+fv+'">';h+='<input type="hidden" name="fo[]" class="filter-operator-hidden" value="'+op+'">';h+='<input type="hidden" name="fv[]" class="filter-value-hidden" value="'+v+'">';h+='<span style="color:#3498db;font-weight:bold;font-size:12px;min-width:20px">#'+(filterIndex+1)+'</span>';h+=fsh;h+='<span class="filter-operator-container">'+buildOperatorSelect(ops,op)+'</span>';h+='<span class="filter-value-container">'+buildValueInput(fv,v)+'</span>';h+='<button type="button" class="btn btn-xs btn-danger btn-remove-filter" title="Hapus"><i class="fa fa-times"></i></button>';h+='</div>';$('#filter_rows').append(h);filterIndex++;updateFilterEmpty()}
function updateFilterEmpty(){$('#filter_rows .filter-row').length>0?$('#filter_empty').hide():$('#filter_empty').show()}
function updateFilterNumbers(){$('#filter_rows .filter-row').each(function(idx){$(this).find('span:first').text('#'+(idx+1))})}
$(document).ready(function(){
   $('#btn_add_filter').click(function(){addFilterRow()});
   $(document).on('click','.btn-remove-filter',function(){$(this).closest('.filter-row').remove();updateFilterEmpty();updateFilterNumbers()});
   $(document).on('change','.filter-field',function(){var r=$(this).closest('.filter-row');var f=$(this).val();r.find('.filter-field-hidden').val(f);var ops=f?getFieldOperators(f):['contains'];r.find('.filter-operator-container').html(buildOperatorSelect(ops,ops[0]));r.find('.filter-operator-hidden').val(ops[0]);r.find('.filter-value-container').html(buildValueInput(f,''));r.find('.filter-value-hidden').val('')});
   $(document).on('change','.filter-operator',function(){$(this).closest('.filter-row').find('.filter-operator-hidden').val($(this).val())});
   $(document).on('change keyup','.filter-value',function(){$(this).closest('.filter-row').find('.filter-value-hidden').val($(this).val())});
   <?php if(!empty($multi_filters)): ?><?php foreach($multi_filters as $i => $mf): ?>addFilterRow('<?= htmlspecialchars($mf['field']); ?>','<?= htmlspecialchars($mf['operator']); ?>','<?= htmlspecialchars($mf['value']); ?>');<?php endforeach; ?><?php endif; ?>
   updateFilterEmpty();
});
// ===== END FILTER BUILDER =====

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

   // Restore link (confirm before redirect)
   $(document).on('click', '.restore-link', function(e){
      e.preventDefault();
      var url = $(this).attr('href');
      var isOrtu = url.indexOf('restore_ortu') !== -1;
      var title = isOrtu ? 'Pulihkan Akun Ortu?' : 'Pulihkan Akun Siswa?';
      var text = isOrtu ? 'Akun ortu akan diaktifkan kembali' : 'Akun siswa akan diaktifkan kembali';
      swal({
         title: title,
         text: text,
         type: "info",
         showCancelButton: true,
         confirmButtonColor: "#27ae60",
         confirmButtonText: "Ya, Pulihkan!",
         cancelButtonText: "Batal",
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
      var serialize_bulk = $('#form_siswa_tk_aktif').serialize();

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
               document.location.href = BASE_URL + '/administrator/siswa_tk_aktif/delete?' + serialize_bulk;      
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

   // Password modal
   $('.btn_ubah_password').click(function(){
      <?php
         $db_refresh_token = $this->mymodel->withquery("select name_setting, value from pengaturan_akun where name_setting = 'administrator_access_token'","row")->value;   
      ?>
      const access_token = "<?= (empty($this->session->userdata('access_token')) ? $db_refresh_token : $this->session->userdata('access_token')) ? : $this->session->userdata('access_token'); ?>";
      const email = $(this).attr('data-email');

      $.ajax({
         url: `https://graph.microsoft.com/v1.0/users/${email}`,
         method: "GET",
         headers: { "Authorization": "Bearer " + access_token },
         success: function(res) {
            $('#user_microsoft_id').val(res.id);
            $('#user_microsoft_mail').val(res.userPrincipalName);
            $('#reset_password').attr('href', `<?php echo base_url('administrator/siswa_tk_aktif/reset_password'); ?>?user_microsoft_id=${res.id}`);
         },
         error: function(err) {
            console.error(err);
         }
      });
   });

   // Detail siswa modal
   $('.btn_detail_siswa').click(function(){
      var id = $(this).data('id');
      var content = $('#detail_siswa_content');
      
      // Show loading
      content.html('<div class="text-center" style="padding:40px"><i class="fa fa-spinner fa-spin" style="font-size:40px;color:#3498db"></i><p style="margin-top:10px;color:#666">Memuat data siswa...</p></div>');
      $('#modal_detail_siswa').modal('show');
      
      // Fetch data via AJAX - get JSON data
      $.ajax({
         url: '<?= site_url('administrator/siswa_tk_aktif/get_detail/' . ''); ?>' + id,
         method: 'GET',
         dataType: 'json',
         success: function(data) {
            if (!data) {
               content.html('<div class="text-center" style="padding:40px"><i class="fa fa-exclamation-triangle" style="font-size:40px;color:#e74c3c"></i><p style="margin-top:10px;color:#666">Data tidak ditemukan</p></div>');
               return;
            }
            
            // Build detail HTML
            var sppType = data.spp_type || 'FULL';
            var sppTypeBadge = '';
            if (sppType == 'FULL') sppTypeBadge = '<span class="label" style="background:#27ae60">FULL</span>';
            else if (sppType == 'HALF') sppTypeBadge = '<span class="label" style="background:#f39c12">HALF</span>';
            else sppTypeBadge = '<span class="label" style="background:#3498db">FREE</span>';
            
            var accUjian = data.acc_ujian || 1;
            var accUjianBadge = accUjian == 1 ? '<span class="label" style="background:#27ae60"><i class="fa fa-check"></i> Boleh</span>' : '<span class="label" style="background:#e74c3c"><i class="fa fa-times"></i> Tidak Boleh</span>';
            
            var sppCustom = data.spp_custom && data.spp_custom > 0 ? 'Rp ' + Number(data.spp_custom).toLocaleString('id-ID') : '-';
            var fotoProfil = data.foto_profil ? '<img src="<?= BASE_URL; ?>uploads/siswa_tk/' + data.foto_profil + '" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #3498db">' : '<div style="width:80px;height:80px;border-radius:50%;background:#e8f4fc;display:flex;align-items:center;justify-content:center"><i class="fa fa-user" style="font-size:35px;color:#3498db"></i></div>';
            
            var html = '<div style="background:#fff;padding:20px;border-radius:5px;border:1px solid #e0e0e0">';
            html += '<div style="display:flex;gap:20px;margin-bottom:20px">';
            html += '<div style="text-align:center">' + fotoProfil + '</div>';
            html += '<div style="flex:1">';
            html += '<h3 style="margin:0 0 5px 0;color:#2c3e50">' + (data.nama_lengkap || '-') + '</h3>';
            html += '<p style="margin:0;color:#666">NIS: ' + (data.nis || '-') + '</p>';
            html += '</div></div>';
            
            html += '<table class="table table-bordered" style="margin:0">';
            html += '<tr><th style="background:#e8f4fc;width:200px">Kelas</th><td>' + (data.kelas_tk_label || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">Tahun Ajaran</th><td>' + (data.tahun_ajaran_label || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">No. Peserta Ujian</th><td>' + (data.nomor_peserta_ujian || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">Acc Ujian</th><td>' + accUjianBadge + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">SPP Type</th><td>' + sppTypeBadge + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">SPP Custom</th><td>' + sppCustom + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">NIK</th><td>' + (data.nik || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">Kewarganegaraan</th><td>' + (data.kewarganegaraan || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">Golongan Darah</th><td>' + (data.golongan_darah || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">Telp</th><td>' + (data.telp || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">Email MS Office</th><td>' + (data.email_ms_office || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">Email MS Office Ortu</th><td>' + (data.email_ms_office_ortu || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">Pendidikan Ayah</th><td>' + (data.pendidikan_ayah || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">Pendidikan Ibu</th><td>' + (data.pendidikan_ibu || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">Penghasilan Ayah</th><td>' + (data.penghasilan_ayah || '-') + '</td></tr>';
            html += '<tr><th style="background:#e8f4fc">Penghasilan Ibu</th><td>' + (data.penghasilan_ibu || '-') + '</td></tr>';
            html += '</table>';
            html += '</div>';
            
            content.html(html);
         },
         error: function(err) {
            content.html('<div class="text-center" style="padding:40px"><i class="fa fa-exclamation-triangle" style="font-size:40px;color:#e74c3c"></i><p style="margin-top:10px;color:#666">Gagal memuat data siswa</p></div>');
         }
      });
   });
});
</script>
