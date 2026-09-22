
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Kpi Sd      <small><?= cclang('detail', ['Kpi Sd']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/kpi_sd'); ?>">Kpi Sd</a></li>
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
                     <h3 class="widget-user-username">Kpi Sd</h3>
                     <h5 class="widget-user-desc">Detail Kpi Sd</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_kpi_sd" id="form_kpi_sd" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_sd->id_kpi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_sd->nama_kpi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Guru </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_sd->guru_sd_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Judul Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_sd->judul_kpi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Jenis Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_sd->jenis_kpi_nama_jenis); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_sd->tanggal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_sd->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Piagam </label>
                        <div class="col-sm-8">
                             <?php if (is_image($kpi_sd->file_piagam)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/kpi_sd/' . $kpi_sd->file_piagam; ?>">
                                <img src="<?= BASE_URL . 'uploads/kpi_sd/' . $kpi_sd->file_piagam; ?>" class="image-responsive" alt="image kpi_sd" title="file_piagam kpi_sd" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/kpi_sd/' . $kpi_sd->file_piagam; ?>">
                                 <img src="<?= get_icon_file($kpi_sd->file_piagam); ?>" class="image-responsive" alt="image kpi_sd" title="file_piagam <?= $kpi_sd->file_piagam; ?>" width="40px"> 
                               <?= $kpi_sd->file_piagam ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Is Approve </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_sd->is_approve); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('kpi_sd_update', function() use ($kpi_sd){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit kpi_sd (Ctrl+e)" href="<?= site_url('administrator/kpi_sd/edit/'.$kpi_sd->id_kpi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Kpi Sd']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/kpi_sd/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Kpi Sd']); ?></a>
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
