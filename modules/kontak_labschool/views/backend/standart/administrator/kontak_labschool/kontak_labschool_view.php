
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Kontak Labschool      <small><?= cclang('detail', ['Kontak Labschool']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/kontak_labschool'); ?>">Kontak Labschool</a></li>
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
                     <h3 class="widget-user-username">Kontak Labschool</h3>
                     <h5 class="widget-user-desc">Detail Kontak Labschool</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_kontak_labschool" id="form_kontak_labschool" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kontak Labschool </label>

                        <div class="col-sm-8">
                           <?= _ent($kontak_labschool->id_kontak_labschool); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tipe Kontak </label>

                        <div class="col-sm-8">
                           <?= _ent($kontak_labschool->tipe_kontak_tipe); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kontak </label>

                        <div class="col-sm-8">
                           <?= _ent($kontak_labschool->kontak); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('kontak_labschool_update', function() use ($kontak_labschool){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit kontak_labschool (Ctrl+e)" href="<?= site_url('administrator/kontak_labschool/edit/'.$kontak_labschool->id_kontak_labschool); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Kontak Labschool']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/kontak_labschool/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Kontak Labschool']); ?></a>
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
