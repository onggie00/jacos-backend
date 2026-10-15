<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Lowongan Kerja <small>tambah</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a href="<?= site_url('administrator/career_lowongan'); ?>">Lowongan Kerja</a></li>
      <li class="active">Tambah</li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-body">
               <div class="box box-widget widget-user-2">
                  <div class="widget-user-header">
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="Tambah">
                     </div>
                     <h3 class="widget-user-username">Lowongan Kerja</h3>
                     <h5 class="widget-user-desc">Tambah lowongan + persyaratannya</h5>
                     <hr>
                  </div>
                  <?= form_open('', ['name' => 'form_career_lowongan', 'class' => 'form-horizontal', 'id' => 'form_career_lowongan', 'method' => 'POST']); ?>
                  <?= career_csrf_field(); ?>
                  <div class="form-group">
                     <label for="title" class="col-sm-2 control-label">Judul <i class="required">*</i></label>
                     <div class="col-sm-8">
                        <input type="text" class="form-control" name="title" id="title" value="<?= set_value('title'); ?>" maxlength="200">
                        <small class="info help-block">Wajib, maks 200 karakter.</small>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="description" class="col-sm-2 control-label">Deskripsi</label>
                     <div class="col-sm-8">
                        <textarea class="form-control" name="description" id="description" rows="6"><?= set_value('description'); ?></textarea>
                        <small class="info help-block">Teks murni — tampil apa adanya dan di-escape saat tampil ke kandidat (tanpa HTML).</small>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-sm-2 control-label">Periode <i class="required">*</i></label>
                     <div class="col-sm-4">
                        <input type="date" class="form-control" name="opening_date" value="<?= set_value('opening_date'); ?>">
                        <small class="info help-block">Buka (wajib)</small>
                     </div>
                     <div class="col-sm-4">
                        <input type="date" class="form-control" name="closing_date" value="<?= set_value('closing_date'); ?>">
                        <small class="info help-block">Tutup (wajib, tidak boleh &lt; buka)</small>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="education_level" class="col-sm-2 control-label">Jenjang pendidikan</label>
                     <div class="col-sm-8">
                        <input type="text" class="form-control" name="education_level" id="education_level" value="<?= set_value('education_level'); ?>" maxlength="20">
                        <small class="info help-block">Teks opsional, maks 20 karakter (belum ada daftar tetap dari sekolah).</small>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-sm-2 control-label">Publikasi</label>
                     <div class="col-sm-8">
                        <label class="radio-inline"><input type="radio" name="is_published" value="1" <?= set_radio('is_published', '1', FALSE); ?>> Terbit (butuh min. 1 persyaratan + tanggal sah)</label>
                        <label class="radio-inline"><input type="radio" name="is_published" value="0" <?= set_radio('is_published', '0', TRUE); ?>> Draf</label>
                     </div>
                  </div>

                  <!-- Persyaratan awal (dapat diatur rinci setelah disimpan) -->
                  <div class="form-group">
                     <label class="col-sm-2 control-label">Persyaratan</label>
                     <div class="col-sm-10" id="req_rows">
                        <div class="req_row form-group" style="margin-bottom:6px">
                           <div class="col-sm-2" style="padding-left:0"><input type="text" class="form-control" name="requirement_name[]" placeholder="Nama"></div>
                           <div class="col-sm-2" style="padding-left:0">
                              <select class="form-control" name="requirement_type[]">
                                 <option value="required">Wajib</option>
                                 <option value="optional">Opsional</option>
                              </select>
                           </div>
                           <div class="col-sm-2" style="padding-left:0">
                              <select class="form-control" name="input_type[]">
                                 <option value="text">Teks</option>
                                 <option value="number">Angka</option>
                                 <option value="document">Dokumen</option>
                              </select>
                           </div>
                           <div class="col-sm-2" style="padding-left:0"><input type="text" class="form-control" name="allowed_formats[]" placeholder="pdf,jpg" disabled></div>
                           <div class="col-sm-1" style="padding-left:0"><input type="number" class="form-control" name="max_file_size_kb[]" min="1" max="5120" placeholder="KB" disabled></div>
                           <div class="col-sm-3">
                              <select class="form-control template_picker">
                                 <option value="">-- dari template --</option>
                                 <?php foreach ($templates_aktif as $t): ?>
                                    <option data-name="<?= html_escape($t->name); ?>" value="<?= (int) $t->id_requirement_template; ?>"><?= html_escape($t->name); ?></option>
                                 <?php endforeach; ?>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="col-sm-offset-2 col-sm-8">
                        <button type="button" class="btn btn-flat btn-default" id="btn_add_row"><i class="fa fa-plus"></i> Baris persyaratan</button>
                     </div>
                  </div>

                  <div class="message"></div>
                  <div class="row-fluid col-md-7 container-button-bottom">
                     <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="Simpan (Ctrl+s)">
                        <i class="fa fa-save"></i> Simpan
                     </button>
                     <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save_back" data-stype='back' title="Simpan & kembali">
                        <i class="ion ion-ios-list-outline"></i> Simpan &amp; kembali
                     </a>
                     <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="Batal (Ctrl+x)">
                        <i class="fa fa-undo"></i> Batal
                     </a>
                     <span class="loading loading-hide">
                        <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg">
                        <i>Menyimpan data…</i>
                     </span>
                  </div>
                  <?= form_close(); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- Page script -->
<script>
$(document).ready(function(){
   $('#btn_cancel').click(function(){ window.location.href = BASE_URL + 'administrator/career_lowongan'; return false; });

   // Pilih template → isi kolom nama.
   $(document).on('change', '.template_picker', function(){
      var name = $(this).find('option:selected').data('name') || '';
      if (name) { $(this).closest('.req_row').find('input[name="requirement_name[]"]').first().val(name); }
   });

   // Dokumen → aktifkan kolom formats & ukuran.
   $(document).on('change', 'select[name="input_type[]"]', function(){
      var is_doc = $(this).val() === 'document';
      var $row = $(this).closest('.req_row');
      $row.find('input[name="allowed_formats[]"]').prop('disabled', !is_doc);
      $row.find('input[name="max_file_size_kb[]"]').prop('disabled', !is_doc);
      if (is_doc && !$row.find('input[name="allowed_formats[]"]').val()) {
         $row.find('input[name="allowed_formats[]"]').val('pdf,jpg,jpeg,png,doc,docx');
      }
   });

   $('#btn_add_row').click(function(){
      var $last = $('#req_rows .req_row').last();
      var $clone = $last.clone();
      $clone.find('input[name="requirement_name[]"]').val('');
      $clone.find('.template_picker').val('');
      $('#req_rows').append($clone);
      return false;
   });

   $('.btn_save').click(function(){
      $('.message').fadeOut();
      var form = $('#form_career_lowongan');
      var data_post = form.serializeArray();
      var save_type = $(this).attr('data-stype');
      data_post.push({name: 'save_type', value: save_type});
      $('.loading').show();
      $.ajax({
         url: form.attr('action'),
         type: 'POST',
         dataType: 'json',
         data: data_post,
      })
      .done(function(res) {
         if(res.success) {
            if (save_type == 'back') { window.location.href = BASE_URL + 'administrator/career_lowongan/edit/' + res.id; return; }
            $('.message').printMessage({message : 'Lowongan disimpan. Lanjut atur persyaratan di halaman ubah.', type : 'success'});
         } else {
            $('.message').printMessage({message : res.message, type : 'warning'});
         }
      })
      .fail(function() {
         $('.message').printMessage({message : 'Error save data', type : 'warning'});
      })
      .always(function() { $('.loading').hide(); });
      return false;
   });
});
</script>
