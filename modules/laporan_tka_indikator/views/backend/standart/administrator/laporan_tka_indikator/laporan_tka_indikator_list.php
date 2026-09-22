<style>
/* Action Buttons */
.btn-action {
  border: 1px solid;
  background: transparent;
  transition: all 0.2s ease;
  margin: 0 2px;
}
.btn-action-edit { border-color: #3498db; color: #3498db !important; }
.btn-action-edit:hover { background: #3498db; color: #fff !important; }
.btn-action-delete { border-color: #e67e22; color: #e67e22 !important; }
.btn-action-delete:hover { background: #e67e22; color: #fff !important; }

/* Top Buttons */
.btn-top { margin-right: 5px; border-radius: 3px; }
.btn-filter { background: #9b59b6; border-color: #9b59b6; color: #fff; }
.btn-filter:hover { background: #8e44ad; border-color: #8e44ad; color: #fff; }
.btn-export-disabled { opacity: 0.5; cursor: not-allowed; }

/* Active Filter Badge */
.filter-badge {
  display: inline-block;
  background: #27ae60;
  color: #fff;
  padding: 5px 12px;
  border-radius: 15px;
  margin-left: 10px;
  font-size: 12px;
}
.filter-badge a { color: #fff; margin-left: 8px; }
.filter-badge a:hover { text-decoration: none; opacity: 0.8; }

/* Table Styling */
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; }
.table td { font-size: 13px; vertical-align: middle; }
</style>

<script type="text/javascript">
</script>

<section class="content-header">
   <h1>
      <?= cclang('laporan_tka_indikator') ?> <small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('laporan_tka_indikator') ?></li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            
            <!-- Box Header with Action Buttons -->
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-list"></i> Data Laporan TKA Indikator
                  <span class="label bg-yellow" style="margin-left:10px"><?= $laporan_tka_indikator_counts; ?> Data</span>
                  <?php if(!empty($filter_tahun)): ?>
                  <span class="filter-badge">
                     <i class="fa fa-calendar"></i> Tahun: <?= $filter_tahun; ?>
                     <a href="<?= base_url('administrator/laporan_tka_indikator?' . http_build_query(array_merge($this->input->get(), ['tahun' => '']))) ?>" title="Hapus Filter"><i class="fa fa-times"></i></a>
                  </span>
                  <?php endif; ?>
                  <?php if(!empty($filter_kategori)): ?>
                  <span class="filter-badge">
                     <i class="fa fa-tag"></i> <?= $filter_kategori; ?>
                     <a href="<?= base_url('administrator/laporan_tka_indikator?' . http_build_query(array_merge($this->input->get(), ['kategori' => '']))) ?>" title="Hapus Filter"><i class="fa fa-times"></i></a>
                  </span>
                  <?php endif; ?>
               </h3>
               <div class="box-tools pull-right">
                  <a class="btn btn-sm btn-filter btn-top" title="Filter" data-toggle="modal" data-target="#modalFilter">
                     <i class="fa fa-filter"></i> Filter
                  </a>
                  <?php if(!empty($filter_tahun) || !empty($filter_kategori)): ?>
                  <a class="btn btn-sm btn-danger btn-top" id="btn_kosongkan" title="Hapus semua data sesuai filter">
                     <i class="fa fa-trash"></i> Kosongkan Data
                  </a>
                  <?php endif; ?>
                  <a class="btn btn-sm btn-success btn-top" title="Import Data" data-toggle="modal" data-target="#modalImport">
                     <i class="fa fa-upload"></i> Import
                  </a>
                  <?php is_allowed('laporan_tka_indikator_export', function() use ($filter_tahun, $filter_kategori) {?>
                  <?php if(!empty($filter_tahun) || !empty($filter_kategori)): ?>
                  <a class="btn btn-sm btn-info btn-top" title="Export Excel" href="<?= site_url('administrator/laporan_tka_indikator/export?' . http_build_query(['tahun' => $filter_tahun, 'kategori' => $filter_kategori])); ?>">
                     <i class="fa fa-file-excel-o"></i> Export
                  </a>
                  <?php else: ?>
                  <a class="btn btn-sm btn-info btn-top btn-export-disabled" title="Pilih filter terlebih dahulu">
                     <i class="fa fa-file-excel-o"></i> Export
                  </a>
                  <?php endif; ?>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">
               
               <!-- Search Form -->
               <form name="form_search" id="form_search" action="<?= base_url('administrator/laporan_tka_indikator/index'); ?>" method="get">
               <?php if (!empty($filter_tahun)): ?><input type="hidden" name="tahun" value="<?= $filter_tahun ?>"><?php endif; ?>
               <?php if (!empty($filter_kategori)): ?><input type="hidden" name="kategori" value="<?= $filter_kategori ?>"><?php endif; ?>
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-5">
                     <div class="input-group">
                        <input type="text" class="form-control" name="q" id="filter" placeholder="Cari data..." value="<?= $this->input->get('q'); ?>">
                        <span class="input-group-btn">
                           <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Cari</button>
                           <?php if(!empty($this->input->get('q'))): ?>
                           <a class="btn btn-flat btn-default" href="<?= base_url('administrator/laporan_tka_indikator?' . http_build_query(['tahun' => $filter_tahun, 'kategori' => $filter_kategori])); ?>"><i class="fa fa-undo"></i></a>
                           <?php endif; ?>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control chosen chosen-select" name="f" id="field">
                        <option value="">Semua Kolom</option>
                        <option <?= $this->input->get('f') == 'judul_kategori' ? 'selected' :''; ?> value="judul_kategori">Kategori</option>
                        <option <?= $this->input->get('f') == 'soal' ? 'selected' :''; ?> value="soal">Soal</option>
                        <option <?= $this->input->get('f') == 'no_urut' ? 'selected' :''; ?> value="no_urut">No Urut</option>
                        <option <?= $this->input->get('f') == 'tahun' ? 'selected' :''; ?> value="tahun">Tahun</option>
                     </select>
                  </div>
                  <div class="col-md-4 text-right">
                     <?php if(!empty($this->input->get('q'))): ?>
                     <span class="text-muted" style="line-height:34px">
                        Hasil pencarian: <strong>"<?= $this->input->get('q'); ?>"</strong>
                     </span>
                     <?php endif; ?>
                  </div>
               </div>
               </form>

               <!-- Data Table -->
               <form name="form_laporan_tka_indikator" id="form_laporan_tka_indikator" action="">
               <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover">
                  <thead>
                     <tr>
                        <th width="30"><input type="checkbox" class="flat-red" id="check_all"></th>
                        <th width="50">No</th>
                        <th width="80">No Urut</th>
                        <th>Kategori</th>
                        <th>Indikator Soal</th>
                        <th width="80">Tahun</th>
                        <th width="140">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                  <?php 
                  $no = ($this->input->get('page')) ? ($this->input->get('page') - 1) * $this->limit_page : 0;
                  if($laporan_tka_indikator_counts > 0): 
                     foreach($laporan_tka_indikators as $row): 
                        $no++;
                  ?>
                     <tr>
                        <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $row->id_indikator; ?>"></td>
                        <td class="text-center"><?= $no; ?></td>
                        <td class="text-center"><?= _ent($row->no_urut); ?></td>
                        <td><span class="label label-primary"><?= _ent($row->judul_kategori); ?></span></td>
                        <td><?= _ent($row->soal); ?></td>
                        <td class="text-center"><?= _ent($row->tahun); ?></td>
                        <td>
                           <?php is_allowed('laporan_tka_indikator_update', function() use ($row){?>
                           <a href="<?= site_url('administrator/laporan_tka_indikator/edit/'.$row->id_indikator); ?>" class="btn btn-action btn-action-edit btn-xs"><i class="fa fa-edit"></i> Edit</a>
                           <?php }) ?>
                           <?php is_allowed('laporan_tka_indikator_delete', function() use ($row){?>
                           <a href="javascript:void(0);" data-href="<?= site_url('administrator/laporan_tka_indikator/delete/'.$row->id_indikator); ?>" class="btn btn-action btn-action-delete btn-xs remove-data"><i class="fa fa-trash"></i> Hapus</a>
                           <?php }) ?>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <tr>
                        <td colspan="7" class="text-center" style="padding:30px">
                           <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                           <span style="color:#999">Data tidak ditemukan</span>
                        </td>
                     </tr>
                  <?php endif; ?>
                  </tbody>
               </table>
               </div>

               <!-- Bulk Action & Pagination -->
               <div class="row" style="margin-top:15px">
                  <div class="col-md-6">
                     <div class="input-group" style="max-width:300px">
                        <select class="form-control" name="bulk" id="bulk">
                           <option value="">-- Bulk Action --</option>
                           <option value="delete">Hapus Terpilih</option>
                        </select>
                        <span class="input-group-btn">
                           <button type="button" class="btn btn-flat btn-default" id="apply">Terapkan</button>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-6 text-right">
                     <?= $pagination; ?>
                  </div>
               </div>
               </form>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL FILTER -->
<div class="modal fade" id="modalFilter" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#9b59b6;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-filter"></i> Filter Data</h4>
         </div>
         <form id="formFilter" action="<?= base_url('administrator/laporan_tka_indikator'); ?>" method="GET">
            <?php if (!empty($this->input->get('q'))): ?><input type="hidden" name="q" value="<?= $this->input->get('q') ?>"><?php endif; ?>
            <?php if (!empty($this->input->get('f'))): ?><input type="hidden" name="f" value="<?= $this->input->get('f') ?>"><?php endif; ?>
            <div class="modal-body">
               <div class="form-group">
                  <label><i class="fa fa-calendar"></i> Tahun</label>
                  <select class="form-control" name="tahun">
                     <option value="">-- Semua Tahun --</option>
                     <?php foreach($list_tahun as $t): ?>
                        <option value="<?= $t->tahun ?>" <?= ($filter_tahun == $t->tahun) ? 'selected' : ''; ?>><?= $t->tahun ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="form-group">
                  <label><i class="fa fa-tag"></i> Kategori Indikator</label>
                  <select class="form-control" name="kategori">
                     <option value="">-- Semua Kategori --</option>
                     <?php foreach($list_kategori as $k): ?>
                        <option value="<?= $k->judul_kategori ?>" <?= ($filter_kategori == $k->judul_kategori) ? 'selected' : ''; ?>><?= $k->judul_kategori ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-flat" style="background:#9b59b6;color:#fff"><i class="fa fa-check"></i> Terapkan</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- MODAL IMPORT -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#27ae60;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-upload"></i> Import Data TKA Indikator</h4>
         </div>
         <form id="formImport" enctype="multipart/form-data">
            <div class="modal-body">
               <div class="form-group">
                  <label>Upload File Excel</label>
                  <input type="file" class="form-control" name="file_import" id="file_import" accept=".xlsx,.xls" required>
                  <small class="help-block">Format: .xlsx atau .xls. Kolom: No Urut, Indikator Soal, Kategori, Tahun.</small>
               </div>
               <div class="form-group">
                  <a href="<?= site_url('administrator/laporan_tka_indikator/download_sample'); ?>" class="btn btn-flat btn-sm btn-default">
                     <i class="fa fa-download"></i> Unduh Contoh File
                  </a>
               </div>
               <div id="importResult" style="display:none;"></div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-success btn-flat" id="btnImport"><i class="fa fa-upload"></i> Import</button>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){
   
   // Delete single
   $('.remove-data').click(function(){
      var url = $(this).data('href');
      swal({
         title: "<?= cclang('are_you_sure'); ?>",
         text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
         cancelButtonText: "<?= cclang('no_cancel_plx'); ?>"
      }, function(isConfirm){
         if (isConfirm) {
            document.location.href = url;
         }
      });
      return false;
   });

   // Kosongkan Data
   $('#btn_kosongkan').on('click', function() {
      var tahun = '<?= $filter_tahun ?>';
      var kategori = '<?= $filter_kategori ?>';
      var count = '<?= $laporan_tka_indikator_counts ?>';
      var filterText = [];
      if (tahun) filterText.push('Tahun: ' + tahun);
      if (kategori) filterText.push('Kategori: ' + kategori);
      
      swal({
         title: "Hapus " + count + " data?",
         text: "Filter: " + filterText.join(', ') + "\nData yang dihapus tidak dapat dikembalikan!",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Ya, Hapus!",
         cancelButtonText: "Batal"
      }, function(isConfirm){
         if (isConfirm) {
            $.ajax({
               url: BASE_URL + '/administrator/laporan_tka_indikator/clear_filtered',
               type: 'POST',
               data: { tahun: tahun, kategori: kategori },
               dataType: 'json',
               success: function(res){
                  if(res.success){
                     swal({ title: 'Berhasil!', text: res.message, type: 'success' }, function(){ window.location.reload(); });
                  } else {
                     swal({ title: 'Gagal!', text: res.message, type: 'error' });
                  }
               },
               error: function(){
                  swal({ title: 'Error!', text: 'Terjadi kesalahan.', type: 'error' });
               }
            });
         }
      });
   });

   // Bulk action
   $('#apply').click(function(){
      var bulk = $('#bulk').val();
      if (!bulk) {
         swal({ title: "Pilih aksi terlebih dahulu", type: "warning" });
         return false;
      }
      if (bulk == 'delete') {
         var ids = [];
         $('input.check:checked').each(function(){ ids.push($(this).val()); });
         if (ids.length === 0) {
            swal({ title: "Pilih data terlebih dahulu", type: "warning" });
            return false;
         }
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?= cclang('yes_delete_it'); ?>"
         }, function(isConfirm){
            if (isConfirm) {
               $('#form_laporan_tka_indikator').attr('action', '<?= base_url('administrator/laporan_tka_indikator/delete'); ?>').submit();
            }
         });
      }
      return false;
   });

   // Check all
   $('#check_all').on('ifChanged', function(){
      $('input.check').iCheck($(this).is(':checked') ? 'check' : 'uncheck');
   });

   // Import AJAX
   $('#formImport').on('submit', function(e){
      e.preventDefault();
      var formData = new FormData(this);
      var btn = $('#btnImport');
      var result = $('#importResult');
      
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Mengimpor...');
      result.hide();
      
      $.ajax({
         url: BASE_URL + '/administrator/laporan_tka_indikator/import',
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         dataType: 'json',
         success: function(res){
            result.show();
            if(res.success){
               var cls = res.has_issue ? 'alert-warning' : 'alert-success';
               var icon = res.has_issue ? 'fa-exclamation-triangle' : 'fa-check-circle';
               var html = '<div class="alert ' + cls + '"><i class="fa ' + icon + '"></i> ' + res.message;
               if (res.detail) html += '<br><br>' + res.detail;
               html += '</div>';
               result.html(html);
               setTimeout(function(){ window.location.reload(); }, 3000);
            } else {
               result.html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + res.message + '</div>');
            }
         },
         error: function(){
            result.show().html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Terjadi kesalahan.</div>');
         },
         complete: function(){
            btn.prop('disabled', false).html('<i class="fa fa-upload"></i> Import');
         }
      });
   });
});
</script>
