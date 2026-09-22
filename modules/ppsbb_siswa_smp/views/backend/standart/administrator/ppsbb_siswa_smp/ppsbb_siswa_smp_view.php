
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Ppsbb Siswa SMP      <small><?= cclang('detail', ['Ppsbb Siswa SMP']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/ppsbb_siswa_smp'); ?>">Ppsbb Siswa SMP</a></li>
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
                     <h3 class="widget-user-username">Ppsbb Siswa SMP</h3>
                     <h5 class="widget-user-desc">Detail Ppsbb Siswa SMP</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_ppsbb_siswa_smp" id="form_ppsbb_siswa_smp" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Ppsbb Siswa Smp </label>

                        <div class="col-sm-8">
                           <?= _ent($ppsbb_siswa_smp->id_ppsbb_siswa_smp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa Smp </label>

                        <div class="col-sm-8">
                           <?= _ent($ppsbb_siswa_smp->id_siswa_smp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($ppsbb_siswa_smp->jenis_prestasi_jenis_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($ppsbb_siswa_smp->nama_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($ppsbb_siswa_smp->keterangan_prestasi_keterangan_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan Prestasi (Lainnya) </label>

                        <div class="col-sm-8">
                           <?= _ent($ppsbb_siswa_smp->keterangan_prestasi_lainnya); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($ppsbb_siswa_smp->tahun_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Sertifikat </label>
                        <div class="col-sm-8">
                             <?php if (is_image($ppsbb_siswa_smp->sertifikat)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/ppsbb_siswa_smp/' . $ppsbb_siswa_smp->sertifikat; ?>">
                                <img src="<?= BASE_URL . 'uploads/ppsbb_siswa_smp/' . $ppsbb_siswa_smp->sertifikat; ?>" class="image-responsive" alt="image ppsbb_siswa_smp" title="sertifikat ppsbb_siswa_smp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/ppsbb_siswa_smp/' . $ppsbb_siswa_smp->sertifikat; ?>">
                                 <img src="<?= get_icon_file($ppsbb_siswa_smp->sertifikat); ?>" class="image-responsive" alt="image ppsbb_siswa_smp" title="sertifikat <?= $ppsbb_siswa_smp->sertifikat; ?>" width="40px"> 
                               <?= $ppsbb_siswa_smp->sertifikat ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($ppsbb_siswa_smp->jenis_jenjang_jenis_jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Lomba </label>

                        <div class="col-sm-8">
                           <?= _ent($ppsbb_siswa_smp->jenis_lomba_jenis_lomba); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('ppsbb_siswa_smp_update', function() use ($ppsbb_siswa_smp){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit ppsbb_siswa_smp (Ctrl+e)" href="<?= site_url('administrator/ppsbb_siswa_smp/edit/'.$ppsbb_siswa_smp->id_ppsbb_siswa_smp); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Ppsbb Siswa Smp']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/ppsbb_siswa_smp/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Ppsbb Siswa Smp']); ?></a>
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
