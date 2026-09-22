
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Transaksi Spp      <small><?= cclang('detail', ['Transaksi Spp']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/transaksi_spp'); ?>">Transaksi Spp</a></li>
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
                     <h3 class="widget-user-username">Transaksi Spp</h3>
                     <h5 class="widget-user-desc">Detail Transaksi Spp</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_transaksi_spp" id="form_transaksi_spp" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Transaksi </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->id_transaksi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">No Transaksi </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->no_transaksi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Bank </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->nama_bank); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Va Number </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->va_number); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">User Email </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->user_email); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">User Name </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->user_name); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">User Phone </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->user_phone); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Description </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->description); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Biaya Pendaftaran </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->id_biaya_pendaftaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Biaya </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->total_biaya); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Transaksi </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->status_transaksi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Expired Datetime </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->expired_datetime); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->updated_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Is Show </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->is_show); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Payment Response </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->payment_response); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Payment Amount </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->payment_amount); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Bulan </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->bulan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kode Tagihan </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->kode_tagihan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Kwitansi </label>
                        <div class="col-sm-8">
                             <?php if (is_image($transaksi_spp->file_kwitansi)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/transaksi_spp/' . $transaksi_spp->file_kwitansi; ?>">
                                <img src="<?= BASE_URL . 'uploads/transaksi_spp/' . $transaksi_spp->file_kwitansi; ?>" class="image-responsive" alt="image transaksi_spp" title="file_kwitansi transaksi_spp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/transaksi_spp/' . $transaksi_spp->file_kwitansi; ?>">
                                 <img src="<?= get_icon_file($transaksi_spp->file_kwitansi); ?>" class="image-responsive" alt="image transaksi_spp" title="file_kwitansi <?= $transaksi_spp->file_kwitansi; ?>" width="40px"> 
                               <?= $transaksi_spp->file_kwitansi ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->id_siswa_aktif); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Detail Bulan </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_spp->detail_bulan); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('transaksi_spp_update', function() use ($transaksi_spp){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit transaksi_spp (Ctrl+e)" href="<?= site_url('administrator/transaksi_spp/edit/'.$transaksi_spp->id_transaksi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Transaksi Spp']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/transaksi_spp/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Transaksi Spp']); ?></a>
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
