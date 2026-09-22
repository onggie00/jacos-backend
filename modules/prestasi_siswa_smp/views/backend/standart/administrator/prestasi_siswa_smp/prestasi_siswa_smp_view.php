
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Prestasi Siswa SMP      <small><?= cclang('detail', ['Prestasi Siswa SMP']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/prestasi_siswa_smp'); ?>">Prestasi Siswa SMP</a></li>
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
                     <h3 class="widget-user-username">Prestasi Siswa SMP</h3>
                     <h5 class="widget-user-desc">Detail Prestasi Siswa SMP</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_prestasi_siswa_smp" id="form_prestasi_siswa_smp" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_smp->id_prestasi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Prestasi </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_smp->nama_prestasi); ?>
                        </div>
                    </div>

                    <?php if(!empty($prestasi_siswa_smp->judul)): ?>
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Judul </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_smp->judul); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Raih </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_smp->tgl_raih); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Juara </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_smp->juara); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Prestasi </label>
                        <div class="col-sm-8">
                             <?php if (!empty($prestasi_siswa_smp->file_prestasi)): ?>
                              <?php if (is_image($prestasi_siswa_smp->file_prestasi)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_siswa_smp/' . $prestasi_siswa_smp->file_prestasi; ?>">
                                <img src="<?= BASE_URL . 'uploads/prestasi_siswa_smp/' . $prestasi_siswa_smp->file_prestasi; ?>" class="image-responsive" alt="image prestasi_siswa_smp" title="file_prestasi prestasi_siswa_smp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/prestasi_siswa_smp/' . $prestasi_siswa_smp->file_prestasi; ?>">
                                 <img src="<?= get_icon_file($prestasi_siswa_smp->file_prestasi); ?>" class="image-responsive" alt="image prestasi_siswa_smp" title="file_prestasi <?= $prestasi_siswa_smp->file_prestasi; ?>" width="40px"> 
                               <?= $prestasi_siswa_smp->file_prestasi ?>
                               </a>
                               </label>
                              <?php endif; ?>
                              <?php else: ?>
                              <span class="text-muted">-</span>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Foto </label>
                        <div class="col-sm-8">
                             <?php if (!empty($prestasi_siswa_smp->foto_prestasi)): ?>
                              <?php if (is_image($prestasi_siswa_smp->foto_prestasi)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_siswa_smp/' . $prestasi_siswa_smp->foto_prestasi; ?>">
                                <img src="<?= BASE_URL . 'uploads/prestasi_siswa_smp/' . $prestasi_siswa_smp->foto_prestasi; ?>" class="image-responsive" alt="image prestasi_siswa_smp" title="foto_prestasi prestasi_siswa_smp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/prestasi_siswa_smp/' . $prestasi_siswa_smp->foto_prestasi; ?>">
                                 <img src="<?= get_icon_file($prestasi_siswa_smp->foto_prestasi); ?>" class="image-responsive" alt="image prestasi_siswa_smp" title="foto_prestasi <?= $prestasi_siswa_smp->foto_prestasi; ?>" width="40px"> 
                               <?= $prestasi_siswa_smp->foto_prestasi ?>
                               </a>
                               </label>
                              <?php endif; ?>
                              <?php else: ?>
                              <span class="text-muted">-</span>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa </label>

                        <div class="col-sm-8">
                           <?php if (!empty($prestasi_siswa_smp->id_siswa)): ?>
                           <?= anchor('administrator/siswa_smp_aktif/view/'.$prestasi_siswa_smp->id_siswa.'?popup=show', $prestasi_siswa_smp->siswa_smp_aktif_nama_lengkap, array('class' => 'popup-view')); ?>
                           <?php else: ?>
                           <span class="text-muted">-</span>
                           <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kelas </label>

                        <div class="col-sm-8">
                           <?= !empty($prestasi_siswa_smp->kelas_smp_label) ? _ent($prestasi_siswa_smp->kelas_smp_label) : '<span class="text-muted">-</span>'; ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tingkatan Prestasi </label>

                        <div class="col-sm-8">
                           <?= !empty($prestasi_siswa_smp->jenis_prestasi) ? _ent($prestasi_siswa_smp->jenis_prestasi) : _ent($prestasi_siswa_smp->jenis_prestasi_id); ?>
                        </div>
                    </div>

                    <!-- Pusprenas Info -->
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kurasi Pusprenas </label>

                        <div class="col-sm-8">
                           <?php
                             $pusprenas_val = isset($prestasi_siswa_smp->kurasi_pusprenas) ? strtoupper($prestasi_siswa_smp->kurasi_pusprenas) : 'TIDAK';
                             if ($pusprenas_val === 'YA') {
                                 echo '<span class="label label-success"><i class="fa fa-check-circle"></i> Ya — Tercatat di Pusprenas</span>';
                             } else {
                                 echo '<span class="label label-default">Tidak</span>';
                             }
                           ?>
                        </div>
                    </div>

                    <?php if (!empty($prestasi_siswa_smp->link_pusprenas)): ?>
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Link Pusprenas </label>

                        <div class="col-sm-8">
                           <a href="<?= _ent($prestasi_siswa_smp->link_pusprenas); ?>" target="_blank" class="btn btn-sm btn-default">
                              <i class="fa fa-external-link"></i> Buka Link Pusprenas
                           </a>
                           <br><small class="text-muted"><?= _ent($prestasi_siswa_smp->link_pusprenas); ?></small>
                        </div>
                    </div>
                    <?php endif; ?>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Konten </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_smp->konten); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Posting </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_smp->tanggal_posting); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status </label>

                        <div class="col-sm-8">
                           <?php
                             $st = isset($prestasi_siswa_smp->is_approved) ? (int)$prestasi_siswa_smp->is_approved : 0;
                             if ($st === 1) {
                                 echo '<span class="label label-success"><i class="fa fa-check"></i> Disetujui</span>';
                             } elseif ($st === 2) {
                                 echo '<span class="label label-danger"><i class="fa fa-times"></i> Ditolak</span>';
                             } else {
                                 echo '<span class="label label-warning"><i class="fa fa-clock-o"></i> Belum Disetujui</span>';
                             }
                           ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('prestasi_siswa_smp_update', function() use ($prestasi_siswa_smp){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit prestasi_siswa_smp (Ctrl+e)" href="<?= site_url('administrator/prestasi_siswa_smp/edit/'.$prestasi_siswa_smp->id_prestasi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Prestasi Siswa Smp']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/prestasi_siswa_smp/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Prestasi Siswa Smp']); ?></a>
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
