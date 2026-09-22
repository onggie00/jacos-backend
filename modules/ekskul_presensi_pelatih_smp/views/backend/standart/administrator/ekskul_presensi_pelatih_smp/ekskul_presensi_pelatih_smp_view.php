
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Presensi Pelatih  SMP      <small><?= cclang('detail', ['Presensi Pelatih  SMP']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/ekskul_presensi_pelatih_smp'); ?>">Presensi Pelatih  SMP</a></li>
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
                     <h3 class="widget-user-username">Presensi Pelatih  SMP</h3>
                     <h5 class="widget-user-desc">Detail Presensi Pelatih  SMP</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_ekskul_presensi_pelatih_smp" id="form_ekskul_presensi_pelatih_smp" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_presensi_pelatih_smp->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Ektrakurikuler </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_presensi_pelatih_smp->ekskul_nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pelatih </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_presensi_pelatih_smp->ekskul_manajemen_smp_nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Hari Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_presensi_pelatih_smp->hari_absen); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_presensi_pelatih_smp->tanggal_absen); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Waktu Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_presensi_pelatih_smp->waktu_absen); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_presensi_pelatih_smp->status_absen); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul_presensi_pelatih_smp->keterangan_presensi); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('ekskul_presensi_pelatih_smp_update', function() use ($ekskul_presensi_pelatih_smp){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit ekskul_presensi_pelatih_smp (Ctrl+e)" href="<?= site_url('administrator/ekskul_presensi_pelatih_smp/edit/'.$ekskul_presensi_pelatih_smp->); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Ekskul Presensi Pelatih Smp']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/ekskul_presensi_pelatih_smp/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Ekskul Presensi Pelatih Smp']); ?></a>
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
