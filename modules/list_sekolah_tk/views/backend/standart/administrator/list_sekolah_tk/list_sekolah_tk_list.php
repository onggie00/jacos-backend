<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">

<style>
/* Override DataTables agar seragam dengan labs-table */
.labs-table.dataTable thead th { background: linear-gradient(180deg, #FFFBF5, #F5F0EA); border-bottom: 2px solid var(--labs-border-strong); }
.labs-table.dataTable tbody td { border-bottom: 1px solid var(--labs-border); }
.labs-table.dataTable tbody tr:hover { background: var(--labs-bg-soft); }
.labs-table-wrap .dataTables_wrapper > .dataTables_filter { display: none; }
</style>

<script>
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
   <h1><i class="fa fa-university"></i> <?= cclang('list_sekolah_tk') ?> <small class="labs-text-muted"><?= cclang('list_all'); ?></small></h1>
   <ol class="breadcrumb"><li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li><li class="active"><?= cclang('list_sekolah_tk') ?></li></ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">

      <!-- Stat Cards -->
      <div class="col-md-12">
         <div class="row">
            <div class="col-md-4">
               <div class="labs-stat-card">
                  <div class="labs-stat-card__icon labs-stat-card__icon--primary"><i class="fa fa-university"></i></div>
                  <div class="labs-stat-card__body">
                     <div class="labs-stat-card__label">Total Sekolah</div>
                     <div class="labs-stat-card__value" data-counter="<?= (int)$list_sekolah_tk_counts; ?>">0</div>
                  </div>
               </div>
            </div>
            <div class="col-md-4">
               <div class="labs-stat-card">
                  <div class="labs-stat-card__icon labs-stat-card__icon--success"><i class="fa fa-hashtag"></i></div>
                  <div class="labs-stat-card__body">
                     <div class="labs-stat-card__label">NPSN Terdaftar</div>
                     <div class="labs-stat-card__value" data-counter="<?= (int)$list_sekolah_tk_counts; ?>">0</div>
                  </div>
               </div>
            </div>
            <div class="col-md-4">
               <div class="labs-stat-card">
                  <div class="labs-stat-card__icon labs-stat-card__icon--info"><i class="fa fa-map-marker"></i></div>
                  <div class="labs-stat-card__body">
                     <div class="labs-stat-card__label">Sumber Data</div>
                     <div class="labs-stat-card__value labs-fs-md">Import Excel</div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="col-md-12">
         <div class="labs-card">

            <!-- Card Header -->
            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-list"></i> Daftar Sekolah TK
                  <span class="labs-badge labs-badge--orange"><?= $list_sekolah_tk_counts; ?> Data</span>
               </h3>
               <div>
                  <a class="labs-btn labs-btn--info" data-toggle="modal" data-target="#modal_import" title="Import Data Sekolah">
                     <i class="fa fa-upload"></i> Import Sekolah
                  </a>
                  <?php is_allowed('list_sekolah_tk_delete', function(){?>
                  <a class="labs-btn labs-btn--danger btn_delete_all" id="btn_delete_all" title="Hapus semua data untuk re-import" href="javascript:void(0);">
                     <i class="fa fa-trash"></i> Delete All
                  </a>
                  <?php }) ?>
                  <?php is_allowed('list_sekolah_tk_add', function(){?>
                  <a class="labs-btn labs-btn--success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('list_sekolah_tk')]); ?> (Ctrl+a)" href="<?= site_url('administrator/list_sekolah_tk/add'); ?>">
                     <i class="fa fa-plus"></i> <?= cclang('add_new_button', [cclang('list_sekolah_tk')]); ?>
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="labs-card__body">

               <!-- Filter Bar -->
               <form name="form_list_sekolah_tk" id="form_list_sekolah_tk" action="<?= base_url('administrator/list_sekolah_tk/index'); ?>">
               <div class="labs-filter-inline">
                  <div class="labs-filter-inline__search">
                     <i class="fa fa-search labs-filter-inline__search-icon"></i>
                     <input type="text" class="form-control" name="q" id="filter" placeholder="Cari nama sekolah / NPSN / lokasi..." value="<?= $this->input->get('q'); ?>">
                  </div>
                  <select class="form-control" name="f" id="field" style="width:200px;height:34px;font-size:12px">
                     <option value="">Semua Field</option>
                     <option <?= $this->input->get('f') == 'nama_sekolah' ? 'selected' :''; ?> value="nama_sekolah">Nama Sekolah</option>
                     <option <?= $this->input->get('f') == 'no_sekolah' ? 'selected' :''; ?> value="no_sekolah">NPSN</option>
                     <option <?= $this->input->get('f') == 'lokasi' ? 'selected' :''; ?> value="lokasi">Lokasi</option>
                  </select>
                  <button type="submit" class="labs-btn labs-btn--primary"><i class="fa fa-search"></i> Cari</button>
                  <?php if(!empty($this->input->get('q')) || !empty($this->input->get('f'))): ?>
                  <a class="labs-btn labs-btn--default" href="<?= base_url('administrator/list_sekolah_tk'); ?>"><i class="fa fa-undo"></i> Reset</a>
                  <?php endif; ?>
               </div>

               <!-- Data Table -->
               <div class="labs-table-wrap">
                  <table class="labs-table dataTable" id="list_sekolah_tk_table">
                     <thead>
                        <tr>
                           <th width="30"><input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all"></th>
                           <th>Nama Sekolah</th>
                           <th>NPSN</th>
                           <th>Lokasi</th>
                           <th>Status</th>
                           <th width="220" class="labs-text-center">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_list_sekolah_tk">
                     <?php if($list_sekolah_tk_counts > 0): ?>
                     <?php foreach($list_sekolah_tks as $list_sekolah_tk): ?>
                        <tr>
                           <td class="labs-text-center">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $list_sekolah_tk->id_list_sekolah_tk; ?>">
                           </td>
                           <td>
                              <div class="labs-name-cell">
                                 <span class="labs-avatar labs-avatar--sm" data-name="<?= _ent($list_sekolah_tk->nama_sekolah); ?>"></span>
                                 <span class="labs-fw-semi"><?= _ent($list_sekolah_tk->nama_sekolah); ?></span>
                              </div>
                           </td>
                           <td><span class="labs-text-muted"><?= _ent($list_sekolah_tk->no_sekolah); ?></span></td>
                           <td>
                              <?php if(!empty($list_sekolah_tk->lokasi)): ?>
                                 <i class="fa fa-map-marker labs-text-primary"></i> <?= _ent($list_sekolah_tk->lokasi); ?>
                              <?php else: ?>
                                 <span class="labs-text-light">-</span>
                              <?php endif; ?>
                           </td>
                           <td>
                              <?php if($list_sekolah_tk->is_show == 1): ?>
                                 <span class="labs-badge labs-badge--success labs-badge--solid"><i class="fa fa-check"></i> Aktif</span>
                              <?php else: ?>
                                 <span class="labs-badge labs-badge--default"><i class="fa fa-eye-slash"></i> Hidden</span>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center;white-space:nowrap">
                              <div class="labs-row-actions" style="justify-content:center">
                                 <?php is_allowed('list_sekolah_tk_view', function() use ($list_sekolah_tk){?>
                                 <a href="<?= site_url('administrator/list_sekolah_tk/view/' . $list_sekolah_tk->id_list_sekolah_tk); ?>" class="labs-btn labs-btn--info labs-btn--sm" title="<?= cclang('view_button'); ?>">
                                    <i class="fa fa-eye"></i>
                                 </a>
                                 <?php }) ?>
                                 <?php is_allowed('list_sekolah_tk_update', function() use ($list_sekolah_tk){?>
                                 <a href="<?= site_url('administrator/list_sekolah_tk/edit/' . $list_sekolah_tk->id_list_sekolah_tk); ?>" class="labs-btn labs-btn--warning labs-btn--sm" title="<?= cclang('update_button'); ?>">
                                    <i class="fa fa-edit"></i>
                                 </a>
                                 <?php }) ?>
                                 <?php is_allowed('list_sekolah_tk_delete', function() use ($list_sekolah_tk){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/list_sekolah_tk/delete/' . $list_sekolah_tk->id_list_sekolah_tk); ?>" class="labs-btn labs-btn--danger labs-btn--sm remove-data" title="<?= cclang('remove_button'); ?>">
                                    <i class="fa fa-trash"></i>
                                 </a>
                                 <?php }) ?>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="6">
                              <div class="labs-empty">
                                 <i class="fa fa-inbox"></i>
                                 <?php if(!empty($this->input->get('q'))): ?>
                                    <p>Data sekolah tidak ditemukan untuk pencarian "<strong><?= htmlspecialchars($this->input->get('q')); ?></strong>"</p>
                                 <?php else: ?>
                                    <p>Daftar Sekolah TK belum tersedia. Silakan import data terlebih dahulu.</p>
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
                     <button type="button" class="labs-btn labs-btn--default" id="apply" title="<?= cclang('apply_bulk_action'); ?>">
                        <i class="fa fa-check"></i> Terapkan
                     </button>
                     <span class="labs-text-muted labs-fs-sm">Total: <strong><?= $list_sekolah_tk_counts; ?></strong> data</span>
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

<!-- ============ MODAL IMPORT DATA SEKOLAH =============== -->
<div class="modal fade labs-modal" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><i class="fa fa-upload"></i> Import Data Sekolah TK</h4>
         </div>
         <form action="<?= base_url('administrator/list_sekolah_tk/import/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">File</label>
                  <div class="col-xs-9">
                     <input name="file_upload" class="form-control" type="file" accept=".xls,.xlsx" required>
                  </div>
               </div>
               <div class="labs-text-muted labs-fs-sm" style="margin-top:8px;padding-left:15px">
                  <i class="fa fa-info-circle labs-text-info"></i> File yang akan diimport akan sesuai format file hasil export Excel
               </div>
            </div>
            <div class="modal-footer">
               <button class="labs-btn labs-btn--default" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> Tutup</button>
               <button type="submit" class="labs-btn labs-btn--info"><i class="fa fa-upload"></i> Import</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL IMPORT DATA SEKOLAH -->

<!-- labs-ui JS (counter untuk stat card) -->
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<!-- Page script -->
<script>
$(document).ready(function(){

   // Delete All (double confirm)
   $('.btn_delete_all').click(function(){
      swal({
          title: "<?= cclang('are_you_sure'); ?>",
          text: "Tindakan ini akan MENGHAPUS SEMUA data list_sekolah_tk. Tindakan ini tidak dapat dibatalkan.",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Lanjut",
          cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
          closeOnConfirm: false,
          closeOnCancel: true
        },
        function(isConfirm){
          if (isConfirm) {
            swal({
              title: "Konfirmasi terakhir",
              text: "Ketik 'HAPUS' untuk konfirmasi hapus semua data:",
              type: "input",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Hapus Semua",
              cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
              closeOnConfirm: false,
              closeOnCancel: true
            },
            function(inputValue){
              if (inputValue === "HAPUS") {
                document.location.href = BASE_URL + '/administrator/list_sekolah_tk/delete_all?confirm=yes';
              } else if (inputValue === false) {
                return false;
              } else {
                swal.showInputError("Ketik 'HAPUS' (tanpa kutip) untuk konfirmasi");
                return false;
              }
            });
          }
        });
      return false;
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
        },
        function(isConfirm){
          if (isConfirm) {
            document.location.href = url;
          }
        });
      return false;
   });

   // Bulk action
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_list_sekolah_tk').serialize();
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
               document.location.href = BASE_URL + '/administrator/list_sekolah_tk/delete?' + serialize_bulk;
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
