<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<script type="text/javascript">
</script>

<!-- Content Header -->
<section class="content-header">
   <h1>
      <i class="fa fa-users"></i> <?= cclang('acara_presensi') ?> <small class="labs-text-muted"><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/acara'); ?>">Acara</a></li>
      <li class="active"><?= cclang('acara_presensi') ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">

            <!-- Card Header -->
            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-users"></i> Data Presensi Acara
                  <span class="labs-badge labs-badge--orange"><?= $acara_presensi_counts; ?> Data</span>
               </h3>
               <div class="labs-flex labs-flex-gap-2">
                  <?php is_allowed('acara_presensi_add', function(){?>
                  <a class="labs-btn labs-btn--success" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('acara_presensi')]); ?> (Ctrl+a)" href="<?= site_url('administrator/acara_presensi/add'); ?>">
                     <i class="fa fa-plus"></i> Tambah
                  </a>
                  <?php }) ?>
                  <?php is_allowed('acara_presensi_export', function(){?>
                  <a class="labs-btn labs-btn--default" title="<?= cclang('export'); ?> <?= cclang('acara_presensi') ?>" href="<?= site_url('administrator/acara_presensi/export'); ?>">
                     <i class="fa fa-file-excel-o"></i> Export XLS
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="labs-card__body">

               <form name="form_acara_presensi" id="form_acara_presensi" action="<?= base_url('administrator/acara_presensi/index'); ?>">

               <!-- Filter Bar -->
               <div class="labs-filter-inline">
                  <div class="labs-filter-inline__search">
                     <i class="fa fa-search labs-filter-inline__search-icon"></i>
                     <input type="text" class="form-control" name="q" id="filter" placeholder="Cari presensi..." value="<?= $this->input->get('q'); ?>">
                  </div>
                  <input type="hidden" name="id_acara" value="<?= $this->input->get('id_acara'); ?>">
                  <select class="form-control chosen chosen-select" name="f" id="field">
                     <option value=""><?= cclang('all'); ?></option>
                  </select>
                  <button type="submit" class="labs-btn labs-btn--primary"><i class="fa fa-search"></i> Filter</button>
                  <?php if(!empty($this->input->get('q')) || !empty($this->input->get('id_acara'))): ?>
                  <a class="labs-btn labs-btn--default" href="<?= base_url('administrator/acara_presensi'); ?>"><i class="fa fa-undo"></i> Reset</a>
                  <?php endif; ?>
               </div>

               <!-- Data Table -->
               <div class="labs-table-wrap">
                  <table class="labs-table">
                     <thead>
                        <tr>
                           <th width="30">
                              <input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all">
                           </th>
                           <th>Acara</th>
                           <th>NPP</th>
                           <th>Peserta</th>
                           <th>Role</th>
                           <th>Waktu Presensi</th>
                           <th>Waktu Selesai</th>
                           <th width="240">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_acara_presensi">
                     <?php if ($acara_presensi_counts > 0): ?>
                     <?php foreach($acara_presensis as $acara_presensi): ?>
                        <tr>
                           <td class="labs-text-center">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $acara_presensi->id_presensi; ?>">
                           </td>
                           <td>
                              <?php if ($acara_presensi->id_acara): ?>
                              <a href="<?= site_url('administrator/acara/view/'.$acara_presensi->id_acara.'?popup=show'); ?>" class="popup-view labs-text-primary labs-fw-semi">
                                 <i class="fa fa-calendar labs-text-primary"></i> <?= $acara_presensi->acara_nama_acara; ?>
                              </a>
                              <?php else: ?>
                              <span class="labs-text-light">-</span>
                              <?php endif; ?>
                           </td>
                           <td class="labs-cell-numeric"><?= _ent($acara_presensi->npp); ?></td>
                           <td>
                              <div class="labs-name-cell">
                                 <span class="labs-avatar labs-avatar--sm"><?= strtoupper(substr($acara_presensi->peserta, 0, 2)); ?></span>
                                 <span><?= _ent($acara_presensi->peserta); ?></span>
                              </div>
                           </td>
                           <td class="labs-text-center">
                              <?php if (!empty($acara_presensi->role_lainnya)): ?>
                                 <span class="labs-badge labs-badge--info"><?= $acara_presensi->role; ?></span>
                                 <small class="labs-text-muted"> - <?= $acara_presensi->role_lainnya; ?></small>
                              <?php else: ?>
                                 <span class="labs-badge labs-badge--info"><?= $acara_presensi->role; ?></span>
                              <?php endif; ?>
                           </td>
                           <td class="labs-text-center">
                              <?php if (!empty($acara_presensi->waktu_presensi)): ?>
                                 <div class="labs-fw-semi"><?= formatTanggal($acara_presensi->waktu_presensi); ?></div>
                                 <small class="labs-text-muted"><?= date('H:i', strtotime($acara_presensi->waktu_presensi)); ?> WIB</small>
                              <?php else: ?>
                                 <span class="labs-text-light">-</span>
                              <?php endif; ?>
                           </td>
                           <td class="labs-text-center">
                              <?php if (!empty($acara_presensi->waktu_presensi_selesai)): ?>
                                 <div class="labs-fw-semi"><?= formatTanggal($acara_presensi->waktu_presensi_selesai); ?></div>
                                 <small class="labs-text-muted"><?= date('H:i', strtotime($acara_presensi->waktu_presensi_selesai)); ?> WIB</small>
                              <?php else: ?>
                                 <span class="labs-text-light">-</span>
                              <?php endif; ?>
                           </td>
                           <td style="white-space:nowrap">
                              <div class="labs-row-actions" style="justify-content:center">
                                 <?php is_allowed('acara_presensi_update', function() use ($acara_presensi){?>
                                 <a href="<?= site_url('administrator/acara_presensi/edit/' . $acara_presensi->id_presensi); ?>" class="labs-btn labs-btn--warning labs-btn--sm"><i class="fa fa-edit"></i> Edit</a>
                                 <?php }) ?>
                                 <?php is_allowed('acara_presensi_delete', function() use ($acara_presensi){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/acara_presensi/delete/' . $acara_presensi->id_presensi); ?>" class="labs-btn labs-btn--danger labs-btn--sm remove-data"><i class="fa fa-trash"></i> Hapus</a>
                                 <?php }) ?>
                                 <a target="_blank" href="<?= site_url('apiapp/acara/lihat_sertifikat?id_acara='.$acara_presensi->id_acara.'&npp='.$acara_presensi->npp.'&role='.$acara_presensi->role); ?>" class="labs-btn labs-btn--info labs-btn--sm"><i class="fa fa-file-pdf-o"></i> Sertifikat</a>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="8">
                              <div class="labs-empty">
                                 <i class="fa fa-inbox"></i>
                                 <p>Data presensi acara tidak tersedia</p>
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

<!-- Page script -->
<script>
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
      var serialize_bulk = $('#form_acara_presensi').serialize();

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
               document.location.href = BASE_URL + '/administrator/acara_presensi/delete?' + serialize_bulk;      
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
