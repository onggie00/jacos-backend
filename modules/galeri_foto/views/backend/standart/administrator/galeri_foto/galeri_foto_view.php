
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Galeri Foto      <small><?= cclang('detail', ['Galeri Foto']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/galeri_foto'); ?>">Galeri Foto</a></li>
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
                     <h3 class="widget-user-username">Galeri Foto</h3>
                     <h5 class="widget-user-desc">Detail Galeri Foto</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_galeri_foto" id="form_galeri_foto" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Galeri </label>

                        <div class="col-sm-8">
                           <?= _ent($galeri_foto->id_galeri); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Label </label>

                        <div class="col-sm-8">
                           <?= _ent($galeri_foto->label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Foto </label>
                        <div class="col-sm-8">
                             <?php if (is_image($galeri_foto->foto)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/galeri_foto/' . $galeri_foto->foto; ?>">
                                <img src="<?= BASE_URL . 'uploads/galeri_foto/' . $galeri_foto->foto; ?>" class="image-responsive" alt="image galeri_foto" title="foto galeri_foto" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/galeri_foto/' . $galeri_foto->foto; ?>">
                                 <img src="<?= get_icon_file($galeri_foto->foto); ?>" class="image-responsive" alt="image galeri_foto" title="foto <?= $galeri_foto->foto; ?>" width="40px"> 
                               <?= $galeri_foto->foto ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                      
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('galeri_foto_update', function() use ($galeri_foto){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit galeri_foto (Ctrl+e)" href="<?= site_url('administrator/galeri_foto/edit/'.$galeri_foto->id_galeri); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Galeri Foto']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/galeri_foto/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Galeri Foto']); ?></a>
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
