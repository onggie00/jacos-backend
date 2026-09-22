<style>
/* Top Buttons */
.btn-top { margin-right: 5px; border-radius: 3px; }

/* Table Styling */
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; text-align: center; }
.table td { font-size: 13px; vertical-align: middle; }

/* Status Labels */
.label-menunggu { background: #95a5a6; }
.label-aktivasi { background: #e67e22; }
.label-lunas { background: #27ae60; }

/* Action Buttons */
.btn-aksi { margin: 2px 1px; padding: 4px 0; font-size: 11px; border-radius: 3px; width: 48%; display: inline-block; text-align: center; transition: opacity 0.2s ease; }
.btn-aksi:hover { opacity: 0.85; }
.btn-aksi i { margin-right: 3px; }
.btn-aksi-full { margin: 2px 1px; padding: 4px 0; font-size: 11px; border-radius: 3px; width: 98%; display: block; text-align: center; transition: opacity 0.2s ease; }
.btn-aksi-full:hover { opacity: 0.85; }
.btn-aksi-full i { margin-right: 3px; }

/* Filter Builder */
.filter-builder { transition: all 0.3s ease; }
.filter-builder:hover { box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
.filter-row { animation: fadeInRow 0.3s ease; }
@keyframes fadeInRow {
   from { opacity: 0; transform: translateY(-5px); }
   to   { opacity: 1; transform: translateY(0); }
}
.filter-builder { padding: 8px !important; }
.filter-builder > div:first-child { margin-bottom: 6px !important; }
.filter-builder > div:first-child strong { font-size: 13px !important; }
.filter-builder #filter_empty { padding: 8px !important; font-size: 12px; }
.filter-builder .filter-row { gap: 6px !important; margin-bottom: 4px !important; }
.filter-builder .filter-row .form-control { font-size: 12px !important; padding: 4px 8px !important; height: auto !important; }
.filter-builder .btn-flat { padding: 4px 10px !important; font-size: 12px !important; }

/* Info Boxes */
.info-box-custom { min-height:90px; border-radius:6px; margin-bottom:15px; display:flex; align-items:center; padding:15px 20px; box-shadow:0 2px 4px rgba(0,0,0,0.08); color:#fff; position:relative; overflow:hidden; }
.info-box-custom .info-icon { font-size:40px; opacity:0.35; position:absolute; right:15px; top:50%; transform:translateY(-50%); }
.info-box-custom .info-label { font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.9; margin-bottom:5px; display:block; font-weight:500; }
.info-box-custom .info-value { font-size:24px; font-weight:700; line-height:1.2; display:block; color:#fff; word-break:break-word; }
.info-box-custom .info-detail { font-size:11px; opacity:0.85; margin-top:4px; display:block; color:#fff; }
.bg-grad-blue { background:linear-gradient(135deg,#3c8dbc 0%,#367fa9 100%); }
.bg-grad-green { background:linear-gradient(135deg,#00a65a 0%,#008d4c 100%); }
.bg-grad-purple { background:linear-gradient(135deg,#605ca8 0%,#555299 100%); }
.bg-grad-orange { background:linear-gradient(135deg,#f39c12 0%,#db8b0a 100%); }

/* Chart Toggle */
.chart-toggle-area {
   max-height: 320px;
   overflow: hidden;
   transition: max-height 0.4s ease-in-out, opacity 0.4s ease-in-out, margin-top 0.4s ease-in-out;
   opacity: 1;
}
.chart-toggle-area.chart-hidden {
   max-height: 0;
   opacity: 0;
   margin-top: 0 !important;
}
.chart-canvas-wrap { position:relative; height:280px; width:100%; }
#ketepatan_canvas_area { max-height:none !important; overflow:visible !important; }

/* Ketepatan pie cards */
.ketepatan-pie-card {
   background: #fff;
   border: 1px solid #e6e9ec;
   border-radius: 6px;
   padding: 10px 12px;
   height: 100%;
   box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.ketepatan-pie-title {
   font-size: 13px;
   font-weight: 600;
   color: #2c3e50;
   text-align: center;
   margin-bottom: 6px;
}
.ketepatan-pie-summary {
   font-size: 11px;
   color: #555;
   text-align: center;
   padding: 6px 4px 0;
   border-top: 1px dashed #e6e9ec;
   margin-top: 4px;
   line-height: 1.45;
}
.ketepatan-legend-dot {
   display: inline-block;
   width: 8px;
   height: 8px;
   border-radius: 50%;
   margin: 0 4px 0 8px;
   vertical-align: middle;
}

/* Detail Modal */
.detail-modal .modal-header {
   background: linear-gradient(135deg, #3c8dbc, #367fa9);
   color: #fff;
   border-radius: 5px 5px 0 0;
}
.detail-modal .modal-header .modal-title { font-size: 16px; }
.detail-modal .modal-header .close { color: #fff; opacity: 0.8; }
.detail-modal .detail-header {
   background: linear-gradient(135deg, #f39c12, #db8b0a);
   color: #fff;
   padding: 12px 15px;
   border-radius: 5px;
   margin-bottom: 15px;
   font-size: 13px;
}
.detail-modal .detail-header .label-no { font-size: 14px; font-weight: 700; font-family: 'Courier New', monospace; }
.detail-modal .detail-section {
   background: #f8f9fa;
   border-radius: 5px;
   padding: 12px 15px;
   margin-bottom: 12px;
   border-left: 4px solid #3c8dbc;
}
.detail-modal .detail-section h6 {
   font-size: 12px;
   text-transform: uppercase;
   letter-spacing: 0.5px;
   color: #666;
   margin: 0 0 10px 0;
   font-weight: 600;
}
.detail-modal .detail-row {
   display: flex;
   margin-bottom: 6px;
   font-size: 13px;
}
.detail-modal .detail-label {
   min-width: 140px;
   font-weight: 600;
   color: #555;
}
.detail-modal .detail-value {
   color: #222;
   word-break: break-word;
}
.detail-modal .detail-value.amount {
   font-size: 18px;
   font-weight: 700;
   color: #27ae60;
}
.detail-modal .kwitansi-link {
   display: inline-flex;
   align-items: center;
   gap: 6px;
   background: #27ae60;
   color: #fff;
   padding: 6px 14px;
   border-radius: 4px;
   text-decoration: none;
   font-size: 13px;
   font-weight: 600;
   transition: background 0.2s;
}
.detail-modal .kwitansi-link:hover { background: #1e8e4e; color: #fff; }
.detail-modal .kwitansi-link i { font-size: 14px; }
</style>

<script type="text/javascript">
<?php if ($this->session->flashdata('success')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('success'))); ?>
   toastr.success("<?= $msg; ?>", "Berhasil", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } else if ($this->session->flashdata('error')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('error'))); ?>
   toastr.error("<?= $msg; ?>", "Error", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } else if ($this->session->flashdata('warning')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('warning'))); ?>
   toastr.warning("<?= $msg; ?>", "Peringatan", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } else if ($this->session->flashdata('info')) { ?>
   toastr.info("<?= addslashes($this->session->flashdata('info')); ?>");
<?php } ?>
</script>

<!-- Content Header -->
<section class="content-header">
   <h1>
      <i class="fa fa-credit-card"></i> <?= cclang('transaksi_spp') ?>
      <small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('transaksi_spp') ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">

            <!-- Box Header -->
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-list-alt"></i> Data Transaksi SPP
                  <span class="label bg-yellow" style="margin-left:10px"><?= $transaksi_spp_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('transaksi_spp_add', function () { ?>
                     <a class="btn btn-sm btn-info btn-top" title="Aktivasi VA SPP" data-toggle="modal" data-target="#modal_aktivasi_va">
                        <i class="fa fa-plus-circle"></i> Aktivasi SPP
                     </a>
                     <a class="btn btn-sm btn-success btn-top" title="Generate SPP" data-toggle="modal" data-target="#modal_add_new">
                        <i class="fa fa-plus-square-o"></i> Generate SPP
                     </a>
                  <?php }) ?>
                  <?php is_allowed('transaksi_spp_export', function () { ?>
                     <?php
                        $filter_param = '';
                        if (!empty($_GET['f'])) $filter_param .= (strpos($filter_param, '?') === false ? '?' : '&') . 'f=' . $_GET['f'];
                        if (!empty($_GET['q'])) $filter_param .= (strpos($filter_param, '?') === false ? '?' : '&') . 'q=' . $_GET['q'];
                     ?>
                     <a class="btn btn-sm btn-success btn-top" title="Export XLS" href="<?= site_url('administrator/transaksi_spp/export') . $filter_param; ?>">
                        <i class="fa fa-file-excel-o"></i> XLS
                     </a>
                     <a class="btn btn-sm btn-success btn-top" title="Export PDF" href="<?= site_url('administrator/transaksi_spp/export_pdf'); ?>">
                        <i class="fa fa-file-pdf-o"></i> PDF
                     </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">

               <!-- ===== INFOBOX ===== -->
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-blue">
                        <i class="fa fa-money info-icon"></i>
                        <div>
                           <span class="info-label">Total Tagihan</span>
                           <span class="info-value">Rp <?= number_format($info_total_tagihan, 0, ',', '.'); ?></span>
                           <span class="info-detail"><?= $label_ta_aktif; ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-green">
                        <i class="fa fa-check-circle info-icon"></i>
                        <div>
                           <span class="info-label">Terbayar</span>
                           <span class="info-value">Rp <?= number_format($info_terbayar, 0, ',', '.'); ?></span>
                           <span class="info-detail"><?= $label_ta_aktif; ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-orange">
                        <i class="fa fa-clock-o info-icon"></i>
                        <div>
                           <span class="info-label">Belum Bayar</span>
                           <span class="info-value">Rp <?= number_format($info_belum_bayar, 0, ',', '.'); ?></span>
                           <span class="info-detail"><?= $label_ta_aktif; ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6 col-xs-12">
                     <div class="info-box-custom bg-grad-purple">
                        <i class="fa fa-exclamation-triangle info-icon"></i>
                        <div>
                           <span class="info-label">Tunggakan</span>
                           <span class="info-value">Rp <?= number_format($info_tunggakan, 0, ',', '.'); ?></span>
                           <span class="info-detail">Tahun sebelumnya</span>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- ===== END INFOBOX ===== -->

               <!--
               ===== CHART (bar) DISABLED per request 2026-08-28 - Hidden (commented) per user request =====
               <div class="box box-solid" style="border:1px solid #e3e6ea;border-top:3px solid #3498db;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,0.06);margin-bottom:15px;overflow:hidden">
                  <div class="box-header with-border" style="background:linear-gradient(135deg,#f8f9fa 0%,#eef2f7 100%);padding:12px 15px">
                     <h3 class="box-title" style="font-size:14px;color:#2c3e50;font-weight:600">
                        <i class="fa fa-bar-chart" style="color:#3498db;margin-right:6px"></i> Grafik Tagihan vs Pembayaran
                        <span class="label" style="background:#3498db;margin-left:8px;font-size:10px;padding:3px 8px;border-radius:10px"><?= $label_ta_aktif; ?></span>
                     </h3>
                     <div class="box-tools pull-right">
                        <button type="button" class="btn btn-xs btn-default btn-toggle-chart" id="btn_toggle_chart" title="Sembunyikan/Tampilkan Chart">
                           <i class="fa fa-eye" id="icon_toggle_chart"></i>
                           <span id="text_toggle_chart">Tampilkan Chart</span>
                        </button>
                        <form method="get" action="<?= base_url('administrator/transaksi_spp/index'); ?>" style="display:inline-flex;align-items:center;gap:6px;margin:0">
                           <?php
                              $preserved = array('q', 'f', 'ff', 'fo', 'fv');
                              foreach ($preserved as $pk) {
                                 $pv = $this->input->get($pk);
                                 if (is_array($pv)) {
                                    foreach ($pv as $vv) {
                                       echo '<input type="hidden" name="' . $pk . '[]" value="' . htmlspecialchars($vv) . '">';
                                    }
                                 } elseif ($pv !== null && $pv !== '') {
                                    echo '<input type="hidden" name="' . $pk . '" value="' . htmlspecialchars($pv) . '">';
                                 }
                              }
                           ?>
                           <label style="font-weight:normal;margin:0;font-size:12px;color:#555">TA:</label>
                           <select name="ta" class="form-control input-sm" style="width:auto;min-width:140px" onchange="this.form.submit()">
                              <?php if (!empty($tahun_ajaran_options)): ?>
                                 <?php foreach ($tahun_ajaran_options as $opt_ta): ?>
                                    <option value="<?= $opt_ta->id_tahun_ajaran; ?>" <?= ($opt_ta->id_tahun_ajaran == $selected_ta) ? 'selected' : ''; ?>>
                                       <?= htmlspecialchars($opt_ta->label); ?>
                                    </option>
                                 <?php endforeach; ?>
                              <?php endif; ?>
                           </select>
                        </form>
                     </div>
                  </div>
                  <div class="box-body" style="padding:15px 20px 20px"
                     <div id="dashboard_chart_canvas_area" class="chart-toggle-area chart-hidden">
                        <?php if (!empty($chart_labels)): ?>
                           <div class="chart-canvas-wrap">
                              <canvas id="chart_spp"></canvas>
                           </div>
                        <?php else: ?>
                           <div class="alert alert-warning" style="margin:0;text-align:center">
                              <i class="fa fa-info-circle"></i> Tidak ada data chart untuk tahun ajaran ini.
                           </div>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
               ===== END CHART (bar) ===== -->

               <!-- ===== CHART KETEPATAN PEMBAYARAN ===== -->
               <div class="box box-solid" style="border:1px solid #e3e6ea;border-top:3px solid #27ae60;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,0.06);margin-bottom:15px;overflow:hidden">
                  <div class="box-header with-border" style="background:linear-gradient(135deg,#f8f9fa 0%,#eaf4ee 100%);padding:12px 15px">
                     <h3 class="box-title" style="font-size:14px;color:#2c3e50;font-weight:600">
                        <i class="fa fa-pie-chart" style="color:#27ae60;margin-right:6px"></i> Grafik Ketepatan Waktu Pembayaran SPP
                        <span class="label" id="ketepatan_ta_label" style="background:#27ae60;margin-left:8px;font-size:10px;padding:3px 8px;border-radius:10px"><?= $label_ta_aktif; ?></span>
                     </h3>
                     <div class="box-tools pull-right">
                        <button type="button" class="btn btn-xs btn-default" id="btn_toggle_ketepatan" title="Sembunyikan/Tampilkan Chart">
                           <i class="fa fa-eye" id="icon_toggle_ketepatan"></i>
                           <span id="text_toggle_ketepatan">Sembunyikan Chart</span>
                        </button>
                     </div>
                  </div>
                  <div class="box-body" style="padding:15px 20px 18px">
                     <!-- Filter -->
                     <div id="ketepatan_filter_bar" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-bottom:12px;padding:10px 12px;background:#f8f9fa;border:1px solid #e6e9ec;border-radius:5px">
                        <label style="font-weight:600;font-size:12px;color:#2c3e50;margin:0">Filter:</label>
                        <select id="ketepatan_bulan" class="form-control input-sm" style="width:auto;min-width:140px">
                           <?php $current_month = get_bulan((int) date('n')); ?>
                           <option value="">Semua Bulan</option>
                           <?php foreach (array('Juli','Agustus','September','Oktober','November','Desember','Januari','Februari','Maret','April','Mei','Juni') as $opt_bln): ?>
                              <option value="<?= $opt_bln; ?>" <?= ($opt_bln === $current_month) ? 'selected' : ''; ?>><?= $opt_bln; ?></option>
                           <?php endforeach; ?>
                        </select>
                        <select id="ketepatan_ta" class="form-control input-sm" style="width:auto;min-width:150px">
                           <?php if (!empty($tahun_ajaran_options)): ?>
                              <?php foreach ($tahun_ajaran_options as $opt_ta): ?>
                                 <option value="<?= $opt_ta->id_tahun_ajaran; ?>" <?= ($opt_ta->id_tahun_ajaran == $selected_ta) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($opt_ta->label); ?>
                                 </option>
                              <?php endforeach; ?>
                           <?php endif; ?>
                        </select>
                        <button type="button" class="btn btn-sm btn-success" id="btn_ketepatan_apply">
                           <i class="fa fa-check"></i> Terapkan
                        </button>
                        <small id="ketepatan_loading" style="display:none;color:#888;margin-left:6px"><i class="fa fa-spinner fa-spin"></i> Memuat...</small>
                     </div>

                     <div id="ketepatan_canvas_area" class="chart-toggle-area">
                        <!-- Baris 1: Keseluruhan -->
                        <div class="row" style="margin-bottom:12px">
                           <div class="col-md-12 col-sm-12 col-xs-12">
                              <div class="ketepatan-pie-card" data-jenjang="all">
                                 <div class="ketepatan-pie-title"><i class="fa fa-globe" style="color:#27ae60"></i> Keseluruhan</div>
                                 <div class="chart-canvas-wrap" style="height:260px"><canvas id="chart_ketepatan_all"></canvas></div>
                                 <div class="ketepatan-pie-summary" id="summary_ketepatan_all">-</div>
                              </div>
                           </div>
                        </div>
                        <!-- Baris 2: 4 Jenjang -->
                        <div class="row">
                           <div class="col-md-3 col-sm-6 col-xs-12" style="margin-bottom:12px">
                              <div class="ketepatan-pie-card" data-jenjang="sd">
                                 <div class="ketepatan-pie-title"><i class="fa fa-graduation-cap" style="color:#3c8dbc"></i> SD</div>
                                 <div class="chart-canvas-wrap" style="height:220px"><canvas id="chart_ketepatan_sd"></canvas></div>
                                 <div class="ketepatan-pie-summary" id="summary_ketepatan_sd">-</div>
                              </div>
                           </div>
                           <div class="col-md-3 col-sm-6 col-xs-12" style="margin-bottom:12px">
                              <div class="ketepatan-pie-card" data-jenjang="smp">
                                 <div class="ketepatan-pie-title"><i class="fa fa-graduation-cap" style="color:#8e44ad"></i> SMP</div>
                                 <div class="chart-canvas-wrap" style="height:220px"><canvas id="chart_ketepatan_smp"></canvas></div>
                                 <div class="ketepatan-pie-summary" id="summary_ketepatan_smp">-</div>
                              </div>
                           </div>
                           <div class="col-md-3 col-sm-6 col-xs-12" style="margin-bottom:12px">
                              <div class="ketepatan-pie-card" data-jenjang="sma">
                                 <div class="ketepatan-pie-title"><i class="fa fa-graduation-cap" style="color:#e67e22"></i> SMA</div>
                                 <div class="chart-canvas-wrap" style="height:220px"><canvas id="chart_ketepatan_sma"></canvas></div>
                                 <div class="ketepatan-pie-summary" id="summary_ketepatan_sma">-</div>
                              </div>
                           </div>
                           <div class="col-md-3 col-sm-6 col-xs-12" style="margin-bottom:12px">
                              <div class="ketepatan-pie-card" data-jenjang="ft">
                                 <div class="ketepatan-pie-title"><i class="fa fa-graduation-cap" style="color:#16a085"></i> FT (France Track)</div>
                                 <div class="chart-canvas-wrap" style="height:220px"><canvas id="chart_ketepatan_ft"></canvas></div>
                                 <div class="ketepatan-pie-summary" id="summary_ketepatan_ft">-</div>
                              </div>
                           </div>
                        </div>

                        <!-- Keterangan -->
                        <div style="margin-top:6px;border:1px solid #e6e9ec;border-radius:5px;overflow:hidden">
                           <div id="btn_toggle_keterangan" style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#f8f9fa;cursor:pointer;user-select:none">
                              <span style="font-size:12px;font-weight:600;color:#2c3e50"><i class="fa fa-info-circle" style="color:#3498db;margin-right:5px"></i> Keterangan Kategori Pembayaran</span>
                              <i class="fa fa-chevron-down" id="icon_toggle_keterangan" style="font-size:11px;color:#888;transition:transform 0.3s"></i>
                           </div>
                           <div id="keterangan_body" style="display:none;padding:10px 12px;font-size:11px;color:#555;line-height:1.6">
                              <span class="ketepatan-legend-dot" style="background:#27ae60"></span> <strong>Lebih awal</strong> = pembayaran sebelum tanggal 1 bulan tagihan<br>
                              <span class="ketepatan-legend-dot" style="background:#3498db"></span> <strong>Tepat waktu</strong> = pembayaran antara tanggal 1 s/d akhir bulan tagihan<br>
                              <span class="ketepatan-legend-dot" style="background:#e74c3c"></span> <strong>Telat bayar</strong> = pembayaran setelah akhir bulan tagihan (sudah beda bulan)<br>
                              <span style="color:#888;font-style:italic">Data dihitung dari transaksi lunas (status_transaksi = 2) berdasarkan kolom updated_at.</span>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- ===== END CHART KETEPATAN ===== -->

               <!-- ===== ADVANCED FILTER ===== -->
               <form name="form_transaksi_spp" id="form_transaksi_spp" action="<?= base_url('administrator/transaksi_spp/index'); ?>" method="get">
               <div class="filter-builder" style="margin-bottom:15px;padding:12px;background:#f8f9fa;border-radius:5px;border:1px solid #e0e0e0">
                  <div style="display:flex;align-items:center;margin-bottom:10px">
                     <i class="fa fa-filter" style="color:#3498db;margin-right:8px"></i>
                     <strong style="color:#2c3e50;font-size:14px">Advanced Filter</strong>
                     <button type="button" class="btn btn-xs btn-success" id="btn_add_filter" style="margin-left:auto">
                        <i class="fa fa-plus"></i> Tambah Filter
                     </button>
                  </div>

                  <div id="filter_rows"></div>

                  <div id="filter_empty" style="text-align:center;padding:15px;color:#999">
                     <i class="fa fa-search" style="font-size:20px;margin-bottom:5px"></i><br>
                     <small>Klik "Tambah Filter" untuk menambah filter pencarian</small>
                  </div>

                  <div style="margin-top:10px;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                     <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Terapkan Filter</button>
                     <a class="btn btn-flat btn-default" href="<?= base_url('administrator/transaksi_spp'); ?>"><i class="fa fa-times"></i> Reset Semua</a>
                     <div style="margin-left:auto;display:flex;gap:8px;align-items:center">
                        <input type="text" class="form-control input-sm" name="q" id="filter" placeholder="Cari cepat..." value="<?= htmlspecialchars($this->input->get('q')); ?>" style="width:200px">
                        <select class="form-control input-sm" name="f" style="width:150px">
                           <option value="">Semua Field</option>
                           <option <?= $this->input->get('f') == 'no_transaksi' ? 'selected' : ''; ?> value="no_transaksi">No Transaksi</option>
                           <option <?= $this->input->get('f') == 'nama_bank' ? 'selected' : ''; ?> value="nama_bank">Nama Bank</option>
                           <option <?= $this->input->get('f') == 'va_number' ? 'selected' : ''; ?> value="va_number">VA Number</option>
                           <option <?= $this->input->get('f') == 'user_name' ? 'selected' : ''; ?> value="user_name">Nama Siswa</option>
                           <option <?= $this->input->get('f') == 'total_biaya' ? 'selected' : ''; ?> value="total_biaya">Total Biaya</option>
                           <option <?= $this->input->get('f') == 'status_transaksi' ? 'selected' : ''; ?> value="status_transaksi">Status</option>
                           <option <?= $this->input->get('f') == 'bulan' ? 'selected' : ''; ?> value="bulan">Bulan</option>
                           <option <?= $this->input->get('f') == 'kode_tagihan' ? 'selected' : ''; ?> value="kode_tagihan">Kode Tagihan</option>
                        </select>
                     </div>
                  </div>
               </div>

               <?php if(!empty($multi_filters)): ?>
               <div style="margin-bottom:10px;padding:8px 12px;background:#e8f4fc;border-radius:3px;border:1px solid #bee5eb">
                  <i class="fa fa-info-circle" style="color:#3498db"></i>
                  <small style="color:#2c3e50"><strong>Filter aktif:</strong>
                  <?php foreach($multi_filters as $mf): ?>
                     <span class="label label-primary" style="margin:2px;padding:3px 8px;font-size:11px">
                        <?= htmlspecialchars($mf['field']); ?> <?= htmlspecialchars($mf['operator']); ?> "<?= htmlspecialchars($mf['value']); ?>"
                     </span>
                  <?php endforeach; ?>
                  </small>
               </div>
               <?php endif; ?>

               <!-- ===== DATA TABLE ===== -->
               <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr>
                           <th width="30"><input type="checkbox" class="flat-red" id="check_all" name="check_all"></th>
                           <th>No Transaksi</th>
                           <th>Nama Siswa</th>
                           <th>Bank</th>
                           <th>VA Number</th>
                           <th>Bulan</th>
                           <th>Total Biaya</th>
                           <th>Status</th>
                           <th width="280">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_transaksi_spp">
                        <?php if ($transaksi_spp_counts > 0): ?>
                           <?php foreach ($transaksi_spps as $transaksi_spp): ?>
                              <tr>
                                 <td width="5">
                                    <input type="checkbox" class="flat-red check" name="id[]" value="<?= $transaksi_spp->id_transaksi; ?>">
                                 </td>
                                 <td><span style="font-family:'Courier New',monospace;font-size:12px;font-weight:600;color:#2c3e50;background:#f4f6f7;padding:2px 6px;border-radius:3px;border:1px solid #dce3e6"><?= _ent($transaksi_spp->no_transaksi); ?></span></td>
                                 <td><?= _ent($transaksi_spp->user_name); ?></td>
                                 <td style="text-align:center">
                                    <?php
                                       $bank_color = '#95a5a6';
                                       if (strtoupper($transaksi_spp->nama_bank) == 'BRI') $bank_color = '#003d79';
                                       elseif (strtoupper($transaksi_spp->nama_bank) == 'BNI') $bank_color = '#f37021';
                                    ?>
                                    <span class="label" style="background:<?= $bank_color; ?>"><?= _ent($transaksi_spp->nama_bank); ?></span>
                                 </td>
                                 <td><code style="font-size:11px"><?= _ent($transaksi_spp->va_number ?: '-'); ?></code></td>
                                 <td style="text-align:center"><?= _ent($transaksi_spp->bulan); ?></td>
                                 <td style="text-align:right;font-weight:600">Rp <?= number_format((float) $transaksi_spp->total_biaya, 0, ',', '.'); ?></td>
                                 <td style="text-align:center">
                                    <?php
                                       if ($transaksi_spp->status_transaksi == "0") {
                                          $status_text = "Menunggu Aktivasi";
                                          $status_class = "label-aktivasi";
                                          $status_icon = "fa-clock-o";
                                       } elseif ($transaksi_spp->status_transaksi == "1") {
                                          $status_text = "Menunggu Bayar";
                                          $status_class = "label-menunggu";
                                          $status_icon = "fa-hourglass-half";
                                       } else {
                                          $status_text = "Lunas";
                                          $status_class = "label-lunas";
                                          $status_icon = "fa-check";
                                       }
                                    ?>
                                    <span class="label <?= $status_class; ?>"><i class="fa <?= $status_icon; ?>"></i> <?= $status_text; ?></span>
                                 </td>
                                 <td style="text-align:center">
                                    <div style="margin-bottom:3px">
                                       <?php is_allowed('transaksi_spp_view', function () use ($transaksi_spp) { ?>
                                          <button type="button" class="btn btn-sm btn-info btn-aksi" data-toggle="modal" data-target="#modal_detail_transaksi" data-id="<?= $transaksi_spp->id_transaksi; ?>" title="Lihat detail">
                                             <i class="fa fa-eye"></i> Detail
                                          </button>
                                       <?php }) ?>
                                    </div>
                                    <div>
                                       <?php is_allowed('transaksi_spp_update', function () use ($transaksi_spp) { ?>
                                          <a href="<?= site_url('administrator/transaksi_spp/edit/' . $transaksi_spp->id_transaksi); ?>" class="btn btn-sm btn-warning btn-aksi" title="Edit data">
                                             <i class="fa fa-edit"></i> Edit
                                          </a>
                                       <?php }) ?>
                                       <?php is_allowed('transaksi_spp_delete', function () use ($transaksi_spp) { ?>
                                          <a href="javascript:void(0);" data-href="<?= site_url('administrator/transaksi_spp/delete/' . $transaksi_spp->id_transaksi); ?>" class="btn btn-sm btn-danger btn-aksi remove-data" title="Hapus data">
                                             <i class="fa fa-trash"></i> Hapus
                                          </a>
                                       <?php }) ?>
                                    </div>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr>
                              <td colspan="9" class="text-center" style="padding:30px">
                                 <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                                 <span style="color:#999">
                                    <?php if (!empty($multi_filters) || !empty($this->input->get('q'))): ?>
                                       Data tidak ditemukan untuk filter/pencarian yang dipilih
                                    <?php else: ?>
                                       Data transaksi SPP belum tersedia
                                    <?php endif; ?>
                                 </span>
                              </td>
                           </tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>
               <!-- ===== END DATA TABLE ===== -->

               <!-- Bulk Action & Pagination -->
               <div class="row" style="margin-top:15px">
                  <div class="col-md-6">
                     <div class="input-group" style="max-width:300px">
                        <select class="form-control" name="bulk" id="bulk">
                           <option value="">-- Bulk Action --</option>
                           <option value="delete">Hapus Terpilih</option>
                        </select>
                        <span class="input-group-btn">
                           <button type="button" class="btn btn-flat btn-default" id="apply">Terapkan</button>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-6 text-right">
                     <div class="dataTables_paginate paging_simple_numbers">
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
               </form>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- ============ MODAL GENERATE SPP =============== -->
<div class="modal fade" id="modal_add_new" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#27ae60;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel"><i class="fa fa-plus-square-o"></i> Generate SPP</h3>
         </div>
         <form action="<?= base_url('administrator/transaksi_spp/add_save/'); ?>" method="post" class="form-horizontal" id="form-aktif">
            <div class="modal-body">
               <div class="form-group mb-3">
                  <label class="control-label col-xs-3">Jenjang</label>
                  <div class="col-xs-8">
                     <select class="form-control chosen chosen-select-deselect" name="jenjang" id="jenjang" data-placeholder="Select Jenjang">
                        <option value="sd">SD</option>
                        <option value="smp">SMP</option>
                        <option value="sma">SMA</option>
                        <option value="ft">France Track</option>
                     </select>
                  </div>
               </div>
               <div class="form-group mb-3">
                  <label class="control-label col-xs-3">Siswa</label>
                  <div class="col-xs-8" id="list-sd">
                     <select class="form-control chosen chosen-select-deselect" name="id_siswa_sd_aktif" id="id_siswa_sd_aktif" data-placeholder="Select Siswa">
                        <option value=""></option>
                        <?php foreach (db_get_all_data('siswa_sd_aktif') as $row) : ?>
                           <option value="<?= $row->id_siswa_sd_aktif ?>"><?= $row->nama_lengkap; ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="col-xs-8" id="list-smp">
                     <select class="form-control chosen chosen-select-deselect" name="id_siswa_smp_aktif" id="id_siswa_smp_aktif" data-placeholder="Select Siswa">
                        <option value=""></option>
                        <?php foreach (db_get_all_data('siswa_smp_aktif') as $row) : ?>
                           <option value="<?= $row->id_siswa_smp_aktif ?>"><?= $row->nama_lengkap; ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="col-xs-8" id="list-sma">
                     <select class="form-control chosen chosen-select-deselect" name="id_siswa_sma_aktif" id="id_siswa_sma_aktif" data-placeholder="Select Siswa">
                        <option value=""></option>
                        <?php foreach (db_get_all_data('siswa_sma_aktif') as $row) : ?>
                           <option value="<?= $row->id_siswa_sma_aktif ?>"><?= $row->nama_lengkap; ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="col-xs-8" id="list-ft">
                     <select class="form-control chosen chosen-select-deselect" name="id_siswa_ft_aktif" id="id_siswa_ft_aktif" data-placeholder="Select Siswa">
                        <option value=""></option>
                        <?php foreach (db_get_all_data('siswa_ft_aktif') as $row) : ?>
                           <option value="<?= $row->id_siswa_ft_aktif ?>"><?= $row->nama_lengkap; ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="form-group mb-3">
                  <label class="control-label col-xs-3">Tahun Ajaran</label>
                  <div class="col-xs-8">
                     <select class="form-control chosen chosen-select-deselect" name="tahun_ajaran" id="tahun_ajaran" data-placeholder="Select Tahun Ajaran" required>
                        <option value=""></option>
                        <?php foreach (db_get_all_data('tahun_ajaran') as $row) : ?>
                           <option value="<?= $row->code ?>"><?= $row->label; ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-default btn-flat" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> Tutup</button>
               <button class="btn btn-info btn-flat btn_save"><i class="fa fa-save"></i> Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- ============ MODAL AKTIVASI VA =============== -->
<div class="modal fade" id="modal_aktivasi_va" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#3c8dbc;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel"><i class="fa fa-credit-card"></i> Aktivasi VA</h3>
         </div>
         <form class="form-horizontal" method="post" action="<?= site_url('administrator/transaksi_spp/aktivasi_va'); ?>">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3" for="jenjang">Jenjang</label>
                  <div class="col-xs-8">
                     <select class="form-control" name="jenjang_aktivasi" id="aktivasi_jenjang">
                        <option value="sd">SD</option>
                        <option value="smp">SMP</option>
                        <option value="sma">SMA</option>
                        <option value="ft">FT</option>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3" for="tahun_ajaran">Siswa</label>
                  <div class="col-xs-8">
                     <select class="form-control chosen chosen-select-deselect" name="id_siswa_aktif" id="aktivasi_id_siswa_aktif" data-placeholder="Select Siswa" disabled>
                        <option value="">Pilih Jenjang Dahulu</option>
                     </select>
                  </div>
               </div>
               <div class="form-group" id="section-tagihan" style="display:none;">
                  <label class="control-label col-xs-3">Tagihan SPP</label>
                  <div class="col-xs-12">
                     <div id="loading-tagihan" style="display:none; text-align:center;">
                        <i class="fa fa-spinner fa-spin"></i> Memuat data tagihan...
                     </div>
                     <table class="table table-bordered table-striped" id="tabel-tagihan" style="display:none;">
                        <thead>
                           <tr>
                              <th width="30"><input type="checkbox" id="check_all_tagihan"></th>
                              <th>Bulan</th>
                              <th>Kode Tagihan</th>
                              <th>Total Biaya</th>
                           </tr>
                        </thead>
                        <tbody id="tbody-tagihan"></tbody>
                     </table>
                     <div class="text-right" id="section-total" style="display:none;">
                        <strong>Total Dipilih: Rp <span id="total-biaya-dipilih">0</span></strong>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-default btn-flat" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> Tutup</button>
               <button type="button" class="btn btn-info btn-flat" id="btn-simpan-aktivasi"><i class="fa fa-save"></i> Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- ============ MODAL DETAIL TRANSAKSI =============== -->
<div class="modal fade detail-modal" id="modal_detail_transaksi" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
             </button>
             <h4 class="modal-title" id="detailModalLabel"><i class="fa fa-credit-card"></i> Detail Transaksi SPP</h4>
          </div>
          <div class="modal-body" id="modal_detail_body">
             <div class="text-center" style="padding:40px">
                <i class="fa fa-spinner fa-spin fa-3x" style="color:#3c8dbc"></i>
                <p style="margin-top:10px;color:#999">Memuat data...</p>
             </div>
          </div>
          <div class="modal-footer">
             <button type="button" class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
          </div>
      </div>
   </div>
</div>
<div id="modal_detail_template" style="display:none;">
   <div class="detail-header">
      <span class="label-no">&lt;no_transaksi&gt;</span>
      <span class="pull-right" style="margin-top:2px"><i class="fa fa-calendar-o"></i> Dibuat: <span class="detail-created">-</span></span>
   </div>
   <div class="detail-section" style="border-left-color:#3c8dbc;">
      <h6><i class="fa fa-info-circle"></i> Informasi Transaksi</h6>
      <div class="detail-row"><span class="detail-label">Nama Siswa</span><span class="detail-value detail-user-name">-</span></div>
      <div class="detail-row"><span class="detail-label">Email</span><span class="detail-value detail-user-email">-</span></div>
      <div class="detail-row"><span class="detail-label">Bank</span><span class="detail-value"><span class="detail-bank"></span></span></div>
      <div class="detail-row"><span class="detail-label">VA Number</span><span class="detail-value detail-va">-</span></div>
      <div class="detail-row"><span class="detail-label">Bulan</span><span class="detail-value detail-bulan">-</span></div>
      <div class="detail-row"><span class="detail-label">Kode Tagihan</span><span class="detail-value detail-kode">-</span></div>
      <div class="detail-row"><span class="detail-label">Expired</span><span class="detail-value detail-expired">-</span></div>
      <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value detail-status"></span></div>
      <div class="detail-row"><span class="detail-label">Deskripsi</span><span class="detail-value detail-desc">-</span></div>
   </div>
   <div class="detail-section" style="border-left-color:#27ae60;">
      <h6><i class="fa fa-money"></i> Total Biaya</h6>
      <div class="detail-row"><span class="detail-value amount detail-amount">-</span></div>
   </div>
   <div class="detail-section" style="border-left-color:#8e44ad;">
      <h6><i class="fa fa-file-pdf-o"></i> Bukti Pembayaran</h6>
      <div class="detail-row">
         <span class="detail-label">Kwitansi</span>
         <span class="detail-value detail-kwitansi">-</span>
      </div>
   </div>
   <div class="detail-section" style="border-left-color:#95a5a6;">
      <h6><i class="fa fa-history"></i> Riwayat</h6>
      <div class="detail-row"><span class="detail-label">Terakhir Diubah</span><span class="detail-value detail-updated">-</span></div>
   </div>
</div>

<!-- Page script -->
<script>
// ===== FILTER BUILDER =====
var filterFields = [
   { value: 'no_transaksi', label: 'No Transaksi', type: 'text', operators: ['contains','equals','starts_with','ends_with'] },
   { value: 'nama_bank', label: 'Nama Bank', type: 'text', operators: ['contains','equals'] },
   { value: 'va_number', label: 'VA Number', type: 'text', operators: ['contains','equals'] },
   { value: 'user_name', label: 'Nama Siswa', type: 'text', operators: ['contains','equals','starts_with'] },
   { value: 'total_biaya', label: 'Total Biaya', type: 'number', operators: ['equals','gt','lt'] },
   { value: 'status_transaksi', label: 'Status', type: 'select_status', operators: ['equals'] },
   { value: 'bulan', label: 'Bulan', type: 'text', operators: ['contains','equals'] },
   { value: 'kode_tagihan', label: 'Kode Tagihan', type: 'text', operators: ['contains','equals'] }
];
var operatorLabels = { 'contains':'Mengandung','equals':'Sama dengan','starts_with':'Diawali','ends_with':'Diakhiri','gt':'Lebih dari','lt':'Kurang dari' };
var statusOptions = [
   { id: '0', text: 'Menunggu Aktivasi' },
   { id: '1', text: 'Menunggu Pembayaran' },
   { id: '2', text: 'Pembayaran Berhasil' }
];
var filterIndex = 0;

function getSelectOptions(f){
   if (f === 'status_transaksi') return statusOptions;
   return [];
}
function getFieldType(f){
   for (var i = 0; i < filterFields.length; i++) {
      if (filterFields[i].value === f) return filterFields[i].type;
   }
   return 'text';
}
function getFieldOperators(f){
   for (var i = 0; i < filterFields.length; i++) {
      if (filterFields[i].value === f) return filterFields[i].operators;
   }
   return ['contains'];
}
function buildOperatorSelect(ops, sel){
   var h = '<select class="form-control input-sm filter-operator" style="width:130px">';
   for (var i = 0; i < ops.length; i++) {
      var o = ops[i];
      h += '<option value="' + o + '"' + (o === sel ? ' selected' : '') + '>' + operatorLabels[o] + '</option>';
   }
   h += '</select>';
   return h;
}
function buildValueInput(f, v){
   var t = getFieldType(f);
   if (t === 'text' || t === 'number') {
      var it = (t === 'number') ? 'number' : 'text';
      return '<input type="' + it + '" class="form-control input-sm filter-value" style="width:200px" placeholder="Masukkan nilai..." value="' + (v || '') + '">';
   }
   var opts = getSelectOptions(f);
   var h = '<select class="form-control input-sm filter-value" style="width:200px">';
   h += '<option value="">-- Pilih --</option>';
   for (var i = 0; i < opts.length; i++) {
      h += '<option value="' + opts[i].id + '"' + (opts[i].id === v ? ' selected' : '') + '>' + opts[i].text + '</option>';
   }
   h += '</select>';
   return h;
}
function addFilterRow(fv, op, v){
   fv = fv || ''; op = op || ''; v = v || '';
   var fsh = '<select class="form-control input-sm filter-field" style="width:180px">';
   fsh += '<option value="">-- Pilih Field --</option>';
   for (var i = 0; i < filterFields.length; i++) {
      fsh += '<option value="' + filterFields[i].value + '"' + (filterFields[i].value === fv ? ' selected' : '') + '>' + filterFields[i].label + '</option>';
   }
   fsh += '</select>';
   var ops = fv ? getFieldOperators(fv) : ['contains'];
   if (!op || ops.indexOf(op) === -1) op = ops[0];
   var h = '<div class="filter-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px">';
   h += '<input type="hidden" name="ff[]" class="filter-field-hidden" value="' + fv + '">';
   h += '<input type="hidden" name="fo[]" class="filter-operator-hidden" value="' + op + '">';
   h += '<input type="hidden" name="fv[]" class="filter-value-hidden" value="' + v + '">';
   h += '<span style="color:#3498db;font-weight:bold;font-size:12px;min-width:20px">#' + (filterIndex + 1) + '</span>';
   h += fsh;
   h += '<span class="filter-operator-container">' + buildOperatorSelect(ops, op) + '</span>';
   h += '<span class="filter-value-container">' + buildValueInput(fv, v) + '</span>';
   h += '<button type="button" class="btn btn-xs btn-danger btn-remove-filter" title="Hapus"><i class="fa fa-times"></i></button>';
   h += '</div>';
   $('#filter_rows').append(h);
   filterIndex++;
   updateFilterEmpty();
}
function updateFilterEmpty(){
   if ($('#filter_rows .filter-row').length > 0) {
      $('#filter_empty').hide();
   } else {
      $('#filter_empty').show();
   }
}
function updateFilterNumbers(){
   $('#filter_rows .filter-row').each(function(idx){
      $(this).find('span:first').text('#' + (idx + 1));
   });
}

$(document).ready(function(){
   $('#btn_add_filter').click(function(){ addFilterRow(); });
   $(document).on('click', '.btn-remove-filter', function(){
      $(this).closest('.filter-row').remove();
      updateFilterEmpty();
      updateFilterNumbers();
   });
   $(document).on('change', '.filter-field', function(){
      var r = $(this).closest('.filter-row');
      var f = $(this).val();
      r.find('.filter-field-hidden').val(f);
      var ops = f ? getFieldOperators(f) : ['contains'];
      r.find('.filter-operator-container').html(buildOperatorSelect(ops, ops[0]));
      r.find('.filter-operator-hidden').val(ops[0]);
      r.find('.filter-value-container').html(buildValueInput(f, ''));
      r.find('.filter-value-hidden').val('');
   });
   $(document).on('change', '.filter-operator', function(){
      $(this).closest('.filter-row').find('.filter-operator-hidden').val($(this).val());
   });
   $(document).on('change keyup', '.filter-value', function(){
      $(this).closest('.filter-row').find('.filter-value-hidden').val($(this).val());
   });
   <?php if(!empty($multi_filters)): ?>
      <?php foreach($multi_filters as $i => $mf): ?>
         addFilterRow('<?= addslashes($mf['field']); ?>', '<?= addslashes($mf['operator']); ?>', '<?= addslashes($mf['value']); ?>');
      <?php endforeach; ?>
   <?php endif; ?>
   updateFilterEmpty();
});
// ===== END FILTER BUILDER =====

$(document).ready(function(){

   // Jenjang switch for Generate modal
   $("#list-sd").show(); $("#list-smp").hide(); $("#list-sma").hide(); $("#list-ft").hide();
   $("#jenjang").change(function() {
      var jenjang = $(this).val();
      $("#list-sd, #list-smp, #list-sma, #list-ft").hide();
      $("#list-" + jenjang).show();
   });

   // Remove data
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({
         title: "<?= cclang('are_you_sure'); ?>",
         text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
         cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
         closeOnConfirm: true,
         closeOnCancel: true
      },
      function(isConfirm){
         if (isConfirm) { document.location.href = url; }
      });
      return false;
   });

   // Bulk action
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_transaksi_spp').serialize();
      if (bulk.val() == 'delete') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
            cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
            closeOnConfirm: true,
            closeOnCancel: true
         },
         function(isConfirm){
            if (isConfirm) { document.location.href = BASE_URL + '/administrator/transaksi_spp/delete?' + serialize_bulk; }
         });
         return false;
      } else if (bulk.val() == '') {
         swal({ title: "Upss", text: "<?= cclang('please_choose_bulk_action_first'); ?>", type: "warning", confirmButtonColor: "#DD6B55", confirmButtonText: "Okay!" });
         return false;
      }
      return false;
   });

   // Check all
   var checkAll = $('#check_all');
   var checkboxes = $('input.check');
   checkAll.on('ifChecked ifUnchecked', function(event) {
      if (event.type == 'ifChecked') { checkboxes.iCheck('check'); } else { checkboxes.iCheck('uncheck'); }
   });
   checkboxes.on('ifChanged', function(event){
      if (checkboxes.filter(':checked').length == checkboxes.length) { checkAll.prop('checked', 'checked'); } else { checkAll.removeProp('checked'); }
      checkAll.iCheck('update');
   });

   // Aktivasi modal
   function resetTagihan() {
      $('#section-tagihan').hide(); $('#tabel-tagihan').hide();
      $('#tbody-tagihan').empty(); $('#section-total').hide();
      $('#total-biaya-dipilih').text('0');
   }

   $('#aktivasi_jenjang').change(function() {
      var jenjang = $(this).val();
      var $select = $('#aktivasi_id_siswa_aktif');
      $select.empty().append("<option value=''>Pilih Jenjang Dahulu</option>").prop('disabled', true);
      resetTagihan();
      $.ajax({
         url: BASE_URL + '/administrator/transaksi_spp/get_siswa',
         type: 'POST', data: { jenjang: jenjang }, dataType: 'html',
         success: function(response) {
            $select.empty().html(response).prop('disabled', false);
            $select.trigger('chosen:updated');
         },
         error: function() { alert('Gagal mengambil data siswa.'); }
      });
   });

   $('#aktivasi_id_siswa_aktif').change(function() {
      var id_siswa = $(this).val();
      var jenjang  = $('#aktivasi_jenjang').val();
      resetTagihan();
      if (!id_siswa) return;
      $('#section-tagihan').show(); $('#loading-tagihan').show();
      $.ajax({
         url: BASE_URL + '/administrator/transaksi_spp/cek_tagihan',
         type: 'POST', data: { id_siswa: id_siswa, jenjang: jenjang }, dataType: 'JSON',
         success: function(response) {
            $('#loading-tagihan').hide();
            if (!response || response.length === 0) {
               $('#tbody-tagihan').html('<tr><td colspan="4" class="text-center">Tidak ada tagihan tersedia</td></tr>');
               $('#tabel-tagihan').show(); return;
            }
            var rows = '';
            $.each(response, function(i, item) {
               rows += '<tr>';
               rows += '<td><input type="checkbox" class="check-tagihan" data-id="' + item.id_transaksi + '" data-biaya="' + item.total_biaya + '" data-jenjang="' + jenjang + '"></td>';
               rows += '<td>' + item.bulan + ' ' + item.tahun_ajaran + '</td>';
               rows += '<td>' + item.kode_tagihan + '</td>';
               rows += '<td>Rp ' + formatRupiah(item.total_biaya) + '</td>';
               rows += '</tr>';
            });
            $('#tbody-tagihan').html(rows);
            $('#tabel-tagihan').show(); $('#section-total').show();
         },
         error: function() { $('#loading-tagihan').hide(); alert('Gagal mengambil data tagihan.'); }
      });
   });

   $(document).on('change', '#check_all_tagihan', function() {
      $('.check-tagihan').prop('checked', $(this).is(':checked')); hitungTotal();
   });
   $(document).on('change', '.check-tagihan', function() {
      var total = $('.check-tagihan').length;
      var checked = $('.check-tagihan:checked').length;
      $('#check_all_tagihan').prop('checked', total === checked);
      hitungTotal();
   });

   function hitungTotal() {
      var total = 0;
      $('.check-tagihan:checked').each(function() { total += parseInt($(this).data('biaya')) || 0; });
      $('#total-biaya-dipilih').text(formatRupiah(total));
   }
   function formatRupiah(angka) { return parseInt(angka).toLocaleString('id-ID'); }

   $('#btn-simpan-aktivasi').click(function() {
      var jenjang = $('#aktivasi_jenjang').val();
      var id_siswa = $('#aktivasi_id_siswa_aktif').val();
      var checked = $('.check-tagihan:checked');
      if (!id_siswa) { alert('Pilih siswa terlebih dahulu.'); return; }
      if (checked.length === 0) { alert('Pilih minimal satu tagihan.'); return; }
      var tagihan = [];
      checked.each(function() {
         tagihan.push({ id_transaksi: $(this).data('id'), total_biaya: $(this).data('biaya'), jenjang: $(this).data('aktivasi_jenjang') });
      });
      $.ajax({
         url: BASE_URL + 'administrator/transaksi_spp/aktivasi_va',
         type: 'POST', data: { jenjang: jenjang, id_siswa_aktif: id_siswa, tagihan: tagihan }, dataType: 'JSON',
         success: function(res) {
            if (res.status === '1') {
               toastr.success(res.message + ' VA : ' + res.data);
               $('#modal_aktivasi_va').modal('hide');
               setTimeout(function(){ location.reload(); }, 1500);
            } else { toastr.error(res.message); }
         },
         error: function() { alert('Gagal menyimpan aktivasi.'); }
      });
   });

});
</script>

<!-- ===== CHART (Chart.js) ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
(function(){
   if (typeof Chart === 'undefined') {
      document.write('<script src="<?= BASE_ASSET; ?>/admin-lte/plugins/chartjs/Chart.min.js"><\/script>');
   }
})();
</script>
<?php if (!empty($chart_labels)): ?>
<script>
(function(){
   var canvas = document.getElementById('chart_spp');
   if (!canvas || typeof Chart === 'undefined') { return; }

   var fmtRp = function(n) {
      var s = Math.round(n).toString();
      return 'Rp ' + s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
   };

   var data = {
      labels: <?= json_encode($chart_labels); ?>,
      datasets: [
         {
            label: 'Total Tagihan',
            data: <?= json_encode($chart_tagihan); ?>,
            backgroundColor: 'rgba(52,152,219,0.85)',
            borderColor: '#2980b9',
            borderWidth: 1,
            hoverBackgroundColor: 'rgba(41,128,185,0.95)'
         },
         {
            label: 'Terbayar',
            data: <?= json_encode($chart_bayar); ?>,
            backgroundColor: 'rgba(46,204,113,0.85)',
            borderColor: '#27ae60',
            borderWidth: 1,
            hoverBackgroundColor: 'rgba(39,174,96,0.95)'
         }
      ]
   };

   new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: data,
      options: {
         responsive: true,
         maintainAspectRatio: false,
         legend: {
            display: true,
            position: 'top',
            labels: {
               fontSize: 12,
               fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
               fontColor: '#555',
               usePointStyle: true,
               padding: 16
            }
         },
         tooltips: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            bodyFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            titleFontSize: 13,
            bodyFontSize: 12,
            cornerRadius: 4,
            xPadding: 10,
            yPadding: 8,
            callbacks: {
               title: function(items) {
                  return items[0].xLabel;
               },
               label: function(ti, data) {
                  return ' ' + data.datasets[ti.datasetIndex].label + ': ' + fmtRp(ti.yLabel);
               }
            }
         },
         scales: {
            yAxes: [{
               gridLines: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
               ticks: {
                  beginAtZero: true,
                  fontSize: 11,
                  fontColor: '#777',
                  callback: function(v) { return 'Rp ' + Math.round(v).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
               }
            }],
            xAxes: [{
               gridLines: { display: false },
               ticks: { fontSize: 11, fontColor: '#555' },
               barPercentage: 0.7,
               categoryPercentage: 0.8
            }]
         }
      }
   });
})();
</script>
<?php endif; ?>

<!-- ===== CHART TOGGLE ===== -->
<script>
(function(){
   var KEY = 'transaksi_spp_chart_visible';
   var area = document.getElementById('dashboard_chart_canvas_area');
   var btn  = document.getElementById('btn_toggle_chart');
   var ic   = document.getElementById('icon_toggle_chart');
   var tx   = document.getElementById('text_toggle_chart');
   if (!area || !btn || !ic || !tx) { return; }

   function hasClass(el, name) { return (' ' + el.className + ' ').indexOf(' ' + name + ' ') !== -1; }
   function addClass(el, name) { if (!hasClass(el, name)) { el.className = el.className + ' ' + name; } }
   function removeClass(el, name) { el.className = (' ' + el.className + ' ').replace(' ' + name + ' ', ' ').replace(/^\s+|\s+$/g, ''); }

   function apply(state){
      if (state === '0') {
         addClass(area, 'chart-hidden');
         ic.className = 'fa fa-eye';
         tx.textContent = 'Tampilkan Chart';
      } else {
         removeClass(area, 'chart-hidden');
         ic.className = 'fa fa-eye-slash';
         tx.textContent = 'Sembunyikan Chart';
      }
   }

   var stored = null;
   try { stored = window.localStorage.getItem(KEY); } catch(e) {}
   // Default: hidden (state '0')
   apply(stored === '1' ? '1' : '0');

   btn.addEventListener('click', function(){
      var newState = hasClass(area, 'chart-hidden') ? '1' : '0';
      apply(newState);
      try { window.localStorage.setItem(KEY, newState); } catch(e) {}
      if (newState === '1' && typeof jQuery !== 'undefined') {
         setTimeout(function() { jQuery(window).trigger('resize'); }, 450);
      }
   });
})();
</script>

<!-- ===== CHART KETEPATAN PEMBAYARAN (Chart.js pie) ===== -->
<script>
(function(){
   if (typeof Chart === 'undefined') {
      // CDN script tag at bottom of page already attempted; bailing out
   }

   var COLORS = {
      lebih_awal: '#27ae60',
      tepat_waktu: '#3498db',
      telat_bayar: '#e74c3c'
   };
   var LABELS = {
      lebih_awal: 'Lebih awal',
      tepat_waktu: 'Tepat waktu',
      telat_bayar: 'Telat bayar'
   };
   var KEY = ['lebih_awal','tepat_waktu','telat_bayar'];
   var chartInstances = {};

   function buildData(stats){
      var labels = []; var data = []; var bg = [];
      KEY.forEach(function(k){
         labels.push(LABELS[k]);
         data.push(parseInt(stats[k] || 0, 10));
         bg.push(COLORS[k]);
      });
      return { labels: labels, datasets: [{ data: data, backgroundColor: bg, borderWidth: 1, borderColor: '#fff' }] };
   }

   function fmtPct(n, total){
      if (!total) return '0%';
      return Math.round((n / total) * 100) + '%';
   }

   function renderPie(canvasId, summaryId, stats){
      var canvas = document.getElementById(canvasId);
      if (!canvas || typeof Chart === 'undefined') return;
      var total = (parseInt(stats.lebih_awal||0,10) + parseInt(stats.tepat_waktu||0,10) + parseInt(stats.telat_bayar||0,10));
      var cfg = {
         type: 'pie',
         data: buildData(stats),
         options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { position: 'bottom', labels: { fontSize: 11, fontColor: '#444', boxWidth: 10, padding: 8 } },
            tooltips: {
               backgroundColor: 'rgba(0,0,0,0.8)',
               callbacks: {
                  label: function(ti, d){
                     var v = parseInt(d.datasets[0].data[ti.index], 10) || 0;
                     return ' ' + d.labels[ti.index] + ': ' + v + ' (' + fmtPct(v, total) + ')';
                  }
               }
            }
         }
      };
      if (chartInstances[canvasId]) {
         chartInstances[canvasId].data = cfg.data;
         chartInstances[canvasId].update();
      } else {
         chartInstances[canvasId] = new Chart(canvas.getContext('2d'), cfg);
      }

      var s = document.getElementById(summaryId);
      if (s) {
         s.innerHTML =
            '<strong>' + total + '</strong> transaksi &nbsp;|&nbsp; ' +
            '<span style="color:' + COLORS.lebih_awal + '">' + stats.lebih_awal + ' (' + fmtPct(stats.lebih_awal, total) + ')</span> &nbsp; ' +
            '<span style="color:' + COLORS.tepat_waktu + '">' + stats.tepat_waktu + ' (' + fmtPct(stats.tepat_waktu, total) + ')</span> &nbsp; ' +
            '<span style="color:' + COLORS.telat_bayar + '">' + stats.telat_bayar + ' (' + fmtPct(stats.telat_bayar, total) + ')</span>';
      }
   }

   function loadAll(){
      var bulan = $('#ketepatan_bulan').val() || '';
      var id_ta = $('#ketepatan_ta').val() || '';
      var taText = $('#ketepatan_ta option:selected').text() || '';
      $('#ketepatan_loading').show();
      $.ajax({
         url: BASE_URL + 'administrator/transaksi_spp/ajax_ketepatan_pembayaran',
         type: 'POST',
         dataType: 'JSON',
         data: { bulan: bulan, id_ta: id_ta },
         success: function(res){
            $('#ketepatan_loading').hide();
            if (!res || res.success === false) {
               var msg = (res && res.message) ? res.message : 'Gagal memuat data';
               ['all','sd','smp','sma','ft'].forEach(function(j){
                  var canvas = document.getElementById('chart_ketepatan_' + j);
                  if (canvas) {
                     var ctx = canvas.getContext('2d');
                     ctx.clearRect(0, 0, canvas.width, canvas.height);
                     ctx.font = '13px sans-serif';
                     ctx.fillStyle = '#999';
                     ctx.textAlign = 'center';
                     ctx.fillText(msg, canvas.width / 2, canvas.height / 2);
                  }
                  var sm = document.getElementById('summary_ketepatan_' + j);
                  if (sm) sm.innerHTML = '-';
               });
               return;
            }
            var d = res.data || {};
            renderPie('chart_ketepatan_all', 'summary_ketepatan_all', d.all || {lebih_awal:0,tepat_waktu:0,telat_bayar:0});
            renderPie('chart_ketepatan_sd',   'summary_ketepatan_sd',   d.sd   || {lebih_awal:0,tepat_waktu:0,telat_bayar:0});
            renderPie('chart_ketepatan_smp',  'summary_ketepatan_smp',  d.smp  || {lebih_awal:0,tepat_waktu:0,telat_bayar:0});
            renderPie('chart_ketepatan_sma',  'summary_ketepatan_sma',  d.sma  || {lebih_awal:0,tepat_waktu:0,telat_bayar:0});
            renderPie('chart_ketepatan_ft',   'summary_ketepatan_ft',   d.ft   || {lebih_awal:0,tepat_waktu:0,telat_bayar:0});
            $('#ketepatan_ta_label').text(taText || '-');
         },
         error: function(){
            $('#ketepatan_loading').hide();
         }
      });
   }

   // Bind button
   $(document).ready(function(){
      $('#btn_ketepatan_apply').click(function(){ loadAll(); });
      $('#ketepatan_bulan, #ketepatan_ta').change(function(){
         // no auto-load; user clicks Terapkan
      });
      // Initial load (default: Semua Bulan + TA aktif)
      loadAll();

      // Keterangan show/hide toggle
      var btnKet = document.getElementById('btn_toggle_keterangan');
      var bodyKet = document.getElementById('keterangan_body');
      var iconKet = document.getElementById('icon_toggle_keterangan');
      if (btnKet && bodyKet) {
         var KEY_KET = 'transaksi_spp_keterangan_visible';
         var storedKet = null;
         try { storedKet = window.localStorage.getItem(KEY_KET); } catch(e) {}
         if (storedKet === '1') {
            bodyKet.style.display = 'block';
            if (iconKet) iconKet.style.transform = 'rotate(180deg)';
         }
         btnKet.addEventListener('click', function(){
            var isOpen = bodyKet.style.display !== 'none';
            bodyKet.style.display = isOpen ? 'none' : 'block';
            if (iconKet) iconKet.style.transform = isOpen ? '' : 'rotate(180deg)';
            try { window.localStorage.setItem(KEY_KET, isOpen ? '0' : '1'); } catch(e) {}
         });
      }

      // Show/hide toggle
      var KEY2 = 'transaksi_spp_ketepatan_visible';
      var area = document.getElementById('ketepatan_canvas_area');
      var btn  = document.getElementById('btn_toggle_ketepatan');
      var ic   = document.getElementById('icon_toggle_ketepatan');
      var tx   = document.getElementById('text_toggle_ketepatan');
      function hasClass2(el, name){ return (' ' + el.className + ' ').indexOf(' ' + name + ' ') !== -1; }
      function addClass2(el, name){ if (!hasClass2(el, name)) el.className = el.className + ' ' + name; }
      function removeClass2(el, name){ el.className = (' ' + el.className + ' ').replace(' ' + name + ' ', ' ').replace(/^\s+|\s+$/g, ''); }
      function apply2(state){
         if (state === '0') {
            addClass2(area, 'chart-hidden');
            ic.className = 'fa fa-eye';
            tx.textContent = 'Tampilkan Chart';
         } else {
            removeClass2(area, 'chart-hidden');
            ic.className = 'fa fa-eye-slash';
            tx.textContent = 'Sembunyikan Chart';
         }
      }
      var stored = null;
      try { stored = window.localStorage.getItem(KEY2); } catch(e) {}
      apply2(stored === '0' ? '0' : '1'); // default: visible
      btn.addEventListener('click', function(){
         var newState = hasClass2(area, 'chart-hidden') ? '1' : '0';
         apply2(newState);
         try { window.localStorage.setItem(KEY2, newState); } catch(e) {}
         if (newState === '1' && typeof jQuery !== 'undefined') {
            setTimeout(function(){
               jQuery(window).trigger('resize');
               Object.keys(chartInstances).forEach(function(cid){
                  if (chartInstances[cid]) chartInstances[cid].resize();
               });
            }, 450);
         }
      });
   });
})();
</script>

<!-- ===== MODAL DETAIL TRANSAKSI ===== -->
<script>
$(document).ready(function(){
   $('#modal_detail_transaksi').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var id = button.data('id');
      if (!id) return;

      // Load template
      var tpl = $('#modal_detail_template').html();
      $('#modal_detail_body').html(tpl);

      $.ajax({
         url: BASE_URL + 'administrator/transaksi_spp/get_detail',
         type: 'POST',
         dataType: 'json',
         data: { id: id },
         success: function(res) {
            if (!res || !res.success) {
               $('#modal_detail_body').html('<div class="alert alert-danger">' + (res ? res.message : 'Gagal memuat data') + '</div>');
               return;
            }
            var d = res.data;

            $('#modal_detail_body').find('.detail-header .label-no').text(d.no_transaksi);
            $('#modal_detail_body').find('.detail-created').text(d.created_at);
            $('#modal_detail_body').find('.detail-user-name').text(d.user_name);
            $('#modal_detail_body').find('.detail-user-email').text(d.user_email);
            $('#modal_detail_body').find('.detail-bank').html('<span class="label" style="background:' + d.bank_color + '">' + d.nama_bank + '</span>');
            $('#modal_detail_body').find('.detail-va').text(d.va_number);
            $('#modal_detail_body').find('.detail-bulan').text(d.bulan);
            $('#modal_detail_body').find('.detail-kode').text(d.kode_tagihan);
            $('#modal_detail_body').find('.detail-expired').text(d.expired_datetime);
            $('#modal_detail_body').find('.detail-amount').text('Rp ' + parseInt(d.total_biaya).toLocaleString('id-ID'));
            $('#modal_detail_body').find('.detail-desc').text(d.description || '-');
            $('#modal_detail_body').find('.detail-updated').text(d.updated_at);
            $('#modal_detail_body').find('.detail-status').html('<span class="label ' + d.status_class + '"><i class="fa ' + d.status_icon + '"></i> ' + d.status_text + '</span>');

            // Kwitansi
            var kwitansiHtml = '-';
            if (d.file_kwitansi) {
               kwitansiHtml = '<a href="' + BASE_URL + 'uploads/kwitansi/' + d.file_kwitansi + '" target="_blank" class="kwitansi-link"><i class="fa fa-file-pdf-o"></i> Buka Kwitansi</a>';
            }
            $('#modal_detail_body').find('.detail-kwitansi').html(kwitansiHtml);
         },
         error: function() {
            $('#modal_detail_body').html('<div class="alert alert-danger">Gagal memuat data transaksi.</div>');
         }
      });
   });
});
</script>
