<style>
.fase1-table th { 
    background: #f8f9fa; 
    font-weight: 600; 
    font-size: 12px; 
    vertical-align: middle;
    text-align: center;
}
.fase1-table td { 
    font-size: 12px; 
    vertical-align: middle;
}
.constraint-warning { background: #fff3cd; }
.constraint-danger { background: #f8d7da; }
.constraint-ok { background: #d4edda; }

/* Summary cards */
.summary-card {
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
    text-align: center;
}
.summary-card .number {
    font-size: 28px;
    font-weight: bold;
}
.summary-card .label-text {
    font-size: 13px;
    color: #666;
}
.card-blue { background: #e3f2fd; border: 1px solid #90caf9; }
.card-green { background: #e8f5e9; border: 1px solid #a5d6a7; }
.card-orange { background: #fff3e0; border: 1px solid #ffcc80; }
.card-red { background: #fce4ec; border: 1px solid #ef9a9a; }

/* Guru group */
.guru-group {
    margin-bottom: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    overflow: hidden;
}
.guru-group-header {
    background: #f5f5f5;
    padding: 8px 15px;
    font-weight: 600;
    cursor: pointer;
}
.guru-group-header:hover {
    background: #e0e0e0;
}
.guru-group-header .badge {
    margin-left: 10px;
}

/* Ketetapan badges */
.badge-ketetapan {
    font-size: 10px;
    padding: 2px 6px;
    margin-left: 5px;
}
.badge-hari { background: #28a745; color: #fff; }
.badge-jam { background: #007bff; color: #fff; }
</style>

<!-- Content Header -->
<section class="content-header">
   <h1>
      Fase 1: Preview Alokasi <small>Urutan constraint dan slot siap jadwalkan</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">Alokasi Mapel</a></li>
      <li class="active">Fase 1</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <!-- Summary Cards -->
      <div class="col-md-3">
         <div class="summary-card card-blue">
            <div class="number"><?= $total_alokasi; ?></div>
            <div class="label-text">Alokasi Final</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="summary-card card-green">
            <div class="number"><?= $total_slots; ?></div>
            <div class="label-text">Total Slot</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="summary-card card-orange">
            <div class="number"><?= $total_guru; ?></div>
            <div class="label-text">Guru Aktif</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="summary-card card-red">
            <div class="number" id="counter_belum">-</div>
            <div class="label-text">Belum Terjadwal</div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-sort-amount-asc"></i> Urutan Generate (Constraint Terberat Duluan)
               </h3>
               <div class="box-tools pull-right">
                  <a class="btn btn-sm btn-success btn-start-generate" href="javascript:void(0);">
                     <i class="fa fa-play"></i> Mulai Generate Fase 2
                  </a>
                  <a class="btn btn-sm btn-default" href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">
                     <i class="fa fa-arrow-left"></i> Kembali
                  </a>
               </div>
            </div>

            <div class="box-body">
               <!-- Info -->
               <div class="alert alert-info">
                  <i class="fa fa-info-circle"></i>
                  <strong>Fase 1 - Penjelasan:</strong>
                  <ul style="margin-bottom:0">
                     <li>Menampilkan semua alokasi berstatus <strong>FINAL</strong> yang belum terpenuhi</li>
                     <li>Setiap alokasi dengan <code>jam_target=N</code> di-expand menjadi N slot terpisah</li>
                     <li>Diurutkan berdasarkan <strong>constraint terberat</strong>: guru dengan sisa jam paling sedikit duluan</li>
                     <li>Ketetapan hari/jam ditampilkan sebagai badge di setiap slot</li>
                  </ul>
               </div>

               <!-- Legend -->
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-12">
                     <strong>Legend:</strong>
                     <span class="label badge-ketetapan badge-hari">HARI: 3,5,6</span> = Hanya hari Selasa, Kamis, Jumat
                     <span class="label badge-ketetapan badge-jam">JAM: 7,8,9,10</span> = Hanya jam ke 7-10
                  </div>
               </div>

               <!-- Data Table -->
               <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover fase1-table" id="table_fase1">
                     <thead>
                        <tr>
                           <th width="40">No</th>
                           <th>Guru</th>
                           <th>Kode Mapel</th>
                           <th>Kelas</th>
                           <th>Tingkatan</th>
                           <th>Slot Jam</th>
                           <th>Sisa Kuota Guru</th>
                           <th>Ketetapan</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php 
                     $no = 0;
                     $current_guru = '';
                     foreach ($expanded as $item): 
                        $no++;
                        
                        // Guru group header
                        $guru_key = $item['id_guru'];
                        if ($guru_key !== $current_guru) {
                            $current_guru = $guru_key;
                            $sisa = $item['guru_sisa_jam'];
                            $row_class = ($sisa <= 0) ? 'constraint-danger' : (($sisa <= 3) ? 'constraint-warning' : '');
                        }
                        
                        // Get ketetapan for this mapel
                        $ketetapan_hari = isset($ketetapan_map[$item['guru_kode_mapel']]['hari']) ? $ketetapan_map[$item['guru_kode_mapel']]['hari'] : null;
                        $ketetapan_jam = isset($ketetapan_map[$item['guru_kode_mapel']]['jam']) ? $ketetapan_map[$item['guru_kode_mapel']]['jam'] : null;
                        
                        $row_class = ($item['guru_sisa_jam'] <= 0) ? 'constraint-danger' : (($item['guru_sisa_jam'] <= 3) ? 'constraint-warning' : 'constraint-ok');
                     ?>
                        <tr class="<?= $row_class; ?>" data-guru="<?= $item['id_guru']; ?>">
                           <td style="text-align:center"><?= $no; ?></td>
                           <td>
                              <strong><?= _ent($item['guru_nama']); ?></strong>
                              <br><small class="text-muted">Kuota: <?= $item['guru_jam_ajar']; ?> jam</small>
                           </td>
                           <td style="text-align:center">
                              <span class="label label-info"><?= _ent($item['mapel_kode']); ?></span>
                              <br><small><?= _ent($item['mapel_nama']); ?></small>
                           </td>
                           <td style="text-align:center">
                              <strong><?= _ent($item['kelas_label']); ?></strong>
                           </td>
                           <td style="text-align:center">
                              <?php
                              $tingkatan_label = array(1 => 'Kelas 7', 2 => 'Kelas 8', 3 => 'Kelas 9');
                              echo isset($tingkatan_label[$item['id_tingkatan']]) ? $tingkatan_label[$item['id_tingkatan']] : '-';
                              ?>
                           </td>
                           <td style="text-align:center">
                              <span class="label label-default">Slot <?= $item['jam_ke']; ?> dari <?= $item['jam_target']; ?></span>
                           </td>
                           <td style="text-align:center">
                              <?php if ($item['guru_sisa_jam'] <= 0): ?>
                                 <span class="label label-danger"><?= $item['guru_sisa_jam']; ?> jam</span>
                              <?php elseif ($item['guru_sisa_jam'] <= 3): ?>
                                 <span class="label label-warning"><?= $item['guru_sisa_jam']; ?> jam</span>
                              <?php else: ?>
                                 <span class="label label-success"><?= $item['guru_sisa_jam']; ?> jam</span>
                              <?php endif; ?>
                           </td>
                           <td>
                              <?php if ($ketetapan_hari): ?>
                                 <span class="label badge-ketetapan badge-hari">
                                    HARI: <?= implode(',', $ketetapan_hari); ?>
                                 </span>
                              <?php endif; ?>
                              <?php if ($ketetapan_jam): ?>
                                 <span class="label badge-ketetapan badge-jam">
                                    JAM: <?= implode(',', $ketetapan_jam); ?>
                                 </span>
                              <?php endif; ?>
                              <?php if (!$ketetapan_hari && !$ketetapan_jam): ?>
                                 <small class="text-muted">Bebas</small>
                              <?php endif; ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if (empty($expanded)): ?>
                        <tr>
                           <td colspan="8" class="text-center" style="padding:30px">
                              <i class="fa fa-check-circle" style="font-size:40px;color:#28a745"></i><br>
                              <span style="color:#28a745"><strong>Semua alokasi sudah terpenuhi!</strong></span>
                              <br><span style="color:#999">Tidak ada slot yang perlu dijadwalkan</span>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <!-- Bottom Info -->
               <div class="row" style="margin-top:20px">
                  <div class="col-md-6">
                     <div class="panel panel-info">
                        <div class="panel-heading">
                           <h3 class="panel-title"><i class="fa fa-info-circle"></i> Ringkasan Per Guru</h3>
                        </div>
                        <div class="panel-body" style="max-height:300px;overflow-y:auto">
                           <table class="table table-condensed table-bordered" style="font-size:12px">
                              <thead>
                                 <tr>
                                    <th>Guru</th>
                                    <th>Kode</th>
                                    <th>Kuota</th>
                                    <th>Terpakai</th>
                                    <th>Sisa</th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php foreach ($guru_sisa as $id_guru => $sisa): ?>
                                 <?php
                                 // Find guru info from expanded
                                 $guru_info = null;
                                 foreach ($expanded as $e) {
                                     if ($e['id_guru'] == $id_guru) {
                                         $guru_info = $e;
                                         break;
                                     }
                                 }
                                 if (!$guru_info) continue;
                                 
                                 $terpakai = $guru_info['guru_jam_ajar'] - $sisa;
                                 $row_cls = ($sisa <= 0) ? 'danger' : (($sisa <= 3) ? 'warning' : '');
                                 ?>
                                 <tr class="<?= $row_cls; ?>">
                                    <td><?= _ent($guru_info['guru_nama']); ?></td>
                                    <td><?= _ent($guru_info['guru_kode_mapel']); ?></td>
                                    <td style="text-align:center"><?= $guru_info['guru_jam_ajar']; ?></td>
                                    <td style="text-align:center"><?= $terpakai; ?></td>
                                    <td style="text-align:center"><strong><?= $sisa; ?></strong></td>
                                 </tr>
                              <?php endforeach; ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="panel panel-warning">
                        <div class="panel-heading">
                           <h3 class="panel-title"><i class="fa fa-gavel"></i> Ketetapan Aktif</h3>
                        </div>
                        <div class="panel-body">
                           <?php if (!empty($ketetapan_map)): ?>
                           <table class="table table-condensed table-bordered" style="font-size:12px">
                              <thead>
                                 <tr>
                                    <th>Mapel</th>
                                    <th>Tipe</th>
                                    <th>Nilai</th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php foreach ($ketetapan_map as $kode => $rules): ?>
                                 <?php foreach ($rules as $tipe => $nilai): ?>
                                 <tr>
                                    <td><strong><?= _ent($kode); ?></strong></td>
                                    <td>
                                       <?php if ($tipe == 'hari'): ?>
                                          <span class="label badge-hari">HARI</span>
                                       <?php else: ?>
                                          <span class="label badge-jam">JAM</span>
                                       <?php endif; ?>
                                    </td>
                                    <td><?= implode(', ', $nilai); ?></td>
                                 </tr>
                                 <?php endforeach; ?>
                              <?php endforeach; ?>
                              </tbody>
                           </table>
                           <?php else: ?>
                              <p class="text-muted">Tidak ada ketetapan aktif</p>
                           <?php endif; ?>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Confirm Modal -->
<div class="modal fade" id="modal_confirm_generate" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#f0ad4e;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Konfirmasi Generate</h4>
         </div>
         <div class="modal-body">
            <p>Anda akan memulai proses <strong>Fase 2: Penempatan Waktu</strong>.</p>
            <p>Proses ini akan:</p>
            <ul>
               <li>Mengambil semua slot di atas (<?= $total_slots; ?> slot)</li>
               <li>Menempatkan ke slot waktu yang tersedia</li>
               <li>Mematuhi semua ketetapan hari/jam</li>
               <li>Menghindari bentrok kelas dan guru</li>
            </ul>
            <p><strong>Apakah Anda yakin ingin melanjutkan?</strong></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <a href="<?= site_url('administrator/mapel_alokasi_smp/fase2'); ?>" class="btn btn-warning" id="btn_confirm_generate">
               <i class="fa fa-play"></i> Ya, Mulai Generate
            </a>
         </div>
      </div>
   </div>
</div>

<!-- Page script -->
<script>
$(document).ready(function(){
    
    // Update counter
    var totalSlots = <?= $total_slots; ?>;
    $('#counter_belum').text(totalSlots);
    
    // Start generate button
    $('.btn-start-generate').click(function(){
        <?php if ($total_slots > 0): ?>
        $('#modal_confirm_generate').modal('show');
        <?php else: ?>
        toastr.info('Tidak ada slot yang perlu dijadwalkan');
        <?php endif; ?>
    });
    
    // Toggle guru group
    $('.guru-group-header').click(function(){
        $(this).next('.guru-group-body').slideToggle();
    });
    
});
</script>
