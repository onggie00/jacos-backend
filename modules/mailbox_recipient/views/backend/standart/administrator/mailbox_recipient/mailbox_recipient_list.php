
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('mailbox_recipient') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('mailbox_recipient') ?></li>
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
                        <?php is_allowed('mailbox_recipient_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('mailbox_recipient')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/mailbox_recipient/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('mailbox_recipient')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('mailbox_recipient_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('mailbox_recipient') ?>']); ?>" href="<?= site_url('administrator/mailbox_recipient/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('mailbox_recipient_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('mailbox_recipient') ?>']); ?>" href="<?= site_url('administrator/mailbox_recipient/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('mailbox_recipient') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('mailbox_recipient')]); ?>  <i class="label bg-yellow"><?= $mailbox_recipient_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_mailbox_recipient" id="form_mailbox_recipient" action="<?= base_url('administrator/mailbox_recipient/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('id_mail') ?></th>
                           <th> <?= cclang('npp') ?></th>
                           <th> <?= cclang('nama_lengkap') ?></th>
                           <th> <?= cclang('role') ?></th>
                           <th> <?= cclang('is_read') ?></th>
                           <th> <?= cclang('is_important') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_mailbox_recipient">
                     <?php foreach($mailbox_recipients as $mailbox_recipient): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $mailbox_recipient->id; ?>">
                           </td>
                                                       
                           <td><?php if  ($mailbox_recipient->id_mail) {

                              echo anchor('administrator/mailbox_mail/view/'.$mailbox_recipient->id_mail.'?popup=show', $mailbox_recipient->mailbox_mail_mailbox_title, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($mailbox_recipient->npp); ?></td> 
                           <td><?= _ent($mailbox_recipient->nama_lengkap); ?></td> 
                           <td><?= _ent($mailbox_recipient->role); ?></td> 
                           <td><?= _ent($mailbox_recipient->is_read); ?></td> 
                           <td><?= _ent($mailbox_recipient->is_important); ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('mailbox_recipient_view', function() use ($mailbox_recipient){?>
                                 <a href="<?= site_url('administrator/mailbox_recipient/single_pdf/' .$mailbox_recipient->id); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/mailbox_recipient/view/' . $mailbox_recipient->id); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('mailbox_recipient_update', function() use ($mailbox_recipient){?>
                              <a href="<?= site_url('administrator/mailbox_recipient/edit/' . $mailbox_recipient->id); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('mailbox_recipient_delete', function() use ($mailbox_recipient){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/mailbox_recipient/delete/' . $mailbox_recipient->id); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($mailbox_recipient_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Mailbox Recipient data is not available
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
                            <option <?= $this->input->get('f') == 'id_mail' ? 'selected' :''; ?> value="id_mail">Mail</option>
                           <option <?= $this->input->get('f') == 'npp' ? 'selected' :''; ?> value="npp">NPP</option>
                           <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' :''; ?> value="nama_lengkap">Name</option>
                           <option <?= $this->input->get('f') == 'role' ? 'selected' :''; ?> value="role">Role</option>
                           <option <?= $this->input->get('f') == 'is_read' ? 'selected' :''; ?> value="is_read">Is Read?</option>
                           <option <?= $this->input->get('f') == 'is_important' ? 'selected' :''; ?> value="is_important">Marked As Important?</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/mailbox_recipient');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_mailbox_recipient').serialize();

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
               document.location.href = BASE_URL + '/administrator/mailbox_recipient/delete?' + serialize_bulk;      
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