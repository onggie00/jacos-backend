
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pekerjaan Ortu      <small><?= cclang('detail', ['Pekerjaan Ortu']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pekerjaan_ortu'); ?>">Pekerjaan Ortu</a></li>
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
                     <h3 class="widget-user-username">Pekerjaan Ortu</h3>
                     <h5 class="widget-user-desc">Detail Pekerjaan Ortu</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pekerjaan_ortu" id="form_pekerjaan_ortu" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Pekerjaan Ortu </label>

                        <div class="col-sm-8">
                           <?= _ent($pekerjaan_ortu->id_pekerjaan_ortu); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Pekerjaan </label>

                        <div class="col-sm-8">
                           <?= _ent($pekerjaan_ortu->nama_pekerjaan); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pekerjaan_ortu_update', function() use ($pekerjaan_ortu){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pekerjaan_ortu (Ctrl+e)" href="<?= site_url('administrator/pekerjaan_ortu/edit/'.$pekerjaan_ortu->id_pekerjaan_ortu); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pekerjaan Ortu']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pekerjaan_ortu/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pekerjaan Ortu']); ?></a>
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
