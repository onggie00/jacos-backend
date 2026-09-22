<?php
function labs_initials($name) {
    $name = trim($name);
    if ($name === '') return '?';
    $parts = preg_split('/\s+/', $name);
    if (count($parts) >= 2) {
        return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
    }
    return strtoupper(mb_substr($parts[0], 0, 2));
}
function labs_kategori_badge($k) {
    $map = array(
        'Immediate Professional Follow-up' => 'danger',
        'High Occupational Risk'           => 'danger',
        'Burnout with emotional distress'  => 'warning',
        'Emotional Distress'               => 'orange',
        'Burnout Dominant'                 => 'orange',
        'Needs Monitoring'                 => 'warning',
        'Workplace Support Needed'         => 'success',
        'Healthy but Fatigued'             => 'info',
        'Optimal Wellbeing'                => 'success',
    );
    return isset($map[$k]) ? $map[$k] : 'default';
}
function labs_skor_badge($k) {
    if (in_array($k, array('Risiko Tinggi','Berat','Sangat Tinggi','Cukup Parah'))) return 'danger';
    if (in_array($k, array('Sedang','Tinggi','Menurun','Perlu Perhatian'))) return 'warning';
    if (in_array($k, array('Ringan','Risiko Sedang'))) return 'info';
    return 'success';
}
?>

<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">

<section class="content-header">
   <h1>
      Detail Sesi MHCU
      <small class="labs-text-muted">Hasil Skrining</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mhcu_sesi'); ?>">Monitoring Sesi</a></li>
      <li class="active">Detail</li>
   </ol>
</section>

<?php
$sesi = $detail['sesi'];
$hasil = $detail['hasil'];
$scores = $detail['scores'];
$peringatan = !empty($detail['peringatan']) ? $detail['peringatan'] : array();
?>

<section class="content">
   <div class="row">

      <!-- LEFT COLUMN -->
      <div class="col-md-4">

         <!-- Profil card -->
         <div class="labs-card labs-mb-4">
            <div class="labs-card__body">
               <div class="labs-flex labs-flex-gap-3" style="align-items:center;margin-bottom:16px">
                  <span class="labs-avatar labs-avatar--lg"><?= labs_initials($sesi->nama_lengkap); ?></span>
                  <div>
                     <div style="font-size:16px;font-weight:600;color:var(--labs-text)"><?= _ent($sesi->nama_lengkap); ?></div>
                     <div class="labs-fs-sm labs-text-muted">NPP: <?= _ent($sesi->npp); ?></div>
                  </div>
               </div>
               <div class="labs-info-grid">
                  <div class="labs-info-grid__item">
                     <div class="labs-info-grid__label">Role</div>
                     <div class="labs-info-grid__value"><span class="labs-badge labs-badge--info"><?= _ent($sesi->presensi_role); ?></span></div>
                  </div>
                  <div class="labs-info-grid__item">
                     <div class="labs-info-grid__label">Periode</div>
                     <div class="labs-info-grid__value"><?= _ent($sesi->nama_periode); ?></div>
                  </div>
                  <div class="labs-info-grid__item">
                     <div class="labs-info-grid__label">Status</div>
                     <div class="labs-info-grid__value">
                        <?php if ($sesi->status == 'selesai'): ?>
                           <span class="labs-badge labs-badge--success"><span class="dot"></span> Selesai</span>
                        <?php else: ?>
                           <span class="labs-badge labs-badge--warning"><span class="dot"></span> Belum Selesai</span>
                        <?php endif; ?>
                     </div>
                  </div>
                  <div class="labs-info-grid__item">
                     <div class="labs-info-grid__label">Waktu Selesai</div>
                     <div class="labs-info-grid__value">
                        <?php if ($sesi->submitted_at): ?>
                           <div class="labs-fs-sm"><?= formatTanggal($sesi->submitted_at); ?></div>
                           <div class="labs-fs-xs labs-text-muted"><?= date('H:i', strtotime($sesi->submitted_at)); ?> WIB</div>
                        <?php else: ?>
                           <span class="labs-text-muted">-</span>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Kesimpulan -->
         <?php if (!empty($hasil)): ?>
         <?php $bcls = labs_kategori_badge($hasil->kategori_keseluruhan); ?>
         <div class="labs-card labs-mb-4">
            <div class="labs-card__header">
               <h3 class="labs-card__title"><i class="fa fa-heart"></i> Kesimpulan</h3>
            </div>
            <div class="labs-card__body labs-text-center">
               <span class="labs-badge labs-badge--solid labs-badge--<?= $bcls; ?>" style="font-size:18px;padding:10px 24px"><?= _ent($hasil->kategori_keseluruhan); ?></span>
               <?php if ($hasil->is_krisis == 1): ?>
                  <div style="margin-top:12px">
                     <span class="labs-badge labs-badge--solid labs-badge--danger labs-badge--pulse"><i class="fa fa-exclamation-triangle"></i> STATUS KRISIS</span>
                  </div>
               <?php endif; ?>
               <?php if (!empty($hasil->saran_rekomendasi)): ?>
                  <hr class="labs-divider">
                  <div class="labs-fs-xs labs-text-muted labs-fw-bold labs-mb-2" style="text-transform:uppercase;letter-spacing:0.5px;text-align:left">Rekomendasi</div>
                  <div style="font-size:13px;line-height:1.6;text-align:left"><?= _ent($hasil->saran_rekomendasi); ?></div>
               <?php endif; ?>
            </div>
         </div>
         <?php endif; ?>

         <!-- Peringatan timeline -->
         <?php if (!empty($peringatan)): ?>
         <div class="labs-card">
            <div class="labs-card__header">
               <h3 class="labs-card__title"><i class="fa fa-bell"></i> Tiket Peringatan <span class="labs-badge labs-badge--danger"><?= count($peringatan); ?></span></h3>
            </div>
            <div class="labs-card__body">
               <?php foreach($peringatan as $p):
                  $statusCls = ($p->status_alert == 'waiting') ? 'warning' : (($p->status_alert == 'process') ? 'info' : 'success');
               ?>
               <div style="border-left:3px solid var(--labs-danger);background:var(--labs-danger-soft);padding:12px 16px;border-radius:6px;margin-bottom:10px">
                  <div class="labs-flex-between labs-mb-2">
                     <strong class="labs-text-danger"><?= _ent($p->jenis_alert); ?></strong>
                     <span class="labs-badge labs-badge--<?= $statusCls; ?>"><?= _ent($p->status_alert); ?></span>
                  </div>
                  <div class="labs-fs-sm"><strong>Diproses:</strong> <?= _ent($p->diproses_oleh ?: '-'); ?></div>
                  <div class="labs-fs-xs labs-text-muted" style="margin-top:6px">
                     <i class="fa fa-clock-o"></i> <?= formatTanggal($p->created_at) . ' ' . date('H:i', strtotime($p->created_at)); ?> WIB
                  </div>
               </div>
               <?php endforeach; ?>
            </div>
         </div>
         <?php endif; ?>
      </div>

      <!-- RIGHT COLUMN - Skor -->
      <div class="col-md-8">
         <div class="labs-card">
            <div class="labs-card__header">
               <h3 class="labs-card__title"><i class="fa fa-bar-chart"></i> Skor Instrument</h3>
               <span class="labs-badge labs-badge--default"><?= count($scores); ?> item</span>
            </div>
            <div class="labs-card__body">
               <?php
               $current_inst = '';
               $grouped = array();
               foreach ($scores as $s) {
                  $key = $s->kode_instrument . '|' . $s->nama_instrument;
                  if (!isset($grouped[$key])) $grouped[$key] = array();
                  $grouped[$key][] = $s;
               }

               if (!empty($scores)):
               ?>
               <div class="labs-table-wrap">
                  <table class="labs-table">
                     <thead>
                        <tr>
                           <th>Instrument</th>
                           <th>Dimensi / Aspek</th>
                           <th class="labs-cell-numeric" width="120">Skor</th>
                           <th>Kategori</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php foreach ($grouped as $key => $items):
                        $parts = explode('|', $key);
                        $instLabel = $parts[0] . ' - ' . $parts[1];
                        $first = true;
                        foreach ($items as $s):
                           $bcls = labs_skor_badge($s->kategori);
                           // Hitung lebar progress bar (skor diasumsikan max 100)
                           $skorNum = floatval($s->skor);
                           $width = min(100, max(5, $skorNum));
                     ?>
                        <tr>
                           <td><?= $first ? '<strong>'._ent($instLabel).'</strong>' : ''; ?></td>
                           <td><?= _ent($s->dimensi_aspek); ?></td>
                           <td class="labs-cell-numeric">
                              <div class="labs-fw-bold"><?= $s->skor; ?></div>
                              <div class="labs-progress">
                                 <div class="labs-progress__bar labs-progress__bar--<?= $bcls; ?>" style="width: <?= $width; ?>%"></div>
                              </div>
                           </td>
                           <td><span class="labs-badge labs-badge--<?= $bcls; ?>"><?= _ent($s->kategori); ?></span></td>
                        </tr>
                     <?php $first = false; endforeach; endforeach; ?>
                     </tbody>
                  </table>
               </div>
               <?php else: ?>
                  <div class="labs-empty">
                     <i class="fa fa-hourglass-half"></i>
                     <p>Belum ada skor (sesi belum selesai)</p>
                  </div>
               <?php endif; ?>
            </div>
            <div class="labs-card__body" style="border-top:1px solid var(--labs-border);padding-top:16px">
               <a href="<?= site_url('administrator/mhcu_sesi'); ?>" class="labs-btn labs-btn--default">
                  <i class="fa fa-arrow-left"></i> Kembali ke Daftar
               </a>
               <?php if (!empty($hasil)): ?>
               <a href="<?= site_url('administrator/mhcu_sesi/export_pdf/' . $sesi->id_mhcu_sesi); ?>" class="labs-btn labs-btn--danger" target="_blank">
                  <i class="fa fa-file-pdf-o"></i> Export PDF
               </a>
               <?php else: ?>
               <button class="labs-btn labs-btn--default" disabled title="Sesi belum selesai - PDF belum bisa di-generate">
                  <i class="fa fa-file-pdf-o"></i> Export PDF
               </button>
               <?php endif; ?>
         </div>
      </div>

   </div>
</section>