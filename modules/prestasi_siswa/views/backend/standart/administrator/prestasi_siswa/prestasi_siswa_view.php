
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Prestasi Siswa      <small><?= cclang('detail', ['Prestasi Siswa']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/prestasi_siswa'); ?>">Prestasi Siswa</a></li>
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
                     <h3 class="widget-user-username">Prestasi Siswa</h3>
                     <h5 class="widget-user-desc">Detail Prestasi Siswa</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_prestasi_siswa" id="form_prestasi_siswa" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa->id_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa->nama_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Raih </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa->tgl_raih); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Juara </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa->juara); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Prestasi </label>
                        <div class="col-sm-8">
                             <?php if (is_image($prestasi_siswa->file_prestasi)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_siswa/' . $prestasi_siswa->file_prestasi; ?>">
                                <img src="<?= BASE_URL . 'uploads/prestasi_siswa/' . $prestasi_siswa->file_prestasi; ?>" class="image-responsive" alt="image prestasi_siswa" title="file_prestasi prestasi_siswa" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/prestasi_siswa/' . $prestasi_siswa->file_prestasi; ?>">
                                 <img src="<?= get_icon_file($prestasi_siswa->file_prestasi); ?>" class="image-responsive" alt="image prestasi_siswa" title="file_prestasi <?= $prestasi_siswa->file_prestasi; ?>" width="40px"> 
                               <?= $prestasi_siswa->file_prestasi ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Foto Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa->foto_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa->id_siswa); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Judul </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa->judul); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Konten </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa->konten); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Posting </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa->tanggal_posting); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Is Approved </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa->is_approved); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('prestasi_siswa_update', function() use ($prestasi_siswa){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit prestasi_siswa (Ctrl+e)" href="<?= site_url('administrator/prestasi_siswa/edit/'.$prestasi_siswa->id_prestasi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Prestasi Siswa']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/prestasi_siswa/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Prestasi Siswa']); ?></a>
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
