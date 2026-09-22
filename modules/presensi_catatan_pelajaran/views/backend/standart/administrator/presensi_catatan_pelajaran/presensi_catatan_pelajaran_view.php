
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Catatan Presensi Pelajaran      <small><?= cclang('detail', ['Catatan Presensi Pelajaran']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/presensi_catatan_pelajaran'); ?>">Catatan Presensi Pelajaran</a></li>
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
                     <h3 class="widget-user-username">Catatan Presensi Pelajaran</h3>
                     <h5 class="widget-user-desc">Detail Catatan Presensi Pelajaran</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_presensi_catatan_pelajaran" id="form_presensi_catatan_pelajaran" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Presensi Catatan </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->id_presensi_catatan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">NIS </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->siswa_sd_aktif_nis); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Hari </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->hari); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Waktu </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->tanggal_waktu); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Kehadiran </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->status_hadir); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jam Ke- </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->jam_ke); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->updated_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated By </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_catatan_pelajaran->updated_by); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('presensi_catatan_pelajaran_update', function() use ($presensi_catatan_pelajaran){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit presensi_catatan_pelajaran (Ctrl+e)" href="<?= site_url('administrator/presensi_catatan_pelajaran/edit/'.$presensi_catatan_pelajaran->id_presensi_catatan); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Presensi Catatan Pelajaran']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/presensi_catatan_pelajaran/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Presensi Catatan Pelajaran']); ?></a>
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
