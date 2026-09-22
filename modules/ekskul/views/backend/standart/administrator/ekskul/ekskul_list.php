<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<style>
@media (min-width: 992px) {
   .col-md-2-4 { width: 20%; float: left; padding-left: 15px; padding-right: 15px; }
}
@media (max-width: 991px) {
   .col-md-2-4 { width: 50%; float: left; padding-left: 15px; padding-right: 15px; }
}
.labs-ekskul-foto { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
.labs-ekskul-foto-placeholder { width: 40px; height: 40px; border-radius: 8px; background: var(--labs-bg-soft); display: flex; align-items: center; justify-content: center; color: var(--labs-text-light); }
</style>

<section class="content-header">
   <h1>
      Ekstrakurikuler
      <small class="labs-text-muted">Manajemen Data</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Ekstrakurikuler</li>
   </ol>
</section>

<section class="content">

   <?php if ($this->session->flashdata('success')): ?>
      <script>toastr.success("<?= $this->session->flashdata('success'); ?>");</script>
   <?php elseif ($this->session->flashdata('error')): ?>
      <script>toastr.error("<?= $this->session->flashdata('error'); ?>");</script>
   <?php endif; ?>

   <!-- STAT CARDS -->
   <div class="row">
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--primary">
               <i class="fa fa-users"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Total Ekskul</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->total); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--info">
               <i class="fa fa-graduation-cap"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">SD</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->sd); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--success">
               <i class="fa fa-graduation-cap"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">SMP</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->smp); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--warning">
               <i class="fa fa-graduation-cap"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">SMA</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->sma); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--danger">
               <i class="fa fa-graduation-cap"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">FT</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->ft); ?>">0</div>
            </div>
         </div>
      </div>
   </div>

   <!-- MAIN LIST -->
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">

            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-list-alt"></i>
                  Daftar Ekstrakurikuler
                  <span class="labs-badge labs-badge--default labs-ml-2"><?= $ekskul_counts; ?> data</span>
               </h3>
               <div>
                  <?php is_allowed('ekskul_add', function(){ ?>
                  <a class="labs-btn labs-btn--success labs-btn--sm" href="<?= site_url('administrator/ekskul/add'); ?>" title="Tambah Ekskul">
                     <i class="fa fa-plus-square-o"></i> Tambah
                  </a>
                  <?php }) ?>
                  <button class="labs-btn labs-btn--info labs-btn--sm" data-toggle="modal" data-target="#modal_setting" title="Setting Tahun & Semester">
                     <i class="fa fa-cog"></i> Setting
                  </button>
                  <button class="labs-btn labs-btn--warning labs-btn--sm" data-toggle="modal" data-target="#modal_bank" title="Setting Bank & Pembayaran">
                     <i class="fa fa-university"></i> Bank
                  </button>
                  <?php is_allowed('ekskul_export', function(){ ?>
                  <a class="labs-btn labs-btn--success labs-btn--sm" href="<?= site_url('administrator/ekskul/export'); ?>" title="Export XLS">
                     <i class="fa fa-file-excel-o"></i> XLS
                  </a>
                  <a class="labs-btn labs-btn--danger labs-btn--sm" href="<?= site_url('administrator/ekskul/export_pdf'); ?>" title="Export PDF">
                     <i class="fa fa-file-pdf-o"></i> PDF
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="labs-card__body">

               <!-- FILTER -->
               <form action="<?= base_url('administrator/ekskul/index'); ?>" method="GET" class="labs-filter-inline">
                  <select class="form-control" name="f" title="Filter Kolom">
                     <option value="">Semua Kolom</option>
                     <option value="nama" <?= ($this->input->get('f') == 'nama') ? 'selected' : ''; ?>>Nama</option>
                     <option value="judul" <?= ($this->input->get('f') == 'judul') ? 'selected' : ''; ?>>Judul</option>
                     <option value="hari" <?= ($this->input->get('f') == 'hari') ? 'selected' : ''; ?>>Hari</option>
                     <option value="jenjang" <?= ($this->input->get('f') == 'jenjang') ? 'selected' : ''; ?>>Jenjang</option>
                     <option value="tahun_ajaran_aktif" <?= ($this->input->get('f') == 'tahun_ajaran_aktif') ? 'selected' : ''; ?>>Tahun Ajaran</option>
                  </select>

                  <select class="form-control" name="jenjang" id="filterJenjang" title="Jenjang">
                     <option value="">Semua Jenjang</option>
                     <option value="sd" <?= ($this->input->get('jenjang') == 'sd') ? 'selected' : ''; ?>>SD</option>
                     <option value="smp" <?= ($this->input->get('jenjang') == 'smp') ? 'selected' : ''; ?>>SMP</option>
                     <option value="sma" <?= ($this->input->get('jenjang') == 'sma') ? 'selected' : ''; ?>>SMA</option>
                     <option value="ft" <?= ($this->input->get('jenjang') == 'ft') ? 'selected' : ''; ?>>FT</option>
                  </select>

                  <div class="labs-filter-inline__search">
                     <i class="fa fa-search labs-filter-inline__search-icon"></i>
                     <input type="text" class="form-control" name="q" placeholder="Cari nama, judul, hari..." value="<?= htmlspecialchars($this->input->get('q'), ENT_QUOTES); ?>">
                  </div>

                  <button type="submit" class="labs-btn labs-btn--primary labs-btn--sm"><i class="fa fa-filter"></i></button>
                  <a href="<?= base_url('administrator/ekskul'); ?>" class="labs-btn labs-btn--default labs-btn--sm" title="Reset"><i class="fa fa-undo"></i></a>
               </form>

               <!-- TABLE -->
               <form name="form_ekskul" id="form_ekskul" action="<?= base_url('administrator/ekskul/index'); ?>">
               <div class="labs-table-wrap">
                  <div class="labs-table-scroll">
                  <table class="labs-table">
                     <thead>
                        <tr>
                           <th style="width:30px"><input type="checkbox" class="flat-red" id="check_all" title="Pilih semua"></th>
                           <th>Nama</th>
                           <th>Judul</th>
                           <th>Jenjang</th>
                           <th>Hari & Jam</th>
                           <th class="labs-cell-numeric">Biaya</th>
                           <th>Tahun / Semester</th>
                           <th class="labs-cell-numeric">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php foreach($ekskuls as $ekskul): ?>
                        <tr>
                           <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $ekskul->id_ekskul; ?>"></td>
                           <td>
                              <div class="labs-name-cell">
                                 <?php if (!empty($ekskul->foto) && is_image($ekskul->foto)): ?>
                                    <img src="<?= BASE_URL . 'uploads/ekskul/' . $ekskul->foto; ?>" class="labs-ekskul-foto" alt="<?= _ent($ekskul->nama); ?>">
                                 <?php else: ?>
                                    <span class="labs-ekskul-foto-placeholder"><i class="fa fa-image"></i></span>
                                 <?php endif; ?>
                                 <div>
                                    <div class="labs-fw-semi"><?= _ent($ekskul->nama); ?></div>
                                 </div>
                              </div>
                           </td>
                           <td><?= _ent($ekskul->judul); ?></td>
                           <td><span class="labs-badge labs-badge--info"><?= strtoupper(_ent($ekskul->jenjang)); ?></span></td>
                           <td>
                              <div class="labs-fs-sm"><?= _ent($ekskul->hari); ?></div>
                              <div class="labs-fs-xs labs-text-muted"><?= date("H:i", strtotime($ekskul->jam)); ?> WIB</div>
                           </td>
                           <td class="labs-cell-numeric"><?= formatIDR($ekskul->nominal_biaya); ?></td>
                           <td>
                              <div class="labs-fs-sm"><?= _ent($ekskul->tahun_ajaran_aktif); ?></div>
                              <div class="labs-fs-xs labs-text-muted">Semester <?= ($ekskul->semester_aktif == 1) ? 'Ganjil' : 'Genap'; ?></div>
                           </td>
                           <td class="labs-cell-numeric">
                              <button type="button" class="labs-btn labs-btn--info labs-btn--sm btn_detail" data-id="<?= $ekskul->id_ekskul; ?>" title="Detail">
                                 <i class="fa fa-eye"></i>
                              </button>
                              <?php is_allowed('ekskul_update', function() use ($ekskul){ ?>
                              <a href="<?= site_url('administrator/ekskul/edit/' . $ekskul->id_ekskul); ?>" class="labs-btn labs-btn--warning labs-btn--sm" title="Edit">
                                 <i class="fa fa-edit"></i>
                              </a>
                              <?php }) ?>
                              <?php is_allowed('ekskul_delete', function() use ($ekskul){ ?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/ekskul/delete/' . $ekskul->id_ekskul); ?>" class="labs-btn labs-btn--danger labs-btn--sm remove-data" title="Hapus">
                                 <i class="fa fa-close"></i>
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if ($ekskul_counts == 0): ?>
                        <tr class="labs-empty-row">
                           <td colspan="8">
                              <div class="labs-empty">
                                 <i class="fa fa-inbox"></i>
                                 <p>Belum ada data ekstrakurikuler</p>
                              </div>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>
               </form>

               <!-- BULK + PAGINATION -->
               <div class="labs-flex-between labs-mt-4">
                  <div style="display:flex;gap:8px;align-items:center">
                     <select class="form-control" name="bulk" id="bulk" style="width:120px">
                        <option value="">Bulk</option>
                        <option value="delete">Delete</option>
                     </select>
                     <button type="button" class="labs-btn labs-btn--default labs-btn--sm" id="apply">Apply</button>
                  </div>
                  <div>
                     <div class="labs-text-muted labs-fs-sm labs-mb-2" style="text-align:right">
                        Total: <strong><?= $ekskul_counts; ?></strong> data
                     </div>
                     <?= $pagination; ?>
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL DETAIL EKSKUL -->
<div class="modal fade labs-modal" id="modalDetail" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-desktop"></i> Detail Ekstrakurikuler</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body" id="detail_content"></div>
         <div class="modal-footer">
            <button class="labs-btn labs-btn--default" data-dismiss="modal">
               <i class="fa fa-times"></i> Tutup
            </button>
            <a href="#" class="labs-btn labs-btn--warning" id="btn_modal_edit" style="display:none">
               <i class="fa fa-edit"></i> Edit
            </a>
         </div>
      </div>
   </div>
</div>

<!-- MODAL SETTING TAHUN & SEMESTER -->
<div class="modal fade labs-modal" id="modal_setting" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-cog"></i> Setting Ekstrakurikuler</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <form action="<?= base_url('administrator/ekskul/setting_update/'); ?>" method="post" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Jenjang</label>
                  <div class="col-xs-8">
                     <select name="jenjang" class="form-control">
                        <option value="sd">SD</option>
                        <option value="smp">SMP</option>
                        <option value="sma">SMA</option>
                        <option value="ft">FT</option>
                     </select>
                  </div>
               </div>
               <div class="form-group hidden">
                  <label class="control-label col-xs-3">Nama Bank</label>
                  <div class="col-xs-8">
                     <input class="form-control" type="text" name="bank" value="" />
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Tahun Ajaran</label>
                  <div class="col-xs-8">
                     <input class="form-control" type="text" name="tahun_ajaran_aktif" value="<?= date('Y') . "/" . date('Y', strtotime('+1 year')); ?>" />
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Semester</label>
                  <div class="col-xs-8">
                     <select name="semester_aktif" class="form-control">
                        <option value="1">Ganjil</option>
                        <option value="2">Genap</option>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <div class="col-xs-8 col-xs-offset-3">
                     <ul style="padding-left:16px;margin:0">
                        <li class="text-muted" style="font-weight:bold;font-size:12px">Setting berlaku untuk semua ekskul pada jenjang terpilih.</li>
                        <li class="text-muted" style="font-weight:bold;font-size:12px">Ganjil = 1, Genap = 2</li>
                     </ul>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button class="labs-btn labs-btn--default" data-dismiss="modal">Batal</button>
               <button type="submit" class="labs-btn labs-btn--info">Update</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- MODAL SETTING BANK -->
<div class="modal fade labs-modal" id="modal_bank" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-university"></i> Setting Bank & Pembayaran</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <form action="<?= base_url('administrator/ekskul/setting_bank_update/'); ?>" method="post" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Jenjang</label>
                  <div class="col-xs-8">
                     <select name="jenjang" class="form-control">
                        <option value="sd">SD</option>
                        <option value="smp">SMP</option>
                        <option value="sma">SMA</option>
                        <option value="ft">FT</option>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Nama Bank</label>
                  <div class="col-xs-8">
                     <input class="form-control" type="text" name="bank" value="" required />
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Rekening</label>
                  <div class="col-xs-8">
                     <input class="form-control" type="text" name="rekening" value="" required />
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Atas Nama</label>
                  <div class="col-xs-8">
                     <input class="form-control" type="text" name="atas_nama_rekening" value="" required />
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button class="labs-btn labs-btn--default" data-dismiss="modal">Batal</button>
               <button type="submit" class="labs-btn labs-btn--info">Update</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- SKELETON TEMPLATE -->
<script type="text/template" id="detail-skeleton">
<div class="labs-text-center" style="padding: 20px 0;">
   <div class="labs-skeleton labs-skeleton--circle" style="width:80px;height:80px;margin:0 auto 16px"></div>
   <div class="labs-skeleton labs-skeleton--lg labs-skeleton--block" style="width:40%;margin:0 auto 16px"></div>
   <div class="labs-skeleton labs-skeleton--block" style="height:120px"></div>
</div>
</script>

<script>
$(document).ready(function(){

   // ============ DETAIL MODAL ============
   $('.btn_detail').click(function(){
      var id = $(this).data('id');
      var content = $('#detail_content');
      content.html($('#detail-skeleton').html());
      $('#modalDetail').modal('show');

      $.ajax({
         url: '<?= site_url('administrator/ekskul/get_detail/'); ?>' + id,
         method: 'GET',
         dataType: 'json',
         success: function(data){
            if (!data || data.error) {
               content.html('<div class="labs-empty"><i class="fa fa-exclamation-circle" style="color:var(--labs-danger)"></i><p>' + (data.error || 'Data tidak ditemukan') + '</p></div>');
               return;
            }
            renderDetail(data);
         },
         error: function(){
            content.html('<div class="labs-empty"><i class="fa fa-exclamation-circle" style="color:var(--labs-danger)"></i><p>Gagal memuat data</p></div>');
         }
      });
   });

   // ============ REMOVE ============
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({
         title: "<?= cclang('are_you_sure'); ?>",
         text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
         cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
         closeOnConfirm: true,
         closeOnCancel: true
      },
      function(isConfirm){
         if (isConfirm) {
            document.location.href = url;
         }
      });
      return false;
   });

   // ============ BULK ============
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_ekskul').serialize();

      if (bulk.val() == 'delete') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
            cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
            closeOnConfirm: true,
            closeOnCancel: true
         },
         function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/ekskul/delete?' + serialize_bulk;
            }
         });
         return false;
      } else if(bulk.val() == '') {
         swal({
            title: "Upss",
            text: "<?= cclang('please_choose_bulk_action_first'); ?>",
            type: "warning",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Okay!",
            closeOnConfirm: true,
            closeOnCancel: true
         });
         return false;
      }
      return false;
   });

   // ============ CHECK ALL ============
   var checkAll = $('#check_all');
   var checkboxes = $('input.check');
   checkAll.on('ifChecked ifUnchecked', function(event) {
      if (event.type == 'ifChecked') { checkboxes.iCheck('check'); }
      else { checkboxes.iCheck('uncheck'); }
   });
   checkboxes.on('ifChanged', function(event){
      if(checkboxes.filter(':checked').length == checkboxes.length) {
         checkAll.prop('checked', 'checked');
      } else {
         checkAll.removeProp('checked');
      }
      checkAll.iCheck('update');
   });

   // ============ JENJANG FILTER ============
   $('#filterJenjang').change(function(){
      var jenjang = $(this).val();
      var q = $('input[name="q"]').val() || '';
      var f = $('select[name="f"]').val() || '';
      var url = '<?= base_url('administrator/ekskul'); ?>?';
      var params = [];
      if (q) params.push('q=' + encodeURIComponent(q));
      if (f) params.push('f=' + encodeURIComponent(f));
      if (jenjang) {
         params.push('f=jenjang');
         params.push('q=' + encodeURIComponent(jenjang));
      }
      window.location.href = url + params.join('&');
   });

}); /*end doc ready*/

// ============ RENDER DETAIL ============
function renderDetail(e){
   var html = '';

   // Header with foto
   html += '<div style="display:flex;gap:20px;align-items:center;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--labs-border)">';
   if (e.foto) {
      html += '<img src="<?= BASE_URL; ?>uploads/ekskul/' + e.foto + '" style="width:100px;height:100px;border-radius:12px;object-fit:cover" alt="' + (e.nama||'') + '">';
   } else {
      html += '<div style="width:100px;height:100px;border-radius:12px;background:var(--labs-bg-soft);display:flex;align-items:center;justify-content:center"><i class="fa fa-image" style="font-size:36px;color:var(--labs-text-light)"></i></div>';
   }
   html += '<div>';
   html += '  <div style="font-size:20px;font-weight:700;color:var(--labs-text)">' + (e.nama||'') + '</div>';
   html += '  <div style="font-size:14px;color:var(--labs-text-muted);margin-top:4px">' + (e.judul||'') + '</div>';
   html += '  <div style="margin-top:8px"><span class="labs-badge labs-badge--info">' + (e.jenjang||'').toUpperCase() + '</span></div>';
   html += '</div>';
   html += '</div>';

   // Tabs
   html += '<div class="labs-modal-tabs">';
   html += '  <div class="labs-modal-tabs__item active" data-tab="tab-info"><i class="fa fa-info-circle"></i> Informasi</div>';
   html += '  <div class="labs-modal-tabs__item" data-tab="tab-jadwal"><i class="fa fa-clock-o"></i> Jadwal & Biaya</div>';
   html += '  <div class="labs-modal-tabs__item" data-tab="tab-konten"><i class="fa fa-file-text-o"></i> Konten</div>';
   html += '</div>';

   // TAB INFO
   html += '<div class="labs-tab-content active" id="tab-info">';
   html += '  <div class="labs-modal-section">';
   html += '    <h4 class="labs-modal-section__title"><i class="fa fa-info-circle"></i> Informasi Umum</h4>';
   html += '    <div class="labs-info-grid">';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Nama</div><div class="labs-info-grid__value">' + (e.nama||'-') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Judul</div><div class="labs-info-grid__value">' + (e.judul||'-') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Jenjang</div><div class="labs-info-grid__value"><span class="labs-badge labs-badge--info">' + (e.jenjang||'-').toUpperCase() + '</span></div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Tahun Ajaran</div><div class="labs-info-grid__value">' + (e.tahun_ajaran_aktif||'-') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Semester</div><div class="labs-info-grid__value">' + (e.semester_aktif == 1 ? 'Ganjil' : (e.semester_aktif == 2 ? 'Genap' : '-')) + '</div></div>';
   html += '    </div>';
   html += '  </div>';
   html += '</div>';

   // TAB JADWAL & BIAYA
   html += '<div class="labs-tab-content" id="tab-jadwal">';
   html += '  <div class="labs-modal-section">';
   html += '    <h4 class="labs-modal-section__title"><i class="fa fa-clock-o"></i> Jadwal & Biaya</h4>';
   html += '    <div class="labs-info-grid">';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Hari</div><div class="labs-info-grid__value">' + (e.hari||'-') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Jam</div><div class="labs-info-grid__value">' + (e.jam ? e.jam.substring(0,5) + ' WIB' : '-') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Biaya</div><div class="labs-info-grid__value labs-fw-bold" style="color:var(--labs-primary)">Rp ' + (e.nominal_biaya ? Number(e.nominal_biaya).toLocaleString('id-ID') : '0') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Link Pendaftaran</div><div class="labs-info-grid__value">' + (e.link_daftar ? '<a href="' + e.link_daftar + '" target="_blank" class="labs-btn labs-btn--sm labs-btn--primary"><i class="fa fa-external-link"></i> Buka</a>' : '-') + '</div></div>';
   html += '    </div>';
   html += '  </div>';
   html += '  <div class="labs-modal-section">';
   html += '    <h4 class="labs-modal-section__title"><i class="fa fa-university"></i> Rekening Pembayaran</h4>';
   html += '    <div class="labs-info-grid">';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Bank</div><div class="labs-info-grid__value">' + (e.bank||'-') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">No. Rekening</div><div class="labs-info-grid__value">' + (e.rekening||'-') + '</div></div>';
   html += '      <div class="labs-info-grid__item"><div class="labs-info-grid__label">Atas Nama</div><div class="labs-info-grid__value">' + (e.atas_nama_rekening||'-') + '</div></div>';
   html += '    </div>';
   html += '  </div>';
   html += '</div>';

   // TAB KONTEN
   html += '<div class="labs-tab-content" id="tab-konten">';
   html += '  <div class="labs-modal-section">';
   html += '    <h4 class="labs-modal-section__title"><i class="fa fa-file-text-o"></i> Deskripsi</h4>';
   html += '    <div style="font-size:13px;line-height:1.7;color:var(--labs-text)">' + (e.konten||'<span class="labs-text-muted">Tidak ada deskripsi</span>') + '</div>';
   html += '  </div>';
   html += '</div>';

   $('#detail_content').html(html);

   // Edit button
   var $btnEdit = $('#btn_modal_edit');
   $btnEdit.attr('href', '<?= site_url('administrator/ekskul/edit/'); ?>' + e.id_ekskul).show();

   // Re-init labs-ui tabs
   if (window.LabsUI && LabsUI.initTabs) LabsUI.initTabs();
}
</script>
