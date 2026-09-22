
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Anggota Ekskul SD      <small><?= cclang('detail', ['Anggota Ekskul SD']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/ekskul_member_sd'); ?>">Anggota Ekskul SD</a></li>
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
                     <h3 class="widget-user-username">Anggota Ekskul SD</h3>
                     <h5 class="widget-user-desc">Detail Anggota Ekskul SD</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_ekskul_member_sd" id="form_ekskul_member_sd" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Member </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_member_sd->id_member); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Ekstrakurikuler </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_member_sd->ekskul_nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_member_sd->siswa_sd_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Bukti Pembayaran </label>
                        <div class="col-sm-8">
                             <?php if (is_image($ekskul_member_sd->file_pembayaran)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/ekskul_member_sd/' . $ekskul_member_sd->file_pembayaran; ?>">
                                <img src="<?= BASE_URL . 'uploads/ekskul_member_sd/' . $ekskul_member_sd->file_pembayaran; ?>" class="image-responsive" alt="image ekskul_member_sd" title="file_pembayaran ekskul_member_sd" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/ekskul_member_sd/' . $ekskul_member_sd->file_pembayaran; ?>">
                                 <img src="<?= get_icon_file($ekskul_member_sd->file_pembayaran); ?>" class="image-responsive" alt="image ekskul_member_sd" title="file_pembayaran <?= $ekskul_member_sd->file_pembayaran; ?>" width="40px"> 
                               <?= $ekskul_member_sd->file_pembayaran ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_member_sd->status_member); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Bergabung </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_member_sd->joined_date); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('ekskul_member_sd_update', function() use ($ekskul_member_sd){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit ekskul_member_sd (Ctrl+e)" href="<?= site_url('administrator/ekskul_member_sd/edit/'.$ekskul_member_sd->id_member); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Ekskul Member Sd']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/ekskul_member_sd/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Ekskul Member Sd']); ?></a>
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
