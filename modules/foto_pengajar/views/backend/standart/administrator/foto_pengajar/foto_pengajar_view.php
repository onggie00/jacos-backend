
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Foto Pengajar      <small><?= cclang('detail', ['Foto Pengajar']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/foto_pengajar'); ?>">Foto Pengajar</a></li>
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
                     <h3 class="widget-user-username">Foto Pengajar</h3>
                     <h5 class="widget-user-desc">Detail Foto Pengajar</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_foto_pengajar" id="form_foto_pengajar" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Foto Pengajar </label>

                        <div class="col-sm-8">
                           <?= _ent($foto_pengajar->id_foto_pengajar); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama </label>

                        <div class="col-sm-8">
                           <?= _ent($foto_pengajar->nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jabatan </label>

                        <div class="col-sm-8">
                           <?= _ent($foto_pengajar->jabatan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Foto </label>
                        <div class="col-sm-8">
                             <?php if (is_image($foto_pengajar->foto)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/foto_pengajar/' . $foto_pengajar->foto; ?>">
                                <img src="<?= BASE_URL . 'uploads/foto_pengajar/' . $foto_pengajar->foto; ?>" class="image-responsive" alt="image foto_pengajar" title="foto foto_pengajar" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/foto_pengajar/' . $foto_pengajar->foto; ?>">
                                 <img src="<?= get_icon_file($foto_pengajar->foto); ?>" class="image-responsive" alt="image foto_pengajar" title="foto <?= $foto_pengajar->foto; ?>" width="40px"> 
                               <?= $foto_pengajar->foto ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                      
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('foto_pengajar_update', function() use ($foto_pengajar){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit foto_pengajar (Ctrl+e)" href="<?= site_url('administrator/foto_pengajar/edit/'.$foto_pengajar->id_foto_pengajar); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Foto Pengajar']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/foto_pengajar/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Foto Pengajar']); ?></a>
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
