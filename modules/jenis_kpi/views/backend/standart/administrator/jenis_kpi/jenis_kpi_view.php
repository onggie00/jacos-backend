
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Jenis Kpi      <small><?= cclang('detail', ['Jenis Kpi']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/jenis_kpi'); ?>">Jenis Kpi</a></li>
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
                     <h3 class="widget-user-username">Jenis Kpi</h3>
                     <h5 class="widget-user-desc">Detail Jenis Kpi</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_jenis_kpi" id="form_jenis_kpi" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Jenis Kpi </label>

                        <div class="col-sm-8">
                           <?= _ent($jenis_kpi->id_jenis_kpi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Jenis </label>

                        <div class="col-sm-8">
                           <?= _ent($jenis_kpi->nama_jenis); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('jenis_kpi_update', function() use ($jenis_kpi){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit jenis_kpi (Ctrl+e)" href="<?= site_url('administrator/jenis_kpi/edit/'.$jenis_kpi->id_jenis_kpi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Jenis Kpi']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/jenis_kpi/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Jenis Kpi']); ?></a>
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
