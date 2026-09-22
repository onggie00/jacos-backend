
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Guru Smp      <small><?= cclang('detail', ['Guru Smp']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/guru_smp'); ?>">Guru Smp</a></li>
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
                     <h3 class="widget-user-username">Guru Smp</h3>
                     <h5 class="widget-user-desc">Detail Guru Smp</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_guru_smp" id="form_guru_smp" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Guru </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->id_guru); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nik </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->nik); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nuptk </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->nuptk); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Npp </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Npwp </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->npwp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Alamat </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->alamat); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Agama </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->agama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Kelamin </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->jenis_kelamin); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Menikah </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->status_menikah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jumlah Anak </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->jumlah_anak); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">No Telp </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->no_telp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Email </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->email); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Posisi </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->posisi_guru_nama_posisi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Satuan Pendidikan </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->satuan_pendidikan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Unit </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->unit); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Mapel </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->mata_pelajaran_smp_nama_mapel); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Kepegawaian </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->status_kepegawaian); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Informasi Kepala Pimpinan </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->informasi_kepala_pimpinan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Emp Code </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->emp_code); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Token </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->token); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Token Expired </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->token_expired); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Email Ms Office </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->email_ms_office); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Foto Profil </label>
                        <div class="col-sm-8">
                             <?php if (is_image($guru_smp->foto_profil)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/guru_smp/' . $guru_smp->foto_profil; ?>">
                                <img src="<?= BASE_URL . 'uploads/guru_smp/' . $guru_smp->foto_profil; ?>" class="image-responsive" alt="image guru_smp" title="foto_profil guru_smp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/guru_smp/' . $guru_smp->foto_profil; ?>">
                                 <img src="<?= get_icon_file($guru_smp->foto_profil); ?>" class="image-responsive" alt="image guru_smp" title="foto_profil <?= $guru_smp->foto_profil; ?>" width="40px"> 
                               <?= $guru_smp->foto_profil ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">No Kk </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->no_kk); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tempat Lahir </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->tempat_lahir); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Lahir </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_smp->tgl_lahir); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Slip Gaji </label>
                        <div class="col-sm-8">
                             <?php if (is_image($guru_smp->slip_gaji)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/guru_smp/' . $guru_smp->slip_gaji; ?>">
                                <img src="<?= BASE_URL . 'uploads/guru_smp/' . $guru_smp->slip_gaji; ?>" class="image-responsive" alt="image guru_smp" title="slip_gaji guru_smp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/guru_smp/' . $guru_smp->slip_gaji; ?>">
                                 <img src="<?= get_icon_file($guru_smp->slip_gaji); ?>" class="image-responsive" alt="image guru_smp" title="slip_gaji <?= $guru_smp->slip_gaji; ?>" width="40px"> 
                               <?= $guru_smp->slip_gaji ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                      
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('guru_smp_update', function() use ($guru_smp){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit guru_smp (Ctrl+e)" href="<?= site_url('administrator/guru_smp/edit/'.$guru_smp->id_guru); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Guru Smp']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/guru_smp/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Guru Smp']); ?></a>
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
