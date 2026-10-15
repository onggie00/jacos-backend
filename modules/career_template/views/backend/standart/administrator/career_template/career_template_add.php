<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Master Persyaratan <small>tambah</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a href="<?= site_url('administrator/career_template'); ?>">Master Persyaratan</a></li>
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
                     <h3 class="widget-user-username">Master Persyaratan</h3>
                     <h5 class="widget-user-desc">Tambah template persyaratan</h5>
                     <hr>
                  </div>
                  <?= form_open(site_url('administrator/career_template/add_save'), ['name' => 'form_career_template', 'class' => 'form-horizontal', 'id' => 'form_career_template', 'method' => 'POST']); ?>
                  <?= career_csrf_field(); ?>
                  <div class="form-group">
                     <label for="name" class="col-sm-2 control-label">Nama <i class="required">*</i></label>
                     <div class="col-sm-8">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Contoh: Ijazah SD" value="<?= set_value('name'); ?>" maxlength="200">
                        <small class="info help-block">Wajib, maks 200 karakter. Nilai disalin saat ditambahkan ke lowongan.</small>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="description" class="col-sm-2 control-label">Deskripsi</label>
                     <div class="col-sm-8">
                        <textarea class="form-control" name="description" id="description" rows="4" maxlength="2000"><?= set_value('description'); ?></textarea>
                        <small class="info help-block">Opsional, maks 2000 karakter. Teks murni (tanpa HTML/script).</small>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-sm-2 control-label">Status</label>
                     <div class="col-sm-8">
                        <label class="radio-inline"><input type="radio" name="is_active" value="1" checked> Aktif</label>
                        <label class="radio-inline"><input type="radio" name="is_active" value="0"> Nonaktif</label>
                     </div>
                  </div>
                  <div class="message"></div>
                  <div class="row-fluid col-md-7 container-button-bottom">
                     <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="Simpan (Ctrl+s)">
                        <i class="fa fa-save"></i> Simpan
                     </button>
                     <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save_back" data-stype='back' title="Simpan & kembali">
                        <i class="ion ion-ios-list-outline"></i> Simpan & kembali
                     </a>
                     <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="<?= cclang('cancel_button'); ?> (Ctrl+x)">
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
