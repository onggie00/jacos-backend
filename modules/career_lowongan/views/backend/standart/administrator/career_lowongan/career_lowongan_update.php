<!-- Muat bundle desain labs-ui -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Lowongan Kerja <small>ubah &amp; detail</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a href="<?= site_url('administrator/career_lowongan'); ?>">Lowongan Kerja</a></li>
      <li class="active">Ubah</li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">
            <div class="labs-card__header">
               <div class="labs-card__title"><i class="fa fa-edit"></i> Lowongan #<?= (int) $lowongan->id_job_posting; ?> <small><?= html_escape($lowongan->title); ?></small></div>
               <div class="labs-row-actions" id="btn_terbit_area">
                  <?php if ((int) $lowongan->is_published === 1): ?>
                     <span class="labs-badge labs-badge--success"><span class="dot"></span>Status: terbit</span>
                  <?php else: ?>
                     <span class="labs-badge labs-badge--default"><span class="dot"></span>Status: draf</span>
                  <?php endif; ?>
                  <?php if ((int) $lowongan->is_published === 0 && $this->aauth->is_allowed('career_lowongan_update')): ?>
                     <button class="labs-btn labs-btn--success" id="btn_terbitkan"><i class="fa fa-bullhorn"></i> Terbitkan</button>
                  <?php elseif ($this->aauth->is_allowed('career_lowongan_update')): ?>
                     <button class="labs-btn labs-btn--warning" id="btn_tutup"><i class="fa fa-life-ring"></i> Tutup (jadi draf)</button>
                  <?php endif; ?>
               </div>
            </div>
            <div class="labs-card__body">
               <?php if ($punya_lamaran): ?>
                  <div class="alert alert-warning">
                     Lowongan ini <b>sudah memiliki lamaran</b>. Judul/tanggal tetap bisa diubah, tapi:
                     <ul style="margin-bottom:0">
                        <li>Lowongan tidak dapat dihapus — cukup tutup lewat tanggal tutup atau tombol &ldquo;Tutup&rdquo; (unpublikasi).</li>
                        <li>Persyaratan yang <b>sudah dipakai lamaran</b> terkunci per baris (D12): nama/format/ukuran masih boleh diubah, tipe isian, wajib/opsional, dan hapus tidak boleh.</li>
                        <li>Menambah persyaratan <b>WAJIB</b> baru perlu konfirmasi eksplisit (lamaran yang sudah masuk bisa jadi kekurangan syarat).</li>
                     </ul>
                  </div>
               <?php endif; ?>

               <!-- HIGH #4: action eksplisit ke edit_save/$id -->
               <?= form_open(site_url('administrator/career_lowongan/edit_save/' . (int) $lowongan->id_job_posting), ['name' => 'form_career_lowongan', 'class' => 'form-horizontal', 'id' => 'form_career_lowongan', 'method' => 'POST']); ?>
               <?= career_csrf_field(); ?>
                  <div class="labs-form-group">
                     <label for="title" class="labs-form-label">Judul <i class="required">*</i></label>
                     <div class="col-sm-8 col-xs-12">
                        <input type="text" class="form-control" name="title" id="title" value="<?= set_value('title', $lowongan->title); ?>" maxlength="200">
                        <small class="info help-block">Wajib, maks 200 karakter.</small>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <label for="description" class="labs-form-label">Deskripsi</label>
                     <div class="col-sm-8 col-xs-12">
                        <textarea class="form-control" name="description" id="description" rows="6"><?= set_value('description', $lowongan->description); ?></textarea>
                        <small class="info help-block">Teks murni (di-escape saat tampil ke kandidat), maks 10.000 karakter.</small>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <span class="labs-form-label">Periode <i class="required">*</i></span>
                     <div class="row">
                        <div class="col-sm-4 col-xs-12">
                           <input type="date" class="form-control" name="opening_date" value="<?= set_value('opening_date', $lowongan->opening_date); ?>">
                           <small class="info help-block">Buka</small>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                           <input type="date" class="form-control" name="closing_date" value="<?= set_value('closing_date', $lowongan->closing_date); ?>">
                           <small class="info help-block">Tutup (tidak boleh &lt; buka)</small>
                        </div>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <label for="education_level" class="labs-form-label">Jenjang pendidikan</label>
                     <div class="col-sm-8 col-xs-12">
                        <input type="text" class="form-control" name="education_level" id="education_level" value="<?= set_value('education_level', $lowongan->education_level); ?>" maxlength="20">
                        <small class="info help-block">Teks opsional, maks 20 karakter.</small>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <span class="labs-form-label">Publikasi</span>
                     <div class="col-sm-8 col-xs-12">
                        <label class="radio-inline"><input type="radio" name="is_published" value="1" <?= set_radio('is_published', '1', (int) $lowongan->is_published === 1); ?>> Terbit</label>
                        <label class="radio-inline"><input type="radio" name="is_published" value="0" <?= set_radio('is_published', '0', (int) $lowongan->is_published === 0); ?>> Draf / unpublikasi</label>
                        <small class="info help-block">Terbit butuh min. 1 persyaratan + tanggal sah (dicek juga saat draf → terbit lewat simpan).</small>
                     </div>
                  </div>
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

               <!-- ================= Persyaratan ================= -->
               <div class="labs-modal-section labs-mt-5">
                  <div class="labs-modal-section__title"><i class="fa fa-list-ul"></i> Persyaratan lowongan</div>
                  <div class="labs-table-wrap">
                     <div class="labs-table-scroll">
                        <table class="labs-table" id="tbl_requirements">
                           <thead>
                              <tr>
                                 <th width="45">Urut</th>
                                 <th>Nama</th>
                                 <th width="12%">Wajib?</th>
                                 <th width="11%">Tipe isian</th>
                                 <th width="14%">Format</th>
                                 <th width="9%">Maks KB</th>
                                 <th width="215">Aksi</th>
                              </tr>
                           </thead>
                           <tbody>
                           <?php foreach ($requirements as $r): ?>
                              <?php $terkunci = !empty($dipakai_req[(int) $r->id_job_posting_requirement]); ?>
                              <tr data-rid="<?= (int) $r->id_job_posting_requirement; ?>">
                                 <td><?= (int) $r->display_order; ?></td>
                                 <td><?= html_escape($r->requirement_name); ?></td>
                                 <td><?= $r->requirement_type === 'required' ? 'Wajib' : 'Opsional'; ?></td>
                                 <td><?= html_escape($r->input_type); ?></td>
                                 <td><?= html_escape($r->allowed_formats !== NULL ? $r->allowed_formats : '-'); ?></td>
                                 <td><?= $r->max_file_size_kb !== NULL ? (int) $r->max_file_size_kb : '-'; ?></td>
                                 <td>
                                    <?php if ($this->aauth->is_allowed('career_lowongan_update')): ?>
                                       <a href="javascript:void(0);" class="labs-btn labs-btn--default labs-btn--xs req-edit" data-rid="<?= (int) $r->id_job_posting_requirement; ?>" data-name="<?= html_escape($r->requirement_name); ?>" data-type="<?= html_escape($r->requirement_type); ?>" data-input="<?= html_escape($r->input_type); ?>" data-formats="<?= html_escape($r->allowed_formats); ?>" data-maxkb="<?= $r->max_file_size_kb !== NULL ? (int) $r->max_file_size_kb : ''; ?>" data-terkunci="<?= $terkunci ? '1' : '0'; ?>"><i class="fa fa-edit"></i> Ubah</a>
                                       <a href="javascript:void(0);" class="labs-btn labs-btn--default labs-btn--xs req-move" data-rid="<?= (int) $r->id_job_posting_requirement; ?>" data-dir="up"><i class="fa fa-arrow-up"></i></a>
                                       <a href="javascript:void(0);" class="labs-btn labs-btn--default labs-btn--xs req-move" data-rid="<?= (int) $r->id_job_posting_requirement; ?>" data-dir="down"><i class="fa fa-arrow-down"></i></a>
                                    <?php endif; ?>
                                    <?php if (!$terkunci && $this->aauth->is_allowed('career_lowongan_update')): ?>
                                       <a href="javascript:void(0);" data-href="<?= site_url('administrator/career_lowongan/requirement_delete/' . $r->id_job_posting_requirement); ?>" class="labs-btn labs-btn--danger labs-btn--xs remove-data"><i class="fa fa-close"></i> Hapus</a>
                                    <?php elseif ($terkunci): ?>
                                       <span class="labs-badge labs-badge--info" title="Sudah dipakai lamaran — terkunci (D12)"><span class="dot"></span>Terkunci</span>
                                    <?php endif; ?>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                           <?php if (count($requirements) == 0): ?>
                              <tr class="labs-empty-row"><td colspan="7"><div class="labs-empty"><i class="fa fa-list-ul"></i><p>Belum ada persyaratan — tambah minimal satu sebelum terbit.</p></div></td></tr>
                           <?php endif; ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  <span style="display:none" id="csrf_holder"><?= career_csrf_field(); ?></span>

                  <!-- Form persyaratan baru (dari template/ad-hoc) -->
                  <div class="labs-filter-inline labs-mt-4">
                     <select class="form-control template_picker" id="req_template">
                        <option value="">-- dari template (opsional) --</option>
                        <?php foreach ($templates_aktif as $t): ?>
                           <option data-name="<?= html_escape($t->name); ?>" value="<?= (int) $t->id_requirement_template; ?>"><?= html_escape($t->name); ?></option>
                        <?php endforeach; ?>
                     </select>
                     <input type="text" class="form-control" name="requirement_name" id="req_name" placeholder="Nama persyaratan" maxlength="200">
                     <select class="form-control" name="requirement_type" id="req_type">
                        <option value="required">Wajib</option>
                        <option value="optional">Opsional</option>
                     </select>
                     <select class="form-control" name="input_type" id="req_input">
                        <option value="text">Teks</option>
                        <option value="number">Angka</option>
                        <option value="document">Dokumen</option>
                     </select>
                     <input type="text" class="form-control" name="allowed_formats" id="req_formats" placeholder="ekstensi koma" disabled>
                     <input type="number" class="form-control" name="max_file_size_kb" id="req_maxkb" min="1" max="5120" placeholder="maks KB" disabled>
                     <button class="labs-btn labs-btn--success" id="btn_req_save" type="button"><i class="fa fa-plus"></i> Tambah</button>
                  </div>
                  <div id="req_message" class="help-block labs-mt-2"></div>

                  <!-- Dialog ubah persyaratan (inline dgn form kecil) -->
                  <div class="modal fade labs-modal" id="modal_req_edit" tabindex="-1" role="dialog" aria-hidden="true">
                     <div class="modal-dialog" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title">Ubah persyaratan</h4>
                           </div>
                           <div class="modal-body">
                              <div class="alert alert-warning hidden" id="edit_lock_note">Baris ini sudah dipakai lamaran — nama/format/ukuran boleh diubah, tipe isian &amp; wajib/opsional terkunci (D12).</div>
                              <form id="form_req_edit" class="form">
                                 <?php $csrf = career_csrf_token(); ?>
                                 <div class="labs-form-group">
                                    <label for="edit_name" class="labs-form-label">Nama</label>
                                    <input type="text" class="form-control" name="requirement_name" id="edit_name" maxlength="200">
                                 </div>
                                 <div class="labs-form-group">
                                    <label for="edit_type" class="labs-form-label">Wajib/opsional</label>
                                    <select class="form-control" name="requirement_type" id="edit_type">
                                       <option value="required">Wajib</option>
                                       <option value="optional">Opsional</option>
                                    </select>
                                 </div>
                                 <div class="labs-form-group">
                                    <label for="edit_input" class="labs-form-label">Tipe isian</label>
                                    <select class="form-control" name="input_type" id="edit_input">
                                       <option value="text">Teks</option>
                                       <option value="number">Angka</option>
                                       <option value="document">Dokumen</option>
                                    </select>
                                 </div>
                                 <div class="labs-form-group">
                                    <label for="edit_formats" class="labs-form-label">Format berkas (document)</label>
                                    <input type="text" class="form-control" name="allowed_formats" id="edit_formats" placeholder="pdf,jpg">
                                 </div>
                                 <div class="labs-form-group">
                                    <label for="edit_maxkb" class="labs-form-label">Maks KB (document)</label>
                                    <input type="number" class="form-control" name="max_file_size_kb" id="edit_maxkb" min="1" max="5120">
                                 </div>
                                 <div class="help-block" id="edit_message"></div>
                              </form>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="labs-btn labs-btn--default" data-dismiss="modal">Tutup</button>
                              <button type="button" class="labs-btn labs-btn--primary" id="btn_req_edit_save">Simpan</button>
                           </div>
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
   var ID_POST = <?= (int) $lowongan->id_job_posting; ?>;
   var CSRF    = $('#csrf_holder input[name="career_csrf"]').val();
   var PUNYA   = <?= $punya_lamaran ? 'true' : 'false'; ?>;

   $('#btn_cancel').click(function(){ window.location.href = BASE_URL + 'administrator/career_lowongan'; return false; });

   // ---- Simpan head posting (action form = edit_save/$id) ----
   $('.btn_save').click(function(){
      var form = $('#form_career_lowongan');
      var data_post = form.serializeArray();
      var save_type = $(this).attr('data-stype');
      data_post.push({name: 'save_type', value: save_type});
      $('.loading').show();
      $.ajax({
         url: form.attr('action'),
         type: 'POST', dataType: 'json', data: data_post,
      })
      .done(function(res) {
         if(res.success) {
            // Selalu ke halaman ubah setelah simpan (anti duplikasi lowongan baru).
            window.location.href = BASE_URL + 'administrator/career_lowongan/edit/' + ID_POST;
         } else {
            $('.message').printMessage({message : res.message, type : 'warning'});
         }
      })
      .fail(function() { $('.message').printMessage({message : 'Error save data', type : 'warning'}); })
      .always(function() { $('.loading').hide(); });
      return false;
   });

   // ---- Terbitkan / tutup (POST + CSRF) ----
   $('#btn_terbitkan').click(function(){
      $.post(BASE_URL + 'administrator/career_lowongan/terbitkan/' + ID_POST,
         { career_csrf: CSRF },
         function(res){
            if (res.success) { window.location.href = BASE_URL + 'administrator/career_lowongan/edit/' + ID_POST; }
            else { alert(res.message || 'Gagal menerbitkan.'); }
         }, 'json').fail(function(){ alert('Error jaringan.'); });
      return false;
   });
   $('#btn_tutup').click(function(){
      $.post(BASE_URL + 'administrator/career_lowongan/tutup/' + ID_POST,
         { career_csrf: CSRF },
         function(res){
            if (res.success) { window.location.href = BASE_URL + 'administrator/career_lowongan/edit/' + ID_POST; }
            else { alert(res.message || 'Gagal menutup.'); }
         }, 'json').fail(function(){ alert('Error jaringan.'); });
      return false;
   });

   // ---- persyaratan: dari template isi nama ----
   $('#req_template').change(function(){
      var name = $(this).find('option:selected').data('name') || '';
      if (name) { $('#req_name').val(name); }
   });
   $('#req_input').change(function(){
      var is_doc = $(this).val() === 'document';
      $('#req_formats').prop('disabled', !is_doc);
      $('#req_maxkb').prop('disabled', !is_doc);
      if (is_doc && !$('#req_formats').val()) { $('#req_formats').val('pdf,jpg,jpeg,png,doc,docx'); }
   });

   function reloadPage(){
      window.location.href = BASE_URL + 'administrator/career_lowongan/edit/' + ID_POST;
   }

   // ---- tambah persyaratan (konfirmasi utk WAJIB di lowongan berlamaran) ----
   $('#btn_req_save').click(function(){
      $('#req_message').text('Menyimpan…');
      var payload = {
         career_csrf: CSRF,
         id_requirement_template: $('#req_template').val() || 0,
         requirement_name: $('#req_name').val(),
         requirement_type: $('#req_type').val(),
         input_type: $('#req_input').val(),
         allowed_formats: $('#req_formats').is(':disabled') ? '' : $('#req_formats').val(),
         max_file_size_kb: $('#req_maxkb').is(':disabled') ? '' : $('#req_maxkb').val()
      };
      $.post(BASE_URL + 'administrator/career_lowongan/requirement_add/' + ID_POST,
         payload,
         function(res){
            if (res.success) { reloadPage(); }
            else if (PUNYA && $('#req_type').val() === 'required' && confirm(res.message + '\n\nTambahkan tetap?')) {
               payload.konfirmasi = '1';
               $.post(BASE_URL + 'administrator/career_lowongan/requirement_add/' + ID_POST, payload,
                  function(r2){ if (r2.success) { reloadPage(); } else { $('#req_message').text(r2.message || 'Gagal.'); } }, 'json')
                  .fail(function(){ $('#req_message').text('Error jaringan.'); });
            }
            else { $('#req_message').text(res.message || 'Gagal.'); }
         }, 'json').fail(function(){ $('#req_message').text('Error jaringan.'); });
      return false;
   });

   // ---- ubah persyaratan (modal) — kunci per-baris, bukan global ----
   $('#tbl_requirements').on('click', '.req-edit', function(){
      var $tr = $(this).closest('tr');
      $('#edit_name').val($(this).data('name'));
      $('#edit_type').val($(this).data('type'));
      $('#edit_input').val($(this).data('input'));
      $('#edit_formats').val($(this).data('formats') || '');
      $('#edit_maxkb').val($(this).data('maxkb') || '');
      var terkunci = $(this).data('terkunci') == '1';
      $('#edit_lock_note').toggle(terkunci);
      if (PUNYA || terkunci) {
         $('#edit_type').val($tr.find('td:eq(2)').text().indexOf('Wajib') === 0 ? 'required' : 'optional');
      }
      var is_doc = $(this).data('input') === 'document';
      $('#edit_formats').prop('disabled', !is_doc);
      $('#edit_maxkb').prop('disabled', !is_doc);
      $('#edit_message').text('');
      $('#modal_req_edit').data('rid', $(this).data('rid')).modal('show');
      return false;
   });
   $('#btn_req_edit_save').click(function(){
      var rid = $('#modal_req_edit').data('rid');
      $('#edit_message').text('Menyimpan…');
      $.post(BASE_URL + 'administrator/career_lowongan/requirement_update/' + rid,
         { career_csrf: CSRF,
           requirement_name: $('#edit_name').val(),
           requirement_type: $('#edit_type').val(),
           input_type: $('#edit_input').val(),
           allowed_formats: $('#edit_formats').is(':disabled') ? '' : $('#edit_formats').val(),
           max_file_size_kb: $('#edit_maxkb').is(':disabled') ? '' : $('#edit_maxkb').val() },
         function(res){
            if (res.success) { reloadPage(); }
            else { $('#edit_message').text(res.message || 'Gagal.'); }
         }, 'json').fail(function(){ $('#edit_message').text('Error jaringan.'); });
      return false;
   });

   // ---- naik/turun persyaratan — AJAX POST + CSRF (bukan form navigasi) ----
   $('#tbl_requirements').on('click', '.req-move', function(){
      var rid = $(this).data('rid');
      var dir = $(this).data('dir');
      $.post(BASE_URL + 'administrator/career_lowongan/requirement_move/' + rid,
         { career_csrf: CSRF, dir: dir },
         function(res){
            if (res.success) { reloadPage(); }
            else { alert(res.message || 'Gagal mengubah urutan.'); }
         }, 'json').fail(function(){ alert('Error jaringan.'); });
      return false;
   });

   // ---- hapus persyaratan — konfirmasi + POST + CSRF ----
   $('#tbl_requirements').on('click', '.remove-data', function(){
      var url = $(this).data('href');
      if (!confirm('Hapus persyaratan ini?')) { return false; }
      $.post(url, { career_csrf: CSRF },
         function(res){
            if (res.success) { reloadPage(); }
            else { alert(res.message || 'Gagal menghapus.'); }
         }, 'json').fail(function(){ alert('Ditolak (403).'); });
      return false;
   });

   // ---- hapus lowongan — konfirmasi + POST + CSRF ----
   $('.btn_delete_row').on('click', function(){
      var url = $(this).data('href');
      if (!confirm('Hapus lowongan ini?')) { return false; }
      $.post(url, { career_csrf: CSRF },
         function(res){
            if (res.success) { window.location.href = BASE_URL + 'administrator/career_lowongan'; }
            else { alert(res.message || 'Gagal menghapus.'); }
         }, 'json').fail(function(){ alert('Ditolak (403).'); });
      return false;
   });
});
</script>
