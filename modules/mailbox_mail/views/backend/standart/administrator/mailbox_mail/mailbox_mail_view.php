
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Mailbox Mail      <small><?= cclang('detail', ['Mailbox Mail']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/mailbox_mail'); ?>">Mailbox Mail</a></li>
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
                     <h3 class="widget-user-username">Mailbox Mail</h3>
                     <h5 class="widget-user-desc">Detail Mailbox Mail</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_mailbox_mail" id="form_mailbox_mail" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Mail </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->id_mail); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Category </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->mailbox_category_category); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Title </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->mailbox_title); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Description </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->mailbox_description); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Is Confidential? </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->is_confidential); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mail Number </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->mail_number); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mail Content </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->mail_content); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mail Address </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->mail_address); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mail Date </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->mail_date); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mail Time </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->mail_time); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Mail Header (KOP) </label>
                        <div class="col-sm-8">
                             <?php if (is_image($mailbox_mail->mail_header_file)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/mailbox_mail/' . $mailbox_mail->mail_header_file; ?>">
                                <img src="<?= BASE_URL . 'uploads/mailbox_mail/' . $mailbox_mail->mail_header_file; ?>" class="image-responsive" alt="image mailbox_mail" title="mail_header_file mailbox_mail" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/mailbox_mail/' . $mailbox_mail->mail_header_file; ?>">
                                 <img src="<?= get_icon_file($mailbox_mail->mail_header_file); ?>" class="image-responsive" alt="image mailbox_mail" title="mail_header_file <?= $mailbox_mail->mail_header_file; ?>" width="40px"> 
                               <?= $mailbox_mail->mail_header_file ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->updated_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Deleted At </label>

                        <div class="col-sm-8">
                           <?= _ent($mailbox_mail->deleted_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('mailbox_mail_update', function() use ($mailbox_mail){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit mailbox_mail (Ctrl+e)" href="<?= site_url('administrator/mailbox_mail/edit/'.$mailbox_mail->id_mail); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Mailbox Mail']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/mailbox_mail/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Mailbox Mail']); ?></a>
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
