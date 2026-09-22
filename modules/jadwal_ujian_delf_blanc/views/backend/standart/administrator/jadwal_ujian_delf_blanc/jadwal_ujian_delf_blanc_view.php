
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Jadwal Ujian Delf Blanc      <small><?= cclang('detail', ['Jadwal Ujian Delf Blanc']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/jadwal_ujian_delf_blanc'); ?>">Jadwal Ujian Delf Blanc</a></li>
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
                     <h3 class="widget-user-username">Jadwal Ujian Delf Blanc</h3>
                     <h5 class="widget-user-desc">Detail Jadwal Ujian Delf Blanc</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_jadwal_ujian_delf_blanc" id="form_jadwal_ujian_delf_blanc" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Jadwal </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf_blanc->id_jadwal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Mapel </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf_blanc->mata_pelajaran_ft_nama_mapel); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Jenis Ujian </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf_blanc->jenis_ujian_nama_ujian); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tingkatan </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf_blanc->tingkatan_ft_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf_blanc->tanggal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Ruang </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf_blanc->ruang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Hari </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf_blanc->hari); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jam Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf_blanc->jam_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jam Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf_blanc->jam_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf_blanc->tahun_ajaran_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Semester </label>

                        <div class="col-sm-8">
                           <?= _ent($jadwal_ujian_delf_blanc->semester); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('jadwal_ujian_delf_blanc_update', function() use ($jadwal_ujian_delf_blanc){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit jadwal_ujian_delf_blanc (Ctrl+e)" href="<?= site_url('administrator/jadwal_ujian_delf_blanc/edit/'.$jadwal_ujian_delf_blanc->id_jadwal); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Jadwal Ujian Delf Blanc']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/jadwal_ujian_delf_blanc/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Jadwal Ujian Delf Blanc']); ?></a>
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
