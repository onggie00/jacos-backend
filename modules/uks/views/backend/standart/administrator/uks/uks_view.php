
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Uks      <small><?= cclang('detail', ['Uks']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/uks'); ?>">Uks</a></li>
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
                     <h3 class="widget-user-username">Uks</h3>
                     <h5 class="widget-user-desc">Detail Uks</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_uks" id="form_uks" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Uks </label>

                        <div class="col-sm-8">
                           <?= _ent($uks->id_uks); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Judul </label>

                        <div class="col-sm-8">
                           <?= _ent($uks->judul); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Konten </label>

                        <div class="col-sm-8">
                           <?= _ent($uks->konten); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Foto </label>
                        <div class="col-sm-8">
                             <?php if (is_image($uks->foto)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/uks/' . $uks->foto; ?>">
                                <img src="<?= BASE_URL . 'uploads/uks/' . $uks->foto; ?>" class="image-responsive" alt="image uks" title="foto uks" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/uks/' . $uks->foto; ?>">
                                 <img src="<?= get_icon_file($uks->foto); ?>" class="image-responsive" alt="image uks" title="foto <?= $uks->foto; ?>" width="40px"> 
                               <?= $uks->foto ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($uks->created_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('uks_update', function() use ($uks){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit uks (Ctrl+e)" href="<?= site_url('administrator/uks/edit/'.$uks->id_uks); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Uks']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/uks/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Uks']); ?></a>
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
