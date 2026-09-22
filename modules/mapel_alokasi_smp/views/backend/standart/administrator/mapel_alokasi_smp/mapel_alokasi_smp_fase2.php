<style>
.result-card {
    border-radius: 5px;
    padding: 20px;
    margin-bottom: 15px;
    text-align: center;
}
.result-card .number {
    font-size: 36px;
    font-weight: bold;
}
.result-card .label-text {
    font-size: 14px;
    color: #666;
}
.card-success { background: #d4edda; border: 1px solid #28a745; }
.card-danger { background: #f8d7da; border: 1px solid #dc3545; }
.card-info { background: #d1ecf1; border: 1px solid #17a2b8; }
.card-warning { background: #fff3cd; border: 1px solid #ffc107; }

.log-box {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    padding: 15px;
    max-height: 400px;
    overflow-y: auto;
    font-family: monospace;
    font-size: 12px;
}
.log-ok { color: #28a745; }
.log-error { color: #dc3545; }
.log-info { color: #17a2b8; }
</style>

<!-- Content Header -->
<section class="content-header">
   <h1>
      Fase 2: Hasil Generate <small>Penempatan waktu jadwal</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">Alokasi Mapel</a></li>
      <li><a href="<?= site_url('administrator/mapel_alokasi_smp/fase1'); ?>">Fase 1</a></li>
      <li class="active">Fase 2</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <!-- Summary Cards -->
      <div class="col-md-3">
         <div class="result-card card-info">
            <div class="number"><?= $result['total_slots']; ?></div>
            <div class="label-text">Total Slot</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="result-card card-success">
            <div class="number"><?= $result['berhasil']; ?></div>
            <div class="label-text">Berhasil Terjadwal</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="result-card card-warning">
            <div class="number"><?= $result['sisa']; ?></div>
            <div class="label-text">Masih Belum Terpenuhi</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="result-card <?= $result['success'] ? 'card-success' : 'card-danger'; ?>">
            <div class="number"><?= $result['success'] ? '✓' : '✗'; ?></div>
            <div class="label-text"><?= $result['success'] ? 'Berhasil' : 'Ada Error'; ?></div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-list"></i> Detail Hasil Generate
               </h3>
               <div class="box-tools pull-right">
                  <a class="btn btn-sm btn-info" href="<?= site_url('administrator/jadwal_pelajaran_smp'); ?>">
                     <i class="fa fa-calendar"></i> Lihat Jadwal
                  </a>
                  <a class="btn btn-sm btn-warning btn-reset-jadwal" href="javascript:void(0);">
                     <i class="fa fa-undo"></i> Reset & Ulangi
                  </a>
                  <a class="btn btn-sm btn-default" href="<?= site_url('administrator/mapel_alokasi_smp/fase1'); ?>">
                     <i class="fa fa-arrow-left"></i> Kembali ke Fase 1
                  </a>
               </div>
            </div>

            <div class="box-body">
               <!-- Status Alert -->
               <?php if ($result['success'] && $result['sisa'] == 0): ?>
               <div class="alert alert-success">
                  <i class="fa fa-check-circle"></i>
                  <strong>Selamat!</strong> Semua slot berhasil dijadwalkan. 
                  <a href="<?= site_url('administrator/jadwal_pelajaran_smp'); ?>">Lihat jadwal lengkap</a>.
               </div>
               <?php elseif ($result['sisa'] > 0): ?>
               <div class="alert alert-warning">
                  <i class="fa fa-exclamation-triangle"></i>
                  <strong>Perhatian:</strong> Masih ada <?= $result['sisa']; ?> alokasi yang belum terpenuhi. 
                  Periksa log di bawah untuk detail.
               </div>
               <?php endif; ?>

               <?php if (!empty($result['errors'])): ?>
               <div class="alert alert-danger">
                  <i class="fa fa-times-circle"></i>
                  <strong>Error (<?= count($result['errors']); ?>):</strong>
                  <ul style="margin-bottom:0;margin-top:10px">
                     <?php foreach ($result['errors'] as $error): ?>
                     <li><?= _ent($error); ?></li>
                     <?php endforeach; ?>
                  </ul>
               </div>
               <?php endif; ?>

               <!-- Log -->
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <h3 class="panel-title">
                        <i class="fa fa-terminal"></i> Log Proses
                        <span class="badge"><?= count($result['log']); ?> entries</span>
                     </h3>
                  </div>
                  <div class="panel-body">
                     <div class="log-box">
                        <?php foreach ($result['log'] as $log): ?>
                        <?php
                        $log_class = 'log-info';
                        if (strpos($log, 'OK:') === 0) $log_class = 'log-ok';
                        if (strpos($log, 'GAGAL:') === 0 || strpos($log, 'Error') !== false) $log_class = 'log-error';
                        ?>
                        <div class="<?= $log_class; ?>"><?= _ent($log); ?></div>
                        <?php endforeach; ?>
                        <?php if (empty($result['log'])): ?>
                        <div class="log-info">Tidak ada log</div>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>

               <!-- Info Panel -->
               <div class="panel panel-info">
                  <div class="panel-heading">
                     <h3 class="panel-title"><i class="fa fa-info-circle"></i> Penjelasan</h3>
                  </div>
                  <div class="panel-body">
                     <ul>
                        <li><strong>Total Slot</strong>: Jumlah slot yang perlu dijadwalkan (dari Fase 1)</li>
                        <li><strong>Berhasil Terjadwal</strong>: Jumlah baris di tabel <code>jadwal_pelajaran_smp</code></li>
                        <li><strong>Masih Belum Terpenuhi</strong>: Alokasi yang <code>jam_target > jam_terpenuhi</code></li>
                        <li><strong>Reset & Ulangi</strong>: Hapus semua jadwal dan mulai dari awal</li>
                     </ul>
                     <p><strong>Constraint yang dicek:</strong></p>
                     <ol>
                        <li>Slot harus kategori MENGAJAR</li>
                        <li>Kelas tidak bentrok di slot yang sama</li>
                        <li>Guru tidak bentrok di slot yang sama (lintas kelas/jenjang)</li>
                        <li>Maksimal 3 jam per hari untuk kombinasi (kelas, mapel)</li>
                        <li>Ketetapan hari/jam dari tabel <code>mapel_ketetapan_smp</code></li>
                     </ol>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Page script -->
<script>
$(document).ready(function(){
    
    // Reset jadwal
    $('.btn-reset-jadwal').click(function(){
        swal({
            title: "Reset Jadwal?",
            text: "Semua jadwal akan dihapus dan jam_terpenuhi direset ke 0",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, Reset!",
            cancelButtonText: "Batal",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                $.post(BASE_URL + '/administrator/mapel_alokasi_smp/reset_jadwal', function(res){
                    var data = JSON.parse(res);
                    if (data.success) {
                        swal("Berhasil", data.message, "success");
                        setTimeout(function(){
                            window.location.href = BASE_URL + '/administrator/mapel_alokasi_smp/fase1';
                        }, 1500);
                    } else {
                        swal("Gagal", data.message, "error");
                    }
                });
            }
        });
    });
    
});
</script>
