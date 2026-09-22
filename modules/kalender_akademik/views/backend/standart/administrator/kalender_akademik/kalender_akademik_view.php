
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Kalender Akademik      <small><?= cclang('detail', ['Kalender Akademik']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/kalender_akademik'); ?>">Kalender Akademik</a></li>
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
                     <h3 class="widget-user-username">Kalender Akademik</h3>
                     <h5 class="widget-user-desc">Detail Kalender Akademik</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_kalender_akademik" id="form_kalender_akademik" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kalender </label>

                        <div class="col-sm-8">
                           <?= _ent($kalender_akademik->id_kalender); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tipe </label>

                        <div class="col-sm-8">
                           <?= _ent($kalender_akademik->tipe_kalender_nama_tipe); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Label </label>

                        <div class="col-sm-8">
                           <?= _ent($kalender_akademik->label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Date </label>

                        <div class="col-sm-8">
                           <?= _ent($kalender_akademik->date); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($kalender_akademik->tahun_ajaran_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Bulan </label>

                        <div class="col-sm-8">
                           <?= _ent($kalender_akademik->bulan); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('kalender_akademik_update', function() use ($kalender_akademik){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit kalender_akademik (Ctrl+e)" href="<?= site_url('administrator/kalender_akademik/edit/'.$kalender_akademik->id_kalender); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Kalender Akademik']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/kalender_akademik/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Kalender Akademik']); ?></a>
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
