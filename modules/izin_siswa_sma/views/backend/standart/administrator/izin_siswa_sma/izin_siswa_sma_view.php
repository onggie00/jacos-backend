
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Izin SMA      <small><?= cclang('detail', ['Izin SMA']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/izin_siswa_sma'); ?>">Izin SMA</a></li>
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
                     <h3 class="widget-user-username">Izin SMA</h3>
                     <h5 class="widget-user-desc">Detail Izin SMA</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_izin_siswa_sma" id="form_izin_siswa_sma" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_sma->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_sma->siswa_sma_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_sma->tanggal_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_sma->tanggal_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Izin </label>
                        <div class="col-sm-8">
                             <?php if (is_image($izin_siswa_sma->file_izin)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/izin_siswa_sma/' . $izin_siswa_sma->file_izin; ?>">
                                <img src="<?= BASE_URL . 'uploads/izin_siswa_sma/' . $izin_siswa_sma->file_izin; ?>" class="image-responsive" alt="image izin_siswa_sma" title="file_izin izin_siswa_sma" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/izin_siswa_sma/' . $izin_siswa_sma->file_izin; ?>">
                                 <img src="<?= get_icon_file($izin_siswa_sma->file_izin); ?>" class="image-responsive" alt="image izin_siswa_sma" title="file_izin <?= $izin_siswa_sma->file_izin; ?>" width="40px"> 
                               <?= $izin_siswa_sma->file_izin ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_sma->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Izin </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_sma->jenis_izin); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Satus </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_sma->status); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Approver </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_sma->guru_sma_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_sma->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Update At </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_sma->update_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('izin_siswa_sma_update', function() use ($izin_siswa_sma){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit izin_siswa_sma (Ctrl+e)" href="<?= site_url('administrator/izin_siswa_sma/edit/'.$izin_siswa_sma->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Izin Siswa Sma']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/izin_siswa_sma/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Izin Siswa Sma']); ?></a>
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
