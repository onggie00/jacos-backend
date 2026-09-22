
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('ujian_ruang_pendaftaran_sma') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('ujian_ruang_pendaftaran_sma') ?></li>
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
                        <?php is_allowed('ujian_ruang_pendaftaran_sma_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('ujian_ruang_pendaftaran_sma')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/ujian_ruang_pendaftaran_sma/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('ujian_ruang_pendaftaran_sma')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('ujian_ruang_pendaftaran_sma_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('ujian_ruang_pendaftaran_sma') ?>']); ?>" href="<?= site_url('administrator/ujian_ruang_pendaftaran_sma/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('ujian_ruang_pendaftaran_sma_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('ujian_ruang_pendaftaran_sma') ?>']); ?>" href="<?= site_url('administrator/ujian_ruang_pendaftaran_sma/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('ujian_ruang_pendaftaran_sma') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('ujian_ruang_pendaftaran_sma')]); ?>  <i class="label bg-yellow"><?= $ujian_ruang_pendaftaran_sma_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_ujian_ruang_pendaftaran_sma" id="form_ujian_ruang_pendaftaran_sma" action="<?= base_url('administrator/ujian_ruang_pendaftaran_sma/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('nama_ruang') ?></th>
                           <th> <?= cclang('judul_ujian') ?></th>
                           <th> <?= cclang('tahun_ajaran') ?></th>
                           <th> <?= cclang('kepala_sekolah') ?></th>
                           <th> <?= cclang('created_at') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_ujian_ruang_pendaftaran_sma">
                     <?php foreach($ujian_ruang_pendaftaran_smas as $ujian_ruang_pendaftaran_sma): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $ujian_ruang_pendaftaran_sma->id_ruang_pendaftaran; ?>">
                           </td>
                                                       
                           <td><?= _ent($ujian_ruang_pendaftaran_sma->nama_ruang); ?></td> 
                           <td><?= _ent($ujian_ruang_pendaftaran_sma->judul_ujian); ?></td> 
                           <td><?php echo $ujian_ruang_pendaftaran_sma->tahun_ajaran; ?> </td>
                             
                           <td><?php echo $ujian_ruang_pendaftaran_sma->kepala_sekolah; ?> </td>
                             
                           <td><?= _ent($ujian_ruang_pendaftaran_sma->created_at); ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('ujian_ruang_pendaftaran_sma_view', function() use ($ujian_ruang_pendaftaran_sma){?>
                              <a target="_blank" href="<?= site_url('administrator/ujian_ruang_pendaftaran_sma/cetak_album/' .$ujian_ruang_pendaftaran_sma->id_ruang_pendaftaran); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= "Cetak Album"; ?> <br/>
                              <a href="<?= site_url('administrator/ujian_ruang_pendaftaran_sma/view/' . $ujian_ruang_pendaftaran_sma->id_ruang_pendaftaran); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('ujian_ruang_pendaftaran_sma_update', function() use ($ujian_ruang_pendaftaran_sma){?>
                              <a href="<?= site_url('administrator/ujian_ruang_pendaftaran_sma/edit/' . $ujian_ruang_pendaftaran_sma->id_ruang_pendaftaran); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('ujian_ruang_pendaftaran_sma_delete', function() use ($ujian_ruang_pendaftaran_sma){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/ujian_ruang_pendaftaran_sma/delete/' . $ujian_ruang_pendaftaran_sma->id_ruang_pendaftaran); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($ujian_ruang_pendaftaran_sma_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Ruang Ujian PSB SMA data is not available
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
                            <option <?= $this->input->get('f') == 'nama_ruang' ? 'selected' :''; ?> value="nama_ruang">Nama Ruang</option>
                           <option <?= $this->input->get('f') == 'judul_ujian' ? 'selected' :''; ?> value="judul_ujian">Ujian</option>
                           <option <?= $this->input->get('f') == 'tahun_ajaran' ? 'selected' :''; ?> value="tahun_ajaran">Tahun Ajaran</option>
                           <option <?= $this->input->get('f') == 'kepala_sekolah' ? 'selected' :''; ?> value="kepala_sekolah">Kepala Sekolah</option>
                           <option <?= $this->input->get('f') == 'created_at' ? 'selected' :''; ?> value="created_at">Created At</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/ujian_ruang_pendaftaran_sma');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_ujian_ruang_pendaftaran_sma').serialize();

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
               document.location.href = BASE_URL + '/administrator/ujian_ruang_pendaftaran_sma/delete?' + serialize_bulk;      
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