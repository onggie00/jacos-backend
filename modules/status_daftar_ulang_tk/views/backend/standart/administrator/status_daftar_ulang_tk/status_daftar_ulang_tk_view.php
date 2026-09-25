
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Status Daftar Ulang TK      <small><?= cclang('detail', ['Status Daftar Ulang TK']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/status_daftar_ulang_tk'); ?>">Status Daftar Ulang TK</a></li>
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
                     <h3 class="widget-user-username">Status Daftar Ulang TK</h3>
                     <h5 class="widget-user-desc">Detail Status Daftar Ulang TK</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_status_daftar_ulang_tk" id="form_status_daftar_ulang_tk" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Daftar Ulang </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_tk->id_daftar_ulang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa TK </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_tk->id_siswa_tk); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_tk->status); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kwitansi </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_tk->kwitansi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kartu Sementara </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_tk->kartu_sementara); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('status_daftar_ulang_tk_update', function() use ($status_daftar_ulang_tk){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit status_daftar_ulang_tk (Ctrl+e)" href="<?= site_url('administrator/status_daftar_ulang_tk/edit/'.$status_daftar_ulang_tk->id_daftar_ulang); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Status Daftar Ulang TK']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/status_daftar_ulang_tk/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Status Daftar Ulang TK']); ?></a>
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
