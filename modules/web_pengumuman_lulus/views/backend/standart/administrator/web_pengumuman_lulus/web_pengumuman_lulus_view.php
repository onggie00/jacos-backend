
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      File Pengumuman Lulus      <small><?= cclang('detail', ['File Pengumuman Lulus']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/web_pengumuman_lulus'); ?>">File Pengumuman Lulus</a></li>
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
                     <h3 class="widget-user-username">File Pengumuman Lulus</h3>
                     <h5 class="widget-user-desc">Detail File Pengumuman Lulus</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_web_pengumuman_lulus" id="form_web_pengumuman_lulus" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Web Pengumuman Lulus </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengumuman_lulus->id_web_pengumuman_lulus); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Dokumen </label>
                        <div class="col-sm-8">
                             <?php if (is_image($web_pengumuman_lulus->dokumen)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/web_pengumuman_lulus/' . $web_pengumuman_lulus->dokumen; ?>">
                                <img src="<?= BASE_URL . 'uploads/web_pengumuman_lulus/' . $web_pengumuman_lulus->dokumen; ?>" class="image-responsive" alt="image web_pengumuman_lulus" title="dokumen web_pengumuman_lulus" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/web_pengumuman_lulus/' . $web_pengumuman_lulus->dokumen; ?>">
                                 <img src="<?= get_icon_file($web_pengumuman_lulus->dokumen); ?>" class="image-responsive" alt="image web_pengumuman_lulus" title="dokumen <?= $web_pengumuman_lulus->dokumen; ?>" width="40px"> 
                               <?= $web_pengumuman_lulus->dokumen ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengumuman_lulus->jenjang); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('web_pengumuman_lulus_update', function() use ($web_pengumuman_lulus){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit web_pengumuman_lulus (Ctrl+e)" href="<?= site_url('administrator/web_pengumuman_lulus/edit/'.$web_pengumuman_lulus->id_web_pengumuman_lulus); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Web Pengumuman Lulus']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/web_pengumuman_lulus/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Web Pengumuman Lulus']); ?></a>
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
