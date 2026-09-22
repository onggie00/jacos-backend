
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Izin SMP      <small><?= cclang('detail', ['Izin SMP']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/izin_siswa_smp'); ?>">Izin SMP</a></li>
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
                     <h3 class="widget-user-username">Izin SMP</h3>
                     <h5 class="widget-user-desc">Detail Izin SMP</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_izin_siswa_smp" id="form_izin_siswa_smp" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_smp->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_smp->siswa_smp_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_smp->tanggal_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_smp->tanggal_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Izin </label>
                        <div class="col-sm-8">
                             <?php if (is_image($izin_siswa_smp->file_izin)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/izin_siswa_smp/' . $izin_siswa_smp->file_izin; ?>">
                                <img src="<?= BASE_URL . 'uploads/izin_siswa_smp/' . $izin_siswa_smp->file_izin; ?>" class="image-responsive" alt="image izin_siswa_smp" title="file_izin izin_siswa_smp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/izin_siswa_smp/' . $izin_siswa_smp->file_izin; ?>">
                                 <img src="<?= get_icon_file($izin_siswa_smp->file_izin); ?>" class="image-responsive" alt="image izin_siswa_smp" title="file_izin <?= $izin_siswa_smp->file_izin; ?>" width="40px"> 
                               <?= $izin_siswa_smp->file_izin ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_smp->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Izin </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_smp->jenis_izin); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_smp->status); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Approver </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_smp->guru_smp_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_smp->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Update At </label>

                        <div class="col-sm-8">
                           <?= _ent($izin_siswa_smp->update_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('izin_siswa_smp_update', function() use ($izin_siswa_smp){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit izin_siswa_smp (Ctrl+e)" href="<?= site_url('administrator/izin_siswa_smp/edit/'.$izin_siswa_smp->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Izin Siswa Smp']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/izin_siswa_smp/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Izin Siswa Smp']); ?></a>
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
