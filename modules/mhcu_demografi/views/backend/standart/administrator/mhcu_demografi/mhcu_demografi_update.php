<?php
function labs_initials_demografi_edit($name) {
    $name = trim($name);
    if ($name === '') return '?';
    $parts = preg_split('/\s+/', $name);
    if (count($parts) >= 2) {
        return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
    }
    return strtoupper(mb_substr($parts[0], 0, 2));
}
?>

<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<section class="content-header">
   <h1>
      <i class="fa fa-users"></i> Demografi MHCU
      <small class="labs-text-muted">Edit + Opsi</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mhcu_demografi'); ?>">Demografi</a></li>
      <li class="active">Edit</li>
   </ol>
</section>

<section class="content">
   <div class="row">

      <!-- FORM EDIT -->
      <div class="col-md-5">
         <div class="labs-card">
            <div class="labs-card__header">
               <h3 class="labs-card__title"><i class="fa fa-edit"></i> Data Pertanyaan</h3>
            </div>
            <div class="labs-card__body">
               <form id="form_mhcu_demografi" action="<?= base_url('administrator/mhcu_demografi/edit_save/' . $mhcu_demografi->id_demografi_pertanyaan); ?>" method="POST">
                  <div class="labs-form-group">
                     <label class="labs-form-label">No Urut</label>
                     <input type="number" class="form-control" name="no_urut" value="<?= $mhcu_demografi->no_urut; ?>">
                     <?= form_error('no_urut'); ?>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Kode</label>
                     <input type="text" class="form-control" name="demografi_kode" value="<?= _ent($mhcu_demografi->demografi_kode); ?>">
                     <?= form_error('demografi_kode'); ?>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Pertanyaan</label>
                     <input type="text" class="form-control" name="demografi_pertanyaan" value="<?= _ent($mhcu_demografi->demografi_pertanyaan); ?>">
                     <?= form_error('demografi_pertanyaan'); ?>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Tipe Input</label>
                     <select class="form-control" name="input_type">
                        <option value="radio_single_choice" <?= ($mhcu_demografi->input_type == 'radio_single_choice') ? 'selected' : ''; ?>>Radio (Single Choice)</option>
                        <option value="checkbox_multi_choice" <?= ($mhcu_demografi->input_type == 'checkbox_multi_choice') ? 'selected' : ''; ?>>Checkbox (Multi Choice)</option>
                        <option value="number" <?= ($mhcu_demografi->input_type == 'number') ? 'selected' : ''; ?>>Number</option>
                        <option value="text" <?= ($mhcu_demografi->input_type == 'text') ? 'selected' : ''; ?>>Text</option>
                     </select>
                     <?= form_error('input_type'); ?>
                  </div>
                  <div class="labs-flex labs-flex-gap-2 labs-mt-4">
                     <button type="submit" class="labs-btn labs-btn--primary" name="save_type" value="save">
                        <i class="fa fa-save"></i> Simpan
                     </button>
                     <button type="submit" class="labs-btn labs-btn--info" name="save_type" value="stay">
                        <i class="fa fa-save"></i> Simpan & Lanjut
                     </button>
                     <a href="<?= site_url('administrator/mhcu_demografi'); ?>" class="labs-btn labs-btn--default">
                        <i class="fa fa-arrow-left"></i> Kembali
                     </a>
                  </div>
               </form>
            </div>
         </div>
      </div>

      <!-- NESTED: OPSI -->
      <div class="col-md-7">
         <div class="labs-card">
            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-list"></i> Opsi Jawaban
                  <span class="labs-badge labs-badge--default labs-ml-2"><?= count($demografi_options); ?> item</span>
               </h3>
            </div>
            <div class="labs-card__body">

               <div class="labs-table-wrap">
                  <div class="labs-table-scroll">
                  <table class="labs-table">
                     <thead>
                        <tr>
                           <th width="60">No</th>
                           <th>Label</th>
                           <th width="100">Skor</th>
                           <th width="100" class="labs-cell-numeric">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php foreach($demografi_options as $opt): ?>
                        <tr id="opt_row_<?= $opt->id_demografi_option; ?>">
                           <td class="labs-cell-numeric"><?= $opt->no_urut; ?></td>
                           <td><?= _ent($opt->label_option); ?></td>
                           <td class="labs-cell-numeric"><?= _ent($opt->skor); ?></td>
                           <td class="labs-cell-numeric">
                              <div class="labs-row-actions" style="justify-content:center">
                                 <button type="button" class="labs-btn labs-btn--warning labs-btn--icon labs-btn--sm btn-edit-opt"
                                         data-id="<?= $opt->id_demografi_option; ?>"
                                         data-label="<?= _ent($opt->label_option); ?>"
                                         data-skor="<?= _ent($opt->skor); ?>"
                                         data-urut="<?= $opt->no_urut; ?>"
                                         title="Edit">
                                    <i class="fa fa-edit"></i>
                                 </button>
                                 <button type="button" class="labs-btn labs-btn--danger labs-btn--icon labs-btn--sm btn-del-opt"
                                         data-id="<?= $opt->id_demografi_option; ?>"
                                         title="Hapus">
                                    <i class="fa fa-trash"></i>
                                 </button>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if(count($demografi_options) == 0): ?>
                        <tr class="labs-empty-row">
                           <td colspan="4">
                              <div class="labs-empty">
                                 <i class="fa fa-list"></i>
                                 <p>Belum ada opsi</p>
                              </div>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>

               <hr class="labs-divider">

               <h4 class="labs-card__title labs-mb-3"><i class="fa fa-plus-circle"></i> Tambah Opsi</h4>
               <form id="form_add_option" action="<?= base_url('administrator/mhcu_demografi/add_option/' . $mhcu_demografi->id_demografi_pertanyaan); ?>" method="POST">
                  <div class="labs-form-group">
                     <div class="row" style="display:flex;gap:var(--labs-space-2);flex-wrap:wrap">
                        <div style="flex:0 0 80px">
                           <input type="number" class="form-control" name="no_urut" placeholder="No" required>
                        </div>
                        <div style="flex:1;min-width:200px">
                           <input type="text" class="form-control" name="label_option" placeholder="Label Opsi" required>
                        </div>
                        <div style="flex:0 0 120px">
                           <input type="text" class="form-control" name="skor" placeholder="Skor (opsional)">
                        </div>
                        <div style="flex:0 0 auto">
                           <button type="submit" class="labs-btn labs-btn--primary">
                              <i class="fa fa-plus"></i> Tambah
                           </button>
                        </div>
                     </div>
                  </div>
               </form>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL EDIT OPSI -->
<div class="modal fade labs-modal" id="modalEditOption" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-edit"></i> Edit Opsi</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <form id="form_edit_option" method="POST">
            <div class="modal-body">
               <div class="labs-modal-section">
                  <input type="hidden" name="id_option" id="edit_id_option">
                  <div class="labs-form-group">
                     <label class="labs-form-label">No Urut</label>
                     <input type="number" class="form-control" name="no_urut" id="edit_no_urut" required>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Label</label>
                     <input type="text" class="form-control" name="label_option" id="edit_label" required>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Skor</label>
                     <input type="text" class="form-control" name="skor" id="edit_skor">
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="labs-btn labs-btn--default" data-dismiss="modal">Batal</button>
               <button type="submit" class="labs-btn labs-btn--primary"><i class="fa fa-save"></i> Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){
   // Save utama
   $('#form_mhcu_demografi').submit(function(e){
      e.preventDefault();
      var form = $(this);
      $.ajax({
         url: form.attr('action'),
         type: 'POST',
         data: form.serialize(),
         dataType: 'json',
         success: function(r){
            if(r.success){
               if(r.redirect){ window.location.href = r.redirect; }
               else { toastr.success(r.message); }
            } else {
               if(r.errors){ $.each(r.errors, function(k,v){ toastr.error(v); }); }
               else { toastr.error(r.message); }
            }
         }
      });
   });

   // Tambah opsi
   $('#form_add_option').submit(function(e){
      e.preventDefault();
      var form = $(this);
      $.ajax({
         url: form.attr('action'),
         type: 'POST',
         data: form.serialize(),
         dataType: 'json',
         success: function(r){
            if(r.success){ toastr.success(r.message); location.reload(); }
            else { toastr.error(r.message); }
         }
      });
   });

   // Edit opsi
   $('.btn-edit-opt').click(function(){
      var btn = $(this);
      $('#edit_id_option').val(btn.data('id'));
      $('#edit_no_urut').val(btn.data('urut'));
      $('#edit_label').val(btn.data('label'));
      $('#edit_skor').val(btn.data('skor'));
      $('#modalEditOption').modal('show');
   });

   $('#form_edit_option').submit(function(e){
      e.preventDefault();
      var id_opt = $('#edit_id_option').val();
      var url = '<?= base_url('administrator/mhcu_demografi/edit_option/' . $mhcu_demografi->id_demografi_pertanyaan); ?>/' + id_opt;
      $.ajax({
         url: url,
         type: 'POST',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(r){
            if(r.success){
               toastr.success(r.message);
               $('#modalEditOption').modal('hide');
               location.reload();
            } else { toastr.error(r.message); }
         }
      });
   });

   // Hapus opsi
   $('.btn-del-opt').click(function(){
      var id = $(this).data('id');
      swal({title: "Hapus opsi?", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "Ya, Hapus", cancelButtonText: "Batal"},
      function(isConfirm){
         if(isConfirm){
            $.ajax({
               url: '<?= base_url('administrator/mhcu_demografi/delete_option'); ?>/' + id,
               type: 'POST',
               dataType: 'json',
               success: function(r){
                  if(r.success){ toastr.success(r.message); location.reload(); }
                  else { toastr.error(r.message); }
               }
            });
         }
      });
   });
});
</script>