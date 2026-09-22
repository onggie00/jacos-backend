
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pengaturan Kwitansi Program      <small><?= cclang('detail', ['Pengaturan Kwitansi Program']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pengaturan_kwitansi_program'); ?>">Pengaturan Kwitansi Program</a></li>
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
                     <h3 class="widget-user-username">Pengaturan Kwitansi Program</h3>
                     <h5 class="widget-user-desc">Detail Pengaturan Kwitansi Program</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pengaturan_kwitansi_program" id="form_pengaturan_kwitansi_program" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Pengaturan </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_kwitansi_program->id_pengaturan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kepala Sekolah (Nama) </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_kwitansi_program->kepala_sekolah_nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kepala Sekolah (NPP) </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_kwitansi_program->kepala_sekolah_npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kepala TU (Nama) </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_kwitansi_program->kepala_tu_nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kepala TU (NPP) </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_kwitansi_program->kepala_tu_npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kepala Sekretariat (Nama) </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_kwitansi_program->kepala_sekretariat_nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kepala Sekretariat (NPP) </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_kwitansi_program->kepala_sekretariat_npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_kwitansi_program->jenjang); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pengaturan_kwitansi_program_update', function() use ($pengaturan_kwitansi_program){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pengaturan_kwitansi_program (Ctrl+e)" href="<?= site_url('administrator/pengaturan_kwitansi_program/edit/'.$pengaturan_kwitansi_program->id_pengaturan); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pengaturan Kwitansi Program']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pengaturan_kwitansi_program/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pengaturan Kwitansi Program']); ?></a>
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
