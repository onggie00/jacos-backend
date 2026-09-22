
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Activity User      <small><?= cclang('detail', ['Activity User']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/activity_user'); ?>">Activity User</a></li>
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
                     <h3 class="widget-user-username">Activity User</h3>
                     <h5 class="widget-user-desc">Detail Activity User</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_activity_user" id="form_activity_user" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Activity </label>

                        <div class="col-sm-8">
                           <?= _ent($activity_user->id_activity); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($activity_user->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Isian </label>

                        <div class="col-sm-8">
                           <?= _ent($activity_user->value); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal </label>

                        <div class="col-sm-8">
                           <?= _ent($activity_user->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Diubah Oleh </label>

                        <div class="col-sm-8">
                           <?= _ent($activity_user->updated_by); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('activity_user_update', function() use ($activity_user){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit activity_user (Ctrl+e)" href="<?= site_url('administrator/activity_user/edit/'.$activity_user->id_activity); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Activity User']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/activity_user/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Activity User']); ?></a>
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
