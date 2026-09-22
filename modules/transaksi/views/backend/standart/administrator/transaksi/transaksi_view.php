
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Transaksi      <small><?= cclang('detail', ['Transaksi']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/transaksi'); ?>">Transaksi</a></li>
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
                     <h3 class="widget-user-username">Transaksi</h3>
                     <h5 class="widget-user-desc">Detail Transaksi</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_transaksi" id="form_transaksi" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Transaksi </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->id_transaksi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">No Transaksi </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->no_transaksi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Bank </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->nama_bank); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Va Number </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->va_number); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">User Email </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->user_email); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">User Name </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->user_name); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">User Phone </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->user_phone); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Description </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->description); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Biaya Pendaftaran </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->id_biaya_pendaftaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Biaya </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->total_biaya); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Transaksi </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->status_transaksi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Expired Datetime </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->expired_datetime); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('transaksi_update', function() use ($transaksi){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit transaksi (Ctrl+e)" href="<?= site_url('administrator/transaksi/edit/'.$transaksi->id_transaksi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Transaksi']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/transaksi/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Transaksi']); ?></a>
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
