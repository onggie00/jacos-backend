<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">

<section class="content-header">
   <h1>
      <i class="fa fa-calendar"></i> Periode MHCU
      <small class="labs-text-muted">Edit</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mhcu_periode'); ?>">Periode MHCU</a></li>
      <li class="active">Edit</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-8 col-md-offset-2">

         <div class="labs-card">
            <div class="labs-card__header">
               <h3 class="labs-card__title"><i class="fa fa-edit"></i> Edit Periode MHCU</h3>
               <a href="<?= site_url('administrator/mhcu_periode'); ?>" class="labs-btn labs-btn--default labs-btn--sm">
                  <i class="fa fa-arrow-left"></i> Kembali
               </a>
            </div>
            <div class="labs-card__body">
               <form id="form_mhcu_periode" action="<?= base_url('administrator/mhcu_periode/edit_save/' . $mhcu_periode->id_mhcu_periode); ?>" method="POST">

                  <div class="labs-form-group">
                     <label class="labs-form-label">Nama Periode</label>
                     <input type="text" class="form-control" name="nama_periode" value="<?= _ent($mhcu_periode->nama_periode); ?>">
                     <?= form_error('nama_periode'); ?>
                  </div>

                  <div class="row" style="display:flex;gap:var(--labs-space-3);flex-wrap:wrap">
                     <div class="labs-form-group" style="flex:1;min-width:200px">
                        <label class="labs-form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" value="<?= _ent($mhcu_periode->tanggal_mulai); ?>">
                        <?= form_error('tanggal_mulai'); ?>
                     </div>
                     <div class="labs-form-group" style="flex:1;min-width:200px">
                        <label class="labs-form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="tanggal_selesai" value="<?= _ent($mhcu_periode->tanggal_selesai); ?>">
                        <?= form_error('tanggal_selesai'); ?>
                     </div>
                  </div>

                  <div class="labs-form-group">
                     <label class="labs-form-label">Status Aktif</label>
                     <select class="form-control" name="is_active">
                        <option value="1" <?= ($mhcu_periode->is_active == 1) ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?= ($mhcu_periode->is_active == 0) ? 'selected' : ''; ?>>Nonaktif</option>
                     </select>
                     <?= form_error('is_active'); ?>
                  </div>

                  <div class="labs-form-group">
                     <label class="labs-form-label">Target Peserta (untuk Tracker Proyektor)</label>
                     <input type="number" min="0" class="form-control" name="target_peserta" value="<?= _ent(isset($mhcu_periode->target_peserta) ? $mhcu_periode->target_peserta : ''); ?>" placeholder="cth: 180">
                     <small class="text-muted">Total peserta yang ditargetkan mengikuti MHCU di periode ini. Ditampilkan di halaman tracker publik.</small>
                     <?= form_error('target_peserta'); ?>
                  </div>

                  <div class="labs-form-group">
                     <label class="labs-form-label">Tanggal Publish Hasil</label>
                     <input type="date" class="form-control" name="tanggal_publish" value="<?= _ent(isset($mhcu_periode->tanggal_publish) ? $mhcu_periode->tanggal_publish : ''); ?>">
                     <small class="text-muted">Sebelum tanggal ini, peserta tidak bisa melihat skor/hasil. Kosongkan = langsung tampil.</small>
                     <?= form_error('tanggal_publish'); ?>
                  </div>

                  <hr class="labs-divider">

                  <div class="labs-flex labs-flex-gap-2">
                     <button type="submit" class="labs-btn labs-btn--primary" name="save_type" value="save">
                        <i class="fa fa-save"></i> Simpan
                     </button>
                     <button type="submit" class="labs-btn labs-btn--info" name="save_type" value="stay">
                        <i class="fa fa-save"></i> Simpan & Lanjut
                     </button>
                     <a href="<?= site_url('administrator/mhcu_periode'); ?>" class="labs-btn labs-btn--default">
                        <i class="fa fa-times"></i> Batal
                     </a>
                  </div>

               </form>
            </div>
         </div>

      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   $('#form_mhcu_periode').submit(function(e){
      e.preventDefault();
      var form = $(this);
      $.ajax({
         url: form.attr('action'),
         type: 'POST',
         data: form.serialize(),
         dataType: 'json',
         success: function(response){
            if(response.success){
               if(response.redirect){
                  window.location.href = response.redirect;
               } else {
                  toastr.success(response.message);
               }
            } else {
               if(response.errors){
                  $.each(response.errors, function(key, val){
                     toastr.error(val);
                  });
               } else {
                  toastr.error(response.message);
               }
            }
         }
      });
   });
});
</script>