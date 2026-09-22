
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('kpi_smp') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('kpi_smp') ?></li>
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
                        <?php is_allowed('kpi_smp_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('kpi_smp')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/kpi_smp/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('kpi_smp')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('kpi_smp_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('kpi_smp') ?>']); ?>" href="<?= site_url('administrator/kpi_smp/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('kpi_smp_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('kpi_smp') ?>']); ?>" href="<?= site_url('administrator/kpi_smp/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('kpi_smp') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('kpi_smp')]); ?>  <i class="label bg-yellow"><?= $kpi_smp_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_kpi_smp" id="form_kpi_smp" action="<?= base_url('administrator/kpi_smp/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('nama_kpi') ?></th>
                           <th> <?= cclang('id_guru') ?></th>
                           <th> <?= cclang('judul_kpi') ?></th>
                           <th> <?= cclang('id_jenis_kpi') ?></th>
                           <th> <?= cclang('tanggal') ?></th>
                           <th> <?= cclang('keterangan') ?></th>
                           <th> <?= cclang('file_piagam') ?></th>
                           <th> <?= cclang('is_approve') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_kpi_smp">
                     <?php foreach($kpi_smps as $kpi_smp): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $kpi_smp->id_kpi; ?>">
                           </td>
                                                       
                           <td><?= _ent($kpi_smp->nama_kpi); ?></td> 
                           <td><?php if  ($kpi_smp->id_guru) {

                              echo anchor('administrator/guru_smp/view/'.$kpi_smp->id_guru.'?popup=show', $kpi_smp->guru_smp_nama_lengkap, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($kpi_smp->judul_kpi); ?></td> 
                           <td><?php if  ($kpi_smp->id_jenis_kpi) {

                              echo anchor('administrator/jenis_kpi/view/'.$kpi_smp->id_jenis_kpi.'?popup=show', $kpi_smp->jenis_kpi_nama_jenis, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($kpi_smp->tanggal); ?></td> 
                           <td><?= _ent($kpi_smp->keterangan); ?></td> 
                           <td>
                              <?php if (!empty($kpi_smp->file_piagam)): ?>
                                <?php if (is_image($kpi_smp->file_piagam)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/kpi_smp/' . $kpi_smp->file_piagam; ?>">
                                  <img src="<?= BASE_URL . 'uploads/kpi_smp/' . $kpi_smp->file_piagam; ?>" class="image-responsive" alt="image kpi_smp" title="file_piagam kpi_smp" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/kpi_smp/' . $kpi_smp->file_piagam; ?>">
                                   <img src="<?= get_icon_file($kpi_smp->file_piagam); ?>" class="image-responsive image-icon" alt="image kpi_smp" title="file_piagam <?= $kpi_smp->file_piagam; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td><?= _ent(get_status_kpi($kpi_smp->is_approve)); ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('kpi_smp_view', function() use ($kpi_smp){?>
                                 <a href="<?= site_url('administrator/kpi_smp/single_pdf/' .$kpi_smp->id_kpi); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/kpi_smp/view/' . $kpi_smp->id_kpi); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('kpi_smp_update', function() use ($kpi_smp){?>
                              <a href="<?= site_url('administrator/kpi_smp/edit/' . $kpi_smp->id_kpi); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('kpi_smp_delete', function() use ($kpi_smp){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/kpi_smp/delete/' . $kpi_smp->id_kpi); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($kpi_smp_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Kpi Smp data is not available
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
                            <option <?= $this->input->get('f') == 'nama_kpi' ? 'selected' :''; ?> value="nama_kpi">Nama Kpi</option>
                           <option <?= $this->input->get('f') == 'id_guru' ? 'selected' :''; ?> value="id_guru">Id Guru</option>
                           <option <?= $this->input->get('f') == 'judul_kpi' ? 'selected' :''; ?> value="judul_kpi">Judul Kpi</option>
                           <option <?= $this->input->get('f') == 'id_jenis_kpi' ? 'selected' :''; ?> value="id_jenis_kpi">Id Jenis Kpi</option>
                           <option <?= $this->input->get('f') == 'tanggal' ? 'selected' :''; ?> value="tanggal">Tanggal</option>
                           <option <?= $this->input->get('f') == 'keterangan' ? 'selected' :''; ?> value="keterangan">Keterangan</option>
                           <option <?= $this->input->get('f') == 'file_piagam' ? 'selected' :''; ?> value="file_piagam">File Piagam</option>
                           <option <?= $this->input->get('f') == 'is_approve' ? 'selected' :''; ?> value="is_approve">Is Approve</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/kpi_smp');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_kpi_smp').serialize();

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
               document.location.href = BASE_URL + '/administrator/kpi_smp/delete?' + serialize_bulk;      
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