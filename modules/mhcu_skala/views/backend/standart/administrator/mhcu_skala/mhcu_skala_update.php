<section class="content-header">
   <h1>Skala MHCU<small>Edit + Opsi Jawaban</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mhcu_skala'); ?>">Skala MHCU</a></li>
      <li class="active">Edit</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <!-- Form Edit Skala -->
      <div class="col-md-5">
         <div class="box box-warning">
            <div class="box-header with-border"><h3 class="box-title">Data Skala</h3></div>
            <div class="box-body">
               <form id="form_mhcu_skala" action="<?= base_url('administrator/mhcu_skala/edit_save/' . $mhcu_skala->id_skala); ?>" method="POST">
                  <div class="form-group">
                     <label>Kode Skala</label>
                     <input type="text" class="form-control" name="kode_skala" value="<?= _ent($mhcu_skala->kode_skala); ?>">
                     <?= form_error('kode_skala'); ?>
                  </div>
                  <div class="form-group">
                     <label>Nama Skala</label>
                     <input type="text" class="form-control" name="nama_skala" value="<?= _ent($mhcu_skala->nama_skala); ?>">
                     <?= form_error('nama_skala'); ?>
                  </div>
                  <div class="form-group">
                     <button type="submit" class="btn btn-flat btn-success" name="save_type" value="save"><i class="fa fa-floppy-o"></i> Simpan</button>
                     <button type="submit" class="btn btn-flat btn-info" name="save_type" value="stay"><i class="fa fa-floppy-o"></i> Simpan & Lanjut</button>
                     <a href="<?= site_url('administrator/mhcu_skala'); ?>" class="btn btn-flat btn-default">Kembali</a>
                  </div>
               </form>
            </div>
         </div>
      </div>

      <!-- Nested: Opsi Skala -->
      <div class="col-md-7">
         <div class="box box-info">
            <div class="box-header with-border">
               <h3 class="box-title">Opsi Jawaban</h3>
            </div>
            <div class="box-body">
               <table class="table table-bordered table-striped" id="table_options">
                  <thead>
                     <tr>
                        <th width="40">No Urut</th>
                        <th>Label Opsi</th>
                        <th width="80">Skor</th>
                        <th width="80">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                  <?php foreach($skala_options as $opt): ?>
                     <tr id="opt_row_<?= $opt->id_skala_option; ?>">
                        <td><?= $opt->no_urut_option; ?></td>
                        <td><?= _ent($opt->label_option); ?></td>
                        <td><?= $opt->skor_value; ?></td>
                        <td>
                           <a href="javascript:void(0);" class="btn btn-xs btn-warning btn-edit-opt" data-id="<?= $opt->id_skala_option; ?>" data-label="<?= _ent($opt->label_option); ?>" data-skor="<?= $opt->skor_value; ?>" data-urut="<?= $opt->no_urut_option; ?>"><i class="fa fa-edit"></i></a>
                           <a href="javascript:void(0);" class="btn btn-xs btn-danger btn-del-opt" data-id="<?= $opt->id_skala_option; ?>"><i class="fa fa-close"></i></a>
                        </td>
                     </tr>
                  <?php endforeach; ?>
                  <?php if(count($skala_options) == 0): ?>
                     <tr id="no_data"><td colspan="4">Belum ada opsi</td></tr>
                  <?php endif; ?>
                  </tbody>
               </table>

               <hr>
               <h4>Tambah Opsi Baru</h4>
               <form id="form_add_option" action="<?= base_url('administrator/mhcu_skala/add_option/' . $mhcu_skala->id_skala); ?>" method="POST">
                  <div class="row">
                     <div class="col-md-3">
                        <input type="number" class="form-control" name="no_urut_option" placeholder="No Urut" required>
                     </div>
                     <div class="col-md-5">
                        <input type="text" class="form-control" name="label_option" placeholder="Label Opsi" required>
                     </div>
                     <div class="col-md-2">
                        <input type="number" class="form-control" name="skor_value" placeholder="Skor" required>
                     </div>
                     <div class="col-md-2">
                        <button type="submit" class="btn btn-flat btn-success"><i class="fa fa-plus"></i> Tambah</button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Modal Edit Opsi -->
<div class="modal fade" id="modalEditOption" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            <h4 class="modal-title">Edit Opsi</h4>
         </div>
         <form id="form_edit_option" method="POST">
            <div class="modal-body">
               <input type="hidden" name="id_option" id="edit_id_option">
               <div class="form-group">
                  <label>No Urut</label>
                  <input type="number" class="form-control" name="no_urut_option" id="edit_no_urut" required>
               </div>
               <div class="form-group">
                  <label>Label Opsi</label>
                  <input type="text" class="form-control" name="label_option" id="edit_label" required>
               </div>
               <div class="form-group">
                  <label>Skor</label>
                  <input type="number" class="form-control" name="skor_value" id="edit_skor" required>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-success">Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){
   // Save skala
   $('#form_mhcu_skala').submit(function(e){
      e.preventDefault();
      var form = $(this);
      $.ajax({ url: form.attr('action'), type: 'POST', data: form.serialize(), dataType: 'json',
         success: function(r){
            if(r.success){ if(r.redirect){ window.location.href = r.redirect; } else { toastr.success(r.message); } }
            else { if(r.errors){ $.each(r.errors, function(k,v){ toastr.error(v); }); } else { toastr.error(r.message); } }
         }
      });
   });

   // Add option
   $('#form_add_option').submit(function(e){
      e.preventDefault();
      var form = $(this);
      $.ajax({ url: form.attr('action'), type: 'POST', data: form.serialize(), dataType: 'json',
         success: function(r){
            if(r.success){ toastr.success(r.message); location.reload(); }
            else { toastr.error(r.message); }
         }
      });
   });

   // Edit option
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
      var url = '<?= base_url('administrator/mhcu_skala/edit_option/' . $mhcu_skala->id_skala); ?>/' + id_opt;
      $.ajax({ url: url, type: 'POST', data: $(this).serialize(), dataType: 'json',
         success: function(r){
            if(r.success){ toastr.success(r.message); $('#modalEditOption').modal('hide'); location.reload(); }
            else { toastr.error(r.message); }
         }
      });
   });

   // Delete option
   $('.btn-del-opt').click(function(){
      var id = $(this).data('id');
      swal({title: "Hapus opsi?", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "Ya, Hapus", cancelButtonText: "Batal"},
      function(isConfirm){
         if(isConfirm){
            $.ajax({ url: '<?= base_url('administrator/mhcu_skala/delete_option'); ?>/' + id, type: 'POST', dataType: 'json',
               success: function(r){ if(r.success){ toastr.success(r.message); location.reload(); } else { toastr.error(r.message); } }
            });
         }
      });
   });
});
</script>
