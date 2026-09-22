<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap.min.css">
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
.btn-action-final { border-color: #27ae60; color: #27ae60 !important; }
.btn-action-final:hover { background: #27ae60; color: #fff !important; }
.btn-action-draft { border-color: #f39c12; color: #f39c12 !important; }
.btn-action-draft:hover { background: #f39c12; color: #fff !important; }

/* Top Buttons */
.btn-top { margin-right: 5px; border-radius: 3px; }

/* Table Styling */
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; }
.table td { font-size: 13px; vertical-align: middle; }

/* Status badges */
.badge-draft { background: #f39c12; }
.badge-final { background: #27ae60; }

/* Guru load card */
.guru-load-card {
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    padding: 8px 12px;
    margin-bottom: 5px;
    font-size: 12px;
    background: #fff;
}
.guru-load-card .nama { font-weight: 600; }
.guru-load-card .quota { color: #666; }
.guru-load-card .over { color: #e74c3c; font-weight: bold; }
.guru-load-card .ok { color: #27ae60; }
</style>

<script type="text/javascript">
</script>

<!-- Content Header -->
<section class="content-header">
   <h1>
      <?= cclang('mapel_alokasi_smp') ?> <small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('mapel_alokasi_smp') ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <!-- Guru Load Summary -->
      <div class="col-md-12">
         <div class="box box-info collapsed-box">
            <div class="box-header with-border" data-widget="collapse" style="cursor:pointer">
               <h3 class="box-title">
                  <i class="fa fa-users"></i> Ringkasan Kuota Guru
                  <span class="label bg-blue" style="margin-left:10px"><?= count($guru_loads); ?> Guru</span>
               </h3>
               <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool"><i class="fa fa-plus"></i></button>
               </div>
            </div>
            <div class="box-body" style="display:none">
               <div class="row">
                  <?php foreach($guru_loads as $gl): ?>
                  <div class="col-md-3">
                     <div class="guru-load-card">
                        <div class="nama"><?= _ent($gl->nama_lengkap); ?></div>
                        <div class="quota">
                           <?= $gl->kode_mapel; ?> | 
                           Kuota: <?= $gl->jam_ajar; ?> jam | 
                           Terpakai: <?= $gl->total_jam_target; ?> jam | 
                           <span class="<?= ($gl->sisa_kuota < 0) ? 'over' : 'ok'; ?>">
                              Sisa: <?= $gl->sisa_kuota; ?> jam
                           </span>
                        </div>
                     </div>
                  </div>
                  <?php endforeach; ?>
               </div>
            </div>
         </div>
      </div>

      <!-- Main Table -->
      <div class="col-md-12">
         <div class="box box-warning">
            <!-- Box Header -->
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-calendar"></i> Data Alokasi Mapel
                  <span class="label bg-yellow" style="margin-left:10px"><?= $alokasi_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('mapel_alokasi_smp_update', function(){?>
                  <a class="btn btn-sm btn-success btn-top" title="Quick Generate" href="<?= site_url('administrator/mapel_alokasi_smp/quick_generate_preview'); ?>">
                     <i class="fa fa-magic"></i> Quick Generate
                  </a>
                  <?php }) ?>
                  <?php is_allowed('mapel_alokasi_smp_add', function(){?>
                  <a class="btn btn-sm btn-info btn-top" title="Import dari Excel" href="<?= site_url('administrator/mapel_alokasi_smp/import_excel'); ?>">
                     <i class="fa fa-file-excel-o"></i> Import Excel
                  </a>
                  <a class="btn btn-sm btn-warning btn-top" id="btn_add_new" title="Generate Draft (Ctrl+a)" href="<?= site_url('administrator/mapel_alokasi_smp/generate_draft'); ?>">
                     <i class="fa fa-plus"></i> Generate Draft
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">
               <!-- Search Form -->
               <form name="form_alokasi" id="form_alokasi" action="<?= base_url('administrator/mapel_alokasi_smp/index'); ?>">
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-4">
                     <div class="input-group">
                        <input type="text" class="form-control" name="q" id="filter" placeholder="Cari data alokasi..." value="<?= $this->input->get('q'); ?>">
                        <span class="input-group-btn">
                           <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Cari</button>
                           <?php if(!empty($this->input->get('q'))): ?>
                           <a class="btn btn-flat btn-default" href="<?= base_url('administrator/mapel_alokasi_smp'); ?>"><i class="fa fa-undo"></i></a>
                           <?php endif; ?>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-2">
                     <select class="form-control chosen chosen-select" name="f" id="field">
                        <option value="">Semua Kolom</option>
                        <option <?= $this->input->get('f') == 'kode_mapel' ? 'selected' : ''; ?> value="kode_mapel">Kode Mapel</option>
                        <option <?= $this->input->get('f') == 'nama_mapel' ? 'selected' : ''; ?> value="nama_mapel">Nama Mapel</option>
                        <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' : ''; ?> value="nama_lengkap">Guru</option>
                        <option <?= $this->input->get('f') == 'label' ? 'selected' : ''; ?> value="label">Kelas</option>
                     </select>
                  </div>
                  <div class="col-md-2">
                     <select class="form-control" name="status" id="status_filter">
                        <option value="">Semua Status</option>
                        <option <?= $this->input->get('status') == 'draft' ? 'selected' : ''; ?> value="draft">Draft</option>
                        <option <?= $this->input->get('status') == 'final' ? 'selected' : ''; ?> value="final">Final</option>
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
                           <th style="text-align:center">Kelas</th>
                           <th style="text-align:center">Mapel</th>
                           <th style="text-align:center">Guru</th>
                           <th style="text-align:center">Jam Target</th>
                           <th style="text-align:center">Jam Terpenuhi</th>
                           <th style="text-align:center">Status</th>
                           <th style="text-align:center" width="250">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_alokasi">
                     <?php if($alokasi_counts > 0): ?>
                     <?php foreach($alokasis as $alokasi): ?>
                        <tr>
                           <td>
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $alokasi->id_alokasi_smp; ?>">
                           </td>
                           <td style="text-align:center">
                              <strong><?= _ent($alokasi->kelas_label); ?></strong>
                           </td>
                           <td>
                              <span class="label label-info"><?= _ent($alokasi->mapel_kode); ?></span>
                              <?= _ent($alokasi->mapel_nama); ?>
                           </td>
                           <td><?= _ent($alokasi->guru_nama_lengkap); ?></td>
                           <td style="text-align:center">
                              <strong><?= $alokasi->jam_target; ?></strong> jam
                           </td>
                           <td style="text-align:center">
                              <?= $alokasi->jam_terpenuhi; ?> jam
                           </td>
                           <td style="text-align:center">
                              <?php if($alokasi->status == 'final'): ?>
                                 <span class="label badge-final">FINAL</span>
                              <?php else: ?>
                                 <span class="label badge-draft">DRAFT</span>
                              <?php endif; ?>
                           </td>
                           <td style="white-space:nowrap;text-align:center">
                              <?php is_allowed('mapel_alokasi_smp_view', function() use ($alokasi){?>
                              <a href="<?= site_url('administrator/mapel_alokasi_smp/view/' . $alokasi->id_alokasi_smp); ?>" class="btn btn-action btn-action-view btn-sm">
                                 <i class="fa fa-eye"></i> Detail
                              </a>
                              <?php }) ?>
                              <?php if($alokasi->status == 'draft'): ?>
                                 <?php is_allowed('mapel_alokasi_smp_update', function() use ($alokasi){?>
                                 <button type="button" class="btn btn-action btn-action-final btn-sm btn-set-final" data-id="<?= $alokasi->id_alokasi_smp; ?>">
                                    <i class="fa fa-check"></i> Final
                                 </button>
                                 <?php }) ?>
                              <?php else: ?>
                                 <?php is_allowed('mapel_alokasi_smp_update', function() use ($alokasi){?>
                                 <button type="button" class="btn btn-action btn-action-draft btn-sm btn-set-draft" data-id="<?= $alokasi->id_alokasi_smp; ?>">
                                    <i class="fa fa-undo"></i> Draft
                                 </button>
                                 <?php }) ?>
                              <?php endif; ?>
                              <?php is_allowed('mapel_alokasi_smp_delete', function() use ($alokasi){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/mapel_alokasi_smp/delete/' . $alokasi->id_alokasi_smp); ?>" class="btn btn-action btn-action-delete btn-sm remove-data">
                                 <i class="fa fa-trash"></i> Hapus
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="8" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">Data alokasi tidak tersedia. <a href="<?= site_url('administrator/mapel_alokasi_smp/generate_draft'); ?>">Generate Draft</a></span>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <!-- Bulk Action & Pagination -->
               <div class="row" style="margin-top:15px">
                  <div class="col-md-6">
                     <div class="input-group" style="max-width:400px">
                        <select class="form-control" name="bulk" id="bulk">
                           <option value="">-- Bulk Action --</option>
                           <option value="final">Set Final</option>
                           <option value="draft">Set Draft</option>
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

   // Set Final single
   $('.btn-set-final').click(function(){
      var id = $(this).data('id');
      $.post(BASE_URL + '/administrator/mapel_alokasi_smp/set_final', {ids: [id]}, function(res){
         var data = JSON.parse(res);
         if(data.success) {
            toastr.success(data.message);
            location.reload();
         } else {
            toastr.error(data.message);
         }
      });
   });

   // Set Draft single
   $('.btn-set-draft').click(function(){
      var id = $(this).data('id');
      $.post(BASE_URL + '/administrator/mapel_alokasi_smp/set_draft', {ids: [id]}, function(res){
         var data = JSON.parse(res);
         if(data.success) {
            toastr.success(data.message);
            location.reload();
         } else {
            toastr.error(data.message);
         }
      });
   });

   // Bulk action
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_alokasi').serialize();

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
               document.location.href = BASE_URL + '/administrator/mapel_alokasi_smp/delete?' + serialize_bulk;      
            }
         });
         return false;
      } else if (bulk.val() == 'final' || bulk.val() == 'draft') {
         var ids = [];
         $('input.check:checked').each(function(){
            ids.push($(this).val());
         });
         
         if (ids.length == 0) {
            swal("Upss", "Pilih data terlebih dahulu", "warning");
            return false;
         }
         
         var action = bulk.val();
         var url = action == 'final' ? 'set_final' : 'set_draft';
         
         $.post(BASE_URL + '/administrator/mapel_alokasi_smp/' + url, {ids: ids}, function(res){
            var data = JSON.parse(res);
            if(data.success) {
               toastr.success(data.message);
               location.reload();
            } else {
               toastr.error(data.message);
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
