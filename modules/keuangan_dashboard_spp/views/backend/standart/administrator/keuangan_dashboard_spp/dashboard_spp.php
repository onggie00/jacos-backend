<?php
function kd_rupiah_js() { /* helper dipakai di JS bawah */ }
?>

<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<style>
/* Scope modul: sticky legend + responsive chart/table */
.kd-legend {
   position: sticky;
   top: 0;
   z-index: 20;
   background: var(--labs-bg-card);
   border: 1px solid var(--labs-border);
   border-radius: var(--labs-radius-md);
   padding: 8px 14px;
   margin-bottom: 15px;
}
.kd-chart-box { position: relative; height: 320px; }
.kd-vs { font-size: 11px; margin-top: 2px; }
.kd-vs .up { color: var(--labs-success); }
.kd-vs .down { color: var(--labs-danger); }
.labs-stat-card__icon--purple { background: linear-gradient(135deg, #7C3AED, #5B21B6); }

/* chip filter aktif */
.kd-chips {
   display: flex;
   flex-wrap: wrap;
   align-items: center;
   gap: 8px;
   margin: 15px 0 0;
}
.kd-chips .kd-chip {
   background: var(--labs-bg-soft);
   border: 1px solid var(--labs-border);
   border-radius: 999px;
   padding: 3px 12px;
   font-size: 12px;
   color: var(--labs-text-muted);
}
.kd-chips .kd-chip b { color: var(--labs-text); }

/* spinner loading utk select filter (animated SVG bg, tanpa ubah layout) */
select.kd-loading {
   background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='3' stroke-linecap='round'%3E%3Ccircle cx='12' cy='12' r='9' opacity='.25'/%3E%3Cpath d='M21 12a9 9 0 0 0-9-9'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 12 12' to='360 12 12' dur='.8s' repeatCount='indefinite'/%3E%3C/path%3E%3C/svg%3E");
   background-repeat: no-repeat;
   background-position: right 10px center;
   background-size: 14px 14px;
}

/* skeleton overlay utk semua section */
.kd-parent { position: relative; }
.kd-skel-overlay {
   position: absolute;
   inset: 0;
   z-index: 5;
   display: none;
   background: rgba(255, 255, 255, 0.7);
   border-radius: var(--labs-radius-md);
   padding: 12px;
}
.kd-skel-overlay.on { display: block; }

.labs-table-sort th { cursor: pointer; user-select: none; }
.labs-table-sort th .sort-icon { opacity: 0.4; margin-left: 4px; font-size: 10px; }
.labs-table-sort th.active .sort-icon { opacity: 1; color: var(--labs-primary); }
@media (max-width: 991px) {
   .kd-charts .col-md-7, .kd-charts .col-md-5 { width: 100%; float: none; }
}
/* date input di filter lanjutan selaras styling select labs-ui */
.labs-filter-inline > input[type="date"] {
   flex: 1 1 140px;
   min-width: 130px;
   max-width: 190px;
   height: 36px !important;
   font-size: 12px;
   color: var(--labs-text);
   background-color: var(--labs-bg-card);
   border: 1px solid var(--labs-border-strong);
   border-radius: var(--labs-radius-sm);
   padding: 6px 10px !important;
   margin: 0;
   box-shadow: none !important;
}
.labs-filter-inline > input[type="date"]:focus {
   border-color: var(--labs-primary);
   outline: none;
   box-shadow: 0 0 0 3px rgba(194, 65, 12, 0.12) !important;
}

/* jarak antar chart card */
.kd-charts .labs-card { margin-bottom: 15px; }
@media (max-width: 767px) {
   .kd-table-card table thead { display: none; }
   .kd-table-card table, .kd-table-card table tbody,
   .kd-table-card table tr, .kd-table-card table td { display: block; width: 100%; }
   .kd-table-card table tr {
      border: 1px solid var(--labs-border);
      border-radius: var(--labs-radius-md);
      margin-bottom: 10px;
      padding: 8px;
   }
   .kd-table-card table td {
      border: none;
      padding: 3px 8px;
      text-align: left;
   }
   .kd-table-card table td:before {
      content: attr(data-label);
      font-weight: 600;
      color: var(--labs-text-muted);
      display: inline-block;
      margin-right: 6px;
   }
}
</style>

<section class="content-header">
   <h1>
      Dashboard SPP
      <small class="labs-text-muted">Monitoring pembayaran SPP</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard SPP</li>
   </ol>
</section>

<section class="content">

   <!-- STAT CARDS -->
   <div class="row kd-parent" id="kd-cards-row">
      <div class="kd-skel-overlay" id="kd-skel-cards">
         <div class="labs-skeleton labs-skeleton--block" style="height:70px;margin-bottom:10px;"></div>
         <div class="labs-skeleton labs-skeleton--block" style="height:70px;"></div>
      </div>
      <div class="col-lg-3 col-md-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--info"><i class="fa fa-file-text-o"></i></div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Total Tagihan</div>
               <div class="labs-stat-card__value" id="kd-tagihan">0</div>
               <div class="kd-vs" id="kd-vs-tagihan"></div>
            </div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--success"><i class="fa fa-check-circle-o"></i></div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Total Diterima</div>
               <div class="labs-stat-card__value" id="kd-diterima">0</div>
               <div class="kd-vs" id="kd-vs-diterima"></div>
            </div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--danger"><i class="fa fa-times-circle-o"></i></div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Total Belum Dibayar</div>
               <div class="labs-stat-card__value" id="kd-belum">0</div>
               <div class="kd-vs" id="kd-vs-belum"></div>
            </div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--purple"><i class="fa fa-pie-chart"></i></div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Kolektibilitas</div>
               <div class="labs-stat-card__value" id="kd-kolektibilitas">0%</div>
               <div class="kd-vs" id="kd-vs-kolektibilitas"></div>
            </div>
         </div>
      </div>
   </div>

   <!-- FILTER -->
   <div class="labs-card" style="margin-top:15px;">
      <div class="labs-card__body">
         <form class="labs-filter-inline" id="kd-form" onsubmit="return false;">
            <select id="kd-bulan" class="form-control" title="Bulan">
               <?php foreach ($bulan_list as $num => $nama) { ?>
               <option value="<?= $num; ?>" <?= $num == $bulan_default ? 'selected' : ''; ?>><?= $nama; ?></option>
               <?php } ?>
            </select>
            <select id="kd-tahun" class="form-control" title="Tahun">
               <?php foreach ($tahun_list as $th) { ?>
               <option value="<?= $th; ?>" <?= $th == $tahun_default ? 'selected' : ''; ?>><?= $th; ?></option>
               <?php } ?>
            </select>
            <button type="button" class="labs-btn labs-btn--primary labs-btn--sm" id="kd-btn-filter"><i class="fa fa-search"></i> Terapkan</button>
         </form>

         <!-- Filter lanjutan (collapsible) -->
         <button type="button" class="labs-toggle-trigger labs-btn labs-btn--ghost labs-btn--sm" id="kd-adv-trigger" style="margin-top:8px;">
            <span class="labs-toggle-trigger__icon"><i class="fa fa-chevron-right"></i></span>
            Filter Lanjutan
         </button>
         <div class="labs-toggle-content" id="kd-advanced">
            <form class="labs-filter-inline" onsubmit="return false;">
               <select id="kd-jenjang" class="form-control" title="Jenjang">
                  <option value="">Semua Jenjang</option>
                  <option value="SD">SD</option>
                  <option value="SMP">SMP</option>
                  <option value="SMA">SMA</option>
                  <option value="FT">FT</option>
               </select>
               <select id="kd-tingkatan" class="form-control" title="Tingkatan" disabled>
                  <option value="">Semua Tingkatan</option>
               </select>
               <select id="kd-kelas" class="form-control" title="Kelas" disabled>
                  <option value="">Semua Kelas</option>
               </select>
               <select id="kd-status" class="form-control" title="Status Transaksi">
                  <option value="">Semua Status</option>
                  <option value="0">Belum Dibayar</option>
                  <option value="1">Menunggu Pembayaran</option>
                  <option value="2">Lunas</option>
               </select>
               <span class="labs-text-muted labs-fs-sm" style="align-self:center;">Tgl Bayar:</span>
               <input type="date" id="kd-start-date" class="form-control" title="Tgl Bayar dari">
               <span class="labs-text-muted labs-fs-sm" style="align-self:center;">s/d</span>
               <input type="date" id="kd-end-date" class="form-control" title="Tgl Bayar s/d">
            </form>
         </div>
      </div>
   </div>

   <!-- CHIP FILTER AKTIF -->
   <div class="kd-chips" id="kd-chips"></div>

   <!-- LEGEND STICKY -->
   <div class="kd-legend">
      <strong>Status:</strong>
      <span class="labs-badge labs-badge--danger">Belum Dibayar</span>
      <span class="labs-badge labs-badge--warning">Menunggu Pembayaran</span>
      <span class="labs-badge labs-badge--success">Lunas</span>
      &nbsp;&nbsp;<strong>Kategori:</strong>
      <span class="labs-badge labs-badge--info">Lebih Awal</span>
      <span class="labs-badge labs-badge--success">Tepat Waktu</span>
      <span class="labs-badge labs-badge--danger">Telat Bayar</span>
      <span class="labs-badge labs-badge--orange">Pembayaran Dimuka</span>
   </div>

   <!-- CHARTS -->
   <div class="row kd-charts">
      <div class="col-md-7">
         <div class="labs-card">
            <div class="labs-card__body">
               <button type="button" class="labs-toggle-trigger" data-labs-toggle="kd-bar" style="width:100%;text-align:left;">
                  <span class="labs-toggle-trigger__icon"><i class="fa fa-chevron-right"></i></span>
                  <strong>Perbandingan Kategori Pembayaran</strong>
               </button>
               <div class="labs-toggle-content" id="kd-bar">
                  <div class="kd-chart-box"><canvas id="kd-chart-bar"></canvas>
                  <div class="kd-skel-overlay" id="kd-skel-bar"><div class="labs-skeleton labs-skeleton--block" style="height:100%;"></div></div></div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-5">
         <div class="labs-card">
            <div class="labs-card__body">
               <button type="button" class="labs-toggle-trigger" data-labs-toggle="kd-pie" style="width:100%;text-align:left;">
                  <span class="labs-toggle-trigger__icon"><i class="fa fa-chevron-right"></i></span>
                  <strong>Persentase Status Transaksi</strong>
               </button>
               <div class="labs-toggle-content" id="kd-pie">
                  <div class="kd-chart-box"><canvas id="kd-chart-pie"></canvas>
                  <div class="kd-skel-overlay" id="kd-skel-pie"><div class="labs-skeleton labs-skeleton--block" style="height:100%;"></div></div></div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- CHARTS BARIS 2: BREAKDOWN JENJANG + TREND -->
   <div class="row kd-charts">
      <div class="col-md-6">
         <div class="labs-card">
            <div class="labs-card__body">
               <button type="button" class="labs-toggle-trigger" data-labs-toggle="kd-breakdown" style="width:100%;text-align:left;">
                  <span class="labs-toggle-trigger__icon"><i class="fa fa-chevron-right"></i></span>
                  <strong>Breakdown per Jenjang</strong>
               </button>
               <div class="labs-toggle-content" id="kd-breakdown">
                  <div class="labs-text-muted" style="font-size:11px;margin-bottom:6px;">Menampilkan seluruh jenjang — filter jenjang di atas hanya menyorot jenjang terpilih.</div>
                  <div class="kd-chart-box"><canvas id="kd-chart-breakdown"></canvas>
                  <div class="kd-skel-overlay" id="kd-skel-breakdown"><div class="labs-skeleton labs-skeleton--block" style="height:100%;"></div></div></div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="labs-card">
            <div class="labs-card__body">
               <button type="button" class="labs-toggle-trigger" data-labs-toggle="kd-trend" style="width:100%;text-align:left;">
                  <span class="labs-toggle-trigger__icon"><i class="fa fa-chevron-right"></i></span>
                  <strong>Trend 12 Bulan Terakhir</strong>
               </button>
               <div class="labs-toggle-content" id="kd-trend">
                  <div class="kd-chart-box"><canvas id="kd-chart-trend"></canvas>
                  <div class="kd-skel-overlay" id="kd-skel-trend"><div class="labs-skeleton labs-skeleton--block" style="height:100%;"></div></div></div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- AGING REPORT -->
   <div class="labs-card kd-table-card" style="margin-top:15px;">
      <div class="labs-card__body">
         <button type="button" class="labs-toggle-trigger" data-labs-toggle="kd-aging" style="width:100%;text-align:left;">
            <span class="labs-toggle-trigger__icon"><i class="fa fa-chevron-right"></i></span>
            <strong>Aging Report / Daftar Penunggak</strong>
            <span class="labs-text-muted" id="kd-aging-total" style="margin-left:8px;font-weight:400;"></span>
         </button>
         <div class="labs-toggle-content" id="kd-aging">
            <form class="labs-filter-inline" onsubmit="return false;">
               <select id="kd-aging-ta" class="form-control" title="Tahun Ajaran">
                  <option value="">Semua Tahun Ajaran</option>
                  <?php foreach ($ta_options as $ta) { ?>
                  <option value="<?= $ta->id_tahun_ajaran; ?>" <?= $ta->id_tahun_ajaran == $ta_aktif ? 'selected' : ''; ?>><?= $ta->label; ?></option>
                  <?php } ?>
               </select>
               <select id="kd-aging-perpage" class="form-control" title="Baris per halaman">
                  <option value="25">25 / halaman</option>
                  <option value="50">50 / halaman</option>
                  <option value="100">100 / halaman</option>
               </select>
               <div class="labs-filter-inline__search">
                  <i class="fa fa-search labs-filter-inline__search-icon"></i>
                  <input type="text" id="kd-aging-search" class="form-control" title="Cari nama / NIS" placeholder="Cari nama / NIS..." maxlength="50">
               </div>
               <button type="button" class="labs-btn labs-btn--primary labs-btn--sm" id="kd-aging-btn-search"><i class="fa fa-search"></i> Cari</button>
               <button type="button" class="labs-btn labs-btn--success labs-btn--sm" id="kd-export-rekap" title="Export rekapitulasi tunggakan per unit & tingkatan (semester TA dari filter)"><i class="fa fa-file-excel-o"></i> Export Rekap Tunggakan</button>
            </form>
            <div id="kd-aging-skeleton">
               <div class="labs-skeleton labs-skeleton--block" style="height:28px;margin-bottom:8px;"></div>
               <div class="labs-skeleton labs-skeleton--block" style="height:28px;margin-bottom:8px;"></div>
               <div class="labs-skeleton labs-skeleton--block" style="height:28px;"></div>
            </div>
            <div id="kd-aging-empty" class="labs-empty" style="display:none;">
               <i class="fa fa-smile-o labs-empty__icon"></i>
               <p>Tidak ada penunggak.</p>
            </div>
            <div class="labs-table-wrap" id="kd-aging-wrap" style="display:none;">
               <table class="labs-table">
                  <thead>
                     <tr>
                        <th>Nama Siswa</th>
                        <th>NIS</th>
                        <th>Jenjang</th>
                        <th>Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th>Jumlah Bulan Menunggak</th>
                        <th>Detail Tunggakan</th>
                        <th>Total Tunggakan</th>
                     </tr>
                  </thead>
                  <tbody id="kd-aging-tbody"></tbody>
               </table>
            </div>
            <div class="labs-flex-between" style="margin-top:10px;">
               <span class="labs-text-muted" id="kd-aging-info"></span>
               <div class="labs-btn-group" id="kd-aging-pager"></div>
            </div>
         </div>
      </div>
   </div>

   <!-- TABEL DETAIL -->
   <div class="labs-card kd-table-card" style="margin-top:15px;">
      <div class="labs-card__body">
         <div class="labs-flex-between" style="margin-bottom:10px;">
            <strong>Detail Transaksi</strong>
            <div>
               <span class="labs-text-muted" id="kd-table-info" style="margin-right:10px;"></span>
               <button type="button" class="labs-btn labs-btn--success labs-btn--sm" id="kd-export-xls"><i class="fa fa-file-excel-o"></i> Export XLS</button>
               <button type="button" class="labs-btn labs-btn--danger labs-btn--sm" id="kd-export-pdf"><i class="fa fa-file-pdf-o"></i> Export PDF</button>
            </div>
         </div>

         <!-- skeleton loading -->
         <div id="kd-skeleton">
            <div class="labs-skeleton labs-skeleton--block" style="height:28px;margin-bottom:8px;"></div>
            <div class="labs-skeleton labs-skeleton--block" style="height:28px;margin-bottom:8px;"></div>
            <div class="labs-skeleton labs-skeleton--block" style="height:28px;margin-bottom:8px;"></div>
            <div class="labs-skeleton labs-skeleton--block" style="height:28px;margin-bottom:8px;"></div>
            <div class="labs-skeleton labs-skeleton--block" style="height:28px;"></div>
         </div>

         <!-- empty state -->
         <div id="kd-empty" class="labs-empty" style="display:none;">
            <i class="fa fa-inbox labs-empty__icon"></i>
            <p>Tidak ada data transaksi untuk filter bulan/tahun ini.</p>
         </div>

         <div class="labs-table-wrap" id="kd-table-wrap" style="display:none;">
            <table class="labs-table">
               <thead class="labs-table-sort">
                  <tr>
                     <th data-sort="no_transaksi">No Transaksi <i class="fa fa-sort sort-icon"></i></th>
                     <th data-sort="siswa">Siswa <i class="fa fa-sort sort-icon"></i></th>
                     <th data-sort="jenjang">Jenjang <i class="fa fa-sort sort-icon"></i></th>
                     <th data-sort="kelas">Kelas <i class="fa fa-sort sort-icon"></i></th>
                     <th data-sort="bulan">Bulan <i class="fa fa-sort sort-icon"></i></th>
                     <th data-sort="jumlah">Jumlah Tagihan <i class="fa fa-sort sort-icon"></i></th>
                     <th data-sort="nominal">Nominal <i class="fa fa-sort sort-icon"></i></th>
                     <th data-sort="total_dibayar">Total Dibayar <i class="fa fa-sort sort-icon"></i></th>
                     <th data-sort="status">Status <i class="fa fa-sort sort-icon"></i></th>
                     <th data-sort="kategori">Kategori <i class="fa fa-sort sort-icon"></i></th>
                     <th data-sort="tgl_bayar">Tgl Bayar <i class="fa fa-sort sort-icon"></i></th>
                  </tr>
               </thead>
               <tbody id="kd-tbody"></tbody>
            </table>
         </div>

         <div class="labs-flex-between" style="margin-top:10px;">
            <span class="labs-text-muted" id="kd-page-info"></span>
            <div class="labs-btn-group" id="kd-pager"></div>
         </div>
      </div>
   </div>

   <!-- MODAL DETAIL TUNGGAKAN -->
   <div class="modal fade labs-modal" id="kd-modal-detail" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title"><i class="fa fa-list-alt"></i> Detail Tunggakan — <span id="kd-detail-nama">-</span></h4>
            </div>
            <div class="modal-body">
               <div class="labs-flex-between" id="kd-detail-topbar" style="margin-bottom:8px;display:none;">
                  <button type="button" class="labs-btn labs-btn--danger labs-btn--sm" id="kd-detail-export-terakhir" style="display:none;"><i class="fa fa-file-pdf-o"></i> Export PDF Terakhir</button>
                  <button type="button" class="labs-btn labs-btn--danger labs-btn--sm" id="kd-detail-export-semua" style="display:none;"><i class="fa fa-file-pdf-o"></i> Export PDF Semua</button>
               </div>
               <div class="labs-text-muted" id="kd-detail-info" style="margin-bottom:10px;font-size:12px;display:none;"></div>

               <!-- loading -->
               <div id="kd-detail-loading">
                  <div class="labs-skeleton labs-skeleton--block" style="height:28px;margin-bottom:8px;"></div>
                  <div class="labs-skeleton labs-skeleton--block" style="height:28px;margin-bottom:8px;"></div>
                  <div class="labs-skeleton labs-skeleton--block" style="height:28px;"></div>
               </div>

               <!-- empty umum: tidak punya tunggakan sama sekali -->
               <div id="kd-detail-empty" class="labs-empty" style="display:none;">
                  <i class="fa fa-smile-o labs-empty__icon"></i>
                  <p>Siswa tidak memiliki tunggakan.</p>
               </div>

               <div id="kd-detail-content" style="display:none;">
                  <div class="labs-modal-section">
                     <div class="labs-modal-section__title"><i class="fa fa-calendar-o"></i> Tunggakan Terakhir <span class="labs-text-muted" id="kd-detail-terakhir-total" style="font-weight:400;font-size:12px;"></span></div>
                     <div id="kd-detail-terakhir-empty" class="labs-empty" style="display:none;">
                        <i class="fa fa-smile-o labs-empty__icon"></i>
                        <p>Tidak ada tunggakan pada rentang filter ini.</p>
                     </div>
                     <div class="labs-table-wrap" id="kd-detail-terakhir-wrap" style="display:none;">
                        <table class="labs-table labs-table-sort" id="kd-detail-table-terakhir">
                           <thead>
                              <tr>
                                 <th data-sort="bulan">Bulan <i class="fa fa-sort sort-icon"></i></th>
                                 <th data-sort="tahun_ajaran">Tahun Ajaran <i class="fa fa-sort sort-icon"></i></th>
                                 <th data-sort="status">Status <i class="fa fa-sort sort-icon"></i></th>
                                 <th data-sort="nominal">Nominal <i class="fa fa-sort sort-icon"></i></th>
                              </tr>
                           </thead>
                           <tbody id="kd-detail-tbody-terakhir"></tbody>
                        </table>
                     </div>
                  </div>
                  <div class="labs-modal-section">
                     <div class="labs-modal-section__title"><i class="fa fa-history"></i> Seluruh Tunggakan <span class="labs-text-muted" id="kd-detail-semua-total" style="font-weight:400;font-size:12px;"></span></div>
                     <div id="kd-detail-semua-empty" class="labs-empty" style="display:none;">
                        <i class="fa fa-smile-o labs-empty__icon"></i>
                        <p>Tidak ada tunggakan lain.</p>
                     </div>
                     <div class="labs-table-wrap" id="kd-detail-semua-wrap" style="display:none;">
                        <table class="labs-table labs-table-sort" id="kd-detail-table-semua">
                           <thead>
                              <tr>
                                 <th data-sort="bulan">Bulan <i class="fa fa-sort sort-icon"></i></th>
                                 <th data-sort="tahun_ajaran">Tahun Ajaran <i class="fa fa-sort sort-icon"></i></th>
                                 <th data-sort="status">Status <i class="fa fa-sort sort-icon"></i></th>
                                 <th data-sort="nominal">Nominal <i class="fa fa-sort sort-icon"></i></th>
                              </tr>
                           </thead>
                           <tbody id="kd-detail-tbody-semua"></tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="labs-btn labs-btn--default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
            </div>
         </div>
      </div>
   </div>

</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
"use strict";

var KD = {
   barChart: null,
   pieChart: null,
   pieInited: false,
   pieData: null,
   trendChart: null,
   trendInited: false,
   breakdownChart: null,
   breakdownInited: false,
   agingInited: false,
   kelasOptions: {},

   colorStatus: { belum: '#B91C1C', menunggu: '#B45309', lunas: '#15803D' },

   rupiah: function (n) {
      return 'Rp ' + parseInt(n, 10).toLocaleString('id-ID');
   },

   statusBadge: function (s) {
      if (s === 0) return '<span class="labs-badge labs-badge--danger">Belum Dibayar</span>';
      if (s === 1) return '<span class="labs-badge labs-badge--warning">Menunggu</span>';
      if (s === 2) return '<span class="labs-badge labs-badge--success">Lunas</span>';
      return '-';
   },

   kategoriBadge: function (k) {
      var map = {
         'awal':   '<span class="labs-badge labs-badge--info">Lebih Awal</span>',
         'tepat':  '<span class="labs-badge labs-badge--success">Tepat Waktu</span>',
         'telat':  '<span class="labs-badge labs-badge--danger">Telat Bayar</span>',
         'dimuka': '<span class="labs-badge labs-badge--orange">Pembayaran Dimuka</span>'
      };
      return map[k] || '<span class="labs-badge labs-badge--default">-</span>';
   },

   vsHtml: function (cur, prev) {
      if (prev === 0 || cur === 0) return '<span class="labs-text-muted">-</span>';
      var pct = ((cur - prev) / prev) * 100;
      var sign = pct >= 0 ? '+' : '';
      var cls = pct >= 0 ? 'up' : 'down';
      var icon = pct >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
      return '<span class="' + cls + '"><i class="fa ' + icon + '"></i> ' + sign + pct.toFixed(1) + '% vs bulan lalu</span>';
   },

   tgl: function (s) {
      if (!s || s === '0000-00-00 00:00:00') return '-';
      return s.substring(8, 10) + '/' + s.substring(5, 7) + '/' + s.substring(0, 4);
   },

   load: function () {
      $('#kd-skeleton').show();
      $('#kd-table-wrap').hide();
      $('#kd-empty').hide();
      $('#kd-table-info').text('');
      KD.showSkel(['kd-skel-cards', 'kd-skel-bar', 'kd-skel-pie']);
      if (KD.trendInited) KD.showSkel(['kd-skel-trend']);
      if (KD.breakdownInited) KD.showSkel(['kd-skel-breakdown']);
      KD.renderChips();

      $.post("<?= site_url('administrator/keuangan_dashboard_spp/data'); ?>", {
         bulan: $('#kd-bulan').val(),
         tahun: $('#kd-tahun').val(),
         jenjang: $('#kd-jenjang').val(),
         tingkatan: $('#kd-tingkatan').val(),
         kelas: $('#kd-kelas').val(),
         status: $('#kd-status').val(),
         page: KD.page || 1,
         sort_by: KD.sort.by,
         sort_dir: KD.sort.dir,
         start_date: $('#kd-start-date').val(),
         end_date: $('#kd-end-date').val()
      }, null, 'json').done(function (res) {
         KD.kelasOptions = res.kelas_options || {};
         KD.tingkatanOptions = res.tingkatan_options || {};
         KD.renderCards(res.summary);
         KD.renderBar(res.bar);
         if (KD.pieInited) KD.renderPie(res.pie);
         KD.pieData = res.pie;
         KD.renderTable(res.detail);
         $('#kd-skeleton').hide();
         KD.hideSkel(['kd-skel-cards', 'kd-skel-bar', 'kd-skel-pie']);
         // refresh section tambahan yang sudah pernah dibuka
         if (KD.trendInited) KD.fetchTrend();
         if (KD.breakdownInited) KD.fetchBreakdown();
         if (KD.agingInited) KD.fetchAging();
      }).fail(function () {
         $('#kd-skeleton').hide();
         KD.hideSkel(['kd-skel-cards', 'kd-skel-bar', 'kd-skel-pie']);
         $('#kd-empty').show();
      });
   },

   // refresh TABEL DETAIL saja (filter tabel/sort/paginasi) — chart & summary tidak disentuh
   loadTable: function () {
      $('#kd-skeleton').show();
      KD.setLoading('jenjang', 1);
      KD.setLoading('tingkatan', 1);
      KD.setLoading('kelas', 1);
      $.post("<?= site_url('administrator/keuangan_dashboard_spp/data'); ?>", {
         bulan: $('#kd-bulan').val(),
         tahun: $('#kd-tahun').val(),
         jenjang: $('#kd-jenjang').val(),
         tingkatan: $('#kd-tingkatan').val(),
         kelas: $('#kd-kelas').val(),
         status: $('#kd-status').val(),
         page: KD.page || 1,
         sort_by: KD.sort.by,
         sort_dir: KD.sort.dir,
         start_date: $('#kd-start-date').val(),
         end_date: $('#kd-end-date').val()
      }, null, 'json').done(function (res) {
         KD.renderTable(res.detail);
         $('#kd-skeleton').hide();
      }).fail(function () {
         $('#kd-skeleton').hide();
         $('#kd-empty').show();
      }).always(function () {
         KD.setLoading('jenjang', -1);
         KD.setLoading('tingkatan', -1);
         KD.setLoading('kelas', -1);
      });
   },

   showSkel: function (ids) {
      for (var i = 0; i < ids.length; i++) { $('#' + ids[i]).addClass('on'); }
   },

   hideSkel: function (ids) {
      for (var i = 0; i < ids.length; i++) { $('#' + ids[i]).removeClass('on'); }
   },

   // Spinner loading di select filter (counter, krn loadTable & kelas_options jalan paralel)
   loadCount: { jenjang: 0, tingkatan: 0, kelas: 0 },
   setLoading: function (sel, delta) {
      this.loadCount[sel] = Math.max(0, (this.loadCount[sel] || 0) + delta);
      $('#kd-' + sel).toggleClass('kd-loading', this.loadCount[sel] > 0);
   },

   statusText: function (s) {
      if (s === '0') return 'Belum Dibayar';
      if (s === '1') return 'Menunggu';
      if (s === '2') return 'Lunas';
      return 'Semua';
   },

   renderChips: function () {
      var bulanNama = $('#kd-bulan option:selected').text();
      var j = $('#kd-jenjang').val();
      var t = $('#kd-tingkatan').val();
      var k = $('#kd-kelas').val();
      var html = '<span class="kd-chip">Filter aktif: <b>' + bulanNama + ' ' + $('#kd-tahun').val() + '</b></span>';
      html += '<span class="kd-chip">Jenjang: <b>' + (j || 'Semua') + '</b></span>';
      html += '<span class="kd-chip">Tingkatan: <b>' + (t || 'Semua') + '</b></span>';
      html += '<span class="kd-chip">Kelas: <b>' + (k || 'Semua') + '</b></span>';
      html += '<span class="kd-chip">Status: <b>' + KD.statusText($('#kd-status').val()) + '</b></span>';
      html += '<button type="button" class="labs-btn labs-btn--default labs-btn--xs" id="kd-reset"><i class="fa fa-times"></i> Reset</button>';
      $('#kd-chips').html(html);
   },

   renderCards: function (sum) {
      var cur = sum.bulan_ini, prev = sum.bulan_lalu;
      var invalid = cur.invalid;
      $('#kd-tagihan').text(invalid ? '-' : KD.rupiah(cur.total_tagihan));
      $('#kd-diterima').text(invalid ? '-' : KD.rupiah(cur.total_diterima));
      $('#kd-belum').text(invalid ? '-' : KD.rupiah(cur.belum_dibayar));
      $('#kd-kolektibilitas').text(invalid ? '-' : cur.kolektibilitas + '%');
      $('#kd-vs-tagihan').html(prev && !prev.invalid ? KD.vsHtml(cur.total_tagihan, prev.total_tagihan) : '');
      $('#kd-vs-diterima').html(prev && !prev.invalid ? KD.vsHtml(cur.total_diterima, prev.total_diterima) : '');
      $('#kd-vs-belum').html(prev && !prev.invalid ? KD.vsHtml(cur.belum_dibayar, prev.belum_dibayar) : '');
      $('#kd-vs-kolektibilitas').html(prev && !prev.invalid ? KD.vsHtml(cur.kolektibilitas, prev.kolektibilitas) : '');
   },

   renderBar: function (bar) {
      var ctx = document.getElementById('kd-chart-bar');
      var data = [bar.awal_nominal, bar.tepat_nominal, bar.telat_nominal, bar.dimuka_nominal];
      if (KD.barChart) { KD.barChart.destroy(); }
      KD.barChart = new Chart(ctx, {
         type: 'bar',
         data: {
            labels: ['Lebih Awal', 'Tepat Waktu', 'Telat Bayar', 'Pembayaran Dimuka'],
            datasets: [{
               label: 'Nominal (Rp)',
               data: data,
               backgroundColor: ['#0E7490', '#15803D', '#B91C1C', '#C2410C']
            }]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
               legend: { display: false },
               tooltip: { callbacks: { label: function (c) { return KD.rupiah(c.parsed.y); } } }
            },
            scales: {
               y: { ticks: { callback: function (v) { return 'Rp ' + (v / 1000000) + ' jt'; } } }
            }
         }
      });
   },

   renderPie: function (pie) {
      var ctx = document.getElementById('kd-chart-pie');
      if (KD.pieChart) { KD.pieChart.destroy(); }
      KD.pieChart = new Chart(ctx, {
         type: 'pie',
         data: {
            labels: ['Belum Dibayar', 'Menunggu Pembayaran', 'Lunas'],
            datasets: [{
               data: [pie.belum, pie.menunggu, pie.lunas],
               backgroundColor: [KD.colorStatus.belum, KD.colorStatus.menunggu, KD.colorStatus.lunas]
            }]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
               legend: { position: 'bottom' },
               tooltip: {
                  callbacks: {
                     label: function (c) {
                        var total = c.dataset.data.reduce(function (a, b) { return a + b; }, 0);
                        var pct = total > 0 ? (c.parsed / total * 100).toFixed(1) : '0';
                        return c.label + ': ' + c.parsed + ' (' + pct + '%)';
                     }
                  }
               }
            }
         }
      });
      KD.pieInited = true;
   },

   renderTable: function (d) {      var total = parseInt(d.total, 10);
      if (total === 0) {
         $('#kd-empty').show();
         $('#kd-table-info').text('');
         $('#kd-page-info').text('');
         $('#kd-pager').empty();
         return;
      }
      $('#kd-table-wrap').show();
      $('#kd-table-info').text(total.toLocaleString('id-ID') + ' transaksi');

      var html = '';
      for (var i = 0; i < d.rows.length; i++) {
         var r = d.rows[i];
         html += '<tr>';
         html += '<td data-label="No Transaksi">' + r.no_transaksi + '</td>';
         html += '<td data-label="Siswa">' + (r.user_name || '-') + '</td>';
         html += '<td data-label="Jenjang">' + (r.jenjang || '-') + '</td>';
         html += '<td data-label="Kelas">' + (r.kelas || '-') + '</td>';
         html += '<td data-label="Bulan">' + r.bulan + '</td>';
         html += '<td data-label="Jumlah Tagihan">' + (r.count_bill ? r.count_bill + ' bulan' : '1 bulan') + (r.detail_bulan && r.count_bill > 1 ? ' (' + r.detail_bulan + ')' : '') + '</td>';
         html += '<td data-label="Nominal">' + KD.rupiah(r.total_biaya) + '</td>';
         html += '<td data-label="Total Dibayar">' + KD.rupiah(r.total_dibayar) + '</td>';
         html += '<td data-label="Status">' + KD.statusBadge(parseInt(r.status_transaksi, 10)) + '</td>';
         html += '<td data-label="Kategori">' + KD.kategoriBadge(r.kategori) + '</td>';
         html += '<td data-label="Tgl Bayar">' + KD.tgl(r.updated_at) + '</td>';
         html += '</tr>';
      }
      $('#kd-tbody').html(html);

      var tp = parseInt(d.total_page, 10) || 1;
      $('#kd-page-info').text('Halaman ' + d.page + ' dari ' + tp);
      var pager = '';
      if (d.page > 1) {
         pager += '<button type="button" class="labs-btn labs-btn--default labs-btn--sm" data-page="' + (d.page - 1) + '"><i class="fa fa-chevron-left"></i></button>';
      }
      if (d.page < tp) {
         pager += '<button type="button" class="labs-btn labs-btn--default labs-btn--sm" data-page="' + (d.page + 1) + '">Selanjutnya <i class="fa fa-chevron-right"></i></button>';
      }
      $('#kd-pager').html(pager);
   },

   fetchTrend: function () {
      $('#kd-skel-trend').addClass('on');
      $.post("<?= site_url('administrator/keuangan_dashboard_spp/trend'); ?>", {
         bulan: $('#kd-bulan').val(),
         tahun: $('#kd-tahun').val(),
         jenjang: $('#kd-jenjang').val(),
         tingkatan: $('#kd-tingkatan').val(),
         kelas: $('#kd-kelas').val(),
         status: $('#kd-status').val()
      }, null, 'json').done(function (res) {
         KD.trendInited = true;
         KD.renderTrend(res);
         $('#kd-skel-trend').removeClass('on');
      }).fail(function () { $('#kd-skel-trend').removeClass('on'); });
   },

   renderTrend: function (res) {
      var ctx = document.getElementById('kd-chart-trend');
      if (KD.trendChart) { KD.trendChart.destroy(); }
      KD.trendChart = new Chart(ctx, {
         type: 'line',
         data: {
            labels: res.labels,
            datasets: [{
               label: 'Penerimaan (Rp)',
               data: res.nominals,
               borderColor: '#C2410C',
               backgroundColor: 'rgba(194, 65, 12, 0.1)',
               fill: true,
               tension: 0.3
            }]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
               legend: { display: false },
               tooltip: { callbacks: { label: function (c) { return KD.rupiah(c.parsed.y); } } }
            },
            scales: {
               y: { ticks: { callback: function (v) { return 'Rp ' + (v / 1000000) + ' jt'; } } }
            }
         }
      });
   },

   fetchBreakdown: function () {
      $('#kd-skel-breakdown').addClass('on');
      $.post("<?= site_url('administrator/keuangan_dashboard_spp/breakdown_jenjang'); ?>", {
         bulan: $('#kd-bulan').val(),
         tahun: $('#kd-tahun').val(),
         tingkatan: $('#kd-tingkatan').val(),
         kelas: $('#kd-kelas').val(),
         status: $('#kd-status').val()
      }, null, 'json').done(function (res) {
         KD.breakdownInited = true;
         KD.renderBreakdown(res);
         $('#kd-skel-breakdown').removeClass('on');
      }).fail(function () { $('#kd-skel-breakdown').removeClass('on'); });
   },

   renderBreakdown: function (res) {
      var ctx = document.getElementById('kd-chart-breakdown');
      if (KD.breakdownChart) { KD.breakdownChart.destroy(); }
      var sel = $('#kd-jenjang').val();
      var labels = ['SD', 'SMP', 'SMA', 'FT'];
      var colTagihan = [], colTerbayar = [];
      for (var i = 0; i < labels.length; i++) {
         colTagihan.push(!sel || labels[i] === sel ? '#B45309' : 'rgba(180, 83, 9, 0.25)');
         colTerbayar.push(!sel || labels[i] === sel ? '#15803D' : 'rgba(21, 128, 61, 0.25)');
      }
      KD.breakdownChart = new Chart(ctx, {
         type: 'bar',
         data: {
            labels: labels,
            datasets: [
               { label: 'Tagihan', data: [res.SD.tagihan, res.SMP.tagihan, res.SMA.tagihan, res.FT.tagihan], backgroundColor: colTagihan },
               { label: 'Terbayar', data: [res.SD.lunas_nominal, res.SMP.lunas_nominal, res.SMA.lunas_nominal, res.FT.lunas_nominal], backgroundColor: colTerbayar }
            ]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
               tooltip: { callbacks: { label: function (c) { return c.dataset.label + ': ' + KD.rupiah(c.parsed.y); } } }
            },
            scales: {
               y: { ticks: { callback: function (v) { return 'Rp ' + (v / 1000000) + ' jt'; } } }
            }
         }
      });
   },

   fetchAging: function () {
      $('#kd-aging-skeleton').show();
      $('#kd-aging-wrap').hide();
      $('#kd-aging-empty').hide();
      $('#kd-aging-info').text('');
      $('#kd-aging-pager').empty();
      $.post("<?= site_url('administrator/keuangan_dashboard_spp/aging'); ?>", {
         jenjang: $('#kd-jenjang').val(),
         tingkatan: $('#kd-tingkatan').val(),
         kelas: $('#kd-kelas').val(),
         status: $('#kd-status').val(),
         id_tahun_ajaran: $('#kd-aging-ta').val(),
         bulan: $('#kd-bulan').val(),
         tahun: $('#kd-tahun').val(),
         q: ($('#kd-aging-search').val() || '').trim(),
         page: KD.agingPage || 1,
         per_page: KD.agingPerPage || 25
      }, null, 'json').done(function (res) {
         KD.agingInited = true;
         $('#kd-aging-skeleton').hide();
         var rows = res.rows || [];
         if (rows.length === 0) {
            $('#kd-aging-empty').show();
            return;
         }
         var html = '';
         for (var i = 0; i < rows.length; i++) {
            var r = rows[i];
            html += '<tr>';
            html += '<td data-label="Nama Siswa"><b>' + (r.nama || '-') + '</b></td>';
            html += '<td data-label="NIS">' + (r.nis || '-') + '</td>';
            html += '<td data-label="Jenjang"><span class="labs-badge labs-badge--default">' + r.jenjang + '</span></td>';
            html += '<td data-label="Kelas">' + (r.kelas || '-') + '</td>';
            html += '<td data-label="Tahun Ajaran">' + (r.tahun_ajaran || '-') + '</td>';
            html += '<td data-label="Jumlah Bulan Menunggak">' + parseInt(r.jml_bulan, 10) + ' bulan</td>';
            html += '<td data-label="Detail Tunggakan"><button type="button" class="labs-btn labs-btn--info labs-btn--xs" data-kd-detail data-jenjang="' + r.jenjang + '" data-nis="' + (r.nis || '') + '">List Tunggakan</button></td>';
            html += '<td data-label="Total Tunggakan">' + KD.rupiah(r.total_tunggakan) + '</td>';
            html += '</tr>';
         }
         $('#kd-aging-tbody').html(html);
         $('#kd-aging-wrap').show();

         var total = parseInt(res.total, 10) || 0;
         var tp = parseInt(res.total_page, 10) || 1;
         $('#kd-aging-total').text(total.toLocaleString('id-ID') + ' penunggak');
         $('#kd-aging-info').text('Halaman ' + res.page + ' dari ' + tp);
         var pager = '';
         if (res.page > 1) {
            pager += '<button type="button" class="labs-btn labs-btn--default labs-btn--sm" data-page="' + (res.page - 1) + '"><i class="fa fa-chevron-left"></i></button>';
         }
         if (res.page < tp) {
            pager += '<button type="button" class="labs-btn labs-btn--default labs-btn--sm" data-page="' + (res.page + 1) + '">Selanjutnya <i class="fa fa-chevron-right"></i></button>';
         }
         $('#kd-aging-pager').html(pager);
      }).fail(function () {
         $('#kd-aging-skeleton').hide();
      });
   },

   exportUrl: function (format) {
      return "<?= site_url('administrator/keuangan_dashboard_spp/export'); ?>?format=" + format +
         '&bulan=' + $('#kd-bulan').val() +
         '&tahun=' + $('#kd-tahun').val() +
         '&jenjang=' + encodeURIComponent($('#kd-jenjang').val() || '') +
         '&tingkatan=' + encodeURIComponent($('#kd-tingkatan').val() || '') +
         '&kelas=' + encodeURIComponent($('#kd-kelas').val() || '') +
         '&status=' + ($('#kd-status').val() || '') +
         '&sort_by=' + (KD.sort.by || '') +
         '&sort_dir=' + (KD.sort.dir || '') +
         '&start_date=' + ($('#kd-start-date').val() || '') +
         '&end_date=' + ($('#kd-end-date').val() || '');
   },

   setSort: function (by) {
      if (KD.sort.by === by) {
         KD.sort.dir = (KD.sort.dir === 'asc') ? 'desc' : 'asc';
      } else {
         KD.sort.by = by;
         KD.sort.dir = 'asc';
      }
      $('#kd-table-wrap thead th').each(function () {
         var $th = $(this);
         var on = $th.data('sort') === by;
         $th.toggleClass('active', on);
         $th.find('.sort-icon')
            .removeClass('fa-sort fa-sort-asc fa-sort-desc')
            .addClass(on ? (KD.sort.dir === 'asc' ? 'fa-sort-asc' : 'fa-sort-desc') : 'fa-sort');
      });
      KD.page = 1;
      KD.loadTable();
   },

   fillTingkatan: function () {
      var j = $('#kd-jenjang').val();
      var $t = $('#kd-tingkatan');
      $t.empty().append('<option value="">Semua Tingkatan</option>');
      if (j && KD.tingkatanOptions[j]) {
         for (var i = 0; i < KD.tingkatanOptions[j].length; i++) {
            $t.append('<option value="' + KD.tingkatanOptions[j][i] + '">' + KD.tingkatanOptions[j][i] + '</option>');
         }
         $t.prop('disabled', false);
      } else {
         $t.prop('disabled', true);
      }
   },

   fillKelasByTingkatan: function () {
      var j = $('#kd-jenjang').val();
      var t = $('#kd-tingkatan').val();
      var $k = $('#kd-kelas');
      $k.empty().append('<option value="">Semua Kelas</option>');
      if (!j) { $k.prop('disabled', true); return; }
      if (!t) { KD.fillKelas(); return; }
      $k.prop('disabled', true).append('<option value="" disabled>Memuat...</option>');
      KD.setLoading('kelas', 1);
      $.post("<?= site_url('administrator/keuangan_dashboard_spp/kelas_options'); ?>", {
         jenjang: j,
         tingkatan: t
      }, null, 'json').done(function (res) {
         $k.empty().append('<option value="">Semua Kelas</option>');
         var rows = res.kelas || [];
         for (var i = 0; i < rows.length; i++) {
            $k.append('<option value="' + rows[i] + '">' + rows[i] + '</option>');
         }
         $k.prop('disabled', false);
      }).always(function () {
         KD.setLoading('kelas', -1);
      });
   },

   fillKelas: function () {
      var j = $('#kd-jenjang').val();
      var $k = $('#kd-kelas');
      $k.empty().append('<option value="">Semua Kelas</option>');
      if (j && KD.kelasOptions[j]) {
         for (var i = 0; i < KD.kelasOptions[j].length; i++) {
            $k.append('<option value="' + KD.kelasOptions[j][i] + '">' + KD.kelasOptions[j][i] + '</option>');
         }
         $k.prop('disabled', false);
      } else {
         $k.prop('disabled', true);
      }
   },

   // Modal Detail Tunggakan — fetch data dari endpoint aging_detail
   openDetail: function (jenjang, nis) {
      KD.detailJenjang = jenjang;
      KD.detailNis = nis;
      KD.detailSort = {
         terakhir: { by: 'tahun_ajaran', dir: 'asc' },
         semua: { by: 'tahun_ajaran', dir: 'asc' }
      };
      $('#kd-detail-nama').text('-');
      $('#kd-detail-info').hide();
      $('#kd-detail-loading').show();
      $('#kd-detail-empty').hide();
      $('#kd-detail-content').hide();
      $('#kd-detail-topbar').hide();
      $('#kd-detail-export-terakhir').hide();
      $('#kd-detail-export-semua').hide();
      $('#kd-modal-detail').modal('show');
      $.post("<?= site_url('administrator/keuangan_dashboard_spp/aging_detail'); ?>", {
         jenjang: jenjang,
         nis: nis,
         id_tahun_ajaran: $('#kd-aging-ta').val(),
         bulan: $('#kd-bulan').val(),
         tahun: $('#kd-tahun').val()
      }, null, 'json').done(function (res) {
         $('#kd-detail-loading').hide();
         var nama = res.siswa && res.siswa.nama ? res.siswa.nama : '-';
         $('#kd-detail-nama').text(nama);
         $('#kd-detail-info').text('NIS: ' + (res.siswa && res.siswa.nis ? res.siswa.nis : '-') +
            ' · Jenjang: ' + (res.siswa && res.siswa.jenjang ? res.siswa.jenjang : '-') +
            ' · Kelas: ' + (res.siswa && res.siswa.kelas ? res.siswa.kelas : '-')).show();
         KD.detailData = {
            terakhir: (res.terakhir && res.terakhir.rows) || [],
            semua: (res.semua && res.semua.rows) || []
         };
         KD.detailTotals = {
            terakhir: (res.terakhir && res.terakhir.total_nominal) || 0,
            semua: (res.semua && res.semua.total_nominal) || 0
         };
         if (KD.detailData.terakhir.length === 0 && KD.detailData.semua.length === 0) {
            $('#kd-detail-empty').show();
            return;
         }
         KD.renderDetailTable('terakhir');
         KD.renderDetailTable('semua');
         $('#kd-detail-content').show();
         $('#kd-detail-topbar').show();
         $('#kd-detail-export-terakhir').toggle(KD.detailData.terakhir.length > 0);
         $('#kd-detail-export-semua').toggle(KD.detailData.semua.length > 0);
      }).fail(function () {
         $('#kd-detail-loading').hide();
         $('#kd-detail-empty').show();
      });
   },

   bulanUrut: { 'Juli': 0, 'Agustus': 1, 'September': 2, 'Oktober': 3, 'November': 4, 'Desember': 5, 'Januari': 6, 'Februari': 7, 'Maret': 8, 'April': 9, 'Mei': 10, 'Juni': 11 },

   detailSortValue: function (r, by) {
      if (by === 'bulan') return KD.bulanUrut[r.bulan] !== undefined ? KD.bulanUrut[r.bulan] : 99;
      if (by === 'status') return parseInt(r.status_transaksi, 10);
      if (by === 'nominal') return parseInt(r.total_biaya, 10);
      return r.tahun_ajaran || '';
   },

   renderDetailTable: function (key) {
      var rows = KD.detailData[key].slice();
      var st = KD.detailSort[key];
      rows.sort(function (a, b) {
         var av = KD.detailSortValue(a, st.by), bv = KD.detailSortValue(b, st.by);
         var cmp;
         if (typeof av === 'number') { cmp = av - bv; }
         else { cmp = av < bv ? -1 : (av > bv ? 1 : 0); }
         return st.dir === 'asc' ? cmp : -cmp;
      });
      var html = '';
      for (var i = 0; i < rows.length; i++) {
         var r = rows[i];
         html += '<tr>';
         html += '<td>' + r.bulan + '</td>';
         html += '<td>' + (r.tahun_ajaran || '-') + '</td>';
         html += '<td>' + KD.statusBadge(parseInt(r.status_transaksi, 10)) + '</td>';
         html += '<td>' + KD.rupiah(r.total_biaya) + '</td>';
         html += '</tr>';
      }
      $('#kd-detail-tbody-' + key).html(html);
      $('#kd-detail-' + key + '-total').text('— total ' + KD.rupiah(KD.detailTotals[key]));
      var isEmpty = rows.length === 0;
      $('#kd-detail-' + key + '-empty').toggle(isEmpty);
      $('#kd-detail-' + key + '-wrap').toggle(!isEmpty);
      // ikon sort header
      $('#kd-detail-table-' + key + ' th[data-sort]').each(function () {
         var $th = $(this);
         var on = $th.data('sort') === st.by;
         $th.toggleClass('active', on);
         $th.find('.sort-icon')
            .removeClass('fa-sort fa-sort-asc fa-sort-desc')
            .addClass(on ? (st.dir === 'asc' ? 'fa-sort-asc' : 'fa-sort-desc') : 'fa-sort');
      });
   }
};

$(function () {
   KD.page = 1;
   KD.sort = { by: '', dir: 'desc' };
   KD.agingPage = 1;
   KD.agingPerPage = 25;
   KD.load();

   // Terapkan = refresh penuh (chart + summary + semua tabel)
   $('#kd-btn-filter').click(function () { KD.page = 1; KD.load(); });
   // filter tabel detail = refresh tabel detail SAJA
   $('#kd-jenjang').change(function () { KD.fillTingkatan(); KD.fillKelas(); KD.page = 1; KD.loadTable(); });
   $('#kd-tingkatan').change(function () { KD.page = 1; KD.fillKelasByTingkatan(); KD.loadTable(); });
   $('#kd-kelas').change(function () { KD.page = 1; KD.loadTable(); });
   $('#kd-status').change(function () { KD.page = 1; KD.loadTable(); });
   $('#kd-start-date').change(function () { KD.page = 1; KD.loadTable(); });
   $('#kd-end-date').change(function () {
      var s = $('#kd-start-date').val();
      var e = $('#kd-end-date').val();
      if (s && e && e < s) {
         alert('Tanggal akhir tidak boleh lebih awal dari tanggal mulai.');
         $('#kd-end-date').val('');
         return;
      }
      KD.page = 1;
      KD.loadTable();
   });

   // filter/paginasi aging = refresh tabel aging SAJA
   $('#kd-aging-ta').change(function () { KD.agingPage = 1; if (KD.agingInited) KD.fetchAging(); });
   $('#kd-aging-perpage').change(function () { KD.agingPage = 1; KD.agingPerPage = parseInt($(this).val(), 10); if (KD.agingInited) KD.fetchAging(); });
   // search aging: tombol Cari / Enter di input — reset halaman, state TA/per_page tetap
   function kdAgingSearch() { KD.agingPage = 1; if (KD.agingInited) KD.fetchAging(); }
   $('#kd-aging-btn-search').click(kdAgingSearch);
   $('#kd-aging-search').on('keydown', function (e) {
      if (e.which === 13) { e.preventDefault(); kdAgingSearch(); }
   });
   $(document).on('click', '#kd-aging-pager button', function () {
      KD.agingPage = parseInt($(this).data('page'), 10);
      KD.fetchAging();
   });

   // Tombol Detail Tunggakan — buka modal (endpoint aging_detail, modal di atas)
   $(document).on('click', '[data-kd-detail]', function () {
      KD.openDetail($(this).data('jenjang'), $(this).data('nis'));
   });

   // Sorting tabel modal detail (client-side)
   $(document).on('click', '#kd-detail-table-terakhir th[data-sort], #kd-detail-table-semua th[data-sort]', function () {
      var $table = $(this).closest('table');
      var key = $table.attr('id') === 'kd-detail-table-terakhir' ? 'terakhir' : 'semua';
      var by = $(this).data('sort');
      var st = KD.detailSort[key];
      if (st.by === by) { st.dir = (st.dir === 'asc') ? 'desc' : 'asc'; }
      else { st.by = by; st.dir = 'asc'; }
      KD.renderDetailTable(key);
   });

   // Export PDF laporan tunggakan (2 scope: rentang aktif / seluruh)
   function kdExportTunggakan(scope) {
      window.location.href = "<?= site_url('administrator/keuangan_dashboard_spp/export_tunggakan'); ?>?scope=" + scope +
         '&jenjang=' + encodeURIComponent(KD.detailJenjang || '') +
         '&nis=' + encodeURIComponent(KD.detailNis || '') +
         '&id_tahun_ajaran=' + encodeURIComponent($('#kd-aging-ta').val() || '') +
         '&bulan=' + encodeURIComponent($('#kd-bulan').val() || '') +
         '&tahun=' + encodeURIComponent($('#kd-tahun').val() || '');
   }
   $('#kd-detail-export-terakhir').click(function () { kdExportTunggakan('terakhir'); });
   $('#kd-detail-export-semua').click(function () { kdExportTunggakan('semua'); });

   // sorting tabel detail
   $(document).on('click', '#kd-table-wrap thead th[data-sort]', function () {
      KD.setSort($(this).data('sort'));
   });

   $(document).on('click', '#kd-pager button', function () {
      KD.page = parseInt($(this).data('page'), 10);
      KD.load();
   });

   // Toggle custom filter lanjutan: SELALU collapsed saat load (tanpa persist localStorage)
   $('#kd-adv-trigger').removeClass('is-expanded');
   $('#kd-advanced').removeClass('is-expanded');
   if (window.localStorage) { try { localStorage.removeItem('labs-ui-toggle-kd-advanced'); } catch (e) {} }
   $('#kd-adv-trigger').off('click').on('click', function (e) {
      e.preventDefault();
      var open = $(this).hasClass('is-expanded');
      $(this).toggleClass('is-expanded', !open);
      $('#kd-advanced').toggleClass('is-expanded', !open);
   });

   // Reset filter lanjutan dari chip
   $(document).on('click', '#kd-reset', function () {
      $('#kd-jenjang').val('');
      $('#kd-tingkatan').val('').prop('disabled', true);
      $('#kd-kelas').val('').prop('disabled', true);
      $('#kd-status').val('');
      KD.page = 1;
      KD.load();
   });

   // Pie/bar chart di-init/resize lazily saat accordion dibuka (content collapsed = canvas 0 height)
   $(document).on('click', '[data-labs-toggle="kd-pie"]', function () {
      setTimeout(function () {
         if (!KD.pieInited && KD.pieData) {
            KD.renderPie(KD.pieData);
         } else if (KD.pieChart) {
            KD.pieChart.resize();
         }
      }, 450);
   });
   $(document).on('click', '[data-labs-toggle="kd-bar"]', function () {
      setTimeout(function () { if (KD.barChart) KD.barChart.resize(); }, 450);
   });

   // Section tambahan: fetch data saat pertama dibuka
   $(document).on('click', '[data-labs-toggle="kd-trend"]', function () {
      if (!KD.trendInited) { KD.fetchTrend(); }
      else { setTimeout(function () { if (KD.trendChart) KD.trendChart.resize(); }, 450); }
   });
   $(document).on('click', '[data-labs-toggle="kd-breakdown"]', function () {
      if (!KD.breakdownInited) { KD.fetchBreakdown(); }
      else { setTimeout(function () { if (KD.breakdownChart) KD.breakdownChart.resize(); }, 450); }
   });
   $(document).on('click', '[data-labs-toggle="kd-aging"]', function () {
      if (!KD.agingInited) { KD.fetchAging(); }
   });

   // Export sesuai filter aktif
   $('#kd-export-xls').click(function () { window.location.href = KD.exportUrl('xls'); });
   $('#kd-export-pdf').click(function () { window.location.href = KD.exportUrl('pdf'); });
   // Export rekapitulasi tunggakan (semester TA dari filter bulan/tahun aktif;
   // end_date filter dipakai sebagai cut-off simulasi jika terisi)
   $('#kd-export-rekap').click(function () {
      window.location.href = "<?= site_url('administrator/keuangan_dashboard_spp/export_rekap'); ?>?bulan=" + $('#kd-bulan').val() + '&tahun=' + $('#kd-tahun').val() +
         '&end_date=' + ($('#kd-end-date').val() || '');
   });
});
</script>
