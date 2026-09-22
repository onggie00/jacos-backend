
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Laporan Kinerja Pimpinan      <small><?= cclang('detail', ['Laporan Kinerja Pimpinan']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/laporan_kinerja_pimpinan'); ?>">Laporan Kinerja Pimpinan</a></li>
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
                     <h3 class="widget-user-username">Laporan Kinerja Pimpinan</h3>
                     <h5 class="widget-user-desc">Detail Laporan Kinerja Pimpinan</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_laporan_kinerja_pimpinan" id="form_laporan_kinerja_pimpinan" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Laporan </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->id_laporan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pimpinan </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->pimpinan_sd_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Unit </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jabatan </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->jabatan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Unit Kerja </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->unit_kerja); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Umur </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->umur); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Masa Kerja </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->masa_kerja); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Golongan </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->golongan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kepribadian Sosial </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->kepribadian_sosial); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Leadership </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->leadership); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pengembangan Sekolah </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->pengembangan_sekolah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Bidang Tugas Wakil Akademik / Kesiswaan </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->bidang_tugas_wakil_akademik_kesiswaan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Skor </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->total_skor); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Rank </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->rank); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->tahun_ajaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_pimpinan->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('laporan_kinerja_pimpinan_update', function() use ($laporan_kinerja_pimpinan){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit laporan_kinerja_pimpinan (Ctrl+e)" href="<?= site_url('administrator/laporan_kinerja_pimpinan/edit/'.$laporan_kinerja_pimpinan->id_laporan); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Laporan Kinerja Pimpinan']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/laporan_kinerja_pimpinan/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Laporan Kinerja Pimpinan']); ?></a>
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
