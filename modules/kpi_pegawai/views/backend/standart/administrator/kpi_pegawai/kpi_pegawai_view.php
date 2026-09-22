
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Kpi Pegawai      <small><?= cclang('detail', ['Kpi Pegawai']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/kpi_pegawai'); ?>">Kpi Pegawai</a></li>
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
                     <h3 class="widget-user-username">Kpi Pegawai</h3>
                     <h5 class="widget-user-desc">Detail Kpi Pegawai</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_kpi_pegawai" id="form_kpi_pegawai" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pegawai->id_kpi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pegawai->nama_kpi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Pegawai </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pegawai->pegawai_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Judul Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pegawai->judul_kpi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Jenis Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pegawai->jenis_kpi_pegawai_nama_jenis); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pegawai->tanggal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pegawai->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Piagam </label>
                        <div class="col-sm-8">
                             <?php if (is_image($kpi_pegawai->file_piagam)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/kpi_pegawai/' . $kpi_pegawai->file_piagam; ?>">
                                <img src="<?= BASE_URL . 'uploads/kpi_pegawai/' . $kpi_pegawai->file_piagam; ?>" class="image-responsive" alt="image kpi_pegawai" title="file_piagam kpi_pegawai" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/kpi_pegawai/' . $kpi_pegawai->file_piagam; ?>">
                                 <img src="<?= get_icon_file($kpi_pegawai->file_piagam); ?>" class="image-responsive" alt="image kpi_pegawai" title="file_piagam <?= $kpi_pegawai->file_piagam; ?>" width="40px"> 
                               <?= $kpi_pegawai->file_piagam ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Is Approved </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pegawai->is_approved); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('kpi_pegawai_update', function() use ($kpi_pegawai){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit kpi_pegawai (Ctrl+e)" href="<?= site_url('administrator/kpi_pegawai/edit/'.$kpi_pegawai->id_kpi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Kpi Pegawai']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/kpi_pegawai/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Kpi Pegawai']); ?></a>
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
