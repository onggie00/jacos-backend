<style>
.content-header > h1 { font-size:22px; font-weight:600; }
.content-header > h1 > small { font-size:13px; color:#777; }
.breadcrumb { background:transparent; }
.box.box-warning { border-top-color:#f39c12; }
.table { margin-bottom:0; }
.table th { background:#f8f9fa; font-weight:600; font-size:12px; vertical-align:middle; text-align:center; white-space:nowrap; }
.table td { font-size:13px; vertical-align:middle; }
.table-striped > tbody > tr:nth-of-type(odd) { background:#fafbfc; }
.table-hover > tbody > tr:hover { background:#eef7ff; }
.label { padding:3px 8px; border-radius:3px; color:#fff; font-size:11px; display:inline-block; }
.text-muted { color:#999; }
.filter-bar { margin-bottom:15px; padding:12px 15px; background:#f8f9fa; border-radius:5px; border:1px solid #e0e0e0; }
.filter-bar .form-group { margin-bottom:8px; margin-right:8px; }
.filter-bar label { font-weight:600; font-size:12px; color:#555; margin-right:5px; }
.filter-bar .form-control { font-size:13px; }
.info-box-custom { min-height:100px; border-radius:6px; margin-bottom:12px; display:flex; align-items:center; padding:15px 20px; box-shadow:0 2px 4px rgba(0,0,0,0.08); color:#fff; position:relative; overflow:hidden; }
.info-box-custom .info-icon { font-size:50px; opacity:0.35; position:absolute; right:20px; top:50%; transform:translateY(-50%); }
.info-box-custom .info-label { font-size:13px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.95; margin-bottom:5px; display:block; font-weight:600; color:#fff; }
.info-box-custom .info-value { font-size:28px; font-weight:700; line-height:1.2; display:block; color:#fff; }
.bg-grad-blue { background:linear-gradient(135deg,#3c8dbc 0%,#367fa9 100%); }
.bg-grad-green { background:linear-gradient(135deg,#00a65a 0%,#008d4c 100%); }
.bg-grad-purple { background:linear-gradient(135deg,#9b59b6 0%,#8e44ad 100%); }
.filter-chip { display:inline-block; padding:3px 8px; margin:2px; font-size:11px; background:#3498db; color:#fff; border-radius:3px; }
</style>

<script type="text/javascript">
<?php if ($this->session->flashdata('success')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('success'))); ?>
   toastr.success("<?= $msg; ?>", "Berhasil", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } else if ($this->session->flashdata('error')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('error'))); ?>
   toastr.error("<?= $msg; ?>", "Error", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } ?>
</script>

<section class="content-header">
   <h1><i class="fa fa-th-large"></i> Kelas SD <small>Daftar Kelas Siswa</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Kelas SD</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-list"></i> Data Kelas <span class="label bg-yellow" style="margin-left:8px"><?= (int)$kelas_ft_counts; ?> Data</span></h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('kelas_ft_add', function(){ ?>
                  <a class="btn btn-sm btn-success" title="Tambah Kelas (Ctrl+a)" href="<?= site_url('administrator/kelas_ft/add'); ?>">
                     <i class="fa fa-plus"></i> Tambah Kelas
                  </a>
                  <?php }) ?>
                  <?php is_allowed('kelas_ft_export', function(){ ?>
                  <a class="btn btn-sm btn-info" href="<?= site_url('administrator/kelas_ft/export'); ?>">
                     <i class="fa fa-file-excel-o"></i> Export XLS
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">

               <form method="get" action="<?= base_url('administrator/kelas_ft/index'); ?>" class="form-inline filter-bar">
                  <div class="form-group">
                     <label>Cari:</label>
                     <input type="text" id="filter_q" name="q" value="<?= htmlspecialchars(isset($filter['q']) ? $filter['q'] : ''); ?>" class="form-control input-sm" placeholder="Nama Kelas / Tingkatan" style="width:200px">
                  </div>
                  <div class="form-group">
                     <label>Field:</label>
                     <select name="f" class="form-control input-sm">
                        <option value="">- Semua -</option>
                        <option value="nama_kelas" <?= (isset($filter['f']) && $filter['f']=='nama_kelas') ? 'selected' : ''; ?>>Nama Kelas</option>
                        <option value="id_tingkatan" <?= (isset($filter['f']) && $filter['f']=='id_tingkatan') ? 'selected' : ''; ?>>Tingkatan</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label>Tingkatan:</label>
                     <select name="id_tingkatan" class="form-control input-sm">
                        <option value="">- Semua -</option>
                        <?php if (!empty($list_tingkatan)): foreach($list_tingkatan as $t): ?>
                        <option value="<?= $t->id_tingkatan_ft; ?>" <?= (isset($filter['id_tingkatan']) && $filter['id_tingkatan']==$t->id_tingkatan_ft) ? 'selected' : ''; ?>><?= htmlspecialchars($t->label); ?></option>
                        <?php endforeach; endif; ?>
                     </select>
                  </div>
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Terapkan</button>
                  <a href="<?= base_url('administrator/kelas_ft'); ?>" class="btn btn-default btn-sm"><i class="fa fa-times"></i> Reset</a>
               </form>

               <?php
               $chips = array();
               if (!empty($filter['q'])) $chips[] = 'Pencarian: "'.htmlspecialchars($filter['q']).'"';
               if (!empty($filter['f'])) {
                  $f_labels = array('nama_kelas'=>'Nama Kelas', 'id_tingkatan'=>'Tingkatan');
                  if (isset($f_labels[$filter['f']])) $chips[] = 'Field: '.$f_labels[$filter['f']];
               }
               if (!empty($filter['id_tingkatan'])) {
                  $t_label = '';
                  if (!empty($list_tingkatan)) {
                     foreach($list_tingkatan as $t) { if ($t->id_tingkatan_ft == $filter['id_tingkatan']) { $t_label = $t->label; break; } }
                  }
                  if ($t_label) $chips[] = 'Tingkatan: '.$t_label;
               }
               ?>
               <?php if (!empty($chips)): ?>
               <div style="margin-bottom:12px;padding:8px 12px;background:#e8f4fc;border-radius:3px;border:1px solid #bee5eb">
                  <i class="fa fa-info-circle" style="color:#3498db"></i>
                  <small style="color:#2c3e50"><strong>Filter aktif:</strong>
                  <?php foreach($chips as $c): ?>
                     <span class="filter-chip"><?= $c; ?></span>
                  <?php endforeach; ?>
                  </small>
               </div>
               <?php endif; ?>

               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-4">
                     <div class="info-box-custom bg-grad-blue">
                        <i class="fa fa-users info-icon"></i>
                        <div><span class="info-label">Total Kelas</span><span class="info-value"><?= (int)$kelas_ft_counts; ?></span></div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="info-box-custom bg-grad-green">
                        <i class="fa fa-layer-group info-icon"></i>
                        <div><span class="info-label">Tingkatan</span><span class="info-value"><?= count($list_tingkatan); ?></span></div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="info-box-custom bg-grad-purple">
                        <i class="fa fa-th info-icon"></i>
                        <div><span class="info-label">Info</span><span class="info-value">-</span></div>
                     </div>
                  </div>
               </div>

               <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr>
                           <th width="40">No</th>
                           <th width="5"><input type="checkbox" class="flat-red" id="check_all" title="Pilih semua"></th>
                           <th>Tingkatan</th>
                           <th>Nama Kelas</th>
                           <th>Label</th>
                           <th width="130">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php if (!empty($kelas_fts)): $no = (int)$offset + 1; foreach($kelas_fts as $ks): ?>
                        <tr>
                           <td style="text-align:center"><?= $no++; ?></td>
                           <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $ks->id_kelas_ft; ?>"></td>
                           <td>
                              <?php if (!empty($ks->id_tingkatan)): ?>
                                 <a href="javascript:void(0);" class="popup-view" data-id="<?= $ks->id_tingkatan; ?>">
                                    <i class="fa fa-layer-group text-muted"></i> <?= htmlspecialchars($ks->tingkatan_ft_label); ?>
                                 </a>
                              <?php else: ?>
                                 <span class="text-muted">-</span>
                              <?php endif; ?>
                           </td>
                           <td><i class="fa fa-th-large text-muted"></i> <?= htmlspecialchars($ks->nama_kelas); ?></td>
                           <td><code><?= htmlspecialchars($ks->label); ?></code></td>
                           <td style="text-align:center">
                              <?php is_allowed('kelas_ft_view', function() use ($ks){?>
                              <a href="javascript:void(0);" class="btn btn-xs btn-info btn-detail-modal" data-id="<?= $ks->id_kelas_ft; ?>" title="Lihat detail"><i class="fa fa-eye"></i></a>
                              <?php }) ?>
                              <?php is_allowed('kelas_ft_update', function() use ($ks){?>
                              <a href="<?= site_url('administrator/kelas_ft/edit/' . $ks->id_kelas_ft); ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-edit"></i></a>
                              <?php }) ?>
                              <?php is_allowed('kelas_ft_delete', function() use ($ks){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/kelas_ft/delete/' . $ks->id_kelas_ft); ?>" class="btn btn-xs btn-danger remove-data" title="Hapus"><i class="fa fa-trash"></i></a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; else: ?>
                        <tr>
                           <td colspan="6" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">
                                 <?php if (!empty($filter)): ?>Data kelas tidak ditemukan untuk filter yang dipilih.<?php else: ?>Data kelas SD belum tersedia.<?php endif; ?>
                              </span>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <div class="row" style="margin-top:12px">
                  <div class="col-md-6">
                     <div class="col-sm-2 padd-left-0">
                        <select type="text" class="form-control input-sm chosen chosen-select" name="bulk" id="bulk" placeholder="Bulk Action">
                           <option value="">Bulk Action</option>
                           <option value="delete">Delete</option>
                        </select>
                     </div>
                     <div class="col-sm-2 padd-left-0">
                        <button type="button" class="btn btn-flat btn-sm" name="apply" id="apply">Apply</button>
                     </div>
                     <small class="text-muted" style="margin-left:15px">Menampilkan <?= count($kelas_fts); ?> dari <?= (int)$kelas_ft_counts; ?> data</small>
                  </div>
                  <div class="col-md-6 text-right">
                     <div class="dataTables_paginate paging_simple_numbers"><?= $pagination; ?></div>
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>
</section>

<div class="modal fade" id="modalDetailKelas" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md">
      <div class="modal-content" style="border-radius:5px;overflow:hidden">
         <div class="modal-header" style="background:linear-gradient(135deg,#f39c12,#e67e22);color:#fff;padding:15px 20px">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8">&times;</button>
            <h4 class="modal-title" style="font-weight:600"><i class="fa fa-th-large"></i> Detail Kelas</h4>
         </div>
         <div class="modal-body" id="detail_kelas_content" style="padding:20px;background:#f8f9fa">
            <div class="text-center" style="padding:40px"><i class="fa fa-spinner fa-spin" style="font-size:40px;color:#f39c12"></i><p style="margin-top:10px;color:#666">Memuat detail kelas...</p></div>
         </div>
         <div class="modal-footer" style="background:#fff;padding:12px 20px">
            <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
         </div>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({ title: "<?= cclang('are_you_sure'); ?>", text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "<?= cclang('yes_delete_it'); ?>", cancelButtonText: "<?= cclang('no_cancel_plx'); ?>", closeOnConfirm: true, closeOnCancel: true }, function(isConfirm){ if (isConfirm) { document.location.href = url; } });
      return false;
   });
   $('#apply').click(function(){
      var bulk = $('#bulk');
      if (bulk.val() == 'delete') {
         var checked = $('input.check:checked');
         if (checked.length === 0) { swal({ title: "Upss", text: "Pilih data dulu.", type: "warning", confirmButtonColor: "#DD6B55", confirmButtonText: "Okay!" }); return false; }
         var ids = [];
         checked.each(function() { ids.push($(this).val()); });
         swal({ title: "<?= cclang('are_you_sure'); ?>", text: "Data terpilih akan dihapus.", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "<?= cclang('yes_delete_it'); ?>", cancelButtonText: "<?= cclang('no_cancel_plx'); ?>", closeOnConfirm: true, closeOnCancel: true }, function(isConfirm){ if (isConfirm) { $.get(BASE_URL + '/administrator/kelas_ft/delete', { id: ids }, function(){ location.reload(); }); } });
      } else if (bulk.val() === '') {
         swal({ title: "Upss", text: "Pilih action dulu.", type: "warning", confirmButtonColor: "#DD6B55", confirmButtonText: "Okay!" });
      }
      return false;
   });
   $('#check_all').on('ifChecked ifUnchecked', function(event) {
      if (event.type == 'ifChecked') { $('input.check').iCheck('check'); } else { $('input.check').iCheck('uncheck'); }
   });
   $('input.check').on('ifChanged', function(){
      if ($('input.check').filter(':checked').length == $('input.check').length) { $('#check_all').prop('checked', 'checked'); } else { $('#check_all').removeProp('checked'); }
      $('#check_all').iCheck('update');
   });
   $('a.popup-view').click(function(e){
      e.preventDefault();
      var id = $(this).data('id');
      window.open('<?= base_url('administrator/tingkatan_ft/view/'); ?>' + id + '?popup=show', '_blank');
   });
   $('.btn-detail-modal').click(function(){
      var id = $(this).data('id');
      $('#detail_kelas_content').html('<div class="text-center" style="padding:40px"><i class="fa fa-spinner fa-spin" style="font-size:40px;color:#f39c12"></i><p style="margin-top:10px;color:#666">Memuat detail...</p></div>');
      $('#modalDetailKelas').modal('show');
   });
});
</script>
