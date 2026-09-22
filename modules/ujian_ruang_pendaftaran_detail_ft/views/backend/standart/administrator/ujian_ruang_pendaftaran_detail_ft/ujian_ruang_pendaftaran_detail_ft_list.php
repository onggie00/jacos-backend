
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('ujian_ruang_pendaftaran_detail_ft') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('ujian_ruang_pendaftaran_detail_ft') ?></li>
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
                     <a class="btn btn-flat btn-info" title="Cetak Absensi Simulasi" href="<?= site_url('administrator/ujian_ruang_pendaftaran_detail_ft/cetak_absensi?materi_simulasi=simulasi&f='.$_GET['f'].'&q='.$_GET['q']); ?>"><i class="fa fa-file-pdf-o"></i> Absensi Simulasi </a>
                     <a class="btn btn-flat btn-success" title="Cetak Absensi Ujian" href="<?= site_url('administrator/ujian_ruang_pendaftaran_detail_ft/cetak_absensi?materi_simulasi=materi&f='.$_GET['f'].'&q='.$_GET['q']); ?>"><i class="fa fa-file-pdf-o"></i> Absensi Ujian </a>
                     <a href="javascript:void(0);" data-href="<?= site_url('administrator/ujian_ruang_pendaftaran_detail_ft/kosongkan_ruang/' . $ujian_ruang_pendaftaran_detail_ft->id); ?>" class="btn btn-flat btn-warning remove-data"><i class="fa fa-close"></i> Kosongkan Ruangan</a>
                        <?php is_allowed('ujian_ruang_pendaftaran_detail_ft_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('ujian_ruang_pendaftaran_detail_ft')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/ujian_ruang_pendaftaran_detail_ft/add'); ?>"><i class="fa fa-plus-square-o" ></i> Peserta Ujian</a>
                        <?php }) ?>
                        <?php is_allowed('ujian_ruang_pendaftaran_detail_ft_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('ujian_ruang_pendaftaran_detail_ft') ?>" href="<?= site_url('administrator/ujian_ruang_pendaftaran_detail_ft/export?f='.$_GET[f].'&q='.$_GET[q]); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <a target="_blank" href="<?= site_url('administrator/ujian_ruang_pendaftaran_detail_ft/cetak_kartu_peserta?id_ruang_pendaftaran=' . $_GET['q']); ?>" class="btn btn-flat btn-success"><i class="fa fa-file-pdf-o"></i> Kartu Peserta</a>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('ujian_ruang_pendaftaran_detail_ft') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('ujian_ruang_pendaftaran_detail_ft')]); ?>  <i class="label bg-yellow"><?= $ujian_ruang_pendaftaran_detail_ft_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_ujian_ruang_pendaftaran_detail_ft" id="form_ujian_ruang_pendaftaran_detail_ft" action="<?= base_url('administrator/ujian_ruang_pendaftaran_detail_ft/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('id_ruang_pendaftaran') ?></th>
                           <th> <?= cclang('nama lengkap') ?></th>
                           <th> <?= cclang('nomor_peserta') ?></th>
                           <th> <?= cclang('password') ?></th>
                           <th> <?= cclang('added_at') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_ujian_ruang_pendaftaran_detail_ft">
                     <?php foreach($ujian_ruang_pendaftaran_detail_fts as $ujian_ruang_pendaftaran_detail_ft): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $ujian_ruang_pendaftaran_detail_ft->id; ?>">
                           </td>
                                                       
                           <td><?php if  ($ujian_ruang_pendaftaran_detail_ft->id_ruang_pendaftaran) {

                              echo anchor('administrator/ujian_ruang_pendaftaran_ft/view/'.$ujian_ruang_pendaftaran_detail_ft->id_ruang_pendaftaran.'?popup=show', $ujian_ruang_pendaftaran_detail_ft->ujian_ruang_pendaftaran_ft_nama_ruang, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($ujian_ruang_pendaftaran_detail_ft->nomor_peserta) {

                              echo anchor('administrator/siswa_ft/view_no_peserta/'.$ujian_ruang_pendaftaran_detail_ft->nomor_peserta.'?popup=show', $ujian_ruang_pendaftaran_detail_ft->siswa_ft_nama_lengkap, ['class' => 'popup-view']); }?> </td>
                           <td>
                              <?php echo $ujian_ruang_pendaftaran_detail_ft->no_peserta; ?>
                           </td>
                           <td>
                              <?php echo $ujian_ruang_pendaftaran_detail_ft->password_ujian; ?>
                           </td>
                           <td><?= _ent($ujian_ruang_pendaftaran_detail_ft->added_at); ?></td> 
                           <td width="200">
                            
                           <?php is_allowed('ujian_ruang_pendaftaran_detail_ft_view', function() use ($ujian_ruang_pendaftaran_detail_ft){?>
                              <a href="<?= site_url('administrator/ujian_ruang_pendaftaran_detail_ft/single_pdf/' .$ujian_ruang_pendaftaran_detail_ft->id); ?>" class="label-default hidden"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/ujian_ruang_pendaftaran_detail_ft/view/' . $ujian_ruang_pendaftaran_detail_ft->id); ?>" class="label-default hidden"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('ujian_ruang_pendaftaran_detail_ft_update', function() use ($ujian_ruang_pendaftaran_detail_ft){?>
                              <a href="<?= site_url('administrator/ujian_ruang_pendaftaran_detail_ft/edit/' . $ujian_ruang_pendaftaran_detail_ft->id); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('ujian_ruang_pendaftaran_detail_ft_delete', function() use ($ujian_ruang_pendaftaran_detail_ft){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/ujian_ruang_pendaftaran_detail_ft/delete/' . $ujian_ruang_pendaftaran_detail_ft->id); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                              <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($ujian_ruang_pendaftaran_detail_ft_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Detail Peserta Ujian PSB FT data is not available
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
                                                     <option value="delete">Delete</option>
                                                  </select>
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
                            <option <?= $this->input->get('f') == 'id_ruang_pendaftaran' ? 'selected' :''; ?> value="id_ruang_pendaftaran">Ruang</option>
                           <option <?= $this->input->get('f') == 'nomor_peserta' ? 'selected' :''; ?> value="nomor_peserta">Nomor Peserta</option>
                           <option <?= $this->input->get('f') == 'added_at' ? 'selected' :''; ?> value="added_at">Added At</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/ujian_ruang_pendaftaran_detail_ft');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_ujian_ruang_pendaftaran_detail_ft').serialize();

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
               document.location.href = BASE_URL + '/administrator/ujian_ruang_pendaftaran_detail_ft/delete?' + serialize_bulk;      
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