
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Presensi Tadarus      <small><?= cclang('detail', ['Presensi Tadarus']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/presensi_tadarus'); ?>">Presensi Tadarus</a></li>
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
                     <h3 class="widget-user-username">Presensi Tadarus</h3>
                     <h5 class="widget-user-desc">Detail Presensi Tadarus</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_presensi_tadarus" id="form_presensi_tadarus" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Presensi Tadarus </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->id_presensi_tadarus); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Hari </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->hari); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->tanggal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">NIS Siswa </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->siswa_sma_aktif_id_siswa_sma_aktif); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->kelas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Hadir </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->status_hadir); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kehadiran </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->kehadiran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kelengkapan </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->kelengkapan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Adab </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->adab); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keaktifan </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->keaktifan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Nilai </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->total_nilai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Diupdate Oleh </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->updated_by); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->updated_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Deleted At </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_tadarus->deleted_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('presensi_tadarus_update', function() use ($presensi_tadarus){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit presensi_tadarus (Ctrl+e)" href="<?= site_url('administrator/presensi_tadarus/edit/'.$presensi_tadarus->id_presensi_tadarus); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Presensi Tadarus']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/presensi_tadarus/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Presensi Tadarus']); ?></a>
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
