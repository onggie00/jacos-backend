<!-- Content Header -->
<section class="content-header">
   <h1>
      Presensi Pramuka      <small><?= cclang('detail', ['Presensi Pramuka']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a href="<?= site_url('administrator/presensi_pramuka'); ?>">Presensi Pramuka</a></li>
      <li class="active"><?= cclang('detail'); ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-body">

               <div class="box box-widget widget-user-2">
                  <div class="widget-user-header">
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/view.png" alt="User Avatar">
                     </div>
                     <h3 class="widget-user-username">Presensi Pramuka</h3>
                     <h5 class="widget-user-desc">Detail Presensi Pramuka</h5>
                     <hr>
                  </div>

                  <div class="form-horizontal">

                     <div class="row">
                        <div class="col-md-4">
                           <div class="info-box bg-aqua">
                              <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                              <div class="info-box-content">
                                 <span class="info-box-text">Tanggal & Hari</span>
                                 <span class="info-box-number"><?= _ent($presensi_pramuka->hari); ?>, <?= _ent($presensi_pramuka->tanggal); ?></span>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="info-box bg-green">
                              <span class="info-box-icon"><i class="fa fa-user"></i></span>
                              <div class="info-box-content">
                                 <span class="info-box-text">Nama Siswa</span>
                                 <span class="info-box-number"><?= _ent($presensi_pramuka->nama_lengkap); ?></span>
                                 <span class="progress-description">Kelas: <?= _ent($presensi_pramuka->kelas); ?> | <?= _ent($presensi_pramuka->jenjang); ?></span>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="info-box bg-yellow">
                              <span class="info-box-icon"><i class="fa fa-star"></i></span>
                              <div class="info-box-content">
                                 <span class="info-box-text">Total Nilai</span>
                                 <span class="info-box-number"><?= _ent($presensi_pramuka->total_nilai); ?> (<?= $predikat; ?>)</span>
                                 <span class="progress-description"><?= $deskripsi; ?></span>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12">
                           <div class="box box-solid box-primary">
                              <div class="box-header with-border">
                                 <h3 class="box-title"><i class="fa fa-list-alt"></i> Rincian Rubrik Penilaian Pramuka</h3>
                              </div>
                              <div class="box-body no-padding">
                                 <table class="table table-striped table-bordered">
                                    <thead>
                                       <tr class="bg-gray">
                                          <th>Komponen Penilaian</th>
                                          <th>Status / Keterangan</th>
                                          <th class="text-center" style="width:120px">Nilai (0-100)</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       <tr>
                                          <td><strong>1. Kehadiran</strong></td>
                                          <td>
                                             <?php
                                             $status = $presensi_pramuka->status_hadir;
                                             $badge = strtolower($status) == 'hadir' ? 'label-success' : (strtolower($status) == 'terlambat' ? 'label-warning' : (strtolower($status) == 'izin' ? 'label-info' : (strtolower($status) == 'sakit' ? 'label-primary' : 'label-danger')));
                                             ?>
                                             <span class="label <?= $badge; ?>"><?= _ent($status); ?></span>
                                          </td>
                                          <td class="text-center"><strong><?= _ent($presensi_pramuka->kehadiran); ?></strong>/100</td>
                                       </tr>
                                       <tr>
                                          <td><strong>2. Kelengkapan Atribut</strong> (Bedge, Hasduk & Ring)</td>
                                          <td><?= _ent($presensi_pramuka->status_kelengkapan); ?></td>
                                          <td class="text-center"><strong><?= _ent($presensi_pramuka->kelengkapan); ?></strong>/100</td>
                                       </tr>
                                       <tr>
                                          <td><strong>3. Keaktifan</strong></td>
                                          <td><?= _ent($presensi_pramuka->status_keaktifan); ?></td>
                                          <td class="text-center"><strong><?= _ent($presensi_pramuka->keaktifan); ?></strong>/100</td>
                                       </tr>
                                       <tr class="info">
                                          <td colspan="2"><strong>TOTAL NILAI AKHIR (Rata-rata 3 Komponen)</strong></td>
                                          <td class="text-center"><strong style="font-size:18px"><?= _ent($presensi_pramuka->total_nilai); ?></strong></td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12">
                           <div class="box box-solid box-default">
                              <div class="box-header with-border">
                                 <h3 class="box-title"><i class="fa fa-info-circle"></i> Meta Data</h3>
                              </div>
                              <div class="box-body no-padding">
                                 <table class="table table-bordered">
                                    <tr><th style="width:25%">Dibuat Pada</th><td><?= _ent($presensi_pramuka->created_at); ?></td></tr>
                                    <tr><th>Terakhir Diupdate</th><td><?= _ent($presensi_pramuka->updated_at); ?></td></tr>
                                    <tr><th>Diupdate Oleh</th><td><?= _ent($presensi_pramuka->updated_by); ?></td></tr>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="view-nav" style="margin-top:20px">
                        <?php is_allowed('presensi_pramuka_update', function() use ($presensi_pramuka){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit presensi_pramuka (Ctrl+e)" href="<?= site_url('administrator/presensi_pramuka/edit/'.$presensi_pramuka->id_presensi_pramuka); ?>"><i class="fa fa-edit"></i> Edit</a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/presensi_pramuka/'); ?>"><i class="fa fa-undo"></i> <?= cclang('go_list_button', ['Presensi Pramuka']); ?></a>
                     </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
