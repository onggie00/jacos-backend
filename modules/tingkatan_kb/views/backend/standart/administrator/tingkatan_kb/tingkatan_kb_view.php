
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Tingkatan Kb      <small><?= cclang('detail', ['Tingkatan Kb']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/tingkatan_kb'); ?>">Tingkatan Kb</a></li>
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
                     <h3 class="widget-user-username">Tingkatan Kb</h3>
                     <h5 class="widget-user-desc">Detail Tingkatan Kb</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_tingkatan_kb" id="form_tingkatan_kb" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tingkatan Kb </label>

                        <div class="col-sm-8">
                           <?= _ent($tingkatan_kb->id_tingkatan_kb); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Label </label>

                        <div class="col-sm-8">
                           <?= _ent($tingkatan_kb->label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Usia Minimal (tahun) </label>

                        <div class="col-sm-8">
                           <?= _ent($tingkatan_kb->usia_min); ?>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Usia Maksimal (tahun) </label>

                        <div class="col-sm-8">
                           <?= _ent($tingkatan_kb->usia_max); ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Biaya Spp </label>

                        <div class="col-sm-8">
                           <?= _ent($tingkatan_kb->biaya_spp); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('tingkatan_kb_update', function() use ($tingkatan_kb){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit tingkatan_kb (Ctrl+e)" href="<?= site_url('administrator/tingkatan_kb/edit/'.$tingkatan_kb->id_tingkatan_kb); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Tingkatan Kb']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/tingkatan_kb/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Tingkatan Kb']); ?></a>
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
