
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Agenda      <small><?= cclang('detail', ['Agenda']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/agenda'); ?>">Agenda</a></li>
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
                     <h3 class="widget-user-username">Agenda</h3>
                     <h5 class="widget-user-desc">Detail Agenda</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_agenda" id="form_agenda" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Agenda </label>

                        <div class="col-sm-8">
                           <?= _ent($agenda->id_agenda); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Judul </label>

                        <div class="col-sm-8">
                           <?= _ent($agenda->judul); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Deskripsi </label>

                        <div class="col-sm-8">
                           <?= _ent($agenda->deskripsi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Img Thumbnail </label>
                        <div class="col-sm-8">
                             <?php if (is_image($agenda->img_thumbnail)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/agenda/' . $agenda->img_thumbnail; ?>">
                                <img src="<?= BASE_URL . 'uploads/agenda/' . $agenda->img_thumbnail; ?>" class="image-responsive" alt="image agenda" title="img_thumbnail agenda" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/agenda/' . $agenda->img_thumbnail; ?>">
                                 <img src="<?= get_icon_file($agenda->img_thumbnail); ?>" class="image-responsive" alt="image agenda" title="img_thumbnail <?= $agenda->img_thumbnail; ?>" width="40px"> 
                               <?= $agenda->img_thumbnail ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Img Agenda </label>
                        <div class="col-sm-8">
                             <?php if (is_image($agenda->img_agenda)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/agenda/' . $agenda->img_agenda; ?>">
                                <img src="<?= BASE_URL . 'uploads/agenda/' . $agenda->img_agenda; ?>" class="image-responsive" alt="image agenda" title="img_agenda agenda" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/agenda/' . $agenda->img_agenda; ?>">
                                 <img src="<?= get_icon_file($agenda->img_agenda); ?>" class="image-responsive" alt="image agenda" title="img_agenda <?= $agenda->img_agenda; ?>" width="40px"> 
                               <?= $agenda->img_agenda ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($agenda->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($agenda->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kategori </label>

                        <div class="col-sm-8">
                           <?= _ent($agenda->kategori_agenda_nama_kategori); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('agenda_update', function() use ($agenda){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit agenda (Ctrl+e)" href="<?= site_url('administrator/agenda/edit/'.$agenda->id_agenda); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Agenda']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/agenda/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Agenda']); ?></a>
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
