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
         <div class="box box-warning">
            <div class="box-body">
               <div class="box box-widget widget-user-2">
                  <div class="widget-user-header">
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/update.png" alt="Ubah">
                     </div>
                     <h3 class="widget-user-username">Lowongan #<?= (int) $lowongan->id_job_posting; ?></h3>
                     <h5 class="widget-user-desc"><?= html_escape($lowongan->title); ?></h5>
                     <hr>
                  </div>
                  <?php if ($punya_lamaran): ?>
                     <div class="alert alert-warning" style="margin:0 15px 10px">
                        Lowongan ini <b>sudah memiliki lamaran</b>. Judul/tanggal tetap bisa diubah, tapi:
                        <ul style="margin-bottom:0">
                           <li>Lowongan tidak dapat dihapus — cukup tutup oleh tanggal tutup / di-unpublish.</li>
                           <li>Persyaratan yang sudah dipakai lamaran terkunci: tipe isian &amp; wajib/opsional tidak dapat diubah, baris persyaratan tidak dapat dihapus.</li>
                           <li>Menambah persyaratan baru masih memungkinkan — kandidat yang sudah submit tidak akan diminta isian baru.</li>
                        </ul>
                     </div>
                  <?php endif; ?>

                  <?= form_open('', ['name' => 'form_career_lowongan', 'class' => 'form-horizontal', 'id' => 'form_career_lowongan', 'method' => 'POST']); ?>
                  <?= career_csrf_field(); ?>
                  <?= form_hidden('id_job_posting', (int) $lowongan->id_job_posting); ?>
                  <div class="form-group">
                     <label for="title" class="col-sm-2 control-label">Judul <i class="required">*</i></label>
                     <div class="col-sm-8">
                        <input type="text" class="form-control" name="title" id="title" value="<?= set_value('title', $lowongan->title); ?>" maxlength="200">
                        <small class="info help-block">Wajib, maks 200 karakter.</small>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="description" class="col-sm-2 control-label">Deskripsi</label>
                     <div class="col-sm-8">
                        <textarea class="form-control" name="description" id="description" rows="6"><?= set_value('description', $lowongan->description); ?></textarea>
                        <small class="info help-block">Teks murni (di-escape saat tampil ke kandidat).</small>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-sm-2 control-label">Periode <i class="required">*</i></label>
                     <div class="col-sm-4">
                        <input type="date" class="form-control" name="opening_date" value="<?= set_value('opening_date', $lowongan->opening_date); ?>">
                        <small class="info help-block">Buka</small>
                     </div>
                     <div class="col-sm-4">
                        <input type="date" class="form-control" name="closing_date" value="<?= set_value('closing_date', $lowongan->closing_date); ?>">
                        <small class="info help-block">Tutup (tidak boleh &lt; buka)</small>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="education_level" class="col-sm-2 control-label">Jenjang pendidikan</label>
                     <div class="col-sm-8">
                        <input type="text" class="form-control" name="education_level" id="education_level" value="<?= set_value('education_level', $lowongan->education_level); ?>" maxlength="20">
                        <small class="info help-block">Teks opsional, maks 20 karakter.</small>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-sm-2 control-label">Publikasi</label>
                     <div class="col-sm-8">
                        <label class="radio-inline"><input type="radio" name="is_published" value="1" <?= set_radio('is_published', '1', (int) $lowongan->is_published === 1); ?>> Terbit</label>
                        <label class="radio-inline"><input type="radio" name="is_published" value="0" <?= set_radio('is_published', '0', (int) $lowongan->is_published === 0); ?>> Draf / unpublikasi</label>
                        <small class="info help-block">Terbut butuh min. 1 persyaratan + tanggal sah; lowongan yang sudah terbit tidak dapat di-unpublish melalui cek ini bila kurang?</small>
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
                     <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="<?= cclang('cancel_button'); ?> (Ctrl+x)">
                        <i class="fa fa-undo"></i> <?= cclang('cancel_button'); ?>
                     </a>
                     <span class="loading loading-hide">
                        <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg">
                        <i>Menyimpan data…</i>
                     </span>
                  </div>
                  <?= form_close(); ?>

                  <!-- ================= Persyaratan ================= -->
                  <div style="padding:0 15px 15px">
                     <h4 style="border-bottom:1px solid #eee; padding-bottom:6px">Persyaratan lowongan</h4>
                     <table class="table table-bordered table-striped" id="tbl_requirements">
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
                           <tr data-rid="<?= (int) $r->id_job_posting_requirement; ?>">
                              <td><?= (int) $r->display_order; ?></td>
                              <td><?= html_escape($r->requirement_name); ?></td>
                              <td><?= $r->requirement_type === 'required' ? 'Wajib' : 'Opsional'; ?></td>
                              <td><?= html_escape($r->input_type); ?></td>
                              <td><?= html_escape($r->allowed_formats !== NULL ? $r->allowed_formats : '-'); ?></td>
                              <td><?= $r->max_file_size_kb !== NULL ? (int) $r->max_file_size_kb : '-'; ?></td>
                              <td>
                                 <?php is_allowed('career_lowongan_update', function() use ($r){ ?>
                                    <a href="javascript:void(0);" class="label-default req-edit" data-rid="<?= (int) $r->id_job_posting_requirement; ?>" data-name="<?= html_escape($r->requirement_name); ?>" data-type="<?= html_escape($r->requirement_type); ?>" data-input="<?= html_escape($r->input_type); ?>" data-formats="<?= html_escape($r->allowed_formats); ?>" data-maxkb="<?= $r->max_file_size_kb !== NULL ? (int) $r->max_file_size_kb : ''; ?>"><i class="fa fa-edit"></i> Ubah</a>
                                 <?php }) ?>
                                 <?php is_allowed('career_lowongan_update', function() use ($r){ ?>
                                    <a href="javascript:void(0);" class="label-default req-move" data-rid="<?= (int) $r->id_job_posting_requirement; ?>" data-dir="up" <?= (int) $r->display_order === 1 ? 'style="visibility:hidden"' : ''; ?>><i class="fa fa-arrow-up"></i></a>
                                 <?php }) ?>
                                 <?php if (!$this->model_career_lowongan->requirement_dipakai($r->id_job_posting_requirement)) {
                                    is_allowed('career_lowongan_delete', function() use ($r){ ?>
                                       <a href="javascript:void(0);" data-href="<?= site_url('administrator/career_lowongan/requirement_delete/' . $r->id_job_posting_requirement); ?>" class="label-default remove-data"><i class="fa fa-close"></i> Hapus</a>
                                    <?php });
                                 } else { ?>
                                    <span class="label label-info" title="Sudah dipakai lamaran — terkunci (D12)">Terkunci</span>
                                 <?php } ?>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        <?php if (count($requirements) == 0): ?>
                           <tr><td colspan="7" class="text-center">Belum ada persyarat — tambah satu minimal sebelum terbit.</td></tr>
                        <?php endif; ?>
                        </tbody>
                     </table>

                     <!-- Form persyratan baru (dari template/ad-hoc) -->
                     <h4 style="margin-top:18px">Tambah persyaratan</h4>
                     <?= form_open('', ['name' => 'form_req', 'id' => 'form_req', 'class' => 'form-inline', 'method' => 'POST']); ?>
                     <?= career_csrf_field(); ?>
                     <div class="form-group" style="margin-bottom:8px">
                        <select class="form-control template_picker" id="req_template">
                           <option value="">-- dari template (opsional) --</option>
                           <?php foreach ($templates_aktif as $t): ?>
                              <option data-name="<?= html_escape($t->name); ?>" value="<?= (int) $t->id_requirement_template; ?>"><?= html_escape($t->name); ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="form-group" style="margin-bottom:8px">
                        <input type="text" class="form-control" name="requirement_name" id="req_name" placeholder="Nama persyratan" maxlength="200">
                     </div>
                     <div class="form-group" style="margin-bottom:8px">
                        <select class="form-control" name="requirement_type" id="req_type">
                           <option value="required">Wajib</option>
                           <option value="optional">Opsional</option>
                        </select>
                     </div>
                     <div class="form-group" style="margin-bottom:8px">
                        <select class="form-control" name="input_type" id="req_input">
                           <option value="text">Teks</option>
                           <option value="number">Angka</option>
                           <option value="document">Dokumen</option>
                        </select>
                     </div>
                     <div class="form-group" style="margin-bottom:8px">
                        <input type="text" class="form-control" name="allowed_formats" id="req_formats" placeholder="ekstensi koma" disabled>
                     </div>
                     <div class="form-group" style="margin-bottom:8px">
                        <div class="input-group">
                           <input type="number" class="form-control" name="max_file_size_kb" id="req_maxkb" min="1" max="5120" placeholder="maks KB" disabled>
                           <span class="input-group-btn">
                              <button class="btn btn-flat btn-success" id="btn_req_save" type="button"><i class="fa fa-plus"></i> Tambah</button>
                           </span>
                        </div>
                     </div>
                     <div id="req_message" class="help-block" style="margin-left:2px"></div>
                     <?= form_close(); ?>

                     <!-- Dialog ubah persyratan (inline dgn form kecil) -->
                     <div class="modal fade" id="modal_req_edit" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <button type="button" class="close" data-dismiss="modal">&times;</button>
                                 <h4 class="modal-title">Ubah persyaratan</h4>
                              </div>
                              <div class="modal-body">
                                 <?php if ($punya_lamaran): ?>
                                    <div class="alert alert-warning">Tipe isian &amp; wajib/opsional terkunci (baris sudah dipakai lamaran).</div>
                                 <?php endif; ?>
                                 <form id="form_req_edit" class="form">
                                    <?= career_csrf_field(); ?>
                                    <div class="form-group">
                                       <label>Nama</label>
                                       <input type="text" class="form-control" name="requirement_name" id="edit_name" maxlength="200">
                                    </div>
                                    <div class="form-group">
                                       <label>Wajib/opsional <?= $punya_lamaran ? '(terkunci)' : ''; ?></label>
                                       <select class="form-control" name="requirement_type" id="edit_type" <?= $punya_lamaran ? 'disabled' : ''; ?>>
                                          <option value="required">Wajib</option>
                                          <option value="optional">Opsional</option>
                                       </select>
                                    </div>
                                    <div class="form-group">
                                       <label>Tipe isian <?= $punya_lamaran ? '(terkunci)' : ''; ?></label>
                                       <select class="form-control" name="input_type" id="edit_input" <?= $punya_lamaran ? 'disabled' : ''; ?>>
                                          <option value="text">Teks</option>
                                          <option value="number">Angka</option>
                                          <option value="document">Dokumen</option>
                                       </select>
                                    </div>
                                    <div class="form-group">
                                       <label>Format berkas (document)</label>
                                       <input type="text" class="form-control" name="allowed_formats" id="edit_formats" placeholder="pdf,jpg">
                                    </div>
                                    <div class="form-group">
                                       <label>Maks KB (document)</label>
                                       <input type="number" class="form-control" name="max_file_size_kb" id="edit_maxkb" min="1" max="5120">
                                    </div>
                                    <div class="help-block" id="edit_message"></div>
                                 </form>
                              </div>
                              <div class="modal-footer">
                                 <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Tutup</button>
                                 <button type="button" class="btn btn-flat btn-primary" id="btn_req_edit_save">Simpan</button>
                              </div>
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
   var CSRF = $('input[name="career_csrf"]').first().val();

   $('#btn_cancel').click(function(){ window.location.href = BASE_URL + 'administrator/career_lowongan'; return false; });

   // ---- Simpan head posting ----
   $('.btn_save').click(function(){
      $('.message').fadeOut();
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
            if (save_type == 'back') { window.location.href = res.redirect; return; }
            $('.message').printMessage({message : 'Lowongan disimpan.', type : 'success'});
         } else {
            $('.message').printMessage({message : res.message, type : 'warning'});
         }
      })
      .fail(function() { $('.message').printMessage({message : 'Error save data', type : 'warning'}); })
      .always(function() { $('.loading').hide(); });
      return false;
   });

   // ---- persyratan: dari template isi nama ----
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

   function reloadPage(msg, type){
      $('#req_message').text('');
      window.location.href = BASE_URL + 'administrator/career_lowongan/edit/' + ID_POST;
   }

   // ---- tambah persyratan ----
   $('#btn_req_save').click(function(){
      $('#req_message').text('Menyimpan…');
      $.post(BASE_URL + 'administrator/career_lowongan/requirement_add/' + ID_POST,
         { career_csrf: CSRF,
           id_requirement_template: $('#req_template').val() || 0,
           requirement_name: $('#req_name').val(),
           requirement_type: $('#req_type').val(),
           input_type: $('#req_input').val(),
           allowed_formats: $('#req_formats').is(':disabled') ? '' : $('#req_formats').val(),
           max_file_size_kb: $('#req_maxkb').is(':disabled') ? '' : $('#req_maxkb').val() },
         function(res){
            if (res.success) { reloadPage(); }
            else { $('#req_message').text(res.message || 'Gagal.'); }
         }, 'json').fail(function(){ $('#req_message').text('Error jaringan.'); });
      return false;
   });

   // ---- ubah persyratan (modal) ----
   $('#tbl_requirements').on('click', '.req-edit', function(){
      var $tr = $(this).closest('tr');
      $('#edit_name').val($tr.find('.req-edit').data('name'));
      $('#edit_type').val($tr.find('.req-edit').data('type'));
      $('#edit_input').val($tr.find('.req-edit').data('input'));
      $('#edit_formats').val($tr.find('.req-edit').data('formats') || '');
      $('#edit_maxkb').val($tr.find('.req-edit').data('maxkb') || '');
      $('#edit_message').text('');
      var is_doc = $('#edit_input').val() === 'document';
      $('#edit_formats').prop('disabled', !is_doc);
      $('#edit_maxkb').prop('disabled', !is_doc);
      $('#modal_req_edit').modal('show');
      return false;
   });
   $('#edit_input').change(function(){
      var is_doc = $(this).val() === 'document';
      $('#edit_formats').prop('disabled', !is_doc || $('#edit_input').prop('disabled'));
      $('#edit_maxkb').prop('disabled', !is_doc || $('#edit_input').prop('disabled'));
   });
   $('#btn_req_edit_save').click(function(){
      var rid = $('#tbl_requirements tr.active-edit').data('rid');
      // pakai row yang terakhir di edit (simpan data-rid di modal itu sendiri)
      rid = $('#modal_req_edit').data('rid') || rid;
      $('#edit_message').text('Menyimpan…');
      $.post(BASE_URL + 'administrator/career_lowongan/requirement_update/' + rid,
         { career_csrf: CSRF,
           requirement_name: $('#edit_name').val(),
           requirement_type: $('#edit_input').is(':disabled') ? $('#edit_type').val() : $('#edit_type').val(),
           input_type: $('#edit_input').val(),
           allowed_formats: $('#edit_formats').is(':disabled') ? '' : $('#edit_formats').val(),
           max_file_size_kb: $('#edit_maxkb').is(':disabled') ? '' : $('#edit_maxkb').val() },
         function(res){
            if (res.success) { window.location.href = BASE_URL + 'administrator/career_lowongan/edit/' + ID_POST; }
            else { $('#edit_message').text(res.message || 'Gagal.'); }
         }, 'json').fail(function(){ $('#edit_message').text('Error jaringan.'); });
      return false;
   });
   // simpan rid di modal saat open
   $('#tbl_requirements').on('click', '.req-edit', function(){
      $('#modal_req_edit').data('rid', $(this).data('rid'));
      $('#tbl_requirements tr').removeClass('active-edit');
      $(this).closest('tr').addClass('active-edit');
   });

   // ---- naik/turun persyratan (form POST biasa, konfirmasi CSRF) ----
   $('#tbl_requirements').on('click', '.req-move', function(){
      var rid = $(this).data('rid');
      var dir = $(this).data('dir');
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = BASE_URL + 'administrator/career_lowongan/requirement_move/' + rid + '/' + dir;
      var inp = document.createElement('input');
      inp.type = 'hidden'; inp.name = 'career_csrf'; inp.value = CSRF;
      form.appendChild(inp);
      document.body.appendChild(form);
      form.submit();
      return false;
   });
});
</script>
