<!-- Muat bundle desain labs-ui -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Master Persyaratan <small>ubah</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a href="<?= site_url('administrator/career_template'); ?>">Master Persyaratan</a></li>
      <li class="active">Ubah</li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">
            <div class="labs-card__header">
               <div class="labs-card__title"><i class="fa fa-edit"></i> Master Persyaratan <small>ubah template #<?= (int) $tpl->id_requirement_template; ?></small></div>
               <a class="labs-btn labs-btn--default labs-btn--sm" href="<?= site_url('administrator/career_template'); ?>"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
            <div class="labs-card__body">
               <?php if ($used): ?>
                  <div class="alert alert-warning">
                     Template ini <b>sudah pernah dipakai</b> oleh baris persyaratan lowongan.
                     Menonaktifkan hanya menyembunyikannya dari daftar "tambah persyaratan lowongan" baru; lowongan yang sudah
                     memakainya tidak berubah. Menghapus akan ditolak.
                  </div>
               <?php endif; ?>
               <?= form_open(site_url('administrator/career_template/edit_save/' . (int) $tpl->id_requirement_template), ['name' => 'form_career_template', 'class' => 'form-horizontal', 'id' => 'form_career_template', 'method' => 'POST']); ?>
               <?= career_csrf_field(); ?>
                  <div class="labs-form-group">
                     <label for="name" class="labs-form-label">Nama <i class="required">*</i></label>
                     <div class="col-sm-8 col-xs-12">
                        <input type="text" class="form-control" name="name" id="name" value="<?= set_value('name', $tpl->name); ?>" maxlength="200">
                        <small class="info help-block">Wajib, maks 200 karakter.</small>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <label for="description" class="labs-form-label">Deskripsi</label>
                     <div class="col-sm-8 col-xs-12">
                        <textarea class="form-control" name="description" id="description" rows="4" maxlength="2000"><?= set_value('description', $tpl->description); ?></textarea>
                        <small class="info help-block">Opsional, maks 2000 karakter. Teks murni.</small>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <span class="labs-form-label">Status</span>
                     <div class="col-sm-8 col-xs-12">
                        <label class="radio-inline"><input type="radio" name="is_active" value="1" <?= set_radio('is_active', '1', (int) $tpl->is_active === 1); ?>> Aktif</label>
                        <label class="radio-inline"><input type="radio" name="is_active" value="0" <?= set_radio('is_active', '0', (int) $tpl->is_active === 0); ?>> Nonaktif</label>
                     </div>
                  </div>
                  <hr class="labs-divider">
                  <div class="message"></div>
                  <div class="row-fluid col-md-7 col-xs-12 container-button-bottom labs-flex labs-flex-gap-2 labs-mt-5">
                     <button class="labs-btn labs-btn--primary btn_save btn_action" id="btn_save" data-stype='stay' title="Simpan (Ctrl+s)">
                        <i class="fa fa-save"></i> Simpan
                     </button>
                     <a class="labs-btn labs-btn--info btn_save btn_action btn_save_back" id="btn_save_back" data-stype='back' title="Simpan & kembali">
                        <i class="ion ion-ios-list-outline"></i> Simpan & kembali
                     </a>
                     <a class="labs-btn labs-btn--default btn_action" id="btn_cancel" title="<?= cclang('cancel_button'); ?> (Ctrl+x)">
                        <i class="fa fa-undo"></i> <?= cclang('cancel_button'); ?>
                     </a>
                     <span class="loading loading-hide">
                        <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg">
                        <i><?= cclang('loading_saving_data'); ?></i>
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
      $('#btn_cancel').click(function(){
         window.location.href = BASE_URL + 'administrator/career_template';
         return false;
      });

      $('.btn_save').click(function(){
         $('.message').fadeOut();
         var form = $('#form_career_template');
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
               if (save_type == 'back') {
                  window.location.href = res.redirect;
                  return;
               }
               $('.message').printMessage({message : 'Template disimpan.', type : 'success'});
            } else {
               if (res.errors) { parseErrorField(res.errors); }
               $('.message').printMessage({message : res.message, type : 'warning'});
            }
         })
         .fail(function() {
            $('.message').printMessage({message : 'Error save data', type : 'warning'});
         })
         .always(function() {
            $('.loading').hide();
         });
         return false;
      });
   });
</script>
