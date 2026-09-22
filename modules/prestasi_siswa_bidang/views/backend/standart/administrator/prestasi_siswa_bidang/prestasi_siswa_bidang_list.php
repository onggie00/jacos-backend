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

/* Top Buttons */
.btn-top { margin-right: 5px; border-radius: 3px; }

/* Table Styling */
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; }
.table td { font-size: 13px; vertical-align: middle; }
</style>

<script type="text/javascript">
</script>

<!-- Content Header -->
<section class="content-header">
   <h1>
      <?= cclang('prestasi_siswa_bidang') ?> <small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('prestasi_siswa_bidang') ?></li>
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
                  <i class="fa fa-trophy"></i> Data Bidang Prestasi Siswa
                  <span class="label bg-yellow" style="margin-left:10px"><?= $prestasi_siswa_bidang_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('prestasi_siswa_bidang_add', function(){?>
                  <a class="btn btn-sm btn-success btn-top" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('prestasi_siswa_bidang')]); ?> (Ctrl+a)" href="<?= site_url('administrator/prestasi_siswa_bidang/add'); ?>">
                     <i class="fa fa-plus"></i> <?= cclang('add_new_button', [cclang('prestasi_siswa_bidang')]); ?>
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">

               <!-- Search Form -->
               <form name="form_prestasi_siswa_bidang" id="form_prestasi_siswa_bidang" action="<?= base_url('administrator/prestasi_siswa_bidang/index'); ?>">
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-5">
                     <div class="input-group">
                        <input type="text" class="form-control" name="q" id="filter" placeholder="Cari data bidang prestasi siswa..." value="<?= $this->input->get('q'); ?>">
                        <span class="input-group-btn">
                           <button type="submit" class="btn btn-flat btn-primary" id="sbtn"><i class="fa fa-search"></i> Cari</button>
                           <?php if(!empty($this->input->get('q'))): ?>
                           <a class="btn btn-flat btn-default" id="reset" href="<?= base_url('administrator/prestasi_siswa_bidang'); ?>"><i class="fa fa-undo"></i></a>
                           <?php endif; ?>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control chosen chosen-select" name="f" id="field">
                        <option value="">Semua Kolom</option>
                        <option <?= $this->input->get('f') == 'nama_bidang' ? 'selected' :''; ?> value="nama_bidang">Nama Bidang</option>
                        <option <?= $this->input->get('f') == 'created_at' ? 'selected' :''; ?> value="created_at">Created At</option>
                     </select>
                  </div>
                  <div class="col-md-4 text-right">
                     <?php if(!empty($this->input->get('q'))): ?>
                     <span class="text-muted" style="line-height:34px">
                        Hasil pencarian: <strong>"<?= $this->input->get('q'); ?>"</strong>
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
                           <th style="text-align:center"><?= cclang('nama_bidang') ?></th>
                           <th style="text-align:center"><?= cclang('created_at') ?></th>
                           <th style="text-align:center" width="280">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_prestasi_siswa_bidang">
                     <?php if($prestasi_siswa_bidang_counts > 0): ?>
                     <?php foreach($prestasi_siswa_bidangs as $prestasi_siswa_bidang): ?>
                        <tr>
                           <td>
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $prestasi_siswa_bidang->id_prestasi_bidang; ?>">
                           </td>
                           <td><?= _ent($prestasi_siswa_bidang->nama_bidang); ?></td>
                           <td style="text-align:center"><?= _ent($prestasi_siswa_bidang->created_at); ?></td>
                           <td style="white-space:nowrap;text-align:center">
                              <?php is_allowed('prestasi_siswa_bidang_view', function() use ($prestasi_siswa_bidang){?>
                              <a href="<?= site_url('administrator/prestasi_siswa_bidang/view/' . $prestasi_siswa_bidang->id_prestasi_bidang); ?>" class="btn btn-action btn-action-view btn-sm">
                                 <i class="fa fa-newspaper-o"></i> Detail
                              </a>
                              <?php }) ?>
                              <?php is_allowed('prestasi_siswa_bidang_update', function() use ($prestasi_siswa_bidang){?>
                              <a href="<?= site_url('administrator/prestasi_siswa_bidang/edit/' . $prestasi_siswa_bidang->id_prestasi_bidang); ?>" class="btn btn-action btn-action-edit btn-sm">
                                 <i class="fa fa-edit"></i> Edit
                              </a>
                              <?php }) ?>
                              <?php is_allowed('prestasi_siswa_bidang_delete', function() use ($prestasi_siswa_bidang){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/prestasi_siswa_bidang/delete/' . $prestasi_siswa_bidang->id_prestasi_bidang); ?>" class="btn btn-action btn-action-delete btn-sm remove-data">
                                 <i class="fa fa-trash"></i> Hapus
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="4" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">Data bidang prestasi siswa tidak tersedia</span>
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
      var serialize_bulk = $('#form_prestasi_siswa_bidang').serialize();

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
               document.location.href = BASE_URL + '/administrator/prestasi_siswa_bidang/delete?' + serialize_bulk;      
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
