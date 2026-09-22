
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Jadwal Mapel Sd      <small><?= cclang('detail', ['Jadwal Mapel Sd']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/jadwal_mapel_sd'); ?>">Jadwal Mapel Sd</a></li>
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
                     <h3 class="widget-user-username">Jadwal Mapel Sd</h3>
                     <h5 class="widget-user-desc">Detail Jadwal Mapel Sd</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_jadwal_mapel_sd" id="form_jadwal_mapel_sd" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Jadwal </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sd->id_jadwal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jam Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sd->jam_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jam Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sd->jam_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Hari </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sd->hari); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Ruang Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sd->ruang_kelas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sd->kelas_sd_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Mapel </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sd->mata_pelajaran_sd_nama_mapel); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Guru </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sd->guru_sd_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sd->tahun_ajaran_label); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('jadwal_mapel_sd_update', function() use ($jadwal_mapel_sd){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit jadwal_mapel_sd (Ctrl+e)" href="<?= site_url('administrator/jadwal_mapel_sd/edit/'.$jadwal_mapel_sd->id_jadwal); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Jadwal Mapel Sd']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/jadwal_mapel_sd/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Jadwal Mapel Sd']); ?></a>
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
