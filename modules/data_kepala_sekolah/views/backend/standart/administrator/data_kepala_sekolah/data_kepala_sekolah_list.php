
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('data_kepala_sekolah') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('data_kepala_sekolah') ?></li>
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
                        <?php is_allowed('data_kepala_sekolah_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('data_kepala_sekolah') ?>']); ?>" href="<?= site_url('administrator/data_kepala_sekolah/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('data_kepala_sekolah_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('data_kepala_sekolah') ?>']); ?>" href="<?= site_url('administrator/data_kepala_sekolah/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('data_kepala_sekolah') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('data_kepala_sekolah')]); ?>  <i class="label bg-yellow"><?= $data_kepala_sekolah_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_data_kepala_sekolah" id="form_data_kepala_sekolah" action="<?= base_url('administrator/data_kepala_sekolah/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('nama_kepsek') ?></th>
                           <th> <?= cclang('file_ttd') ?></th>
                           <th> <?= cclang('nrks') ?></th>
                           <th> <?= cclang('jenjang') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_data_kepala_sekolah">
                     <?php foreach($data_kepala_sekolahs as $data_kepala_sekolah): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $data_kepala_sekolah->id; ?>">
                           </td>
                                                       
                           <td><?= _ent($data_kepala_sekolah->nama_kepsek); ?></td> 
                           <td>
                              <?php if (!empty($data_kepala_sekolah->file_ttd)): ?>
                                <?php if (is_image($data_kepala_sekolah->file_ttd)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/data_kepala_sekolah/' . $data_kepala_sekolah->file_ttd; ?>">
                                  <img src="<?= BASE_URL . 'uploads/data_kepala_sekolah/' . $data_kepala_sekolah->file_ttd; ?>" class="image-responsive" alt="image data_kepala_sekolah" title="file_ttd data_kepala_sekolah" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/data_kepala_sekolah/' . $data_kepala_sekolah->file_ttd; ?>">
                                   <img src="<?= get_icon_file($data_kepala_sekolah->file_ttd); ?>" class="image-responsive image-icon" alt="image data_kepala_sekolah" title="file_ttd <?= $data_kepala_sekolah->file_ttd; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td><?= _ent($data_kepala_sekolah->nrks); ?></td> 
                           <td><?= _ent($data_kepala_sekolah->jenjang); ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('data_kepala_sekolah_view', function() use ($data_kepala_sekolah){?>
                                 <a href="<?= site_url('administrator/data_kepala_sekolah/single_pdf/' .$data_kepala_sekolah->id); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/data_kepala_sekolah/view/' . $data_kepala_sekolah->id); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('data_kepala_sekolah_update', function() use ($data_kepala_sekolah){?>
                              <a href="<?= site_url('administrator/data_kepala_sekolah/edit/' . $data_kepala_sekolah->id); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('data_kepala_sekolah_delete', function() use ($data_kepala_sekolah){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/data_kepala_sekolah/delete/' . $data_kepala_sekolah->id); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($data_kepala_sekolah_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Data Kepala Sekolah data is not available
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
                            <option <?= $this->input->get('f') == 'nama_kepsek' ? 'selected' :''; ?> value="nama_kepsek">Nama Kepsek</option>
                           <option <?= $this->input->get('f') == 'file_ttd' ? 'selected' :''; ?> value="file_ttd">File Ttd</option>
                           <option <?= $this->input->get('f') == 'nrks' ? 'selected' :''; ?> value="nrks">Nrks</option>
                           <option <?= $this->input->get('f') == 'jenjang' ? 'selected' :''; ?> value="jenjang">Jenjang</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/data_kepala_sekolah');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_data_kepala_sekolah').serialize();

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
               document.location.href = BASE_URL + '/administrator/data_kepala_sekolah/delete?' + serialize_bulk;      
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