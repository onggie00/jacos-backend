
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Siswa Sma Aktif      <small><?= cclang('detail', ['Siswa Sma Aktif']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/siswa_sma_aktif'); ?>">Siswa Sma Aktif</a></li>
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
                     <h3 class="widget-user-username">Siswa Sma Aktif</h3>
                     <h5 class="widget-user-desc">Detail Siswa Sma Aktif</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_siswa_sma_aktif" id="form_siswa_sma_aktif" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa Sma Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->id_siswa_sma_aktif); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nis </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->nis); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->tahun_ajaran_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->kelas_sma_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa Sma </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->siswa_sma_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kewarganegaraan </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->kewarganegaraan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nik </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->nik); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Golongan Darah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->golongan_darah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Telp </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->telp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pendidikan Ayah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->pendidikan_ayah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pendidikan Ibu </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->pendidikan_ibu); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penghasilan Ayah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->penghasilan_ayah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penghasilan Ibu </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->penghasilan_ibu); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Lahir Ayah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->tgl_lahir_ayah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Lahir Ibu </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif->tgl_lahir_ibu); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('siswa_sma_aktif_update', function() use ($siswa_sma_aktif){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit siswa_sma_aktif (Ctrl+e)" href="<?= site_url('administrator/siswa_sma_aktif/edit/'.$siswa_sma_aktif->id_siswa_sma_aktif); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Siswa Sma Aktif']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/siswa_sma_aktif/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Siswa Sma Aktif']); ?></a>
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
