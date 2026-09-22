
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pengaturan Login Page      <small><?= cclang('detail', ['Pengaturan Login Page']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/apps_login_page'); ?>">Pengaturan Login Page</a></li>
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
                     <h3 class="widget-user-username">Pengaturan Login Page</h3>
                     <h5 class="widget-user-desc">Detail Pengaturan Login Page</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_apps_login_page" id="form_apps_login_page" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Login Page </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_login_page->id_login_page); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File </label>
                        <div class="col-sm-8">
                             <?php if (is_image($apps_login_page->img_file)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/apps_login_page/' . $apps_login_page->img_file; ?>">
                                <img src="<?= BASE_URL . 'uploads/apps_login_page/' . $apps_login_page->img_file; ?>" class="image-responsive" alt="image apps_login_page" title="img_file apps_login_page" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/apps_login_page/' . $apps_login_page->img_file; ?>">
                                 <img src="<?= get_icon_file($apps_login_page->img_file); ?>" class="image-responsive" alt="image apps_login_page" title="img_file <?= $apps_login_page->img_file; ?>" width="40px"> 
                               <?= $apps_login_page->img_file ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Title </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_login_page->title); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Sub Title </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_login_page->sub_title); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Description </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_login_page->description); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                          <?= join_multi_select($apps_login_page->jenjang, '', '', ''); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Role </label>

                        <div class="col-sm-8">
                          <?= join_multi_select($apps_login_page->role, '', '', ''); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Default? </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_login_page->is_default); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Active? </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_login_page->is_active); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_login_page->updated_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated By </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_login_page->aauth_users_email); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_login_page->tanggal_aktif); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_login_page->tanggal_selesai); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('apps_login_page_update', function() use ($apps_login_page){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit apps_login_page (Ctrl+e)" href="<?= site_url('administrator/apps_login_page/edit/'.$apps_login_page->id_login_page); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Apps Login Page']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/apps_login_page/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Apps Login Page']); ?></a>
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
