
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pegawai Slip Lain      <small><?= cclang('detail', ['Pegawai Slip Lain']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pegawai_slip_custom'); ?>">Pegawai Slip Lain</a></li>
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
                     <h3 class="widget-user-username">Pegawai Slip Lain</h3>
                     <h5 class="widget-user-desc">Detail Pegawai Slip Lain</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pegawai_slip_custom" id="form_pegawai_slip_custom" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Slip </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->id_slip); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Slip </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->nama_slip); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">NPP </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Golongan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->golongan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jabatan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->jabatan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Gaji Pokok </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->gaji_pokok); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Istri </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_istri); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Anak </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_anak); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Pengelolaan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_pengelolaan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Jabatan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_jabatan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Kesejahteraan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_kesejahteraan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Masa Kerja </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_masa_kerja); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Fungsional </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_fungsional); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Kehadiran </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->total_kehadiran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Kehadiran </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_kehadiran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Mengajar </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->total_mengajar); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Mengajar </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_mengajar); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Piket </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->total_piket); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Rupiah Per Piket </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->rupiah_per_piket); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Piket </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_piket); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Wali Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_wali_kelas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Pembina </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_pembina); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Insentif FT </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->insentif_ft); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tunjangan Insentif </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->tunjangan_insentif); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Bonus </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->bonus); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Honor </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->honor); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">THR </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->thr); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Gaji Ke 14 </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->gaji14); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">PPH21 </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->pph21); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Penghasilan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->total_penghasilan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Potongan </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->total_potongan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Total Diterima </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->total_diterima); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kwitansi </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->kwitansi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Periode Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->periode_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Periode Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($pegawai_slip_custom->periode_selesai); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pegawai_slip_custom_update', function() use ($pegawai_slip_custom){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pegawai_slip_custom (Ctrl+e)" href="<?= site_url('administrator/pegawai_slip_custom/edit/'.$pegawai_slip_custom->id_slip); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pegawai Slip Custom']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pegawai_slip_custom/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pegawai Slip Custom']); ?></a>
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
