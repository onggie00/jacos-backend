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
        'High Occupational Risk'           => 'orange',
        'Emotional Distress'               => 'orange',
        'Burnout Dominant'                 => 'warning',
        'Needs Monitoring'                 => 'warning',
        'Workplace Support Needed'         => 'success',
        'Healthy but Fatigued'             => 'info',
        'Optimal Wellbeing'                => 'success',
    );
    return isset($map[$k]) ? $map[$k] : 'default';
}
function labs_jenis_badge($j) {
    if ($j == 'merah') return 'danger';
    if ($j == 'orange') return 'orange';
    return 'warning';
}
function labs_status_badge($s) {
    if ($s == 'waiting') return 'warning';
    if ($s == 'scheduled') return 'primary';
    if ($s == 'process') return 'info';
    if ($s == 'finished') return 'success';
    if ($s == 'cancelled') return 'default';
    return 'default';
}

// Statistik dari seluruh data (agregat GROUP BY, bukan hasil paginate)
$status_stats = isset($status_stats) && is_array($status_stats) ? $status_stats : array();
$stat_total     = array_sum($status_stats);
$stat_waiting   = isset($status_stats['waiting'])   ? $status_stats['waiting']   : 0;
$stat_scheduled = isset($status_stats['scheduled']) ? $status_stats['scheduled'] : 0;
$stat_process   = isset($status_stats['process'])   ? $status_stats['process']   : 0;
$stat_finished  = isset($status_stats['finished'])  ? $status_stats['finished']  : 0;
$stat_cancelled = isset($status_stats['cancelled']) ? $status_stats['cancelled'] : 0;
?>

<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<section class="content-header">
   <h1>
      Peringatan MHCU
      <small class="labs-text-muted">Dashboard Krisis</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Peringatan MHCU</li>
   </ol>
</section>

<section class="content">

   <!-- STAT CARDS - Crisis Command Center -->
   <div class="row">
      <div class="col-md-2 col-sm-4">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--danger">
               <i class="fa fa-bell"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Total Tiket</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stat_total); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2 col-sm-4">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--warning">
               <i class="fa fa-hourglass-half"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Waiting</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stat_waiting); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2 col-sm-4">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--primary">
               <i class="fa fa-calendar-check-o"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Scheduled</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stat_scheduled); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2 col-sm-4">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--info">
               <i class="fa fa-cog fa-spin"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Process</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stat_process); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2 col-sm-4">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--success">
               <i class="fa fa-check-circle"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Finished</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stat_finished); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2 col-sm-4">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--danger" style="background:linear-gradient(135deg,#9CA3AF,#4B5563)">
               <i class="fa fa-ban"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Cancelled</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stat_cancelled); ?>">0</div>
            </div>
         </div>
      </div>
   </div>

   <!-- MAIN CARD -->
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">

            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-exclamation-triangle labs-text-danger"></i>
                  Daftar Tiket Peringatan
                  <span class="labs-badge labs-badge--default labs-ml-2"><?= intval($peringatan_counts); ?> tiket</span>
               </h3>
            </div>

            <div class="labs-card__body">

               <!-- MAIN FILTERS (inline) -->
               <form action="<?= base_url('administrator/mhcu_peringatan/index'); ?>" method="GET" class="labs-filter-inline">
                  <select class="form-control" name="jenis" title="Jenis">
                     <option value="">Semua Jenis</option>
                     <option value="kuning" <?= ($this->input->get('jenis') == 'kuning') ? 'selected' : ''; ?>>Kuning</option>
                     <option value="orange" <?= ($this->input->get('jenis') == 'orange') ? 'selected' : ''; ?>>Orange</option>
                     <option value="merah" <?= ($this->input->get('jenis') == 'merah') ? 'selected' : ''; ?>>Merah</option>
                  </select>

                  <select class="form-control" name="status" title="Status">
                     <option value="">Semua Status</option>
                     <option value="waiting" <?= ($this->input->get('status') == 'waiting') ? 'selected' : ''; ?>>Waiting</option>
                     <option value="scheduled" <?= ($this->input->get('status') == 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                     <option value="process" <?= ($this->input->get('status') == 'process') ? 'selected' : ''; ?>>Process</option>
                     <option value="finished" <?= ($this->input->get('status') == 'finished') ? 'selected' : ''; ?>>Finished</option>
                     <option value="cancelled" <?= ($this->input->get('status') == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                  </select>

                  <div class="labs-filter-inline__search">
                     <i class="fa fa-search labs-filter-inline__search-icon"></i>
                     <input type="text" class="form-control" name="q" placeholder="Cari NPP / Nama…" value="<?= htmlspecialchars($this->input->get('q'), ENT_QUOTES); ?>">
                  </div>

                  <button type="submit" class="labs-btn labs-btn--primary labs-btn--sm" title="Filter"><i class="fa fa-filter"></i></button>
                  <a href="<?= base_url('administrator/mhcu_peringatan'); ?>" class="labs-btn labs-btn--default labs-btn--sm" title="Reset"><i class="fa fa-undo"></i></a>
               </form>

               <!-- ADVANCED FILTER TOGGLE -->
               <div class="labs-toggle-section">
                  <button type="button" class="labs-toggle-trigger" data-labs-toggle="advanced-filter">
                     <span class="labs-toggle-trigger__icon"><i class="fa fa-chevron-right"></i></span>
                     <span class="labs-toggle-trigger__label">
                        <i class="fa fa-sliders"></i> Filter Lanjutan
                     </span>
                     <span class="labs-toggle-trigger__badge">Hidden</span>
                  </button>

                  <div class="labs-toggle-content" id="advanced-filter">
                     <form action="<?= base_url('administrator/mhcu_peringatan/index'); ?>" method="GET" class="labs-filter-bar">
                        <!-- preserve current filters -->
                        <?php if ($this->input->get('jenis')): ?><input type="hidden" name="jenis" value="<?= htmlspecialchars($this->input->get('jenis'), ENT_QUOTES); ?>"><?php endif; ?>
                        <?php if ($this->input->get('status')): ?><input type="hidden" name="status" value="<?= htmlspecialchars($this->input->get('status'), ENT_QUOTES); ?>"><?php endif; ?>
                        <?php if ($this->input->get('q')): ?><input type="hidden" name="q" value="<?= htmlspecialchars($this->input->get('q'), ENT_QUOTES); ?>"><?php endif; ?>

                        <div class="labs-filter-bar__row">
                           <div class="labs-filter-bar__field">
                              <label class="labs-filter-bar__label">Dari Tanggal</label>
                              <div class="labs-filter-input">
                                 <i class="fa fa-calendar labs-filter-input__icon"></i>
                                 <input type="date" class="form-control" name="date_from" value="<?= htmlspecialchars($this->input->get('date_from'), ENT_QUOTES); ?>">
                              </div>
                           </div>

                           <div class="labs-filter-bar__field">
                              <label class="labs-filter-bar__label">Sampai Tanggal</label>
                              <div class="labs-filter-input">
                                 <i class="fa fa-calendar labs-filter-input__icon"></i>
                                 <input type="date" class="form-control" name="date_to" value="<?= htmlspecialchars($this->input->get('date_to'), ENT_QUOTES); ?>">
                              </div>
                           </div>

                           <div class="labs-filter-bar__field labs-filter-bar__actions">
                              <button type="submit" class="labs-btn labs-btn--primary labs-btn--sm">
                                 <i class="fa fa-search"></i> Terapkan
                              </button>
                              <a href="<?= base_url('administrator/mhcu_peringatan'); ?>" class="labs-btn labs-btn--default labs-btn--sm" title="Reset Semua">
                                 <i class="fa fa-undo"></i>
                              </a>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>

               <!-- TABLE -->
               <div class="labs-table-wrap">
                  <div class="labs-table-scroll">
                  <table class="labs-table">
                     <thead>
                        <tr>
                           <th>Peserta</th>
                           <th>Role</th>
                           <th>Kategori</th>
                           <th>Jenis Alert</th>
                           <th>Status</th>
                           <th>Jadwal Care</th>
                           <th>Diproses Oleh</th>
                           <th>Dibuat</th>
                           <th class="labs-cell-numeric">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php foreach($peringatans as $p):
                        $initials = labs_initials($p->nama_lengkap);
                        $kategori = !empty($p->kategori_keseluruhan) ? $p->kategori_keseluruhan : '';
                        $care_url = base_url('mhcu_care?token=' . md5($p->id_sesi . MHCU_CARE_TOKEN_SALT) . '&id_sesi=' . $p->id_sesi);
                     ?>
                        <tr>
                           <td>
                              <div class="labs-name-cell">
                                 <span class="labs-avatar"><?= $initials; ?></span>
                                 <div>
                                    <div class="labs-fw-semi"><?= _ent($p->nama_lengkap); ?></div>
                                    <div class="labs-fs-xs labs-text-muted">NPP: <?= _ent($p->npp); ?></div>
                                 </div>
                              </div>
                           </td>
                           <td><span class="labs-badge labs-badge--info"><?= _ent($p->presensi_role); ?></span></td>
                           <td>
                              <?php if ($kategori): ?>
                                 <?php $warna_map = array('hijau'=>'success','kuning'=>'warning','orange'=>'orange','merah'=>'danger'); $kcls = isset($warna_map[$p->kategori_warna]) ? $warna_map[$p->kategori_warna] : 'default'; ?>
                                 <span class="labs-badge labs-badge--<?= $kcls; ?>"><?= _ent($kategori); ?></span>
                              <?php else: ?>
                                 <span class="labs-cell-muted">-</span>
                              <?php endif; ?>
                           </td>
                           <td>
                              <?php
                              $jcls = labs_jenis_badge($p->jenis_alert);
                              $jicon = ($p->jenis_alert == 'merah') ? 'fa-exclamation-circle' : (($p->jenis_alert == 'orange') ? 'fa-exclamation-triangle' : 'fa-warning');
                              ?>
                              <span class="labs-badge labs-badge--<?= $jcls; ?>">
                                 <i class="fa <?= $jicon; ?>"></i> <?= _ent($p->jenis_alert); ?>
                              </span>
                           </td>
                           <td>
                              <?php $scls = labs_status_badge($p->status_alert); ?>
                              <span class="labs-badge labs-badge--<?= $scls; ?>">
                                 <span class="dot"></span> <?= _ent($p->status_alert); ?>
                              </span>
                           </td>
                           <td>
                              <?php if (!empty($p->care_tanggal)): ?>
                                 <div class="labs-fs-sm labs-fw-semi"><?= formatTanggal($p->care_tanggal); ?></div>
                                 <div class="labs-fs-xs labs-text-muted">Sesi <?= intval($p->care_sesi_ke); ?>, <?= date('H:i', strtotime($p->care_jam_mulai)); ?>–<?= date('H:i', strtotime($p->care_jam_selesai)); ?> WIB</div>
                                 <?php if (!empty($p->care_nama_psikolog)): ?>
                                 <div class="labs-fs-xs labs-text-muted"><i class="fa fa-user-md"></i> <?= _ent($p->care_nama_psikolog); ?></div>
                                 <?php endif; ?>
                              <?php else: ?>
                                 <span class="labs-cell-muted">-</span>
                              <?php endif; ?>
                           </td>
                           <td>
                              <?php
                              // Sumber diproses: isi manual admin > admin (updated_by) > sistem MHCU Care (ada jadwal care) > -
                              $diproses_label = $p->diproses_oleh;
                              if (empty($diproses_label) && !empty($p->updated_by) && $p->updated_by !== 'system') {
                                  $diproses_label = 'Admin (' . $p->updated_by . ')';
                              }
                              if (empty($diproses_label) && !empty($p->care_tanggal)) {
                                  $diproses_label = 'Sistem MHCU Care';
                              }
                              echo _ent($diproses_label ?: '-');
                              ?>
                           </td>
                           <td>
                              <div class="labs-fs-sm"><?= formatTanggal($p->created_at); ?></div>
                              <div class="labs-fs-xs labs-text-muted"><?= date('H:i', strtotime($p->created_at)); ?> WIB</div>
                           </td>
                           <td class="labs-cell-numeric">
                              <div class="labs-row-actions" style="justify-content:center">
                                 <button type="button" class="labs-btn labs-btn--info labs-btn--icon labs-btn--sm btn_detail" data-id="<?= $p->id_peringatan; ?>" title="Lihat Detail">
                                    <i class="fa fa-eye"></i>
                                 </button>
                                 <button type="button" class="labs-btn labs-btn--warning labs-btn--icon labs-btn--sm btn-update-status"
                                         data-id="<?= $p->id_peringatan; ?>"
                                         data-status="<?= _ent($p->status_alert); ?>"
                                         data-diproses="<?= _ent($p->diproses_oleh); ?>"
                                         data-keterangan="<?= _ent($p->keterangan); ?>"
                                         title="Update Status">
                                    <i class="fa fa-pencil"></i>
                                 </button>
                                 <button type="button" class="labs-btn labs-btn--success labs-btn--icon labs-btn--sm btn_copy_care"
                                         data-url="<?= _ent($care_url); ?>"
                                         title="Copy Link MHCU Care">
                                    <i class="fa fa-link"></i>
                                 </button>
                                 <?php if ($p->status_alert != 'finished'): ?>
                                 <button type="button" class="labs-btn labs-btn--primary labs-btn--icon labs-btn--sm btn_send_reminder"
                                         data-id="<?= $p->id_peringatan; ?>"
                                         data-nama="<?= _ent($p->nama_lengkap); ?>"
                                         title="Kirim Reminder">
                                    <i class="fa fa-paper-plane"></i>
                                 </button>
                                 <?php endif; ?>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if ($peringatan_counts == 0): ?>
                        <tr class="labs-empty-row">
                           <td colspan="9">
                              <div class="labs-empty">
                                 <i class="fa fa-check-circle labs-text-success"></i>
                                 <p>Tidak ada peringatan aktif</p>
                              </div>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>

               <?php if (!empty($pagination) && $peringatan_counts > 0): ?>
               <div class="labs-flex-between labs-mt-4">
                  <div class="labs-text-muted labs-fs-sm">
                     Total: <strong><?= intval($peringatan_counts); ?></strong> tiket
                  </div>
                  <div><?= $pagination; ?></div>
               </div>
               <?php endif; ?>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL DETAIL -->
<div class="modal fade labs-modal" id="modalDetail" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Detail Peringatan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body" id="detail_content"></div>
         <div class="modal-footer">
            <button class="labs-btn labs-btn--default" data-dismiss="modal">
               <i class="fa fa-times"></i> Tutup
            </button>
         </div>
      </div>
   </div>
</div>

<!-- Modal Update Status -->
<div class="modal fade labs-modal" id="modalUpdateStatus" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-pencil"></i> Update Status Peringatan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <form id="form_update_status" method="POST">
            <div class="modal-body">
               <input type="hidden" name="id_peringatan" id="upd_id">
               <div class="labs-modal-section">
                  <div class="labs-form-group">
                     <label class="labs-form-label">Status</label>
                     <select class="form-control" name="status_alert" id="upd_status">
                        <option value="waiting">Waiting</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="process">Process</option>
                        <option value="finished">Finished</option>
                        <option value="cancelled">Cancelled</option>
                     </select>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Diproses Oleh</label>
                     <input type="text" class="form-control" name="diproses_oleh" id="upd_diproses" placeholder="Nama petugas">
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Keterangan</label>
                     <textarea class="form-control" name="keterangan" id="upd_keterangan" rows="3" placeholder="Catatan manual untuk peserta ini"></textarea>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="labs-btn labs-btn--default" data-dismiss="modal">Batal</button>
               <button type="submit" class="labs-btn labs-btn--primary"><i class="fa fa-save"></i> Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal Kirim Reminder -->
<div class="modal fade labs-modal" id="modalSendReminder" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-paper-plane"></i> Kirim Reminder — <span id="rem_nama"></span></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <form id="form_send_reminder" method="POST">
            <div class="modal-body">
               <input type="hidden" name="id_peringatan" id="rem_id">
               <div class="labs-modal-section">
                  <div class="labs-form-group">
                     <label class="labs-form-label">Judul <span class="labs-text-muted">(±25 huruf)</span></label>
                     <input type="text" class="form-control" name="judul" id="rem_judul" maxlength="30" required>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Deskripsi <span class="labs-text-muted">(±120 huruf, boleh enter)</span></label>
                     <textarea class="form-control" name="deskripsi" id="rem_deskripsi" rows="4" maxlength="150" required></textarea>
                  </div>
                  <div style="background:var(--labs-bg-soft);padding:12px 14px;border-radius:6px;border-left:3px solid var(--labs-info);font-size:12px;line-height:1.7">
                     <div class="labs-fw-semi"><i class="fa fa-info-circle"></i> Catatan / Ketentuan:</div>
                     <ul style="margin:6px 0 0 18px;padding:0">
                        <li>Judul sekitar <strong>25 huruf</strong>, deskripsi sekitar <strong>120 huruf</strong> (boleh enter).</li>
                        <li>Emoji diperbolehkan untuk menonjolkan informasi.</li>
                     </ul>
                     <div style="margin-top:8px"><span class="labs-text-muted">Contoh judul:</span> <span class="labs-fw-semi">🌿 Reminder MHCU-Care</span></div>
                     <div><span class="labs-text-muted">Contoh deskripsi:</span> Bapak/Ibu dijadwalkan mengikuti sesi konseling hari ini, 🕐 jam 13:00 - 14:30 , 📍 di Ruang Psikolog. Ditunggu kehadirannya.</div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="labs-btn labs-btn--default" data-dismiss="modal">Batal</button>
               <button type="submit" class="labs-btn labs-btn--primary" id="rem_submit"><i class="fa fa-paper-plane"></i> Kirim</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- SKELETON TEMPLATE untuk detail -->
<script type="text/template" id="detail-skeleton">
<div class="labs-text-center" style="padding: 20px 0;">
   <div class="labs-skeleton labs-skeleton--circle"></div>
   <div class="labs-skeleton labs-skeleton--lg labs-skeleton--block" style="width:40%;margin:0 auto 16px"></div>
   <div class="labs-skeleton labs-skeleton--block" style="height:120px"></div>
</div>
</script>

<script>
$(document).ready(function(){
   // Update status modal
   $('.btn-update-status').click(function(){
      var btn = $(this);
      $('#upd_id').val(btn.data('id'));
      $('#upd_status').val(btn.data('status'));
      $('#upd_diproses').val(btn.data('diproses'));
      $('#upd_keterangan').val(btn.data('keterangan'));
      $('#modalUpdateStatus').modal('show');
   });

   // Copy link MHCU Care
   $('.btn_copy_care').click(function(){
      var url = $(this).data('url');
      if (navigator.clipboard && navigator.clipboard.writeText) {
         navigator.clipboard.writeText(url).then(function(){ toastr.success('Link MHCU Care dicopy'); });
      } else {
         window.prompt('Copy link berikut:', url);
      }
   });

   $('#form_update_status').submit(function(e){
      e.preventDefault();
      var id = $('#upd_id').val();
      var url = '<?= base_url('administrator/mhcu_peringatan/update_status'); ?>/' + id;
      $.ajax({ url: url, type: 'POST', data: $(this).serialize(), dataType: 'json',
         success: function(r){
            if(r.success){
               toastr.success(r.message);
               $('#modalUpdateStatus').modal('hide');
               location.reload();
            } else {
               toastr.error(r.message);
            }
         }
      });
   });

   // Kirim reminder
   var SAMPLE_JUDUL = '\uD83C\uDF3F Reminder MHCU-Care';
   var SAMPLE_DESKRIPSI = 'Bapak/Ibu dijadwalkan mengikuti sesi konseling hari ini, \uD83D\uDCC5 \uD83D\uDD51 jam 13:00 - 14:30 , \uD83D\uDCCD di Ruang Psikolog. Ditunggu kehadirannya.';
   $('.btn_send_reminder').click(function(){
      $('#rem_id').val($(this).data('id'));
      $('#rem_nama').text($(this).data('nama'));
      $('#rem_judul').val(SAMPLE_JUDUL);
      $('#rem_deskripsi').val(SAMPLE_DESKRIPSI);
      $('#modalSendReminder').modal('show');
   });

   $('#form_send_reminder').submit(function(e){
      e.preventDefault();
      var btn = $('#rem_submit');
      btn.prop('disabled', true);
      var id = $('#rem_id').val();
      var url = '<?= base_url('administrator/mhcu_peringatan/send_reminder'); ?>/' + id;
      $.ajax({ url: url, type: 'POST', data: $(this).serialize(), dataType: 'json',
         success: function(r){
            btn.prop('disabled', false);
            if(r.success){
               toastr.success(r.message);
               $('#modalSendReminder').modal('hide');
            } else {
               toastr.error(r.message);
            }
         },
         error: function(){
            btn.prop('disabled', false);
            toastr.error('Gagal mengirim reminder');
         }
      });
   });

   // Detail modal
   $('.btn_detail').click(function(){
      var id = $(this).data('id');
      var content = $('#detail_content');
      content.html($('#detail-skeleton').html());
      $('#modalDetail').modal('show');

      $.ajax({
         url: '<?= site_url('administrator/mhcu_peringatan/get_detail/'); ?>' + id,
         method: 'GET',
         dataType: 'json',
         success: function(data){
            if (!data || data.error) {
               content.html('<div class="labs-empty"><i class="fa fa-exclamation-circle labs-text-danger"></i><p>' + (data.error || 'Data tidak ditemukan') + '</p></div>');
               return;
            }
            renderDetail(data);
         },
         error: function(){
            content.html('<div class="labs-empty"><i class="fa fa-exclamation-circle labs-text-danger"></i><p>Gagal memuat data</p></div>');
         }
      });
   });

   // Badge "Hidden"/"Shown" untuk toggle
   $(document).on('click', '.labs-toggle-trigger', function(){
      var $b = $(this).find('.labs-toggle-trigger__badge');
      var isExpanded = $(this).hasClass('is-expanded');
      $b.text(isExpanded ? 'Shown' : 'Hidden');
   });
});

function labsKategoriBadge(k){
   var map = {
      'Immediate Professional Follow-up':'danger',
      'High Occupational Risk':'orange',
      'Emotional Distress':'orange',
      'Burnout Dominant':'warning',
      'Needs Monitoring':'warning',
      'Workplace Support Needed':'success',
      'Healthy but Fatigued':'info',
      'Optimal Wellbeing':'success'
   };
   return map[k] || 'default';
}
function labsJenisBadge(j){
   if (j == 'merah') return 'danger';
   if (j == 'orange') return 'orange';
   return 'warning';
}
function labsStatusBadge(s){
   if (s == 'waiting') return 'warning';
   if (s == 'scheduled') return 'primary';
   if (s == 'process') return 'info';
   if (s == 'finished') return 'success';
   if (s == 'cancelled') return 'default';
   return 'default';
}
function labsInitial(name){
   name = (name||'').trim();
   if (!name) return '?';
   var parts = name.split(/\s+/);
   if (parts.length >= 2) return (parts[0][0] + parts[parts.length-1][0]).toUpperCase();
   return parts[0].substring(0,2).toUpperCase();
}
function formatDateID(s){
   if (!s) return '-';
   var BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
   var d = new Date(s + 'T00:00:00');
   return d.getDate() + ' ' + BULAN[d.getMonth()] + ' ' + d.getFullYear();
}

function renderDetail(data){
   var kclr = data.kategori_keseluruhan || '';
   var warnaMap = {hijau:'success',kuning:'warning',orange:'orange',merah:'danger'};
   var kcls = warnaMap[data.kategori_warna] || 'default';
   var jcls = labsJenisBadge(data.jenis_alert);
   var scls = labsStatusBadge(data.status_alert);

   var jicon = (data.jenis_alert == 'merah') ? 'fa-exclamation-circle'
             : (data.jenis_alert == 'orange') ? 'fa-exclamation-triangle'
             : 'fa-warning';

   var html = '';

   // Tabs
   html += '<div class="labs-modal-tabs">';
   html += '  <div class="labs-modal-tabs__item active" data-tab="tab-peserta"><i class="fa fa-user"></i> Peserta</div>';
   html += '  <div class="labs-modal-tabs__item" data-tab="tab-tiket"><i class="fa fa-bell"></i> Tiket</div>';
   if (data.saran_rekomendasi) {
      html += '  <div class="labs-modal-tabs__item" data-tab="tab-rekomendasi"><i class="fa fa-comment"></i> Rekomendasi</div>';
   }
   html += '</div>';

   // TAB PESERTA
   html += '<div class="labs-tab-content active" id="tab-peserta">';
   html += '  <div class="labs-modal-section">';
   html += '    <div style="display:flex;gap:16px;align-items:center;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--labs-border)">';
   if (data.foto_url) {
      html += '    <img src="' + data.foto_url + '" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid var(--labs-danger-soft)" />';
   } else {
      html += '    <span class="labs-avatar labs-avatar--lg">' + labsInitial(data.nama_lengkap) + '</span>';
   }
   html += '      <div>';
   html += '        <div style="font-size:16px;font-weight:600;color:var(--labs-text)">' + (data.nama_lengkap||'-') + '</div>';
   html += '        <div style="font-size:12px;color:var(--labs-text-muted)">NPP: ' + (data.npp||'-') + '</div>';
   html += '      </div>';
   html += '    </div>';
   html += '    <div class="labs-info-grid">';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Role</div><div class="labs-info-grid__value"><span class="labs-badge labs-badge--info">' + (data.presensi_role||'-') + '</span></div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Kategori Hasil</div><div class="labs-info-grid__value">' + (kclr ? '<span class="labs-badge labs-badge--' + kcls + '">' + kclr + '</span>' : '<span class="labs-text-muted">-</span>') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Jenis Alert</div><div class="labs-info-grid__value"><span class="labs-badge labs-badge--' + jcls + '"><i class="fa ' + jicon + '"></i> ' + (data.jenis_alert||'-') + '</span></div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Status</div><div class="labs-info-grid__value"><span class="labs-badge labs-badge--' + scls + '"><span class="dot"></span> ' + (data.status_alert||'-') + '</span></div></div>';
   if (data.care_tanggal) {
      html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Jadwal Konseling</div><div class="labs-info-grid__value"><i class="fa fa-calendar labs-text-primary"></i> ' + formatDateID(data.care_tanggal) + ' &mdash; Sesi ' + data.care_sesi_ke + ' (' + data.care_jam_mulai.substring(0,5) + '\u2013' + data.care_jam_selesai.substring(0,5) + ')</div></div>';
      html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Nama Psikolog</div><div class="labs-info-grid__value">' + (data.care_nama_psikolog ? '<i class="fa fa-user-md labs-text-primary"></i> ' + data.care_nama_psikolog : '<span class="labs-text-muted">-</span>') + '</div></div>';
   }
   html += '    </div>';
   if (data.id_sesi) {
      html += '    <div style="margin-top:16px">';
      html += '      <a href="<?= site_url('administrator/mhcu_sesi/export_pdf/'); ?>' + data.id_sesi + '" target="_blank" class="labs-btn labs-btn--primary labs-btn--sm">';
      html += '        <i class="fa fa-file-pdf-o"></i> Export PDF Hasil Individual</a>';
      html += '    </div>';
   }
   html += '  </div>';
   html += '</div>';

   // TAB TIKET
   html += '<div class="labs-tab-content" id="tab-tiket">';
   html += '  <div class="labs-modal-section">';
   html += '    <h4 class="labs-modal-section__title"><i class="fa fa-clock-o"></i> Timeline</h4>';
   html += '    <div style="border-left:2px solid var(--labs-border);padding-left:16px;margin-left:8px">';

   html += '      <div style="position:relative;padding-bottom:14px">';
   html += '        <span style="position:absolute;left:-23px;top:2px;width:12px;height:12px;border-radius:50%;background:var(--labs-primary);border:2px solid #fff;box-shadow:0 0 0 1px var(--labs-border)"></span>';
   html += '        <div class="labs-fs-xs labs-text-muted">Dibuat</div>';
   html += '        <div class="labs-fw-semi">' + (data.created_at_formatted||'-') + '</div>';
   html += '      </div>';

   // Entri timeline jadwal care (hanya jika sudah scheduled)
   if (data.care_status === 'scheduled' && data.care_tanggal) {
      html += '    <div style="position:relative;padding-bottom:14px">';
      html += '      <span style="position:absolute;left:-23px;top:2px;width:12px;height:12px;border-radius:50%;background:var(--labs-warning);border:2px solid #fff;box-shadow:0 0 0 1px var(--labs-border)"></span>';
      html += '      <div class="labs-fs-xs labs-text-muted">Dijadwalkan (MHCU Care)</div>';
      html += '      <div class="labs-fw-semi"><i class="fa fa-calendar labs-text-primary"></i> ' + formatDateID(data.care_tanggal) + ' &mdash; Sesi ' + data.care_sesi_ke + ' (' + data.care_jam_mulai.substring(0,5) + '\u2013' + data.care_jam_selesai.substring(0,5) + ')</div>';
      if (data.care_nama_psikolog) {
         html += '      <div class="labs-fs-xs labs-text-muted"><i class="fa fa-user-md"></i> Psikolog: ' + data.care_nama_psikolog + '</div>';
      }
      html += '    </div>';
   }

   if (data.diproses_oleh) {
      html += '    <div style="position:relative;padding-bottom:14px">';
      html += '      <span style="position:absolute;left:-23px;top:2px;width:12px;height:12px;border-radius:50%;background:var(--labs-info);border:2px solid #fff;box-shadow:0 0 0 1px var(--labs-border)"></span>';
      html += '      <div class="labs-fs-xs labs-text-muted">Diproses Oleh</div>';
      html += '      <div class="labs-fw-semi">' + data.diproses_oleh + '</div>';
      html += '    </div>';
   }

   if (data.updated_at_formatted) {
      html += '    <div style="position:relative">';
      html += '      <span style="position:absolute;left:-23px;top:2px;width:12px;height:12px;border-radius:50%;background:var(--labs-success);border:2px solid #fff;box-shadow:0 0 0 1px var(--labs-border)"></span>';
      html += '      <div class="labs-fs-xs labs-text-muted">Update Terakhir</div>';
      html += '      <div class="labs-fw-semi">' + data.updated_at_formatted + '</div>';
      html += '    </div>';
   }
   html += '    </div>';
   if (data.keterangan) {
      html += '    <h4 class="labs-modal-section__title" style="margin-top:18px"><i class="fa fa-sticky-note"></i> Keterangan</h4>';
      html += '    <div style="background:var(--labs-bg-soft);padding:14px;border-radius:6px;border-left:3px solid var(--labs-info);line-height:1.6;font-size:13px">' + data.keterangan + '</div>';
   }
   html += '  </div>';
   html += '</div>';

   // TAB REKOMENDASI
   if (data.saran_rekomendasi) {
      html += '<div class="labs-tab-content" id="tab-rekomendasi">';
      html += '  <div class="labs-modal-section">';
      html += '    <h4 class="labs-modal-section__title"><i class="fa fa-comment"></i> Saran & Rekomendasi</h4>';
      html += '    <div style="background:var(--labs-bg-soft);padding:16px;border-radius:6px;border-left:3px solid var(--labs-primary);line-height:1.6;font-size:13px">' + data.saran_rekomendasi + '</div>';

      // Card keterangan tindak lanjut MHCU Care (hanya jika sudah scheduled)
      if (data.care_status === 'scheduled' && data.care_tanggal) {
         html += '    <div style="margin-top:14px;background:var(--labs-bg-soft);padding:16px;border-radius:6px;border-left:3px solid var(--labs-success);line-height:1.6;font-size:13px">';
         html += '      <div class="labs-fw-semi" style="margin-bottom:6px"><i class="fa fa-check-circle labs-text-success"></i> Tindak Lanjut Terjadwal</div>';
         html += '      Peserta sudah terjadwal mengikuti <span class="labs-fw-semi">MHCU Care</span> pada ' + formatDateID(data.care_tanggal) + ', Sesi ' + data.care_sesi_ke + ' (' + data.care_jam_mulai.substring(0,5) + '\u2013' + data.care_jam_selesai.substring(0,5) + ' WIB)' + (data.care_nama_psikolog ? ' bersama psikolog profesional <span class="labs-fw-semi">' + data.care_nama_psikolog + '</span>.' : '.');
         html += '    </div>';
      }

      html += '  </div>';
      html += '</div>';
   }

   $('#detail_content').html(html);
   if (window.LabsUI && LabsUI.initTabs) LabsUI.initTabs();
}
</script>