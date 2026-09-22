
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('ekskul_manajemen_ft') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('ekskul_manajemen_ft') ?></li>
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
                        <?php is_allowed('ekskul_manajemen_ft_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('ekskul_manajemen_ft')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/ekskul_manajemen_ft/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('ekskul_manajemen_ft')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('ekskul_manajemen_ft_export', function(){?>
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
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('ekskul_manajemen_ft') ?>" href="<?php echo (!empty($filter_param)) ? site_url('administrator/ekskul_manajemen_ft/export').$filter_param : site_url('administrator/ekskul_manajemen_ft/export') ; ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('ekskul_manajemen_ft_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('ekskul_manajemen_ft') ?>']); ?>" href="<?= site_url('administrator/ekskul_manajemen_ft/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('ekskul_manajemen_ft') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('ekskul_manajemen_ft')]); ?>  <i class="label bg-yellow"><?= $ekskul_manajemen_ft_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_ekskul_manajemen_ft" id="form_ekskul_manajemen_ft" action="<?= base_url('administrator/ekskul_manajemen_ft/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('id_ekskul') ?></th>
                           <th> <?= cclang('id_guru_pembina') ?></th>
                           <th> <?= cclang('nama') ?></th>
                           <th> <?= cclang('keterangan') ?></th>
                           <th> <?= cclang('email_pelatih') ?></th>
                           <th> <?= cclang('notelp_pelatih') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_ekskul_manajemen_ft">
                     <?php foreach($ekskul_manajemen_fts as $ekskul_manajemen_ft): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $ekskul_manajemen_ft->id_manajemen; ?>">
                           </td>
                                                       
                           <td><?php if  ($ekskul_manajemen_ft->id_ekskul) {

                              echo anchor('administrator/ekskul/view/'.$ekskul_manajemen_ft->id_ekskul.'?popup=show', $ekskul_manajemen_ft->ekskul_nama, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($ekskul_manajemen_ft->id_guru_pembina) {
                                 if ($ekskul_manajemen_ft->jenjang_pembina == "ft") {
                                    echo anchor('administrator/guru_ft/view/'.$ekskul_manajemen_ft->id_guru_pembina.'?popup=show', $ekskul_manajemen_ft->guru_ft_nama_lengkap, ['class' => 'popup-view']);
                                 }
                                 else if($ekskul_manajemen_ft->jenjang_pembina == "sma"){
                                    $get_guru_sma = $this->mymodel->withquery("select nama_lengkap from guru_sma where id_guru = '".$ekskul_manajemen_ft->id_guru_pembina."'","row");
                                    echo anchor('administrator/guru_sma/view/'.$ekskul_manajemen_ft->id_guru_pembina.'?popup=show', $get_guru_sma->nama_lengkap, ['class' => 'popup-view']);
                                 }
                              }
                              ?>
                           </td>
                             
                           <td><?= _ent($ekskul_manajemen_ft->nama); ?></td> 
                           <td><?= _ent($ekskul_manajemen_ft->keterangan); ?></td> 
                           <td><?= _ent($ekskul_manajemen_ft->email_pelatih); ?></td> 
                           <td><?= _ent($ekskul_manajemen_ft->notelp_pelatih); ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('ekskul_manajemen_ft_view', function() use ($ekskul_manajemen_ft){?>
                              <a href="<?= site_url('administrator/ekskul_manajemen_ft/view/' . $ekskul_manajemen_ft->id_manajemen); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('ekskul_manajemen_ft_update', function() use ($ekskul_manajemen_ft){?>
                              <a href="<?= site_url('administrator/ekskul_manajemen_ft/edit/' . $ekskul_manajemen_ft->id_manajemen); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('ekskul_manajemen_ft_delete', function() use ($ekskul_manajemen_ft){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/ekskul_manajemen_ft/delete/' . $ekskul_manajemen_ft->id_manajemen); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($ekskul_manajemen_ft_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Manajemen Ekskul FT data is not available
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
                            <option <?= $this->input->get('f') == 'id_ekskul' ? 'selected' :''; ?> value="id_ekskul">Ekstrakurikuler</option>
                           <option <?= $this->input->get('f') == 'id_guru_pembina' ? 'selected' :''; ?> value="id_guru_pembina">Pembina</option>
                           <option <?= $this->input->get('f') == 'nama' ? 'selected' :''; ?> value="nama">Nama Pelatih</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/ekskul_manajemen_ft');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_ekskul_manajemen_ft').serialize();

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
               document.location.href = BASE_URL + '/administrator/ekskul_manajemen_ft/delete?' + serialize_bulk;      
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