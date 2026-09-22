
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('mailbox_mail') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('mailbox_mail') ?></li>
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
                        <?php is_allowed('mailbox_mail_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('mailbox_mail')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/mailbox_mail/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('mailbox_mail')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('mailbox_mail_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('mailbox_mail') ?>']); ?>" href="<?= site_url('administrator/mailbox_mail/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('mailbox_mail') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('mailbox_mail')]); ?>  <i class="label bg-yellow"><?= $mailbox_mail_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_mailbox_mail" id="form_mailbox_mail" action="<?= base_url('administrator/mailbox_mail/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('mailbox_category') ?></th>
                           <th> <?= cclang('mailbox_title') ?></th>
                           <th> Description</th>
                           <th> <?= cclang('is_confidential') ?></th>
                           <th> <?= cclang('mail_number') ?></th>
                           <th> Signature</th>
                           <th> Receiver</th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_mailbox_mail">
                     <?php foreach($mailbox_mails as $mailbox_mail): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $mailbox_mail->id_mail; ?>">
                           </td>
                                                       
                           <td><?php if  ($mailbox_mail->mailbox_category) {

                              echo anchor('administrator/mailbox_category/view/'.$mailbox_mail->mailbox_category.'?popup=show', $mailbox_mail->mailbox_category_category, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($mailbox_mail->mailbox_title); ?></td> 
                           <td><?= $mailbox_mail->mailbox_description; ?></td>
                           <td><?= ($mailbox_mail->is_confidential == 1) ? "<b>Ya</b>" : "<b>Tidak</b>"; ?></td> 
                           <td><?= _ent($mailbox_mail->mail_number); ?></td> 
                           <td>
                              <a data-toggle="modal" data-target="#modal-signature<?= $mailbox_mail->id_mail;?>" class="btn btn-default"><i class="fa fa-eye"></i> List Signature</a>
                           </td>
                           <td>
                           <a data-toggle="modal" data-target="#modal-receiver<?= $mailbox_mail->id_mail;?>" class="btn btn-primary"><i class="fa fa-eye"></i> List Receiver</a>
                           </td>
                           <td width="200">
                            
                              <?php is_allowed('mailbox_mail_view', function() use ($mailbox_mail){?>
                              <a href="<?= site_url('administrator/mailbox_mail/copy_mail/' .$mailbox_mail->id_mail); ?>" class="label-default"><i class="fa fa-copy"></i> Copy</a>
                              <a href="<?= site_url('administrator/mailbox_mail/view/' . $mailbox_mail->id_mail); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <br/>
                              <?php is_allowed('mailbox_mail_update', function() use ($mailbox_mail){?>
                              <a href="<?= site_url('administrator/mailbox_mail/edit/' . $mailbox_mail->id_mail); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('mailbox_mail_delete', function() use ($mailbox_mail){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/mailbox_mail/delete/' . $mailbox_mail->id_mail); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                              <?php }) ?>
                              <!--<a data-toggle="modal" data-target="#modal-preview<?= $mailbox_mail->id_mail;?>" class="btn btn-default"><i class="fa fa-eye"></i> Preview</a>-->
                              <a target="_blank" href="<?= base_url("apiapp/mailbox/export_pdf_mail?id_mail=".$mailbox_mail->id_mail);?>" class="btn btn-default"><i class="fa fa-eye"></i> Preview PDF</a>

<div class="modal fade" id="modal-signature<?= $mailbox_mail->id_mail;?>">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
         <h4 class="modal-title">List Signature</h4>
         </div>
         <div class="modal-body">
            <table class="table table-bordered table-striped" style="margin-bottom:5px;" cellspacing="0" border="1">
               <?php
               $get_signature = $this->mymodel->withquery("select sign_name, sign_position, sign_signature, sign_order from mailbox_mail_signed where id_mail = '".$mailbox_mail->id_mail."' order by sign_order ASC","result");
                  foreach ($get_signature as $key => $value) {
                     echo "<tr>";
                     echo "<td width='10%'>".($key+1)."</td>";
                     echo "<td width='50%'>".$value->sign_position." <br/>(".$value->sign_name.")</td>";
                     echo "<td width='40%'>";
                     echo (!empty($value->sign_signature)) ? "<img src='".base_url()."uploads/mailbox_mail_signed/".$value->sign_signature."' style='width:150px;height:100px;'/>" : "-";
                     echo "</td>";
                     echo "</tr>";
                  }
               ?>
            </table>
         </div>
         <div class="modal-footer">
         <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modal-receiver<?= $mailbox_mail->id_mail;?>">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
         <h4 class="modal-title">List Receiver</h4>
         </div>
         <div class="modal-body">
            <table class="table table-bordered table-striped" style="margin-bottom:5px;" cellspacing="0" border="1">
               <?php
               $get_receiver = $this->mymodel->withquery("select npp, nama_lengkap, role, is_read, is_important from mailbox_recipient where id_mail = '".$mailbox_mail->id_mail."'","result");
                  foreach ($get_receiver as $key => $value) {
                     echo "<tr>";
                     echo "<td width='10%'>".($key+1)."</td>";
                     echo "<td width='20%'>".$value->npp."</td>";
                     echo "<td width='30%'>".$value->nama_lengkap."</td>";
                     echo "<td width='20%'>";
                     echo (!empty($value->role)) ? $value->role : "-";
                     echo "</td>";
                     echo "<td width='20%'>";
                     echo "<a target='_blank' href='".site_url("apiapp/mailbox/export_pdf_mail?id_mail=".$mailbox_mail->id_mail."&npp=".$value->npp)."' class='btn btn-info'>Preview PDF</a>";
                     echo "</td>";
                     echo "</tr>";
                  }
               ?>
            </table>
         </div>
         <div class="modal-footer">
         <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modal-preview<?= $mailbox_mail->id_mail;?>">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
         <h4 class="modal-title">Preview</h4>
         </div>
         <div class="modal-body">
            <!--<iframe src="<?= base_url("apiapp/mailbox/export_pdf_mail?id_mail=".$mailbox_mail->id_mail);?>" width="100%" height="500px"></iframe>-->
         </div>
         <div class="modal-footer">
         <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($mailbox_mail_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Mailbox Mail data is not available
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
                            <option <?= $this->input->get('f') == 'mailbox_category' ? 'selected' :''; ?> value="mailbox_category">Category</option>
                           <option <?= $this->input->get('f') == 'mailbox_title' ? 'selected' :''; ?> value="mailbox_title">Title</option>
                           <option <?= $this->input->get('f') == 'is_confidential' ? 'selected' :''; ?> value="is_confidential">Is Confidential?</option>
                           <option <?= $this->input->get('f') == 'mail_number' ? 'selected' :''; ?> value="mail_number">Mail Number</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/mailbox_mail');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_mailbox_mail').serialize();

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
               document.location.href = BASE_URL + '/administrator/mailbox_mail/delete?' + serialize_bulk;      
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