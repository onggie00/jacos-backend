
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Data Kepala Sekolah      <small><?= cclang('detail', ['Data Kepala Sekolah']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/data_kepala_sekolah'); ?>">Data Kepala Sekolah</a></li>
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
                     <h3 class="widget-user-username">Data Kepala Sekolah</h3>
                     <h5 class="widget-user-desc">Detail Data Kepala Sekolah</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_data_kepala_sekolah" id="form_data_kepala_sekolah" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($data_kepala_sekolah->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Kepsek </label>

                        <div class="col-sm-8">
                           <?= _ent($data_kepala_sekolah->nama_kepsek); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Ttd </label>
                        <div class="col-sm-8">
                             <?php if (is_image($data_kepala_sekolah->file_ttd)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/data_kepala_sekolah/' . $data_kepala_sekolah->file_ttd; ?>">
                                <img src="<?= BASE_URL . 'uploads/data_kepala_sekolah/' . $data_kepala_sekolah->file_ttd; ?>" class="image-responsive" alt="image data_kepala_sekolah" title="file_ttd data_kepala_sekolah" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/data_kepala_sekolah/' . $data_kepala_sekolah->file_ttd; ?>">
                                 <img src="<?= get_icon_file($data_kepala_sekolah->file_ttd); ?>" class="image-responsive" alt="image data_kepala_sekolah" title="file_ttd <?= $data_kepala_sekolah->file_ttd; ?>" width="40px"> 
                               <?= $data_kepala_sekolah->file_ttd ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nrks </label>

                        <div class="col-sm-8">
                           <?= _ent($data_kepala_sekolah->nrks); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($data_kepala_sekolah->jenjang); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('data_kepala_sekolah_update', function() use ($data_kepala_sekolah){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit data_kepala_sekolah (Ctrl+e)" href="<?= site_url('administrator/data_kepala_sekolah/edit/'.$data_kepala_sekolah->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Data Kepala Sekolah']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/data_kepala_sekolah/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Data Kepala Sekolah']); ?></a>
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
