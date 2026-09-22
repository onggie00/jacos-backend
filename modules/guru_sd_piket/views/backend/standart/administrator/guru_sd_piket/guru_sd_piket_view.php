
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Guru Piket SD      <small><?= cclang('detail', ['Guru Piket SD']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/guru_sd_piket'); ?>">Guru Piket SD</a></li>
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
                     <h3 class="widget-user-username">Guru Piket SD</h3>
                     <h5 class="widget-user-desc">Detail Guru Piket SD</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_guru_sd_piket" id="form_guru_sd_piket" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Guru Piket </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd_piket->id_guru_piket); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Guru </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd_piket->guru_sd_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($guru_sd_piket->kelas_sd_nama_kelas); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('guru_sd_piket_update', function() use ($guru_sd_piket){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit guru_sd_piket (Ctrl+e)" href="<?= site_url('administrator/guru_sd_piket/edit/'.$guru_sd_piket->id_guru_piket); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Guru Sd Piket']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/guru_sd_piket/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Guru Sd Piket']); ?></a>
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
