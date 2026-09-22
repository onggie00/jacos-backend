
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Tahun Ajaran Psb      <small><?= cclang('detail', ['Tahun Ajaran Psb']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/tahun_ajaran_psb'); ?>">Tahun Ajaran Psb</a></li>
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
                     <h3 class="widget-user-username">Tahun Ajaran Psb</h3>
                     <h5 class="widget-user-desc">Detail Tahun Ajaran Psb</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_tahun_ajaran_psb" id="form_tahun_ajaran_psb" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($tahun_ajaran_psb->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Label </label>

                        <div class="col-sm-8">
                           <?= _ent($tahun_ajaran_psb->label); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('tahun_ajaran_psb_update', function() use ($tahun_ajaran_psb){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit tahun_ajaran_psb (Ctrl+e)" href="<?= site_url('administrator/tahun_ajaran_psb/edit/'.$tahun_ajaran_psb->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Tahun Ajaran Psb']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/tahun_ajaran_psb/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Tahun Ajaran Psb']); ?></a>
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
