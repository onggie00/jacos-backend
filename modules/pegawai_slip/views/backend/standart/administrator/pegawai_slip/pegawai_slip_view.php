
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Slip Gaji & Tunjangan Pegawai      <small><?= cclang('detail', ['Slip Gaji & Tunjangan Pegawai']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pegawai_slip'); ?>">Slip Gaji & Tunjangan Pegawai</a></li>
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
                     <h3 class="widget-user-username">Slip Gaji & Tunjangan Pegawai</h3>
                     <h5 class="widget-user-desc">Detail Slip Gaji & Tunjangan Pegawai</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pegawai_slip" id="form_pegawai_slip" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Slip </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->id_slip); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pegawai </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->pegawai_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">NPP </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Golongan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->golongan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jabatan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->jabatan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Gaji Pokok </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->gaji_pokok); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Istri </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_istri); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Anak </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_anak); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Pengelolaan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_pengelolaan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Jabatan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_jabatan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Kesejahteraan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_kesejahteraan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Masa Kerja </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_masa_kerja); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Fungsional </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_fungsional); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Kehadiran </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->total_kehadiran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Rupiah Per Kehadiran </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->rupiah_per_kehadiran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Kehadiran </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_kehadiran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Mengajar </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->total_mengajar); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Rupiah Per Mengajar </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->rupiah_per_mengajar); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Mengajar </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_mengajar); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Piket </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->total_piket); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Rupiah Per Piket </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->rupiah_per_piket); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Piket </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_piket); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Wali Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_wali_kelas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Pembina </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_pembina); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Insentif France Track </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->insentif_ft); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Insentif </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->tunjangan_insentif); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Bonus </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->bonus); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Honor </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->honor); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Periode Mulai </label>

                        <div class="col-sm-8">
                           <?= formatTanggal($pegawai_slip->periode_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Periode Selesai </label>

                        <div class="col-sm-8">
                           <?= formatTanggal($pegawai_slip->periode_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pegawai_slip_update', function() use ($pegawai_slip){?>
                        <!<a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pegawai_slip (Ctrl+e)" href="<?= site_url('administrator/pegawai_slip/edit/' . $pegawai_slip->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pegawai Slip']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pegawai_slip/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pegawai Slip']); ?></a>
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
