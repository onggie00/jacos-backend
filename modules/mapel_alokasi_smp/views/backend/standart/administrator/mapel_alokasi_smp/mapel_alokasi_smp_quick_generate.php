<style>
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
    font-size: 12px;
    color: #666;
}
.card-blue { background: #e3f2fd; border: 1px solid #90caf9; }
.card-green { background: #e8f5e9; border: 1px solid #a5d6a7; }
.card-orange { background: #fff3e0; border: 1px solid #ffcc80; }

.guru-summary-table th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 12px;
}
.guru-summary-table td {
    font-size: 12px;
}
.over-quota { color: #e74c3c; font-weight: bold; }
</style>

<!-- Content Header -->
<section class="content-header">
   <h1>
      Quick Generate <small>Preview dan generate jadwal sekaligus</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">Alokasi Mapel</a></li>
      <li class="active">Quick Generate</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <!-- Summary Cards -->
      <div class="col-md-3">
         <div class="summary-card card-blue">
            <div class="number"><?= $final_count; ?></div>
            <div class="label-text">Alokasi Final</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="summary-card card-green">
            <div class="number"><?= count($guru_summary); ?></div>
            <div class="label-text">Guru Terjadwal</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="summary-card card-orange">
            <div class="number"><?= $total_slots; ?></div>
            <div class="label-text">Total Slot Jam</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="summary-card card-blue">
            <div class="number"><?= $jadwal_count; ?></div>
            <div class="label-text">Jadwal Saat Ini</div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-magic"></i> Preview & Generate Jadwal
               </h3>
               <div class="box-tools pull-right">
                  <a class="btn btn-sm btn-default" href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">
                     <i class="fa fa-arrow-left"></i> Kembali
                  </a>
               </div>
            </div>

            <div class="box-body">
               <?php if ($final_count == 0): ?>
               <!-- No final alokasi -->
               <div class="alert alert-warning">
                  <i class="fa fa-exclamation-triangle"></i>
                  <strong>Belum ada alokasi FINAL!</strong>
                  <p>Silakan import dari Excel atau set alokasi ke FINAL terlebih dahulu.</p>
                  <a class="btn btn-success" href="<?= site_url('administrator/mapel_alokasi_smp/import_excel'); ?>">
                     <i class="fa fa-file-excel-o"></i> Import dari Excel
                  </a>
               </div>
               <?php else: ?>

               <!-- Info -->
               <div class="alert alert-info">
                  <i class="fa fa-info-circle"></i>
                  <strong>Konfirmasi Generate:</strong>
                  Sistem akan menempatkan <strong><?= $total_slots; ?> slot jam</strong> ke jadwal waktu yang tersedia.
                  Proses ini akan menghapus jadwal lama (jika ada) dan generate ulang.
               </div>

               <!-- Guru Summary Table -->
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <h3 class="panel-title">
                        <i class="fa fa-users"></i> Ringkasan Alokasi Per Guru
                        <span class="badge"><?= count($guru_summary); ?> guru</span>
                     </h3>
                  </div>
                  <div class="panel-body" style="max-height:400px;overflow-y:auto">
                     <table class="table table-bordered table-striped guru-summary-table">
                        <thead>
                           <tr>
                              <th>No</th>
                              <th>Nama Guru</th>
                              <th>Kode Mapel</th>
                              <th>Kuota</th>
                              <th>Target</th>
                              <th>Jumlah Kelas</th>
                              <th>Sisa</th>
                           </tr>
                        </thead>
                        <tbody>
                        <?php $no = 0; foreach ($guru_summary as $gs): ?>
                        <?php 
                        $no++;
                        $sisa = $gs->kuota - $gs->total_target;
                        $row_class = ($sisa < 0) ? 'danger' : (($sisa <= 2) ? 'warning' : '');
                        ?>
                        <tr class="<?= $row_class; ?>">
                           <td><?= $no; ?></td>
                           <td><?= _ent($gs->nama_lengkap); ?></td>
                           <td><span class="label label-info"><?= _ent($gs->kode_mapel); ?></span></td>
                           <td style="text-align:center"><?= $gs->kuota; ?></td>
                           <td style="text-align:center"><strong><?= $gs->total_target; ?></strong></td>
                           <td style="text-align:center"><?= $gs->jumlah_kelas; ?></td>
                           <td style="text-align:center">
                              <?php if ($sisa < 0): ?>
                                 <span class="over-quota"><?= $sisa; ?></span>
                              <?php else: ?>
                                 <?= $sisa; ?>
                              <?php endif; ?>
                           </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                     </table>
                  </div>
               </div>

               <!-- Generate Button -->
               <div class="message"></div>
               <div style="margin-top:20px">
                  <button type="button" class="btn btn-lg btn-success" id="btn_generate">
                     <i class="fa fa-play"></i> Generate Jadwal Sekarang
                  </button>
                  <a class="btn btn-lg btn-default" href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">
                     <i class="fa fa-undo"></i> Batal
                  </a>
               </div>

               <?php endif; ?>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Confirm Modal -->
<div class="modal fade" id="modal_confirm" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#28a745;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-check-circle"></i> Konfirmasi Generate</h4>
         </div>
         <div class="modal-body">
            <p>Anda akan generate <strong><?= $total_slots; ?> slot jam</strong> ke jadwal.</p>
            <p><strong>Perhatian:</strong></p>
            <ul>
               <li>Jadwal lama akan <strong>diganti</strong> dengan yang baru</li>
               <li>Proses ini mungkin membutuhkan beberapa saat</li>
               <li>Sistem akan otomatis hindari bentrok kelas dan guru</li>
            </ul>
            <p>Apakah Anda yakin ingin melanjutkan?</p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-success" id="btn_confirm_generate">
               <i class="fa fa-play"></i> Ya, Generate!
            </button>
         </div>
      </div>
   </div>
</div>

<!-- Result Modal -->
<div class="modal fade" id="modal_result" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header" id="result_header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="result_title">Hasil Generate</h4>
         </div>
         <div class="modal-body">
            <div id="result_message"></div>
            <div id="result_log" style="max-height:300px;overflow-y:auto;margin-top:10px"></div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            <a class="btn btn-success" id="btn_view_jadwal" href="<?= site_url('administrator/jadwal_pelajaran_smp'); ?>" style="display:none">
               <i class="fa fa-calendar"></i> Lihat Jadwal
            </a>
         </div>
      </div>
   </div>
</div>

<!-- Page script -->
<script>
$(document).ready(function(){
    
    // Show confirm modal
    $('#btn_generate').click(function(){
        $('#modal_confirm').modal('show');
    });
    
    // Execute generate
    $('#btn_confirm_generate').click(function(){
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');
        
        $.ajax({
            url: BASE_URL + '/administrator/mapel_alokasi_smp/quick_generate_execute',
            type: 'POST',
            dataType: 'json',
            timeout: 120000, // 2 minutes
            success: function(res) {
                $('#modal_confirm').modal('hide');
                
                if (res.success) {
                    $('#result_header').css('background', '#28a745').css('color', '#fff');
                    $('#result_title').html('<i class="fa fa-check-circle"></i> Generate Berhasil!');
                    $('#result_message').html('<div class="alert alert-success">' + res.message + '</div>');
                    $('#btn_view_jadwal').show();
                    
                    // Show log
                    if (res.detail && res.detail.log) {
                        var logHtml = '<div style="font-family:monospace;font-size:11px;background:#f5f5f5;padding:10px;border-radius:3px">';
                        res.detail.log.forEach(function(l){
                            logHtml += '<div>' + l + '</div>';
                        });
                        logHtml += '</div>';
                        $('#result_log').html(logHtml);
                    }
                } else {
                    $('#result_header').css('background', '#dc3545').css('color', '#fff');
                    $('#result_title').html('<i class="fa fa-times-circle"></i> Generate Gagal');
                    $('#result_message').html('<div class="alert alert-danger">' + res.message + '</div>');
                }
                
                $('#modal_result').modal('show');
            },
            error: function(xhr, status, error) {
                $('#modal_confirm').modal('hide');
                
                var errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                $('#result_header').css('background', '#dc3545').css('color', '#fff');
                $('#result_title').html('<i class="fa fa-times-circle"></i> Error');
                $('#result_message').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                $('#result_log').empty();
                $('#modal_result').modal('show');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-play"></i> Ya, Generate!');
            }
        });
    });
    
});
</script>
