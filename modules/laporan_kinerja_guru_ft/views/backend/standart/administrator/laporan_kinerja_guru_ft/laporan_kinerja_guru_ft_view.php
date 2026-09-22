
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Laporan Kinerja Guru FT      <small><?= cclang('detail', ['Laporan Kinerja Guru FT']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/laporan_kinerja_guru_ft'); ?>">Laporan Kinerja Guru FT</a></li>
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
                     <h3 class="widget-user-username">Laporan Kinerja Guru FT</h3>
                     <h5 class="widget-user-desc">Detail Laporan Kinerja Guru FT</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_laporan_kinerja_guru_ft" id="form_laporan_kinerja_guru_ft" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Laporan </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->id_laporan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Guru </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->guru_ft_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Unit Kerja </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->unit_kerja); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mata Pelajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->mata_pelajaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penilaian Pimpinan </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->nilai_pimpinan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penilaian Sejawat </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->nilai_sejawat); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penilaian Siswa </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->nilai_siswa); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penilaian Sendiri </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->nilai_sendiri); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penilaian Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->nilai_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penilaian Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->nilai_presensi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Rank </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->rank); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->tahun_ajaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_kinerja_guru_ft->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('laporan_kinerja_guru_ft_update', function() use ($laporan_kinerja_guru_ft){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit laporan_kinerja_guru_ft (Ctrl+e)" href="<?= site_url('administrator/laporan_kinerja_guru_ft/edit/'.$laporan_kinerja_guru_ft->id_laporan); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Laporan Kinerja Guru Ft']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/laporan_kinerja_guru_ft/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Laporan Kinerja Guru Ft']); ?></a>
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
