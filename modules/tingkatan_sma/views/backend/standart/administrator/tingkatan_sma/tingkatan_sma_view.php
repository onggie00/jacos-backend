
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Tingkatan Sma      <small><?= cclang('detail', ['Tingkatan Sma']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/tingkatan_sma'); ?>">Tingkatan Sma</a></li>
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
                     <h3 class="widget-user-username">Tingkatan Sma</h3>
                     <h5 class="widget-user-desc">Detail Tingkatan Sma</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_tingkatan_sma" id="form_tingkatan_sma" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tingkatan Sma </label>

                        <div class="col-sm-8">
                           <?= _ent($tingkatan_sma->id_tingkatan_sma); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Label </label>

                        <div class="col-sm-8">
                           <?= _ent($tingkatan_sma->label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Biaya Spp </label>

                        <div class="col-sm-8">
                           <?= _ent($tingkatan_sma->biaya_spp); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('tingkatan_sma_update', function() use ($tingkatan_sma){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit tingkatan_sma (Ctrl+e)" href="<?= site_url('administrator/tingkatan_sma/edit/'.$tingkatan_sma->id_tingkatan_sma); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Tingkatan Sma']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/tingkatan_sma/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Tingkatan Sma']); ?></a>
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
