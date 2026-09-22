
<style>
/* Top Buttons */
.btn-top { margin-right: 5px; border-radius: 3px; }

/* Table Styling */
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; text-align: center; }
.table td { font-size: 13px; vertical-align: middle; }

/* Sticky Action Column */
.th-aksi, .td-aksi {
   position: sticky;
   left: 0;
   z-index: 10;
   background: #f8f9fa;
   min-width: 110px;
   width: 110px;
}
.td-aksi { background: #fff; z-index: 5; }
.table-striped > tbody > tr:nth-of-type(odd) > .td-aksi { background: #f9f9f9; }
.table-hover > tbody > tr:hover > .td-aksi { background: #f5f5f5; }

/* Action Buttons */
.btn-aksi-stack {
   display: flex;
   flex-direction: column;
   gap: 4px;
}
.btn-aksi-stack .btn {
   width: 100%;
   padding: 4px 6px;
   font-size: 11px;
   border-radius: 3px;
   text-align: center;
   white-space: nowrap;
}
.btn-aksi-stack .btn i { margin-right: 3px; }

/* File thumbnails */
.file-thumb-wrap {
   display: inline-flex;
   align-items: center;
   justify-content: center;
   width: 38px;
   height: 38px;
   background: #f8f9fa;
   border: 1px solid #e0e0e0;
   border-radius: 5px;
   transition: all 0.2s ease;
}
.file-thumb-wrap:hover {
   background: #e8f4fc;
   border-color: #3498db;
   transform: scale(1.05);
}
.file-thumb {
   width: 28px;
   height: 28px;
   object-fit: contain;
}

/* Sisa OKR text */
.sisa-okr { font-size: 11px; color: #7f8c8d; display: block; margin-top: 3px; }

/* Filter Builder */
.filter-builder { transition: all 0.3s ease; }
.filter-builder:hover { box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
.filter-row { animation: fadeInRow 0.3s ease; }
@keyframes fadeInRow {
   from { opacity: 0; transform: translateY(-5px); }
   to   { opacity: 1; transform: translateY(0); }
}

/* Modal Detail */
#modal_detail_sma .modal-header {
   background: linear-gradient(135deg, #3498db, #2980b9);
   color: #fff;
   padding: 15px 20px;
}
#modal_detail_sma .modal-title { font-weight: 600; }
#modal_detail_sma .modal-body {
   padding: 20px;
   background: #f8f9fa;
   max-height: 75vh;
   overflow-y: auto;
}
.detail-section {
   background: #fff;
   border-radius: 5px;
   border: 1px solid #e0e0e0;
   margin-bottom: 15px;
   overflow: hidden;
}
.detail-section-title {
   background: #e8f4fc;
   color: #2c3e50;
   padding: 8px 15px;
   font-weight: 600;
   font-size: 13px;
   border-bottom: 1px solid #e0e0e0;
}
.detail-section-body { padding: 0; }
.detail-table {
   width: 100%;
   margin-bottom: 0;
}
.detail-table th {
   background: #fafafa;
   width: 35%;
   font-weight: 600;
   color: #555;
   padding: 10px 15px;
   border-bottom: 1px solid #f0f0f0;
   text-align: left;
}
.detail-table td {
   padding: 10px 15px;
   border-bottom: 1px solid #f0f0f0;
}
.detail-table tr:last-child th,
.detail-table tr:last-child td {
   border-bottom: none;
}
.detail-status {
   display: inline-block;
   padding: 4px 10px;
   border-radius: 3px;
   font-size: 12px;
   font-weight: 600;
   color: #fff;
}
.detail-file-list a {
   display: inline-flex;
   align-items: center;
   gap: 5px;
   margin: 2px;
   padding: 4px 8px;
   background: #f8f9fa;
   border: 1px solid #e0e0e0;
   border-radius: 3px;
   font-size: 12px;
   color: #333;
}
.detail-file-list a:hover {
   background: #e8f4fc;
   border-color: #3498db;
   text-decoration: none;
}
.detail-empty { color: #999; font-style: italic; }
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
      <i class="fa fa-money"></i> <?= cclang('program_anggaran_sma') ?>
      <small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('program_anggaran_sma') ?></li>
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
                  <i class="fa fa-list-alt"></i> Data Program Anggaran SMA
                  <span class="label bg-yellow" style="margin-left:10px"><?= $program_anggaran_sma_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php
                     if ($total_proposal < 50) {
                        is_allowed('program_anggaran_sma_add', function(){
                  ?>
                  <a class="btn btn-sm btn-success btn_add_new btn-top" title="<?= cclang('add_new_button', [cclang('program_anggaran_sma')]); ?> (Ctrl+a)" href="<?= site_url('administrator/program_anggaran_sma/add'); ?>">
                     <i class="fa fa-plus-square-o"></i> <?= cclang('add_new_button', [cclang('program_anggaran_sma')]); ?>
                  </a>
                  <?php
                        });
                     }
                  ?>
                  <?php is_allowed('program_anggaran_sma_export', function(){?>
                  <a class="btn btn-sm btn-success btn-top" title="<?= cclang('export'); ?> XLS" href="<?= site_url('administrator/program_anggaran_sma/export'); ?>">
                     <i class="fa fa-file-excel-o"></i> XLS
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">

               <!-- Advanced Filter Builder -->
               <form name="form_program_anggaran_sma" id="form_program_anggaran_sma" action="<?= base_url('administrator/program_anggaran_sma/index'); ?>" method="get">
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
                     <a class="btn btn-flat btn-default" href="<?= base_url('administrator/program_anggaran_sma'); ?>"><i class="fa fa-times"></i> Reset Semua</a>
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
                           <th class="th-aksi">Aksi</th>
                           <th>Nomor Program</th>
                           <th>Jenis Kegiatan</th>
                           <th>Sub Jenis Kegiatan</th>
                           <th>Nama Program</th>
                           <th>Tahun Ajaran</th>
                           <th>Tanggal Program</th>
                           <th>Nominal OKR</th>
                           <th>Nominal Pengajuan</th>
                           <th>File Proposal Pengajuan</th>
                           <th>File Proposal Keuangan</th>
                           <th>Status Pengajuan</th>
                           <th>Tanggal Pencairan</th>
                           <th>Catatan Pengajuan</th>
                           <th>File Laporan Kegiatan</th>
                           <th>File Laporan Keuangan</th>
                           <th>Status Laporan</th>
                           <th>Catatan Laporan</th>
                           <th>Tanggal Pengajuan</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_program_anggaran_sma">
                     <?php if($program_anggaran_sma_counts > 0): ?>
                     <?php foreach($program_anggaran_smas as $program_anggaran_sma): ?>
                        <tr>
                           <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $program_anggaran_sma->id; ?>">
                           </td>
                           <td class="td-aksi">
                              <div class="btn-aksi-stack">
                                 <?php is_allowed('program_anggaran_sma_view', function() use ($program_anggaran_sma){?>
                                 <button type="button" class="btn btn-info btn-detail-sma" data-id="<?= $program_anggaran_sma->id; ?>" title="Lihat detail">
                                    <i class="fa fa-eye"></i> Detail
                                 </button>
                                 <?php }) ?>
                                 <?php
                                    if ($program_anggaran_sma->status_pengajuan == 1 || $program_anggaran_sma->status_pengajuan == 5
                                       || ($program_anggaran_sma->status_pengajuan == 6 && $program_anggaran_sma->status_laporan == 1)
                                       || ($program_anggaran_sma->status_pengajuan == 6 && $program_anggaran_sma->status_laporan == 5)
                                       || $program_anggaran_sma->status_laporan == 5
                                       || $program_anggaran_sma->status_laporan == 1
                                       || ($program_anggaran_sma->status_pengajuan == 4 && $this->session->userdata('user_keuangan') == true)) {
                                 ?>
                                 <?php is_allowed('program_anggaran_sma_update', function() use ($program_anggaran_sma){?>
                                 <a href="<?= site_url('administrator/program_anggaran_sma/edit/' . $program_anggaran_sma->id); ?>" class="btn btn-warning" title="Edit data">
                                    <i class="fa fa-edit"></i> Edit
                                 </a>
                                 <?php }) ?>
                                 <?php } ?>
                                 <?php if ($program_anggaran_sma->status_pengajuan != 4 && $program_anggaran_sma->status_pengajuan != 6): ?>
                                    <?php is_allowed('program_anggaran_sma_delete', function() use ($program_anggaran_sma){?>
                                    <a href="javascript:void(0);" data-href="<?= site_url('administrator/program_anggaran_sma/delete/' . $program_anggaran_sma->id); ?>" class="btn btn-danger remove-data" title="Hapus data">
                                       <i class="fa fa-trash"></i> Hapus
                                    </a>
                                    <?php }) ?>
                                 <?php endif; ?>
                                 <?php if (!empty($program_anggaran_sma->token_confirmation)): ?>
                                    <a href="<?= site_url('administrator/program_anggaran_sma/resend_email/' . $program_anggaran_sma->id); ?>" class="btn btn-primary" title="Kirim Ulang Email Konfirmasi">
                                       <i class="fa fa-envelope"></i> Resend
                                    </a>
                                 <?php endif; ?>
                              </div>
                           </td>
                           <?php
                              //hitung sisa okr
                              $okr = $program_anggaran_sma->nominal_okr;
                              $get_program_by_jenis_kegiatan = $this->mymodel->getbywhere("program_anggaran_sma", "nomor_program = '".$program_anggaran_sma->nomor_program."' and jenis_kegiatan=", $program_anggaran_sma->jenis_kegiatan, "result");
                              $okr_terpakai = 0;
                              foreach ($get_program_by_jenis_kegiatan as $key => $value) {
                                  $okr_terpakai = $okr_terpakai + $value->nominal_pengajuan;
                              }
                              $okr = $okr - $okr_terpakai;
                           ?>
                           <td>
                              <?php if (!empty($program_anggaran_sma->nomor_program)): ?>
                                 <a href="<?= site_url('administrator/program_anggaran/view/'.$program_anggaran_sma->nomor_program.'?popup=show'); ?>" class="popup-view"><?= $program_anggaran_sma->program_anggaran_nomor_program ?: $program_anggaran_sma->nomor_program; ?></a>
                              <?php endif; ?>
                           </td>
                           <td><?= _ent($program_anggaran_sma->jenis_kegiatan); ?></td>
                           <td><?= _ent($program_anggaran_sma->sub_jenis_kegiatan); ?></td>
                           <td><?= _ent($program_anggaran_sma->nama_program); ?></td>
                           <td style="text-align:center"><?= _ent($program_anggaran_sma->tahun_ajaran); ?></td>
                           <td style="text-align:center"><?= !empty($program_anggaran_sma->tanggal_program) ? date("d-m-Y", strtotime($program_anggaran_sma->tanggal_program)) : '-'; ?></td>
                           <td style="text-align:right">Rp <?= number_format($program_anggaran_sma->nominal_okr, 0, ',', '.'); ?></td>
                           <td style="text-align:right">
                              Rp <?= number_format($program_anggaran_sma->nominal_pengajuan, 0, ',', '.'); ?>
                              <span class="sisa-okr"><strong>Sisa OKR: Rp <?= number_format($okr, 0, ',', '.'); ?></strong></span>
                           </td>
                           <td style="text-align:center">
                              <?php if (!empty($program_anggaran_sma->file_proposal_pengajuan)): ?>
                                 <a href="<?= BASE_URL . 'uploads/program_anggaran_sma/' . $program_anggaran_sma->file_proposal_pengajuan; ?>" class="fancybox" rel="group" title="Lihat file proposal pengajuan">
                                    <span class="file-thumb-wrap">
                                       <?php if (is_image($program_anggaran_sma->file_proposal_pengajuan)): ?>
                                          <img src="<?= BASE_URL . 'uploads/program_anggaran_sma/' . $program_anggaran_sma->file_proposal_pengajuan; ?>" class="file-thumb" alt="file proposal">
                                       <?php else: ?>
                                          <img src="<?= get_icon_file($program_anggaran_sma->file_proposal_pengajuan); ?>" class="file-thumb" alt="file proposal">
                                       <?php endif; ?>
                                    </span>
                                 </a>
                              <?php else: ?>
                                 <small class="text-muted">-</small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php if (!empty($program_anggaran_sma->file_proposal_keuangan)): ?>
                                 <a href="<?= BASE_URL . 'uploads/program_anggaran_sma/' . $program_anggaran_sma->file_proposal_keuangan; ?>" class="fancybox" rel="group" title="Lihat file proposal keuangan">
                                    <span class="file-thumb-wrap">
                                       <?php if (is_image($program_anggaran_sma->file_proposal_keuangan)): ?>
                                          <img src="<?= BASE_URL . 'uploads/program_anggaran_sma/' . $program_anggaran_sma->file_proposal_keuangan; ?>" class="file-thumb" alt="file proposal keuangan">
                                       <?php else: ?>
                                          <img src="<?= get_icon_file($program_anggaran_sma->file_proposal_keuangan); ?>" class="file-thumb" alt="file proposal keuangan">
                                       <?php endif; ?>
                                    </span>
                                 </a>
                              <?php else: ?>
                                 <small class="text-muted">-</small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php if (!empty($program_anggaran_sma->status_pengajuan)): ?>
                                 <?php
                                    $sp = $program_anggaran_sma->status_pengajuan;
                                    if ($sp == "1" || $sp == "5") { $bg = "#EA4335"; $col = "#FFFFFF"; }
                                    elseif ($sp == "2" || $sp == "3") { $bg = "#FBBC04"; $col = "#000000"; }
                                    elseif ($sp == "4") { $bg = "#34A853"; $col = "#FFFFFF"; }
                                    elseif ($sp == "6") { $bg = "#4387F8"; $col = "#FFFFFF"; }
                                    else { $bg = "#95a5a6"; $col = "#FFFFFF"; }
                                 ?>
                                 <span style="background-color:<?= $bg; ?>;color:<?= $col; ?>;padding:5px 10px;border-radius:3px;font-size:11px;display:inline-block;">
                                    <?= $program_anggaran_sma->program_anggaran_status_pengajuan_status; ?>
                                 </span>
                                 <?php if (!empty($program_anggaran_sma->file_kwitansi)): ?>
                                    <div style="margin-top:5px;text-align:center;">
                                       <a href="<?= base_url('uploads/program_anggaran_sma/').$program_anggaran_sma->file_kwitansi; ?>" target="_blank">
                                          <small><i class="fa fa-file-text-o"></i> Lihat Kwitansi</small>
                                       </a>
                                    </div>
                                 <?php endif; ?>
                              <?php else: ?>
                                 <small class="text-muted">-</small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center"><?= !empty($program_anggaran_sma->tanggal_pencairan) ? date("d-m-Y", strtotime($program_anggaran_sma->tanggal_pencairan)) : '<small class="text-muted">-</small>'; ?></td>
                           <td><?= _ent($program_anggaran_sma->catatan_pengajuan); ?></td>
                           <td style="text-align:center">
                              <?php if (!empty($program_anggaran_sma->file_laporan_kegiatan)): ?>
                                 <a href="<?= BASE_URL . 'uploads/program_anggaran_sma/' . $program_anggaran_sma->file_laporan_kegiatan; ?>" class="fancybox" rel="group" title="Lihat file laporan kegiatan">
                                    <span class="file-thumb-wrap">
                                       <?php if (is_image($program_anggaran_sma->file_laporan_kegiatan)): ?>
                                          <img src="<?= BASE_URL . 'uploads/program_anggaran_sma/' . $program_anggaran_sma->file_laporan_kegiatan; ?>" class="file-thumb" alt="file laporan kegiatan">
                                       <?php else: ?>
                                          <img src="<?= get_icon_file($program_anggaran_sma->file_laporan_kegiatan); ?>" class="file-thumb" alt="file laporan kegiatan">
                                       <?php endif; ?>
                                    </span>
                                 </a>
                              <?php else: ?>
                                 <small class="text-muted">-</small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center">
                              <?php foreach (explode(',', $program_anggaran_sma->file_laporan_keuangan) as $file): ?>
                                 <?php if (!empty($file)): ?>
                                    <a href="<?= BASE_URL . 'uploads/program_anggaran_sma/' . $file; ?>" class="fancybox" rel="group" title="Lihat file laporan keuangan">
                                       <span class="file-thumb-wrap">
                                          <?php if (is_image($file)): ?>
                                             <img src="<?= BASE_URL . 'uploads/program_anggaran_sma/' . $file; ?>" class="file-thumb" alt="file laporan keuangan">
                                          <?php else: ?>
                                             <img src="<?= get_icon_file($file); ?>" class="file-thumb" alt="file laporan keuangan">
                                          <?php endif; ?>
                                       </span>
                                    </a>
                                 <?php endif; ?>
                              <?php endforeach; ?>
                           </td>
                           <td style="text-align:center">
                              <?php if (!empty($program_anggaran_sma->status_laporan)): ?>
                                 <?php
                                    $sl = $program_anggaran_sma->status_laporan;
                                    if ($sl == "1" || $sl == "5") { $bg = "#EA4335"; $col = "#FFFFFF"; }
                                    elseif ($sl == "2" || $sl == "3") { $bg = "#FBBC04"; $col = "#000000"; }
                                    elseif ($sl == "4") { $bg = "#34A853"; $col = "#FFFFFF"; }
                                    elseif ($sl == "6") { $bg = "#4387F8"; $col = "#FFFFFF"; }
                                    else { $bg = "#95a5a6"; $col = "#FFFFFF"; }
                                 ?>
                                 <span style="background-color:<?= $bg; ?>;color:<?= $col; ?>;padding:5px 10px;border-radius:3px;font-size:11px;display:inline-block;">
                                    <?= $program_anggaran_sma->program_anggaran_status_laporan_status; ?>
                                 </span>
                              <?php else: ?>
                                 <small class="text-muted">-</small>
                              <?php endif; ?>
                           </td>
                           <td><?= _ent($program_anggaran_sma->catatan_laporan); ?></td>
                           <td style="text-align:center"><small><?= !empty($program_anggaran_sma->created_at) ? date("d-m-Y H:i", strtotime($program_anggaran_sma->created_at)) : '-'; ?></small></td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="20" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">
                                 <?php if(!empty($multi_filters) || !empty($this->input->get('q'))): ?>
                                    Data tidak ditemukan untuk filter/pencarian yang dipilih
                                 <?php else: ?>
                                    Data program anggaran SMA belum tersedia
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

<!-- ============ MODAL DETAIL SMA =============== -->
<div class="modal fade" id="modal_detail_sma" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius:5px;overflow:hidden">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8">&times;</button>
            <h4 class="modal-title" style="font-weight:600"><i class="fa fa-eye"></i> Detail Program Anggaran SMA</h4>
         </div>
         <div class="modal-body" id="detail_sma_content">
            <div class="text-center" style="padding:40px">
               <i class="fa fa-spinner fa-spin" style="font-size:40px;color:#3498db"></i>
               <p style="margin-top:10px;color:#666">Memuat data...</p>
            </div>
         </div>
         <div class="modal-footer" style="background:#fff;padding:12px 20px">
            <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
         </div>
      </div>
   </div>
</div>

<!-- Page script -->
<script>
// ===== FILTER BUILDER =====
<?php
   // Ambil opsi status pengajuan & laporan dari tabel master
   $sp_options = array();
   $sl_options = array();
   if (isset($this->mymodel)) {
      $sp_raw = $this->mymodel->withquery("SELECT id_program_anggaran_status_pengajuan AS id, status AS text FROM program_anggaran_status_pengajuan ORDER BY id_program_anggaran_status_pengajuan ASC", "result");
      $sl_raw = $this->mymodel->withquery("SELECT id_program_anggaran_status_laporan AS id, status AS text FROM program_anggaran_status_laporan ORDER BY id_program_anggaran_status_laporan ASC", "result");
      if (!empty($sp_raw)) {
         foreach ($sp_raw as $r) {
            $sp_options[] = array('id' => (string)$r->id, 'text' => $r->text);
         }
      }
      if (!empty($sl_raw)) {
         foreach ($sl_raw as $r) {
            $sl_options[] = array('id' => (string)$r->id, 'text' => $r->text);
         }
      }
   }
?>
var filterFields = [
   { value: 'nomor_program', label: 'Nomor Program', type: 'text', operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'jenis_kegiatan', label: 'Jenis Kegiatan', type: 'text', operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'sub_jenis_kegiatan', label: 'Sub Jenis Kegiatan', type: 'text', operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'nama_program', label: 'Nama Program', type: 'text', operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'tahun_ajaran', label: 'Tahun Ajaran', type: 'text', operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'tanggal_program', label: 'Tanggal Program', type: 'date', operators: ['equals','gt','lt'] },
   { value: 'nominal_okr', label: 'Nominal OKR', type: 'number', operators: ['equals','gt','lt','contains'] },
   { value: 'nominal_pengajuan', label: 'Nominal Pengajuan', type: 'number', operators: ['equals','gt','lt','contains'] },
   { value: 'status_pengajuan', label: 'Status Pengajuan', type: 'select_sp', operators: ['equals'] },
   { value: 'status_laporan', label: 'Status Laporan', type: 'select_sl', operators: ['equals'] },
   { value: 'tanggal_pencairan', label: 'Tanggal Pencairan', type: 'date', operators: ['equals','gt','lt'] }
];
var operatorLabels = { 'contains':'Mengandung','equals':'Sama dengan','starts_with':'Diawali','ends_with':'Diakhiri','gt':'Lebih dari','lt':'Kurang dari' };
var spOptions = <?= json_encode($sp_options); ?>;
var slOptions = <?= json_encode($sl_options); ?>;
var filterIndex = 0;

function getSelectOptions(f){
   if (f === 'status_pengajuan') return spOptions;
   if (f === 'status_laporan') return slOptions;
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
   if (t === 'text' || t === 'number' || t === 'date') {
      var it = (t === 'number') ? 'number' : (t === 'date' ? 'date' : 'text');
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
      var serialize_bulk = $('#form_program_anggaran_sma').serialize();

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
               document.location.href = BASE_URL + '/administrator/program_anggaran_sma/delete?' + serialize_bulk;
            }
          });
        return false;
      } else if(bulk.val() == '')  {
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

   // ===== DETAIL MODAL =====
   $('.btn-detail-sma').click(function(){
      var id = $(this).data('id');
      var content = $('#detail_sma_content');

      content.html('<div class="text-center" style="padding:40px"><i class="fa fa-spinner fa-spin" style="font-size:40px;color:#3498db"></i><p style="margin-top:10px;color:#666">Memuat data...</p></div>');
      $('#modal_detail_sma').modal('show');

      $.ajax({
         url: '<?= site_url('administrator/program_anggaran_sma/get_detail/' . ''); ?>' + id,
         method: 'GET',
         dataType: 'json',
         success: function(data) {
            if (!data || !data.success) {
               content.html('<div class="text-center" style="padding:40px"><i class="fa fa-exclamation-triangle" style="font-size:40px;color:#e74c3c"></i><p style="margin-top:10px;color:#666">' + (data.message || 'Data tidak ditemukan') + '</p></div>');
               return;
            }

            var fileEmpty = '<span class="detail-empty">-</span>';
            var baseUrl = '<?= BASE_URL . 'uploads/program_anggaran_sma/'; ?>';

            function renderFile(name, label) {
               if (!name) return fileEmpty;
               return '<a href="' + baseUrl + name + '" target="_blank" title="Lihat ' + label + '"><i class="fa fa-file-o"></i> ' + name + '</a>';
            }

            function renderFileList(names) {
               if (!names) return fileEmpty;
               var list = names.split(',');
               var html = '<div class="detail-file-list">';
               var hasFile = false;
               for (var i = 0; i < list.length; i++) {
                  var f = list[i].trim();
                  if (f) {
                     hasFile = true;
                     html += renderFile(f, 'File');
                  }
               }
               html += '</div>';
               return hasFile ? html : fileEmpty;
            }

            var spBadge = data.status_pengajuan_text ? '<span class="detail-status" style="background:' + data.status_pengajuan_color + '">' + data.status_pengajuan_text + '</span>' : fileEmpty;
            var slBadge = data.status_laporan_text ? '<span class="detail-status" style="background:' + data.status_laporan_color + '">' + data.status_laporan_text + '</span>' : fileEmpty;

            var html = '';

            html += '<div class="detail-section">';
            html += '<div class="detail-section-title"><i class="fa fa-info-circle"></i> Informasi Utama</div>';
            html += '<div class="detail-section-body">';
            html += '<table class="detail-table">';
            html += '<tr><th>Nomor Program</th><td>' + (data.nomor_program || '-') + '</td></tr>';
            html += '<tr><th>Nama Program</th><td>' + (data.nama_program || '-') + '</td></tr>';
            html += '<tr><th>Jenis Kegiatan</th><td>' + (data.jenis_kegiatan || '-') + '</td></tr>';
            html += '<tr><th>Sub Jenis Kegiatan</th><td>' + (data.sub_jenis_kegiatan || '-') + '</td></tr>';
            html += '<tr><th>Tahun Ajaran</th><td>' + (data.tahun_ajaran || '-') + '</td></tr>';
            html += '</table>';
            html += '</div></div>';

            html += '<div class="detail-section">';
            html += '<div class="detail-section-title"><i class="fa fa-calendar"></i> Tanggal & Nominal</div>';
            html += '<div class="detail-section-body">';
            html += '<table class="detail-table">';
            html += '<tr><th>Tanggal Program</th><td>' + (data.tanggal_program || '-') + '</td></tr>';
            html += '<tr><th>Tanggal Pengajuan</th><td>' + (data.tanggal_pengajuan || '-') + '</td></tr>';
            html += '<tr><th>Tanggal Pencairan</th><td>' + (data.tanggal_pencairan || '-') + '</td></tr>';
            html += '<tr><th>Nominal OKR</th><td>Rp ' + (data.nominal_okr || '0') + '</td></tr>';
            html += '<tr><th>Nominal Pengajuan</th><td>Rp ' + (data.nominal_pengajuan || '0') + '</td></tr>';
            html += '<tr><th>Sisa OKR</th><td>Rp ' + (data.sisa_okr || '0') + '</td></tr>';
            html += '</table>';
            html += '</div></div>';

            html += '<div class="detail-section">';
            html += '<div class="detail-section-title"><i class="fa fa-flag"></i> Status</div>';
            html += '<div class="detail-section-body">';
            html += '<table class="detail-table">';
            html += '<tr><th>Status Pengajuan</th><td>' + spBadge + '</td></tr>';
            html += '<tr><th>Status Laporan</th><td>' + slBadge + '</td></tr>';
            html += '</table>';
            html += '</div></div>';

            html += '<div class="detail-section">';
            html += '<div class="detail-section-title"><i class="fa fa-align-left"></i> Deskripsi Kegiatan</div>';
            html += '<div class="detail-section-body">';
            html += '<table class="detail-table">';
            html += '<tr><th>Dasar Pelaksanaan</th><td>' + (data.dasar_pelaksanaan_kegiatan || '-') + '</td></tr>';
            html += '<tr><th>Tema Kegiatan</th><td>' + (data.tema_kegiatan || '-') + '</td></tr>';
            html += '<tr><th>Tujuan Kegiatan</th><td>' + (data.tujuan_kegiatan || '-') + '</td></tr>';
            html += '<tr><th>Gambaran Acara</th><td>' + (data.gambaran_acara_kegiatan || '-') + '</td></tr>';
            html += '<tr><th>Hasil Diharapkan</th><td>' + (data.hasil_yang_diharapkan || '-') + '</td></tr>';
            html += '<tr><th>Tempat & Waktu</th><td>' + (data.tempat_dan_waktu_pelaksanaan || '-') + '</td></tr>';
            html += '<tr><th>Mitra Kegiatan</th><td>' + (data.mitra_kegiatan || '-') + '</td></tr>';
            html += '<tr><th>Kepanitiaan</th><td>' + (data.kepanitiaan || '-') + '</td></tr>';
            html += '</table>';
            html += '</div></div>';

            html += '<div class="detail-section">';
            html += '<div class="detail-section-title"><i class="fa fa-commenting"></i> Catatan</div>';
            html += '<div class="detail-section-body">';
            html += '<table class="detail-table">';
            html += '<tr><th>Catatan Pengajuan</th><td>' + (data.catatan_pengajuan || '<span class="detail-empty">-</span>') + '</td></tr>';
            html += '<tr><th>Catatan Laporan</th><td>' + (data.catatan_laporan || '<span class="detail-empty">-</span>') + '</td></tr>';
            html += '</table>';
            html += '</div></div>';

            html += '<div class="detail-section">';
            html += '<div class="detail-section-title"><i class="fa fa-paperclip"></i> File Lampiran</div>';
            html += '<div class="detail-section-body">';
            html += '<table class="detail-table">';
            html += '<tr><th>File Proposal Pengajuan</th><td>' + renderFile(data.file_proposal_pengajuan, 'File Proposal Pengajuan') + '</td></tr>';
            html += '<tr><th>File Proposal Keuangan</th><td>' + renderFile(data.file_proposal_keuangan, 'File Proposal Keuangan') + '</td></tr>';
            html += '<tr><th>File Laporan Kegiatan</th><td>' + renderFile(data.file_laporan_kegiatan, 'File Laporan Kegiatan') + '</td></tr>';
            html += '<tr><th>File Laporan Keuangan</th><td>' + renderFileList(data.file_laporan_keuangan) + '</td></tr>';
            html += '<tr><th>File Kwitansi</th><td>' + renderFile(data.file_kwitansi, 'File Kwitansi') + '</td></tr>';
            html += '</table>';
            html += '</div></div>';

            content.html(html);

            if (typeof $.fn.fancybox !== 'undefined') {
               $('.fancybox').fancybox();
            }
         },
         error: function(err) {
            content.html('<div class="text-center" style="padding:40px"><i class="fa fa-exclamation-triangle" style="font-size:40px;color:#e74c3c"></i><p style="margin-top:10px;color:#666">Gagal memuat data</p></div>');
         }
      });
   });
   // ===== END DETAIL MODAL =====

});
</script>