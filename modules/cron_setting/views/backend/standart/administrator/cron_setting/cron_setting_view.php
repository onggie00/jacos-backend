
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Cron Setting      <small><?= cclang('detail', ['Cron Setting']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/cron_setting'); ?>">Cron Setting</a></li>
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
                     <h3 class="widget-user-username">Cron Setting</h3>
                     <h5 class="widget-user-desc">Detail Cron Setting</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_cron_setting" id="form_cron_setting" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Cron Setting </label>

                        <div class="col-sm-8">
                           <?= _ent($cron_setting->id_cron_setting); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama CRON </label>

                        <div class="col-sm-8">
                           <?= _ent($cron_setting->nama_cron); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($cron_setting->siswa_sd_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($cron_setting->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($cron_setting->kelas_sd_nama_kelas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Aktif? </label>

                        <div class="col-sm-8">
                           <?= _ent($cron_setting->is_active); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Nonaktifkan Ulang </label>

                        <div class="col-sm-8">
                           <?= _ent($cron_setting->date_deactivate); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Pengaktifan Ulang </label>

                        <div class="col-sm-8">
                           <?= _ent($cron_setting->date_reactivate); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Update </label>

                        <div class="col-sm-8">
                           <?= _ent($cron_setting->updated_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Diupdate Oleh? </label>

                        <div class="col-sm-8">
                           <?= _ent($cron_setting->aauth_users_email); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('cron_setting_update', function() use ($cron_setting){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit cron_setting (Ctrl+e)" href="<?= site_url('administrator/cron_setting/edit/'.$cron_setting->id_cron_setting); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Cron Setting']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/cron_setting/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Cron Setting']); ?></a>
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
