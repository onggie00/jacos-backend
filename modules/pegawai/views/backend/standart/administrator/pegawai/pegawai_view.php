
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pegawai      <small><?= cclang('detail', ['Pegawai']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pegawai'); ?>">Pegawai</a></li>
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
                     <h3 class="widget-user-username">Pegawai</h3>
                     <h5 class="widget-user-desc">Detail Pegawai</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pegawai" id="form_pegawai" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Pegawai </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->id_pegawai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nik </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->nik); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nuptk </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->nuptk); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Npp </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kk </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->kk); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Npwp </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->npwp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Alamat </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->alamat); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Agama </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->agama_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Kelamin </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->jenis_kelamin); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Menikah </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->status_menikah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jumlah Anak </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->jumlah_anak); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">No Telp </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->no_telp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Email </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->email); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Posisi </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->posisi_pegawai_nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Unit </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->unit); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Kepegawaian </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->status_kepegawaian); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Informasi Kepala Pimpinan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->informasi_kepala_pimpinan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Emp Code </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->emp_code); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Token </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->token); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Token Expired </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->token_expired); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Email Ms Office </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->email_ms_office); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">No Kk </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->no_kk); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tempat Lahir </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->tempat_lahir); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Lahir </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai->tgl_lahir); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Foto Profil </label>
                        <div class="col-sm-8">
                             <?php if (is_image($pegawai->foto_profil)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/pegawai/' . $pegawai->foto_profil; ?>">
                                <img src="<?= BASE_URL . 'uploads/pegawai/' . $pegawai->foto_profil; ?>" class="image-responsive" alt="image pegawai" title="foto_profil pegawai" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/pegawai/' . $pegawai->foto_profil; ?>">
                                 <img src="<?= get_icon_file($pegawai->foto_profil); ?>" class="image-responsive" alt="image pegawai" title="foto_profil <?= $pegawai->foto_profil; ?>" width="40px"> 
                               <?= $pegawai->foto_profil ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Slip Gaji </label>
                        <div class="col-sm-8">
                             <?php if (is_image($pegawai->slip_gaji)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/pegawai/' . $pegawai->slip_gaji; ?>">
                                <img src="<?= BASE_URL . 'uploads/pegawai/' . $pegawai->slip_gaji; ?>" class="image-responsive" alt="image pegawai" title="slip_gaji pegawai" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/pegawai/' . $pegawai->slip_gaji; ?>">
                                 <img src="<?= get_icon_file($pegawai->slip_gaji); ?>" class="image-responsive" alt="image pegawai" title="slip_gaji <?= $pegawai->slip_gaji; ?>" width="40px"> 
                               <?= $pegawai->slip_gaji ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                      
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pegawai_update', function() use ($pegawai){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pegawai (Ctrl+e)" href="<?= site_url('administrator/pegawai/edit/'.$pegawai->id_pegawai); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pegawai']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pegawai/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pegawai']); ?></a>
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
