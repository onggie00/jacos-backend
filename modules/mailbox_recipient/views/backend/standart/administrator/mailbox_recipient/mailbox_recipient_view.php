
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Mailbox Recipient      <small><?= cclang('detail', ['Mailbox Recipient']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/mailbox_recipient'); ?>">Mailbox Recipient</a></li>
      <li class="active"><?= cclang('detail'); ?></li>
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
                    
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/view.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username">Mailbox Recipient</h3>
                     <h5 class="widget-user-desc">Detail Mailbox Recipient</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_mailbox_recipient" id="form_mailbox_recipient" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_recipient->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mail </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_recipient->mailbox_mail_mailbox_title); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">NPP </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_recipient->npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Name </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_recipient->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Role </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_recipient->role); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Is Read? </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_recipient->is_read); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Marked As Important? </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_recipient->is_important); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_recipient->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_recipient->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('mailbox_recipient_update', function() use ($mailbox_recipient){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit mailbox_recipient (Ctrl+e)" href="<?= site_url('administrator/mailbox_recipient/edit/'.$mailbox_recipient->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Mailbox Recipient']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/mailbox_recipient/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Mailbox Recipient']); ?></a>
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
