
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Buku Panduan      <small><?= cclang('detail', ['Buku Panduan']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pengaturan_panduan_ft'); ?>">Buku Panduan</a></li>
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
                     <h3 class="widget-user-username">Buku Panduan</h3>
                     <h5 class="widget-user-desc">Detail Buku Panduan</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pengaturan_panduan_ft" id="form_pengaturan_panduan_ft" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_panduan_ft->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Panduan </label>
                        <div class="col-sm-8">
                             <?php if (is_image($pengaturan_panduan_ft->file_panduan)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/pengaturan_panduan_ft/' . $pengaturan_panduan_ft->file_panduan; ?>">
                                <img src="<?= BASE_URL . 'uploads/pengaturan_panduan_ft/' . $pengaturan_panduan_ft->file_panduan; ?>" class="image-responsive" alt="image pengaturan_panduan_ft" title="file_panduan pengaturan_panduan_ft" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/pengaturan_panduan_ft/' . $pengaturan_panduan_ft->file_panduan; ?>">
                                 <img src="<?= get_icon_file($pengaturan_panduan_ft->file_panduan); ?>" class="image-responsive" alt="image pengaturan_panduan_ft" title="file_panduan <?= $pengaturan_panduan_ft->file_panduan; ?>" width="40px"> 
                               <?= $pengaturan_panduan_ft->file_panduan ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                      
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pengaturan_panduan_ft_update', function() use ($pengaturan_panduan_ft){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pengaturan_panduan_ft (Ctrl+e)" href="<?= site_url('administrator/pengaturan_panduan_ft/edit/'.$pengaturan_panduan_ft->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pengaturan Panduan Ft']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pengaturan_panduan_ft/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pengaturan Panduan Ft']); ?></a>
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
