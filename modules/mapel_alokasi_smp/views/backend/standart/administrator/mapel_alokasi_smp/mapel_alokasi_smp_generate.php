<style>
.generate-table th { 
    background: #f8f9fa; 
    font-weight: 600; 
    font-size: 12px; 
    vertical-align: middle;
    text-align: center;
}
.generate-table td { 
    font-size: 12px; 
    vertical-align: middle;
}
.generate-table .input-jam {
    width: 60px;
    text-align: center;
    padding: 2px 5px;
}
.guru-section {
    margin-bottom: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    overflow: hidden;
}
.guru-section-header {
    background: #3c8dbc;
    color: #fff;
    padding: 10px 15px;
    font-weight: 600;
    cursor: pointer;
    user-select: none;
}
.guru-section-header:hover {
    background: #2c6d91;
}
.guru-section-header .badge {
    background: #fff;
    color: #3c8dbc;
    margin-left: 10px;
}
.guru-section-header .toggle-icon {
    float: right;
    margin-left: 10px;
    transition: transform 0.3s;
}
.guru-section-header .toggle-icon.collapsed {
    transform: rotate(-90deg);
}
.guru-section-body {
    transition: max-height 0.3s ease-out;
    overflow: hidden;
}
.guru-section-body.collapsed {
    max-height: 0 !important;
    padding: 0;
}
.tingkatan-7 { background: #e8f5e9; }
.tingkatan-8 { background: #e3f2fd; }
.tingkatan-9 { background: #fff3e0; }
.over-quota { color: #e74c3c; font-weight: bold; }

/* Validation error highlight */
.has-error .form-control,
.has-error .input-jam {
    border-color: #dd4b39;
    box-shadow: 0 0 0 2px rgba(221, 75, 57, 0.2);
}
.error-input {
    color: #dd4b39;
    font-size: 11px;
    margin-top: 3px;
}

/* Steps in modal */
.step-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
    border-left: 4px solid #3c8dbc;
}
.step-item:last-child {
    margin-bottom: 0;
}
.step-number {
    background: #3c8dbc;
    color: #fff;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
    margin-right: 12px;
    flex-shrink: 0;
}
.step-content {
    flex: 1;
}
.step-content strong {
    display: block;
    font-size: 13px;
    margin-bottom: 3px;
}
.step-content small {
    color: #666;
    font-size: 12px;
}

/* Toolbar */
.generate-toolbar {
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 8px 15px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
</style>

<!-- Content Header -->
<section class="content-header">
   <h1>
      Generate Draft Alokasi <small>Set jam_target per kelas per mapel</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">Mapel Alokasi</a></li>
      <li class="active">Generate Draft</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-magic"></i> Generate Draft Alokasi Mapel
               </h3>
               <div class="box-tools pull-right">
                  <a class="btn btn-sm btn-default" href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">
                     <i class="fa fa-arrow-left"></i> Kembali
                  </a>
               </div>
            </div>

            <div class="box-body">
               <!-- Toolbar -->
               <div class="generate-toolbar">
                  <div>
                     <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_panduan">
                        <i class="fa fa-question-circle"></i> Panduan
                     </button>
                     <button type="button" class="btn btn-sm btn-default" id="btn_expand_all">
                        <i class="fa fa-expand"></i> Expand Semua
                     </button>
                     <button type="button" class="btn btn-sm btn-default" id="btn_collapse_all">
                        <i class="fa fa-compress"></i> Collapse Semua
                     </button>
                  </div>
                  <div>
                     <span class="text-muted">Total: <strong><?= count($guru_loads); ?></strong> guru</span>
                  </div>
               </div>

               <form id="form_generate" action="<?= base_url('administrator/mapel_alokasi_smp/save_draft'); ?>" method="POST">
               <?php
               // Group by guru
               $guru_grouped = array();
               foreach ($combinations as $c) {
                   $guru_grouped[$c['id_guru']][] = $c;
               }
               
               // Get guru load info
               $guru_load_map = array();
               if (isset($guru_loads)) {
                   foreach ($guru_loads as $gl) {
                       $guru_load_map[$gl->id_guru] = $gl;
                   }
               }
               ?>

               <?php foreach ($guru_grouped as $id_guru => $items): ?>
               <?php
                   $first = $items[0];
                   $guru_load = isset($guru_load_map[$id_guru]) ? $guru_load_map[$id_guru] : null;
                   $jam_ajar = $guru_load ? $guru_load->jam_ajar : 0;
                   $total_target = $guru_load ? $guru_load->total_jam_target : 0;
                   $sisa = $jam_ajar - $total_target;
               ?>
               <div class="guru-section" data-guru-section="<?= $id_guru; ?>">
                   <div class="guru-section-header" data-toggle="collapse" data-target="#guru-body-<?= $id_guru; ?>">
                       <i class="fa fa-chevron-down toggle-icon"></i>
                       <i class="fa fa-user"></i> 
                       <?= _ent($first['nama_lengkap']); ?>
                       <span class="label label-default"><?= _ent($first['kode_mapel']); ?></span>
                       <span class="badge">
                           Kuota: <?= $jam_ajar; ?> jam | 
                           Terpakai: <?= $total_target; ?> jam | 
                           Sisa: <span class="sisa-value"><?= $sisa; ?></span> jam
                       </span>
                   </div>
                   <div class="guru-section-body" id="guru-body-<?= $id_guru; ?>">
                       <table class="table table-bordered table-striped generate-table" style="margin-bottom:0">
                           <thead>
                               <tr>
                                   <th width="30">No</th>
                                   <th>Kelas</th>
                                   <th>Mapel</th>
                                   <th width="120">Jam/Minggu</th>
                                   <th width="100">Status</th>
                                   <th>Keterangan</th>
                               </tr>
                           </thead>
                           <tbody>
                           <?php 
                           $no = 0;
                           foreach ($items as $item): 
                               $no++;
                               $row_class = 'tingkatan-' . $item['tingkatan'];
                               $is_final = ($item['status'] == 'final');
                               $input_key = $id_guru . '_' . $item['id_kelas_smp'] . '_' . $item['id_mapel'];
                           ?>
                               <tr class="<?= $row_class; ?>" id="row-<?= $input_key; ?>">
                                   <td style="text-align:center"><?= $no; ?></td>
                                   <td style="text-align:center">
                                       <strong><?= _ent($item['kelas_label']); ?></strong>
                                   </td>
                                   <td>
                                       <?= _ent($item['nama_mapel']); ?>
                                       <input type="hidden" name="alokasi[<?= $input_key; ?>][id_guru]" value="<?= $id_guru; ?>">
                                       <input type="hidden" name="alokasi[<?= $input_key; ?>][id_kelas_smp]" value="<?= $item['id_kelas_smp']; ?>">
                                       <input type="hidden" name="alokasi[<?= $input_key; ?>][id_mapel]" value="<?= $item['id_mapel']; ?>">
                                   </td>
                                   <td style="text-align:center">
                                       <?php if ($is_final): ?>
                                           <span class="label label-success"><?= $item['jam_target']; ?></span>
                                           <input type="hidden" name="alokasi[<?= $input_key; ?>][jam_target]" value="<?= $item['jam_target']; ?>">
                                       <?php else: ?>
                                           <input type="number" 
                                                  class="form-control input-jam" 
                                                  name="alokasi[<?= $input_key; ?>][jam_target]" 
                                                  value="<?= $item['jam_target']; ?>"
                                                  min="0" 
                                                  max="10"
                                                  data-guru="<?= $id_guru; ?>"
                                                  data-jam-ajar="<?= $jam_ajar; ?>"
                                                  data-key="<?= $input_key; ?>">
                                       <?php endif; ?>
                                   </td>
                                   <td style="text-align:center">
                                       <?php if ($is_final): ?>
                                           <span class="label label-success">FINAL</span>
                                       <?php elseif ($item['status'] == 'draft'): ?>
                                           <span class="label label-warning">DRAFT</span>
                                       <?php else: ?>
                                           <span class="label label-default">NEW</span>
                                       <?php endif; ?>
                                   </td>
                                   <td>
                                       <?php if ($item['is_existing']): ?>
                                           <small class="text-muted">Sudah tersimpan</small>
                                       <?php endif; ?>
                                   </td>
                               </tr>
                           <?php endforeach; ?>
                           </tbody>
                       </table>
                   </div>
               </div>
               <?php endforeach; ?>

               <!-- Submit Buttons -->
               <div class="row" style="margin-top:20px">
                   <div class="col-md-12">
                       <div class="message"></div>
                       <div class="errors-detail" style="display:none"></div>
                       <button type="submit" class="btn btn-flat btn-primary btn_save" id="btn_save">
                           <i class="fa fa-save"></i> Simpan Draft
                       </button>
                       <a class="btn btn-flat btn-default" href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">
                           <i class="fa fa-undo"></i> Batal
                       </a>
                   </div>
               </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Modal Panduan -->
<div class="modal fade" id="modal_panduan" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header" style="background:#3c8dbc;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-question-circle"></i> Panduan Generate Jadwal Pelajaran</h4>
         </div>
         <div class="modal-body">
            <div class="step-item">
               <div class="step-number">1</div>
               <div class="step-content">
                  <strong>Isi Jam/Minggu</strong>
                  <small>Tentukan berapa jam per minggu setiap guru mengajar di setiap kelas. Isi <code>0</code> atau kosongkan jika guru tidak mengajar di kelas tersebut.</small>
               </div>
            </div>
            <div class="step-item">
               <div class="step-number">2</div>
               <div class="step-content">
                  <strong>Simpan Draft</strong>
                  <small>Klik tombol <strong>"Simpan Draft"</strong> untuk menyimpan alokasi. Data akan tersimpan dengan status <span class="label label-warning">DRAFT</span>.</small>
               </div>
            </div>
            <div class="step-item">
               <div class="step-number">3</div>
               <div class="step-content">
                  <strong>Set Final</strong>
                  <small>Buka halaman <strong>Alokasi Mapel</strong>, pilih data yang sudah benar, klik <strong>"Set Final"</strong> untuk mengunci alokasi. Status berubah jadi <span class="label label-success">FINAL</span>.</small>
               </div>
            </div>
            <div class="step-item">
               <div class="step-number">4</div>
               <div class="step-content">
                  <strong>Preview Fase 1</strong>
                  <small>Buka <strong>Fase 1</strong> untuk melihat urutan generate. Guru dengan constraint terberat diproses duluan. Periksa ketetapan hari/jam yang berlaku.</small>
               </div>
            </div>
            <div class="step-item">
               <div class="step-number">5</div>
               <div class="step-content">
                  <strong>Generate Fase 2</strong>
                  <small>Klik <strong>"Mulai Generate Fase 2"</strong> untuk menempatkan jadwal ke slot waktu. Sistem otomatis hindari bentrok kelas dan guru.</small>
               </div>
            </div>
            <div class="step-item">
               <div class="step-number">6</div>
               <div class="step-content">
                  <strong>Lihat Hasil</strong>
                  <small>Buka menu <strong>Jadwal Pelajaran</strong> untuk melihat hasil generate dalam bentuk <strong>List</strong> atau <strong>Matrix per kelas</strong>.</small>
               </div>
            </div>
            
            <hr>
            <h5><i class="fa fa-lightbulb-o"></i> Tips:</h5>
            <ul style="font-size:12px">
               <li>Klik header guru untuk <strong>collapse/expand</strong> tabel kelas</li>
               <li>Gunakan tombol <strong>"Collapse Semua"</strong> untuk tampilan lebih ringkas</li>
               <li>Perhatikan <strong>Sisa Kuota</strong> guru di badge header</li>
               <li>Field dengan error akan di-<strong>highlight merah</strong> saat simpan gagal</li>
            </ul>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-check"></i> Mengerti</button>
         </div>
      </div>
   </div>
</div>

<!-- Page script -->
<script>
$(document).ready(function(){
    
    // Toggle collapse for guru section
    $('.guru-section-header').click(function(){
        var body = $(this).next('.guru-section-body');
        var icon = $(this).find('.toggle-icon');
        
        body.slideToggle(300, function(){
            if ($(this).is(':visible')) {
                icon.removeClass('collapsed');
            } else {
                icon.addClass('collapsed');
            }
        });
    });
    
    // Expand all
    $('#btn_expand_all').click(function(){
        $('.guru-section-body').slideDown(300);
        $('.toggle-icon').removeClass('collapsed');
    });
    
    // Collapse all
    $('#btn_collapse_all').click(function(){
        $('.guru-section-body').slideUp(300);
        $('.toggle-icon').addClass('collapsed');
    });
    
    // Calculate total per guru on input change
    $('input.input-jam').on('change', function(){
        var guru_id = $(this).data('guru');
        var jam_ajar = $(this).data('jam-ajar');
        var total = 0;
        
        $('input[data-guru="'+guru_id+'"]').each(function(){
            total += parseInt($(this).val()) || 0;
        });
        
        // Update badge
        var section = $(this).closest('.guru-section');
        var sisa = jam_ajar - total;
        section.find('.sisa-value').text(sisa);
        
        if (total > jam_ajar) {
            section.find('.badge').css('color', '#e74c3c');
        } else {
            section.find('.badge').css('color', '');
        }
        
        // Remove error highlight on change
        $(this).closest('tr').removeClass('has-error');
        $(this).next('.error-input').remove();
    });
    
    // Form submit - only send non-empty entries
    $('#form_generate').on('submit', function(e){
        e.preventDefault();
        
        var form = $(this);
        var url = form.attr('action');
        
        // Collect only non-zero entries
        var postData = {};
        var count = 0;
        
        form.find('input.input-jam').each(function(){
            var val = parseInt($(this).val()) || 0;
            if (val > 0) {
                var key = $(this).data('key');
                var guru_id = $(this).data('guru');
                
                // Get related hidden inputs from the same row
                var row = $(this).closest('tr');
                var id_kelas_smp = row.find('input[name*="id_kelas_smp"]').val();
                var id_mapel = row.find('input[name*="id_mapel"]').val();
                
                postData['alokasi[' + key + '][id_guru]'] = guru_id;
                postData['alokasi[' + key + '][id_kelas_smp]'] = id_kelas_smp;
                postData['alokasi[' + key + '][id_mapel]'] = id_mapel;
                postData['alokasi[' + key + '][jam_target]'] = val;
                count++;
            }
        });
        
        // Check if any data to save
        if (count === 0) {
            var html = '<div class="alert alert-warning">';
            html += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
            html += '<i class="fa fa-exclamation-triangle"></i> Silakan isi minimal 1 jam/minggu untuk salah satu kelas.';
            html += '</div>';
            $('.message').html(html).fadeIn();
            return false;
        }
        
        // Clear previous errors
        $('.has-error').removeClass('has-error');
        $('.error-input').remove();
        $('.message').fadeOut().empty();
        
        // Disable button during submit
        $('#btn_save').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: postData,
            timeout: 30000,
            success: function(res) {
                if (res.success) {
                    var html = '<div class="alert alert-success">';
                    html += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                    html += '<i class="fa fa-check-circle"></i> <strong>' + res.message + '</strong>';
                    
                    if (res.saved_names && res.saved_names.length > 0) {
                        html += '<br><small><i class="fa fa-info-circle"></i> Data tersimpan untuk: ' + res.saved_names.join(', ') + '</small>';
                    }
                    
                    html += '</div>';
                    $('.message').html(html).fadeIn();
                    
                    if (res.redirect) {
                        setTimeout(function(){
                            window.location.href = res.redirect;
                        }, 2500);
                    }
                } else {
                    var html = '<div class="alert alert-danger">';
                    html += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                    html += '<i class="fa fa-times-circle"></i> <strong>' + res.message + '</strong>';
                    
                    if (res.errors) {
                        html += '<ul style="margin-top:10px;margin-bottom:0">';
                        if (Array.isArray(res.errors)) {
                            $.each(res.errors, function(i, val){
                                html += '<li>' + val + '</li>';
                            });
                        } else {
                            $.each(res.errors, function(key, val){
                                html += '<li>' + val + '</li>';
                            });
                        }
                        html += '</ul>';
                    }
                    
                    html += '</div>';
                    $('.message').html(html).fadeIn();
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = 'Terjadi kesalahan koneksi ke server';
                
                if (status === 'timeout') {
                    errorMsg = 'Request timeout. Silakan coba lagi.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var resp = JSON.parse(xhr.responseText);
                        if (resp.message) errorMsg = resp.message;
                    } catch(e) {
                        errorMsg = 'Server error: ' + xhr.status + ' ' + error;
                    }
                }
                
                var html = '<div class="alert alert-danger">';
                html += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                html += '<i class="fa fa-times-circle"></i> ' + errorMsg;
                html += '</div>';
                $('.message').html(html).fadeIn();
            },
            complete: function() {
                $('#btn_save').prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Draft');
                $('html, body').animate({ scrollTop: $('.message').offset().top - 100 }, 500);
            }
        });
        
        return false;
    });
    
});
</script>
