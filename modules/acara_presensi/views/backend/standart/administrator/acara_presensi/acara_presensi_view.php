
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Acara Presensi      <small><?= cclang('detail', ['Acara Presensi']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/acara_presensi'); ?>">Acara Presensi</a></li>
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
                     <h3 class="widget-user-username">Acara Presensi</h3>
                     <h5 class="widget-user-desc">Detail Acara Presensi</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_acara_presensi" id="form_acara_presensi" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_presensi->id_presensi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Acara </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_presensi->acara_nama_acara); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">NPP </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_presensi->npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Peserta </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_presensi->peserta); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Role </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_presensi->role); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Lainnya </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_presensi->role_lainnya); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Waktu Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_presensi->waktu_presensi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Waktu Presensi Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_presensi->waktu_presensi_selesai); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('acara_presensi_update', function() use ($acara_presensi){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit acara_presensi (Ctrl+e)" href="<?= site_url('administrator/acara_presensi/edit/'.$acara_presensi->id_presensi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Acara Presensi']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/acara_presensi/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Acara Presensi']); ?></a>
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
