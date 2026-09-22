
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pimpinan Sma      <small><?= cclang('detail', ['Pimpinan Sma']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pimpinan_sma'); ?>">Pimpinan Sma</a></li>
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
                     <h3 class="widget-user-username">Pimpinan Sma</h3>
                     <h5 class="widget-user-desc">Detail Pimpinan Sma</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pimpinan_sma" id="form_pimpinan_sma" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Pimpinan </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->id_pimpinan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nik </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->nik); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nuptk </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->nuptk); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Npp </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Npwp </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->npwp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Alamat </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->alamat); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Agama </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->agama_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Kelamin </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->jenis_kelamin); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Menikah </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->status_menikah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jumlah Anak </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->jumlah_anak); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">No Telp </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->no_telp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Email </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->email); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Posisi </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->posisi_pimpinan_nama_posisi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Satuan Pendidikan </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->satuan_pendidikan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Unit </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->unit); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Mapel </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->mata_pelajaran_sma_nama_mapel); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Kepegawaian </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->status_kepegawaian); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Informasi Kepala Pimpinan </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->informasi_kepala_pimpinan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Emp Code </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->emp_code); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Token </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->token); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Token Expired </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->token_expired); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Email Ms Office </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->email_ms_office); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Foto Profil </label>
                        <div class="col-sm-8">
                             <?php if (is_image($pimpinan_sma->foto_profil)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/pimpinan_sma/' . $pimpinan_sma->foto_profil; ?>">
                                <img src="<?= BASE_URL . 'uploads/pimpinan_sma/' . $pimpinan_sma->foto_profil; ?>" class="image-responsive" alt="image pimpinan_sma" title="foto_profil pimpinan_sma" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/pimpinan_sma/' . $pimpinan_sma->foto_profil; ?>">
                                 <img src="<?= get_icon_file($pimpinan_sma->foto_profil); ?>" class="image-responsive" alt="image pimpinan_sma" title="foto_profil <?= $pimpinan_sma->foto_profil; ?>" width="40px"> 
                               <?= $pimpinan_sma->foto_profil ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">No Kk </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->no_kk); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tempat Lahir </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->tempat_lahir); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Lahir </label>

                        <div class="col-sm-8">
                           <?= _ent($pimpinan_sma->tgl_lahir); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Slip Gaji </label>
                        <div class="col-sm-8">
                             <?php if (is_image($pimpinan_sma->slip_gaji)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/pimpinan_sma/' . $pimpinan_sma->slip_gaji; ?>">
                                <img src="<?= BASE_URL . 'uploads/pimpinan_sma/' . $pimpinan_sma->slip_gaji; ?>" class="image-responsive" alt="image pimpinan_sma" title="slip_gaji pimpinan_sma" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/pimpinan_sma/' . $pimpinan_sma->slip_gaji; ?>">
                                 <img src="<?= get_icon_file($pimpinan_sma->slip_gaji); ?>" class="image-responsive" alt="image pimpinan_sma" title="slip_gaji <?= $pimpinan_sma->slip_gaji; ?>" width="40px"> 
                               <?= $pimpinan_sma->slip_gaji ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                      
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pimpinan_sma_update', function() use ($pimpinan_sma){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pimpinan_sma (Ctrl+e)" href="<?= site_url('administrator/pimpinan_sma/edit/'.$pimpinan_sma->id_pimpinan); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pimpinan Sma']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pimpinan_sma/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pimpinan Sma']); ?></a>
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
