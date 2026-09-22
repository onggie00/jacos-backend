
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Manajemen Ekskul FT      <small><?= cclang('detail', ['Manajemen Ekskul FT']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/ekskul_manajemen_ft'); ?>">Manajemen Ekskul FT</a></li>
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
                     <h3 class="widget-user-username">Manajemen Ekskul FT</h3>
                     <h5 class="widget-user-desc">Detail Manajemen Ekskul FT</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_ekskul_manajemen_ft" id="form_ekskul_manajemen_ft" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Manajemen </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_manajemen_ft->id_manajemen); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Ekstrakurikuler </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_manajemen_ft->ekskul_nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pembina </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_manajemen_ft->guru_ft_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_manajemen_ft->nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_manajemen_ft->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Email </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_manajemen_ft->email_pelatih); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nomor Telepon </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_manajemen_ft->notelp_pelatih); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_manajemen_ft->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_manajemen_ft->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('ekskul_manajemen_ft_update', function() use ($ekskul_manajemen_ft){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit ekskul_manajemen_ft (Ctrl+e)" href="<?= site_url('administrator/ekskul_manajemen_ft/edit/'.$ekskul_manajemen_ft->id_manajemen); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Ekskul Manajemen Ft']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/ekskul_manajemen_ft/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Ekskul Manajemen Ft']); ?></a>
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
