
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Mata Pelajaran Sma      <small><?= cclang('detail', ['Mata Pelajaran Sma']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/mata_pelajaran_sma'); ?>">Mata Pelajaran Sma</a></li>
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
                     <h3 class="widget-user-username">Mata Pelajaran Sma</h3>
                     <h5 class="widget-user-desc">Detail Mata Pelajaran Sma</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_mata_pelajaran_sma" id="form_mata_pelajaran_sma" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Mapel </label>

                        <div class="col-sm-8">
                           <?= _ent($mata_pelajaran_sma->id_mapel); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Mapel </label>

                        <div class="col-sm-8">
                           <?= _ent($mata_pelajaran_sma->nama_mapel); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kode Mapel </label>

                        <div class="col-sm-8">
                           <?= _ent($mata_pelajaran_sma->kode_mapel); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('mata_pelajaran_sma_update', function() use ($mata_pelajaran_sma){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit mata_pelajaran_sma (Ctrl+e)" href="<?= site_url('administrator/mata_pelajaran_sma/edit/'.$mata_pelajaran_sma->id_mapel); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Mata Pelajaran Sma']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/mata_pelajaran_sma/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Mata Pelajaran Sma']); ?></a>
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
