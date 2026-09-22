<?php
// Inisial untuk avatar
function labs_initials($name) {
    $name = trim($name);
    if ($name === '') return '?';
    $parts = preg_split('/\s+/', $name);
    if (count($parts) >= 2) {
        return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
    }
    return strtoupper(mb_substr($parts[0], 0, 2));
}

// Kategori → badge class mapping (sesuai profil warna 8-dashboard)
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
?>

<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<style>
@media (min-width: 992px) {
   .col-md-2-4 { width: 20%; float: left; padding-left: 15px; padding-right: 15px; }
}
@media (max-width: 991px) {
   .col-md-2-4 { width: 50%; float: left; padding-left: 15px; padding-right: 15px; }
}
.labs-table-sort th { cursor: pointer; user-select: none; }
.labs-table-sort th .sort-icon { opacity: 0.4; margin-left: 4px; font-size: 10px; }
.labs-table-sort th.active .sort-icon { opacity: 1; color: var(--labs-primary); }
</style>

<section class="content-header">
   <h1>
      Monitoring Sesi MHCU
      <small class="labs-text-muted">Dashboard</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Monitoring Sesi MHCU</li>
   </ol>
</section>

<section class="content">

   <!-- STAT CARDS -->
   <div class="row">
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--primary">
               <i class="fa fa-users"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Total Sesi</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->total); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--success">
               <i class="fa fa-check-circle"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Selesai</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->selesai); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--warning">
               <i class="fa fa-clock-o"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Belum Selesai</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->belum); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--danger">
               <i class="fa fa-exclamation-triangle"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Krisis</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->krisis); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--warning">
               <i class="fa fa-user-times"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Tidak Hadir</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->tidak_hadir); ?>">0</div>
            </div>
         </div>
      </div>
   </div>

   <!-- CHART TOGGLE -->
   <div class="labs-toggle-section">
      <button type="button" class="labs-toggle-trigger" data-labs-toggle="chart-section">
         <span class="labs-toggle-trigger__icon"><i class="fa fa-chevron-right"></i></span>
         <span class="labs-toggle-trigger__label">
            <i class="fa fa-bar-chart"></i> Visualisasi Data (Chart)
         </span>
         <span class="labs-toggle-trigger__badge">Hidden</span>
      </button>

      <div class="labs-toggle-content" id="chart-section">
         <div class="row">
            <div class="col-md-5">
               <div class="labs-card">
                  <div class="labs-card__header">
                     <h3 class="labs-card__title"><i class="fa fa-pie-chart"></i> Distribusi Kategori</h3>
                  </div>
                  <div class="labs-card__body">
                     <canvas id="chartPie" height="260"></canvas>
                  </div>
               </div>
            </div>
            <div class="col-md-7">
               <div class="labs-card">
                  <div class="labs-card__header">
                     <h3 class="labs-card__title"><i class="fa fa-bar-chart"></i> Rata-rata Skor per Dimensi</h3>
                     <select class="form-control input-sm" id="chartPeriode" style="width:200px">
                        <option value="">Semua Periode</option>
                        <?php foreach($periodes as $p): ?>
                        <option value="<?= $p->id_mhcu_periode; ?>"><?= _ent($p->nama_periode); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="labs-card__body">
                     <canvas id="chartBar" height="260"></canvas>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- MAIN LIST -->
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">

            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-list-alt"></i>
                  Daftar Sesi
                  <span class="labs-badge labs-badge--default labs-ml-2"><?= $session_counts; ?> sesi</span>
               </h3>
               <div>
                  <a href="javascript:void(0);" id="btn_peserta_tidak_hadir" class="labs-btn labs-btn--warning labs-btn--sm" title="Daftar peserta yang tidak submit sesi MHCU pada periode ini">
                     <i class="fa fa-user-times"></i> Peserta Tidak Hadir
                  </a>
                  <a href="javascript:void(0);" id="btn_export_excel" class="labs-btn labs-btn--success labs-btn--sm" title="Export Excel (minimal filter periode)">
                     <i class="fa fa-file-excel-o"></i> Export Excel
                  </a>
                  <a href="javascript:void(0);" id="btn_export_rekap" class="labs-btn labs-btn--success labs-btn--sm" title="Export Rekap Skor Instrumen (single sheet, minimal filter periode)">
                     <i class="fa fa-file-excel-o"></i> Export Rekap Instrumen
                  </a>
                  <button type="submit" form="formBulkPdf" id="btn_export_pdf_bulk" class="labs-btn labs-btn--danger labs-btn--sm" title="Export PDF terpilih (ZIP)">
                     <i class="fa fa-file-pdf-o"></i> Export PDF
                  </button>
                  <a href="javascript:void(0);" id="btn_recalculate_periode" class="labs-btn labs-btn--warning labs-btn--sm" title="Hitung ulang skor untuk SEMUA sesi selesai di periode aktif (gunakan setelah fix bug penilaian)">
                     <i class="fa fa-refresh"></i> Hitung Ulang Periode Ini
                  </a>
               </div>
            </div>

            <div class="labs-card__body">

               <!-- FILTERS -->
               <form action="<?= base_url('administrator/mhcu_sesi/index'); ?>" method="GET" class="labs-filter-inline">
                  <select class="form-control" name="periode" title="Periode">
                     <option value="">Semua Periode</option>
                     <?php foreach($periodes as $p): ?>
                     <option value="<?= $p->id_mhcu_periode; ?>" <?= ($this->input->get('periode') == $p->id_mhcu_periode) ? 'selected' : ''; ?>><?= _ent($p->nama_periode); ?></option>
                     <?php endforeach; ?>
                  </select>

                  <select class="form-control" name="role" title="Role">
                     <option value="">Semua Role</option>
                     <?php foreach($roles as $r): ?>
                     <option value="<?= _ent($r->presensi_role); ?>" <?= ($this->input->get('role') == $r->presensi_role) ? 'selected' : ''; ?>><?= _ent($r->presensi_role); ?></option>
                     <?php endforeach; ?>
                  </select>

                  <select class="form-control" name="status" title="Status">
                     <option value="">Semua Status</option>
                     <option value="belum_selesai" <?= ($this->input->get('status') == 'belum_selesai') ? 'selected' : ''; ?>>Belum</option>
                     <option value="selesai" <?= ($this->input->get('status') == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                  </select>

                  <select class="form-control" name="kategori" title="Kategori">
                     <option value="">Semua Kategori</option>
                     <option value="Immediate Professional Follow-up" <?= ($this->input->get('kategori') == 'Immediate Professional Follow-up') ? 'selected' : ''; ?>>Immediate Professional Follow-up</option>
                     <option value="High Occupational Risk" <?= ($this->input->get('kategori') == 'High Occupational Risk') ? 'selected' : ''; ?>>High Occupational Risk</option>
                     <option value="Burnout with emotional distress" <?= ($this->input->get('kategori') == 'Burnout with emotional distress') ? 'selected' : ''; ?>>Burnout with emotional distress</option>
                     <option value="Emotional Distress" <?= ($this->input->get('kategori') == 'Emotional Distress') ? 'selected' : ''; ?>>Emotional Distress</option>
                     <option value="Burnout Dominant" <?= ($this->input->get('kategori') == 'Burnout Dominant') ? 'selected' : ''; ?>>Burnout Dominant</option>
                     <option value="Needs Monitoring" <?= ($this->input->get('kategori') == 'Needs Monitoring') ? 'selected' : ''; ?>>Needs Monitoring</option>
                     <option value="Workplace Support Needed" <?= ($this->input->get('kategori') == 'Workplace Support Needed') ? 'selected' : ''; ?>>Workplace Support Needed</option>
                     <option value="Healthy but Fatigued" <?= ($this->input->get('kategori') == 'Healthy but Fatigued') ? 'selected' : ''; ?>>Healthy but Fatigued</option>
                     <option value="Optimal Wellbeing" <?= ($this->input->get('kategori') == 'Optimal Wellbeing') ? 'selected' : ''; ?>>Optimal Wellbeing</option>
                  </select>

                  <div class="labs-filter-inline__search">
                     <i class="fa fa-search labs-filter-inline__search-icon"></i>
                     <input type="text" class="form-control" name="q" placeholder="Cari NPP / Nama…" value="<?= htmlspecialchars($this->input->get('q'), ENT_QUOTES); ?>">
                  </div>

                  <button type="submit" class="labs-btn labs-btn--primary labs-btn--sm"><i class="fa fa-filter"></i></button>
                  <a href="<?= base_url('administrator/mhcu_sesi'); ?>" class="labs-btn labs-btn--default labs-btn--sm" title="Reset"><i class="fa fa-undo"></i></a>
               </form>

               <!-- TABLE -->
               <form id="formBulkPdf" method="POST" action="<?= site_url('administrator/mhcu_sesi/export_pdf_bulk'); ?>">
               <div class="labs-table-wrap">
                  <div class="labs-table-scroll">
                  <table class="labs-table labs-table-sort" id="tableSesi">
                     <thead>
                        <tr>
                           <th style="width:30px"><input type="checkbox" id="check_all" title="Pilih semua"></th>
                           <th data-sort="nama">Peserta <span class="sort-icon"><i class="fa fa-sort"></i></span></th>
                           <th>Role</th>
                           <th>Periode</th>
                           <th data-sort="status">Status <span class="sort-icon"><i class="fa fa-sort"></i></span></th>
                           <th data-sort="kategori">Kategori <span class="sort-icon"><i class="fa fa-sort"></i></span></th>
                           <th class="labs-cell-numeric">Krisis</th>
                           <th>Selesai</th>
                           <th class="labs-cell-numeric">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php foreach($sessions as $s):
                        $initials = labs_initials($s->nama_lengkap);
                     ?>
                        <tr>
                           <td><input type="checkbox" name="ids[]" value="<?= $s->id_mhcu_sesi; ?>" class="chk_row"></td>
                           <td>
                              <div class="labs-name-cell">
                                 <span class="labs-avatar"><?= $initials; ?></span>
                                 <div>
                                    <div class="labs-fw-semi"><?= _ent($s->nama_lengkap); ?></div>
                                    <div class="labs-fs-xs labs-text-muted">NPP: <?= _ent($s->npp); ?></div>
                                 </div>
                              </div>
                           </td>
                           <td><span class="labs-badge labs-badge--info"><?= _ent($s->presensi_role); ?></span></td>
                           <td><?= _ent($s->nama_periode); ?></td>
                           <td>
                              <?php if ($s->status == 'selesai'): ?>
                                 <span class="labs-badge labs-badge--success"><span class="dot"></span> Selesai</span>
                              <?php else: ?>
                                 <span class="labs-badge labs-badge--warning"><span class="dot"></span> Belum</span>
                              <?php endif; ?>
                           </td>
                           <td>
                              <?php
                              if (!empty($s->kategori_keseluruhan)) {
                                 $warna_map = array('hijau_tua'=>'success','hijau_muda'=>'success','hijau_pudar'=>'success','kuning'=>'warning','coklat_orange'=>'orange','orange_tua'=>'orange','orange_muda'=>'orange','merah_muda'=>'danger','merah_tua'=>'danger');
                                 $cls = isset($warna_map[$s->kategori_warna]) ? $warna_map[$s->kategori_warna] : 'default';
                                 echo '<span class="labs-badge labs-badge--' . $cls . '">' . _ent($s->kategori_keseluruhan) . '</span>';
                              } else {
                                 echo '<span class="labs-cell-muted">-</span>';
                              }
                              ?>
                           </td>
                           <td class="labs-cell-numeric">
                              <?php echo ($s->is_krisis == 1) ? '<span class="labs-badge labs-badge--danger labs-badge--pulse">KRISIS</span>' : '<span class="labs-cell-muted">-</span>'; ?>
                           </td>
                           <td>
                              <?php if ($s->submitted_at): ?>
                                 <div class="labs-fs-sm"><?= formatTanggal($s->submitted_at); ?></div>
                                 <div class="labs-fs-xs labs-text-muted"><?= date('H:i', strtotime($s->submitted_at)); ?> WIB</div>
                              <?php else: ?>
                                 <span class="labs-cell-muted">-</span>
                              <?php endif; ?>
                           </td>
                           <td class="labs-cell-numeric">
                              <button type="button" class="labs-btn labs-btn--info labs-btn--sm btn_detail" data-id="<?= $s->id_mhcu_sesi; ?>" title="Lihat Detail">
                                 <i class="fa fa-eye"></i>
                              </button>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if ($session_counts == 0): ?>
                        <tr class="labs-empty-row">
                           <td colspan="8">
                              <div class="labs-empty">
                                 <i class="fa fa-inbox"></i>
                                 <p>Belum ada sesi</p>
                              </div>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>
               </form>

               <?php if (!empty($pagination)): ?>
               <div class="labs-flex-between labs-mt-4">
                  <div class="labs-text-muted labs-fs-sm">
                     Total: <strong><?= $session_counts; ?></strong> sesi
                  </div>
                  <div><?= $pagination; ?></div>
               </div>
               <?php endif; ?>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL WARNING - Filter belum dipilih -->
<div class="modal fade labs-modal" id="modalExportWarning" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
         <div class="modal-header" style="background:linear-gradient(135deg, var(--labs-warning), #92400E)">
            <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Peringatan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
            <div class="labs-text-center" style="padding:10px 0">
               <i class="fa fa-filter" style="font-size:48px;color:var(--labs-warning);margin-bottom:12px;display:block"></i>
               <p style="font-size:14px;font-weight:600;color:var(--labs-text);margin:0 0 8px 0">Filter belum dipilih</p>
               <p class="labs-text-muted" style="margin:0">Silahkan user memilih filter terlebih dahulu (minimal filter <strong>periode</strong>) sebelum export Excel.</p>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="labs-btn labs-btn--warning" data-dismiss="modal">
               <i class="fa fa-check"></i> Mengerti
            </button>
         </div>
      </div>
   </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal fade labs-modal" id="modalDetail" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-desktop"></i> Detail Sesi MHCU</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body" id="detail_content"></div>
         <div class="modal-footer">
            <button class="labs-btn labs-btn--default" data-dismiss="modal">
               <i class="fa fa-times"></i> Tutup
            </button>
            <a href="#" class="labs-btn labs-btn--danger" id="btn_modal_export_pdf" target="_blank" style="display:none">
               <i class="fa fa-file-pdf-o"></i> Export PDF
            </a>
            <a href="#" class="labs-btn labs-btn--success" id="btn_modal_export_excel" style="display:none" title="Export Excel sesi ini">
               <i class="fa fa-file-excel-o"></i> Export Excel
            </a>
            <a href="javascript:void(0);" class="labs-btn labs-btn--warning" id="btn_modal_recalc" style="display:none" title="Hitung ulang skor sesi ini (pakai setelah ada perubahan instrument/library)">
               <i class="fa fa-refresh"></i> Hitung Ulang Skor
            </a>
         </div>
      </div>
   </div>
</div>

<!-- MODAL PESERTA TIDAK HADIR -->
<div class="modal fade labs-modal" id="modalTidakHadir" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header" style="background:linear-gradient(135deg, var(--labs-warning), #92400E)">
            <h4 class="modal-title"><i class="fa fa-user-times"></i> Peserta Tidak Hadir</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
            <div id="tidak_hadir_summary" class="labs-mb-4" style="display:flex;gap:12px;flex-wrap:wrap"></div>
            <div id="tidak_hadir_content">
               <div class="labs-text-center" style="padding:20px 0">
                  <div class="labs-skeleton labs-skeleton--block" style="height:30px;margin-bottom:8px"></div>
                  <div class="labs-skeleton labs-skeleton--block" style="height:30px;margin-bottom:8px"></div>
                  <div class="labs-skeleton labs-skeleton--block" style="height:30px"></div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button class="labs-btn labs-btn--default" data-dismiss="modal">
               <i class="fa fa-times"></i> Tutup
            </button>
            <a href="#" class="labs-btn labs-btn--success" id="btn_modal_export_tidak_hadir">
               <i class="fa fa-file-excel-o"></i> Export Excel
            </a>
         </div>
      </div>
   </div>
</div>

<!-- SKELETON TEMPLATE untuk detail (di-clone oleh JS) -->
<script type="text/template" id="detail-skeleton">
<div class="labs-text-center" style="padding: 20px 0;">
   <div class="labs-skeleton labs-skeleton--circle"></div>
   <div class="labs-skeleton labs-skeleton--lg labs-skeleton--block" style="width:40%;margin:0 auto 16px"></div>
   <div class="labs-skeleton labs-skeleton--block" style="height:80px"></div>
</div>
</script>

<script>
// ============== DETAIL MODAL ==============
$(document).ready(function(){
   $('.btn_detail').click(function(){
      var id = $(this).data('id');
      var content = $('#detail_content');
      content.html($('#detail-skeleton').html());
      $('#modalDetail').modal('show');

      $.ajax({
         url: '<?= site_url('administrator/mhcu_sesi/get_detail/'); ?>' + id,
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
});

function labsKategoriBadge(k){
   var map = {
      'Immediate Professional Follow-up':'danger',
      'High Occupational Risk':'danger',
      'Burnout with emotional distress':'warning',
      'Emotional Distress':'orange',
      'Burnout Dominant':'orange',
      'Needs Monitoring':'warning',
      'Workplace Support Needed':'success',
      'Healthy but Fatigued':'info',
      'Optimal Wellbeing':'success'
   };
   return map[k] || 'default';
}
function labsSkorBadge(k){
   if (['Risiko Tinggi','Berat','Sangat Tinggi','Cukup Parah'].indexOf(k) !== -1) return 'danger';
   if (['Sedang','Tinggi','Menurun','Perlu Perhatian'].indexOf(k) !== -1) return 'warning';
   if (['Ringan','Risiko Sedang'].indexOf(k) !== -1) return 'info';
   return 'success';
}
function labsInitial(name){
   name = (name||'').trim();
   if (!name) return '?';
   var parts = name.split(/\s+/);
   if (parts.length >= 2) return (parts[0][0] + parts[parts.length-1][0]).toUpperCase();
   return parts[0].substring(0,2).toUpperCase();
}
function labsAvatar(name, size){
   var cls = 'labs-avatar' + (size ? ' labs-avatar--'+size : '');
   return '<span class="'+cls+'">' + labsInitial(name) + '</span>';
}

function renderDetail(data){
   var s = data.sesi;
   var h = data.hasil;
   var scores = data.scores || [];
   var peringatan = data.peringatan || [];

   var html = '';

   // Tabs
   html += '<div class="labs-modal-tabs">';
   html += '  <div class="labs-modal-tabs__item active" data-tab="tab-profil"><i class="fa fa-user"></i> Profil</div>';
   html += '  <div class="labs-modal-tabs__item" data-tab="tab-hasil"><i class="fa fa-heart"></i> Hasil & Skor</div>';
   if (peringatan.length > 0) {
      html += '  <div class="labs-modal-tabs__item" data-tab="tab-peringatan"><i class="fa fa-bell"></i> Peringatan <span class="labs-badge labs-badge--danger">' + peringatan.length + '</span></div>';
   }
   html += '</div>';

   // TAB PROFIL
   html += '<div class="labs-tab-content active" id="tab-profil">';
   html += '  <div class="labs-modal-section">';
   html += '    <h4 class="labs-modal-section__title"><i class="fa fa-user-circle"></i> Data Peserta</h4>';
   html += '    <div style="display:flex;gap:16px;align-items:center;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #E7E5E4">';
   html += '      ' + labsAvatar(s.nama_lengkap, 'lg');
   html += '      <div>';
   html += '        <div style="font-size:16px;font-weight:600;color:#1C1917">' + (s.nama_lengkap||'') + '</div>';
   html += '        <div style="font-size:12px;color:#57534E">NPP: ' + (s.npp||'') + '</div>';
   html += '      </div>';
   html += '    </div>';
   html += '    <div class="labs-info-grid">';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Role</div><div class="labs-info-grid__value"><span class="labs-badge labs-badge--info">' + (s.presensi_role||'') + '</span></div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Periode</div><div class="labs-info-grid__value">' + (s.nama_periode||'-') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Status</div><div class="labs-info-grid__value">' + (s.status==='selesai' ? '<span class="labs-badge labs-badge--success"><span class="dot"></span> Selesai</span>' : '<span class="labs-badge labs-badge--warning"><span class="dot"></span> Belum</span>') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Waktu Selesai</div><div class="labs-info-grid__value">' + (s.submitted_at_formatted||'-') + '</div></div>';
   html += '    </div>';
   html += '  </div>';
   html += '</div>';

   // TAB HASIL
   html += '<div class="labs-tab-content" id="tab-hasil">';
   if (h) {
      var kclr = h.kategori_keseluruhan;
      var warnaMap = {hijau_tua:'success',hijau_muda:'success',hijau_pudar:'success',kuning:'warning',coklat_orange:'orange',orange_tua:'orange',orange_muda:'orange',merah_muda:'danger',merah_tua:'danger'};
      var bcls = warnaMap[h.kategori_warna] || 'default';
      html += '  <div class="labs-modal-section">';
      html += '    <h4 class="labs-modal-section__title"><i class="fa fa-heart"></i> Kesimpulan</h4>';
      html += '    <div class="labs-text-center">';
      html += '      <span class="labs-badge labs-badge--solid labs-badge--' + bcls + '" style="font-size:18px;padding:10px 24px">' + (kclr||'') + '</span>';
      if (h.is_krisis == 1) {
         html += '  <div style="margin-top:12px"><span class="labs-badge labs-badge--solid labs-badge--danger labs-badge--pulse" style="font-size:12px"><i class="fa fa-exclamation-triangle"></i> STATUS KRISIS</span></div>';
      }
      html += '    </div>';
      if (h.saran_rekomendasi) {
         html += '  <div style="margin-top:16px;padding-top:16px;border-top:1px solid #E7E5E4">';
         html += '    <div class="labs-fs-xs labs-text-muted labs-fw-bold labs-mb-2" style="text-transform:uppercase;letter-spacing:0.5px">Rekomendasi</div>';
         html += '    <div style="font-size:13px;line-height:1.6;color:#1C1917">' + (h.saran_rekomendasi) + '</div>';
         html += '  </div>';
      }
      html += '  </div>';
   }

   if (scores.length > 0) {
      // Group by instrument
      var grouped = {};
      scores.forEach(function(sc){
         var k = sc.kode_instrument + '|' + sc.nama_instrument;
         if (!grouped[k]) grouped[k] = [];
         grouped[k].push(sc);
      });

      html += '  <div class="labs-modal-section">';
      html += '    <h4 class="labs-modal-section__title"><i class="fa fa-bar-chart"></i> Skor Instrument</h4>';
      html += '    <div class="labs-table-wrap">';
      html += '      <table class="labs-table">';
      html += '        <thead><tr><th>Instrument</th><th>Dimensi</th><th class="labs-cell-numeric">Skor</th><th>Kategori</th></tr></thead>';
      html += '        <tbody>';
      for (var k in grouped) {
         var parts = k.split('|');
         var instLabel = parts[0] + ' - ' + parts[1];
         var first = true;
         grouped[k].forEach(function(sc){
            var lbl = labsSkorBadge(sc.kategori);
            html += '<tr>';
            html += '  <td>' + (first ? '<strong>' + instLabel + '</strong>' : '') + '</td>';
            html += '  <td>' + (sc.dimensi_aspek||'') + '</td>';
            html += '  <td class="labs-cell-numeric">' + (sc.skor||0) + '</td>';
            html += '  <td><span class="labs-badge labs-badge--' + lbl + '">' + (sc.kategori||'') + '</span></td>';
            html += '</tr>';
            first = false;
         });
      }
      html += '        </tbody>';
      html += '      </table>';
      html += '    </div>';
      html += '  </div>';
   } else {
      html += '  <div class="labs-empty"><i class="fa fa-hourglass-half"></i><p>Sesi belum selesai — skor belum tersedia</p></div>';
   }
   html += '</div>';

   // TAB PERINGATAN
   if (peringatan.length > 0) {
      html += '<div class="labs-tab-content" id="tab-peringatan">';
      html += '  <div class="labs-modal-section">';
      html += '    <h4 class="labs-modal-section__title"><i class="fa fa-bell"></i> Tiket Peringatan</h4>';
      peringatan.forEach(function(p){
         var statusBadge = p.status_alert === 'waiting' ? 'warning' : (p.status_alert === 'process' ? 'info' : 'success');
         html += '<div style="border-left:3px solid #B91C1C;background:#FEE2E2;padding:12px 16px;border-radius:6px;margin-bottom:12px">';
         html += '  <div class="labs-flex-between labs-mb-2">';
         html += '    <strong style="color:#B91C1C">' + (p.jenis_alert||'') + '</strong>';
         html += '    <span class="labs-badge labs-badge--' + statusBadge + '">' + (p.status_alert||'') + '</span>';
         html += '  </div>';
         html += '  <div class="labs-fs-sm"><strong>Diproses oleh:</strong> ' + (p.diproses_oleh||'-') + '</div>';
         html += '  <div class="labs-fs-xs labs-text-muted" style="margin-top:6px">Dibuat: ' + (p.created_at_formatted || p.created_at) + '</div>';
         html += '</div>';
      });
      html += '  </div>';
      html += '</div>';
   }

   $('#detail_content').html(html);
   // Re-init labs-ui untuk tab handler
   if (window.LabsUI && LabsUI.initTabs) LabsUI.initTabs();

   // Show/hide export PDF button
   var $btnPdf = $('#btn_modal_export_pdf');
   if (h && s.status === 'selesai') {
      $btnPdf.attr('href', '<?= site_url('administrator/mhcu_sesi/export_pdf/'); ?>' + s.id_mhcu_sesi).show();
   } else {
      $btnPdf.hide();
   }

   // Show/hide export Excel button (kondisi sama dengan PDF)
   var $btnExcel = $('#btn_modal_export_excel');
   if (h && s.status === 'selesai') {
      $btnExcel.attr('href', '<?= site_url('administrator/mhcu_sesi/export_excel/'); ?>' + s.id_mhcu_sesi).show();
   } else {
      $btnExcel.hide();
   }

   // Show/hide recalc button (hanya untuk sesi selesai)
   var $btnRecalc = $('#btn_modal_recalc');
   if (s && s.status === 'selesai') {
      $btnRecalc.data('id', s.id_mhcu_sesi).show();
   } else {
      $btnRecalc.hide();
   }
}

// ============== PESERTA TIDAK HADIR MODAL ==============
$(document).ready(function(){
   $('#btn_peserta_tidak_hadir').click(function(){
      var content = $('#tidak_hadir_content');
      var summary = $('#tidak_hadir_summary');
      content.html('<div class="labs-text-center" style="padding:20px 0"><i class="fa fa-spinner fa-spin fa-2x labs-text-muted"></i></div>');
      summary.html('');
      $('#btn_modal_export_tidak_hadir').attr('href', '#');
      $('#modalTidakHadir').modal('show');

      var params = new URLSearchParams();
      var periodeVal = $('select[name="periode"]').val() || '<?= $filters["id_mhcu_periode"]; ?>';
      if (periodeVal) params.set('periode', periodeVal);
      var roleVal = $('select[name="role"]').val();
      if (roleVal) params.set('role', roleVal);

      $.ajax({
         url: '<?= site_url('administrator/mhcu_sesi/tidak_hadir'); ?>',
         method: 'GET',
         data: params.toString(),
         dataType: 'json',
         success: function(data){
            if (!data || data.error) {
               content.html('<div class="labs-empty"><i class="fa fa-exclamation-circle labs-text-danger"></i><p>' + (data.error || 'Data tidak ditemukan') + '</p></div>');
               return;
            }
            renderTidakHadir(data);
         },
         error: function(){
            content.html('<div class="labs-empty"><i class="fa fa-exclamation-circle labs-text-danger"></i><p>Gagal memuat data</p></div>');
         }
      });
   });
});

function renderTidakHadir(data){
   var rows = data.rows || [];
   var summary = $('#tidak_hadir_summary');
   var content = $('#tidak_hadir_content');

   // Summary badges
   var html = '<span class="labs-badge labs-badge--info">Periode: ' + (data.periode_nama || '-') + '</span>';
   html += '<span class="labs-badge labs-badge--warning">Tidak Hadir: <strong>' + data.count + '</strong></span>';
   html += '<span class="labs-badge labs-badge--default">Total Peserta: ' + data.total_peserta + '</span>';
   summary.html(html);

   // Build export URL
   var exportUrl = '<?= site_url('administrator/mhcu_sesi/export_tidak_hadir'); ?>?periode=' + data.periode_id;
   var roleVal = $('select[name="role"]').val();
   if (roleVal) exportUrl += '&role=' + encodeURIComponent(roleVal);
   $('#btn_modal_export_tidak_hadir').attr('href', exportUrl);

   // Build table
   if (rows.length === 0) {
      content.html('<div class="labs-empty"><i class="fa fa-check-circle labs-text-success"></i><p>Semua peserta sudah submit pada periode ini</p></div>');
      return;
   }

   var tHtml = '<div class="labs-table-wrap"><div class="labs-table-scroll"><table class="labs-table">';
   tHtml += '<thead><tr><th style="width:40px">No</th><th style="width:120px">NPP</th><th>Nama</th><th>Role</th></tr></thead><tbody>';
   for (var i = 0; i < rows.length; i++) {
      var r = rows[i];
      var init = labsInitial(r.nama_lengkap);
      tHtml += '<tr>';
      tHtml += '<td class="labs-cell-numeric">' + (i + 1) + '</td>';
      tHtml += '<td>' + (r.npp || '-') + '</td>';
      tHtml += '<td><div class="labs-name-cell"><span class="labs-avatar">' + init + '</span><div class="labs-fw-semi">' + (r.nama_lengkap || '-') + '</div></div></td>';
      tHtml += '<td><span class="labs-badge labs-badge--info">' + (r.presensi_role || '-') + '</span></td>';
      tHtml += '</tr>';
   }
   tHtml += '</tbody></table></div></div>';
   content.html(tHtml);
}
</script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
var pieChart, barChart;
var kategoriData = <?= json_encode($kategori_stats); ?>;
var warnaMap = {
   'Immediate Professional Follow-up': '#C00000',
   'High Occupational Risk': '#BD5163',
   'Burnout with emotional distress': '#806000',
   'Emotional Distress': '#F4B083',
   'Burnout Dominant': '#C45911',
   'Needs Monitoring': '#FFC000',
   'Workplace Support Needed': '#E2EFD9',
   'Healthy but Fatigued': '#A8D08D',
   'Optimal Wellbeing': '#538135',
   'Belum Selesai': '#A8A29E'
};

function renderPie(data) {
   var labels = [], values = [], colors = [];
   for (var i = 0; i < data.length; i++) {
      labels.push(data[i].kategori || 'Belum Selesai');
      values.push(parseInt(data[i].jumlah));
      colors.push(warnaMap[data[i].kategori] || '#A8A29E');
   }
   if (pieChart) pieChart.destroy();
   var ctx = document.getElementById('chartPie').getContext('2d');
   pieChart = new Chart(ctx, {
      type: 'doughnut',
      data: { labels: labels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
      options: {
         responsive: true,
         maintainAspectRatio: false,
         plugins: {
            legend: { position: 'bottom', labels: { padding: 12, font: { size: 12 }, usePointStyle: true } }
         },
         cutout: '60%'
      }
   });
}

function loadBarChart(periodeId) {
   $.ajax({
      url: '<?= site_url('administrator/mhcu_sesi/get_chart_data'); ?>',
      data: { periode: periodeId || '' },
      dataType: 'json',
      success: function(res) {
         if (!res || !res.scores || res.scores.length === 0) {
            if (barChart) barChart.destroy();
            return;
         }
         var labels = [], values = [], colors = [];
         var colorPool = ['#C2410C','#15803D','#B91C1C','#B45309','#9A3412','#0E7490','#EA580C','#1C1917'];
         for (var i = 0; i < res.scores.length; i++) {
            var sc = res.scores[i];
            labels.push(sc.kode_instrument + ' - ' + sc.dimensi_aspek);
            values.push(parseFloat(sc.avg_skor));
            colors.push(colorPool[i % colorPool.length]);
         }
         if (barChart) barChart.destroy();
         var ctx = document.getElementById('chartBar').getContext('2d');
         barChart = new Chart(ctx, {
            type: 'bar',
            data: { labels: labels, datasets: [{ label: 'Rata-rata Skor', data: values, backgroundColor: colors, borderWidth: 1, borderRadius: 6 }] },
            options: {
               responsive: true,
               maintainAspectRatio: false,
               indexAxis: 'y',
               scales: { x: { beginAtZero: true, grid: { color: '#F5F5F4' } }, y: { grid: { display: false } } },
               plugins: { legend: { display: false } }
            }
         });
      }
   });
}

$(document).ready(function() {
   renderPie(kategoriData);
   loadBarChart('');
   $('#chartPeriode').change(function() { loadBarChart($(this).val()); });

   // Update badge "Hidden"/"Shown" pada toggle
   $(document).on('click', '.labs-toggle-trigger', function(){
      var $b = $(this).find('.labs-toggle-trigger__badge');
      var isExpanded = $(this).hasClass('is-expanded');
      $b.text(isExpanded ? 'Shown' : 'Hidden');
   });

   // Select all checkbox
   $('#check_all').change(function(){
      $('.chk_row').prop('checked', this.checked);
   });

   // Bulk export PDF - validasi minimal 1 terpilih
   $('#btn_export_pdf_bulk').click(function(e){
      if ($('.chk_row:checked').length === 0) {
         e.preventDefault();
         alert('Pilih minimal satu sesi untuk export PDF.');
         return false;
      }
   });

   // Export Excel handler - wajib ada filter periode
   $('#btn_export_excel').click(function(){
      var periode = $('select[name="periode"]').val();
      if (!periode) {
         $('#modalExportWarning').modal('show');
         return;
      }
      // Bangun query string dari filter aktif, hanya field yang relevan untuk export
      var params = new URLSearchParams();
      params.set('periode', periode);
      var role = $('select[name="role"]').val();
      if (role) params.set('role', role);
      var kategori = $('select[name="kategori"]').val();
      if (kategori) params.set('kategori', kategori);
      window.location.href = '<?= base_url('administrator/mhcu_sesi/export'); ?>?' + params.toString();
   });

   // Export Rekap Instrumen handler - wajib ada filter periode
   $('#btn_export_rekap').click(function(){
      var periode = $('select[name="periode"]').val();
      if (!periode) {
         $('#modalExportWarning').modal('show');
         return;
      }
      var params = new URLSearchParams();
      params.set('periode', periode);
      var role = $('select[name="role"]').val();
      if (role) params.set('role', role);
      var kategori = $('select[name="kategori"]').val();
      if (kategori) params.set('kategori', kategori);
      window.location.href = '<?= base_url('administrator/mhcu_sesi/export_rekap_instrumen'); ?>?' + params.toString();
   });

   // Recalc single sesi (dari modal detail)
   $('#btn_modal_recalc').click(function(){
      var id = $(this).data('id');
      if (!id) return;
      if (!confirm('Hitung ulang skor untuk sesi #' + id + '? Data hasil sebelumnya akan ditimpa.')) return;
      var $btn = $(this);
      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menghitung...');
      $.getJSON('<?= site_url('administrator/mhcu_sesi/recalculate'); ?>?id_sesi=' + id)
         .done(function(res){
            alert((res.status ? '✅ ' : '❌ ') + (res.message || ''));
            if (res.status && res.data && res.data.kategori_keseluruhan) {
               // Reload list agar kolom kategori terupdate
               location.reload();
            }
         })
         .fail(function(xhr){
            alert('❌ Gagal: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText));
         })
         .always(function(){
            $btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Hitung Ulang Skor');
         });
   });

   // Recalc semua sesi selesai di PERIODE AKTIF (filter)
   $('#btn_recalculate_periode').click(function(){
      var idPeriode = $('select[name="periode"]').val();
      if (!idPeriode) {
         alert('Pilih periode dulu di filter sebelum menjalankan hitung ulang.');
         return;
      }
      var namaPeriode = $('select[name="periode"] option:selected').text();
      if (!confirm('Hitung ulang skor untuk SEMUA sesi selesai di periode "' + namaPeriode + '"? Bisa memakan waktu beberapa menit.')) return;
      var $btn = $(this);
      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menghitung...');
      $.getJSON('<?= site_url('administrator/mhcu_sesi/recalculate_periode'); ?>?id_periode=' + idPeriode)
         .done(function(res){
            var dist = res.data && res.data.distribution ? res.data.distribution : {};
            var distStr = Object.keys(dist).map(function(k){return k + ': ' + dist[k];}).join('\n') || '(kosong)';
            alert('✅ ' + res.message + '\n\nDistribusi kategori:\n' + distStr);
            location.reload();
         })
         .fail(function(xhr){
            alert('❌ Gagal: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText));
         })
         .always(function(){
            $btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Hitung Ulang Periode Ini');
         });
   });
});

// ============== TABLE SORT (client-side) ==============
$(document).ready(function(){
   var $table = $('#tableSesi');
   var sortDir = { nama: 'asc', status: 'asc', kategori: 'asc' };

   $table.find('th[data-sort]').click(function(){
      var col = $(this).data('sort');
      sortDir[col] = (sortDir[col] === 'asc') ? 'desc' : 'asc';
      var dir = sortDir[col];

      $table.find('th[data-sort]').removeClass('active').find('.sort-icon').html('<i class="fa fa-sort"></i>');
      $(this).addClass('active').find('.sort-icon').html('<i class="fa fa-sort-' + (dir === 'asc' ? 'asc' : 'desc') + '"></i>');

      var colIdx = col === 'nama' ? 1 : (col === 'status' ? 4 : 5); // Peserta=1, Status=4, Kategori=5
      var rows = $table.find('tbody tr.labs-empty-row').detach();
      var dataRows = $table.find('tbody tr').detach();

      dataRows.sort(function(a, b){
         var aText = $(a).find('td').eq(colIdx).text().trim().toLowerCase();
         var bText = $(b).find('td').eq(colIdx).text().trim().toLowerCase();
         if (aText === bText) return 0;
         if (dir === 'asc') return aText > bText ? 1 : -1;
         return aText < bText ? 1 : -1;
      });

      $table.find('tbody').append(dataRows);
      if (rows.length) $table.find('tbody').append(rows);
   });
});
</script>