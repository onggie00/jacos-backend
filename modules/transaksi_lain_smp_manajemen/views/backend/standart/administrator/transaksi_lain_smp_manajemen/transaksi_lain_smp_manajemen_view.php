
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Manajemen Tagihan SMP      <small><?= cclang('detail', ['Manajemen Tagihan SMP']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/transaksi_lain_smp_manajemen'); ?>">Manajemen Tagihan SMP</a></li>
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
                     <h3 class="widget-user-username">Manajemen Tagihan SMP</h3>
                     <h5 class="widget-user-desc">Detail Manajemen Tagihan SMP</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_transaksi_lain_smp_manajemen" id="form_transaksi_lain_smp_manajemen" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kategori </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->transaksi_lain_kategori_nama_kategori); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Tagihan </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->nama_transaksi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nominal </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->nominal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->tahun_ajaran_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tingkatan </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->tingkatan_smp_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kelas </label>

                        <div class="col-sm-8">
                          <?= join_multi_select($transaksi_lain_smp_manajemen->id_kelas, 'kelas_smp', 'id_kelas_smp', 'nama_kelas'); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa </label>

                        <div class="col-sm-8">
                          <?= join_multi_select($transaksi_lain_smp_manajemen->id_siswa, 'siswa_smp_aktif', 'id_siswa_smp_aktif', 'nama_lengkap'); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tagihan Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->tanggal_tagihan_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tagihan Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->tanggal_tagihan_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Bank </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->tipe_bank); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created By </label>

                        <div class="col-sm-8">
                           <?= _ent($transaksi_lain_smp_manajemen->aauth_users_email); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('transaksi_lain_smp_manajemen_update', function() use ($transaksi_lain_smp_manajemen){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit transaksi_lain_smp_manajemen (Ctrl+e)" href="<?= site_url('administrator/transaksi_lain_smp_manajemen/edit/'.$transaksi_lain_smp_manajemen->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Transaksi Lain Smp Manajemen']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/transaksi_lain_smp_manajemen/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Transaksi Lain Smp Manajemen']); ?></a>
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
