
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Presensi Setting Status      <small><?= cclang('detail', ['Presensi Setting Status']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/presensi_setting_status'); ?>">Presensi Setting Status</a></li>
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
                     <h3 class="widget-user-username">Presensi Setting Status</h3>
                     <h5 class="widget-user-desc">Detail Presensi Setting Status</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_presensi_setting_status" id="form_presensi_setting_status" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Status </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_setting_status->id_status); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_setting_status->nama_status); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kode </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_setting_status->code); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Diubah Oleh </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_setting_status->updated_by); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Diubah Tgl </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_setting_status->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('presensi_setting_status_update', function() use ($presensi_setting_status){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit presensi_setting_status (Ctrl+e)" href="<?= site_url('administrator/presensi_setting_status/edit/'.$presensi_setting_status->id_status); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Presensi Setting Status']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/presensi_setting_status/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Presensi Setting Status']); ?></a>
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
