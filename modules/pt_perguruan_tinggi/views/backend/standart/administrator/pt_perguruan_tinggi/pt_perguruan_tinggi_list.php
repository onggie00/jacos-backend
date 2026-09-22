
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('pt_perguruan_tinggi') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('pt_perguruan_tinggi') ?></li>
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
                        <?php is_allowed('pt_perguruan_tinggi_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('pt_perguruan_tinggi')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/pt_perguruan_tinggi/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('pt_perguruan_tinggi')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('pt_perguruan_tinggi_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('pt_perguruan_tinggi') ?>']); ?>" href="<?= site_url('administrator/pt_perguruan_tinggi/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-success" title="Import Data Perguruan Tinggi" data-toggle="modal" data-target="#modal_import"><i class="fa fa-pencil"></i> Import Data PTN</a>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('pt_perguruan_tinggi') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('pt_perguruan_tinggi')]); ?>  <i class="label bg-yellow"><?= $pt_perguruan_tinggi_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_pt_perguruan_tinggi" id="form_pt_perguruan_tinggi" action="<?= base_url('administrator/pt_perguruan_tinggi/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('nama_pt') ?></th>
                           <th> <?= cclang('inisial') ?></th>
                           <th> <?= cclang('provinsi') ?></th>
                           <th> <?= cclang('kota') ?></th>
                           <th> <?= cclang('logo_ptn') ?></th>
                           <th> <?= cclang('updated_at') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_pt_perguruan_tinggi">
                     <?php foreach($pt_perguruan_tinggis as $pt_perguruan_tinggi): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $pt_perguruan_tinggi->id; ?>">
                           </td>
                                                       
                           <td><?= _ent($pt_perguruan_tinggi->nama_pt); ?></td> 
                           <td><?= _ent($pt_perguruan_tinggi->inisial); ?></td> 
                           <td><?php if  ($pt_perguruan_tinggi->provinsi) {

                              echo anchor('administrator/provinces/view/'.$pt_perguruan_tinggi->provinsi.'?popup=show', $pt_perguruan_tinggi->provinces_name, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($pt_perguruan_tinggi->kota) {

                              echo anchor('administrator/regencies/view/'.$pt_perguruan_tinggi->kota.'?popup=show', $pt_perguruan_tinggi->regencies_name, ['class' => 'popup-view']); }?> </td>
                             
                           <td>
                              <?php if (!empty($pt_perguruan_tinggi->logo_ptn)): ?>
                                <?php if (is_image($pt_perguruan_tinggi->logo_ptn)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/pt_perguruan_tinggi/' . $pt_perguruan_tinggi->logo_ptn; ?>">
                                  <img src="<?= BASE_URL . 'uploads/pt_perguruan_tinggi/' . $pt_perguruan_tinggi->logo_ptn; ?>" class="image-responsive" alt="image pt_perguruan_tinggi" title="logo_ptn pt_perguruan_tinggi" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/pt_perguruan_tinggi/' . $pt_perguruan_tinggi->logo_ptn; ?>">
                                   <img src="<?= get_icon_file($pt_perguruan_tinggi->logo_ptn); ?>" class="image-responsive image-icon" alt="image pt_perguruan_tinggi" title="logo_ptn <?= $pt_perguruan_tinggi->logo_ptn; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td><?= _ent($pt_perguruan_tinggi->updated_at); ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('pt_perguruan_tinggi_view', function() use ($pt_perguruan_tinggi){?>
                              <a href="<?= site_url('administrator/pt_perguruan_tinggi/view/' . $pt_perguruan_tinggi->id); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('pt_perguruan_tinggi_update', function() use ($pt_perguruan_tinggi){?>
                              <a href="<?= site_url('administrator/pt_perguruan_tinggi/edit/' . $pt_perguruan_tinggi->id); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('pt_perguruan_tinggi_delete', function() use ($pt_perguruan_tinggi){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/pt_perguruan_tinggi/delete/' . $pt_perguruan_tinggi->id); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($pt_perguruan_tinggi_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Perguruan Tinggi data is not available
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
                            <option <?= $this->input->get('f') == 'nama_pt' ? 'selected' :''; ?> value="nama_pt">Perguruan Tinggi</option>
                           <option <?= $this->input->get('f') == 'inisial' ? 'selected' :''; ?> value="inisial">Singkatan</option>
                           <option <?= $this->input->get('f') == 'provinsi' ? 'selected' :''; ?> value="provinsi">Provinsi</option>
                           <option <?= $this->input->get('f') == 'kota' ? 'selected' :''; ?> value="kota">Kota</option>
                           <option <?= $this->input->get('f') == 'logo_ptn' ? 'selected' :''; ?> value="logo_ptn">Logo PTN</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/pt_perguruan_tinggi');?>" title="<?= cclang('reset_filter'); ?>">
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
<!-- ============ MODAL IMPORT DATA SISWA  =============== -->
<div class="modal fade" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Import Data PTN </h3>
         </div>
         <form action="<?= base_url('administrator/pt_perguruan_tinggi/import_ptn/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">File</label>
                  <div class="col-xs-8">
                     <input name="file_import" class="form-control" type="file"  accept=".xls,.xlsx" required>
                  </div>
               </div>
               <span>*Pastikan seluruh PTN telah mempunyai Inisial.</span><br/>
               <span>*Data yang akan diimport akan sesuai format file hasil export Excel</span><br/>
            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Tutup</button>
               <button type="submit" class="btn btn-info btn_save">Import</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL IMPORT DATA SISWA -->

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
      var serialize_bulk = $('#form_pt_perguruan_tinggi').serialize();

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
               document.location.href = BASE_URL + '/administrator/pt_perguruan_tinggi/delete?' + serialize_bulk;      
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