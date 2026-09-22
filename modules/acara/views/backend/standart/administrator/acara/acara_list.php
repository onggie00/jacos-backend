<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.css">
<style>
/* Shortener link */
.shortener-url { font-size: 11px; word-break: break-all; max-width: 180px; display: inline-block; vertical-align: middle; color: var(--labs-text-muted); }

/* Override DataTables agar seragam dengan labs-table */
.labs-table.dataTable thead th { background: linear-gradient(180deg, #FFFBF5, #F5F0EA); border-bottom: 2px solid var(--labs-border-strong); }
.labs-table.dataTable tbody td { border-bottom: 1px solid var(--labs-border); }
.labs-table.dataTable tbody tr:hover { background: var(--labs-bg-soft); }
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
   <h1><i class="fa fa-calendar"></i> <?= cclang('acara') ?> <small class="labs-text-muted"><?= cclang('list_all'); ?></small></h1>
   <ol class="breadcrumb"><li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li><li class="active"><?= cclang('acara') ?></li></ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">

            <!-- Card Header -->
            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-calendar"></i> Data Acara
                  <span class="labs-badge labs-badge--orange"><?= $acara_counts; ?> Data</span>
               </h3>
               <div>
                  <?php is_allowed('acara_add', function(){?>
                  <a class="labs-btn labs-btn--success" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('acara')]); ?> (Ctrl+a)" href="<?= site_url('administrator/acara/add'); ?>">
                     <i class="fa fa-plus"></i> Tambah Acara
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="labs-card__body">

               <!-- Filter Bar -->
               <form name="form_acara" id="form_acara" action="<?= base_url('administrator/acara/index'); ?>">
               <div class="labs-filter-inline">
                  <div class="labs-filter-inline__search">
                     <i class="fa fa-search labs-filter-inline__search-icon"></i>
                     <input type="text" class="form-control" name="q" id="filter" placeholder="Cari data acara..." value="<?= $this->input->get('q'); ?>">
                  </div>
                  <button type="submit" class="labs-btn labs-btn--primary"><i class="fa fa-search"></i> Cari</button>
                  <?php if(!empty($this->input->get('q'))): ?>
                  <a class="labs-btn labs-btn--default" href="<?= base_url('administrator/acara'); ?>"><i class="fa fa-undo"></i> Reset</a>
                  <span class="labs-text-muted labs-fs-sm" style="align-self:center">
                     Hasil: <strong>"<?= $this->input->get('q'); ?>"</strong>
                  </span>
                  <?php endif; ?>
               </div>

               <!-- Data Table -->
               <div class="labs-table-wrap"> 
                  <table class="labs-table dataTable" id="acara_table">
                     <thead>
                        <tr>
                           <th width="30">
                              <input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all">
                           </th>
                           <th>Nama Acara</th>
                           <th>Acara Untuk</th>
                           <th>QR Code In</th>
                           <th>QR Code Out</th>
                           <th>Waktu</th>
                           <th>Peserta</th>
                           <th>Lokasi</th>
                           <th>Sertifikat</th>
                           <th>Shortener Link</th>
                           <th width="220">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_acara">
                     <?php if($acara_counts > 0): ?>
                     <?php foreach($acaras as $acara): ?>
                        <tr>
                           <td class="labs-text-center">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $acara->id_acara; ?>">
                           </td>
                           <td>
                              <div class="labs-name-cell">
                                 <span class="labs-avatar labs-avatar--sm"><i class="fa fa-calendar"></i></span>
                                 <span class="labs-fw-semi"><?= _ent($acara->nama_acara); ?></span>
                              </div>
                           </td>
                           <td><?= str_replace(",", "<br>", $acara->peserta_acara); ?></td>

                           <!-- QR Code In -->
                           <td class="labs-text-center">
                              <?php if (!empty($acara->qr_code)): ?>
                                 <?php if (is_image($acara->qr_code)): ?>
                                 <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code; ?>">
                                    <img src="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code; ?>" alt="qr_code" width="40px" style="border-radius:var(--labs-radius-sm)">
                                 </a><br>
                                 <span class="labs-badge labs-badge--success labs-badge--solid" style="font-size:10px"><?= $acara->unique_code; ?></span>
                                 <?php else: ?>
                                 <a href="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code; ?>">
                                    <img src="<?= get_icon_file($acara->qr_code); ?>" alt="qr_code" width="40px">
                                 </a>
                                 <?php endif; ?>
                              <?php else: ?>
                                 <span class="labs-text-light"><i class="fa fa-qrcode"></i> -</span>
                              <?php endif; ?>
                           </td>

                           <!-- QR Code Out -->
                           <td class="labs-text-center">
                              <?php if (!empty($acara->qr_code_finish)): ?>
                                 <?php if (is_image($acara->qr_code_finish)): ?>
                                 <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code_finish; ?>">
                                    <img src="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code_finish; ?>" alt="qr_code_finish" width="40px" style="border-radius:var(--labs-radius-sm)">
                                 </a><br>
                                 <span class="labs-badge labs-badge--success labs-badge--solid" style="font-size:10px"><?= $acara->unique_code_finish; ?></span>
                                 <?php else: ?>
                                 <a href="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code_finish; ?>">
                                    <img src="<?= get_icon_file($acara->qr_code_finish); ?>" alt="qr_code_finish" width="40px">
                                 </a>
                                 <?php endif; ?>
                              <?php else: ?>
                                 <span class="labs-text-light"><i class="fa fa-qrcode"></i> -</span>
                              <?php endif; ?>
                           </td>

                           <!-- Waktu -->
                           <td class="labs-text-center" style="white-space:nowrap">
                              <div class="labs-fw-semi"><?= formatHari($acara->waktu_mulai).' - '.formatHari($acara->waktu_selesai); ?></div>
                              <small class="labs-text-muted"><?= formatTanggal($acara->waktu_mulai); ?> - <?= formatTanggal($acara->waktu_selesai); ?></small>
                           </td> 

                           <!-- Peserta -->
                           <td class="labs-text-center">
                              <button class="labs-btn labs-btn--info labs-btn--sm btn_detail" type="button" title="Detail Peserta" data-toggle="modal" data-target="#modal_detail" data-id="<?= $acara->id_acara; ?>" data-nama="<?= _ent($acara->nama_acara); ?>" data-waktu="<?= _ent(formatHari($acara->waktu_mulai).' - '.formatHari($acara->waktu_selesai)); ?>" data-tanggal="<?= formatTanggal($acara->waktu_mulai); ?>" data-lokasi="<?= _ent($acara->lokasi); ?>">
                                 <i class="fa fa-users"></i> Peserta
                              </button>
                           </td> 

                           <!-- Lokasi -->
                           <td class="labs-text-center">
                              <span class="labs-text-muted"><i class="fa fa-map-marker labs-text-primary"></i> <?= _ent($acara->lokasi); ?></span>
                           </td>

                           <!-- Sertifikat -->
                           <td class="labs-text-center">
                              <?php if (!empty($acara->is_certificated)): ?>
                                 <span class="labs-badge labs-badge--success labs-badge--solid"><i class="fa fa-check"></i> Ya</span>
                              <?php else: ?>
                                 <span class="labs-badge labs-badge--default"><i class="fa fa-times"></i> Tidak</span>
                              <?php endif; ?>
                           </td>
                           
                           <!-- Shortener Link -->
                           <td class="labs-text-center">
                              <?php $public_link = base_url('peserta_acara/' . $acara->id_acara); ?>
                              <div style="margin-bottom:4px">
                                 <span class="shortener-url" id="link_<?= $acara->id_acara; ?>">peserta_acara/<?= $acara->id_acara; ?></span>
                              </div>
                              <button type="button" class="labs-btn labs-btn--default labs-btn--xs btn-copy-link" data-link="<?= $public_link; ?>" title="Copy Link">
                                 <i class="fa fa-copy"></i> Copy
                              </button>
                           </td>
                           
                           <!-- Aksi -->
                           <td style="text-align:center;white-space:nowrap">
                              <div class="labs-row-actions" style="justify-content:center">
                                 <button type="button" class="labs-btn labs-btn--info labs-btn--sm btn_detail_acara" title="Detail Acara" data-toggle="modal" data-target="#modal_detail_acara" data-id="<?= $acara->id_acara; ?>" data-nama="<?= _ent($acara->nama_acara); ?>" data-peserta="<?= _ent($acara->peserta_acara); ?>" data-waktu_mulai="<?= $acara->waktu_mulai; ?>" data-waktu_selesai="<?= $acara->waktu_selesai; ?>" data-lokasi="<?= _ent($acara->lokasi); ?>" data-narasumber="<?= _ent($acara->narasumber); ?>" data-keterangan="<?= _ent($acara->keterangan); ?>" data-is_certificated="<?= $acara->is_certificated; ?>" data-no_certificate="<?= _ent($acara->no_certificate); ?>" data-evaluation_url="<?= _ent($acara->evaluation_url); ?>" data-created_at="<?= $acara->created_at; ?>">
                                    <i class="fa fa-eye"></i> Detail
                                 </button>
                                 <?php is_allowed('acara_update', function() use ($acara){?>
                                 <a href="<?= site_url('administrator/acara/edit/' . $acara->id_acara); ?>" class="labs-btn labs-btn--warning labs-btn--sm" title="Edit Acara">
                                    <i class="fa fa-edit"></i> Edit
                                 </a>
                                 <?php }) ?>
                                 <?php is_allowed('acara_delete', function() use ($acara){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/acara/delete/' . $acara->id_acara); ?>" class="labs-btn labs-btn--danger labs-btn--sm remove-data" title="Hapus Acara">
                                    <i class="fa fa-trash"></i> Hapus
                                 </a>
                                 <?php }) ?>
                                 <?php if (!empty($acara->is_certificated) && !empty($acara->file_certificate)): ?>
                                 <button type="button" class="labs-btn labs-btn--success labs-btn--sm btn_sample_sertifikat" title="Lihat Sample Sertifikat" data-id="<?= $acara->id_acara; ?>">
                                    <i class="fa fa-file-pdf-o"></i> Sample Sertifikat
                                 </button>
                                 <?php endif; ?>
                              </div>
                              <div class="labs-row-actions labs-mt-1" style="justify-content:center">
                                 <a href="<?= site_url('administrator/acara_presensi_evaluasi?f=id_acara&q='.$acara->id_acara); ?>" class="labs-btn labs-btn--success labs-btn--xs" title="Hasil Evaluasi">
                                    <i class="fa fa-bar-chart"></i> Hasil
                                 </a>
                                 <a href="<?= site_url('administrator/acara_evaluasi_form?f=id_acara&q='.$acara->id_acara); ?>" class="labs-btn labs-btn--primary labs-btn--xs" title="Form Evaluasi">
                                    <i class="fa fa-list-alt"></i> Form
                                 </a>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="11">
                              <div class="labs-empty">
                                 <i class="fa fa-inbox"></i>
                                 <?php if(!empty($this->input->get('q'))): ?>
                                    <p>Data acara tidak ditemukan untuk pencarian "<strong><?= htmlspecialchars($this->input->get('q')); ?></strong>"</p>
                                 <?php else: ?>
                                    <p>Data acara tidak tersedia</p>
                                 <?php endif; ?>
                              </div>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <!-- Bulk Action & Pagination -->
               <div class="labs-flex-between labs-mt-4">
                  <div class="labs-flex labs-flex-gap-2" style="align-items:center">
                     <select class="form-control" name="bulk" id="bulk" style="width:180px;height:34px;font-size:12px">
                        <option value="">-- Bulk Action --</option>
                        <option value="delete">Hapus Terpilih</option>
                     </select>
                     <button type="button" class="labs-btn labs-btn--default" id="apply">Terapkan</button>
                  </div>
                  <div class="dataTables_paginate paging_simple_numbers">
                     <?= $pagination; ?>
                  </div>
               </div>
               </form>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL DETAIL ACARA -->
<div class="modal fade labs-modal" id="modal_detail_acara" tabindex="-1" role="dialog" aria-labelledby="modalDetailAcara" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-calendar"></i> Detail Acara</h4>
         </div>
         <div class="modal-body">
            <div class="labs-info-grid" style="grid-template-columns: repeat(2, 1fr);">
               <div class="labs-info-grid__item">
                  <div class="labs-info-grid__label"><i class="fa fa-tag"></i> Nama Acara</div>
                  <div class="labs-info-grid__value" id="detail_nama">-</div>
               </div>
               <div class="labs-info-grid__item">
                  <div class="labs-info-grid__label"><i class="fa fa-users"></i> Acara Untuk</div>
                  <div class="labs-info-grid__value" id="detail_peserta_acara">-</div>
               </div>
               <div class="labs-info-grid__item">
                  <div class="labs-info-grid__label"><i class="fa fa-clock-o"></i> Waktu Mulai</div>
                  <div class="labs-info-grid__value" id="detail_waktu_mulai">-</div>
               </div>
               <div class="labs-info-grid__item">
                  <div class="labs-info-grid__label"><i class="fa fa-clock-o"></i> Waktu Selesai</div>
                  <div class="labs-info-grid__value" id="detail_waktu_selesai">-</div>
               </div>
               <div class="labs-info-grid__item">
                  <div class="labs-info-grid__label"><i class="fa fa-map-marker"></i> Lokasi</div>
                  <div class="labs-info-grid__value" id="detail_lokasi">-</div>
               </div>
               <div class="labs-info-grid__item">
                  <div class="labs-info-grid__label"><i class="fa fa-microphone"></i> Narasumber</div>
                  <div class="labs-info-grid__value" id="detail_narasumber">-</div>
               </div>
               <div class="labs-info-grid__item" style="grid-column: span 2;">
                  <div class="labs-info-grid__label"><i class="fa fa-info-circle"></i> Keterangan</div>
                  <div class="labs-info-grid__value" id="detail_keterangan">-</div>
               </div>
               <div class="labs-info-grid__item">
                  <div class="labs-info-grid__label"><i class="fa fa-certificate"></i> Sertifikat</div>
                  <div class="labs-info-grid__value" id="detail_sertifikat">-</div>
               </div>
               <div class="labs-info-grid__item">
                  <div class="labs-info-grid__label"><i class="fa fa-file-text-o"></i> No. Sertifikat</div>
                  <div class="labs-info-grid__value" id="detail_no_certificate">-</div>
               </div>
               <div class="labs-info-grid__item" style="grid-column: span 2;">
                  <div class="labs-info-grid__label"><i class="fa fa-external-link"></i> Evaluation URL</div>
                  <div class="labs-info-grid__value" id="detail_evaluation_url">-</div>
               </div>
               <div class="labs-info-grid__item">
                  <div class="labs-info-grid__label"><i class="fa fa-calendar-plus-o"></i> Dibuat</div>
                  <div class="labs-info-grid__value" id="detail_created_at">-</div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <a id="detail_btn_edit" href="#" class="labs-btn labs-btn--warning"><i class="fa fa-edit"></i> Edit</a>
            <button class="labs-btn labs-btn--default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
         </div>
      </div>
   </div>
</div>

<!-- MODAL DETAIL PRESENSI -->
<div class="modal fade labs-modal" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header" style="background:linear-gradient(135deg, var(--labs-info), #155E75)">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-users"></i> Detail Peserta</h4>
         </div>
         <div class="modal-body">
            <!-- Info Acara -->
            <div class="labs-modal-section">
               <div class="labs-modal-section__title"><i class="fa fa-info-circle"></i> Informasi Acara</div>
               <div class="labs-info-grid" style="grid-template-columns: repeat(2, 1fr);">
                  <div class="labs-info-grid__item">
                     <div class="labs-info-grid__label">Nama Acara</div>
                     <div class="labs-info-grid__value" id="info_nama">-</div>
                  </div>
                  <div class="labs-info-grid__item">
                     <div class="labs-info-grid__label">Waktu</div>
                     <div class="labs-info-grid__value" id="info_waktu">-</div>
                  </div>
                  <div class="labs-info-grid__item">
                     <div class="labs-info-grid__label">Lokasi</div>
                     <div class="labs-info-grid__value" id="info_lokasi">-</div>
                  </div>
               </div>
            </div>
            <div id="detail_peserta" class="labs-mt-4">
               <table id="detail_table" class="labs-table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                  <tr>
                     <th>No</th>
                     <th>Acara</th>
                     <th>NPP</th>
                     <th>Peserta</th>
                     <th>Role</th>
                     <th>Waktu Presensi</th>
                     <th class='notexport'>Action</th>
                  </tr>
                  </thead>
                  <tbody></tbody>
               </table>
            </div>
         </div>
         <div class="modal-footer">
            <button class="labs-btn labs-btn--default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
         </div>
      </div>
   </div>
</div>

<!-- Modal Sample Sertifikat -->
<div class="modal fade labs-modal" id="modal_sample_sertifikat" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-lg" style="width:90%;max-width:1100px;">
      <div class="modal-content">
         <div class="modal-header" style="background:linear-gradient(135deg,#C2410C,#EA580C);color:#fff;">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8;">&times;</button>
            <h4 class="modal-title"><i class="fa fa-file-pdf-o"></i> Sample Sertifikat</h4>
         </div>
         <div class="modal-body" style="padding:0;background:#f5f5f5;">
            <iframe id="iframe_sample_sertifikat" src="" style="width:100%;height:600px;border:none;"></iframe>
            <div id="loading_sertifikat" style="padding:40px;text-align:center;color:#999;">
               <i class="fa fa-spinner fa-spin fa-2x"></i><br>Memuat sample sertifikat...
            </div>
         </div>
         <div class="modal-footer">
            <button class="labs-btn labs-btn--default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
         </div>
      </div>
   </div>
</div>

<!-- Page script -->
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.bootstrap.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.bootstrap.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.html5.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.print.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.colVis.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script>
$(document).ready(function(){

   // Copy Link
   $(document).on('click', '.btn-copy-link', function(){
      var link = $(this).attr('data-link');
      var btn = $(this);
      if (navigator.clipboard) {
         navigator.clipboard.writeText(link).then(function(){
            btn.html('<i class="fa fa-check"></i> Copied!');
            btn.removeClass('labs-btn--default').addClass('labs-btn--success');
            setTimeout(function(){
               btn.html('<i class="fa fa-copy"></i> Copy');
               btn.removeClass('labs-btn--success').addClass('labs-btn--default');
            }, 2000);
         });
      } else {
         var temp = $('<input>');
         $('body').append(temp);
         temp.val(link).select();
         document.execCommand('copy');
         temp.remove();
         btn.html('<i class="fa fa-check"></i> Copied!');
         btn.removeClass('labs-btn--default').addClass('labs-btn--success');
         setTimeout(function(){
            btn.html('<i class="fa fa-copy"></i> Copy');
            btn.removeClass('labs-btn--success').addClass('labs-btn--default');
         }, 2000);
      }
   });

   // Detail Acara Modal
   $('.btn_detail_acara').click(function(){
      var d = $(this).data();
      $('#detail_nama').text(d.nama || '-');
      $('#detail_peserta_acara').html((d.peserta || '-').replace(/,/g, '<br>'));
      
      if (d.waktu_mulai) {
         var tgl_mulai = formatDateID(d.waktu_mulai);
         var jam_mulai = formatTimeID(d.waktu_mulai);
         $('#detail_waktu_mulai').html(tgl_mulai + '<br><small class="labs-text-muted">' + jam_mulai + '</small>');
      } else {
         $('#detail_waktu_mulai').text('-');
      }
      if (d.waktu_selesai) {
         var tgl_selesai = formatDateID(d.waktu_selesai);
         var jam_selesai = formatTimeID(d.waktu_selesai);
         $('#detail_waktu_selesai').html(tgl_selesai + '<br><small class="labs-text-muted">' + jam_selesai + '</small>');
      } else {
         $('#detail_waktu_selesai').text('-');
      }
      
      $('#detail_lokasi').text(d.lokasi || '-');
      $('#detail_narasumber').text(d.narasumber || '-');
      $('#detail_keterangan').text(d.keterangan || '-');
      $('#detail_sertifikat').html(d.is_certificated == '1' ? '<span class="labs-badge labs-badge--success labs-badge--solid"><i class="fa fa-check"></i> Ya</span>' : '<span class="labs-badge labs-badge--default"><i class="fa fa-times"></i> Tidak</span>');
      $('#detail_no_certificate').text(d.no_certificate || '-');
      if (d.evaluation_url) {
         $('#detail_evaluation_url').html('<a href="' + d.evaluation_url + '" target="_blank" class="labs-text-primary"><i class="fa fa-external-link"></i> ' + d.evaluation_url + '</a>');
      } else {
         $('#detail_evaluation_url').text('-');
      }
      if (d.created_at) {
         $('#detail_created_at').text(formatDateID(d.created_at) + ' ' + formatTimeID(d.created_at));
      } else {
         $('#detail_created_at').text('-');
      }
      $('#detail_btn_edit').attr('href', BASE_URL + '/administrator/acara/edit/' + d.id);
   });

   // Sample Sertifikat
   $('.btn_sample_sertifikat').click(function(){
      var id_acara = $(this).data('id');
      var iframe = $('#iframe_sample_sertifikat');
      var loading = $('#loading_sertifikat');
      iframe.attr('src', '').hide();
      loading.show();
      $('#modal_sample_sertifikat').modal('show');
      // Delay agar modal tampil dulu
      setTimeout(function(){
         iframe.attr('src', BASE_URL + 'administrator/acara/sample_sertifikat/' + id_acara);
         iframe.on('load', function(){
            loading.hide();
            iframe.show();
         });
      }, 300);
   });

   // Helper: format tanggal Indonesia
   function formatDateID(dt) {
      if (!dt) return '-';
      var parts = dt.split(/[- :]/);
      if (parts.length < 3) return dt;
      var bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
      var d = parseInt(parts[2], 10);
      var m = parseInt(parts[1], 10) - 1;
      var y = parts[0];
      return d + ' ' + bulan[m] + ' ' + y;
   }
   function formatTimeID(dt) {
      if (!dt) return '-';
      var parts = dt.split(/[- :]/);
      if (parts.length < 4) return '';
      return parts[3] + ':' + (parts[4] || '00');
   }

   // Detail Peserta Modal
   $(".btn_detail").click(function(e) {
      let id_acara = $(this).attr('data-id');
      let nama_acara = $(this).attr('data-nama') || '';
      let waktu = $(this).attr('data-waktu') || '-';
      let tanggal = $(this).attr('data-tanggal') || '';
      let lokasi = $(this).attr('data-lokasi') || '-';
      e.preventDefault();

      $('#info_nama').text(nama_acara || '-');
      $('#info_waktu').text(waktu + (tanggal ? ' (' + tanggal + ')' : ''));
      $('#info_lokasi').text(lokasi);

      let safe_nama = (nama_acara || 'acara').replace(/[^a-zA-Z0-9_\-]/g, '_').replace(/_{2,}/g, '_');
      let now = new Date();
      let pad = function(n){ return String(n).padStart(2,'0'); };
      let timestamp = now.getFullYear() + pad(now.getMonth()+1) + pad(now.getDate()) + '_' + pad(now.getHours()) + pad(now.getMinutes()) + pad(now.getSeconds());
      let filename = 'Peserta_Acara_' + safe_nama + '_' + timestamp;

      $('#detail_table').DataTable().destroy();
      $('#detail_table').DataTable({
         dom: 'Bfrtip',
         buttons: [{
            extend: 'excel',
            text: '<i class="fa fa-file-excel-o"></i> Export Excel',
            className: 'labs-btn labs-btn--success labs-btn--sm',
            title: 'Peserta Acara - ' + (nama_acara || ''),
            filename: filename,
            exportOptions: { columns: ':not(.notexport)' }
         }],
         ajax: {
            url: "<?= site_url('administrator/acara_presensi/detail_peserta'); ?>",
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: { 'id_acara': id_acara },
         },
         columns: [
            { data: 'no', title: 'NO', name: 'no' },
            { data: 'acara', title: 'NAMA ACARA', name: 'acara' },
            { data: 'npp', title: 'NPP', name: 'npp' },
            { data: 'peserta', title: 'PESERTA', name: 'peserta' },
            { data: 'role', title: 'ROLE', name: 'role' },
            { 
               data: 'waktu_presensi', title: 'PRESENSI', name: 'waktu_presensi',
               render: function(data, type, row) {
                  if (!data) return '-';
                  var parts = data.split(/[- :]/);
                  if (parts.length < 4) return data;
                  var bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
                  var d = parseInt(parts[2], 10);
                  var m = parseInt(parts[1], 10) - 1;
                  var y = parts[0];
                  var jam = parts[3] + ':' + (parts[4] || '00');
                  return '<div style="white-space:normal;line-height:1.4"><span class="labs-fw-semi">' + d + ' ' + bulan[m] + ' ' + y + '</span><br><small class="labs-text-muted">' + jam + '</small></div>';
               }
            },
            { data: 'action', title: 'ACTION', name: 'action', exportable: false, orderable: false, searchable: false },
         ],
         columnDefs: [
            { targets: [0,2], className: "labs-text-center" },
            { targets: [3], className: "labs-text-right" },
            { targets: '_all', className: 'dt-center' },
            { targets: [6], className: "labs-text-center"},
            { orderable: false, targets: -1}
         ],
         scrollX: true
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
      var serialize_bulk = $('#form_acara').serialize();

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
               document.location.href = BASE_URL + '/administrator/acara/delete?' + serialize_bulk;      
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

});
</script>
