
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Biaya Spp      <small><?= cclang('detail', ['Biaya Spp']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/biaya_spp'); ?>">Biaya Spp</a></li>
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
                     <h3 class="widget-user-username">Biaya Spp</h3>
                     <h5 class="widget-user-desc">Detail Biaya Spp</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_biaya_spp" id="form_biaya_spp" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Biaya </label>

                        <div class="col-sm-8">
                           <?= _ent($biaya_spp->id_biaya); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nominal </label>

                        <div class="col-sm-8">
                           <?= _ent($biaya_spp->nominal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($biaya_spp->jenjang); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('biaya_spp_update', function() use ($biaya_spp){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit biaya_spp (Ctrl+e)" href="<?= site_url('administrator/biaya_spp/edit/'.$biaya_spp->id_biaya); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Biaya Spp']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/biaya_spp/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Biaya Spp']); ?></a>
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
