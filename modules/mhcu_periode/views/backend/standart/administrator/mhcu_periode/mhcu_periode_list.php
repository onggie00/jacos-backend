<?php
// Hitung statistik periode (dari data existing)
$stat_total = 0;
$stat_aktif = 0;
$stat_nonaktif = 0;
$stat_periode_with_sesi = 0;

foreach ($mhcu_periodes as $p) {
    $stat_total++;
    if ($p->is_active == 1) $stat_aktif++;
    else $stat_nonaktif++;
}
?>

<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-filter-builder.js"></script>

<section class="content-header">
   <h1>
      <i class="fa fa-calendar"></i> Periode MHCU
      <small class="labs-text-muted">Daftar Periode Skrining</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Periode MHCU</li>
   </ol>
</section>

<?php if ($this->session->flashdata('success')): ?>
   <script>toastr.success("<?= addslashes(str_replace("\n", "<br>", $this->session->flashdata('success'))); ?>", "Berhasil", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });</script>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
   <script>toastr.error("<?= addslashes(str_replace("\n", "<br>", $this->session->flashdata('error'))); ?>", "Error", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });</script>
<?php endif; ?>

<section class="content">

   <!-- STAT CARDS - Ringkasan -->
   <div class="row">
      <div class="col-md-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--primary">
               <i class="fa fa-calendar"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Total Periode</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stat_total); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--success">
               <i class="fa fa-check-circle"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Aktif</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stat_aktif); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--warning">
               <i class="fa fa-pause-circle"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Nonaktif</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stat_nonaktif); ?>">0</div>
            </div>
         </div>
      </div>
   </div>

   <!-- MAIN CARD -->
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">

            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-list-alt"></i>
                  Data Periode Skrining MHCU
                  <span class="labs-badge labs-badge--default labs-ml-2"><?= intval($mhcu_periode_counts); ?> Data</span>
               </h3>
               <?php is_allowed('mhcu_periode_add', function() { ?>
               <button type="button" class="labs-btn labs-btn--success labs-btn--sm" id="btn_modal_tambah" title="Tambah Periode">
                  <i class="fa fa-plus"></i> Tambah Periode
               </button>
               <?php }) ?>
            </div>

            <div class="labs-card__body">

               <form name="form_mhcu_periode" id="form_mhcu_periode" action="<?= base_url('administrator/mhcu_periode/index'); ?>">

                  <!-- ADVANCED FILTER BUILDER -->
                  <div class="labs-filter-builder" id="fb_periode">
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
                        <a class="labs-btn labs-btn--default labs-btn--sm" href="<?= base_url('administrator/mhcu_periode'); ?>">
                           <i class="fa fa-undo"></i> Reset
                        </a>

                        <div class="labs-filter-builder__sort">
                           <select name="s" title="Urutkan">
                              <option value="">Urutkan</option>
                              <option <?= $this->input->get('s') == 'nama_periode' ? 'selected' : ''; ?> value="nama_periode">Nama Periode</option>
                              <option <?= $this->input->get('s') == 'tanggal_mulai' ? 'selected' : ''; ?> value="tanggal_mulai">Tanggal Mulai</option>
                              <option <?= $this->input->get('s') == 'tanggal_selesai' ? 'selected' : ''; ?> value="tanggal_selesai">Tanggal Selesai</option>
                              <option <?= $this->input->get('s') == 'is_active' ? 'selected' : ''; ?> value="is_active">Status</option>
                           </select>
                           <select name="d" title="Arah">
                              <option <?= $this->input->get('d') == 'asc' ? 'selected' : ''; ?> value="asc">ASC</option>
                              <option <?= ($this->input->get('d') == 'desc' || empty($this->input->get('d'))) ? 'selected' : ''; ?> value="desc">DESC</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  <!-- ACTIVE FILTERS -->
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
                              <th width="40"><input type="checkbox" class="flat-red" id="check_all" name="check_all"></th>
                              <th>Nama Periode</th>
                              <th>Tanggal Mulai</th>
                              <th>Tanggal Selesai</th>
                              <th width="120">Target Peserta</th>
                              <th width="120">Status</th>
                              <th width="280" class="labs-cell-numeric">Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                        <?php if($mhcu_periode_counts > 0): ?>
                        <?php foreach($mhcu_periodes as $p): ?>
                           <tr>
                              <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $p->id_mhcu_periode; ?>"></td>
                              <td>
                                 <div class="labs-flex labs-flex-gap-2" style="align-items:center">
                                    <i class="fa fa-calendar" style="color:var(--labs-primary)"></i>
                                    <div>
                                       <div class="labs-fw-semi"><?= _ent($p->nama_periode); ?></div>
                                    </div>
                                 </div>
                              </td>
                              <td><?= formatTanggal($p->tanggal_mulai); ?></td>
                              <td><?= formatTanggal($p->tanggal_selesai); ?></td>
                              <td><?= isset($p->target_peserta) && $p->target_peserta !== null ? number_format($p->target_peserta) : '<span class="text-muted">-</span>'; ?></td>
                              <td>
                                 <?php if ($p->is_active == 1): ?>
                                    <span class="labs-badge labs-badge--success"><span class="dot"></span> Aktif</span>
                                 <?php else: ?>
                                    <span class="labs-badge labs-badge--default"><span class="dot"></span> Nonaktif</span>
                                 <?php endif; ?>
                              </td>
                              <td class="labs-cell-numeric">
                                 <div class="labs-row-actions" style="justify-content:center">
                                    <?php is_allowed('mhcu_periode_view', function() use ($p){ ?>
                                    <button type="button" class="labs-btn labs-btn--info labs-btn--icon labs-btn--sm btn_detail" data-id="<?= $p->id_mhcu_periode; ?>" title="Lihat detail">
                                       <i class="fa fa-eye"></i>
                                    </button>
                                    <?php }) ?>
                                    <?php is_allowed('mhcu_periode_update', function() use ($p){ ?>
                                    <a href="<?= site_url('administrator/mhcu_periode/edit/' . $p->id_mhcu_periode); ?>" class="labs-btn labs-btn--warning labs-btn--icon labs-btn--sm" title="Edit">
                                       <i class="fa fa-edit"></i>
                                    </a>
                                    <?php }) ?>
                                    <?php is_allowed('mhcu_periode_export', function() use ($p){ ?>
                                    <a href="<?= site_url('administrator/mhcu_periode/export/' . $p->id_mhcu_periode); ?>" class="labs-btn labs-btn--success labs-btn--icon labs-btn--sm" title="Export Excel Periode Ini">
                                       <i class="fa fa-file-excel-o"></i>
                                    </a>
                                    <?php }) ?>
                                    <?php is_allowed('mhcu_periode_delete', function() use ($p){ ?>
                                    <a href="javascript:void(0);" data-href="<?= site_url('administrator/mhcu_periode/delete/' . $p->id_mhcu_periode); ?>" class="labs-btn labs-btn--danger labs-btn--icon labs-btn--sm remove-data" title="Hapus">
                                       <i class="fa fa-trash"></i>
                                    </a>
                                    <?php }) ?>
                                 </div>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                           <tr class="labs-empty-row">
                              <td colspan="6">
                                 <div class="labs-empty">
                                    <i class="fa fa-inbox"></i>
                                    <p>
                                       <?php if(!empty($multi_filters)): ?>
                                          Data tidak ditemukan untuk filter yang dipilih
                                       <?php else: ?>
                                          Belum ada data periode MHCU
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

                  <!-- BULK & PAGINATION -->
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
                     <?php if(!empty($pagination) && $mhcu_periode_counts > 0): ?>
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
            <h4 class="modal-title"><i class="fa fa-plus-circle"></i> Tambah Periode Skrining</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <form id="formTambah" action="<?= base_url('administrator/mhcu_periode/add_save'); ?>" method="POST">
            <div class="modal-body">
               <div class="labs-modal-section">
                  <div class="labs-form-group">
                     <label class="labs-form-label">Nama Periode</label>
                     <input type="text" class="form-control" name="nama_periode" placeholder="Contoh: Skrining Semester Genap 2025" required>
                  </div>
                  <div class="row" style="display:flex;gap:var(--labs-space-3);flex-wrap:wrap">
                     <div class="labs-form-group" style="flex:1;min-width:200px">
                        <label class="labs-form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" required>
                     </div>
                     <div class="labs-form-group" style="flex:1;min-width:200px">
                        <label class="labs-form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="tanggal_selesai" required>
                     </div>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Status</label>
                     <select class="form-control" name="is_active">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                     </select>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Target Peserta <span class="text-muted">(opsional)</span></label>
                     <input type="number" min="0" class="form-control" name="target_peserta" placeholder="cth: 180">
                     <small class="text-muted">Untuk tampilan tracker proyektor.</small>
                  </div>
                  <div class="labs-form-group">
                     <label class="labs-form-label">Tanggal Publish Hasil <span class="text-muted">(opsional)</span></label>
                     <input type="date" class="form-control" name="tanggal_publish">
                     <small class="text-muted">Sebelum tanggal ini, peserta tidak bisa melihat skor/hasil. Kosongkan = langsung tampil.</small>
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
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-calendar"></i> Detail Periode</h4>
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

<!-- SKELETON TEMPLATE -->
<script type="text/template" id="detail-skeleton">
<div class="labs-text-center" style="padding: 20px 0;">
   <div class="labs-skeleton labs-skeleton--circle"></div>
   <div class="labs-skeleton labs-skeleton--lg labs-skeleton--block" style="width:40%;margin:0 auto 16px"></div>
   <div class="labs-skeleton labs-skeleton--block" style="height:80px"></div>
</div>
</script>

<script>
$(document).ready(function(){
   // Init filter builder
   LabsFilterBuilder.create({
      container: '#fb_periode',
      prefix: 'mhcu',
      fields: [
         { value: 'nama_periode',    label: 'Nama Periode',    type: 'text',   operators: ['contains','equals','starts_with','ends_with'] },
         { value: 'tanggal_mulai',   label: 'Tanggal Mulai',   type: 'text',   operators: ['contains','equals'] },
         { value: 'tanggal_selesai', label: 'Tanggal Selesai', type: 'text',   operators: ['contains','equals'] },
         { value: 'is_active',       label: 'Status',          type: 'select', operators: ['equals'],
            options: [
               { id: '1', text: 'Aktif' },
               { id: '0', text: 'Nonaktif' }
            ]
         }
      ],
      initialFilters: <?= !empty($multi_filters) ? json_encode(array_map(function($f){
         return array('field'=>$f['field'],'operator'=>$f['operator'],'value'=>$f['value']);
      }, $multi_filters)) : '[]'; ?>
   });

   // Tambah
   $('#formTambah').submit(function(e){
      e.preventDefault();
      var form = $(this);
      $.ajax({
         url: form.attr('action'),
         type: 'POST',
         data: form.serialize(),
         dataType: 'json',
         success: function(r){
            if(r.success){
               toastr.success(r.message);
               $('#modalTambah').modal('hide');
               form[0].reset();
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
         url: '<?= site_url('administrator/mhcu_periode/get_detail/'); ?>' + id,
         method: 'GET',
         dataType: 'json',
         success: function(data){
            if (!data || data.error) {
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

   // Bulk
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var s = $('#form_mhcu_periode').serialize();
      if (bulk.val() == 'delete') {
         swal({title:"<?= cclang('are_you_sure'); ?>",text:"<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",type:"warning",showCancelButton:true,confirmButtonColor:"#DD6B55",confirmButtonText:"<?= cclang('yes_delete_it'); ?>",cancelButtonText:"<?= cclang('no_cancel_plx'); ?>"},function(isConfirm){if(isConfirm){document.location.href=BASE_URL+'/administrator/mhcu_periode/delete?'+s}});
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

   // Click handler tombol tambah (tidak ada keybind)
   $('#btn_modal_tambah').on('click', function() {
      $('#modalTambah').modal('show');
   });
});

function renderDetail(data){
   var html = '';
   var isActive = data.is_active == 1;

   html += '<div class="labs-modal-section">';
   html += '  <div class="labs-text-center" style="padding-bottom:16px;margin-bottom:16px;border-bottom:1px solid var(--labs-border)">';
   html += '    <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg, var(--labs-primary), var(--labs-primary-dark));display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">';
   html += '      <i class="fa fa-calendar" style="font-size:30px;color:#fff"></i>';
   html += '    </div>';
   html += '    <h3 style="margin:0;color:var(--labs-text)">' + (data.nama_periode || '-') + '</h3>';
   html += '    <div style="margin-top:8px">';
   html += '      <span class="labs-badge labs-badge--' + (isActive ? 'success' : 'default') + '"><span class="dot"></span> ' + (isActive ? 'Aktif' : 'Nonaktif') + '</span>';
   html += '    </div>';
   html += '  </div>';
   html += '  <div class="labs-info-grid">';
   html += '    <div class="labs-info-grid__item"><div class="labs-info-grid__label"><i class="fa fa-calendar-o" style="color:var(--labs-primary)"></i> Tanggal Mulai</div><div class="labs-info-grid__value">' + (data.tanggal_mulai_formatted || data.tanggal_mulai || '-') + '</div></div>';
   html += '    <div class="labs-info-grid__item"><div class="labs-info-grid__label"><i class="fa fa-calendar-check-o" style="color:var(--labs-primary)"></i> Tanggal Selesai</div><div class="labs-info-grid__value">' + (data.tanggal_selesai_formatted || data.tanggal_selesai || '-') + '</div></div>';
   html += '    <div class="labs-info-grid__item"><div class="labs-info-grid__label"><i class="fa fa-toggle-on" style="color:var(--labs-primary)"></i> Status</div><div class="labs-info-grid__value">' + (isActive ? '<span class="labs-badge labs-badge--success"><span class="dot"></span> Aktif</span>' : '<span class="labs-badge labs-badge--default"><span class="dot"></span> Nonaktif</span>') + '</div></div>';
   if (data.tanggal_publish_formatted || data.tanggal_publish) {
      html += '    <div class="labs-info-grid__item"><div class="labs-info-grid__label"><i class="fa fa-bullhorn" style="color:var(--labs-primary)"></i> Tanggal Publish Hasil</div><div class="labs-info-grid__value">' + (data.tanggal_publish_formatted || data.tanggal_publish) + '</div></div>';
   } else {
      html += '    <div class="labs-info-grid__item"><div class="labs-info-grid__label"><i class="fa fa-bullhorn" style="color:var(--labs-primary)"></i> Tanggal Publish Hasil</div><div class="labs-info-grid__value labs-text-muted">Langsung tampil</div></div>';
   }
   html += '  </div>';
   html += '</div>';

   $('#detail_content').html(html);
}
</script>