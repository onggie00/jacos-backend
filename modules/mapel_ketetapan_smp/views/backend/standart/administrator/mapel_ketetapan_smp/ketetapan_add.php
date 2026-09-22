<script type="text/javascript">
</script>

<!-- Content Header -->
<section class="content-header">
   <h1>
      Tambah Ketetapan <small>Aturan khusus untuk mapel</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mapel_ketetapan_smp'); ?>">Ketetapan</a></li>
      <li class="active">Tambah</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-plus"></i> Form Tambah Ketetapan
               </h3>
            </div>
            <div class="box-body">
               <?= form_open('', array(
                   'name' => 'form_ketetapan',
                   'class' => 'form-horizontal',
                   'id' => 'form_ketetapan',
                   'method' => 'POST'
               )); ?>

               <!-- Kode Mapel -->
               <div class="form-group">
                  <label for="kode_mapel" class="col-sm-2 control-label">Kode Mapel <i class="required">*</i></label>
                  <div class="col-sm-6">
                     <select class="form-control chosen chosen-select" name="kode_mapel" id="kode_mapel" required>
                        <option value="">-- Pilih Mapel --</option>
                        <?php foreach($mapel_list as $mapel): ?>
                        <option value="<?= $mapel->kode_mapel; ?>"><?= $mapel->kode_mapel; ?> - <?= $mapel->nama_mapel; ?></option>
                        <?php endforeach; ?>
                     </select>
                     <small class="info help-block">Pilih kode mapel yang akan diberi ketetapan</small>
                  </div>
               </div>

               <!-- Tipe Ketetapan -->
               <div class="form-group">
                  <label for="tipe_ketetapan" class="col-sm-2 control-label">Tipe Ketetapan <i class="required">*</i></label>
                  <div class="col-sm-6">
                     <select class="form-control" name="tipe_ketetapan" id="tipe_ketetapan" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="hari">Hari (batasi hari tertentu)</option>
                        <option value="jam">Jam (batasi jam_ke tertentu)</option>
                     </select>
                     <small class="info help-block">Tipe pembatasan jadwal</small>
                  </div>
               </div>

               <!-- Nilai Hari -->
               <div class="form-group" id="group_hari" style="display:none">
                  <label class="col-sm-2 control-label">Pilih Hari <i class="required">*</i></label>
                  <div class="col-sm-6">
                     <?php foreach($hari_list as $hari): ?>
                     <label class="checkbox-inline">
                        <input type="checkbox" name="nilai[]" value="<?= $hari->id_hari; ?>"> <?= $hari->hari; ?>
                     </label>
                     <?php endforeach; ?>
                     <small class="info help-block">Pilih hari yang DIPERBOLEHKAN untuk mapel ini</small>
                  </div>
               </div>

               <!-- Nilai Jam -->
               <div class="form-group" id="group_jam" style="display:none">
                  <label class="col-sm-2 control-label">Pilih Jam Ke <i class="required">*</i></label>
                  <div class="col-sm-6">
                     <?php foreach($jam_list as $jam): ?>
                     <label class="checkbox-inline">
                        <input type="checkbox" name="nilai[]" value="<?= $jam->jam_ke; ?>"> Jam ke <?= $jam->jam_ke; ?>
                     </label>
                     <?php endforeach; ?>
                     <small class="info help-block">Pilih jam_ke yang DIPERBOLEHKAN untuk mapel ini</small>
                  </div>
               </div>

               <!-- Keterangan -->
               <div class="form-group">
                  <label for="keterangan" class="col-sm-2 control-label">Keterangan</label>
                  <div class="col-sm-6">
                     <input type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Opsional">
                     <small class="info help-block">Catatan tambahan (opsional)</small>
                  </div>
               </div>

               <!-- Buttons -->
               <div class="message"></div>
               <div class="row-fluid col-md-7 container-button-bottom">
                  <button type="submit" class="btn btn-flat btn-primary" id="btn_save">
                     <i class="fa fa-save"></i> Simpan
                  </button>
                  <a class="btn btn-flat btn-default" id="btn_cancel" href="<?= site_url('administrator/mapel_ketetapan_smp'); ?>">
                     <i class="fa fa-undo"></i> Batal
                  </a>
                  <span class="loading loading-hide">
                     <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg"> 
                     <i>Menyimpan...</i>
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

   // Show/hide based on tipe
   $('#tipe_ketetapan').change(function(){
      var tipe = $(this).val();
      $('#group_hari').hide();
      $('#group_jam').hide();
      
      if (tipe == 'hari') {
         $('#group_hari').show();
      } else if (tipe == 'jam') {
         $('#group_jam').show();
      }
   });

   // Form submit
   $('#form_ketetapan').on('submit', function(e){
      e.preventDefault();
      
      var form = $(this);
      var url = BASE_URL + '/administrator/mapel_ketetapan_smp/add_save';
      var data = form.serialize();
      
      // Validate at least one checkbox checked
      var checked = form.find('input[name="nilai[]"]:checked');
      if (checked.length == 0) {
         swal("Upss", "Pilih minimal satu nilai", "warning");
         return false;
      }
      
      $('.loading').show();
      $('.message').fadeOut();
      
      $.ajax({
         url: url,
         type: 'POST',
         dataType: 'json',
         data: data,
         success: function(res) {
            if (res.success) {
               toastr.success(res.message);
               if (res.redirect) {
                  setTimeout(function(){
                     window.location.href = res.redirect;
                  }, 1000);
               }
            } else {
               if (res.errors) {
                  var errorHtml = '<div class="alert alert-danger"><ul>';
                  $.each(res.errors, function(key, val){
                     errorHtml += '<li>' + val + '</li>';
                  });
                  errorHtml += '</ul></div>';
                  $('.message').html(errorHtml);
               } else {
                  $('.message').html('<div class="alert alert-danger">' + res.message + '</div>');
               }
               $('.message').fadeIn();
            }
         },
         error: function() {
            $('.message').html('<div class="alert alert-danger">Terjadi kesalahan</div>');
            $('.message').fadeIn();
         },
         complete: function() {
            $('.loading').hide();
         }
      });
      
      return false;
   });

   // Cancel
   $('#btn_cancel').click(function(){
      swal({
         title: "<?= cclang('are_you_sure'); ?>",
         text: "Perubahan yang belum disimpan akan hilang",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Ya, Keluar!",
         cancelButtonText: "Tetap di Sini",
         closeOnConfirm: true,
         closeOnCancel: true
      }, function(isConfirm){
         if (isConfirm) {
            window.location.href = BASE_URL + '/administrator/mapel_ketetapan_smp';
         }
      });
      return false;
   });

});
</script>
