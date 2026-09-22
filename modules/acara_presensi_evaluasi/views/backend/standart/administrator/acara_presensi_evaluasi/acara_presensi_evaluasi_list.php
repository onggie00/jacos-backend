<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<style>
/* Group header style */
.group-header {
  cursor: pointer;
  font-weight: 700;
  background-color: var(--labs-bg-soft);
  color: var(--labs-text);
}
.group-header:hover {
  background-color: var(--labs-border);
}
.group-toggle i { transition: transform 0.1s ease; }
</style>

<script type="text/javascript">
$(document).ready(function(){
   // Tombol "Lihat Q&A"
   $('.qA-btn').on('click', function(e) {
      e.preventDefault();
      var npp = $(this).data('npp');
      var nama = $(this).data('nama');
      var peserta = $(this).data('peserta');
      lihatQa(npp, nama, peserta);
   });

   // Tombol "Grafik"
   $('.grafik-btn').on('click', function(e) {
      e.preventDefault();
      var npp = $(this).data('npp');
      var nama = $(this).data('nama');
      var peserta = $(this).data('peserta');
      lihatGrafik(npp, nama, peserta);
   });
});
</script>

<section class="content-header">
   <h1>
      <i class="fa fa-bar-chart"></i> Hasil Evaluasi Acara <small class="labs-text-muted"><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/acara'); ?>">Acara</a></li>
      <li class="active">Hasil Evaluasi</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">

            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-bar-chart"></i> Data Hasil Evaluasi Acara
                  <span class="labs-badge labs-badge--orange"><?= $acara_presensi_evaluasi_counts; ?> Data</span>
               </h3>
               <div>
                  <?php is_allowed('acara_presensi_evaluasi_add', function(){?>
                  <a class="labs-btn labs-btn--success" title="Tambah Data (Ctrl+a)" href="<?= site_url('administrator/acara_presensi_evaluasi/add'); ?>">
                     <i class="fa fa-plus"></i> Tambah Data
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="labs-card__body">

               <form name="form_acara_presensi_evaluasi" id="form_acara_presensi_evaluasi" action="<?= base_url('administrator/acara_presensi_evaluasi/index'); ?>">
               <div class="labs-filter-inline">
                  <div class="labs-filter-inline__search">
                     <i class="fa fa-search labs-filter-inline__search-icon"></i>
                     <input type="text" class="form-control" name="q" id="filter" placeholder="Cari NPP..." value="<?= $this->input->get('q'); ?>">
                  </div>
                  <select class="form-control chosen chosen-select" name="id_acara" id="id_acara">
                     <option value="">-- Semua Acara --</option>
                     <?php if (!empty($acara_dropdown)): foreach ($acara_dropdown as $a): ?>
                     <option <?= (isset($id_acara_selected) && $id_acara_selected == $a->id_acara) ? 'selected' : ''; ?> value="<?= _ent($a->id_acara); ?>"><?= _ent(isset($a->nama_acara) ? $a->nama_acara : $a->id_acara); ?></option>
                     <?php endforeach; endif; ?>
                  </select>
                  <button type="submit" class="labs-btn labs-btn--primary"><i class="fa fa-search"></i> Cari</button>
                  <?php if(!empty($this->input->get('q')) || !empty($this->input->get('id_acara'))): ?>
                  <a class="labs-btn labs-btn--default" href="<?= base_url('administrator/acara_presensi_evaluasi'); ?>"><i class="fa fa-undo"></i> Reset</a>
                  <?php endif; ?>
               </div>

               <div class="labs-table-wrap">
                  <table class="labs-table">
                     <thead>
                        <tr>
                           <th width="30"><input type="checkbox" class="flat-red" id="check_all"></th>
                           <th>NPP</th>
                           <th>Peserta</th>
                           <th>Jumlah Q&amp;A</th>
                           <th width="220">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php if($acara_presensi_evaluasi_counts > 0): ?>
                     <?php foreach ($acara_presensi_evaluasis as $row): ?>
                        <tr>
                           <td class="labs-text-center"><input type="checkbox" class="flat-red check" name="id[]" value="<?= _ent($row->sample_id); ?>"></td>
                           <td class="labs-cell-numeric"><?= _ent($row->npp); ?></td>
                           <td>
                              <div class="labs-name-cell">
                                 <?php
                                 $pesertaDisplay = '';
                                 if (isset($row->peserta) && $row->peserta !== '') {
                                    $pesertaDisplay = $row->peserta;
                                 } elseif (isset($row->guru_nama_lengkap) && $row->guru_nama_lengkap !== null && $row->guru_nama_lengkap !== '') {
                                    $pesertaDisplay = $row->guru_nama_lengkap;
                                 } else {
                                    $pesertaDisplay = '-';
                                 }
                                 $initials = strtoupper(substr($pesertaDisplay, 0, 2));
                                 ?>
                                 <span class="labs-avatar labs-avatar--sm"><?= $initials; ?></span>
                                 <span><?= _ent($pesertaDisplay); ?></span>
                              </div>
                           </td>
                           <td class="labs-text-center">
                              <span class="labs-badge labs-badge--info labs-badge--solid"><?= (int)(isset($row->qa_count) ? $row->qa_count : 0); ?> Q&amp;A</span>
                           </td>
                           <td style="white-space:nowrap">
                              <div class="labs-row-actions" style="justify-content:center">
                                 <a href="#" class="labs-btn labs-btn--info labs-btn--sm grafik-btn" data-npp="<?= _ent($row->npp); ?>" data-peserta="<?= _ent(isset($row->peserta) ? $row->peserta : ''); ?>" data-nama="<?= _ent(isset($row->guru_nama_lengkap) ? $row->guru_nama_lengkap : ''); ?>">
                                    <i class="fa fa-line-chart"></i> Grafik
                                 </a>
                                 <a href="#" class="labs-btn labs-btn--warning labs-btn--sm qA-btn" data-npp="<?= _ent($row->npp); ?>" data-peserta="<?= _ent(isset($row->peserta) ? $row->peserta : ''); ?>" data-nama="<?= _ent(isset($row->guru_nama_lengkap) ? $row->guru_nama_lengkap : ''); ?>">
                                    <i class="fa fa-comments"></i> Q&amp;A
                                 </a>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="5">
                              <div class="labs-empty">
                                 <i class="fa fa-inbox"></i>
                                 <p>Data hasil evaluasi tidak tersedia</p>
                              </div>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <div class="labs-flex-between labs-mt-4">
                  <div class="labs-flex labs-flex-gap-2" style="align-items:center">
                     <select class="form-control" name="bulk" id="bulk" style="width:180px;height:34px;font-size:12px">
                        <option value="">-- Bulk Action --</option>
                        <option value="delete">Hapus Terpilih</option>
                     </select>
                     <button type="button" class="labs-btn labs-btn--default" id="apply">Terapkan</button>
                  </div>
                  <div class="dataTables_paginate paging_simple_numbers">
                     <?= $pagination; ?>
                  </div>
               </div>
               </form>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- Modal: Daftar Pertanyaan & Jawaban -->
<div class="modal fade labs-modal" id="qAModal" tabindex="-1" role="dialog" aria-labelledby="qAModalLabel">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header" style="background:linear-gradient(135deg, var(--labs-warning), #92400E)">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="qAModalLabel"><i class="fa fa-comments"></i> Pertanyaan & Jawaban</h4>
         </div>
         <div class="modal-body">
            <!-- Info Guru -->
            <div class="labs-modal-section">
               <div class="labs-modal-section__title"><i class="fa fa-user"></i> Informasi Peserta</div>
               <div class="labs-info-grid" style="grid-template-columns: repeat(2, 1fr);">
                  <div class="labs-info-grid__item">
                     <div class="labs-info-grid__label">NPP</div>
                     <div class="labs-info-grid__value" id="qA_npp">-</div>
                  </div>
                  <div class="labs-info-grid__item">
                     <div class="labs-info-grid__label">Peserta</div>
                     <div class="labs-info-grid__value" id="qA_peserta">-</div>
                  </div>
               </div>
            </div>

            <!-- Tabel Pertanyaan & Jawaban -->
            <div class="labs-modal-section labs-mt-4">
               <div class="labs-modal-section__title">
                  <i class="fa fa-list"></i> Daftar Pertanyaan & Jawaban
                  <div style="margin-left:auto">
                     <input type="text" id="qA_search_input" class="form-control input-sm" placeholder="Cari..." style="width:200px;height:30px;font-size:12px;display:inline-block">
                  </div>
               </div>
               <div class="labs-table-wrap">
                  <table class="labs-table" id="qA_table">
                     <thead>
                        <tr>
                           <th width="5%">No</th>
                           <th>Pertanyaan</th>
                           <th>Jawaban</th>
                        </tr>
                     </thead>
                     <tbody id="qA_tbody">
                        <!-- filled via JS -->
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="labs-btn labs-btn--default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
         </div>
      </div>
   </div>
</div>

<!-- Modal Grafik per Peserta -->
<div class="modal fade labs-modal" id="grafikModal" tabindex="-1" role="dialog" aria-labelledby="grafikModalLabel">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header" style="background:linear-gradient(135deg, var(--labs-info), #155E75)">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="grafikModalLabel"><i class="fa fa-line-chart"></i> Grafik Jawaban Numeric</h4>
         </div>
         <div class="modal-body">
            <!-- Info section -->
            <div class="labs-modal-section">
               <div class="labs-info-grid" style="grid-template-columns: repeat(2, 1fr);">
                  <div class="labs-info-grid__item">
                     <div class="labs-info-grid__label">NPP</div>
                     <div class="labs-info-grid__value" id="grafik_npp">-</div>
                  </div>
                  <div class="labs-info-grid__item">
                     <div class="labs-info-grid__label">Peserta</div>
                     <div class="labs-info-grid__value" id="grafik_peserta">-</div>
                  </div>
               </div>
            </div>
            <!-- Chart type toggle -->
            <div class="labs-text-center labs-mt-3 labs-mb-3">
               <div class="labs-btn-group">
                  <button type="button" class="labs-btn labs-btn--primary labs-btn--sm" id="grafikTypeBar">
                     <i class="fa fa-bar-chart"></i> Bar
                  </button>
                  <button type="button" class="labs-btn labs-btn--default labs-btn--sm" id="grafikTypeLine">
                     <i class="fa fa-line-chart"></i> Line
                  </button>
               </div>
            </div>
            <!-- Chart canvas -->
            <div style="position:relative;height:380px">
               <canvas id="grafikChart"></canvas>
            </div>
            <div id="grafikEmpty" class="labs-empty" style="display:none">
               <i class="fa fa-inbox"></i>
               <p>Peserta ini belum memiliki jawaban numeric</p>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="labs-btn labs-btn--default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
         </div>
      </div>
   </div>
</div>

<script>
var QA_DATA = [];

function toggleGroup(guru, count) {
   // backward compat - no-op
}

function lihatQa(npp, nama, peserta) {
   $('#qA_npp').text(npp || '-');
   $('#qA_peserta').text(peserta && peserta.length > 0 ? peserta : '-');
   $('#qA_tbody').html('<tr><td colspan="3" class="labs-text-center"><i class="fa fa-spinner fa-spin"></i> Memuat...</td></tr>');

   $.ajax({
      url: BASE_URL + 'administrator/acara_presensi_evaluasi/get_jawaban_by_peserta',
      method: 'GET',
      data: { npp: npp, peserta: peserta },
      dataType: 'json',
      success: function(resp) {
         var $tbody = $('#qA_tbody');
         $tbody.empty();
         if (resp && resp.info) {
            if (resp.info.npp) $('#qA_npp').text(resp.info.npp);
            if (resp.info.peserta && resp.info.peserta.length > 0) $('#qA_peserta').text(resp.info.peserta);
         }
         if (!resp || !resp.qa || resp.qa.length === 0) {
            $tbody.append('<tr><td colspan="3" class="labs-text-center labs-text-muted">Tidak ada data pertanyaan & jawaban</td></tr>');
            return;
         }
         resp.qa.forEach(function(item, i) {
            var $tr = $('<tr></tr>');
            $tr.append('<td class="labs-cell-numeric">' + (i + 1) + '</td>');
            $tr.append('<td>' + (item.pertanyaan || '-') + '</td>');
            $tr.append('<td>' + (item.jawaban || '-') + '</td>');
            $tbody.append($tr);
         });
      },
      error: function() {
         $('#qA_tbody').html('<tr><td colspan="3" class="labs-text-center labs-text-danger">Gagal memuat data. Coba lagi.</td></tr>');
      }
   });

   $('#qAModal').modal('show');
}

$(document).ready(function() {
   $('#qA_search_input').on('input', function() {
      var q = $(this).val().toLowerCase();
      var $tbody = $('#qA_tbody');
      $tbody.find('tr').each(function() {
         var text = $(this).text().toLowerCase();
         if (q === '' || text.indexOf(q) !== -1) {
            $(this).show();
         } else {
            $(this).hide();
         }
      });
   });
});
</script>

<!-- Chart.js init -->
<script>
var grafikChart = null;
var grafikCurrentData = [];

function lihatGrafik(npp, nama, peserta) {
   $('#grafik_npp').text(npp || '-');
   $('#grafik_peserta').text(nama && nama.length > 0 ? nama : (peserta && peserta.length > 0 ? peserta : '-'));
   $('#grafikEmpty').hide();
   $('#grafikChart').show();
   $('#grafikTypeBar').addClass('labs-btn--primary').removeClass('labs-btn--default');
   $('#grafikTypeLine').addClass('labs-btn--default').removeClass('labs-btn--primary');

   if (grafikChart) {
      grafikChart.destroy();
      grafikChart = null;
   }

   var canvas = document.getElementById('grafikChart');
   var ctx = canvas.getContext('2d');
   ctx.clearRect(0, 0, canvas.width, canvas.height);
   ctx.font = '14px sans-serif';
   ctx.fillStyle = '#999';
   ctx.textAlign = 'center';
   ctx.fillText('Memuat...', canvas.width / 2, canvas.height / 2);

   $.ajax({
      url: BASE_URL + 'administrator/acara_presensi_evaluasi/get_chart_by_peserta',
      method: 'GET',
      data: { npp: npp, peserta: peserta, id_acara: $('#id_acara').val() },
      dataType: 'json',
      success: function(resp) {
         if (resp && resp.info && resp.info.nama && resp.info.nama.length > 0) {
            $('#grafik_peserta').text(resp.info.nama);
         }
         if (!resp || !resp.chart || resp.chart.length === 0) {
            $('#grafikEmpty').show();
            $('#grafikChart').hide();
            return;
         }
         renderGrafik(resp.chart, 'bar');
      },
      error: function() {
         $('#grafikEmpty').html('<i class="fa fa-exclamation-triangle labs-text-danger"></i> Gagal memuat data').show();
         $('#grafikChart').hide();
      }
   });

   $('#grafikModal').modal('show');
}

function renderGrafik(data, type) {
   grafikCurrentData = data;
   if (grafikChart) {
      grafikChart.destroy();
   }

   var labels = data.map(function(d) {
      return d.no_urut != null ? d.no_urut.toString() : '?';
   });
   var values = data.map(function(d) { return parseFloat(d.jawaban) || 0; });

   var isLine = (type === 'line');
   var canvas = document.getElementById('grafikChart');
   var ctx = canvas.getContext('2d');

   grafikChart = new Chart(ctx, {
      type: type,
      data: {
         labels: labels,
         datasets: [{
            label: 'Jawaban',
            data: values,
            backgroundColor: isLine ? 'rgba(14, 116, 144, 0.2)' : 'rgba(14, 116, 144, 0.6)',
            borderColor: 'rgba(14, 116, 144, 1)',
            borderWidth: 2,
            fill: isLine,
            tension: 0.3,
            pointRadius: isLine ? 4 : 0,
            pointBackgroundColor: 'rgba(14, 116, 144, 1)'
         }]
      },
      options: {
         responsive: true,
         maintainAspectRatio: false,
         scales: {
            x: { title: { display: true, text: 'No. Urut Pertanyaan' } },
            y: { beginAtZero: true, title: { display: true, text: 'Nilai Jawaban' } }
         },
         plugins: {
            legend: { display: false },
            tooltip: {
               callbacks: {
                  title: function(items) {
                     var idx = items[0].dataIndex;
                     return 'No. Urut: ' + (data[idx].no_urut != null ? data[idx].no_urut : '?');
                  },
                  label: function(context) {
                     var idx = context.dataIndex;
                     var pertanyaan = data[idx].pertanyaan || '';
                     var truncated = pertanyaan.length > 60 ? pertanyaan.substring(0, 60) + '...' : pertanyaan;
                     return [
                        'Jawaban: ' + context.parsed.y,
                        'Pertanyaan: ' + truncated
                     ];
                  }
               }
            }
         }
      }
   });
}

$(document).ready(function() {
   $('#grafikTypeBar').on('click', function() {
      if (grafikCurrentData.length === 0) return;
      $('#grafikTypeBar').addClass('labs-btn--primary').removeClass('labs-btn--default');
      $('#grafikTypeLine').addClass('labs-btn--default').removeClass('labs-btn--primary');
      renderGrafik(grafikCurrentData, 'bar');
   });
   $('#grafikTypeLine').on('click', function() {
      if (grafikCurrentData.length === 0) return;
      $('#grafikTypeLine').addClass('labs-btn--primary').removeClass('labs-btn--default');
      $('#grafikTypeBar').addClass('labs-btn--default').removeClass('labs-btn--primary');
      renderGrafik(grafikCurrentData, 'line');
   });
});
</script>

<script>
$(document).ready(function(){

   // Delete single
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({
         title: "<?= cclang('are_you_sure'); ?>",
         text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
         cancelButtonText: "<?= cclang('no_cancel_plx'); ?>"
      }, function(isConfirm){
         if (isConfirm) { document.location.href = url; }
      });
      return false;
   });

   // Bulk action
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_acara_presensi_evaluasi').serialize();
      if (bulk.val() == 'delete') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?= cclang('yes_delete_it'); ?>"
         }, function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/acara_presensi_evaluasi/delete?' + serialize_bulk;
            }
         });
         return false;
      } else if(bulk.val() == '') {
         swal({ title: "Pilih aksi terlebih dahulu", type: "warning" });
         return false;
      }
      return false;
   });

   // Check all
   $('#check_all').on('ifChanged', function(){
      $('input.check').iCheck($(this).is(':checked') ? 'check' : 'uncheck');
   });

});
</script>
