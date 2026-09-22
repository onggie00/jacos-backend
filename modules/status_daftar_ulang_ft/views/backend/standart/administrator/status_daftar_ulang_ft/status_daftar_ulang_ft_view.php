
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Status Daftar Ulang Ft      <small><?= cclang('detail', ['Status Daftar Ulang Ft']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/status_daftar_ulang_ft'); ?>">Status Daftar Ulang Ft</a></li>
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
                     <h3 class="widget-user-username">Status Daftar Ulang Ft</h3>
                     <h5 class="widget-user-desc">Detail Status Daftar Ulang Ft</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_status_daftar_ulang_ft" id="form_status_daftar_ulang_ft" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Daftar Ulang </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_ft->id_daftar_ulang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa Ft </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_ft->id_siswa_ft); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_ft->status); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Slip Pembayaran </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_ft->slip_pembayaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kwitansi </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_ft->kwitansi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kartu Sementara </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_ft->kartu_sementara); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Lulus </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_ft->tanggal_lulus); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tgl Daftar Ulang </label>

                        <div class="col-sm-8">
                           <?= _ent($status_daftar_ulang_ft->tgl_daftar_ulang); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('status_daftar_ulang_ft_update', function() use ($status_daftar_ulang_ft){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit status_daftar_ulang_ft (Ctrl+e)" href="<?= site_url('administrator/status_daftar_ulang_ft/edit/'.$status_daftar_ulang_ft->id_daftar_ulang); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Status Daftar Ulang Ft']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/status_daftar_ulang_ft/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Status Daftar Ulang Ft']); ?></a>
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
