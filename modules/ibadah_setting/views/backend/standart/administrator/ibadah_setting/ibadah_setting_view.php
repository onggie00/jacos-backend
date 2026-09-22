
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Setting Ibadah      <small><?= cclang('detail', ['Setting Ibadah']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/ibadah_setting'); ?>">Setting Ibadah</a></li>
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
                     <h3 class="widget-user-username">Setting Ibadah</h3>
                     <h5 class="widget-user-desc">Detail Setting Ibadah</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_ibadah_setting" id="form_ibadah_setting" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($ibadah_setting->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Ibadah </label>

                        <div class="col-sm-8">
                           <?= _ent($ibadah_setting->ibadah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Waktu Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($ibadah_setting->start_ibadah); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Waktu Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($ibadah_setting->end_ibadah); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('ibadah_setting_update', function() use ($ibadah_setting){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit ibadah_setting (Ctrl+e)" href="<?= site_url('administrator/ibadah_setting/edit/'.$ibadah_setting->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Ibadah Setting']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/ibadah_setting/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Ibadah Setting']); ?></a>
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
