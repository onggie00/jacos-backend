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

                  <!-- CSRF holder untuk AJAX aksi 6B (token per-sesi) -->
                  <span id="career_csrf_holder" style="display:none"><?= career_csrf_field(); ?></span>

                  <?php // tanda izin disiapkan controller (bisa_verifikasi/bisa_keputusan/bisa_jadwal/bisa_nilai) ?>

                  <?php
                     // #1 6B.2: tombol keputusan nonaktif sesuai D16 -
                     // draft/withdrawn tidak bisa; terima butuh 5 tahap passed.
                     $status_akhir = in_array($app->status, array('draft', 'withdrawn'), TRUE);
                     $tahap_lulus_semua = TRUE;
                     foreach ($tahapan as $t) { if ($t->status !== 'passed') { $tahap_lulus_semua = FALSE; break; } }
                  ?>
                  <?php if ($bisa_keputusan): ?>
                  <div style="padding:4px 8px">
                     <button type="button" class="btn btn-success career-terima" data-id="<?= (int) $app->id_application; ?>"
                        <?= ($status_akhir || $app->status === 'accepted' || ! $tahap_lulus_semua) ? 'disabled title="Semua 5 tahap harus lulus dulu & belum diterima (D16)"' : ''; ?>>
                        <i class="fa fa-check-square-o"></i> Status akhir: Diterima
                     </button>
                     <span class="help-block small" style="display:inline">Syarat: kelima tahap sudah lulus (D16). Aksi tercatat di audit log.</span>
                  </div>
                  <?php endif; ?>

                  <!-- Tata kelola wizard: tahap + aksi 6B -->
                  <div class="box-header with-border"><h3 class="box-title">Tahapan lamaran</h3></div>
                  <div class="box-body">
                     <table class="table table-bordered">
                        <thead>
                           <tr><th width="45">Urut</th><th>Tahap</th><th width="80">Mode</th><th width="105">Status</th><th style="min-width:300px">Jadwal (D16/D18)</th><th style="min-width:230px">Keputusan &amp; nilai</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($tahapan as $t): ?>
                           <tr>
                              <td><?= (int) $t->stage_order; ?></td>
                              <td><?= html_escape($t->name); ?></td>
                              <td><span class="label <?= $t->mode === 'online' ? 'bg-olive' : 'bg-navy'; ?>"><?= html_escape($t->mode); ?></span></td>
                              <td>
                                 <?php
                                    $st_cls = array('pending'=>'label-default','scheduled'=>'label-primary','passed'=>'label-success','failed'=>'label-danger');
                                    $st_lbl = array('pending'=>'Menunggu','scheduled'=>'Terjadwal','passed'=>'Lulus','failed'=>'Tidak Lulus');
                                 ?>
                                 <span class="label <?= isset($st_cls[$t->status]) ? $st_cls[$t->status] : 'label-default'; ?>">
                                    <?= isset($st_lbl[$t->status]) ? $st_lbl[$t->status] : html_escape($t->status); ?></span>
                                 <?= $t->decided_at ? '<br><small class="text-muted">' . html_escape($t->decided_at) . '</small>' : ''; ?>
                              </td>
                              <td style="font-size:12px">
                                 <?php if ((int) $t->is_published === 1): ?>
                                    <span class="label bg-blue">Terbit</span>
                                    <?= html_escape($t->schedule_datetime); ?>
                                    <?php if ($t->channel): ?> — <?= html_escape($t->channel); ?><?php endif; ?>
                                    <?php if ($t->note): ?><br><small class="text-muted"><?= html_escape($t->note); ?></small><?php endif; ?>
                                 <?php elseif ($t->schedule_datetime): ?>
                                    <span class="label label-warning">Draf jadwal</span>
                                    <?= html_escape($t->schedule_datetime); ?> <em class="text-muted">(belum diterbitkan)</em>
                                 <?php else: ?>
                                    <span class="text-muted">Belum terjadwal</span>
                                 <?php endif; ?>

                                 <?php if ($bisa_jadwal): ?>
                                 <form class="career-jadwal-form" data-sid="<?= (int) $t->id_application_stage; ?>"
                                       style="margin-top:6px;max-width:330px" onsubmit="return false">
                                    <div class="input-group input-group-sm" style="margin-bottom:3px">
                                       <input type="datetime-local" class="form-control career-jadwal-tanggal"
                                              value="<?= $t->schedule_datetime ? html_escape(str_replace(' ', 'T', substr($t->schedule_datetime, 0, 16))) : ''; ?>">
                                    </div>
                                    <input type="text" class="form-control career-jadwal-kanal" placeholder="Kanal audiensi / link (maks 200)" maxlength="200"
                                           value="<?= $t->channel ? html_escape($t->channel) : ''; ?>"
                                           style="margin-bottom:3px">
                                    <input type="text" class="form-control career-jadwal-catatan" placeholder="Catatan (maks 500)" maxlength="500"
                                           value="<?= $t->note ? html_escape($t->note) : ''; ?>"
                                           style="margin-bottom:3px">
                                    <button type="button" class="btn btn-flat btn-xs btn-primary career-jadwal" data-sid="<?= (int) $t->id_application_stage; ?>" data-aksi="simpan">Simpan</button>
                                    <button type="button" class="btn btn-flat btn-xs btn-success career-jadwal" data-sid="<?= (int) $t->id_application_stage; ?>" data-aksi="publikasi">Publikasikan</button>
                                    <button type="button" class="btn btn-flat btn-xs btn-warning career-jadwal" data-sid="<?= (int) $t->id_application_stage; ?>" data-aksi="batal">Batal publikasi</button>
                                    <button type="button" class="btn btn-flat btn-xs btn-danger career-jadwal" data-sid="<?= (int) $t->id_application_stage; ?>" data-aksi="hapus">Hapus</button>
                                 </form>
                                 <?php endif; ?>
                              </td>
                              <td style="font-size:12px">
                                 <?php if ($bisa_keputusan): ?>
                                    <?php
                                       // D16.2: tahap boleh diputuskan hanya bila tahap
                                       // sebelumnya lulus; draft/withdrawn terkunci.
                                       $prev_lulus = TRUE;
                                       foreach ($tahapan as $tp_) {
                                          if ((int) $tp_->stage_order >= (int) $t->stage_order) { break; }
                                          if ($tp_->status !== 'passed') { $prev_lulus = FALSE; }
                                       }
                                       $boleh_keputusan = $prev_lulus && ! $status_akhir;
                                    ?>
                                    <?php if (in_array($t->status, array('pending', 'scheduled'), TRUE)): ?>
                                       <button type="button" class="btn btn-flat btn-xs btn-success career-keputusan" data-n="<?= (int) $t->stage_order; ?>" data-keputusan="passed" <?= $boleh_keputusan ? '' : 'disabled title="Tahap sebelumnya belum lulus (D16.2)"'; ?>>Lulus</button>
                                       <button type="button" class="btn btn-flat btn-xs btn-danger career-keputusan" data-n="<?= (int) $t->stage_order; ?>" data-keputusan="failed" <?= $boleh_keputusan ? '' : 'disabled title="Tahap sebelumnya belum lulus (D16.2)"'; ?>>Tidak lulus</button>
                                    <?php else: ?>
                                       <button type="button" class="btn btn-flat btn-xs btn-warning career-koreksi" data-n="<?= (int) $t->stage_order; ?>" <?= $status_akhir ? 'disabled title="Lamaran draft/withdrawn"' : ''; ?>>Koreksi</button>
                                    <?php endif; ?>
                                 <?php endif; ?>
                                 <?php
                                    // #1 6B.2: nilai saat ini TAMPIL bagi pemegang
                                    // career_pelamar_score_view (D13 internal);
                                    // form ubah hanya bila career_pelamar_score_edit.
                                    $nilai_now = NULL;
                                    if ($bisa_score_view) {
                                       foreach ($nilai_rekap as $nr_) {
                                          if ((int) $nr_->id_application_stage === (int) $t->id_application_stage) { $nilai_now = $nr_; break; }
                                       }
                                    }
                                 ?>
                                 <?php if ($bisa_score_view): ?>
                                    <div class="small" style="margin-top:4px">
                                       <?php if ($nilai_now): ?>
                                          <span class="label bg-purple">Skor: <?= html_escape(number_format((float) $nilai_now->score, 2)); ?></span>
                                          <?php if ($nilai_now->rubric_note): ?><br><small class="text-muted"><?= html_escape($nilai_now->rubric_note); ?></small><?php endif; ?>
                                       <?php else: ?>
                                          <span class="text-muted">Belum dinilai</span>
                                       <?php endif; ?>
                                    </div>
                                 <?php endif; ?>
                                 <?php if ($bisa_score_edit): ?>
                                    <form class="career-nilai-form" style="margin-top:4px" onsubmit="return false">
                                       <input type="number" class="career-nilai-skor" placeholder="Skor 0–100" min="0" max="100" step="0.01"
                                              style="width:110px" value="<?= $nilai_now !== NULL ? html_escape(number_format((float) $nilai_now->score, 2, '.', '')) : ''; ?>">
                                       <input type="text" class="career-nilai-catatan" placeholder="Catatan internal" maxlength="2000" style="width:160px" value="<?= $nilai_now !== NULL && $nilai_now->rubric_note ? html_escape($nilai_now->rubric_note) : ''; ?>">
                                       <button type="button" class="btn btn-flat btn-xs btn-default career-nilai" data-sid="<?= (int) $t->id_application_stage; ?>">Simpan nilai</button>
                                    </form>
                                 <?php endif; ?>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        </tbody>
                     </table>
                     <p class="text-muted small">
                        D16.2: tahap dapat dijadwalkan hanya setelah tahap sebelumnya lulus; tahap yang sudah diputuskan hanya bisa dibukakan lewat koreksi. D16.5: koreksi batalkan status akhir.
                     </p>
                  </div>

                  <!-- modal konfirmasi generik (tema) — menggantikan confirm() -->
                  <div class="modal fade" id="modal-ok" tabindex="-1" role="dialog" aria-hidden="true">
                     <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title">Konfirmasi</h4>
                           </div>
                           <div class="modal-body"><p id="modal-ok-teks" class="small"></p></div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                              <button type="button" class="btn btn-primary" id="modal-ok-ya">Ya, lanjutkan</button>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- modal koreksi keputusan (D16.5) -->
                  <div class="modal fade" id="modal-koreksi" tabindex="-1" role="dialog" aria-hidden="true">
                     <div class="modal-dialog" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title">Koreksi keputusan tahap</h4>
                           </div>
                           <div class="modal-body">
                              <p class="text-muted small">Koreksi membuka keputusan tahap: tahap ini dan setelahnya kembali menunggu, jadwal terpublikasi ditarik, dan status diterima/tidak diterima diulang otomatis.</p>
                              <div class="form-group">
                                 <label>Tahap (urutan)</label>
                                 <input type="number" min="1" max="5" class="form-control" id="koreksi-n">
                              </div>
                              <div class="form-group">
                                 <label>Keputusan baru</label>
                                 <select id="koreksi-keputusan" class="form-control">
                                    <option value="passed">Lulus</option>
                                    <option value="failed">Tidak lulus</option>
                                    <?php if (! $status_akhir): // D16.5: kembalikan tahap ke menunggu hanya bila lamaran masih proses ?>
                                    <option value="pending">Kembalikan ke menunggu (tahap setelahnya ikut)</option>
                                    <?php endif; ?>
                                 </select>
                              </div>
                              <div class="form-group">
                                 <label>Alasan koreksi (wajib, maks 500)</label>
                                 <textarea id="koreksi-alasan" class="form-control" rows="3" maxlength="500"></textarea>
                              </div>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                              <button type="button" class="btn btn-primary" id="koreksi-kirim">Simpan koreksi</button>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- modal verifikasi persyaratan -->
                  <div class="modal fade" id="modal-verifikasi" tabindex="-1" role="dialog" aria-hidden="true">
                     <div class="modal-dialog" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title">Verifikasi persyaratan</h4>
                           </div>
                           <div class="modal-body">
                              <input type="hidden" id="verifikasi-rid" value="">
                              <p id="verifikasi-req" class="small text-muted"></p>
                              <div class="form-group">
                                 <label>Keputusan verifikasi</label>
                                 <select id="verifikasi-keputusan" class="form-control">
                                    <option value="verified">Terverifikasi</option>
                                    <option value="revision_requested">Minta revisi</option>
                                    <option value="rejected">Tolak</option>
                                 </select>
                                 <span class="help-block small" id="pesan-wajib-hint">Pesan ke kandidat wajib untuk revisi &amp; tolak (maks 500).</span>
                              </div>
                              <div class="form-group">
                                 <label for="verifikasi-pesan">Pesan ke kandidat (maks 500)</label>
                                 <textarea id="verifikasi-pesan" class="form-control" rows="3" maxlength="500"></textarea>
                              </div>
                              <div class="form-group">
                                 <label for="verifikasi-catatan">Catatan internal (review_note, maks 500 — TIDAK dikirim ke kandidat)</label>
                                 <textarea id="verifikasi-catatan" class="form-control" rows="2" maxlength="500"></textarea>
                              </div>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                              <button type="button" class="btn btn-primary" id="verifikasi-kirim">Simpan verifikasi</button>
                           </div>
                        </div>
                     </div>
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
                           'Tempat lahir'   => $snap->profile->place_of_birth ?? '-',
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
                        <thead><tr><th>Nama</th><th width="110">Tipe</th><th width="80">Wajib</th><th>Jawaban / Dokumen</th><th width="200">Verifikasi<?= $bisa_verifikasi ? ' + aksi' : ''; ?></th></tr></thead>
                        <tbody>
                        <?php
                           $req_lbl = array('not_uploaded'=>'Belum diunggah','pending_review'=>'Menunggu verifikasi','verified'=>'Terverifikasi','revision_requested'=>'Perlu revisi','rejected'=>'Ditolak');
                           $req_cls = array('not_uploaded'=>'label-default','pending_review'=>'label-warning','verified'=>'label-success','revision_requested'=>'label-orange','rejected'=>'label-danger');
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
                                 <?php if ($bisa_verifikasi): ?>
                                    <br>
                                    <button type="button" class="btn btn-flat btn-xs btn-primary career-verifikasi"
                                       data-rid="<?= (int) $r->id_application_requirement; ?>"
                                       data-nama="<?= html_escape($r->requirement_name); ?>"
                                       data-status="<?= html_escape($r->requirement_status); ?>">Verifikasi</button>
                                 <?php endif; ?>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        </tbody>
                     </table>
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
                              <td><code class="small"><?php if ($bisa_score_view || $a->action !== 'simpan_nilai_tahap') { echo html_escape((string) ($a->detail_json ?? '')); } else { echo html_escape('(detail tersembunyi — butuh izen score_view)'); } ?></code></td>
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

<script>
   $(function () {
      var CSRF = $('input[name="career_csrf"]').val() || '';
      function pusatKeputusan($button, url, data) {
         $.post(url, $.extend({ career_csrf: CSRF }, data || {}))
            .done(function (j) {
               if (j && j.success) { alert(j.message || 'Tersimpan.'); location.reload(); }
               else { alert(j && j.message ? j.message : 'Gagal.'); }
            })
            .fail(function (x) {
               var j = null;
               try { j = $.parseJSON(x.responseText); } catch (e) {}
               alert(j && j.message ? j.message : 'Terjadi kegagalan server.');
            });
      }
      // verifikasi persyaratan
      $('.career-verifikasi').on('click', function () {
         $('#verifikasi-rid').val($(this).data('rid'));
         $('#verifikasi-req').text($(this).data('nama') + ' — status: ' + $(this).data('status'));
         $('#verifikasi-keputusan').val('verified');
         $('#verifikasi-pesan').val('');
         $('#verifikasi-catatan').val('');
         $('#modal-verifikasi').modal('show');
      });
      $('#verifikasi-kirim').on('click', function () {
         pusatKeputusan($(this), '<?= site_url('administrator/career_pelamar/verifikasi/'); ?>' + $('#verifikasi-rid').val(), {
            keputusan: $('#verifikasi-keputusan').val(),
            pesan_kandidat: $('#verifikasi-pesan').val(),
            review_note: $('#verifikasi-catatan').val()
         });
      });
      // dialog konfirmasi tema (menggantikan confirm() utk keputusan/terima)
      function tanyaModal(teks, lalu) {
         $('#modal-ok-teks').text(teks);
         $('#modal-ok').modal('show');
         $('#modal-ok-ya').off('click').on('click', function () {
            $('#modal-ok').modal('hide');
            lalu();
         });
      }
      // keputusan tahap
      $('.career-keputusan').on('click', function () {
         var $b = $(this);
         if ($b.prop('disabled')) { return; }
         tanyaModal('Kunci keputusan tahap ini? (tercatat di audit log)', function () {
            pusatKeputusan($b, '<?= site_url('administrator/career_pelamar/keputusan'); ?>', {
               id_application: <?= (int) $app->id_application; ?>,
               n: $b.data('n'),
               keputusan: $b.data('keputusan')
            });
         });
      });
      // status akhir diterima
      $('.career-terima').on('click', function () {
         var $b = $(this);
         if ($b.prop('disabled')) { return; }
         tanyaModal('Jadikan status akhir Diterima? (semua tahap harus lulus)', function () {
            pusatKeputusan($b, '<?= site_url('administrator/career_pelamar/terima'); ?>', {
               id_application: <?= (int) $app->id_application; ?>
            });
         });
      });
      // koreksi keputusan (D16.5)
      $('.career-koreksi').on('click', function () {
         $('#koreksi-n').val($(this).data('n'));
         $('#koreksi-keputusan').val('passed');
         $('#koreksi-alasan').val('');
         $('#modal-koreksi').modal('show');
      });
      $('#koreksi-kirim').on('click', function () {
         pusatKeputusan($(this), '<?= site_url('administrator/career_pelamar/koreksi'); ?>', {
            id_application: <?= (int) $app->id_application; ?>,
            n: $('#koreksi-n').val(),
            keputusan_baru: $('#koreksi-keputusan').val(),
            alasan: $('#koreksi-alasan').val()
         });
      });
      // jadwal D16.2 (D18) — 6C: konfirmasi & hasil memakai modal tema,
      // BUKAN confirm()/alert().
      function html_escape(s) {
         return String(s).replace(/[&<>"']/g, function (c) {
            return ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' })[c];
         });
      }
      function hasilTema(ok, pesan) {
         $('#modal-ok .modal-title').text(ok ? 'Selesai' : 'Gagal');
         $('#modal-ok-teks').html(html_escape(pesan || ''));
         $('#modal-ok').modal('show');
         $('#modal-ok-ya').off('click').on('click', function () {
            $('#modal-ok').modal('hide');
            if (ok) { location.reload(); }
         });
      }
      $('.career-jadwal').on('click', function () {
         var $f    = $(this).closest('.career-jadwal-form');
         var aksi  = $(this).data('aksi');
         var tgl   = $f.find('.career-jadwal-tanggal').val() || '';
         var kanal = $f.find('.career-jadwal-kanal').val() || '';
         var catat = $f.find('.career-jadwal-catatan').val() || '';
         var body  = { id_application_stage: $(this).data('sid'), aksi: aksi,
                       schedule_datetime: tgl, channel: kanal, note: catat };
         var perjalanan = function () {
            $.post('<?= site_url('administrator/career_pelamar/jadwal'); ?>',
                   $.extend({ career_csrf: CSRF }, body))
               .done(function (j) {
                  hasilTema(j && j.success, j && j.message ? j.message : (j ? 'Tersimpan.' : 'Gagal.'));
               })
               .fail(function (x) {
                  var j = null;
                  try { j = $.parseJSON(x.responseText); } catch (e) {}
                  hasilTema(false, j && j.message ? j.message : 'Terjadi kegagalan server.');
               });
         };
         if (aksi === 'simpan' && ! tgl) {
            hasilTema(false, 'Tanggal masih kosong.');
            return;
         }
         if (aksi === 'hapus') {
            // dialog tema (menggantikan confirm() langsung)
            $('#modal-ok .modal-title').text('Hapus jadwal');
            $('#modal-ok-teks').text('Hapus jadwal tahap ini? keputusan tahap tidak terpengaruh.');
            $('#modal-ok').modal('show');
            $('#modal-ok-ya').off('click').on('click', function () {
               $('#modal-ok').modal('hide');
               perjalanan();
            });
            return;
         }
         $('#modal-ok .modal-title').text('Aksi jadwal');
         $('#modal-ok-teks').text('Lakukan aksi "' + aksi + '" untuk jadwal tahap ini?');
         $('#modal-ok').modal('show');
         $('#modal-ok-ya').off('click').on('click', function () {
            $('#modal-ok').modal('hide');
            perjalanan();
         });
      });
      // nilai internal (D13)
      $('.career-nilai').on('click', function () {
         var $f = $(this).closest('.career-nilai-form');
         pusatKeputusan($(this), '<?= site_url('administrator/career_pelamar/nilai'); ?>', {
            id_application_stage: $(this).data('sid'),
            score:       $f.find('.career-nilai-skor').val(),
            rubric_note: $f.find('.career-nilai-catatan').val()
         });
      });
   });
</script>
