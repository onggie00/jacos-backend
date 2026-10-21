<!-- Muat bundle desain labs-ui -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<!-- Gaya tambahan khusus halaman tambah lowongan (labs-ui tidak menyediakan komponen pas) -->
<style>
   /* .career-req-row: baris persyaratan penjamah input paralel, fleksibel dan wrap rapi di layar kecil */
   .career-req-row {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
      margin-bottom: 8px;
   }
   .career-req-row .form-control {
      flex: 1 1 130px;
      min-width: 110px;
      max-width: 240px;
      height: 36px;
   }
   .career-req-row .career-req-picker {
      flex: 1 1 240px;
      max-width: 320px;
   }
   @media (max-width: 768px) {
      .career-req-row .form-control,
      .career-req-row .career-req-picker {
         max-width: none;
         flex: 1 1 calc(50% - 8px);
      }
      .career-req-row .career-req-picker {
         flex: 1 1 100%;
      }
   }
</style>

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
         <div class="labs-card">
            <div class="labs-card__header">
               <div class="labs-card__title"><i class="fa fa-plus-square-o"></i> Lowongan Kerja <small>tambah lowongan + persyaratannya</small></div>
               <a class="labs-btn labs-btn--default labs-btn--sm" href="<?= site_url('administrator/career_lowongan'); ?>"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
            <div class="labs-card__body">
               <?= form_open(site_url('administrator/career_lowongan/add_save'), ['name' => 'form_career_lowongan', 'class' => 'form-horizontal', 'id' => 'form_career_lowongan', 'method' => 'POST']); ?>
               <?= career_csrf_field(); ?>
                  <div class="labs-form-group">
                     <label for="title" class="labs-form-label">Judul <i class="required">*</i></label>
                     <div class="col-sm-8 col-xs-12">
                        <input type="text" class="form-control" name="title" id="title" value="<?= set_value('title'); ?>" maxlength="200">
                        <small class="info help-block">Wajib, maks 200 karakter.</small>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <label for="description" class="labs-form-label">Deskripsi</label>
                     <div class="col-sm-8 col-xs-12">
                        <textarea class="form-control" name="description" id="description" rows="6"><?= set_value('description'); ?></textarea>
                        <small class="info help-block">Teks murni — tampil apa adanya dan di-escape saat tampil ke kandidat (tanpa HTML).</small>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <span class="labs-form-label">Periode <i class="required">*</i></span>
                     <div class="row">
                        <div class="col-sm-4 col-xs-12">
                           <input type="date" class="form-control" name="opening_date" value="<?= set_value('opening_date'); ?>">
                           <small class="info help-block">Buka (wajib)</small>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                           <input type="date" class="form-control" name="closing_date" value="<?= set_value('closing_date'); ?>">
                           <small class="info help-block">Tutup (wajib, tidak boleh &lt; buka)</small>
                        </div>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <label for="education_level" class="labs-form-label">Jenjang pendidikan</label>
                     <div class="col-sm-8 col-xs-12">
                        <input type="text" class="form-control" name="education_level" id="education_level" value="<?= set_value('education_level'); ?>" maxlength="20">
                        <small class="info help-block">Teks opsional, maks 20 karakter (belum ada daftar tetap dari sekolah).</small>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <span class="labs-form-label">Publikasi</span>
                     <div class="col-sm-8 col-xs-12">
                        <label class="radio-inline"><input type="radio" name="is_published" value="1" <?= set_radio('is_published', '1', FALSE); ?>> Terbit (butuh min. 1 persyaratan + tanggal sah)</label>
                        <label class="radio-inline"><input type="radio" name="is_published" value="0" <?= set_radio('is_published', '0', TRUE); ?>> Draf</label>
                     </div>
                  </div>

                  <!-- Persyaratan awal (dapat diatur rinci setelah disimpan) -->
                  <div class="labs-form-group">
                     <span class="labs-form-label">Persyaratan</span>
                     <div class="col-xs-12" id="req_rows">
                        <div class="req_row career-req-row">
                           <input type="text" class="form-control" name="requirement_name[]" placeholder="Nama">
                           <select class="form-control" name="requirement_type[]">
                              <option value="required">Wajib</option>
                              <option value="optional">Opsional</option>
                           </select>
                           <select class="form-control" name="input_type[]">
                              <option value="text">Teks</option>
                              <option value="number">Angka</option>
                              <option value="document">Dokumen</option>
                           </select>
                           <input type="text" class="form-control" name="allowed_formats[]" placeholder="pdf,jpg">
                           <input type="number" class="form-control" name="max_file_size_kb[]" min="1" max="5120" placeholder="KB">
                           <select class="form-control template_picker career-req-picker">
                              <option value="">-- dari template --</option>
                              <?php foreach ($templates_aktif as $t): ?>
                                 <option data-name="<?= html_escape($t->name); ?>" value="<?= (int) $t->id_requirement_template; ?>"><?= html_escape($t->name); ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <button type="button" class="labs-btn labs-btn--default labs-btn--sm" id="btn_add_row"><i class="fa fa-plus"></i> Baris persyaratan</button>
                  </div>

                  <hr class="labs-divider">
                  <div class="message"></div>
                  <div class="row-fluid col-md-7 col-xs-12 container-button-bottom labs-flex labs-flex-gap-2 labs-mt-5">
                     <button class="labs-btn labs-btn--primary btn_save btn_action" id="btn_save" data-stype='stay' title="Simpan (Ctrl+s)">
                        <i class="fa fa-save"></i> Simpan
                     </button>
                     <a class="labs-btn labs-btn--info btn_save btn_action btn_save_back" id="btn_save_back" data-stype='back' title="Simpan &amp; kembali">
                        <i class="ion ion-ios-list-outline"></i> Simpan &amp; kembali
                     </a>
                     <a class="labs-btn labs-btn--default btn_action" id="btn_cancel" title="Batal (Ctrl+x)">
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

   // Dokumen → set formats & ukuran (input tetap ENABLED, tidak pakai
   // disabled — input disabled tidak disubmit dan menggeser index
   // parallel array di server).
   $(document).on('change', 'select[name="input_type[]"]', function(){
      var is_doc = $(this).val() === 'document';
      var $row = $(this).closest('.req_row');
      if (is_doc && !$row.find('input[name="allowed_formats[]"]').val()) {
         $row.find('input[name="allowed_formats[]"]').val('pdf,jpg,jpeg,png,doc,docx');
      }
      if (!is_doc) {
         $row.find('input[name="allowed_formats[]"]').val('');
         $row.find('input[name="max_file_size_kb[]"]').val('');
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
            // HIGH #26: selalu ke halaman ubah (hindari duplikat saat tetap di form).
            window.location.href = res.redirect;
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
