
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Kpi Pimpinan Sma      <small><?= cclang('detail', ['Kpi Pimpinan Sma']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/kpi_pimpinan_sma'); ?>">Kpi Pimpinan Sma</a></li>
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
                     <h3 class="widget-user-username">Kpi Pimpinan Sma</h3>
                     <h5 class="widget-user-desc">Detail Kpi Pimpinan Sma</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_kpi_pimpinan_sma" id="form_kpi_pimpinan_sma" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pimpinan_sma->id_kpi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pimpinan_sma->nama_kpi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Pimpinan </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pimpinan_sma->pimpinan_sma_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Judul Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pimpinan_sma->judul_kpi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Jenis Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pimpinan_sma->jenis_kpi_pimpinan_nama_jenis); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pimpinan_sma->tanggal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($kpi_pimpinan_sma->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Piagam </label>
                        <div class="col-sm-8">
                             <?php if (is_image($kpi_pimpinan_sma->file_piagam)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/kpi_pimpinan_sma/' . $kpi_pimpinan_sma->file_piagam; ?>">
                                <img src="<?= BASE_URL . 'uploads/kpi_pimpinan_sma/' . $kpi_pimpinan_sma->file_piagam; ?>" class="image-responsive" alt="image kpi_pimpinan_sma" title="file_piagam kpi_pimpinan_sma" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/kpi_pimpinan_sma/' . $kpi_pimpinan_sma->file_piagam; ?>">
                                 <img src="<?= get_icon_file($kpi_pimpinan_sma->file_piagam); ?>" class="image-responsive" alt="image kpi_pimpinan_sma" title="file_piagam <?= $kpi_pimpinan_sma->file_piagam; ?>" width="40px"> 
                               <?= $kpi_pimpinan_sma->file_piagam ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                      
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('kpi_pimpinan_sma_update', function() use ($kpi_pimpinan_sma){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit kpi_pimpinan_sma (Ctrl+e)" href="<?= site_url('administrator/kpi_pimpinan_sma/edit/'.$kpi_pimpinan_sma->id_kpi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Kpi Pimpinan Sma']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/kpi_pimpinan_sma/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Kpi Pimpinan Sma']); ?></a>
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
