<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Detail Pelamar <small>lamaran id <?= (int) $app->id_application; ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/career_pelamar'); ?>">Pelamar</a></li>
      <li class="active">Detail</li>
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
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/app.png" alt="Pelamar">
                     </div>
                     <h3 class="widget-user-username"><?= html_escape($app->full_name); ?></h3>
                     <h5 class="widget-user-desc"><?= html_escape($app->email); ?> — lamaran <?= html_escape($app->posting_title); ?></h5>
                     <hr>
                  </div>

                  <div style="padding:6px 4px">
                     <span class="label label-primary">ID <?= (int) $app->id_application; ?></span>
                     <span class="label
                        <?php
                           $cls = array('draft'=>'label-default','submitted'=>'label-primary','in_process'=>'label-warning','accepted'=>'label-success','not_accepted'=>'label-danger','withdrawn'=>'label-default');
                           $lbl = array('draft'=>'Draf','submitted'=>'Terkirim','in_process'=>'Dalam Proses','accepted'=>'Diterima','not_accepted'=>'Tidak Diterima','withdrawn'=>'Dibatalkan');
                           echo isset($cls[$app->status]) ? $cls[$app->status] : 'label-default';
                        ?>">
                        <?= isset($lbl[$app->status]) ? $lbl[$app->status] : html_escape($app->status); ?></span>
                     <span class="label label-default">Dibuat <?= html_escape($app->created_at); ?></span>
                     <span class="label label-default">Kirim <?= $app->submitted_at !== NULL ? html_escape($app->submitted_at) : '-'; ?></span>
                     <span class="label label-default">Lowongan tutup <?= html_escape($app->closing_date); ?></span>
                  </div>

                  <!-- Tata kelola wizard: tahap -->
                  <div class="box-header with-border"><h3 class="box-title">Tahapan lamaran</h3></div>
                  <div class="box-body">
                     <table class="table table-bordered">
                        <thead>
                           <tr><th width="60">Urut</th><th>Tahap</th><th width="100">Mode</th><th width="110">Status</th><th>Tanggal</th><th>Jadwal</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($tahapan as $t): ?>
                           <tr>
                              <td><?= (int) $t->stage_order; ?></td>
                              <td><?= html_escape($t->name); ?></td>
                              <td><span class="label <?= $t->mode === 'online' ? 'bg-olive' : 'bg-navy'; ?>"><?= html_escape($t->mode); ?></span></td>
                              <td><span class="label label-default"><?= html_escape($t->status); ?></span></td>
                              <td><?= $t->scheduled_at !== NULL ? html_escape($t->scheduled_at) : '-'; ?></td>
                              <td>
                                 <?php if ($t->is_published): ?>
                                    <span class="label bg-blue">Terjadwal</span>
                                    <?= html_escape($t->schedule_datetime); ?>
                                    <?php if ($t->channel): ?> — <?= html_escape($t->channel); ?><?php endif; ?>
                                    <?php if ($t->note): ?><br><small class="text-muted"><?= html_escape($t->note); ?></small><?php endif; ?>
                                 <?php else: ?>
                                    <span class="text-muted">Belum terjadwal</span>
                                 <?php endif; ?>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        </tbody>
                     </table>
                  </div>

                  <!-- snapshot -->
                  <div class="box-header with-border"><h3 class="box-title">Profil (snapshot saat submit)</h3></div>
                  <div class="box-body">
                     <?php if ($snapshot !== NULL):
                        // Lake snapshot profile_json — dekodifikasi menjadi objek.
                        $snap = json_decode($snapshot->profile_json);
                        if (!is_object($snap)) { $snap = new stdClass(); }
                        $rows = array(
                           'Nama lengkap'   => $snap->candidate->full_name ?? '-',
                           'Nomor HP'       => $snap->profile->phone ?? '-',
                           'Tempat lahir'   => $snap->place_of_birth ?? '-',
                           'Tanggal lahir'  => $snap->profile->date_of_birth ?? '-',
                           'Jenis kelamin'  => $snap->profile->gender ?? '-',
                           'Alamat'         => $snap->profile->address ?? '-',
                           'Ringkasan diri' => $snap->profile->summary ?? '-',
                        );
                     ?>
                        <table class="table">
                           <?php foreach ($rows as $lbl_ => $val_): ?>
                              <tr><th width="250"><?= $lbl_; ?></th><td><?= nl2br(html_escape((string) $val_)); ?></td></tr>
                           <?php endforeach; ?>
                           <tr><th>Disimpan</th><td><?= html_escape($snapshot->snapshot_at); ?></td></tr>
                        </table>
                     <?php else: ?>
                        <p class="text-muted">Snapshot belum tersimpan (lamaran belum di-submit).</p>
                     <?php endif; ?>

                     <!-- item profil kandidat (dari snapshot) -->
                     <?php $items_ = isset($snap->items) && is_array($snap->items) ? $snap->items : array(); ?>
                     <?php if ($items_): ?>
                     <h4>Item profil riwayat/kualifikasi</h4>
                     <table class="table table-striped">
                        <thead><tr><th width="140">Tipe</th><th>Judul</th><th>Institusi</th><th width="200">Periode</th><th>Deskripsi</th></tr></thead>
                        <tbody>
                        <?php foreach ($items_ as $it): ?>
                           <tr>
                              <td><span class="label label-default"><?= html_escape($it->item_type ?? '-'); ?></span></td>
                              <td><?= html_escape($it->title ?? '-'); ?></td>
                              <td><?= html_escape($it->institution ?? '-'); ?></td>
                              <td><?= html_escape(($it->start_date ?? '') ?: '-'); ?> → <?= html_escape(($it->end_date ?? '') ?: '-'); ?></td>
                              <td><?= nl2br(html_escape($it->description ?? '')); ?></td>
                           </tr>
                        <?php endforeach; ?>
                        </tbody>
                     </table>
                     <?php else: ?>
                        <p class="text-muted small">Tidak ada item profil tercatat pada snapshot.</p>
                     <?php endif; ?>
                  </div>

                  <!-- persyaratan -->
                  <div class="box-header with-border"><h3 class="box-title">Persyaratan lamaran</h3></div>
                  <div class="box-body">
                     <table class="table table-bordered table-striped">
                        <thead><tr><th>Nama</th><th width="110">Tipe</th><th width="80">Wajib</th><th>Jawaban / Dokumen</th><th width="90">Verifikasi</th></tr></thead>
                        <tbody>
                        <?php
                           $req_lbl = array('not_uploaded'=>'Belum diunggah','pending_review'=>'Menunggu verifikasi','verified'=>'Terverifikasi','revision_requested'=>'Perlu revisi','rejected'=>'Ditolak');
                           $req_cls = array('not_uploaded'=>'label-default','pending_review'=>'label-warning ','verified'=>'label-success','revision_requested'=>'label-orange','rejected'=>'label-danger');
                        ?>
                        <?php foreach ($persyaratan as $r): ?>
                           <tr>
                              <td><?= html_escape($r->requirement_name); ?>
                                 <?php if ($r->allowed_formats): ?><small class="text-muted">(<?= html_escape($r->allowed_formats); ?>, maks <?= (int) $r->max_file_size_kb; ?> KB)</small><?php endif; ?>
                              </td>
                              <td><span class="label <?= $r->input_type === 'document' ? 'bg-navy' : 'label-default'; ?>"><?= html_escape($r->input_type); ?></span></td>
                              <td><?= $r->requirement_type === 'required' ? '<span class="label label-danger">Wajib</span>' : '<span class="label label-default">Opsional</span>'; ?></td>
                              <td>
                                 <?php if ($r->input_type === 'document'): ?>
                                    <?php if ($r->id_application_document): ?>
                                       <i class="fa fa-file-pdf-o"></i>
                                       <a href="<?= site_url('administrator/career_pelamar/unduh/' . $r->id_application_document . '?app=' . $app->id_application); ?>">v<?= html_escape($r->version); ?> — <?= html_escape($r->original_name); ?></a>
                                       <small class="text-muted">(<?= number_format((int) $r->file_size / 1024, 1); ?> KB, <?= html_escape($r->uploaded_at); ?>)</small>
                                    <?php else: ?>
                                       <span class="text-muted">Belum diunggah</span>
                                    <?php endif; ?>
                                 <?php else: ?>
                                    <?= nl2br(html_escape($r->answer_text !== NULL && $r->answer_text !== '' ? $r->answer_text : '-')); ?>
                                 <?php endif; ?>
                              </td>
                              <td>
                                 <span class="label <?= isset($req_cls[$r->requirement_status]) ? $req_cls[$r->requirement_status] : 'label-default'; ?>"><?= isset($req_lbl[$r->requirement_status]) ? $req_lbl[$r->requirement_status] : html_escape($r->requirement_status); ?></span>
                                 <?php if ($r->review_note): ?><br><small class="text-muted"><?= html_escape($r->review_note); ?></small><?php endif; ?>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        </tbody>
                     </table>
                     <p class="text-muted small">Verifikasi dokumen &amp; keputusan administrasi merupakan Fase 7 — modul ini hanya menampilkan data.</p>
                  </div>

                  <!-- notifikasi -->
                  <div class="box-header with-border"><h3 class="box-title">Notifikasi kandidat</h3></div>
                  <div class="box-body">
                     <table class="table table-striped">
                        <thead><tr><th width="170">Waktu</th><th width="140">Tipe</th><th>Judul</th><th>Isi</th><th width="60">Baca</th></tr></thead>
                        <tbody>
                        <?php foreach ($notifikasis as $n): ?>
                           <tr class="<?= $n->is_read ? '' : 'active'; ?>">
                              <td><?= html_escape($n->created_at); ?></td>
                              <td><span class="label label-default"><?= html_escape($n->type); ?></span></td>
                              <td><?= html_escape($n->title); ?></td>
                              <td><?= html_escape($n->body); ?></td>
                              <td><?= $n->is_read ? '<span class="label bg-olive">sudah</span>' : '<span class="label label-info">baru</span>'; ?></td>
                           </tr>
                        <?php endforeach; ?>
                        </tbody>
                     </table>
                  </div>

                  <!-- audit -->
                  <div class="box-header with-border"><h3 class="box-title">Riwayat audit</h3></div>
                  <div class="box-body">
                     <table class="table table-striped">
                        <thead><tr><th width="170">Waktu</th><th width="120">Aktor</th><th>Aksi</th><th width="120">Objek</th><th>Detail</th></tr></thead>
                        <tbody>
                        <?php foreach ($audits as $a): ?>
                           <tr>
                              <td><?= html_escape($a->created_at); ?></td>
                              <td><?= html_escape($a->actor_type); ?></td>
                              <td><span class="label label-default"><?= html_escape($a->action); ?></span></td>
                              <td><?= html_escape($a->object_type); ?></td>
                              <td><code class="small"><?= html_escape($a->detail_json); ?></code></td>
                           </tr>
                        <?php endforeach; ?>
                        </tbody>
                     </table>
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>
</section>
