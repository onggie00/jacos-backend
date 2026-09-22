
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      List Tagihan SMA      <small><?= cclang('detail', ['List Tagihan SMA']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/transaksi_lain_sma'); ?>">List Tagihan SMA</a></li>
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
                     <h3 class="widget-user-username">List Tagihan SMA</h3>
                     <h5 class="widget-user-desc">Detail List Tagihan SMA</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_transaksi_lain_sma" id="form_transaksi_lain_sma" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_sma->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_sma->siswa_sma_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tagihan </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_sma->transaksi_lain_sma_manajemen_nama_transaksi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Pembayaran </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_sma->tanggal_bayar); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">VA Number </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_sma->va_number); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nomor Tagihan </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_sma->kode_tagihan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Kwitansi </label>
                        <div class="col-sm-8">
                             <?php if (is_image($transaksi_lain_sma->file_kwitansi)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/transaksi_lain_sma/' . $transaksi_lain_sma->file_kwitansi; ?>">
                                <img src="<?= BASE_URL . 'uploads/transaksi_lain_sma/' . $transaksi_lain_sma->file_kwitansi; ?>" class="image-responsive" alt="image transaksi_lain_sma" title="file_kwitansi transaksi_lain_sma" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/transaksi_lain_sma/' . $transaksi_lain_sma->file_kwitansi; ?>">
                                 <img src="<?= get_icon_file($transaksi_lain_sma->file_kwitansi); ?>" class="image-responsive" alt="image transaksi_lain_sma" title="file_kwitansi <?= $transaksi_lain_sma->file_kwitansi; ?>" width="40px"> 
                               <?= $transaksi_lain_sma->file_kwitansi ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nominal Bayar </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_sma->nominal_bayar); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Transaksi </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_sma->status_transaksi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tagihan Kadaluarsa </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_sma->expired_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_sma->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_sma->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('transaksi_lain_sma_update', function() use ($transaksi_lain_sma){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit transaksi_lain_sma (Ctrl+e)" href="<?= site_url('administrator/transaksi_lain_sma/edit/'.$transaksi_lain_sma->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Transaksi Lain Sma']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/transaksi_lain_sma/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Transaksi Lain Sma']); ?></a>
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
