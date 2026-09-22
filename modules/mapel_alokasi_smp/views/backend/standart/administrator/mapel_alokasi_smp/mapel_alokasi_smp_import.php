<style>
.import-info {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
}
.import-info h5 {
    margin-top: 0;
    font-weight: 600;
}
.mapping-table {
    font-size: 12px;
}
.mapping-table th {
    background: #e9ecef;
}
.step-badge {
    display: inline-block;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #3c8dbc;
    color: #fff;
    text-align: center;
    line-height: 24px;
    font-size: 12px;
    font-weight: bold;
    margin-right: 5px;
}
</style>

<!-- Content Header -->
<section class="content-header">
   <h1>
      Import Alokasi dari Excel <small>Upload file Excel untuk isi alokasi otomatis</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">Alokasi Mapel</a></li>
      <li class="active">Import Excel</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-file-excel-o"></i> Import Alokasi dari Excel
               </h3>
               <div class="box-tools pull-right">
                  <a class="btn btn-sm btn-default" href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">
                     <i class="fa fa-arrow-left"></i> Kembali
                  </a>
               </div>
            </div>

            <div class="box-body">
               <!-- Info Box -->
               <div class="import-info">
                  <h5><i class="fa fa-info-circle"></i> Cara Import</h5>
                  <ol style="margin-bottom:0">
                     <li>Upload file Excel dengan format yang benar (sheet "Lembar2")</li>
                     <li>Sistem akan membaca data guru dan jam per kelas dari Excel</li>
                     <li>Data alokasi akan dibuat otomatis dengan status <span class="label label-success">FINAL</span></li>
                     <li>Setelah import, gunakan <strong>Quick Generate</strong> untuk buat jadwal</li>
                  </ol>
               </div>

               <!-- Mapping Info -->
               <div class="panel panel-info">
                  <div class="panel-heading">
                     <h3 class="panel-title"><i class="fa fa-table"></i> Mapping Kolom Excel</h3>
                  </div>
                  <div class="panel-body">
                     <table class="table table-bordered mapping-table">
                        <thead>
                           <tr>
                              <th>Kolom</th>
                              <th>Isi</th>
                              <th>Mapping ke Database</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td><code>B</code></td>
                              <td>Nama Guru</td>
                              <td>Match ke <code>guru_smp.nama_lengkap</code> (nama sebelum koma)</td>
                           </tr>
                           <tr>
                              <td><code>H</code></td>
                              <td>Kode Mapel (e.g. BIN1-7A)</td>
                              <td>Extract kode sebelum "-" → <code>mata_pelajaran_smp.kode_mapel</code></td>
                           </tr>
                           <tr>
                              <td><code>J-Q</code></td>
                              <td>Jam kelas 7A-7H</td>
                              <td rowspan="3"><code>mapel_alokasi_smp.jam_target</code><br><small>Otomatis detect kelas dari posisi kolom</small></td>
                           </tr>
                           <tr>
                              <td><code>S-Y</code></td>
                              <td>Jam kelas 8A-8G</td>
                           </tr>
                           <tr>
                              <td><code>AA-AG</code></td>
                              <td>Jam kelas 9A-9G</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>

               <!-- Upload Form -->
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <h3 class="panel-title"><i class="fa fa-upload"></i> Upload File</h3>
                  </div>
                  <div class="panel-body">
                     <form id="form_import" enctype="multipart/form-data">
                        <div class="form-group">
                           <label for="file_excel">Pilih File Excel</label>
                           <input type="file" name="file_excel" id="file_excel" accept=".xlsx,.xls,.xlsm" required>
                           <p class="help-block">Format: .xlsx, .xls, atau .xlsm. Sheet harus bernama "Lembar2" atau sheet ke-2.</p>
                        </div>

                        <div class="message"></div>

                        <button type="submit" class="btn btn-primary" id="btn_import">
                           <i class="fa fa-upload"></i> Import Sekarang
                        </button>
                        <a class="btn btn-default" href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">
                           <i class="fa fa-undo"></i> Batal
                        </a>
                     </form>
                  </div>
               </div>

               <!-- Result -->
               <div id="import_result" style="display:none">
                  <div class="panel panel-success">
                     <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-check-circle"></i> Hasil Import</h3>
                     </div>
                     <div class="panel-body">
                        <div id="result_message"></div>
                        <div id="result_details" style="margin-top:10px;max-height:300px;overflow-y:auto"></div>
                        <div id="result_errors" style="margin-top:10px"></div>
                        <div style="margin-top:15px">
                           <a class="btn btn-success" href="<?= site_url('administrator/mapel_alokasi_smp/quick_generate_preview'); ?>">
                              <i class="fa fa-magic"></i> Quick Generate Sekarang
                           </a>
                           <a class="btn btn-default" href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">
                              <i class="fa fa-list"></i> Lihat Alokasi
                           </a>
                        </div>
                     </div>
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
    
    $('#form_import').on('submit', function(e){
        e.preventDefault();
        
        var form = $(this);
        var formData = new FormData(this);
        
        // Validate file
        var fileInput = $('#file_excel')[0];
        if (!fileInput.files || !fileInput.files[0]) {
            swal('Error', 'Pilih file terlebih dahulu', 'error');
            return false;
        }
        
        // Disable button
        $('#btn_import').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Mengimport...');
        $('.message').fadeOut().empty();
        $('#import_result').hide();
        
        $.ajax({
            url: BASE_URL + '/administrator/mapel_alokasi_smp/import_excel_process',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: 60000,
            success: function(res) {
                if (res.success) {
                    // Show success
                    $('.message').html('<div class="alert alert-success"><i class="fa fa-check"></i> ' + res.message + '</div>').fadeIn();
                    
                    // Show details
                    var detailsHtml = '<ul class="list-group">';
                    if (res.details && res.details.length > 0) {
                        res.details.forEach(function(d){
                            detailsHtml += '<li class="list-group-item"><i class="fa fa-check text-success"></i> ' + d + '</li>';
                        });
                    }
                    detailsHtml += '</ul>';
                    $('#result_details').html(detailsHtml);
                    
                    // Show errors if any
                    if (res.errors && res.errors.length > 0) {
                        var errorsHtml = '<div class="alert alert-warning"><strong>Warning:</strong><ul style="margin-bottom:0">';
                        res.errors.forEach(function(e){
                            errorsHtml += '<li>' + e + '</li>';
                        });
                        errorsHtml += '</ul></div>';
                        $('#result_errors').html(errorsHtml);
                    }
                    
                    $('#import_result').fadeIn();
                    
                } else {
                    $('.message').html('<div class="alert alert-danger"><i class="fa fa-times"></i> ' + res.message + '</div>').fadeIn();
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('.message').html('<div class="alert alert-danger"><i class="fa fa-times"></i> ' + errorMsg + '</div>').fadeIn();
            },
            complete: function() {
                $('#btn_import').prop('disabled', false).html('<i class="fa fa-upload"></i> Import Sekarang');
                $('html, body').animate({ scrollTop: $('.message').offset().top - 100 }, 500);
            }
        });
        
        return false;
    });
    
});
</script>
