<?php
function labs_initials_demografi($name) {
    $name = trim($name);
    if ($name === '') return '?';
    $parts = preg_split('/\s+/', $name);
    if (count($parts) >= 2) {
        return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
    }
    return strtoupper(mb_substr($parts[0], 0, 2));
}

function labs_input_type_badge($t) {
    $map = array(
        'radio_single_choice'   => 'info',
        'checkbox_multi_choice' => 'orange',
        'number'                => 'success',
        'text'                  => 'default'
    );
    return isset($map[$t]) ? $map[$t] : 'default';
}
function labs_input_type_label($t) {
    $map = array(
        'radio_single_choice'   => 'Radio',
        'checkbox_multi_choice' => 'Checkbox',
        'number'                => 'Number',
        'text'                  => 'Text'
    );
    return isset($map[$t]) ? $map[$t] : $t;
}
?>

<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-filter-builder.js"></script>

<section class="content-header">
   <h1>
      <i class="fa fa-users"></i> Demografi MHCU
      <small class="labs-text-muted">Pertanyaan Demografi</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Demografi</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">

            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-list-alt"></i>
                  Data Pertanyaan Demografi
                  <span class="labs-badge labs-badge--default labs-ml-2"><?= intval($mhcu_demografi_counts); ?> Data</span>
               </h3>
               <?php is_allowed('mhcu_demografi_add', function() { ?>
               <button type="button" class="labs-btn labs-btn--success labs-btn--sm" id="btn_modal_tambah" data-toggle="modal" data-target="#modalTambah">
                  <i class="fa fa-plus"></i> Tambah Pertanyaan
               </button>
               <?php }) ?>
            </div>

            <div class="labs-card__body">

               <form name="form_mhcu_demografi" id="form_mhcu_demografi" action="<?= base_url('administrator/mhcu_demografi/index'); ?>">

                  <!-- ADVANCED FILTER BUILDER -->
                  <div class="labs-filter-builder" id="fb_demografi">
                     <div class="labs-filter-builder__header">
                        <span class="labs-filter-builder__title">
                           <i class="fa fa-filter"></i> Advanced Filter
                        </span>
                        <div class="labs-filter-builder__actions">
                           <button type="button" class="labs-btn labs-btn--success labs-btn--sm" data-labs-fb-add>
                              <i class="fa fa-plus"></i> Tambah Filter
                           </button>
                        </div>
                     </div>

                     <div class="labs-filter-builder__rows"></div>

                     <div class="labs-filter-builder__empty">
                        <i class="fa fa-search"></i>
                        <p>Klik "Tambah Filter" untuk menambah filter pencarian</p>
                     </div>

                     <div class="labs-filter-builder__footer">
                        <button type="submit" class="labs-btn labs-btn--primary labs-btn--sm">
                           <i class="fa fa-search"></i> Terapkan Filter
                        </button>
                        <a class="labs-btn labs-btn--default labs-btn--sm" href="<?= base_url('administrator/mhcu_demografi'); ?>">
                           <i class="fa fa-undo"></i> Reset
                        </a>

                        <div class="labs-filter-builder__sort">
                           <select name="s" title="Urutkan">
                              <option value="">Urutkan</option>
                              <option <?= $this->input->get('s')=='no_urut'?'selected':'' ?> value="no_urut">No Urut</option>
                              <option <?= $this->input->get('s')=='demografi_kode'?'selected':'' ?> value="demografi_kode">Kode</option>
                           </select>
                           <select name="d" title="Arah">
                              <option <?= $this->input->get('d')=='asc'?'selected':'' ?> value="asc">ASC</option>
                              <option <?= ($this->input->get('d')=='desc'||empty($this->input->get('d')))?'selected':'' ?> value="desc">DESC</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  <!-- ACTIVE FILTERS CHIPS -->
                  <?php if(!empty($multi_filters)): ?>
                  <div class="labs-filter-builder__active">
                     <i class="fa fa-info-circle"></i>
                     <strong>Filter aktif:</strong>
                     <?php foreach($multi_filters as $mf): ?>
                        <span class="labs-filter-builder__chip">
                           <?= htmlspecialchars($mf['field']); ?> <?= htmlspecialchars($mf['operator']); ?> "<?= htmlspecialchars($mf['value']); ?>"
                        </span>
                     <?php endforeach; ?>
                  </div>
                  <?php endif; ?>

                  <!-- TABLE -->
                  <div class="labs-table-wrap">
                     <div class="labs-table-scroll">
                     <table class="labs-table">
                        <thead>
                           <tr>
                              <th width="40"><input type="checkbox" class="flat-red" id="check_all"></th>
                              <th width="50">No</th>
                              <th width="100">Kode</th>
                              <th>Pertanyaan</th>
                              <th width="130">Tipe Input</th>
                              <th width="80" class="labs-cell-numeric">Opsi</th>
                              <th width="200" class="labs-cell-numeric">Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                        <?php if($mhcu_demografi_counts > 0): ?>
                        <?php foreach($mhcu_demografis as $d):
                           $bcls = labs_input_type_badge($d->input_type);
                           $blbl = labs_input_type_label($d->input_type);
                           $initials = labs_initials_demografi($d->demografi_pertanyaan);
                           // Hitung opsi (existing logic)
                           $opt_count = count($this->model_mhcu_demografi->get_options($d->id_demografi_pertanyaan));
                        ?>
                           <tr>
                              <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $d->id_demografi_pertanyaan; ?>"></td>
                              <td class="labs-cell-numeric"><?= $d->no_urut; ?></td>
                              <td>
                                 <span class="labs-badge labs-badge--info"><?= _ent($d->demografi_kode); ?></span>
                              </td>
                              <td>
                                 <div class="labs-fw-semi"><?= _ent($d->demografi_pertanyaan); ?></div>
                              </td>
                              <td><span class="labs-badge labs-badge--<?= $bcls; ?>"><?= _ent($blbl); ?></span></td>
                              <td class="labs-cell-numeric">
                                 <span class="labs-badge labs-badge--default"><?= $opt_count; ?></span>
                              </td>
                              <td class="labs-cell-numeric">
                                 <div class="labs-row-actions" style="justify-content:center">
                                    <?php is_allowed('mhcu_demografi_view', function() use ($d){ ?>
                                    <button type="button" class="labs-btn labs-btn--info labs-btn--icon labs-btn--sm btn_detail" data-id="<?= $d->id_demografi_pertanyaan; ?>" title="Detail">
                                       <i class="fa fa-eye"></i>
                                    </button>
                                    <?php }) ?>
                                    <?php is_allowed('mhcu_demografi_update', function() use ($d){ ?>
                                    <a href="<?= site_url('administrator/mhcu_demografi/edit/'.$d->id_demografi_pertanyaan); ?>" class="labs-btn labs-btn--warning labs-btn--icon labs-btn--sm" title="Edit">
                                       <i class="fa fa-edit"></i>
                                    </a>
                                    <?php }) ?>
                                    <?php is_allowed('mhcu_demografi_delete', function() use ($d){ ?>
                                    <a href="javascript:void(0);" data-href="<?= site_url('administrator/mhcu_demografi/delete/'.$d->id_demografi_pertanyaan); ?>" class="labs-btn labs-btn--danger labs-btn--icon labs-btn--sm remove-data" title="Hapus">
                                       <i class="fa fa-trash"></i>
                                    </a>
                                    <?php }) ?>
                                 </div>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                           <tr class="labs-empty-row">
                              <td colspan="7">
                                 <div class="labs-empty">
                                    <i class="fa fa-inbox"></i>
                                    <p>
                                       <?php if(!empty($multi_filters)): ?>
                                          Data tidak ditemukan untuk filter yang dipilih
                                       <?php else: ?>
                                          Belum ada data pertanyaan demografi
                                       <?php endif; ?>
                                    </p>
                                 </div>
                              </td>
                           </tr>
                        <?php endif; ?>
                        </tbody>
                     </table>
                     </div>
                  </div>

                  <!-- BULK ACTION & PAGINATION -->
                  <div class="labs-flex-between labs-mt-4">
                     <div class="input-group" style="max-width:320px">
                        <select class="form-control" name="bulk" id="bulk" style="height:34px;border-radius:6px 0 0 6px;border:1px solid var(--labs-border-strong)">
                           <option value="">-- Bulk Action --</option>
                           <option value="delete">Hapus Terpilih</option>
                        </select>
                        <span class="input-group-btn">
                           <button type="button" class="labs-btn labs-btn--default labs-btn--sm" id="apply" style="border-radius:0 6px 6px 0">Terapkan</button>
                        </span>
                     </div>
                     <?php if(!empty($pagination) && $mhcu_demografi_counts > 0): ?>
                     <div><?= $pagination; ?></div>
                     <?php endif; ?>
                  </div>

               </form>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL TAMBAH -->
<div class="modal fade labs-modal" id="modalTambah" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-plus-circle"></i> Tambah Pertanyaan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <form id="formTambah" action="<?= base_url('administrator/mhcu_demografi/add_save'); ?>" method="POST">
            <div class="modal-body">
               <div class="labs-modal-section">
                  <div class="labs-form-group">
                     <label class="labs-form-label">No Urut</label>
                     <input type="number" class="form-control" name="no_urut" placeholder="1" required>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Kode</label>
                     <input type="text" class="form-control" name="demografi_kode" placeholder="Contoh: usia, jenis_kelamin" required>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Pertanyaan</label>
                     <input type="text" class="form-control" name="demografi_pertanyaan" placeholder="Contoh: Berapa usia Anda?" required>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Tipe Input</label>
                     <select class="form-control" name="input_type">
                        <option value="radio_single_choice">Radio (Single Choice)</option>
                        <option value="checkbox_multi_choice">Checkbox (Multi Choice)</option>
                        <option value="number">Number</option>
                        <option value="text">Text</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="labs-btn labs-btn--default" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
               <button type="submit" class="labs-btn labs-btn--primary"><i class="fa fa-save"></i> Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal fade labs-modal" id="modalDetail" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-users"></i> Detail Demografi</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body" id="detail_content">
            <div class="labs-text-center" style="padding:40px">
               <i class="fa fa-spinner fa-spin" style="font-size:40px;color:var(--labs-primary)"></i>
               <p class="labs-mt-3 labs-text-muted">Memuat data...</p>
            </div>
         </div>
         <div class="modal-footer">
            <button class="labs-btn labs-btn--default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
         </div>
      </div>
   </div>
</div>

<script type="text/template" id="detail-skeleton">
<div class="labs-text-center" style="padding: 20px 0;">
   <div class="labs-skeleton labs-skeleton--circle"></div>
   <div class="labs-skeleton labs-skeleton--lg labs-skeleton--block" style="width:50%;margin:0 auto 16px"></div>
   <div class="labs-skeleton labs-skeleton--block" style="height:120px"></div>
</div>
</script>

<script>
$(document).ready(function(){
   // Init filter builder
   LabsFilterBuilder.create({
      container: '#fb_demografi',
      prefix: 'mhcu',
      fields: [
         { value: 'demografi_kode',     label: 'Kode',       type: 'text',   operators: ['contains','equals'] },
         { value: 'demografi_pertanyaan', label: 'Pertanyaan', type: 'text', operators: ['contains','equals'] },
         { value: 'input_type',         label: 'Tipe Input', type: 'select', operators: ['equals'],
            options: [
               { id: 'radio_single_choice',   text: 'Radio (Single Choice)' },
               { id: 'checkbox_multi_choice', text: 'Checkbox (Multi Choice)' },
               { id: 'number',                text: 'Number' },
               { id: 'text',                  text: 'Text' }
            ]
         }
      ],
      initialFilters: <?= !empty($multi_filters) ? json_encode(array_map(function($f){
         return array('field'=>$f['field'],'operator'=>$f['operator'],'value'=>$f['value']);
      }, $multi_filters)) : '[]'; ?>
   });

   // Form tambah
   $('#formTambah').submit(function(e){
      e.preventDefault();
      $.ajax({
         url: $(this).attr('action'),
         type: 'POST',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(r){
            if(r.success){
               toastr.success(r.message);
               $('#modalTambah').modal('hide');
               location.reload();
            } else {
               if(r.errors){ $.each(r.errors, function(k,v){ toastr.error(v); }); }
               else { toastr.error(r.message); }
            }
         }
      });
   });

   // Detail modal
   $('.btn_detail').click(function(){
      var id = $(this).data('id');
      var content = $('#detail_content');
      content.html($('#detail-skeleton').html());
      $('#modalDetail').modal('show');

      $.ajax({
         url: '<?= site_url('administrator/mhcu_demografi/get_detail/'); ?>' + id,
         method: 'GET',
         dataType: 'json',
         success: function(data){
            if(!data || data.error){
               content.html('<div class="labs-empty"><i class="fa fa-exclamation-circle labs-text-danger"></i><p>' + (data.error || 'Data tidak ditemukan') + '</p></div>');
               return;
            }
            renderDetail(data);
         },
         error: function(){
            content.html('<div class="labs-empty"><i class="fa fa-exclamation-circle labs-text-danger"></i><p>Gagal memuat</p></div>');
         }
      });
   });

   // Delete
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({title:"<?= cclang('are_you_sure'); ?>",text:"<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",type:"warning",showCancelButton:true,confirmButtonColor:"#DD6B55",confirmButtonText:"<?= cclang('yes_delete_it'); ?>",cancelButtonText:"<?= cclang('no_cancel_plx'); ?>"},function(isConfirm){if(isConfirm){document.location.href=url}});
      return false;
   });

   // Bulk action
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var s = $('#form_mhcu_demografi').serialize();
      if(bulk.val() == 'delete'){
         swal({title:"<?= cclang('are_you_sure'); ?>",text:"<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",type:"warning",showCancelButton:true,confirmButtonColor:"#DD6B55",confirmButtonText:"<?= cclang('yes_delete_it'); ?>",cancelButtonText:"<?= cclang('no_cancel_plx'); ?>"},function(isConfirm){if(isConfirm){document.location.href=BASE_URL+'/administrator/mhcu_demografi/delete?'+s}});
      } else if(bulk.val() == ''){
         swal({title:"Upss",text:"Pilih bulk action dulu",type:"warning",confirmButtonText:"Okay!"});
      }
      return false;
   });

   // Check all
   var checkAll = $('#check_all');
   var cb = $('input.check');
   checkAll.on('ifChecked ifUnchecked', function(e){if(e.type=='ifChecked'){cb.iCheck('check')}else{cb.iCheck('uncheck')}});
   cb.on('ifChanged', function(){if(cb.filter(':checked').length==cb.length){checkAll.prop('checked','checked')}else{checkAll.removeProp('checked')}checkAll.iCheck('update')});
});

function labsInputTypeBadge(t){
   var map = {'radio_single_choice':'info','checkbox_multi_choice':'orange','number':'success','text':'default'};
   return map[t] || 'default';
}
function labsInputTypeLabel(t){
   var map = {'radio_single_choice':'Radio','checkbox_multi_choice':'Checkbox','number':'Number','text':'Text'};
   return map[t] || t;
}

function renderDetail(data){
   var html = '';
   var bcls = labsInputTypeBadge(data.input_type);
   var blbl = labsInputTypeLabel(data.input_type);

   // Tabs (single tab karena data kecil)
   html += '<div class="labs-modal-tabs">';
   html += '  <div class="labs-modal-tabs__item active" data-tab="tab-info"><i class="fa fa-info-circle"></i> Info</div>';
   if (data.options && data.options.length > 0) {
      html += '  <div class="labs-modal-tabs__item" data-tab="tab-options"><i class="fa fa-list"></i> Opsi <span class="labs-badge labs-badge--default">' + data.options.length + '</span></div>';
   }
   html += '</div>';

   // TAB INFO
   html += '<div class="labs-tab-content active" id="tab-info">';
   html += '  <div class="labs-modal-section">';
   html += '    <h4 class="labs-modal-section__title"><i class="fa fa-question-circle"></i> Pertanyaan</h4>';
   html += '    <div style="background:var(--labs-bg-soft);padding:16px;border-radius:6px;border-left:3px solid var(--labs-primary);font-size:14px;font-weight:600">' + (data.demografi_pertanyaan||'-') + '</div>';
   html += '    <div class="labs-info-grid labs-mt-4">';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">No Urut</div><div class="labs-info-grid__value">' + (data.no_urut||'-') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Kode</div><div class="labs-info-grid__value"><span class="labs-badge labs-badge--info">' + (data.demografi_kode||'-') + '</span></div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Tipe Input</div><div class="labs-info-grid__value"><span class="labs-badge labs-badge--' + bcls + '">' + blbl + '</span></div></div>';
   html += '    </div>';
   html += '  </div>';
   html += '</div>';

   // TAB OPSI
   if (data.options && data.options.length > 0) {
      html += '<div class="labs-tab-content" id="tab-options">';
      html += '  <div class="labs-modal-section">';
      html += '    <h4 class="labs-modal-section__title"><i class="fa fa-list"></i> Opsi Jawaban</h4>';
      html += '    <div class="labs-table-wrap">';
      html += '      <table class="labs-table">';
      html += '        <thead><tr><th width="40">No</th><th>Label</th><th width="100">Skor</th></tr></thead>';
      html += '        <tbody>';
      for (var i = 0; i < data.options.length; i++) {
         var o = data.options[i];
         html += '<tr>';
         html += '  <td class="labs-cell-numeric">' + o.no_urut + '</td>';
         html += '  <td>' + o.label_option + '</td>';
         html += '  <td class="labs-cell-numeric">' + (o.skor||'-') + '</td>';
         html += '</tr>';
      }
      html += '        </tbody>';
      html += '      </table>';
      html += '    </div>';
      html += '  </div>';
      html += '</div>';
   }

   $('#detail_content').html(html);
   if (window.LabsUI && LabsUI.initTabs) LabsUI.initTabs();
}
</script>