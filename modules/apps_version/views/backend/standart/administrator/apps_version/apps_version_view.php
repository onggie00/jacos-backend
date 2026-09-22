
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      App Version      <small><?= cclang('detail', ['App Version']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/apps_version'); ?>">App Version</a></li>
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
                     <h3 class="widget-user-username">App Version</h3>
                     <h5 class="widget-user-desc">Detail App Version</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_apps_version" id="form_apps_version" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_version->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Android </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_version->android_ver); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">IOS </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_version->ios_ver); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Release Note </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_version->release_note); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Versi Yang Aktif? </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_version->is_active_ver); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Buat </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_version->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Update </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_version->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('apps_version_update', function() use ($apps_version){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit apps_version (Ctrl+e)" href="<?= site_url('administrator/apps_version/edit/'.$apps_version->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Apps Version']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/apps_version/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Apps Version']); ?></a>
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
