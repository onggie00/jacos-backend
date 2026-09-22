
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Rapor SMA      <small><?= cclang('detail', ['Rapor SMA']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/siswa_sma_aktif_raport'); ?>">Rapor SMA</a></li>
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
                     <h3 class="widget-user-username">Rapor SMA</h3>
                     <h5 class="widget-user-desc">Detail Rapor SMA</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_siswa_sma_aktif_raport" id="form_siswa_sma_aktif_raport" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif_raport->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">NIS </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif_raport->nis); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif_raport->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Detail Siswa </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif_raport->siswa_sma_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif_raport->tahun_ajaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Ujian </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif_raport->jenis_ujian); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Sync SPP </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif_raport->tanggal_sync_valid); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Rapor </label>
                        <div class="col-sm-8">
                             <?php if (is_image($siswa_sma_aktif_raport->file_raport)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/siswa_sma_aktif_raport/' . $siswa_sma_aktif_raport->file_raport; ?>">
                                <img src="<?= BASE_URL . 'uploads/siswa_sma_aktif_raport/' . $siswa_sma_aktif_raport->file_raport; ?>" class="image-responsive" alt="image siswa_sma_aktif_raport" title="file_raport siswa_sma_aktif_raport" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/siswa_sma_aktif_raport/' . $siswa_sma_aktif_raport->file_raport; ?>">
                                 <img src="<?= get_icon_file($siswa_sma_aktif_raport->file_raport); ?>" class="image-responsive" alt="image siswa_sma_aktif_raport" title="file_raport <?= $siswa_sma_aktif_raport->file_raport; ?>" width="40px"> 
                               <?= $siswa_sma_aktif_raport->file_raport ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($siswa_sma_aktif_raport->created_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('siswa_sma_aktif_raport_update', function() use ($siswa_sma_aktif_raport){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit siswa_sma_aktif_raport (Ctrl+e)" href="<?= site_url('administrator/siswa_sma_aktif_raport/edit/'.$siswa_sma_aktif_raport->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Siswa Sma Aktif Raport']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/siswa_sma_aktif_raport/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Siswa Sma Aktif Raport']); ?></a>
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
