
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Jadwal Mapel Sma      <small><?= cclang('detail', ['Jadwal Mapel Sma']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/jadwal_mapel_sma'); ?>">Jadwal Mapel Sma</a></li>
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
                     <h3 class="widget-user-username">Jadwal Mapel Sma</h3>
                     <h5 class="widget-user-desc">Detail Jadwal Mapel Sma</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_jadwal_mapel_sma" id="form_jadwal_mapel_sma" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Jadwal </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sma->id_jadwal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jam Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sma->jam_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jam Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sma->jam_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Hari </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sma->hari); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sma->kelas_sma_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Mapel </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sma->mata_pelajaran_sma_nama_mapel); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Guru </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sma->guru_sma_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Ruang Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sma->ruang_kelas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_mapel_sma->tahun_ajaran_label); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('jadwal_mapel_sma_update', function() use ($jadwal_mapel_sma){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit jadwal_mapel_sma (Ctrl+e)" href="<?= site_url('administrator/jadwal_mapel_sma/edit/'.$jadwal_mapel_sma->id_jadwal); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Jadwal Mapel Sma']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/jadwal_mapel_sma/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Jadwal Mapel Sma']); ?></a>
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
