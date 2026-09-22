
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('fasilitas') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('fasilitas') ?></li>
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
                        <?php is_allowed('fasilitas_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('fasilitas')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/fasilitas/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('fasilitas')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('fasilitas_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('fasilitas') ?>']); ?>" href="<?= site_url('administrator/fasilitas/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('fasilitas_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('fasilitas') ?>']); ?>" href="<?= site_url('administrator/fasilitas/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('fasilitas') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('fasilitas')]); ?>  <i class="label bg-yellow"><?= $fasilitas_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_fasilitas" id="form_fasilitas" action="<?= base_url('administrator/fasilitas/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('nama_fasilitas') ?></th>
                           <th> <?= cclang('deskripsi') ?></th>
                           <th> <?= cclang('img_fasilitas') ?></th>
                           <th> <?= cclang('jenjang') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_fasilitas">
                     <?php foreach($fasilitass as $fasilitas): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $fasilitas->id_fasilitas; ?>">
                           </td>
                                                       
                           <td><?= _ent($fasilitas->nama_fasilitas); ?></td> 
                           <td><?= _ent($fasilitas->deskripsi); ?></td> 
                           <td>
                              <?php if (!empty($fasilitas->img_fasilitas)): ?>
                                <?php if (is_image($fasilitas->img_fasilitas)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/fasilitas/' . $fasilitas->img_fasilitas; ?>">
                                  <img src="<?= BASE_URL . 'uploads/fasilitas/' . $fasilitas->img_fasilitas; ?>" class="image-responsive" alt="image fasilitas" title="img_fasilitas fasilitas" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/fasilitas/' . $fasilitas->img_fasilitas; ?>">
                                   <img src="<?= get_icon_file($fasilitas->img_fasilitas); ?>" class="image-responsive image-icon" alt="image fasilitas" title="img_fasilitas <?= $fasilitas->img_fasilitas; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td><?= _ent($fasilitas->jenjang); ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('fasilitas_view', function() use ($fasilitas){?>
                                 <a href="<?= site_url('administrator/fasilitas/single_pdf/' .$fasilitas->id_fasilitas); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/fasilitas/view/' . $fasilitas->id_fasilitas); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('fasilitas_update', function() use ($fasilitas){?>
                              <a href="<?= site_url('administrator/fasilitas/edit/' . $fasilitas->id_fasilitas); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('fasilitas_delete', function() use ($fasilitas){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/fasilitas/delete/' . $fasilitas->id_fasilitas); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($fasilitas_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Fasilitas data is not available
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
                            <option <?= $this->input->get('f') == 'nama_fasilitas' ? 'selected' :''; ?> value="nama_fasilitas">Nama Fasilitas</option>
                           <option <?= $this->input->get('f') == 'deskripsi' ? 'selected' :''; ?> value="deskripsi">Deskripsi</option>
                           <option <?= $this->input->get('f') == 'img_fasilitas' ? 'selected' :''; ?> value="img_fasilitas">Img Fasilitas</option>
                           <option <?= $this->input->get('f') == 'jenjang' ? 'selected' :''; ?> value="jenjang">Jenjang</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/fasilitas');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_fasilitas').serialize();

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
               document.location.href = BASE_URL + '/administrator/fasilitas/delete?' + serialize_bulk;      
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