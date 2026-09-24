
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Siswa TK Aktif      <small><?= cclang('detail', ['Siswa TK Aktif']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/siswa_tk_aktif'); ?>">Siswa TK Aktif</a></li>
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
                     <h3 class="widget-user-username">Siswa TK Aktif</h3>
                     <h5 class="widget-user-desc">Detail Siswa TK Aktif</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_siswa_tk_aktif" id="form_siswa_tk_aktif" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa TK Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->id_siswa_tk_aktif); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nis </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->nis); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->kelas_tk_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa TK </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->siswa_tk_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->tahun_ajaran_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kewarganegaraan </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->kewarganegaraan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nik </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->nik); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Golongan Darah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->golongan_darah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Telp </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->telp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pendidikan Ayah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->pendidikan_ayah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pendidikan Ibu </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->pendidikan_ibu); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penghasilan Ayah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->penghasilan_ayah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penghasilan Ibu </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->penghasilan_ibu); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Lahir Ayah </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->tgl_lahir_ayah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Lahir Ibu </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_tk_aktif->tgl_lahir_ibu); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('siswa_tk_aktif_update', function() use ($siswa_tk_aktif){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit siswa_tk_aktif (Ctrl+e)" href="<?= site_url('administrator/siswa_tk_aktif/edit/'.$siswa_tk_aktif->id_siswa_tk_aktif); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Siswa TK Aktif']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/siswa_tk_aktif/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Siswa TK Aktif']); ?></a>
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
