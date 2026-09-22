
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Fasilitas      <small><?= cclang('detail', ['Fasilitas']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/fasilitas'); ?>">Fasilitas</a></li>
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
                     <h3 class="widget-user-username">Fasilitas</h3>
                     <h5 class="widget-user-desc">Detail Fasilitas</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_fasilitas" id="form_fasilitas" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Fasilitas </label>

                        <div class="col-sm-8">
                           <?= _ent($fasilitas->id_fasilitas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Fasilitas </label>

                        <div class="col-sm-8">
                           <?= _ent($fasilitas->nama_fasilitas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Deskripsi </label>

                        <div class="col-sm-8">
                           <?= _ent($fasilitas->deskripsi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Img Fasilitas </label>
                        <div class="col-sm-8">
                             <?php if (is_image($fasilitas->img_fasilitas)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/fasilitas/' . $fasilitas->img_fasilitas; ?>">
                                <img src="<?= BASE_URL . 'uploads/fasilitas/' . $fasilitas->img_fasilitas; ?>" class="image-responsive" alt="image fasilitas" title="img_fasilitas fasilitas" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/fasilitas/' . $fasilitas->img_fasilitas; ?>">
                                 <img src="<?= get_icon_file($fasilitas->img_fasilitas); ?>" class="image-responsive" alt="image fasilitas" title="img_fasilitas <?= $fasilitas->img_fasilitas; ?>" width="40px"> 
                               <?= $fasilitas->img_fasilitas ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                          <?= join_multi_select($fasilitas->jenjang, '', '', ''); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('fasilitas_update', function() use ($fasilitas){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit fasilitas (Ctrl+e)" href="<?= site_url('administrator/fasilitas/edit/'.$fasilitas->id_fasilitas); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Fasilitas']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/fasilitas/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Fasilitas']); ?></a>
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
