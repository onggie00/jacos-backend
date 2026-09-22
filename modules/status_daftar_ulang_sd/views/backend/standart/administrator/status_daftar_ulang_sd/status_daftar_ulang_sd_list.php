<link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.css">
<style>
/* Top Buttons */
.btn-top { margin-right: 5px; border-radius: 3px; }

/* Table Styling */
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; text-align: center; }
.table td { font-size: 13px; vertical-align: middle; }

/* Status Badges */
.badge-status { display: inline-block; padding: 3px 9px; border-radius: 10px; font-size: 11px; font-weight: 600; color: #fff; }
.badge-status-0 { background: #95a5a6; }
.badge-status-1 { background: #f39c12; }
.badge-status-2 { background: #27ae60; }

/* File Thumbnails */
.thumb-file { width: 40px; height: 40px; object-fit: cover; border-radius: 3px; border: 1px solid #e0e0e0; }

/* Action Buttons */
.btn-aksi { margin: 2px 1px; padding: 4px 0; font-size: 11px; border-radius: 3px; width: 48%; display: inline-block; text-align: center; }
.btn-aksi i { margin-right: 3px; }
.btn-aksi-full { margin: 2px 1px; padding: 4px 0; font-size: 11px; border-radius: 3px; width: 98%; display: block; text-align: center; }
.btn-aksi-full i { margin-right: 3px; }

/* VA BRI: 13 digits, truncate */
.va-bri-cell { max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; vertical-align: middle; font-family: monospace; font-size: 12px; }

/* Gelombang badge */
.badge-gelombang { background: #3498db; padding: 3px 7px; border-radius: 3px; color: #fff; font-size: 11px; }
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
   <h1><i class="fa fa-check-circle"></i> <?= cclang('status_daftar_ulang_sd') ?> <small><?= cclang('list_all'); ?></small></h1>
   <ol class="breadcrumb"><li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li><li class="active"><?= cclang('status_daftar_ulang_sd') ?></li></ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">

            <!-- Box Header -->
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-list"></i> Data Status Daftar Ulang SD
                  <span class="label bg-yellow" style="margin-left:10px"><?= $status_daftar_ulang_sd_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('status_daftar_ulang_sd_export', function(){?>
                  <a class="btn btn-sm btn-action btn-action-export btn-top" title="<?= cclang('export'); ?> XLS" href="<?= site_url('administrator/status_daftar_ulang_sd/export') . '?' . http_build_query($_GET); ?>">
                     <i class="fa fa-file-excel-o"></i> XLS
                  </a>
                  <a class="btn btn-sm btn-action btn-action-pdf btn-top" title="<?= cclang('export'); ?> PDF" href="<?= site_url('administrator/status_daftar_ulang_sd/export_pdf') . '?' . http_build_query($_GET); ?>">
                     <i class="fa fa-file-pdf-o"></i> PDF
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">

               <!-- Advanced Filter Builder -->
               <form name="form_status_daftar_ulang_sd" id="form_status_daftar_ulang_sd" action="<?= base_url('administrator/status_daftar_ulang_sd/index'); ?>">
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
                     <button type="submit" class="btn btn-flat btn-primary" id="sbtn"><i class="fa fa-search"></i> Terapkan Filter</button>
                     <a class="btn btn-flat btn-default" href="<?= base_url('administrator/status_daftar_ulang_sd'); ?>"><i class="fa fa-times"></i> Reset Semua</a>
                     <div style="margin-left:auto;display:flex;gap:8px;align-items:center">
                        <select class="form-control input-sm" name="s" id="sort" style="width:170px">
                           <option value="">Urutkan</option>
                           <option <?= $this->input->get('s') == 'id_daftar_ulang' ? 'selected' : ''; ?> value="id_daftar_ulang">ID Daftar Ulang</option>
                           <option <?= $this->input->get('s') == 'tgl_daftar_ulang' ? 'selected' : ''; ?> value="tgl_daftar_ulang">Tanggal Daftar Ulang</option>
                           <option <?= $this->input->get('s') == 'tanggal_lulus' ? 'selected' : ''; ?> value="tanggal_lulus">Tanggal Lulus</option>
                           <option <?= $this->input->get('s') == 'tgl_aktivasi' ? 'selected' : ''; ?> value="tgl_aktivasi">Tanggal Aktivasi</option>
                           <option <?= $this->input->get('s') == 'tgl_bayar' ? 'selected' : ''; ?> value="tgl_bayar">Tanggal Bayar</option>
                           <option <?= $this->input->get('s') == 'status' ? 'selected' : ''; ?> value="status">Status</option>
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
                           <th>Email</th>
                           <th>No Peserta</th>
                           <th>Thn Ajaran</th>
                           <th>Gelombang</th>
                           <th>Status</th>
                           <th>VA BRI</th>
                           <th>Tgl Daftar Ulang</th>
                           <th>Tgl Lulus</th>
                           <th>Expired Date</th>
                           <th>Slip</th>
                           <th>Kwitansi</th>
                           <th>Kartu</th>
                           <th width="280">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_status_daftar_ulang_sd">
                     <?php if($status_daftar_ulang_sd_counts > 0): ?>
                     <?php foreach($status_daftar_ulang_sds as $row): ?>
                        <tr>
                           <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $row->id_daftar_ulang; ?>"></td>
                           <td><i class="fa fa-user"></i> <?= _ent($row->nama_lengkap); ?></td>
                           <td><small><?= _ent($row->email); ?></small></td>
                           <td style="text-align:center"><?= _ent($row->no_peserta); ?></td>
                           <td style="text-align:center"><?= _ent($row->tahun_ajaran); ?></td>
                           <td style="text-align:center">
                              <?php if (!is_null($row->gelombang) && $row->gelombang !== ''): ?>
                                 <span class="badge-gelombang"><?= _ent($row->gelombang); ?></span>
                              <?php else: ?>
                                 <small class="text-muted">-</small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php
                                 $status_text = '-';
                                 $status_class = 'badge-status-0';
                                 if ($row->status == 0) { $status_text = 'Menunggu Aktivasi'; $status_class = 'badge-status-0'; }
                                 else if ($row->status == 1) { $status_text = 'VA Aktif'; $status_class = 'badge-status-1'; }
                                 else if ($row->status == 2) { $status_text = 'Lunas'; $status_class = 'badge-status-2'; }
                              ?>
                              <span class="badge-status <?= $status_class; ?>"><?= $status_text; ?></span>
                           </td>
                           <td>
                              <?php if (!empty($row->va_bri)): ?>
                                 <span class="va-bri-cell" title="<?= htmlspecialchars($row->va_bri); ?>"><?= $row->va_bri; ?></span>
                              <?php else: ?>
                                 <small class="text-muted">-</small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center"><?= $row->tgl_daftar_ulang ? date('d-m-Y', strtotime($row->tgl_daftar_ulang)) : '<small class="text-muted">-</small>'; ?></td>
                           <td style="text-align:center"><?= $row->tanggal_lulus ? date('d-m-Y', strtotime($row->tanggal_lulus)) : '<small class="text-muted">-</small>'; ?></td>
                           <td style="text-align:center"><?= !empty($row->expired_datetime) ? date('d-m-Y H:i', strtotime($row->expired_datetime)) : '<small class="text-muted">-</small>'; ?></td>
                           <td style="text-align:center">
                              <?php if (!empty($row->slip_pembayaran)): ?>
                                 <?php $no_transaksi = $this->mymodel->withquery("select no_transaksi from transaksi where user_email like '%".$this->db->escape_like_str($row->email)."%' and user_name like '%".$this->db->escape_like_str($row->nama_lengkap)."%' order by id_transaksi desc","row")->no_transaksi;  ?>
                                 <a href="https://psb.labschoolcibubur.sch.id/slip-pembayaran/<?= urlencode($row->email); ?>/sd/<?= urlencode($row->nama_lengkap); ?>?no_transaksi=<?= $no_transaksi; ?>" target="_blank" title="Lihat Slip Pembayaran Digital">
                                    <i class="fa fa-file-pdf-o" style="font-size:20px;color:#e74c3c"></i>
                                 </a>
                              <?php else: ?>
                                 <small class="text-muted">-</small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php if (!empty($row->kwitansi)): ?>
                                 <?php if (is_image($row->kwitansi)): ?>
                                    <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/kwitansi/' . $row->kwitansi; ?>">
                                       <img src="<?= BASE_URL . 'uploads/kwitansi/' . $row->kwitansi; ?>" class="thumb-file" alt="kwitansi" title="<?= $row->kwitansi; ?>">
                                    </a>
                                 <?php else: ?>
                                    <a href="<?= BASE_URL . 'uploads/kwitansi/' . $row->kwitansi; ?>" title="<?= $row->kwitansi; ?>">
                                       <img src="<?= get_icon_file($row->kwitansi); ?>" class="thumb-file" alt="kwitansi">
                                    </a>
                                 <?php endif; ?>
                              <?php else: ?>
                                 <small class="text-muted">-</small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php if (!empty($row->kartu_sementara)): ?>
                                 <?php if (is_image($row->kartu_sementara)): ?>
                                    <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/kartu_siswa_sementara/' . $row->kartu_sementara; ?>">
                                       <img src="<?= BASE_URL . 'uploads/kartu_siswa_sementara/' . $row->kartu_sementara; ?>" class="thumb-file" alt="kartu sementara" title="<?= $row->kartu_sementara; ?>">
                                    </a>
                                 <?php else: ?>
                                    <a href="<?= BASE_URL . 'uploads/kartu_siswa_sementara/' . $row->kartu_sementara; ?>" title="<?= $row->kartu_sementara; ?>">
                                       <img src="<?= get_icon_file($row->kartu_sementara); ?>" class="thumb-file" alt="kartu sementara">
                                    </a>
                                 <?php endif; ?>
                              <?php else: ?>
                                 <small class="text-muted">-</small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <div style="margin-bottom:3px">
                                 <?php is_allowed('status_daftar_ulang_sd_view', function() use ($row){?>
                                 <a href="<?= site_url('administrator/status_daftar_ulang_sd/view/'.$row->id_daftar_ulang); ?>" class="btn btn-sm btn-info btn-aksi" title="Lihat detail">
                                    <i class="fa fa-eye"></i> Detail
                                 </a>
                                 <a href="<?= site_url('administrator/status_daftar_ulang_sd/single_pdf/'.$row->id_daftar_ulang); ?>" class="btn btn-sm btn-default btn-aksi" title="Cetak PDF" target="_blank">
                                    <i class="fa fa-file-pdf-o"></i> PDF
                                 </a>
                                 <?php }) ?>
                              </div>
                              <div style="margin-bottom:3px">
                                 <?php is_allowed('status_daftar_ulang_sd_update', function() use ($row){?>
                                 <a href="<?= site_url('administrator/status_daftar_ulang_sd/edit/'.$row->id_daftar_ulang); ?>" class="btn btn-sm btn-warning btn-aksi" title="Edit / Aktivasi">
                                    <i class="fa fa-edit"></i> Edit
                                 </a>
                                 <?php }) ?>
                                 <?php if ($row->status == 0 || $row->status == 2): ?>
                                 <?php is_allowed('status_daftar_ulang_sd_delete', function() use ($row){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/status_daftar_ulang_sd/delete/'.$row->id_daftar_ulang); ?>" class="btn btn-sm btn-danger btn-aksi remove-data" title="Hapus data">
                                    <i class="fa fa-trash"></i> Hapus
                                 </a>
                                 <?php }) ?>
                                 <?php endif; ?>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="15" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">
                                 <?php if(!empty($multi_filters)): ?>
                                    Data status daftar ulang tidak ditemukan untuk filter yang dipilih
                                 <?php elseif(!empty($this->input->get('q'))): ?>
                                    Data tidak ditemukan untuk pencarian "<?= htmlspecialchars($this->input->get('q')); ?>"
                                 <?php else: ?>
                                    Data status daftar ulang SD belum tersedia
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
                     <div class="input-group" style="max-width:420px">
                        <select class="form-control" name="bulk" id="bulk">
                           <option value="">-- Bulk Action --</option>
                           <option value="1">Aktivasi VA</option>
                           <option value="2">Siswa Aktif</option>
                           <option value="3">Update Expired</option>
                        </select>
                        <span class="input-group-btn">
                           <button type="button" class="btn btn-flat btn-default" name="apply" id="apply">Terapkan</button>
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

<!-- ============ MODAL SISWA AKTIF (existing) =============== -->
<div class="modal fade" id="modal_add_new" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#27ae60;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8">&times;</button>
            <h4 class="modal-title"><i class="fa fa-user-plus"></i> Tambah Siswa Baru (Khusus Siswa Aktif)</h4>
         </div>
         <form action="<?= base_url('administrator/status_daftar_ulang_sd/siswa_aktif/'); ?>" method="post" class="form-horizontal" id="form-aktif">
            <div class="modal-body">
               <div class="form-group mb-3">
                  <label class="control-label col-xs-3">Kelas</label>
                  <div class="col-xs-8">
                     <select class="form-control chosen chosen-select-deselect" name="kelas_sd" id="kelas_sd" data-placeholder="Pilih Kelas" required>
                        <option value=""></option>
                        <?php foreach (db_get_all_data('kelas_sd') as $r) : ?>
                           <option value="<?= $r->id_kelas_sd ?>"><?= $r->label; ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="form-group mb-3">
                  <label class="control-label col-xs-3">Tahun Ajaran</label>
                  <div class="col-xs-8">
                     <select class="form-control chosen chosen-select-deselect" name="tahun_ajaran" id="tahun_ajaran" data-placeholder="Pilih Tahun Ajaran" required>
                        <option value=""></option>
                        <?php foreach (db_get_all_data('tahun_ajaran') as $r) : ?>
                           <option value="<?= $r->id_tahun_ajaran ?>"><?= $r->label; ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="form-group mb-3">
                  <label for="spp_custom" class="col-xs-3 control-label">SPP Khusus <span class="text-danger">Optional</span></label>
                  <div class="col-xs-8">
                     <input type="text" class="form-control" name="spp_custom" id="spp_custom" placeholder="SPP khusus">
                     <small class="info help-block"></small>
                  </div>
               </div>
            </div>
            <input type="hidden" name="ids" id="ids">
            <div class="modal-footer">
               <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
               <button class="btn btn-success btn-flat btn_save"><i class="fa fa-save"></i> Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- ============ MODAL UPDATE EXPIRED VA (existing, unik SD) =============== -->
<div class="modal fade" id="modal_update_expired" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#f39c12;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8">&times;</button>
            <h4 class="modal-title"><i class="fa fa-clock-o"></i> Update Expired Date VA BRI</h4>
         </div>
         <form action="<?= base_url('administrator/status_daftar_ulang_sd/update_expired_va/'); ?>" method="post" class="form-horizontal" id="form-expired">
            <div class="modal-body">
               <div class="form-group mb-3">
                  <label class="control-label col-xs-3">Expired Date</label>
                  <div class="col-xs-8">
                     <input type="text" class="form-control pull-right datetimepicker" name="expired_date" placeholder="Expired Date" id="expired_date">
                  </div>
               </div>
            </div>
            <input type="hidden" name="ids" id="idsEx">
            <div class="modal-footer">
               <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
               <button class="btn btn-warning btn-flat btn_save"><i class="fa fa-save"></i> Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Page script -->
<script>
// ===== FILTER BUILDER =====
var filterFields = [
   { value: 'nama_lengkap',     label: 'Nama Siswa',         type: 'text',          operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'email',            label: 'Email',              type: 'text',          operators: ['contains','equals'] },
   { value: 'no_peserta',       label: 'No Peserta',         type: 'text',          operators: ['contains','equals','starts_with'] },
   { value: 'nisn',             label: 'NISN',               type: 'text',          operators: ['contains','equals'] },
   { value: 'va_bri',           label: 'VA BRI',             type: 'text',          operators: ['contains','equals','starts_with'] },
   { value: 'id_daftar_ulang',  label: 'ID Daftar Ulang',    type: 'number',        operators: ['equals','gt','lt'] },
   { value: 'status',           label: 'Status',             type: 'select_status', operators: ['equals'] },
   { value: 'tahun_ajaran',     label: 'Tahun Ajaran',       type: 'select_ta',     operators: ['equals'] },
   { value: 'gelombang',        label: 'Gelombang',          type: 'select_gel',    operators: ['equals'] },
   { value: 'tgl_daftar_ulang', label: 'Tgl Daftar Ulang',   type: 'date',          operators: ['equals','gt','lt','gte','lte'] },
   { value: 'tanggal_lulus',    label: 'Tanggal Lulus',      type: 'date',          operators: ['equals','gt','lt','gte','lte'] },
   { value: 'tgl_aktivasi',     label: 'Tanggal Aktivasi',   type: 'date',          operators: ['equals','gt','lt','gte','lte'] },
   { value: 'tgl_bayar',        label: 'Tanggal Bayar',      type: 'date',          operators: ['equals','gt','lt','gte','lte'] },
   { value: 'expired_datetime', label: 'Expired Date VA',    type: 'date',          operators: ['equals','gt','lt','gte','lte'] },
];
var operatorLabels = { 'contains':'Mengandung','equals':'Sama dengan','starts_with':'Diawali','ends_with':'Diakhiri','gt':'Lebih dari','lt':'Kurang dari','gte':'Lebih dari sama dengan','lte':'Kurang dari sama dengan' };
var taOptions = <?= json_encode(array_map(function($t){ return array('id'=>$t->tahun_ajaran,'text'=>$t->tahun_ajaran); }, (array)$list_tahun_ajaran)); ?>;
var gelOptions = <?= json_encode(array_map(function($g){ return array('id'=>(string)$g->gelombang,'text'=>'Gelombang '.$g->gelombang); }, (array)$list_gelombang)); ?>;
var statusOptions = [
   {id:'0', text:'Menunggu Aktivasi'},
   {id:'1', text:'VA Aktif (Menunggu Pembayaran)'},
   {id:'2', text:'Pembayaran Berhasil (Lunas)'}
];
function getSelectOptions(f){
   switch(f){
      case 'tahun_ajaran': return taOptions;
      case 'gelombang':    return gelOptions;
      case 'status':       return statusOptions;
      default: return [];
   }
}
function getFieldType(f){ for(var i=0;i<filterFields.length;i++){ if(filterFields[i].value===f) return filterFields[i].type; } return 'text'; }
function getFieldOperators(f){ for(var i=0;i<filterFields.length;i++){ if(filterFields[i].value===f) return filterFields[i].operators; } return ['contains']; }
function buildOperatorSelect(ops, sel){
   var h='<select class="form-control input-sm filter-operator" style="width:150px">';
   for(var i=0;i<ops.length;i++){ var o=ops[i]; h+='<option value="'+o+'"'+(o===sel?' selected':'')+'>'+operatorLabels[o]+'</option>'; }
   h+='</select>'; return h;
}
function buildValueInput(f, v){
   var t=getFieldType(f);
   if(t==='text' || t==='number'){
      var it=(t==='number') ? 'number' : 'text';
      return '<input type="'+it+'" class="form-control input-sm filter-value" style="width:200px" placeholder="Masukkan nilai..." value="'+(v||'')+'">';
   }
   if(t==='date'){
      return '<input type="date" class="form-control input-sm filter-value" style="width:200px" value="'+(v||'')+'">';
   }
   var opts=getSelectOptions(f);
   var h='<select class="form-control input-sm filter-value" style="width:200px">';
   h+='<option value="">-- Pilih --</option>';
   for(var i=0;i<opts.length;i++){ h+='<option value="'+opts[i].id+'"'+(opts[i].id===v?' selected':'')+'>'+opts[i].text+'</option>'; }
   h+='</select>'; return h;
}
var filterIndex = 0;
function addFilterRow(fv, op, v){
   fv=fv||''; op=op||''; v=v||'';
   var fsh='<select class="form-control input-sm filter-field" style="width:180px">';
   fsh+='<option value="">-- Pilih Field --</option>';
   for(var i=0;i<filterFields.length;i++){ fsh+='<option value="'+filterFields[i].value+'"'+(filterFields[i].value===fv?' selected':'')+'>'+filterFields[i].label+'</option>'; }
   fsh+='</select>';
   var ops=fv ? getFieldOperators(fv) : ['contains'];
   if(!op || ops.indexOf(op)===-1) op=ops[0];
   var h='<div class="filter-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px">';
   h+='<input type="hidden" name="ff[]" class="filter-field-hidden" value="'+fv+'">';
   h+='<input type="hidden" name="fo[]" class="filter-operator-hidden" value="'+op+'">';
   h+='<input type="hidden" name="fv[]" class="filter-value-hidden" value="'+v+'">';
   h+='<span style="color:#3498db;font-weight:bold;font-size:12px;min-width:20px">#'+(filterIndex+1)+'</span>';
   h+=fsh;
   h+='<span class="filter-operator-container">'+buildOperatorSelect(ops,op)+'</span>';
   h+='<span class="filter-value-container">'+buildValueInput(fv,v)+'</span>';
   h+='<button type="button" class="btn btn-xs btn-danger btn-remove-filter" title="Hapus"><i class="fa fa-times"></i></button>';
   h+='</div>';
   $('#filter_rows').append(h);
   filterIndex++;
   updateFilterEmpty();
}
function updateFilterEmpty(){ $('#filter_rows .filter-row').length>0 ? $('#filter_empty').hide() : $('#filter_empty').show(); }
function updateFilterNumbers(){ $('#filter_rows .filter-row').each(function(idx){ $(this).find('span:first').text('#'+(idx+1)); }); }
$(document).ready(function(){
   $('#btn_add_filter').click(function(){ addFilterRow(); });
   $(document).on('click','.btn-remove-filter',function(){
      $(this).closest('.filter-row').remove();
      updateFilterEmpty();
      updateFilterNumbers();
   });
   $(document).on('change','.filter-field',function(){
      var r=$(this).closest('.filter-row');
      var f=$(this).val();
      r.find('.filter-field-hidden').val(f);
      var ops=f ? getFieldOperators(f) : ['contains'];
      r.find('.filter-operator-container').html(buildOperatorSelect(ops, ops[0]));
      r.find('.filter-operator-hidden').val(ops[0]);
      r.find('.filter-value-container').html(buildValueInput(f, ''));
      r.find('.filter-value-hidden').val('');
   });
   $(document).on('change','.filter-operator',function(){
      $(this).closest('.filter-row').find('.filter-operator-hidden').val($(this).val());
   });
   $(document).on('change keyup','.filter-value',function(){
      $(this).closest('.filter-row').find('.filter-value-hidden').val($(this).val());
   });
   <?php if(!empty($multi_filters)): ?>
      <?php foreach($multi_filters as $i => $mf): ?>
         addFilterRow('<?= htmlspecialchars($mf['field'], ENT_QUOTES); ?>','<?= htmlspecialchars($mf['operator'], ENT_QUOTES); ?>','<?= htmlspecialchars($mf['value'], ENT_QUOTES); ?>');
      <?php endforeach; ?>
   <?php endif; ?>
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

   // Bulk action
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_status_daftar_ulang_sd').serialize();

      if (bulk.val() == '1') {
         swal({
            title: "<?= cclang('Aktivasi Virtual Account'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?= cclang('Ya'); ?>",
            cancelButtonText: "<?= cclang('Batal'); ?>",
            closeOnConfirm: true,
            closeOnCancel: true
         }, function(isConfirm){
            if (isConfirm) {
               $('#st').remove();
               document.location.href = BASE_URL + '/administrator/status_daftar_ulang_sd/update_status?' + serialize_bulk;
            }
         });
         return false;

      } else if (bulk.val() == '2') {
         $('#modal_add_new').modal('show');
         var serialize = $('#form_status_daftar_ulang_sd').serializeArray();
         $('#ids').val(JSON.stringify(serialize));
         return false;

      } else if (bulk.val() == '3') {
         $('#modal_update_expired').modal('show');
         var serialize = $('#form_status_daftar_ulang_sd').serializeArray();
         $('#idsEx').val(JSON.stringify(serialize));
         return false;

      } else {
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
   });

   // Check all
   var checkAll = $('#check_all');
   var checkboxes = $('input.check');

   checkAll.on('ifChecked ifUnchecked', function(event){
      if (event.type == 'ifChecked') {
         checkboxes.iCheck('check');
      } else {
         checkboxes.iCheck('uncheck');
      }
   });

   checkboxes.on('ifChanged', function(event){
      if (checkboxes.filter(':checked').length == checkboxes.length) {
         checkAll.prop('checked', 'checked');
      } else {
         checkAll.removeProp('checked');
      }
      checkAll.iCheck('update');
   });
});
</script>
