
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Laporan Kinerja Guru SD      <small><?= cclang('detail', ['Laporan Kinerja Guru SD']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/laporan_kinerja_guru_sd'); ?>">Laporan Kinerja Guru SD</a></li>
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
                     <h3 class="widget-user-username">Laporan Kinerja Guru SD</h3>
                     <h5 class="widget-user-desc">Detail Laporan Kinerja Guru SD</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_laporan_kinerja_guru_sd" id="form_laporan_kinerja_guru_sd" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Laporan </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->id_laporan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Guru </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->guru_sd_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Unit Kerja </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->unit_kerja); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mata Pelajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->mata_pelajaran); ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Umur </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->umur); ?>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Masa Kerja </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->masa_kerja); ?>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Golongan </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->golongan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kompetensi Pedagogik </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->kompetensi_pedagogik); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kompetensi Profesional </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->kompetensi_profesional); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kompetensi Kepribadian </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->kompetensi_kepribadian); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kompetensi Sosial </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->kompetensi_sosial); ?>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Leadership </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->leadership); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penilaian Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->nilai_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penilaian Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->nilai_presensi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Rank </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->rank); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->tahun_ajaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_sd->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('laporan_kinerja_guru_sd_update', function() use ($laporan_kinerja_guru_sd){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit laporan_kinerja_guru_sd (Ctrl+e)" href="<?= site_url('administrator/laporan_kinerja_guru_sd/edit/'.$laporan_kinerja_guru_sd->id_laporan); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Laporan Kinerja Guru Sd']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/laporan_kinerja_guru_sd/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Laporan Kinerja Guru Sd']); ?></a>
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
