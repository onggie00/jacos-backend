
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Prestasi Guru Ft      <small><?= cclang('detail', ['Prestasi Guru Ft']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/prestasi_guru_ft'); ?>">Prestasi Guru Ft</a></li>
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
                     <h3 class="widget-user-username">Prestasi Guru Ft</h3>
                     <h5 class="widget-user-desc">Detail Prestasi Guru Ft</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_prestasi_guru_ft" id="form_prestasi_guru_ft" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_guru_ft->id_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_guru_ft->nama_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Guru </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_guru_ft->guru_ft_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_guru_ft->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Prestasi </label>
                        <div class="col-sm-8">
                             <?php if (is_image($prestasi_guru_ft->file_prestasi)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_guru_ft/' . $prestasi_guru_ft->file_prestasi; ?>">
                                <img src="<?= BASE_URL . 'uploads/prestasi_guru_ft/' . $prestasi_guru_ft->file_prestasi; ?>" class="image-responsive" alt="image prestasi_guru_ft" title="file_prestasi prestasi_guru_ft" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/prestasi_guru_ft/' . $prestasi_guru_ft->file_prestasi; ?>">
                                 <img src="<?= get_icon_file($prestasi_guru_ft->file_prestasi); ?>" class="image-responsive" alt="image prestasi_guru_ft" title="file_prestasi <?= $prestasi_guru_ft->file_prestasi; ?>" width="40px"> 
                               <?= $prestasi_guru_ft->file_prestasi ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Foto Prestasi </label>
                        <div class="col-sm-8">
                             <?php if (is_image($prestasi_guru_ft->foto_prestasi)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_guru_ft/' . $prestasi_guru_ft->foto_prestasi; ?>">
                                <img src="<?= BASE_URL . 'uploads/prestasi_guru_ft/' . $prestasi_guru_ft->foto_prestasi; ?>" class="image-responsive" alt="image prestasi_guru_ft" title="foto_prestasi prestasi_guru_ft" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/prestasi_guru_ft/' . $prestasi_guru_ft->foto_prestasi; ?>">
                                 <img src="<?= get_icon_file($prestasi_guru_ft->foto_prestasi); ?>" class="image-responsive" alt="image prestasi_guru_ft" title="foto_prestasi <?= $prestasi_guru_ft->foto_prestasi; ?>" width="40px"> 
                               <?= $prestasi_guru_ft->foto_prestasi ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Raih </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_guru_ft->tgl_raih); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Is Approve </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_guru_ft->is_approve); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('prestasi_guru_ft_update', function() use ($prestasi_guru_ft){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit prestasi_guru_ft (Ctrl+e)" href="<?= site_url('administrator/prestasi_guru_ft/edit/'.$prestasi_guru_ft->id_prestasi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Prestasi Guru Ft']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/prestasi_guru_ft/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Prestasi Guru Ft']); ?></a>
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
