<script type="text/javascript">
   <?php if ($this->session->flashdata('success')) { ?>
      toastr.success("<?php echo $this->session->flashdata('success'); ?>");
   <?php } else if ($this->session->flashdata('error')) {  ?>
      toastr.error("<?php echo $this->session->flashdata('error'); ?>");
   <?php } else if ($this->session->flashdata('warning')) {  ?>
      toastr.warning("<?php echo $this->session->flashdata('warning'); ?>");
   <?php } else if ($this->session->flashdata('info')) {  ?>
      toastr.info("<?php echo $this->session->flashdata('info'); ?>");
   <?php } ?>
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <i class="fa fa-users"></i> <?= cclang('siswa_kb') ?> <small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('siswa_kb') ?></li>
   </ol>
</section>

<style>
/* Info Boxes */
.info-box-custom { min-height:90px; border-radius:6px; margin-bottom:15px; display:flex; align-items:center; padding:15px 20px; box-shadow:0 2px 4px rgba(0,0,0,0.08); color:#fff; position:relative; overflow:hidden; }
.info-box-custom .info-icon { font-size:40px; opacity:0.35; position:absolute; right:15px; top:50%; transform:translateY(-50%); }
.info-box-custom .info-label { font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.9; margin-bottom:5px; display:block; font-weight:500; }
.info-box-custom .info-value { font-size:28px; font-weight:700; line-height:1.2; display:block; color:#fff; }
.info-box-custom .info-detail { font-size:11px; opacity:0.85; margin-top:4px; display:block; color:#fff; }
.bg-grad-blue { background:linear-gradient(135deg,#3c8dbc 0%,#367fa9 100%); }
.bg-grad-green { background:linear-gradient(135deg,#00a65a 0%,#008d4c 100%); }
.bg-grad-purple { background:linear-gradient(135deg,#605ca8 0%,#555299 100%); }
.bg-grad-orange { background:linear-gradient(135deg,#f39c12 0%,#db8b0a 100%); }

/* Filter */
.filter-builder { margin-bottom:15px; padding:12px; background:#f8f9fa; border-radius:5px; border:1px solid #e0e0e0; }

/* Table Styling */
.table { font-size:13px; }
.table th { background:#f8f9fa; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:0.3px; color:#555; vertical-align:middle; white-space:nowrap; }
.table td { vertical-align:middle; }
.table tr:hover { background:#f5f5f5; }

/* Action Buttons */
.btn-action { padding:3px 8px; font-size:11px; border-radius:3px; margin:1px; display:inline-block; }
.btn-action i { margin-right:3px; }
.btn-action-view { background:#3498db; color:#fff; }
.btn-action-view:hover { background:#2980b9; color:#fff; text-decoration:none; }
.btn-action-edit { background:#27ae60; color:#fff; }
.btn-action-edit:hover { background:#229954; color:#fff; text-decoration:none; }
.btn-action-delete { background:#e74c3c; color:#fff; }
.btn-action-delete:hover { background:#c0392b; color:#fff; text-decoration:none; }
.btn-action-pdf { background:#f39c12; color:#fff; }
.btn-action-pdf:hover { background:#d68910; color:#fff; text-decoration:none; }

/* Status Labels */
.label-status { padding:4px 10px; border-radius:3px; font-size:11px; font-weight:600; display:inline-block; }
.label-lulus { background:#27ae60; color:#fff; }
.label-belum { background:#e74c3c; color:#fff; }
.label-proses { background:#f39c12; color:#fff; }

/* Empty State */
.empty-state { text-align:center; padding:40px 20px; color:#999; }
.empty-state i { font-size:50px; margin-bottom:15px; display:block; opacity:0.3; }
.empty-state p { font-size:14px; margin:0; }
</style>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">

         <!-- Info Boxes -->
         <div class="row" style="margin-bottom:15px">
            <div class="col-md-3 col-sm-6 col-xs-12">
               <div class="info-box-custom bg-grad-blue">
                  <i class="fa fa-user-plus info-icon"></i>
                  <div>
                     <span class="info-label">Total Siswa Daftar</span>
                     <span class="info-value"><?= number_format($total_daftar); ?></span>
                     <span class="info-detail">Seluruh siswa terdaftar</span>
                  </div>
               </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
               <div class="info-box-custom bg-grad-green">
                  <i class="fa fa-check-circle info-icon"></i>
                  <div>
                     <span class="info-label">Total Siswa Aktif</span>
                     <span class="info-value"><?= number_format($total_aktif); ?></span>
                     <span class="info-detail">Siswa aktif keseluruhan</span>
                  </div>
               </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
               <div class="info-box-custom bg-grad-purple">
                  <i class="fa fa-calendar info-icon"></i>
                  <div>
                     <span class="info-label">Daftar TA Ini</span>
                     <span class="info-value"><?= number_format($total_daftar_ta); ?></span>
                     <span class="info-detail"><?= !empty($tahun_ajaran_aktif) ? $tahun_ajaran_aktif->label : '-'; ?></span>
                  </div>
               </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
               <div class="info-box-custom bg-grad-orange">
                  <i class="fa fa-star info-icon"></i>
                  <div>
                     <span class="info-label">Aktif TA Ini</span>
                     <span class="info-value"><?= number_format($total_aktif_ta); ?></span>
                     <span class="info-detail"><?= !empty($tahun_ajaran_aktif) ? $tahun_ajaran_aktif->label : '-'; ?></span>
                  </div>
               </div>
            </div>
         </div>

         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-graduation-cap"></i> Data Siswa SD
                  <span class="label bg-yellow" style="margin-left:10px"><?= $siswa_kb_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('siswa_kb_add', function () { ?>
                     <a class="btn btn-sm btn-success" title="Tambah Siswa (Ctrl+a)" href="<?= site_url('administrator/siswa_kb/add'); ?>"><i class="fa fa-plus"></i> Tambah</a>
                  <?php }) ?>
                  <?php is_allowed('siswa_kb_export', function () { ?>
                     <a class="btn btn-sm btn-success" title="Export XLS" href="<?= site_url('administrator/siswa_kb/export?' . http_build_query($_GET)); ?>"><i class="fa fa-file-excel-o"></i> XLS</a>
                  <?php }) ?>
                  <a class="btn btn-sm btn-info" title="Import Excel" data-toggle="modal" data-target="#modal_add_new"><i class="fa fa-upload"></i> Import</a>
               </div>
            </div>

            <div class="box-body">
               <!-- Filter & Search -->
               <form name="form_filter_siswa_kb" id="form_filter_siswa_kb" action="<?= base_url('administrator/siswa_kb/index'); ?>">
               <div class="filter-builder">
                  <div style="display:flex;align-items:center;margin-bottom:10px">
                     <i class="fa fa-filter" style="color:#3498db;margin-right:8px"></i>
                     <strong style="color:#2c3e50;font-size:14px">Filter & Pencarian</strong>
                  </div>
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group" style="margin-bottom:8px">
                           <input type="text" class="form-control" name="q" id="filter" placeholder="Cari nama, email, NISN..." value="<?= htmlspecialchars($this->input->get('q')); ?>">
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group" style="margin-bottom:8px">
                           <select class="form-control chosen chosen-select" name="f">
                              <option value="">Semua Field</option>
                              <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' : ''; ?> value="nama_lengkap">Nama Lengkap</option>
                              <option <?= $this->input->get('f') == 'email' ? 'selected' : ''; ?> value="email">Email</option>
                              <option <?= $this->input->get('f') == 'email_ms_office' ? 'selected' : ''; ?> value="email_ms_office">Email Ms. Office</option>
                              <option <?= $this->input->get('f') == 'nisn' ? 'selected' : ''; ?> value="nisn">NISN</option>
                              <option <?= $this->input->get('f') == 'email_ms_office_ortu' ? 'selected' : ''; ?> value="email_ms_office_ortu">Email Ms. Office Ortu</option>
                              <option <?= $this->input->get('f') == 'is_mutasi' ? 'selected' : ''; ?> value="is_mutasi">Mutasi?</option>
                              <option <?= $this->input->get('f') == 'kelas_mutasi' ? 'selected' : ''; ?> value="kelas_mutasi">Kelas Mutasi</option>
                              <option <?= $this->input->get('f') == 'status_lulus' ? 'selected' : ''; ?> value="status_lulus">Status Lulus</option>
                              <option <?= $this->input->get('f') == 'no_peserta' ? 'selected' : ''; ?> value="no_peserta">No Peserta</option>
                              <option <?= $this->input->get('f') == 'no_transaksi' ? 'selected' : ''; ?> value="no_transaksi">No Transaksi</option>
                              <option <?= $this->input->get('f') == 'va_number' ? 'selected' : ''; ?> value="va_number">Virtual Account</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group" style="margin-bottom:8px">
                           <select class="form-control chosen chosen-select" name="s">
                              <option value="">Sort By</option>
                              <option <?= $this->input->get('s') == 'id_siswa_kb' ? 'selected' : ''; ?> value="id_siswa_kb">ID</option>
                              <option <?= $this->input->get('s') == 'nama_lengkap' ? 'selected' : ''; ?> value="nama_lengkap">Nama</option>
                              <option <?= $this->input->get('s') == 'no_peserta' ? 'selected' : ''; ?> value="no_peserta">No Peserta</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group" style="margin-bottom:8px">
                           <select class="form-control chosen chosen-select" name="d">
                              <option <?= $this->input->get('d') == 'desc' ? 'selected' : ''; ?> value="desc">Descending</option>
                              <option <?= $this->input->get('d') == 'asc' ? 'selected' : ''; ?> value="asc">Ascending</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group" style="margin-bottom:8px">
                           <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
                           <?php if(!empty($this->input->get('q')) || !empty($this->input->get('f'))): ?>
                           <a class="btn btn-default" href="<?= base_url('administrator/siswa_kb'); ?>"><i class="fa fa-times"></i> Reset</a>
                           <?php endif; ?>
                        </div>
                     </div>
                  </div>
               </div>
               </form>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('siswa_kb')]); ?> <i class="label bg-yellow"><?= $siswa_kb_counts; ?> <?= cclang('items'); ?></i></h5>
                  </div>
                  <!-- <!-- <?= $calender ?> --> -->
                  <form name="form_siswa_kb" id="form_siswa_kb" action="<?= base_url('administrator/siswa_kb/index'); ?>">

                     <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                           <thead>
                              <tr class="">
                                 <th>
                                    <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                                 </th>
                                 <th>Action</th>
                                 <th> <?= cclang('nama_lengkap') ?></th>
                                 <th> Tingkatan</th>
                                 <th> <?= cclang('no_peserta') ?></th>
                                 <th> <?= cclang('email') ?></th>
                                 <th> <?= cclang('status_lulus') ?></th>
                                 <th> <?= cclang('email_ms_office') ?></th>
                                 <th> <?= cclang('nisn') ?></th>
                                 <th> <?= cclang('email_ms_office_ortu') ?></th>
                                 <th> <?= cclang('notelp_ibu') ?></th>
                                 <th> <?= cclang('notelp_ayah') ?></th>
                                 <th> <?= cclang('sekolah_asal') ?></th>
                                 <th> <?= cclang('foto_peserta') ?></th>
                                 <th> <?= cclang('akte_lahir') ?></th>
                                 <th> <?= cclang('kartu_keluarga') ?></th>
                                 <th> <?= cclang('is_mutasi') ?></th>
                                 <th> <?= cclang('no_transaksi') ?></th>
                                 <th> <?= cclang('va_number') ?></th>
                                 <th> Tanggal Daftar</th>
                                 <th> Tanggal Pembayaran (Pendaftaran)</th>
                                 <th> Tanggal Aktivasi Daftar Ulang</th>
                                 <th> Tanggal Pembayaran (Daftar Ulang) </th>
                                 <th> Status Daftar Ulang</th>
                              </tr>
                           </thead>
                           <tbody id="tbody_siswa_kb">
                              <?php foreach ($siswa_kbs as $siswa_kb) : ?>
                                 <tr>
                                    <td width="5">
                                       <input type="checkbox" class="flat-red check" name="id[]" value="<?= $siswa_kb->id_siswa_kb; ?>">
                                    </td>
                                    <td width="180" style="white-space:nowrap">
                                       <?php is_allowed('siswa_kb_view', function () use ($siswa_kb) { ?>
                                          <a href="<?= site_url('administrator/siswa_kb/view/' . $siswa_kb->id_siswa_kb); ?>" class="btn-action btn-action-view" title="Lihat Detail"><i class="fa fa-eye"></i> Detail</a>
                                          <a href="<?= site_url('administrator/siswa_kb/single_pdf/' . $siswa_kb->id_siswa_kb); ?>" class="btn-action btn-action-pdf" title="Download PDF"><i class="fa fa-file-pdf-o"></i> PDF</a>
                                       <?php }) ?>
                                       <?php is_allowed('siswa_kb_update', function () use ($siswa_kb) { ?>
                                          <a href="<?= site_url('administrator/siswa_kb/edit/' . $siswa_kb->id_siswa_kb); ?>" class="btn-action btn-action-edit" title="Edit"><i class="fa fa-pencil"></i> Edit</a>
                                       <?php }) ?>
                                       <?php is_allowed('siswa_kb_delete', function () use ($siswa_kb) { ?>
                                          <a href="javascript:void(0);" data-href="<?= site_url('administrator/siswa_kb/delete/' . $siswa_kb->id_siswa_kb); ?>" class="btn-action btn-action-delete remove-data" title="Hapus"><i class="fa fa-trash"></i> Hapus</a>
                                       <?php }) ?>
                                    </td>
                                    <td><?= _ent(ucwords($siswa_kb->nama_lengkap)); ?></td>
                                    <td><?= _ent($siswa_kb->tingkatan_label); ?></td>
                                    <td><?= _ent($siswa_kb->no_peserta); ?></td>
                                    <td><?= _ent($siswa_kb->email); ?></td>
                                    <td><?= _ent($siswa_kb->status_lulus) ?></td>
                                    <td><?= _ent($siswa_kb->email_ms_office); ?></td>
                                    <td><?= _ent($siswa_kb->nisn); ?></td>
                                    <td><?= _ent($siswa_kb->email_ms_office_ortu); ?></td>
                                    <td><?= _ent($siswa_kb->notelp_ibu); ?></td>
                                    <td><?= _ent($siswa_kb->notelp_ayah); ?></td>
                                    <td><?= _ent($siswa_kb->sekolah_asal); ?></td>
                                    <td><?= "<a target='_blank' href='".BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->foto_peserta."'>Lihat Foto Peserta</a>"; ?></td>
                                    <td><?= "<a target='_blank' href='".BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->akte_lahir."'>Lihat Akte Lahir</a>"; ?></td>
                                    <td><?= "<a target='_blank' href='".BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->kartu_keluarga."'>Lihat KK</a>"; ?></td>
                                  <!-- <td>
                                       <?php if (!empty($siswa_kb->foto_peserta)) : ?>
                                          <?php if (is_image($siswa_kb->foto_peserta)) : ?>
                                             <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->foto_peserta; ?>">
                                                <img src="<?= BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->foto_peserta; ?>" class="image-responsive" alt="image siswa_kb" title="foto_peserta siswa_kb" width="40px">
                                             </a>
                                          <?php else : ?>
                                             <a href="<?= BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->foto_peserta; ?>">
                                                <img src="<?= get_icon_file($siswa_kb->foto_peserta); ?>" class="image-responsive image-icon" alt="image siswa_kb" title="foto_peserta <?= $siswa_kb->foto_peserta; ?>" width="40px">
                                             </a>
                                          <?php endif; ?>
                                       <?php endif; ?>
                                    </td>

                                    <td>
                                       <?php if (!empty($siswa_kb->akte_lahir)) : ?>
                                          <?php if (is_image($siswa_kb->akte_lahir)) : ?>
                                             <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->akte_lahir; ?>">
                                                <img src="<?= BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->akte_lahir; ?>" class="image-responsive" alt="image siswa_kb" title="akte_lahir siswa_kb" width="40px">
                                             </a>
                                          <?php else : ?>
                                             <a href="<?= BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->akte_lahir; ?>">
                                                <img src="<?= get_icon_file($siswa_kb->akte_lahir); ?>" class="image-responsive image-icon" alt="image siswa_kb" title="akte_lahir <?= $siswa_kb->akte_lahir; ?>" width="40px">
                                             </a>
                                          <?php endif; ?>
                                       <?php endif; ?>
                                    </td>

                                    <td>
                                       <?php if (!empty($siswa_kb->kartu_keluarga)) : ?>
                                          <?php if (is_image($siswa_kb->kartu_keluarga)) : ?>
                                             <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->kartu_keluarga; ?>">
                                                <img src="<?= BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->kartu_keluarga; ?>" class="image-responsive" alt="image siswa_kb" title="kartu_keluarga siswa_kb" width="40px">
                                             </a>
                                          <?php else : ?>
                                             <a href="<?= BASE_URL . 'uploads/siswa_kb/' . $siswa_kb->kartu_keluarga; ?>">
                                                <img src="<?= get_icon_file($siswa_kb->kartu_keluarga); ?>" class="image-responsive image-icon" alt="image siswa_kb" title="kartu_keluarga <?= $siswa_kb->kartu_keluarga; ?>" width="40px">
                                             </a>
                                          <?php endif; ?>
                                       <?php endif; ?>
                                    </td> -->
                                    <td><?= _ent($siswa_kb->is_mutasi); ?></td>
                                    <td><?= _ent($siswa_kb->no_transaksi); ?></td>
                                    <td><?= _ent($siswa_kb->va_number); ?></td>
                                    <td><?= _ent($siswa_kb->tanggal_daftar); ?></td>
                                    <td><?= _ent($siswa_kb->tanggal_pembayaran); ?></td>
                                    <td><?= _ent($siswa_kb->tanggal_aktivasi); ?></td>
                                    <td><?= _ent($siswa_kb->tanggal_bayar_daftar_ulang); ?></td>
                                    <td><?= _ent($siswa_kb->status_daftar_ulang); ?></td>

                                 </tr>
                              <?php endforeach; ?>
                              <?php if ($siswa_kb_counts == 0) : ?>
                                 <tr>
                                    <td colspan="100">
                                       Siswa SD data is not available
                                    </td>
                                 </tr>
                              <?php endif; ?>
                           </tbody>
                        </table>
                     </div>
               </div>
               <hr>
               <!-- /.widget-user -->
               <div class="row">
                  <div class="col-md-8">
                     <div class="col-sm-2 padd-left-0 ">
                        <select type="text" class="form-control chosen chosen-select" name="bulk" id="bulk" placeholder="Site Email">
                           <option value="5">Download File</option>
                           <option value="6">Download Kartu Peserta</option>
                           <option value="1">Hapus</option>
                           <option value="2">Lulus</option>
                           <option value="3">Tidak Lulus</option>
                           <option value="4">Cadangan</option>
                           <option value="">Bulk</option>

                        </select>
                        <input type="hidden" id="tgl_du" name="tgl_du">
                        <input type="hidden" id="st" name="st">

                     </div>

                     <div class="col-sm-2 padd-left-0 ">
                        <button type="button" class="btn btn-flat" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>"><?= cclang('apply_button'); ?></button>
                     </div>
                  </div>
                  </form>
                  <div class="col-md-4">
                     <div class="dataTables_paginate paging_simple_numbers pull-right" id="example2_paginate">
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
            </div>
            <!--/box body -->
         </div>
         <!--/box -->
      </div>
   </div>
</section>
<!-- /.content -->
<!-- ============ MODAL IMPORT SISWA =============== -->
<div class="modal fade" id="modal_add_new" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content" style="border-radius:8px;overflow:hidden">
         <div class="modal-header" style="background:linear-gradient(135deg,#00a65a,#008d4c);color:#fff;border:none">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8"><span>&times;</span></button>
            <h4 class="modal-title"><i class="fa fa-upload"></i> Import Siswa Baru</h4>
         </div>
         <form action="<?= base_url('administrator/siswa_kb/import/'); ?>" method="post" enctype="multipart/form-data">
            <div class="modal-body" style="padding:20px">
               <div style="background:#f8f9fa;border-radius:6px;padding:15px;margin-bottom:15px">
                  <p style="margin:0;color:#666;font-size:13px">
                     <i class="fa fa-info-circle" style="color:#3498db"></i> Import siswa baru ke tabel <strong>siswa_kb_aktif</strong>.<br>
                     Jika siswa sudah terdaftar, data akan diupdate berdasarkan nama lengkap.
                  </p>
               </div>
               <div class="form-group">
                  <label style="font-weight:600;color:#2c3e50"><i class="fa fa-graduation-cap" style="color:#f39c12"></i> Tahun Ajaran <span class="text-danger">*</span></label>
                  <select class="form-control chosen chosen-select-deselect" name="tahun_ajaran" id="tahun_ajaran" data-placeholder="-- Pilih Tahun Ajaran --" required>
                     <option value=""></option>
                     <?php foreach (db_get_all_data('tahun_ajaran') as $row) : ?>
                        <option value="<?= $row->id_tahun_ajaran ?>"><?= $row->label; ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="form-group">
                  <label style="font-weight:600;color:#2c3e50"><i class="fa fa-th" style="color:#9b59b6"></i> Kelas <span class="text-danger">*</span></label>
                  <select class="form-control chosen chosen-select-deselect" name="kelas_sd" id="kelas_sd" data-placeholder="-- Pilih Kelas --" required>
                     <option value=""></option>
                     <?php foreach (db_get_all_data('kelas_sd') as $row) : ?>
                        <option value="<?= $row->id_kelas_sd ?>"><?= $row->label; ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="form-group">
                  <label style="font-weight:600;color:#2c3e50"><i class="fa fa-file-excel-o" style="color:#27ae60"></i> File Excel <span class="text-danger">*</span></label>
                  <input name="file_siswa" class="form-control" type="file" accept=".xls,.xlsx" required style="padding:6px">
                  <p style="margin-top:8px;font-size:12px;color:#999">
                     Format: .xlsx atau .xls.
                     <a href="<?= base_url('uploads/example/format_siswa_kb.xlsx'); ?>" style="color:#3498db"><i class="fa fa-download"></i> Download template</a>
                  </p>
               </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #eee;padding:15px 20px">
               <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
               <button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Import Sekarang</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL IMPORT SISWA-->
               </div>


            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Tutup</button>
               <button class="btn btn-info btn_save">Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL ADD BARANG-->
<!-- Page script -->
<script>
   $(document).ready(function() {

      $('.remove-data').click(function() {

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
            function(isConfirm) {
               if (isConfirm) {
                  document.location.href = url;
               }
            });

         return false;
      });


      $('#apply').click(function() {

         var bulk = $('#bulk');
         var serialize_bulk = $('#form_siswa_kb').serialize();

         if (bulk.val() == '1') {
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
                  document.location.href = BASE_URL + '/administrator/siswa_kb/delete?' + serialize_bulk;      
               }
             });

           return false;

         }
         else if (bulk.val() == '2') {
            swal({
                  title: "Silahkan masukan tanggal daftar ulang",
                  text: "Format tanggal harus 'YYYY-MM-DD' Contoh:2022-12-30",
                  type: "input",
                  showCancelButton: true,
                  closeOnConfirm: false,
                  animation: "slide-from-top",
                  inputPlaceholder: "Write something"
               },
               function(inputValue) {
                  if (inputValue === null) return false;

                  if (inputValue === "") {
                     swal.showInputError("You need to write something!");
                     return false
                  }
                  // console.log(inputValue)
                  $('#st').val('2');
                  $('#tgl_du').val(inputValue);
                  document.location.href = BASE_URL + '/administrator/siswa_kb/update_status_daftar_ulang?' + $('#form_siswa_kb').serialize();
               })
            return false;

         } else if (bulk.val() == '3') {
            $('#st').val('3');
            document.location.href = BASE_URL + '/administrator/siswa_kb/update_status?' + $('#form_siswa_kb').serialize();

            return false;
         } else if (bulk.val() == '4') {
            $('#st').val('4');
            document.location.href = BASE_URL + '/administrator/siswa_kb/update_status?' + $('#form_siswa_kb').serialize();

            return false;
         } else if (bulk.val() == '5') {
            document.location.href = BASE_URL + 'administrator/siswa_kb/download_file?' + $('#form_siswa_kb').serialize();
            return false;
         } else if (bulk.val() == '6') {
            document.location.href = BASE_URL + 'administrator/siswa_kb/download_kartu_peserta?' + $('#form_siswa_kb').serialize();
            return false;
         } else if (bulk.val() == '') {
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

      }); /*end appliy click*/


      //check all
      var checkAll = $('#check_all');
      var checkboxes = $('input.check');

      checkAll.on('ifChecked ifUnchecked', function(event) {
         if (event.type == 'ifChecked') {
            checkboxes.iCheck('check');
         } else {
            checkboxes.iCheck('uncheck');
         }
      });

      checkboxes.on('ifChanged', function(event) {
         if (checkboxes.filter(':checked').length == checkboxes.length) {
            checkAll.prop('checked', 'checked');
         } else {
            checkAll.removeProp('checked');
         }
         checkAll.iCheck('update');
      });

   }); /*end doc ready*/
</script>