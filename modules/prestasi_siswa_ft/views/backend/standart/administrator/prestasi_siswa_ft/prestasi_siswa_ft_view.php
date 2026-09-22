
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Prestasi Siswa FT      <small><?= cclang('detail', ['Prestasi Siswa FT']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/prestasi_siswa_ft'); ?>">Prestasi Siswa FT</a></li>
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
                     <h3 class="widget-user-username">Prestasi Siswa FT</h3>
                     <h5 class="widget-user-desc">Detail Prestasi Siswa FT</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_prestasi_siswa_ft" id="form_prestasi_siswa_ft" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_ft->id_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_ft->nama_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Raih </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_ft->tgl_raih); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Juara </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_ft->juara); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Prestasi </label>
                        <div class="col-sm-8">
                             <?php if (is_image($prestasi_siswa_ft->file_prestasi)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft->file_prestasi; ?>">
                                <img src="<?= BASE_URL . 'uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft->file_prestasi; ?>" class="image-responsive" alt="image prestasi_siswa_ft" title="file_prestasi prestasi_siswa_ft" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/prestasi_siswa_ft/' . $prestasi_siswa_ft->file_prestasi; ?>">
                                 <img src="<?= get_icon_file($prestasi_siswa_ft->file_prestasi); ?>" class="image-responsive" alt="image prestasi_siswa_ft" title="file_prestasi <?= $prestasi_siswa_ft->file_prestasi; ?>" width="40px"> 
                               <?= $prestasi_siswa_ft->file_prestasi ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Foto </label>
                        <div class="col-sm-8">
                             <?php if (is_image($prestasi_siswa_ft->foto_prestasi)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft->foto_prestasi; ?>">
                                <img src="<?= BASE_URL . 'uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft->foto_prestasi; ?>" class="image-responsive" alt="image prestasi_siswa_ft" title="foto_prestasi prestasi_siswa_ft" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/prestasi_siswa_ft/' . $prestasi_siswa_ft->foto_prestasi; ?>">
                                 <img src="<?= get_icon_file($prestasi_siswa_ft->foto_prestasi); ?>" class="image-responsive" alt="image prestasi_siswa_ft" title="foto_prestasi <?= $prestasi_siswa_ft->foto_prestasi; ?>" width="40px"> 
                               <?= $prestasi_siswa_ft->foto_prestasi ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_ft->siswa_ft_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tingkatan Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_ft->jenis_prestasi_jenis_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Konten </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_ft->konten); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Posting </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_ft->tanggal_posting); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Disetujui? </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_ft->is_approved); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('prestasi_siswa_ft_update', function() use ($prestasi_siswa_ft){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit prestasi_siswa_ft (Ctrl+e)" href="<?= site_url('administrator/prestasi_siswa_ft/edit/'.$prestasi_siswa_ft->id_prestasi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Prestasi Siswa Ft']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/prestasi_siswa_ft/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Prestasi Siswa Ft']); ?></a>
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
