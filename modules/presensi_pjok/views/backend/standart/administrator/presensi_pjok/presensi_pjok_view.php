<script type="text/javascript">
</script>

<style>
.badge-predikat { padding: 5px 12px; border-radius: 4px; font-weight: bold; font-size: 14px; color: #fff; }
.badge-a { background: #27ae60; }
.badge-b { background: #3498db; }
.badge-c { background: #f39c12; }
.badge-d { background: #e67e22; }
.badge-e { background: #e74c3c; }
.nilai-bar-wrap { display: flex; align-items: center; gap: 10px; }
.nilai-bar { flex: 1; background: #eee; border-radius: 4px; height: 10px; overflow: hidden; }
.nilai-bar-fill { height: 100%; border-radius: 4px; transition: width 0.3s; }
</style>

<!-- Content Header -->
<section class="content-header">
   <h1>
      Presensi PJOK      <small><?= cclang('detail', ['Presensi PJOK']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/presensi_pjok'); ?>">Presensi PJOK</a></li>
      <li class="active"><?= cclang('detail'); ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-body ">
               <div class="box box-widget widget-user-2">
                  <div class="widget-user-header ">
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/view.png" alt="User Avatar">
                     </div>
                     <h3 class="widget-user-username">Presensi PJOK</h3>
                     <h5 class="widget-user-desc">Detail Presensi PJOK</h5>
                     <hr>
                  </div>

                  <div class="form-horizontal" name="form_presensi_pjok" id="form_presensi_pjok">

                     <!-- Info Cards -->
                     <div class="row" style="margin-bottom:20px">
                        <div class="col-md-4">
                           <div class="info-box bg-aqua">
                              <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                              <div class="info-box-content">
                                 <span class="info-box-text">Tanggal</span>
                                 <span class="info-box-number"><?= _ent($presensi_pjok->hari); ?>, <?= _ent($presensi_pjok->tanggal); ?></span>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="info-box bg-green">
                              <span class="info-box-icon"><i class="fa fa-user"></i></span>
                              <div class="info-box-content">
                                 <span class="info-box-text">Siswa</span>
                                 <span class="info-box-number"><?= _ent($presensi_pjok->nama_lengkap); ?></span>
                                 <div class="progress"><div class="progress-bar" style="width:100%"></div></div>
                                 <span class="progress-description">Kelas: <?= _ent($presensi_pjok->kelas); ?> | <?= _ent($presensi_pjok->jenjang); ?></span>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="info-box bg-yellow">
                              <span class="info-box-icon"><i class="fa fa-star"></i></span>
                              <div class="info-box-content">
                                 <span class="info-box-text">Total Nilai</span>
                                 <span class="info-box-number"><?= _ent($presensi_pjok->total_nilai); ?></span>
                                 <div class="progress"><div class="progress-bar" style="width:<?= (float) $presensi_pjok->total_nilai; ?>%"></div></div>
                                 <span class="progress-description"><span class="badge-predikat <?= strtolower($predikat) == 'a' ? 'badge-a' : (strtolower($predikat) == 'b' ? 'badge-b' : (strtolower($predikat) == 'c' ? 'badge-c' : (strtolower($predikat) == 'd' ? 'badge-d' : 'badge-e'))); ?>"><?= $predikat; ?></span> <?= $deskripsi; ?></span>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Detail Table -->
                     <div class="box box-solid box-primary">
                        <div class="box-header with-border">
                           <h3 class="box-title"><i class="fa fa-list-alt"></i> Detail Penilaian PJOK</h3>
                        </div>
                        <div class="box-body table-responsive no-padding">
                           <table class="table table-bordered table-striped">
                              <thead style="background:#f8f9fa">
                                 <tr>
                                    <th style="width:30%">Aspek</th>
                                    <th style="width:20%" class="text-center">Nilai</th>
                                    <th style="width:50%">Progress</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr>
                                    <td><strong>Kehadiran</strong></td>
                                    <td class="text-center"><strong><?= _ent($presensi_pjok->kehadiran); ?></strong>/100</td>
                                    <td>
                                       <?php $k_val = (int) $presensi_pjok->kehadiran; $k_color = $k_val >= 90 ? '#27ae60' : ($k_val >= 80 ? '#3498db' : ($k_val >= 70 ? '#f39c12' : ($k_val >= 60 ? '#e67e22' : '#e74c3c'))); ?>
                                       <div class="nilai-bar-wrap">
                                          <div class="nilai-bar"><div class="nilai-bar-fill" style="width:<?= $k_val; ?>%;background:<?= $k_color; ?>"></div></div>
                                          <small style="color:<?= $k_color; ?>;font-weight:600"><?= $k_val; ?>%</small>
                                       </div>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td><strong>Status Keaktifan</strong></td>
                                    <td class="text-center">
                                       <?php
                                       $sk = $presensi_pjok->status_keaktifan;
                                       $sk_class = 'label-default';
                                       if ($sk == 'Sangat Aktif') $sk_class = 'label-success';
                                       elseif ($sk == 'Aktif') $sk_class = 'label-info';
                                       elseif ($sk == 'Cukup Aktif' || $sk == 'Cukup_aktif') $sk_class = 'label-warning';
                                       ?>
                                       <span class="label <?= $sk_class; ?>"><?= _ent($sk); ?></span>
                                    </td>
                                    <td>-</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Keaktifan</strong></td>
                                    <td class="text-center"><strong><?= _ent($presensi_pjok->keaktifan); ?></strong>/100</td>
                                    <td>
                                       <?php $a_val = (int) $presensi_pjok->keaktifan; $a_color = $a_val >= 90 ? '#27ae60' : ($a_val >= 80 ? '#3498db' : ($a_val >= 70 ? '#f39c12' : ($a_val >= 60 ? '#e67e22' : '#e74c3c'))); ?>
                                       <div class="nilai-bar-wrap">
                                          <div class="nilai-bar"><div class="nilai-bar-fill" style="width:<?= $a_val; ?>%;background:<?= $a_color; ?>"></div></div>
                                          <small style="color:<?= $a_color; ?>;font-weight:600"><?= $a_val; ?>%</small>
                                       </div>
                                    </td>
                                 </tr>
                                 <tr style="background:#f0f0f0">
                                    <td><strong>Total Nilai</strong></td>
                                    <td class="text-center"><strong style="font-size:18px"><?= _ent($presensi_pjok->total_nilai); ?></strong></td>
                                    <td><span class="badge-predikat <?= strtolower($predikat) == 'a' ? 'badge-a' : (strtolower($predikat) == 'b' ? 'badge-b' : (strtolower($predikat) == 'c' ? 'badge-c' : (strtolower($predikat) == 'd' ? 'badge-d' : 'badge-e'))); ?>"><?= $predikat; ?></span> <?= $deskripsi; ?></td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>

                     <!-- Status & Info -->
                     <div class="row">
                        <div class="col-md-6">
                           <div class="box box-solid box-default">
                              <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-check-circle"></i> Status Kehadiran</h3></div>
                              <div class="box-body">
                                 <?php
                                 $status = $presensi_pjok->status_hadir;
                                 $status_class = 'label-default';
                                 if ($status == 'Hadir') $status_class = 'label-success';
                                 elseif ($status == 'Terlambat') $status_class = 'label-warning';
                                 elseif ($status == 'Sakit') $status_class = 'label-warning';
                                 elseif ($status == 'Izin') $status_class = 'label-info';
                                 else $status_class = 'label-danger';
                                 ?>
                                 <span class="label <?= $status_class; ?>" style="font-size:16px;padding:10px 20px"><?= _ent($status); ?></span>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="box box-solid box-default">
                              <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-clock-o"></i> Informasi Waktu</h3></div>
                              <div class="box-body">
                                 <table class="table table-bordered mb-0">
                                    <tr><th style="width:40%">Dibuat</th><td><?= _ent($presensi_pjok->created_at); ?></td></tr>
                                    <tr><th>Diupdate</th><td><?= _ent($presensi_pjok->updated_at); ?></td></tr>
                                    <tr><th>Oleh</th><td><?= _ent($presensi_pjok->updated_by); ?></td></tr>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>

                     <br><br>

                     <div class="view-nav">
                        <?php is_allowed('presensi_pjok_update', function() use ($presensi_pjok){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit presensi_pjok (Ctrl+e)" href="<?= site_url('administrator/presensi_pjok/edit/'.$presensi_pjok->id_presensi_pjok); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Presensi PJOK']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/presensi_pjok/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Presensi PJOK']); ?></a>
                     </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
