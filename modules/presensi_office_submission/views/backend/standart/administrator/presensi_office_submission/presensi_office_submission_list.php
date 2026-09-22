
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('presensi_office_submission') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('presensi_office_submission') ?></li>
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
                        <?php is_allowed('presensi_office_submission_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('presensi_office_submission')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/presensi_office_submission/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('presensi_office_submission')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('presensi_office_submission_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('presensi_office_submission') ?>']); ?>" href="<?= site_url('administrator/presensi_office_submission/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('presensi_office_submission') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('presensi_office_submission')]); ?>  <i class="label bg-yellow"><?= $presensi_office_submission_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_presensi_office_submission" id="form_presensi_office_submission" action="<?= base_url('administrator/presensi_office_submission/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('npp') ?></th>
                           <th> <?= cclang('nama_lengkap') ?></th>
                           <th> <?= cclang('submission_code') ?></th>
                           <th> <?= cclang('file_submission') ?></th>
                           <th> <?= cclang('submission_status') ?></th>
                           <th> <?= cclang('date_start') ?></th>
                           <th> <?= cclang('created_at') ?></th>
                           <th> <?= cclang('updated_at') ?></th>
                           <th> <?= cclang('updated_by') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_presensi_office_submission">
                     <?php foreach($presensi_office_submissions as $presensi_office_submission): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $presensi_office_submission->id_submission; ?>">
                           </td>
                                                       
                           <td><?= _ent($presensi_office_submission->npp); ?></td> 
                           <td><?= _ent($presensi_office_submission->nama_lengkap); ?></td> 
                           <td><?php if  ($presensi_office_submission->submission_code) {

                              echo anchor('administrator/presensi_submission_type/view/'.$presensi_office_submission->submission_code.'?popup=show', $presensi_office_submission->presensi_submission_type_name, ['class' => 'popup-view']); }?> </td>
                             
                           <td>
                              <?php if (!empty($presensi_office_submission->file_submission)): ?>
                                <?php if (is_image($presensi_office_submission->file_submission)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL .  $presensi_office_submission->file_submission; ?>">
                                  <img src="<?= BASE_URL . $presensi_office_submission->file_submission; ?>" class="image-responsive" alt="image presensi_office_submission" title="file_submission presensi_office_submission" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL .  $presensi_office_submission->file_submission; ?>">
                                   <img src="<?= get_icon_file($presensi_office_submission->file_submission); ?>" class="image-responsive image-icon" alt="image presensi_office_submission" title="file_submission <?= $presensi_office_submission->file_submission; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td><?php if ($presensi_office_submission->submission_status == 0){ echo "Menunggu persetujuan"; } elseif ($presensi_office_submission->submission_status == 1){ echo "Disetujui";} elseif ($presensi_office_submission->submission_status == 2){ echo "Ditolak";} elseif ($presensi_office_submission->submission_status == 3){ echo "Perlu validasi lebih lanjut";} ?></td> 
                           <td><?= formatTanggalRange($presensi_office_submission->date_start, $presensi_office_submission->date_end) ; ?></td> 
                           <td><?= formatTanggal($presensi_office_submission->created_at); ?></td> 
                           <td><?= (!empty($presensi_office_submission->updated_at)) ? formatTanggal($presensi_office_submission->updated_at) : ""; ?></td> 
                           <td><?= _ent($presensi_office_submission->updated_by); ?></td> 
                           <td width="200">
                              <!--modal button detail-->
                              <button type="button" class="btn btn-flat btn-primary" data-toggle="modal" data-target="#modal-detail<?= $presensi_office_submission->id_submission; ?>">
                                 <i class="fa fa-eye"></i> 
                                </button>
                              <!--modal detail-->
                              <div class="modal fade" id="modal-detail<?= $presensi_office_submission->id_submission; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                 <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                       <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                          <h4 class="modal-title" id="myModalLabel">Detail Presensi Office Submission</h4>
                                       </div>
                                       <div class="modal-body">
                                          <table class="table table-bordered table-striped">                                           
                                             <tr>
                                                <td> <?= cclang('npp') ?></td>
                                                <td> <?= _ent($presensi_office_submission->npp); ?></td>
                                             </tr>
                                             <tr>
                                                <td> <?= cclang('nama_lengkap') ?></td>
                                                <td> <?= _ent($presensi_office_submission->nama_lengkap); ?></td>
                                             </tr>
                                             <tr>
                                                <td> <?= cclang('submission_code') ?></td>
                                                <td> <?php if  ($presensi_office_submission->submission_code) {
                                                   
                                                }
                                                ?> </td>
                                             </tr>
                                             <tr>
                                                <td> <?= cclang('file_submission') ?></td>
                                                <td> <?= "<img src='" . BASE_URL . $presensi_office_submission->file_submission . "' class='image-responsive' alt='image presensi_office_submission' title='file_submission presensi_office_submission ' width='40px'/>" ?></td>
                                             </tr>
                                             <tr>
                                                <td> <?= cclang('submission_status') ?></td>
                                                <td> <?php if ($presensi_office_submission->submission_status == 0){ echo "Menunggu persetujuan"; } elseif ($presensi_office_submission->submission_status == 1){ echo "Disetujui";} elseif ($presensi_office_submission->submission_status == 2){ echo "Ditolak";} elseif ($presensi_office_submission->submission_status == 3){ echo "Perlu validasi lebih lanjut";} ?></td>
                                             </tr>
                                             <tr>
                                                <td> <?= cclang('date_start') ?></td>
                                                <td> <?= _ent($presensi_office_submission->date_start); ?></td>
                                             </tr>
                                             <tr>
                                                <td> <?= cclang('created_at') ?></td>
                                                <td> <?= _ent($presensi_office_submission->created_at); ?></td>
                                             </tr>
                                             <tr>
                                                <td> <?= cclang('updated_at') ?></td>
                                                <td> <?= _ent($presensi_office_submission->updated_at); ?></td>
                                             </tr>
                                             <tr>
                                                <td> <?= cclang('updated_by') ?></td>
                                                <td> <?= _ent($presensi_office_submission->updated_by); ?></td>
                                             </tr>
                                          </table>
                                       </div>
                                       <div class="modal-footer">
                                             <!--Download PDF-->
                                             <a class="btn btn-info" target="_blank" href="<?= site_url('apiapp/pengajuan/export_pdf?id_submission=' . $presensi_office_submission->id_submission); ?>"> <i class="fa fa-download"></i> Generate PDF</a>
                                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <?php is_allowed('presensi_office_submission_update', function() use ($presensi_office_submission){?>
                                 <a href="<?= site_url('administrator/presensi_office_submission/edit/'.$presensi_office_submission->id_submission); ?>" class="btn btn-flat btn-info"><i class="fa fa-pencil "></i></a>
                              <?php }) ?> 
                              <?php is_allowed('presensi_office_submission_delete', function() use ($presensi_office_submission){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/presensi_office_submission/delete/' . $presensi_office_submission->id_submission); ?>" class="btn btn-flat btn-danger remove-data"><i class="fa fa-trash"></i></a>
                              <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($presensi_office_submission_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Presensi Office Submission data is not available
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
                            <option <?= $this->input->get('f') == 'npp' ? 'selected' :''; ?> value="npp">NPP</option>
                           <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' :''; ?> value="nama_lengkap">Nama Lengkap</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/presensi_office_submission');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_presensi_office_submission').serialize();

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
               document.location.href = BASE_URL + '/administrator/presensi_office_submission/delete?' + serialize_bulk;      
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