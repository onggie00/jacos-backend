<section class="content-header">
   <h1>Instrument MHCU<small>Edit + Item</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mhcu_instrument'); ?>">Instrument</a></li>
      <li class="active">Edit</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <!-- Form Edit Instrument -->
      <div class="col-md-4">
         <div class="box box-warning">
            <div class="box-header with-border"><h3 class="box-title">Data Instrument</h3></div>
            <div class="box-body">
               <form id="form_mhcu_instrument" action="<?= base_url('administrator/mhcu_instrument/edit_save/' . $mhcu_instrument->id_instrument); ?>" method="POST">
                  <div class="form-group">
                     <label>No Urut</label>
                     <input type="number" class="form-control" name="no_urut" value="<?= $mhcu_instrument->no_urut; ?>">
                     <?= form_error('no_urut'); ?>
                  </div>
                  <div class="form-group">
                     <label>Kode</label>
                     <input type="text" class="form-control" name="kode_instrument" value="<?= _ent($mhcu_instrument->kode_instrument); ?>">
                     <?= form_error('kode_instrument'); ?>
                  </div>
                  <div class="form-group">
                     <label>Nama</label>
                     <input type="text" class="form-control" name="nama_instrument" value="<?= _ent($mhcu_instrument->nama_instrument); ?>">
                     <?= form_error('nama_instrument'); ?>
                  </div>
                  <div class="form-group">
                     <button type="submit" class="btn btn-flat btn-success" name="save_type" value="save"><i class="fa fa-floppy-o"></i> Simpan</button>
                     <button type="submit" class="btn btn-flat btn-info" name="save_type" value="stay"><i class="fa fa-floppy-o"></i> Simpan & Lanjut</button>
                     <a href="<?= site_url('administrator/mhcu_instrument'); ?>" class="btn btn-flat btn-default">Kembali</a>
                  </div>
               </form>
            </div>
         </div>
      </div>

      <!-- Nested: Items -->
      <div class="col-md-8">
         <div class="box box-info">
            <div class="box-header with-border"><h3 class="box-title">Item Instrumen</h3></div>
            <div class="box-body">
               <table class="table table-bordered table-striped" id="table_items">
                  <thead>
                     <tr>
                        <th width="30">No</th>
                        <th>Teks Item</th>
                        <th>Dimensi</th>
                        <th>Skala</th>
                        <th width="40">Rev</th>
                        <th width="60">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                  <?php foreach($instrument_items as $item): ?>
                     <tr id="item_row_<?= $item->id_instrument_item; ?>">
                        <td><?= $item->no_urut_item; ?></td>
                        <td><?= _ent($item->text_item); ?></td>
                        <td><?= _ent($item->dimensi_aspek); ?></td>
                        <td><?= _ent($item->nama_skala); ?></td>
                        <td><?php echo ($item->is_reversed == 1) ? '<span class="label label-danger">R</span>' : '-'; ?></td>
                        <td>
                           <a href="javascript:void(0);" class="btn btn-xs btn-warning btn-edit-item" 
                              data-id="<?= $item->id_instrument_item; ?>"
                              data-urut="<?= $item->no_urut_item; ?>"
                              data-text="<?= _ent($item->text_item); ?>"
                              data-dimensi="<?= _ent($item->dimensi_aspek); ?>"
                              data-skala="<?= $item->id_skala; ?>"
                              data-type="<?= $item->input_type; ?>"
                              data-reversed="<?= $item->is_reversed; ?>"><i class="fa fa-edit"></i></a>
                           <a href="javascript:void(0);" class="btn btn-xs btn-danger btn-del-item" data-id="<?= $item->id_instrument_item; ?>"><i class="fa fa-close"></i></a>
                        </td>
                     </tr>
                  <?php endforeach; ?>
                  <?php if(count($instrument_items) == 0): ?>
                     <tr id="no_data"><td colspan="6">Belum ada item</td></tr>
                  <?php endif; ?>
                  </tbody>
               </table>

               <hr>
               <h4>Tambah Item Baru</h4>
               <form id="form_add_item" action="<?= base_url('administrator/mhcu_instrument/add_item/' . $mhcu_instrument->id_instrument); ?>" method="POST">
                  <div class="row">
                     <div class="col-md-2">
                        <input type="number" class="form-control" name="no_urut_item" placeholder="No Urut" required>
                     </div>
                     <div class="col-md-4">
                        <input type="text" class="form-control" name="text_item" placeholder="Teks soal" required>
                     </div>
                     <div class="col-md-3">
                        <input type="text" class="form-control" name="dimensi_aspek" placeholder="Dimensi/Aspek">
                     </div>
                     <div class="col-md-3">
                        <select class="form-control" name="id_skala" required>
                           <option value="">-- Pilih Skala --</option>
                           <?php foreach($all_skalas as $sk): ?>
                           <option value="<?= $sk->id_skala; ?>"><?= _ent($sk->nama_skala); ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                  </div>
                  <div class="row" style="margin-top:8px;">
                     <div class="col-md-3">
                        <select class="form-control" name="input_type" required>
                           <option value="radio_single_choice">Radio</option>
                           <option value="checkbox_multi_choice">Checkbox</option>
                           <option value="text">Text</option>
                           <option value="number">Number</option>
                        </select>
                     </div>
                     <div class="col-md-2">
                        <label><input type="checkbox" name="is_reversed" value="1"> Reversed</label>
                     </div>
                     <div class="col-md-3">
                        <button type="submit" class="btn btn-flat btn-success"><i class="fa fa-plus"></i> Tambah Item</button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Modal Edit Item -->
<div class="modal fade" id="modalEditItem" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            <h4 class="modal-title">Edit Item</h4>
         </div>
         <form id="form_edit_item" method="POST">
            <div class="modal-body">
               <input type="hidden" name="id_item" id="edit_id_item">
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>No Urut</label>
                        <input type="number" class="form-control" name="no_urut_item" id="edit_no_urut" required>
                     </div>
                  </div>
                  <div class="col-md-9">
                     <div class="form-group">
                        <label>Teks Item</label>
                        <input type="text" class="form-control" name="text_item" id="edit_text" required>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Dimensi/Aspek</label>
                        <input type="text" class="form-control" name="dimensi_aspek" id="edit_dimensi">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Skala</label>
                        <select class="form-control" name="id_skala" id="edit_skala" required>
                           <option value="">-- Pilih --</option>
                           <?php foreach($all_skalas as $sk): ?>
                           <option value="<?= $sk->id_skala; ?>"><?= _ent($sk->nama_skala); ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>Tipe</label>
                        <select class="form-control" name="input_type" id="edit_type" required>
                           <option value="radio_single_choice">Radio</option>
                           <option value="checkbox_multi_choice">Checkbox</option>
                           <option value="text">Text</option>
                           <option value="number">Number</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>&nbsp;</label><br>
                        <label><input type="checkbox" name="is_reversed" id="edit_reversed" value="1"> Reversed</label>
                     </div>
                  </div>
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
   $('#form_mhcu_instrument').submit(function(e){
      e.preventDefault();
      var form = $(this);
      $.ajax({ url: form.attr('action'), type: 'POST', data: form.serialize(), dataType: 'json',
         success: function(r){
            if(r.success){ if(r.redirect){ window.location.href = r.redirect; } else { toastr.success(r.message); } }
            else { if(r.errors){ $.each(r.errors, function(k,v){ toastr.error(v); }); } else { toastr.error(r.message); } }
         }
      });
   });

   $('#form_add_item').submit(function(e){
      e.preventDefault();
      var form = $(this);
      $.ajax({ url: form.attr('action'), type: 'POST', data: form.serialize(), dataType: 'json',
         success: function(r){ if(r.success){ toastr.success(r.message); location.reload(); } else { toastr.error(r.message); } }
      });
   });

   $('.btn-edit-item').click(function(){
      var btn = $(this);
      $('#edit_id_item').val(btn.data('id'));
      $('#edit_no_urut').val(btn.data('urut'));
      $('#edit_text').val(btn.data('text'));
      $('#edit_dimensi').val(btn.data('dimensi'));
      $('#edit_skala').val(btn.data('skala'));
      $('#edit_type').val(btn.data('type'));
      $('#edit_reversed').prop('checked', btn.data('reversed') == 1);
      $('#modalEditItem').modal('show');
   });

   $('#form_edit_item').submit(function(e){
      e.preventDefault();
      var id_item = $('#edit_id_item').val();
      var url = '<?= base_url('administrator/mhcu_instrument/edit_item/' . $mhcu_instrument->id_instrument); ?>/' + id_item;
      $.ajax({ url: url, type: 'POST', data: $(this).serialize(), dataType: 'json',
         success: function(r){ if(r.success){ toastr.success(r.message); $('#modalEditItem').modal('hide'); location.reload(); } else { toastr.error(r.message); } }
      });
   });

   $('.btn-del-item').click(function(){
      var id = $(this).data('id');
      swal({title: "Hapus item?", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "Ya, Hapus", cancelButtonText: "Batal"},
      function(isConfirm){
         if(isConfirm){
            $.ajax({ url: '<?= base_url('administrator/mhcu_instrument/delete_item'); ?>/' + id, type: 'POST', dataType: 'json',
               success: function(r){ if(r.success){ toastr.success(r.message); location.reload(); } else { toastr.error(r.message); } }
            });
         }
      });
   });
});
</script>
