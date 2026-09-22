
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Presensi FT      <small><?= cclang('detail', ['Presensi FT']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/presensi_ft'); ?>">Presensi FT</a></li>
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
                     <h3 class="widget-user-username">Presensi FT</h3>
                     <h5 class="widget-user-desc">Detail Presensi FT</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_presensi_ft" id="form_presensi_ft" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->siswa_ft_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">WIFI SSID </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->pengaturan_whitelist_ssid_nama_ssid); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">IP </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->wifi_ip); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Hari Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->hari_absen); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Waktu Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->waktu_absen); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->tanggal_absen); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->status_absen); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Alasan Keterlambatan </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->alasan_terlambat); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Izin </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->izin_siswa_ft_jenis_izin); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_ft->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('presensi_ft_update', function() use ($presensi_ft){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit presensi_ft (Ctrl+e)" href="<?= site_url('administrator/presensi_ft/edit/'.$presensi_ft->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Presensi Ft']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/presensi_ft/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Presensi Ft']); ?></a>
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
