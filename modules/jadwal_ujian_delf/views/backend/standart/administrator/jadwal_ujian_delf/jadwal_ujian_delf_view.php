
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Jadwal Ujian Delf      <small><?= cclang('detail', ['Jadwal Ujian Delf']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/jadwal_ujian_delf'); ?>">Jadwal Ujian Delf</a></li>
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
                     <h3 class="widget-user-username">Jadwal Ujian Delf</h3>
                     <h5 class="widget-user-desc">Detail Jadwal Ujian Delf</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_jadwal_ujian_delf" id="form_jadwal_ujian_delf" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Jadwal </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf->id_jadwal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Mapel </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf->mata_pelajaran_ft_nama_mapel); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Jenis Ujian </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf->jenis_ujian_nama_ujian); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tingkatan </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf->tingkatan_ft_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf->tanggal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Ruang </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf->ruang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Hari </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf->hari); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jam Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf->jam_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jam Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf->jam_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf->tahun_ajaran_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Semester </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf->semester); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('jadwal_ujian_delf_update', function() use ($jadwal_ujian_delf){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit jadwal_ujian_delf (Ctrl+e)" href="<?= site_url('administrator/jadwal_ujian_delf/edit/'.$jadwal_ujian_delf->id_jadwal); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Jadwal Ujian Delf']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/jadwal_ujian_delf/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Jadwal Ujian Delf']); ?></a>
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
