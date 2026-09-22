
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Mailbox Mail Signed      <small><?= cclang('detail', ['Mailbox Mail Signed']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/mailbox_mail_signed'); ?>">Mailbox Mail Signed</a></li>
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
                     <h3 class="widget-user-username">Mailbox Mail Signed</h3>
                     <h5 class="widget-user-desc">Detail Mailbox Mail Signed</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_mailbox_mail_signed" id="form_mailbox_mail_signed" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Mail Signed </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail_signed->id_mail_signed); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mail </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail_signed->mailbox_mail_mailbox_title); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Name </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail_signed->sign_name); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Position </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail_signed->sign_position); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Signature File </label>
                        <div class="col-sm-8">
                             <?php if (is_image($mailbox_mail_signed->sign_signature)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/mailbox_mail_signed/' . $mailbox_mail_signed->sign_signature; ?>">
                                <img src="<?= BASE_URL . 'uploads/mailbox_mail_signed/' . $mailbox_mail_signed->sign_signature; ?>" class="image-responsive" alt="image mailbox_mail_signed" title="sign_signature mailbox_mail_signed" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/mailbox_mail_signed/' . $mailbox_mail_signed->sign_signature; ?>">
                                 <img src="<?= get_icon_file($mailbox_mail_signed->sign_signature); ?>" class="image-responsive" alt="image mailbox_mail_signed" title="sign_signature <?= $mailbox_mail_signed->sign_signature; ?>" width="40px"> 
                               <?= $mailbox_mail_signed->sign_signature ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Order </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail_signed->sign_order); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail_signed->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail_signed->updated_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Deleted At </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail_signed->deleted_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('mailbox_mail_signed_update', function() use ($mailbox_mail_signed){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit mailbox_mail_signed (Ctrl+e)" href="<?= site_url('administrator/mailbox_mail_signed/edit/'.$mailbox_mail_signed->id_mail_signed); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Mailbox Mail Signed']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/mailbox_mail_signed/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Mailbox Mail Signed']); ?></a>
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
