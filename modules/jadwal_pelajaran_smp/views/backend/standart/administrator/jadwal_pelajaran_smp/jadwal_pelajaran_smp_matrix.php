<style>
.matrix-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
.matrix-table th {
    background: #3c8dbc;
    color: #fff;
    padding: 8px 5px;
    text-align: center;
    font-weight: 600;
    border: 1px solid #2c6d91;
}
.matrix-table td {
    padding: 8px 5px;
    text-align: center;
    border: 1px solid #ddd;
    vertical-align: middle;
    min-width: 100px;
}
.matrix-table .jam-header {
    background: #f5f5f5;
    font-weight: 600;
    width: 80px;
}
.matrix-table .hari-header {
    background: #3c8dbc;
    color: #fff;
    font-weight: 600;
}
.matrix-cell {
    padding: 3px;
}
.matrix-cell .mapel {
    font-weight: 600;
    color: #333;
    font-size: 11px;
}
.matrix-cell .guru {
    color: #666;
    font-size: 10px;
}
.matrix-cell .kode {
    color: #999;
    font-size: 9px;
}
.non-mengajar {
    background: #f0f0f0;
    color: #999;
    font-style: italic;
    font-size: 10px;
}
.empty-cell {
    background: #fff;
}
/* Warna per mapel */
.bg-mapel-1 { background: #e3f2fd; }
.bg-mapel-2 { background: #e8f5e9; }
.bg-mapel-3 { background: #fff3e0; }
.bg-mapel-4 { background: #fce4ec; }
.bg-mapel-5 { background: #f3e5f5; }
.bg-mapel-6 { background: #e0f7fa; }
.bg-mapel-7 { background: #fff8e1; }
.bg-mapel-8 { background: #efebe9; }
</style>

<!-- Content Header -->
<section class="content-header">
   <h1>
      Matrix Jadwal <small><?= _ent($kelas->label); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/jadwal_pelajaran_smp'); ?>">Jadwal Pelajaran</a></li>
      <li class="active">Matrix <?= _ent($kelas->label); ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-th"></i> Jadwal Pelajaran - <?= _ent($kelas->label); ?>
               </h3>
               <div class="box-tools pull-right">
                  <!-- Export Button -->
                  <?php is_allowed('jadwal_pelajaran_smp_export', function() use ($kelas){?>
                  <a class="btn btn-sm btn-success" href="<?= site_url('administrator/jadwal_pelajaran_smp/export_kelas/' . $kelas->id_kelas_smp); ?>">
                     <i class="fa fa-file-excel-o"></i> Export Excel
                  </a>
                  <?php }) ?>
                  
                  <!-- Kelas Dropdown -->
                  <div class="btn-group">
                     <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-exchange"></i> Ganti Kelas <span class="caret"></span>
                     </button>
                     <ul class="dropdown-menu dropdown-menu-right" style="max-height:300px;overflow-y:auto">
                        <?php 
                        $current_t = 0;
                        $t_label = array(1 => 'Kelas 7', 2 => 'Kelas 8', 3 => 'Kelas 9');
                        foreach ($kelas_list as $k): 
                            if ($k->id_tingkatan != $current_t) {
                                $current_t = $k->id_tingkatan;
                                echo '<li class="dropdown-header">' . $t_label[$current_t] . '</li>';
                            }
                            $active = ($k->id_kelas_smp == $kelas->id_kelas_smp) ? ' class="active"' : '';
                        ?>
                        <li<?= $active ?>><a href="<?= site_url('administrator/jadwal_pelajaran_smp/matrix/' . $k->id_kelas_smp); ?>"><?= $k->label; ?></a></li>
                        <?php endforeach; ?>
                     </ul>
                  </div>
                  
                  <a class="btn btn-sm btn-default" href="<?= site_url('administrator/jadwal_pelajaran_smp'); ?>">
                     <i class="fa fa-list"></i> List View
                  </a>
               </div>
            </div>

            <div class="box-body">
               <?php
               // Build matrix data structure
               $matrix = array();
               foreach ($jadwal_matrix as $j) {
                   $matrix[$j->id_hari][$j->jam_ke] = $j;
               }
               
               // Get unique jam_ke list (sorted)
               $jam_ke_list = array();
               foreach ($jam_list as $jl) {
                   if (!in_array($jl->jam_ke, $jam_ke_list)) {
                       $jam_ke_list[] = $jl->jam_ke;
                   }
               }
               sort($jam_ke_list);
               
               // Color map for mapel
               $mapel_colors = array();
               $color_idx = 0;
               foreach ($jadwal_matrix as $j) {
                   if (!isset($mapel_colors[$j->id_mapel])) {
                       $color_idx++;
                       if ($color_idx > 8) $color_idx = 1;
                       $mapel_colors[$j->id_mapel] = 'bg-mapel-' . $color_idx;
                   }
               }
               ?>

               <div class="table-responsive">
                  <table class="matrix-table">
                     <thead>
                        <tr>
                           <th class="jam-header">Jam</th>
                           <?php foreach ($hari_list as $hari): ?>
                           <th class="hari-header"><?= $hari->hari; ?></th>
                           <?php endforeach; ?>
                        </tr>
                     </thead>
                     <tbody>
                     <?php 
                     $prev_jam = '';
                     foreach ($jam_list as $jl): 
                         // Skip if duplicate jam_ke (different hari have different id_jam)
                         if ($jl->jam_ke == $prev_jam) continue;
                         $prev_jam = $jl->jam_ke;
                     ?>
                        <tr>
                           <td class="jam-header">
                              <strong>Jam <?= $jl->jam_ke; ?></strong><br>
                              <small><?= $jl->jam_pelajaran; ?></small>
                           </td>
                           <?php foreach ($hari_list as $hari): ?>
                           <td>
                              <?php if (isset($matrix[$hari->id_hari][$jl->jam_ke])): ?>
                                 <?php $cell = $matrix[$hari->id_hari][$jl->jam_ke]; ?>
                                 <div class="matrix-cell <?= isset($mapel_colors[$cell->id_mapel]) ? $mapel_colors[$cell->id_mapel] : ''; ?>">
                                    <div class="mapel"><?= _ent($cell->mapel_kode); ?></div>
                                    <div class="guru"><?= _ent($cell->guru_nama); ?></div>
                                 </div>
                              <?php else: ?>
                                 <div class="empty-cell">&nbsp;</div>
                              <?php endif; ?>
                           </td>
                           <?php endforeach; ?>
                        </tr>
                     <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>

               <!-- Legend -->
               <div class="row" style="margin-top:20px">
                  <div class="col-md-12">
                     <strong>Legend Mapel:</strong>
                     <div style="margin-top:10px">
                        <?php 
                        $shown_mapel = array();
                        foreach ($jadwal_matrix as $j) {
                            if (!isset($shown_mapel[$j->id_mapel])) {
                                $shown_mapel[$j->id_mapel] = $j;
                                $cls = isset($mapel_colors[$j->id_mapel]) ? $mapel_colors[$j->id_mapel] : '';
                                echo '<span class="label ' . $cls . '" style="margin-right:10px;padding:5px 10px;color:#333">' . $j->mapel_kode . ' - ' . $j->mapel_nama . '</span>';
                            }
                        }
                        ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
