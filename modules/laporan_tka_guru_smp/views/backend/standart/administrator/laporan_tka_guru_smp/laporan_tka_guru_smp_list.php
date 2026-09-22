<style>
.btn-action {
  border: 1px solid;
  background: transparent;
  transition: all 0.2s ease;
  margin: 1px;
  padding: 2px 6px;
  font-size: 11px;
  line-height: 1.4;
}
.btn-action-pdf { border-color: #e74c3c; color: #e74c3c !important; }
.btn-action-pdf:hover { background: #e74c3c; color: #fff !important; }
.btn-action-edit { border-color: #3498db; color: #3498db !important; }
.btn-action-edit:hover { background: #3498db; color: #fff !important; }
.btn-action-delete { border-color: #e67e22; color: #e67e22 !important; }
.btn-action-delete:hover { background: #e67e22; color: #fff !important; }
.btn-action-detail { border-color: #1abc9c; color: #1abc9c !important; }
.btn-action-detail:hover { background: #1abc9c; color: #fff !important; }
.btn-top { margin-right: 5px; border-radius: 3px; }
.btn-filter { background: #9b59b6; border-color: #9b59b6; color: #fff; }
.btn-filter:hover { background: #8e44ad; border-color: #8e44ad; color: #fff; }
.btn-export-disabled { opacity: 0.5; cursor: not-allowed; }
.filter-badge { display: inline-block; background: #27ae60; color: #fff; padding: 5px 12px; border-radius: 15px; margin-left: 10px; font-size: 12px; }
.filter-badge a { color: #fff; margin-left: 8px; }
.filter-badge a:hover { text-decoration: none; opacity: 0.8; }
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; }
.table td { font-size: 13px; vertical-align: middle; }
</style>

<section class="content-header">
   <h1><?= cclang('laporan_tka_guru_smp') ?> <small><?= cclang('list_all'); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('laporan_tka_guru_smp') ?></li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-list"></i> Data Laporan TKA Guru SMP
                  <span class="label bg-yellow" style="margin-left:10px"><?= $laporan_tka_guru_smp_counts; ?> Data</span>
                  <?php if(!empty($_GET['tahun'])): ?>
                  <span class="filter-badge">
                     <i class="fa fa-calendar"></i> Tahun: <?= $_GET['tahun']; ?>
                     <a href="<?= base_url('administrator/laporan_tka_guru_smp'); ?>" title="Hapus Filter"><i class="fa fa-times"></i></a>
                  </span>
                  <?php endif; ?>
               </h3>
               <div class="box-tools pull-right">
                  <a class="btn btn-sm btn-filter btn-top" data-toggle="modal" data-target="#modal_filter_tahun"><i class="fa fa-filter"></i> Filter</a>
                  <?php if(!empty($_GET['tahun'])): ?>
                  <a class="btn btn-sm btn-danger btn-top" id="btn_kosongkan"><i class="fa fa-trash"></i> Kosongkan Data</a>
                  <?php endif; ?>
                  <a class="btn btn-sm btn-success btn-top" data-toggle="modal" data-target="#modal_import"><i class="fa fa-upload"></i> Import</a>
                  <a class="btn btn-sm btn-warning btn-top" data-toggle="modal" data-target="#modal_import_detail"><i class="fa fa-file-excel-o"></i> Import Detail</a>
                  <?php is_allowed('laporan_tka_guru_smp_export', function(){?>
                  <a class="btn btn-sm btn-info btn-top btn-export <?= empty($_GET['tahun']) ? 'btn-export-disabled' : ''; ?>" id="btn_export" href="<?= site_url('administrator/laporan_tka_guru_smp/export?q='.$this->input->get('q').'&f='.$this->input->get('f').'&tahun='.$this->input->get('tahun')); ?>"><i class="fa fa-file-excel-o"></i> Export</a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">
               <form name="form_search" id="form_search" action="<?= base_url('administrator/laporan_tka_guru_smp/index'); ?>" method="get">
               <input type="hidden" name="tahun" value="<?= $this->input->get('tahun'); ?>">
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-5">
                     <div class="input-group">
                        <input type="text" class="form-control" name="q" placeholder="Cari data..." value="<?= $this->input->get('q'); ?>">
                        <span class="input-group-btn">
                           <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Cari</button>
                           <?php if(!empty($this->input->get('q'))): ?>
                           <a class="btn btn-flat btn-default" href="<?= base_url('administrator/laporan_tka_guru_smp?tahun='.$this->input->get('tahun')); ?>"><i class="fa fa-undo"></i></a>
                           <?php endif; ?>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control chosen chosen-select" name="f">
                        <option value="">Semua Kolom</option>
                        <option <?= $this->input->get('f') == 'guru' ? 'selected' :''; ?> value="guru">Guru</option>
                        <option <?= $this->input->get('f') == 'unit_kerja' ? 'selected' :''; ?> value="unit_kerja">Unit Kerja</option>
                        <option <?= $this->input->get('f') == 'mata_pelajaran' ? 'selected' :''; ?> value="mata_pelajaran">Mata Pelajaran</option>
                        <option <?= $this->input->get('f') == 'total_skor' ? 'selected' :''; ?> value="total_skor">Total Skor</option>
                     </select>
                  </div>
                  <div class="col-md-4 text-right">
                     <?php if(!empty($this->input->get('q'))): ?>
                     <span class="text-muted" style="line-height:34px">Hasil: <strong>"<?= $this->input->get('q'); ?>"</strong></span>
                     <?php endif; ?>
                  </div>
               </div>
               </form>

               <form name="form_laporan_tka_guru_smp" id="form_laporan_tka_guru_smp" action="">
               <input type="hidden" name="tahun" value="<?= $this->input->get('tahun'); ?>">
               <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover">
                  <thead>
                     <tr>
                        <th width="30"><input type="checkbox" class="flat-red" id="check_all"></th>
                        <th>Guru</th>
                        <th>NPP</th>
                        <th>Unit Kerja</th>
                        <th>Mata Pelajaran</th>
                        <th>B.Indo</th>
                        <th>B.Ing</th>
                        <th>Numerasi</th>
                        <th>Skor</th>
                        <th>Rerata Sekolah</th>
                        <th>Rerata Unit</th>
                        <th>Rank</th>
                        <th>Rank Unit</th>
                        <th>Tahun</th>
                        <th width="130">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                  <?php if($laporan_tka_guru_smp_counts > 0): ?>
                     <?php foreach($laporan_tka_guru_smps as $row): ?>
                     <tr>
                        <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $row->id_laporan; ?>"></td>
                        <td><?= $row->guru_smp_nama_lengkap ?? '-'; ?></td>
                        <td><?= _ent($row->npp ?? '-'); ?></td>
                        <td><?= _ent($row->unit_kerja); ?></td>
                        <td><?= _ent($row->mata_pelajaran); ?></td>
                        <td class="text-center"><?= _ent($row->bhs_indonesia); ?></td>
                        <td class="text-center"><?= _ent($row->bhs_inggris); ?></td>
                        <td class="text-center"><?= _ent($row->numerasi); ?></td>
                        <td class="text-center"><strong><?= _ent($row->total_skor); ?></strong></td>
                        <td class="text-center"><?= _ent($row->rerata_sekolah); ?></td>
                        <td class="text-center"><?= _ent($row->rerata_unit); ?></td>
                        <td class="text-center"><?= _ent($row->rank); ?></td>
                        <td class="text-center"><?= _ent($row->rank_unit); ?></td>
                        <td class="text-center"><?= _ent($row->tahun); ?></td>
                        <td style="white-space:nowrap">
                           <a target="_blank" href="<?= site_url('apiapp/export_tka_guru?tipe_guru=guru_smp&id='.$row->id_laporan); ?>" class="btn btn-action btn-action-pdf btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</a>
                           <?php is_allowed('laporan_tka_guru_smp_view', function() use ($row){?>
                           <a href="javascript:void(0);" data-id="<?= $row->id_laporan; ?>" class="btn btn-action btn-action-detail btn-xs btn-detail-nilai" title="Detail Nilai"><i class="fa fa-eye"></i></a>
                           <?php }) ?>
                           <?php is_allowed('laporan_tka_guru_smp_update', function() use ($row){?>
                           <a href="<?= site_url('administrator/laporan_tka_guru_smp/edit/'.$row->id_laporan); ?>" class="btn btn-action btn-action-edit btn-xs"><i class="fa fa-edit"></i> Edit</a>
                           <?php }) ?>
                           <?php is_allowed('laporan_tka_guru_smp_delete', function() use ($row){?>
                           <a href="javascript:void(0);" data-href="<?= site_url('administrator/laporan_tka_guru_smp/delete/'.$row->id_laporan); ?>" class="btn btn-action btn-action-delete btn-xs remove-data"><i class="fa fa-trash"></i></a>
                           <?php }) ?>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <tr>
                        <td colspan="15" class="text-center" style="padding:30px">
                           <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                           <span style="color:#999">Data tidak ditemukan</span>
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
                        <span class="input-group-btn">
                           <button type="button" class="btn btn-flat btn-default" id="apply">Terapkan</button>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-6 text-right"><?= $pagination; ?></div>
               </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL FILTER -->
<div class="modal fade" id="modal_filter_tahun" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
         <div class="modal-header" style="background:#9b59b6;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-filter"></i> Filter Berdasarkan Tahun</h4>
         </div>
         <div class="modal-body">
            <div class="form-group">
               <label>Tahun</label>
               <select id="select_filter_tahun" class="form-control">
                  <option value="">-- Semua Tahun --</option>
                  <?php if(!empty($list_tahun)): foreach($list_tahun as $t): ?>
                     <option value="<?= $t['tahun']; ?>" <?= ($this->input->get('tahun') == $t['tahun']) ? 'selected' : ''; ?>><?= $t['tahun']; ?></option>
                  <?php endforeach; endif; ?>
               </select>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-flat" style="background:#9b59b6;color:#fff" id="btn_apply_filter"><i class="fa fa-check"></i> Terapkan</button>
         </div>
      </div>
   </div>
</div>

<!-- MODAL IMPORT -->
<div class="modal fade" id="modal_import" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#27ae60;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-upload"></i> Import Data Laporan TKA</h4>
         </div>
         <form action="<?= base_url('administrator/laporan_tka_guru_smp/import'); ?>" method="post" enctype="multipart/form-data">
            <div class="modal-body">
               <div class="form-group">
                  <label>Upload File Excel</label>
                  <input type="file" class="form-control" name="file_upload" required accept=".xlsx,.xls">
                  <small class="help-block">Format: .xlsx atau .xls</small>
               </div>
               <div class="form-group">
                  <label>Tahun</label>
                  <input type="text" class="form-control" name="tahun" placeholder="Contoh: <?= date('Y') ?>" required>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-upload"></i> Import</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- MODAL IMPORT DETAIL -->
<div class="modal fade" id="modal_import_detail" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background:#f39c12;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-file-excel-o"></i> Import Detail Nilai TKA</h4>
         </div>
         <form id="formImportDetail" enctype="multipart/form-data">
            <div class="modal-body">
               <div class="form-group">
                  <label>Upload File Excel</label>
                  <input type="file" class="form-control" name="file_import_detail" accept=".xlsx,.xls" required>
                  <small class="help-block">Kolom header: Unit Kerja, Nama Guru, Skor, No-1, No-2, dst.</small>
               </div>
               <div class="form-group">
                  <label>Tahun</label>
                  <input type="text" class="form-control" name="tahun_detail" placeholder="Contoh: <?= date('Y') ?>" required>
               </div>
               <div id="importDetailResult" style="display:none;"></div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-warning btn-flat" id="btnImportDetail"><i class="fa fa-upload"></i> Import Detail</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- MODAL DETAIL NILAI -->
<div class="modal fade" id="modal_detail_nilai" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header" style="background:#1abc9c;color:#fff">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
            <h4 class="modal-title"><i class="fa fa-eye"></i> Detail Nilai TKA</h4>
         </div>
         <div class="modal-body">
            <div id="detailNilaiLoading" class="text-center" style="padding:30px">
               <i class="fa fa-spinner fa-spin" style="font-size:30px;color:#1abc9c"></i><br>
               <span class="text-muted">Memuat data...</span>
            </div>
            <div id="detailNilaiContent" style="display:none"></div>
            <div id="detailNilaiEmpty" style="display:none" class="text-center">
               <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
               <span class="text-muted">Belum ada data detail nilai.</span>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Tutup</button>
         </div>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){

   // Import Detail AJAX
   $('#formImportDetail').on('submit', function(e){
      e.preventDefault();
      var formData = new FormData(this);
      var btn = $('#btnImportDetail');
      var result = $('#importDetailResult');
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Mengimpor...');
      result.hide();
      $.ajax({
         url: BASE_URL + '/administrator/laporan_tka_guru_smp/import_detail',
         type: 'POST', data: formData, processData: false, contentType: false, dataType: 'json',
         success: function(res){
            result.show();
            if(res.success){
               result.html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + res.message + '</div>');
               setTimeout(function(){ window.location.reload(); }, 3000);
            } else {
               result.html('<div class="alert alert-danger">' + res.message + '</div>');
            }
         },
         error: function(){ result.show().html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Terjadi kesalahan server.</div>'); },
         complete: function(){ btn.prop('disabled', false).html('<i class="fa fa-upload"></i> Import Detail'); }
      });
   });

   // Detail Nilai AJAX
   $(document).on('click', '.btn-detail-nilai', function(){
      var id = $(this).data('id');
      $('#modal_detail_nilai').modal('show');
      $('#detailNilaiLoading').show();
      $('#detailNilaiContent').hide().empty();
      $('#detailNilaiEmpty').hide();
      $.ajax({
         url: BASE_URL + '/administrator/laporan_tka_guru_smp/detail_nilai/' + id,
         type: 'GET', dataType: 'json',
         success: function(res){
            $('#detailNilaiLoading').hide();
            if(res.success && res.details && res.details.length > 0){
               var html = '<div class="table-responsive">';
               html += '<p><strong>Nama:</strong> ' + (res.nama_guru||'-') + ' &nbsp;|&nbsp; <strong>Unit:</strong> ' + (res.unit_kerja||'-') + ' &nbsp;|&nbsp; <strong>Tahun:</strong> ' + (res.tahun||'-') + '</p>';
               html += '<table class="table table-bordered table-striped table-hover"><thead><tr><th width="50">No</th><th width="100">No Urut</th><th>Kategori</th><th>Indikator Soal</th><th width="100">Keterangan</th></tr></thead><tbody>';
               for(var i=0;i<res.details.length;i++){
                  var d=res.details[i];
                  var badge=(d.keterangan==='Benar')?'<span class="label label-success">Benar</span>':'<span class="label label-danger">Salah</span>';
                  html+='<tr><td class="text-center">'+(i+1)+'</td><td class="text-center">'+(d.no_urut||'-')+'</td><td><span class="label label-primary">'+(d.judul_kategori||'-')+'</span></td><td>'+(d.soal||'-')+'</td><td class="text-center">'+badge+'</td></tr>';
               }
               html+='</tbody></table></div>';
               $('#detailNilaiContent').html(html).show();
            } else { $('#detailNilaiEmpty').show(); }
         },
         error: function(){ $('#detailNilaiLoading').hide(); $('#detailNilaiContent').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Gagal memuat data.</div>').show(); }
      });
   });

   // Filter
   $('#btn_apply_filter').on('click', function() {
      var tahun = $('#select_filter_tahun').val();
      var url = '<?= base_url('administrator/laporan_tka_guru_smp'); ?>';
      if (tahun) url += '?tahun=' + tahun;
      window.location.href = url;
   });

   var urlParams = new URLSearchParams(window.location.search);
   var currentTahun = urlParams.get('tahun');
   if (!currentTahun) $('#btn_export').addClass('btn-export-disabled');

   $('#btn_export').on('click', function(e) {
      if (!currentTahun) { e.preventDefault(); swal({ title: "Perhatian", text: "Pilih filter tahun terlebih dahulu.", type: "warning" }); return false; }
   });

   $('#btn_kosongkan').on('click', function() {
      swal({ title: "Hapus Semua Data " + currentTahun + "?", text: "Data yang dihapus tidak dapat dikembalikan!", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "Ya, Hapus!", cancelButtonText: "Batal" }, function(isConfirm){
         if (isConfirm) { var form = $('<form>', { method: 'POST', action: '<?= base_url('administrator/laporan_tka_guru_smp/delete_by_tahun'); ?>' }); form.append($('<input>', {type: 'hidden', name: 'tahun', value: currentTahun})); form.appendTo('body').submit(); }
      });
   });

   $('.remove-data').click(function(){
      var url = $(this).data('href');
      swal({ title: "<?= cclang('are_you_sure'); ?>", text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "<?= cclang('yes_delete_it'); ?>" }, function(isConfirm){ if (isConfirm) document.location.href = url; });
      return false;
   });

   $('#apply').click(function(){
      var bulk = $('#bulk').val();
      if (!bulk) { swal({ title: "Pilih aksi", type: "warning" }); return false; }
      if (bulk == 'delete') {
         var ids = [];
         $('input.check:checked').each(function(){ ids.push($(this).val()); });
         if (ids.length === 0) { swal({ title: "Pilih data", type: "warning" }); return false; }
         swal({ title: "<?= cclang('are_you_sure'); ?>", text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "<?= cclang('yes_delete_it'); ?>" }, function(isConfirm){ if (isConfirm) $('#form_laporan_tka_guru_smp').attr('action', '<?= base_url('administrator/laporan_tka_guru_smp/delete'); ?>').submit(); });
      }
      return false;
   });

   $('#check_all').on('ifChanged', function(){ $('input.check').iCheck($(this).is(':checked') ? 'check' : 'uncheck'); });
});
</script>
