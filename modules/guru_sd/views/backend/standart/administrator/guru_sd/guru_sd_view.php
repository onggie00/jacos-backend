
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Guru Sd      <small><?= cclang('detail', ['Guru Sd']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/guru_sd'); ?>">Guru Sd</a></li>
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
                     <h3 class="widget-user-username">Guru Sd</h3>
                     <h5 class="widget-user-desc">Detail Guru Sd</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_guru_sd" id="form_guru_sd" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Guru </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->id_guru); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nik </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->nik); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nuptk </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->nuptk); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Npp </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Npwp </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->npwp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Alamat </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->alamat); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Agama </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->agama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Kelamin </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->jenis_kelamin); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Menikah </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->status_menikah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jumlah Anak </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->jumlah_anak); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">No Telp </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->no_telp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Email </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->email); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Posisi </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->posisi_guru_nama_posisi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Satuan Pendidikan </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->satuan_pendidikan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Unit </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->unit); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Mapel </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->mata_pelajaran_sd_nama_mapel); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Kepegawaian </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->status_kepegawaian); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Informasi Kepala Pimpinan </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->informasi_kepala_pimpinan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Emp Code </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->emp_code); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Token </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->token); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Token Expired </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->token_expired); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Email Ms Office </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->email_ms_office); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Foto Profil </label>
                        <div class="col-sm-8">
                             <?php if (is_image($guru_sd->foto_profil)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/guru_sd/' . $guru_sd->foto_profil; ?>">
                                <img src="<?= BASE_URL . 'uploads/guru_sd/' . $guru_sd->foto_profil; ?>" class="image-responsive" alt="image guru_sd" title="foto_profil guru_sd" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/guru_sd/' . $guru_sd->foto_profil; ?>">
                                 <img src="<?= get_icon_file($guru_sd->foto_profil); ?>" class="image-responsive" alt="image guru_sd" title="foto_profil <?= $guru_sd->foto_profil; ?>" width="40px"> 
                               <?= $guru_sd->foto_profil ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">No Kk </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->no_kk); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tempat Lahir </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->tempat_lahir); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Lahir </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd->tgl_lahir); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Slip Gaji </label>
                        <div class="col-sm-8">
                             <?php if (is_image($guru_sd->slip_gaji)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/guru_sd/' . $guru_sd->slip_gaji; ?>">
                                <img src="<?= BASE_URL . 'uploads/guru_sd/' . $guru_sd->slip_gaji; ?>" class="image-responsive" alt="image guru_sd" title="slip_gaji guru_sd" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/guru_sd/' . $guru_sd->slip_gaji; ?>">
                                 <img src="<?= get_icon_file($guru_sd->slip_gaji); ?>" class="image-responsive" alt="image guru_sd" title="slip_gaji <?= $guru_sd->slip_gaji; ?>" width="40px"> 
                               <?= $guru_sd->slip_gaji ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                      
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('guru_sd_update', function() use ($guru_sd){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit guru_sd (Ctrl+e)" href="<?= site_url('administrator/guru_sd/edit/'.$guru_sd->id_guru); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Guru Sd']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/guru_sd/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Guru Sd']); ?></a>
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
