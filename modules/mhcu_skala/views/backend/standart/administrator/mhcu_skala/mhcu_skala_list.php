<style>
.btn-top { margin-right: 5px; border-radius: 3px; }
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; text-align: center; }
.table td { font-size: 13px; vertical-align: middle; }
.btn-aksi { margin: 2px 1px; padding: 4px 0; font-size: 11px; border-radius: 3px; width: 31%; display: inline-block; text-align: center; }
.btn-aksi i { margin-right: 3px; }
</style>

<script type="text/javascript">
</script>

<section class="content-header">
   <h1><i class="fa fa-sliders"></i> Skala Jawaban <small>Daftar Skala & Opsi</small></h1>
   <ol class="breadcrumb"><li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li><li class="active">Skala MHCU</li></ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-sliders"></i> Data Skala Jawaban MHCU
                  <span class="label bg-yellow" style="margin-left:10px"><?= $mhcu_skala_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('mhcu_skala_add', function() { ?>
                  <button type="button" class="btn btn-sm btn-top" id="btn_modal_tambah" data-toggle="modal" data-target="#modalTambah" style="background:#27ae60;color:#fff;border:none">
                     <i class="fa fa-plus"></i> Tambah Skala
                  </button>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">
               <form name="form_mhcu_skala" id="form_mhcu_skala" action="<?= base_url('administrator/mhcu_skala/index'); ?>">
               <div class="filter-builder" style="margin-bottom:15px;padding:12px;background:#f8f9fa;border-radius:5px;border:1px solid #e0e0e0">
                  <div style="display:flex;align-items:center;margin-bottom:10px">
                     <i class="fa fa-filter" style="color:#3498db;margin-right:8px"></i>
                     <strong style="color:#2c3e50;font-size:14px">Advanced Filter</strong>
                     <button type="button" class="btn btn-xs btn-success" id="btn_add_filter" style="margin-left:auto"><i class="fa fa-plus"></i> Tambah Filter</button>
                  </div>
                  <div id="filter_rows"></div>
                  <div id="filter_empty" style="text-align:center;padding:15px;color:#999">
                     <i class="fa fa-search" style="font-size:20px;margin-bottom:5px"></i><br>
                     <small>Klik "Tambah Filter" untuk menambah filter pencarian</small>
                  </div>
                  <div style="margin-top:10px;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                     <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Terapkan Filter</button>
                     <a class="btn btn-flat btn-default" href="<?= base_url('administrator/mhcu_skala'); ?>"><i class="fa fa-times"></i> Reset</a>
                     <div style="margin-left:auto;display:flex;gap:8px;align-items:center">
                        <select class="form-control input-sm" name="s" style="width:130px">
                           <option value="">Urutkan</option>
                           <option <?= $this->input->get('s') == 'kode_skala' ? 'selected' : ''; ?> value="kode_skala">Kode</option>
                           <option <?= $this->input->get('s') == 'nama_skala' ? 'selected' : ''; ?> value="nama_skala">Nama</option>
                        </select>
                        <select class="form-control input-sm" name="d" style="width:80px">
                           <option <?= $this->input->get('d') == 'asc' ? 'selected' : ''; ?> value="asc">A-Z</option>
                           <option <?= ($this->input->get('d') == 'desc' || empty($this->input->get('d'))) ? 'selected' : ''; ?> value="desc">Z-A</option>
                        </select>
                     </div>
                  </div>
               </div>

               <?php if(!empty($multi_filters)): ?>
               <div style="margin-bottom:10px;padding:8px 12px;background:#e8f4fc;border-radius:3px;border:1px solid #bee5eb">
                  <i class="fa fa-info-circle" style="color:#3498db"></i>
                  <small style="color:#2c3e50"><strong>Filter aktif:</strong>
                  <?php foreach($multi_filters as $mf): ?>
                     <span class="label label-primary" style="margin:2px;padding:3px 8px;font-size:11px"><?= htmlspecialchars($mf['field']); ?> <?= htmlspecialchars($mf['operator']); ?> "<?= htmlspecialchars($mf['value']); ?>"</span>
                  <?php endforeach; ?>
                  </small>
               </div>
               <?php endif; ?>

               <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover dataTable">
                     <thead>
                        <tr>
                           <th width="30"><input type="checkbox" class="flat-red" id="check_all"></th>
                           <th>Kode Skala</th>
                           <th>Nama Skala</th>
                           <th width="80">Jumlah Opsi</th>
                           <th width="220">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php if($mhcu_skala_counts > 0): ?>
                     <?php foreach($mhcu_skalas as $s): ?>
                        <tr>
                           <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $s->id_skala; ?>"></td>
                           <td><span class="label label-primary"><?= _ent($s->kode_skala); ?></span></td>
                           <td><?= _ent($s->nama_skala); ?></td>
                           <td style="text-align:center"><?php echo count($this->model_mhcu_skala->get_options($s->id_skala)); ?></td>
                           <td style="text-align:center">
                              <?php is_allowed('mhcu_skala_view', function() use ($s){ ?>
                              <button type="button" class="btn btn-sm btn-info btn-aksi btn_detail" data-id="<?= $s->id_skala; ?>"><i class="fa fa-eye"></i> Detail</button>
                              <?php }) ?>
                              <?php is_allowed('mhcu_skala_update', function() use ($s){ ?>
                              <a href="<?= site_url('administrator/mhcu_skala/edit/' . $s->id_skala); ?>" class="btn btn-sm btn-warning btn-aksi"><i class="fa fa-edit"></i> Edit</a>
                              <?php }) ?>
                              <?php is_allowed('mhcu_skala_delete', function() use ($s){ ?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/mhcu_skala/delete/' . $s->id_skala); ?>" class="btn btn-sm btn-danger btn-aksi remove-data"><i class="fa fa-trash"></i> Hapus</a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="5" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">Belum ada data skala</span>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <div class="row" style="margin-top:15px">
                  <div class="col-md-6">
                     <div class="input-group" style="max-width:300px">
                        <select class="form-control" name="bulk" id="bulk">
                           <option value="">-- Bulk Action --</option>
                           <option value="delete">Hapus Terpilih</option>
                        </select>
                        <span class="input-group-btn"><button type="button" class="btn btn-flat btn-default" id="apply">Terapkan</button></span>
                     </div>
                  </div>
                  <div class="col-md-6 text-right">
                     <div class="dataTables_paginate paging_simple_numbers"><?= $pagination; ?></div>
                  </div>
               </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
   <div class="modal-dialog"><div class="modal-content" style="border-radius:5px;overflow:hidden">
      <div class="modal-header" style="background:linear-gradient(135deg,#27ae60,#2ecc71);color:#fff;padding:15px 20px">
         <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8">&times;</button>
         <h4 class="modal-title" style="font-weight:600"><i class="fa fa-plus-circle"></i> Tambah Skala</h4>
      </div>
      <form id="formTambah" action="<?= base_url('administrator/mhcu_skala/add_save'); ?>" method="POST">
         <div class="modal-body" style="padding:20px">
            <div class="form-group">
               <label>Kode Skala</label>
               <input type="text" class="form-control" name="kode_skala" placeholder="Contoh: likert_1_6" required>
            </div>
            <div class="form-group">
               <label>Nama Skala</label>
               <input type="text" class="form-control" name="nama_skala" placeholder="Contoh: Skala 1-6" required>
            </div>
         </div>
         <div class="modal-footer" style="background:#f8f9fa;padding:12px 20px">
            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
            <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-save"></i> Simpan</button>
         </div>
      </form>
   </div></div>
</div>

<!-- MODAL DETAIL -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg"><div class="modal-content" style="border-radius:5px;overflow:hidden">
      <div class="modal-header" style="background:linear-gradient(135deg,#3498db,#2980b9);color:#fff;padding:15px 20px">
         <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8">&times;</button>
         <h4 class="modal-title" style="font-weight:600"><i class="fa fa-sliders"></i> Detail Skala</h4>
      </div>
      <div class="modal-body" id="detail_content" style="padding:20px;background:#f8f9fa">
         <div class="text-center" style="padding:40px"><i class="fa fa-spinner fa-spin" style="font-size:40px;color:#3498db"></i><p style="margin-top:10px;color:#666">Memuat data...</p></div>
      </div>
      <div class="modal-footer" style="background:#fff;padding:12px 20px">
         <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
      </div>
   </div></div>
</div>

<script>
var filterFields = [
   { value: 'kode_skala', label: 'Kode Skala', type: 'text', operators: ['contains','equals'] },
   { value: 'nama_skala', label: 'Nama Skala', type: 'text', operators: ['contains','equals'] },
];
var operatorLabels = { 'contains':'Mengandung','equals':'Sama dengan' };
var filterIndex = 0;
function getFieldType(f){for(var i=0;i<filterFields.length;i++){if(filterFields[i].value===f)return filterFields[i].type}return'text'}
function getFieldOperators(f){for(var i=0;i<filterFields.length;i++){if(filterFields[i].value===f)return filterFields[i].operators}return['contains']}
function buildOperatorSelect(ops,sel){var h='<select class="form-control input-sm filter-operator" style="width:130px">';for(var i=0;i<ops.length;i++){var o=ops[i];h+='<option value="'+o+'"'+(o===sel?' selected':'')+'>'+operatorLabels[o]+'</option>'}h+='</select>';return h}
function buildValueInput(f,v){return'<input type="text" class="form-control input-sm filter-value" style="width:200px" placeholder="Masukkan nilai..." value="'+(v||'')+'">'}
function addFilterRow(fv,op,v){fv=fv||'';op=op||'';v=v||'';var fsh='<select class="form-control input-sm filter-field" style="width:180px"><option value="">-- Pilih Field --</option>';for(var i=0;i<filterFields.length;i++){fsh+='<option value="'+filterFields[i].value+'"'+(filterFields[i].value===fv?' selected':'')+'>'+filterFields[i].label+'</option>'}fsh+='</select>';var ops=fv?getFieldOperators(fv):['contains'];if(!op||ops.indexOf(op)===-1)op=ops[0];var h='<div class="filter-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px">';h+='<input type="hidden" name="ff[]" class="filter-field-hidden" value="'+fv+'">';h+='<input type="hidden" name="fo[]" class="filter-operator-hidden" value="'+op+'">';h+='<input type="hidden" name="fv[]" class="filter-value-hidden" value="'+v+'">';h+='<span style="color:#3498db;font-weight:bold;font-size:12px;min-width:20px">#'+(filterIndex+1)+'</span>';h+=fsh;h+='<span class="filter-operator-container">'+buildOperatorSelect(ops,op)+'</span>';h+='<span class="filter-value-container">'+buildValueInput(fv,v)+'</span>';h+='<button type="button" class="btn btn-xs btn-danger btn-remove-filter"><i class="fa fa-times"></i></button>';h+='</div>';$('#filter_rows').append(h);filterIndex++;updateFilterEmpty()}
function updateFilterEmpty(){$('#filter_rows .filter-row').length>0?$('#filter_empty').hide():$('#filter_empty').show()}

$(document).ready(function(){
   $('#btn_add_filter').click(function(){addFilterRow()});
   $(document).on('click','.btn-remove-filter',function(){$(this).closest('.filter-row').remove();updateFilterEmpty()});
   $(document).on('change','.filter-field',function(){var r=$(this).closest('.filter-row');var f=$(this).val();r.find('.filter-field-hidden').val(f);var ops=f?getFieldOperators(f):['contains'];r.find('.filter-operator-container').html(buildOperatorSelect(ops,ops[0]));r.find('.filter-operator-hidden').val(ops[0]);r.find('.filter-value-container').html(buildValueInput(f,''));r.find('.filter-value-hidden').val('')});
   $(document).on('change','.filter-operator',function(){$(this).closest('.filter-row').find('.filter-operator-hidden').val($(this).val())});
   $(document).on('change keyup','.filter-value',function(){$(this).closest('.filter-row').find('.filter-value-hidden').val($(this).val())});
   <?php if(!empty($multi_filters)): ?><?php foreach($multi_filters as $mf): ?>addFilterRow('<?= htmlspecialchars($mf['field']); ?>','<?= htmlspecialchars($mf['operator']); ?>','<?= htmlspecialchars($mf['value']); ?>');<?php endforeach; ?><?php endif; ?>
   updateFilterEmpty();

   // Tambah AJAX
   $('#formTambah').submit(function(e){
      e.preventDefault();
      $.ajax({url:$(this).attr('action'),type:'POST',data:$(this).serialize(),dataType:'json',
         success:function(r){if(r.success){toastr.success(r.message);$('#modalTambah').modal('hide');location.reload()}else{if(r.errors){$.each(r.errors,function(k,v){toastr.error(v)})}else{toastr.error(r.message)}}}
      });
   });

   // Detail modal
   $('.btn_detail').click(function(){
      var id=$(this).data('id');
      var content=$('#detail_content');
      content.html('<div class="text-center" style="padding:40px"><i class="fa fa-spinner fa-spin" style="font-size:40px;color:#3498db"></i><p style="margin-top:10px;color:#666">Memuat data...</p></div>');
      $('#modalDetail').modal('show');
      $.ajax({url:'<?= site_url('administrator/mhcu_skala/get_detail/'); ?>'+id,method:'GET',dataType:'json',
         success:function(data){
            if(!data||data.error){content.html('<div class="text-center" style="padding:40px"><i class="fa fa-exclamation-triangle" style="font-size:40px;color:#e74c3c"></i><p style="margin-top:10px;color:#666">'+(data.error||'Data tidak ditemukan')+'</p></div>');return}
            var html='<div style="background:#fff;padding:20px;border-radius:5px;border:1px solid #e0e0e0">';
            html+='<div style="text-align:center;margin-bottom:20px"><div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#3498db,#2980b9);display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px"><i class="fa fa-sliders" style="font-size:30px;color:#fff"></i></div>';
            html+='<h3 style="margin:0;color:#2c3e50">'+(data.nama_skala||'-')+'</h3><p style="color:#666;margin:5px 0 0">Kode: '+(data.kode_skala||'-')+'</p></div>';
            if(data.options&&data.options.length>0){
               html+='<h4 style="margin-bottom:10px"><i class="fa fa-list" style="color:#3498db"></i> Opsi Jawaban</h4>';
               html+='<table class="table table-bordered table-striped"><thead><tr><th width="40">No</th><th>Label</th><th width="80">Skor</th></tr></thead><tbody>';
               for(var i=0;i<data.options.length;i++){var o=data.options[i];html+='<tr><td style="text-align:center">'+o.no_urut_option+'</td><td>'+o.label_option+'</td><td style="text-align:center"><span class="label label-primary">'+o.skor_value+'</span></td></tr>'}
               html+='</tbody></table>'}else{html+='<p class="text-muted text-center">Belum ada opsi</p>'}
            html+='</div>';content.html(html);
         },error:function(){content.html('<div class="text-center" style="padding:40px"><i class="fa fa-exclamation-triangle" style="font-size:40px;color:#e74c3c"></i><p style="margin-top:10px;color:#666">Gagal memuat data</p></div>')}
      });
   });

   // Delete
   $('.remove-data').click(function(){var url=$(this).attr('data-href');swal({title:"<?= cclang('are_you_sure'); ?>",text:"<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",type:"warning",showCancelButton:true,confirmButtonColor:"#DD6B55",confirmButtonText:"<?= cclang('yes_delete_it'); ?>",cancelButtonText:"<?= cclang('no_cancel_plx'); ?>"},function(isConfirm){if(isConfirm){document.location.href=url}});return false});

   // Bulk
   $('#apply').click(function(){var bulk=$('#bulk');var s=$('#form_mhcu_skala').serialize();if(bulk.val()=='delete'){swal({title:"<?= cclang('are_you_sure'); ?>",text:"<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",type:"warning",showCancelButton:true,confirmButtonColor:"#DD6B55",confirmButtonText:"<?= cclang('yes_delete_it'); ?>",cancelButtonText:"<?= cclang('no_cancel_plx'); ?>"},function(isConfirm){if(isConfirm){document.location.href=BASE_URL+'/administrator/mhcu_skala/delete?'+s}})}else if(bulk.val()==''){swal({title:"Upss",text:"Pilih bulk action dulu",type:"warning",confirmButtonText:"Okay!"})}return false});

   // Check all
   var checkAll=$('#check_all');var cb=$('input.check');
   checkAll.on('ifChecked ifUnchecked',function(e){if(e.type=='ifChecked'){cb.iCheck('check')}else{cb.iCheck('uncheck')}});
   cb.on('ifChanged',function(){if(cb.filter(':checked').length==cb.length){checkAll.prop('checked','checked')}else{checkAll.removeProp('checked')}checkAll.iCheck('update')});
});
</script>
