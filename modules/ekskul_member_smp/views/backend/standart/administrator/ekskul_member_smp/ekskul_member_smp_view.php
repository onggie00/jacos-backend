
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Anggota Ekskul SMP      <small><?= cclang('detail', ['Anggota Ekskul SMP']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/ekskul_member_smp'); ?>">Anggota Ekskul SMP</a></li>
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
                     <h3 class="widget-user-username">Anggota Ekskul SMP</h3>
                     <h5 class="widget-user-desc">Detail Anggota Ekskul SMP</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_ekskul_member_smp" id="form_ekskul_member_smp" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Member </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_member_smp->id_member); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Ektrakurikuler </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_member_smp->ekskul_nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_member_smp->siswa_smp_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Bukti Pembayaran </label>
                        <div class="col-sm-8">
                             <?php if (is_image($ekskul_member_smp->file_pembayaran)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/ekskul_member_smp/' . $ekskul_member_smp->file_pembayaran; ?>">
                                <img src="<?= BASE_URL . 'uploads/ekskul_member_smp/' . $ekskul_member_smp->file_pembayaran; ?>" class="image-responsive" alt="image ekskul_member_smp" title="file_pembayaran ekskul_member_smp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/ekskul_member_smp/' . $ekskul_member_smp->file_pembayaran; ?>">
                                 <img src="<?= get_icon_file($ekskul_member_smp->file_pembayaran); ?>" class="image-responsive" alt="image ekskul_member_smp" title="file_pembayaran <?= $ekskul_member_smp->file_pembayaran; ?>" width="40px"> 
                               <?= $ekskul_member_smp->file_pembayaran ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_member_smp->status_member); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Bergabung </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_member_smp->joined_date); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('ekskul_member_smp_update', function() use ($ekskul_member_smp){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit ekskul_member_smp (Ctrl+e)" href="<?= site_url('administrator/ekskul_member_smp/edit/'.$ekskul_member_smp->id_member); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Ekskul Member Smp']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/ekskul_member_smp/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Ekskul Member Smp']); ?></a>
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
