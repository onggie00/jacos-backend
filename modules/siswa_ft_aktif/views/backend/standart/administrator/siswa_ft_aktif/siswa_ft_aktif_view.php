
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Siswa Ft Aktif      <small><?= cclang('detail', ['Siswa Ft Aktif']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/siswa_ft_aktif'); ?>">Siswa Ft Aktif</a></li>
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
                     <h3 class="widget-user-username">Siswa Ft Aktif</h3>
                     <h5 class="widget-user-desc">Detail Siswa Ft Aktif</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_siswa_ft_aktif" id="form_siswa_ft_aktif" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa Ft Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->id_siswa_ft_aktif); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nis </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->nis); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->tahun_ajaran_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->kelas_sma_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa Ft </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->id_siswa_ft); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kewarganegaraan </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->kewarganegaraan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nik </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->nik); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Golongan Darah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->golongan_darah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Telp </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->telp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pendidikan Ayah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->pendidikan_ayah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pendidikan Ibu </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->pendidikan_ibu); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penghasilan Ayah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->penghasilan_ayah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penghasilan Ibu </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->penghasilan_ibu); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Lahir Ayah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->tgl_lahir_ayah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Lahir Ibu </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_ft_aktif->tgl_lahir_ibu); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('siswa_ft_aktif_update', function() use ($siswa_ft_aktif){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit siswa_ft_aktif (Ctrl+e)" href="<?= site_url('administrator/siswa_ft_aktif/edit/'.$siswa_ft_aktif->id_siswa_ft_aktif); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Siswa Ft Aktif']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/siswa_ft_aktif/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Siswa Ft Aktif']); ?></a>
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
