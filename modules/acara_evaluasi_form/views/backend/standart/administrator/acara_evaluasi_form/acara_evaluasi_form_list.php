<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<style>
/* Dynamic row styles */
.dynamic-row {
  background: var(--labs-bg-soft);
  border: 1px solid var(--labs-border);
  border-radius: var(--labs-radius);
  padding: var(--labs-space-4);
  margin-bottom: var(--labs-space-3);
  position: relative;
}
.dynamic-row .row-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--labs-space-3);
}
.dynamic-row .row-number {
  font-weight: 600;
  color: var(--labs-text-muted);
  font-size: 13px;
}
.btn-remove-row {
  background: var(--labs-danger);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 26px;
  height: 26px;
  font-size: 14px;
  line-height: 1;
  cursor: pointer;
  transition: var(--labs-transition-fast);
}
.btn-remove-row:hover { background: #991B1B; transform: scale(1.1); }
.btn-add-row {
  border: 2px dashed var(--labs-success);
  background: transparent;
  color: var(--labs-success);
  padding: var(--labs-space-3) var(--labs-space-4);
  border-radius: var(--labs-radius);
  font-size: 13px;
  cursor: pointer;
  transition: var(--labs-transition-fast);
  width: 100%;
}
.btn-add-row:hover { background: var(--labs-success); color: #fff; }
#modal_add_multiple .modal-body { max-height: 65vh; overflow-y: auto; }
</style>

<script type="text/javascript">
</script>

<section class="content-header">
   <h1>
      <i class="fa fa-list-alt"></i> Form Evaluasi Acara <small class="labs-text-muted"><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/acara'); ?>">Acara</a></li>
      <li class="active">Form Evaluasi</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">

            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-list-alt"></i> Data Form Evaluasi Acara
                  <span class="labs-badge labs-badge--orange"><?= $acara_evaluasi_form_counts; ?> Data</span>
               </h3>
               <div>
                  <?php is_allowed('acara_evaluasi_form_add', function(){?>
                  <a href="javascript:void(0);" class="labs-btn labs-btn--success" id="btn_add_multiple">
                     <i class="fa fa-plus"></i> Tambah Form
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="labs-card__body">

               <form name="form_acara_evaluasi_form" id="form_acara_evaluasi_form" action="<?= base_url('administrator/acara_evaluasi_form/index'); ?>">
               <div class="labs-filter-inline">
                  <div class="labs-filter-inline__search">
                     <i class="fa fa-search labs-filter-inline__search-icon"></i>
                     <input type="text" class="form-control" name="q" id="filter" placeholder="Cari form evaluasi..." value="<?= $this->input->get('q'); ?>">
                  </div>
                  <select class="form-control chosen chosen-select" name="f" id="field">
                     <option value="">Semua Kolom</option>
                     <option <?= $this->input->get('f') == 'id_acara' ? 'selected' : ''; ?> value="id_acara">Acara</option>
                     <option <?= $this->input->get('f') == 'pertanyaan' ? 'selected' : ''; ?> value="pertanyaan">Pertanyaan</option>
                     <option <?= $this->input->get('f') == 'tipe_pertanyaan' ? 'selected' : ''; ?> value="tipe_pertanyaan">Tipe</option>
                     <option <?= $this->input->get('f') == 'no_urut' ? 'selected' : ''; ?> value="no_urut">No Urut</option>
                  </select>
                  <button type="submit" class="labs-btn labs-btn--primary"><i class="fa fa-search"></i> Cari</button>
                  <?php if(!empty($this->input->get('q'))): ?>
                  <a class="labs-btn labs-btn--default" href="<?= base_url('administrator/acara_evaluasi_form'); ?>"><i class="fa fa-undo"></i> Reset</a>
                  <?php endif; ?>
               </div>

               <div class="labs-table-wrap">
                  <table class="labs-table">
                     <thead>
                        <tr>
                           <th width="30"><input type="checkbox" class="flat-red" id="check_all"></th>
                           <th>Acara</th>
                           <th>No</th>
                           <th>Pertanyaan</th>
                           <th>Tipe</th>
                           <th>Wajib</th>
                           <th>Pilihan Jawaban</th>
                           <th width="240">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php if($acara_evaluasi_form_counts > 0): ?>
                     <?php foreach($acara_evaluasi_forms as $row): ?>
                        <tr>
                           <td class="labs-text-center"><input type="checkbox" class="flat-red check" name="id[]" value="<?= $row->id_evaluasi_form; ?>"></td>
                           <td>
                              <?php if($row->id_acara): ?>
                              <a href="<?= site_url('administrator/acara/view/'.$row->id_acara); ?>" class="labs-text-primary labs-fw-semi"><?= _ent($row->acara_nama_acara); ?></a>
                              <?php else: ?>
                              <span class="labs-text-light">-</span>
                              <?php endif; ?>
                           </td>
                           <td class="labs-cell-numeric"><?= _ent($row->no_urut); ?></td>
                           <td><?= _ent($row->pertanyaan); ?></td>
                           <td class="labs-text-center">
                              <?php
                              $tipe_badge = array(
                                 'text' => 'info',
                                 'textarea' => 'primary',
                                 'radio' => 'warning',
                                 'checkbox' => 'success',
                                 'selectbox' => 'default',
                              );
                              $badge_class = isset($tipe_badge[$row->tipe_pertanyaan]) ? $tipe_badge[$row->tipe_pertanyaan] : 'default';
                              ?>
                              <span class="labs-badge labs-badge--<?= $badge_class; ?>"><?= $row->tipe_pertanyaan; ?></span>
                           </td>
                           <td class="labs-text-center">
                              <?php if (!empty($row->is_required)): ?>
                                 <span class="labs-badge labs-badge--success labs-badge--solid"><i class="fa fa-check"></i> Ya</span>
                              <?php else: ?>
                                 <span class="labs-badge labs-badge--default">Tidak</span>
                              <?php endif; ?>
                           </td>
                           <td><small class="labs-text-muted"><?= _ent($row->jawaban_pertanyaan); ?></small></td>
                           <td style="white-space:nowrap">
                              <div class="labs-row-actions" style="justify-content:center">
                                 <?php is_allowed('acara_evaluasi_form_view', function() use ($row){?>
                                 <a href="<?= site_url('administrator/acara_evaluasi_form/view/'.$row->id_evaluasi_form); ?>" class="labs-btn labs-btn--info labs-btn--sm"><i class="fa fa-eye"></i> Detail</a>
                                 <?php }) ?>
                                 <?php is_allowed('acara_evaluasi_form_update', function() use ($row){?>
                                 <a href="<?= site_url('administrator/acara_evaluasi_form/edit/'.$row->id_evaluasi_form); ?>" class="labs-btn labs-btn--warning labs-btn--sm"><i class="fa fa-edit"></i> Edit</a>
                                 <?php }) ?>
                                 <?php is_allowed('acara_evaluasi_form_delete', function() use ($row){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/acara_evaluasi_form/delete/'.$row->id_evaluasi_form); ?>" class="labs-btn labs-btn--danger labs-btn--sm remove-data"><i class="fa fa-trash"></i> Hapus</a>
                                 <?php }) ?>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="8">
                              <div class="labs-empty">
                                 <i class="fa fa-inbox"></i>
                                 <p>Data form evaluasi tidak tersedia</p>
                              </div>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <div class="labs-flex-between labs-mt-4">
                  <div class="labs-flex labs-flex-gap-2" style="align-items:center">
                     <select class="form-control" name="bulk" id="bulk" style="width:180px;height:34px;font-size:12px">
                        <option value="">-- Bulk Action --</option>
                        <option value="delete">Hapus Terpilih</option>
                     </select>
                     <button type="button" class="labs-btn labs-btn--default" id="apply">Terapkan</button>
                  </div>
                  <div class="dataTables_paginate paging_simple_numbers">
                     <?= $pagination; ?>
                  </div>
               </div>
               </form>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL ADD MULTIPLE -->
<div class="modal fade labs-modal" id="modal_add_multiple" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header" style="background:linear-gradient(135deg, var(--labs-success), #166534)">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-plus-circle"></i> Tambah Form Evaluasi (Multiple)</h4>
         </div>
         <div class="modal-body">
            
            <!-- Pilih Acara -->
            <div class="form-group">
               <label>Pilih Acara <span class="labs-text-danger">*</span></label>
               <select id="modal_id_acara" class="form-control chosen chosen-select" data-placeholder="Pilih Acara...">
                  <option value="">Loading data acara...</option>
               </select>
            </div>

            <div class="labs-divider"></div>

            <!-- Dynamic Rows Container -->
            <div id="dynamic_rows_container">
               <!-- rows will be generated here -->
            </div>

            <!-- Add Row Button -->
            <button type="button" class="btn-add-row" id="btn_add_row">
               <i class="fa fa-plus"></i> Tambah Pertanyaan
            </button>

         </div>
         <div class="modal-footer">
            <div id="modal_multiple_msg" style="display:inline-block;margin-right:10px"></div>
            <button type="button" class="labs-btn labs-btn--default" data-dismiss="modal">Batal</button>
            <button type="button" class="labs-btn labs-btn--success" id="btn_save_multiple">
               <i class="fa fa-save"></i> Simpan Semua
            </button>
         </div>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){

   var rowIndex = 0;

   // Open modal on button click
   $('#btn_add_multiple').click(function(e){
      e.preventDefault();
      $('#modal_add_multiple').modal('show');
   });

   // Load acara list on modal open
   $('#modal_add_multiple').on('show.bs.modal', function(){
      rowIndex = 0;
      $('#dynamic_rows_container').empty();
      $('#modal_multiple_msg').html('');
      
      // Load acara dropdown
      $.ajax({
         url: BASE_URL + '/administrator/acara_evaluasi_form/get_acara_list',
         type: 'GET',
         dataType: 'json',
         success: function(data){
            var html = '<option value="">-- Pilih Acara --</option>';
            for(var i = 0; i < data.length; i++){
               html += '<option value="'+data[i].id_acara+'">'+data[i].nama_acara+'</option>';
            }
            $('#modal_id_acara').html(html).trigger('chosen:updated');
         }
      });

      // Add first row
      addRow();
   });

   // Add row
   $('#btn_add_row').click(function(){
      addRow();
   });

   function addRow(){
      rowIndex++;
      var html = '';
      html += '<div class="dynamic-row" data-index="'+rowIndex+'">';
      html += '  <div class="row-header">';
      html += '    <span class="row-number"><i class="fa fa-bars"></i> Pertanyaan #'+rowIndex+'</span>';
      html += '    <button type="button" class="btn-remove-row" title="Hapus baris ini"><i class="fa fa-times"></i></button>';
      html += '  </div>';
      html += '  <div class="row">';
      html += '    <div class="col-md-8">';
      html += '      <div class="form-group">';
      html += '        <label style="font-size:12px">Pertanyaan <span class="labs-text-danger">*</span></label>';
      html += '        <textarea class="form-control" name="pertanyaan[]" rows="2" placeholder="Tulis pertanyaan..." required></textarea>';
      html += '      </div>';
      html += '    </div>';
      html += '    <div class="col-md-4">';
      html += '      <div class="form-group">';
      html += '        <label style="font-size:12px">Tipe <span class="labs-text-danger">*</span></label>';
      html += '        <select class="form-control" name="tipe_pertanyaan[]">';
      html += '          <option value="text">Text</option>';
      html += '          <option value="textarea">Textarea</option>';
      html += '          <option value="radio">Radio (Pilihan)</option>';
      html += '          <option value="checkbox">Checkbox (Centang)</option>';
      html += '          <option value="selectbox">Select (Dropdown)</option>';
      html += '        </select>';
      html += '      </div>';
      html += '    </div>';
      html += '  </div>';
      html += '  <div class="row">';
      html += '    <div class="col-md-6">';
      html += '      <div class="form-group">';
      html += '        <label style="font-size:12px">Pilihan Jawaban</label>';
      html += '        <input type="text" class="form-control" name="jawaban_pertanyaan[]" placeholder="Jika multiple jawaban, pisahkan dengan |">';
      html += '        <small class="help-block labs-text-muted">Contoh: Ya|Tidak|Mungkin</small>';
      html += '      </div>';
      html += '    </div>';
      html += '    <div class="col-md-3">';
      html += '      <div class="form-group">';
      html += '        <label style="font-size:12px">Wajib?</label>';
      html += '        <select class="form-control" name="is_required[]">';
      html += '          <option value="1">Ya</option>';
      html += '          <option value="0">Tidak</option>';
      html += '        </select>';
      html += '      </div>';
      html += '    </div>';
      html += '    <div class="col-md-3">';
      html += '      <div class="form-group">';
      html += '        <label style="font-size:12px">No Urut</label>';
      html += '        <input type="number" class="form-control" name="no_urut[]" value="'+rowIndex+'" min="1">';
      html += '      </div>';
      html += '    </div>';
      html += '  </div>';
      html += '</div>';

      $('#dynamic_rows_container').append(html);

      // Scroll to new row
      var container = $('#modal_add_multiple .modal-body');
      container.animate({ scrollTop: container[0].scrollHeight }, 300);
   }

   // Remove row
   $(document).on('click', '.btn-remove-row', function(){
      var rows = $('#dynamic_rows_container .dynamic-row');
      if (rows.length <= 1) {
         swal({ title: "Minimal 1 pertanyaan diperlukan", type: "warning", timer: 1500 });
         return;
      }
      $(this).closest('.dynamic-row').fadeOut(200, function(){
         $(this).remove();
         renumberRows();
      });
   });

   function renumberRows(){
      $('#dynamic_rows_container .dynamic-row').each(function(i){
         $(this).find('.row-number').html('<i class="fa fa-bars"></i> Pertanyaan #' + (i + 1));
         $(this).find('input[name="no_urut[]"]').val(i + 1);
      });
      rowIndex = $('#dynamic_rows_container .dynamic-row').length;
   }

   // Save multiple
   $('#btn_save_multiple').click(function(){
      var id_acara = $('#modal_id_acara').val();
      var msg = $('#modal_multiple_msg');
      msg.html('');

      if (!id_acara) {
         msg.html('<span class="labs-text-danger"><i class="fa fa-exclamation-circle"></i> Pilih acara terlebih dahulu.</span>');
         return;
      }

      var rows = $('#dynamic_rows_container .dynamic-row');
      if (rows.length === 0) {
         msg.html('<span class="labs-text-danger"><i class="fa fa-exclamation-circle"></i> Tidak ada pertanyaan.</span>');
         return;
      }

      var hasEmpty = false;
      rows.each(function(){
         var val = $(this).find('textarea[name="pertanyaan[]"]').val().trim();
         if (!val) hasEmpty = true;
      });
      if (hasEmpty) {
         msg.html('<span class="labs-text-danger"><i class="fa fa-exclamation-circle"></i> Semua field pertanyaan wajib diisi.</span>');
         return;
      }

      var btn = $('#btn_save_multiple');
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

      var formData = $('#modal_add_multiple').find('select, textarea, input').serialize();

      $.ajax({
         url: BASE_URL + '/administrator/acara_evaluasi_form/add_multiple_save',
         type: 'POST',
         data: formData + '&id_acara=' + id_acara,
         dataType: 'json',
         success: function(res){
            if (res.success) {
               msg.html('<span class="labs-text-success"><i class="fa fa-check-circle"></i> ' + res.message + '</span>');
               setTimeout(function(){ window.location.reload(); }, 1500);
            } else {
               msg.html('<span class="labs-text-danger"><i class="fa fa-exclamation-circle"></i> ' + res.message + '</span>');
            }
         },
         error: function(){
            msg.html('<span class="labs-text-danger"><i class="fa fa-exclamation-circle"></i> Terjadi kesalahan server.</span>');
         },
         complete: function(){
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Semua');
         }
      });
   });

   // --- Existing scripts ---
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({
         title: "<?= cclang('are_you_sure'); ?>",
         text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
         cancelButtonText: "<?= cclang('no_cancel_plx'); ?>"
      }, function(isConfirm){
         if (isConfirm) { document.location.href = url; }
      });
      return false;
   });

   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_acara_evaluasi_form').serialize();
      if (bulk.val() == 'delete') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?= cclang('yes_delete_it'); ?>"
         }, function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/acara_evaluasi_form/delete?' + serialize_bulk;
            }
         });
         return false;
      } else if(bulk.val() == '') {
         swal({ title: "Pilih aksi terlebih dahulu", type: "warning" });
         return false;
      }
      return false;
   });

   $('#check_all').on('ifChanged', function(){
      $('input.check').iCheck($(this).is(':checked') ? 'check' : 'uncheck');
   });

});
</script>
