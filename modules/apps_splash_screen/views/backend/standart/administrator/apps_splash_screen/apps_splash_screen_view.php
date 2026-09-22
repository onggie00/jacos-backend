
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pengaturan Splash Screen      <small><?= cclang('detail', ['Pengaturan Splash Screen']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/apps_splash_screen'); ?>">Pengaturan Splash Screen</a></li>
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
                     <h3 class="widget-user-username">Pengaturan Splash Screen</h3>
                     <h5 class="widget-user-desc">Detail Pengaturan Splash Screen</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_apps_splash_screen" id="form_apps_splash_screen" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Splash Screen </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_splash_screen->id_splash_screen); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File </label>
                        <div class="col-sm-8">
                             <?php if (is_image($apps_splash_screen->img_file)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/apps_splash_screen/' . $apps_splash_screen->img_file; ?>">
                                <img src="<?= BASE_URL . 'uploads/apps_splash_screen/' . $apps_splash_screen->img_file; ?>" class="image-responsive" alt="image apps_splash_screen" title="img_file apps_splash_screen" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/apps_splash_screen/' . $apps_splash_screen->img_file; ?>">
                                 <img src="<?= get_icon_file($apps_splash_screen->img_file); ?>" class="image-responsive" alt="image apps_splash_screen" title="img_file <?= $apps_splash_screen->img_file; ?>" width="40px"> 
                               <?= $apps_splash_screen->img_file ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Title </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_splash_screen->title); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Description </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_splash_screen->description); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                          <?= join_multi_select($apps_splash_screen->jenjang, '', '', ''); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Role </label>

                        <div class="col-sm-8">
                          <?= join_multi_select($apps_splash_screen->role, '', '', ''); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Default? </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_splash_screen->is_default); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Active? </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_splash_screen->is_active); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_splash_screen->updated_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated By </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_splash_screen->aauth_users_email); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_splash_screen->tanggal_aktif); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_splash_screen->tanggal_selesai); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('apps_splash_screen_update', function() use ($apps_splash_screen){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit apps_splash_screen (Ctrl+e)" href="<?= site_url('administrator/apps_splash_screen/edit/'.$apps_splash_screen->id_splash_screen); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Apps Splash Screen']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/apps_splash_screen/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Apps Splash Screen']); ?></a>
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
