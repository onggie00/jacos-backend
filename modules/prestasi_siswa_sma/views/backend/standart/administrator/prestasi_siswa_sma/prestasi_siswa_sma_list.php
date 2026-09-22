
<script type="text/javascript">
</script>
<style>
/* Info box */
.info-box-col { width:20%; float:left; padding-left:8px; padding-right:8px; margin-bottom:12px; }
@media (max-width:1199px) { .info-box-col { width:33.333333%; } }
@media (max-width:767px) { .info-box-col { width:50%; } }
@media (max-width:480px) { .info-box-col { width:100%; } }
.info-box-rekap { border-radius:4px; padding:12px; color:#fff; min-height:80px; box-shadow:0 1px 1px rgba(0,0,0,0.1); }
.info-box-rekap .title { font-size:13px; font-weight:600; margin-bottom:6px; }
.info-box-rekap .value { font-size:24px; font-weight:700; }
.bg-green { background:#00a65a; }
.bg-blue { background:#3c8dbc; }
.bg-orange { background:#f39c12; }
.bg-red { background:#dd4b39; }
</style>
<!-- Content Header (Page header) -->
<style>
.datepicker, .datepicker-dropdown { z-index: 1050 !important; }
.filter-tambahan-box { overflow: visible !important; }
@media (max-width: 768px) {
   .content-header > h1 { font-size:18px; }
   .table { font-size:12px; }
   .table th, .table td { white-space:nowrap; }
   .filter-tambahan-box .box-body { padding:8px; }
   .filter-row .form-control { font-size:12px; }
   .info-box-rekap { min-height:60px; padding:8px; }
   .info-box-rekap .value { font-size:18px; }
   .btn-block-mobile { width:100%; margin-bottom:5px; }
}
</style>
<section class="content-header">
   <h1>
      <?= cclang('prestasi_siswa_sma') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('prestasi_siswa_sma') ?></li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row" >
      
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-body ">
               <!-- Widget: user widget style 1 -->
               <div class="box box-widget widget-user-2">
                  <!-- Add the bg color to the header using any of the bg-* classes -->
                  <div class="widget-user-header ">
                     <div class="row pull-right">
                        <?php is_allowed('prestasi_siswa_sma_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('prestasi_siswa_sma')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/prestasi_siswa_sma/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('prestasi_siswa_sma')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('prestasi_siswa_sma_export', function(){?>
                        <?php 
                              $filter_param = null;
                              if (!empty($_GET['f'])) {
                                 $filter_param = "?f=".$_GET['f'];
                              }
                              if (!empty($_GET['f']) && empty($filter_param)) {
                                 $filter_param = "?f=&q=".$_GET['q'];
                              }
                              else if(!empty($_GET['q']) && !empty($filter_param)){
                                 $filter_param = $filter_param."&q=".$_GET['q'];
                              }
                              else{
                                 $filter_param = "?q=".$_GET['q'];
                              }

                           ?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('prestasi_siswa_sma') ?>" href="<?php echo (!empty($filter_param)) ? site_url('administrator/prestasi_siswa_sma/export').$filter_param : site_url('administrator/prestasi_siswa_sma/export') ; ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('prestasi_siswa_sma') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('prestasi_siswa_sma')]); ?>  <i class="label bg-yellow"><?= $prestasi_siswa_sma_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_prestasi_siswa_sma" id="form_prestasi_siswa_sma" action="<?= base_url('administrator/prestasi_siswa_sma/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('nama_prestasi') ?></th>
                           <th> <?= cclang('tgl_raih') ?></th>
                           <th> <?= cclang('juara') ?></th>
                           <th> <?= cclang('file_prestasi') ?></th>
                           <th> <?= cclang('foto_prestasi') ?></th>
                           <th> <?= cclang('id_siswa') ?></th>
                           <th> <?= cclang('jenis_prestasi_id') ?></th>
                           <th> <?= cclang('konten') ?></th>
                           <th> <?= cclang('tanggal_posting') ?></th>
                           <th> <?= cclang('is_approved') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_prestasi_siswa_sma">
                     <?php foreach($prestasi_siswa_smas as $prestasi_siswa_sma): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $prestasi_siswa_sma->id_prestasi; ?>">
                           </td>
                                                       
                           <td><?= _ent($prestasi_siswa_sma->nama_prestasi); ?></td> 
                           <td><?= _ent($prestasi_siswa_sma->tgl_raih); ?></td> 
                           <td><?= _ent($prestasi_siswa_sma->juara); ?></td> 
                           <td>
                              <?php if (!empty($prestasi_siswa_sma->file_prestasi)): ?>
                                <?php if (is_image($prestasi_siswa_sma->file_prestasi)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_siswa_sma/' . $prestasi_siswa_sma->file_prestasi; ?>">
                                  <img src="<?= BASE_URL . 'uploads/prestasi_siswa_sma/' . $prestasi_siswa_sma->file_prestasi; ?>" class="image-responsive" alt="image prestasi_siswa_sma" title="file_prestasi prestasi_siswa_sma" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/prestasi_siswa_sma/' . $prestasi_siswa_sma->file_prestasi; ?>">
                                   <img src="<?= get_icon_file($prestasi_siswa_sma->file_prestasi); ?>" class="image-responsive image-icon" alt="image prestasi_siswa_sma" title="file_prestasi <?= $prestasi_siswa_sma->file_prestasi; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td>
                              <?php if (!empty($prestasi_siswa_sma->foto_prestasi)): ?>
                                <?php if (is_image($prestasi_siswa_sma->foto_prestasi)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_siswa_sma/' . $prestasi_siswa_sma->foto_prestasi; ?>">
                                  <img src="<?= BASE_URL . 'uploads/prestasi_siswa_sma/' . $prestasi_siswa_sma->foto_prestasi; ?>" class="image-responsive" alt="image prestasi_siswa_sma" title="foto_prestasi prestasi_siswa_sma" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/prestasi_siswa_sma/' . $prestasi_siswa_sma->foto_prestasi; ?>">
                                   <img src="<?= get_icon_file($prestasi_siswa_sma->foto_prestasi); ?>" class="image-responsive image-icon" alt="image prestasi_siswa_sma" title="foto_prestasi <?= $prestasi_siswa_sma->foto_prestasi; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td><?php if  ($prestasi_siswa_sma->id_siswa) {

                              echo anchor('administrator/siswa_sma_aktif/view/'.$prestasi_siswa_sma->id_siswa.'?popup=show', $prestasi_siswa_sma->siswa_sma_aktif_nama_lengkap, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($prestasi_siswa_sma->jenis_prestasi_id) {

                              echo anchor('administrator/jenis_prestasi/view/'.$prestasi_siswa_sma->jenis_prestasi_id.'?popup=show', $prestasi_siswa_sma->jenis_prestasi, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($prestasi_siswa_sma->konten); ?></td> 
                           <td><?= _ent($prestasi_siswa_sma->tanggal_posting); ?></td> 
                           <td><?= (!empty($prestasi_siswa_sma->is_approved)) ?  "DISETUJUI" : "BELUM DISETUJUI"; ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('prestasi_siswa_sma_view', function() use ($prestasi_siswa_sma){?>
                                 <a href="<?= site_url('administrator/prestasi_siswa_sma/single_pdf/' .$prestasi_siswa_sma->id_prestasi); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/prestasi_siswa_sma/view/' . $prestasi_siswa_sma->id_prestasi); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('prestasi_siswa_sma_update', function() use ($prestasi_siswa_sma){?>
                              <a href="<?= site_url('administrator/prestasi_siswa_sma/edit/' . $prestasi_siswa_sma->id_prestasi); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('prestasi_siswa_sma_delete', function() use ($prestasi_siswa_sma){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/prestasi_siswa_sma/delete/' . $prestasi_siswa_sma->id_prestasi); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($prestasi_siswa_sma_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Prestasi Siswa SMA data is not available
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
                     <div class="col-sm-2 padd-left-0 " >
                        <select type="text" class="form-control chosen chosen-select" name="bulk" id="bulk" placeholder="Site Email" >
                           <option value="">Bulk</option>
                           <option value="disetujui">Disetujui</option>
                           <option value="ditolak">Ditolak</option>
                           <option value="delete">Delete</option>
                        </select>
                        <input type="hidden" id="st" name="st">
                     </div>
                     <div class="col-sm-2 padd-left-0 ">
                        <button type="button" class="btn btn-flat" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>"><?= cclang('apply_button'); ?></button>
                     </div>
                     <div class="col-sm-3 padd-left-0  " >
                        <input type="text" class="form-control" name="q" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('q'); ?>">
                     </div>
                     <div class="col-sm-3 padd-left-0 " >
                        <select type="text" class="form-control chosen chosen-select" name="f" id="field" >
                           <option value=""><?= cclang('all'); ?></option>
                           <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' :''; ?> value="nama_lengkap">Nama Lengkap</option>
                           <option <?= $this->input->get('f') == 'kelas' ? 'selected' :''; ?> value="kelas">Kelas</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/prestasi_siswa_sma');?>" title="<?= cclang('reset_filter'); ?>">
                        <i class="fa fa-undo"></i>
                        </a>
                     </div>
                  </div>
                  </form>                  <div class="col-md-4">
                     <div class="dataTables_paginate paging_simple_numbers pull-right" id="example2_paginate" >
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

<!-- Page script -->
<script>
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
      var ids = $('#form_prestasi_siswa_sma input[name="id[]"]:checked');
      if (!ids.length) {
         swal({
            title: "Upss",
            text: "Pilih data terlebih dahulu",
            type: "warning",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Okay!"
         });
         return false;
      }
      var checked_ids = ids.serialize();
      var st_val = '';
      if (bulk.val() == 'disetujui') {
         st_val = '1';
      } else if (bulk.val() == 'ditolak') {
         st_val = '2';
      }
      var serialize_bulk = checked_ids + '&bulk=' + encodeURIComponent(bulk.val()) + '&st=' + encodeURIComponent(st_val);

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
               document.location.href = BASE_URL + '/administrator/prestasi_siswa_sma/delete?' + serialize_bulk;      
            }
          });

        return false;

      } 
      else if (bulk.val() == 'disetujui') {
            swal({
               title: "<?= cclang('are_you_sure'); ?>",
               text: "Data berikut akan disetujui ?",
               type: "warning",
               showCancelButton: true,
               confirmButtonColor: "#DD6B55",
               confirmButtonText: "Ya, disetujui",
               cancelButtonText: "Tidak, batal",
               closeOnConfirm: true,
               closeOnCancel: true
            },
            function(isConfirm){
               if (isConfirm) {
                  document.location.href = BASE_URL + '/administrator/prestasi_siswa_sma/update_status?' + serialize_bulk;      
               }
            });

         return false;
      }
      else if (bulk.val() == 'ditolak') {

            swal({
               title: "<?= cclang('are_you_sure'); ?>",
               text: "Data berikut akan ditolak ?",
               type: "warning",
               showCancelButton: true,
               confirmButtonColor: "#DD6B55",
               confirmButtonText: "Ya, ditolak",
               cancelButtonText: "Tidak, batal",
               closeOnConfirm: true,
               closeOnCancel: true
            },
            function(isConfirm){
               if (isConfirm) {
                  document.location.href = BASE_URL + '/administrator/prestasi_siswa_sma/update_status?' + serialize_bulk;      
               }
            });

            return false;
      }
      else if(bulk.val() == '')  {
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

    });/*end appliy click*/


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

    checkboxes.on('ifChanged', function(event){
        if(checkboxes.filter(':checked').length == checkboxes.length) {
            checkAll.prop('checked', 'checked');
        } else {
            checkAll.removeProp('checked');
        }
        checkAll.iCheck('update');
    });

  }); /*end doc ready*/
</script>
<script>
$(function(){
   // Re-init datepicker when filter box expanded (fix visual bug)
   $('.filter-tambahan-box').on('shown.bs.collapse', function(){
      $('.datepicker').datepicker('update');
   });
});
</script>
