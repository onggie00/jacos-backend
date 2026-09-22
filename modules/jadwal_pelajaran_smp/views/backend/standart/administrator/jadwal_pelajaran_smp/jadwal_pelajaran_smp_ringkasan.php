<style>
.ringkasan-table th {
    background: #3c8dbc;
    color: #fff;
    padding: 8px 5px;
    text-align: center;
    font-weight: 600;
    border: 1px solid #2c6d91;
    font-size: 12px;
}
.ringkasan-table td {
    padding: 6px 5px;
    text-align: center;
    border: 1px solid #ddd;
    font-size: 12px;
    vertical-align: middle;
}
.ringkasan-table .guru-cell {
    text-align: left;
    font-weight: 600;
}
.ringkasan-table .total-cell {
    background: #f5f5f5;
    font-weight: 600;
}
.over-limit { background: #f8d7da; color: #721c24; }
.ok-limit { background: #d4edda; color: #155724; }
.warning-limit { background: #fff3cd; color: #856404; }
.empty-cell { color: #ccc; }

.summary-cards {
    margin-bottom: 20px;
}
.summary-card {
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 10px;
    text-align: center;
    border: 1px solid #e0e0e0;
}
.summary-card .number {
    font-size: 28px;
    font-weight: bold;
}
.summary-card .label-text {
    font-size: 12px;
    color: #666;
}
</style>

<!-- Content Header -->
<section class="content-header">
   <h1>
      Ringkasan Guru <small>Jadwal mengajar per hari</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/jadwal_pelajaran_smp'); ?>">Jadwal Pelajaran</a></li>
      <li class="active">Ringkasan Guru</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <!-- Summary Cards -->
      <div class="col-md-3">
         <div class="summary-card">
            <div class="number"><?= count($guru_total); ?></div>
            <div class="label-text">Guru Terjadwal</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="summary-card">
            <div class="number"><?= count($hari_list); ?></div>
            <div class="label-text">Hari Mengajar</div>
         </div>
      </div>
      <div class="col-md-3">
         <?php
         $total_jam = 0;
         foreach ($guru_total as $gt) {
             $total_jam += $gt->total_jam;
         }
         ?>
         <div class="summary-card">
            <div class="number"><?= $total_jam; ?></div>
            <div class="label-text">Total Jam Mengajar</div>
         </div>
      </div>
      <div class="col-md-3">
         <?php
         $over_limit = 0;
         foreach ($guru_total as $gt) {
             if ($gt->total_jam > $gt->jam_ajar) {
                 $over_limit++;
             }
         }
         ?>
         <div class="summary-card" style="<?= ($over_limit > 0) ? 'border-color:#dc3545;background:#f8d7da' : 'border-color:#28a745;background:#d4edda' ?>">
            <div class="number"><?= $over_limit; ?></div>
            <div class="label-text">Guru Overload</div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-users"></i> Ringkasan Jam Mengajar Per Guru Per Hari
               </h3>
               <div class="box-tools pull-right">
                  <a class="btn btn-sm btn-default" href="<?= site_url('administrator/jadwal_pelajaran_smp'); ?>">
                     <i class="fa fa-list"></i> List View
                  </a>
               </div>
            </div>

            <div class="box-body">
               <!-- Info -->
               <div class="alert alert-info">
                  <i class="fa fa-info-circle"></i>
                  <strong>Legend:</strong>
                  <span class="label ok-limit" style="margin-left:10px">Normal</span>
                  <span class="label warning-limit" style="margin-left:5px">Mendekati Batas</span>
                  <span class="label over-limit" style="margin-left:5px">Overload</span>
                  <span style="margin-left:10px;color:#999">Kuota = jam_ajar dari tabel guru_smp</span>
               </div>

               <!-- Table -->
               <div class="table-responsive">
                  <table class="table table-bordered table-striped ringkasan-table">
                     <thead>
                        <tr>
                           <th rowspan="2" style="vertical-align:bottom">No</th>
                           <th rowspan="2" style="vertical-align:bottom">Nama Guru</th>
                           <th rowspan="2" style="vertical-align:bottom">Kode Mapel</th>
                           <th rowspan="2" style="vertical-align:bottom">Kuota</th>
                           <?php foreach ($hari_list as $hari): ?>
                           <th><?= $hari->hari; ?></th>
                           <?php endforeach; ?>
                           <th rowspan="2" style="vertical-align:bottom;background:#2c6d91">Total</th>
                           <th rowspan="2" style="vertical-align:bottom;background:#2c6d91">Sisa</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php
                     // Build lookup: guru -> hari -> jam
                     $guru_hari_map = array();
                     foreach ($guru_per_hari as $gph) {
                         if (!isset($guru_hari_map[$gph->id_guru])) {
                             $guru_hari_map[$gph->id_guru] = array();
                         }
                         $guru_hari_map[$gph->id_guru][$gph->id_hari] = $gph->jam_per_hari;
                     }
                     
                     $no = 0;
                     foreach ($guru_total as $gt):
                         $no++;
                         $sisa = $gt->jam_ajar - $gt->total_jam;
                         
                         // Row class based on total
                         $row_class = '';
                         if ($gt->total_jam > $gt->jam_ajar) {
                             $row_class = 'over-limit';
                         } elseif ($sisa <= 2) {
                             $row_class = 'warning-limit';
                         } else {
                             $row_class = 'ok-limit';
                         }
                     ?>
                        <tr class="<?= $row_class; ?>">
                           <td><?= $no; ?></td>
                           <td class="guru-cell"><?= _ent($gt->nama_lengkap); ?></td>
                           <td><?= _ent($gt->kode_mapel); ?></td>
                           <td><strong><?= $gt->jam_ajar; ?></strong></td>
                           <?php foreach ($hari_list as $hari): ?>
                           <td>
                              <?php
                              $jam = isset($guru_hari_map[$gt->id_guru][$hari->id_hari]) ? $guru_hari_map[$gt->id_guru][$hari->id_hari] : 0;
                              echo ($jam > 0) ? $jam : '<span class="empty-cell">-</span>';
                              ?>
                           </td>
                           <?php endforeach; ?>
                           <td class="total-cell">
                              <strong><?= $gt->total_jam; ?></strong>
                           </td>
                           <td class="total-cell">
                              <?php if ($sisa < 0): ?>
                                 <span style="color:#dc3545;font-weight:bold"><?= $sisa; ?></span>
                              <?php elseif ($sisa == 0): ?>
                                 <span style="color:#856404">0</span>
                              <?php else: ?>
                                 <span style="color:#28a745"><?= $sisa; ?></span>
                              <?php endif; ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>

               <!-- Detail per Hari -->
               <div class="row" style="margin-top:30px">
                  <div class="col-md-12">
                     <h4><i class="fa fa-bar-chart"></i> Distribusi Jam Per Hari</h4>
                     <table class="table table-bordered table-condensed" style="font-size:12px">
                        <thead>
                           <tr>
                              <th>Hari</th>
                              <th>Total Jam</th>
                              <th>Jumlah Guru</th>
                              <th>Rata-rata/Guru</th>
                           </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($hari_list as $hari): ?>
                        <?php
                        $hari_total = 0;
                        $hari_guru = 0;
                        foreach ($guru_per_hari as $gph) {
                            if ($gph->id_hari == $hari->id_hari) {
                                $hari_total += $gph->jam_per_hari;
                                $hari_guru++;
                            }
                        }
                        $avg = ($hari_guru > 0) ? round($hari_total / $hari_guru, 1) : 0;
                        ?>
                        <tr>
                           <td><strong><?= $hari->hari; ?></strong></td>
                           <td style="text-align:center"><?= $hari_total; ?> jam</td>
                           <td style="text-align:center"><?= $hari_guru; ?> guru</td>
                           <td style="text-align:center"><?= $avg; ?> jam/guru</td>
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
