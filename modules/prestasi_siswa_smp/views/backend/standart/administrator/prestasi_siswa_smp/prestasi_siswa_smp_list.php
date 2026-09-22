<style>
/* Action Buttons */
.btn-action {
  border: 1px solid;
  background: transparent;
  transition: all 0.2s ease;
  margin: 2px 1px;
  padding: 4px 10px;
  font-size: 12px;
  border-radius: 3px;
}
.btn-action i { margin-right: 4px; }
.btn-action-view { border-color: #3498db; color: #3498db !important; }
.btn-action-view:hover { background: #3498db; color: #fff !important; }
.btn-action-edit { border-color: #f39c12; color: #f39c12 !important; }
.btn-action-edit:hover { background: #f39c12; color: #fff !important; }
.btn-action-delete { border-color: #e74c3c; color: #e74c3c !important; }
.btn-action-delete:hover { background: #e74c3c; color: #fff !important; }

/* Top Buttons */
.btn-top { margin-right: 5px; border-radius: 3px; }

/* Table Styling */
.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; }
.table td { font-size: 13px; vertical-align: middle; }

/* Filter tambahan box */
.filter-tambahan-box { border: 1px solid #f0ad4e; border-radius: 4px; margin-bottom: 15px; }
.filter-tambahan-box .box-title { font-size: 14px; font-weight: 600; }
.filter-tambahan-box .box-body { padding: 12px; background: #fcf8e3; }
.filter-row { margin-bottom: 8px; }
.filter-row label { margin-bottom: 2px; }

/* Status label kecil */
.status-label { font-size: 10px; padding: 2px 6px; }

/* Badge pusprenas */
.badge-pusprenas { font-size: 10px; padding: 2px 6px; }
</style>

<script type="text/javascript">
</script>

<!-- Content Header -->
<style>
.datepicker, .datepicker-dropdown { z-index: 1050 !important; }
.filter-tambahan-box { overflow: visible !important; }
@media (max-width: 768px) {
   .content-header > h1 { font-size:18px; }
   .table { font-size:12px; }
   .table th, .table td { white-space:nowrap; }
   .filter-tambahan-box .box-body { padding:8px; }
   .filter-row .form-control { font-size:12px; }
   .info-box-rekap { min-height:60px; padding:8px; }
   .info-box-rekap .value { font-size:18px; }
   .btn-block-mobile { width:100%; margin-bottom:5px; }
}
</style>
<section class="content-header">
   <h1>
      <?= cclang('prestasi_siswa_smp'); ?> <small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('prestasi_siswa_smp'); ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">

            <!-- Box Header -->
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-trophy"></i> <?= cclang('prestasi_siswa_smp'); ?>
                  <span class="label bg-yellow" style="margin-left:10px"><?= $prestasi_siswa_smp_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('prestasi_siswa_smp_add', function(){?>
                  <a class="btn btn-sm btn-success btn-top" id="btn_add_new" title="<?= cclang('add_new_button', array(cclang('prestasi_siswa_smp'))); ?> (Ctrl+a)" href="<?= site_url('administrator/prestasi_siswa_smp/add'); ?>">
                     <i class="fa fa-plus"></i> <?= cclang('add_new_button', array(cclang('prestasi_siswa_smp'))); ?>
                  </a>
                  <?php }) ?>
                  <?php is_allowed('prestasi_siswa_smp_export', function(){?>
                  <a class="btn btn-sm btn-success btn-top" title="<?= cclang('export'); ?> <?= cclang('prestasi_siswa_smp'); ?>" href="<?= site_url('administrator/prestasi_siswa_smp/export') . '?' . http_build_query($this->input->get()); ?>">
                     <i class="fa fa-file-excel-o"></i> <?= cclang('export'); ?> XLS
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">

               <?php
               $has_active_filter = !empty($filters['id_siswa'])
                   || !empty($filters['jenis_prestasi_id'])
                   || !empty($filters['id_prestasi_bidang'])
                   || (isset($filters['is_approved']) && $filters['is_approved'] !== '')
                   || !empty($filters['tgl_raih_from'])
                   || !empty($filters['tgl_raih_to'])
                   || !empty($filters['kurasi_pusprenas']);
               ?>

               <form name="form_prestasi_siswa_smp" id="form_prestasi_siswa_smp" action="<?= base_url('administrator/prestasi_siswa_smp/index'); ?>">

               <!-- Search Row -->
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-5">
                     <div class="input-group">
                        <input type="text" class="form-control" name="q" id="filter" placeholder="Cari data prestasi..." value="<?= htmlspecialchars($this->input->get('q'), ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="input-group-btn">
                           <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Cari</button>
                           <?php if(!empty($this->input->get('q'))): ?>
                           <a class="btn btn-flat btn-default" href="<?= base_url('administrator/prestasi_siswa_smp'); ?>"><i class="fa fa-undo"></i></a>
                           <?php endif; ?>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control chosen chosen-select" name="f" id="field">
                        <option value=""><?= cclang('all'); ?></option>
                        <option value="nama_prestasi" <?= ($this->input->get('f') == 'nama_prestasi') ? 'selected' : ''; ?>><?= cclang('field_nama_prestasi'); ?></option>
                        <option value="nama_lengkap" <?= ($this->input->get('f') == 'nama_lengkap') ? 'selected' : ''; ?>><?= cclang('field_nama_lengkap'); ?></option>
                        <option value="kelas" <?= ($this->input->get('f') == 'kelas') ? 'selected' : ''; ?>><?= cclang('field_kelas'); ?></option>
                        <option value="juara" <?= ($this->input->get('f') == 'juara') ? 'selected' : ''; ?>><?= cclang('field_juara'); ?></option>
                        <option value="konten" <?= ($this->input->get('f') == 'konten') ? 'selected' : ''; ?>><?= cclang('field_konten'); ?></option>
                     </select>
                  </div>
                  <div class="col-md-4 text-right">
                     <?php if(!empty($this->input->get('q'))): ?>
                     <span class="text-muted" style="line-height:34px">
                        Hasil pencarian: <strong>"<?= htmlspecialchars($this->input->get('q'), ENT_QUOTES, 'UTF-8'); ?>"</strong>
                     </span>
                     <?php endif; ?>
                  </div>
               </div>

               <!-- Filter Tambahan Box (collapsible) -->
               <div class="box box-warning box-solid filter-tambahan-box <?= $has_active_filter ? '' : 'collapsed-box'; ?>">
                  <div class="box-header with-border">
                     <h3 class="box-title"><i class="fa fa-filter"></i> <?= cclang('filter_tambahan'); ?></h3>
                     <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                           <i class="fa <?= $has_active_filter ? 'fa-minus' : 'fa-plus'; ?>"></i>
                        </button>
                     </div>
                  </div>
                  <div class="box-body" style="display: <?= $has_active_filter ? 'block' : 'none'; ?>">
                     <div class="row filter-row">
                        <div class="col-md-3">
                           <label style="font-size:12px">Siswa</label>
                           <select class="form-control chosen chosen-select" name="id_siswa" id="filter_id_siswa">
                              <option value=""><?= cclang('semua'); ?></option>
                              <?php if(!empty($dropdown_siswa)): foreach($dropdown_siswa as $id => $nama): ?>
                              <option value="<?= $id; ?>" <?= ((int)$this->input->get('id_siswa') === (int)$id) ? 'selected' : ''; ?>><?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?></option>
                              <?php endforeach; endif; ?>
                           </select>
                        </div>
                        <div class="col-md-3">
                           <label style="font-size:12px">Jenis Prestasi</label>
                           <select class="form-control chosen chosen-select" name="jenis_prestasi_id" id="filter_jenis">
                              <option value=""><?= cclang('semua'); ?></option>
                              <?php if(!empty($dropdown_jenis)): foreach($dropdown_jenis as $id => $nama): ?>
                              <option value="<?= $id; ?>" <?= ((int)$this->input->get('jenis_prestasi_id') === (int)$id) ? 'selected' : ''; ?>><?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?></option>
                              <?php endforeach; endif; ?>
                           </select>
                        </div>
                        <div class="col-md-3">
                           <label style="font-size:12px">Bidang Prestasi</label>
                           <select class="form-control chosen chosen-select" name="id_prestasi_bidang" id="filter_bidang">
                              <option value=""><?= cclang('semua'); ?></option>
                              <?php if(!empty($dropdown_bidang)): foreach($dropdown_bidang as $id => $nama): ?>
                              <option value="<?= $id; ?>" <?= ((int)$this->input->get('id_prestasi_bidang') === (int)$id) ? 'selected' : ''; ?>><?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?></option>
                              <?php endforeach; endif; ?>
                           </select>
                        </div>
                        <div class="col-md-3">
                           <label style="font-size:12px">Status</label>
                           <select class="form-control" name="is_approved" id="filter_status">
                              <option value=""><?= cclang('semua'); ?></option>
                              <option value="1" <?= ($this->input->get('is_approved') === '1') ? 'selected' : ''; ?>><?= cclang('disetujui'); ?></option>
                              <option value="0" <?= ($this->input->get('is_approved') === '0') ? 'selected' : ''; ?>><?= cclang('belum_disetujui'); ?></option>
                              <option value="2" <?= ($this->input->get('is_approved') === '2') ? 'selected' : ''; ?>><?= cclang('ditolak'); ?></option>
                           </select>
                        </div>
                     </div>

                     <div class="row filter-row">
                        <div class="col-md-3">
                           <label style="font-size:12px"><?= cclang('tgl_raih_dari'); ?></label>
                           <input type="text" class="form-control datepicker" name="tgl_raih_from" id="filter_tgl_from" placeholder="dd/mm/yyyy" autocomplete="off" value="<?= !empty($filters['tgl_raih_from']) ? date('d/m/Y', strtotime($filters['tgl_raih_from'])) : ''; ?>">
                        </div>
                        <div class="col-md-3">
                           <label style="font-size:12px"><?= cclang('tgl_raih_hingga'); ?></label>
                           <input type="text" class="form-control datepicker" name="tgl_raih_to" id="filter_tgl_to" placeholder="dd/mm/yyyy" autocomplete="off" value="<?= !empty($filters['tgl_raih_to']) ? date('d/m/Y', strtotime($filters['tgl_raih_to'])) : ''; ?>">
                        </div>
                        <div class="col-md-3">
                           <label style="font-size:12px">Kurasi Pusprenas</label>
                           <select class="form-control" name="kurasi_pusprenas" id="filter_kurasi">
                              <option value=""><?= cclang('semua'); ?></option>
                              <option value="YA" <?= ($this->input->get('kurasi_pusprenas') == 'YA') ? 'selected' : ''; ?>><?= cclang('ya'); ?></option>
                              <option value="TIDAK" <?= ($this->input->get('kurasi_pusprenas') == 'TIDAK') ? 'selected' : ''; ?>><?= cclang('tidak'); ?></option>
                           </select>
                        </div>
                        <div class="col-md-3 text-right" style="padding-top:22px">
                           <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-check"></i> <?= cclang('terapkan_filter'); ?></button>
                           <a class="btn btn-default btn-flat" href="<?= base_url('administrator/prestasi_siswa_smp'); ?>"><i class="fa fa-undo"></i> <?= cclang('reset_semua'); ?></a>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Data Table -->
               <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover dataTable">
                     <thead>
                        <tr>
                           <th width="30">
                              <input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all">
                           </th>
                           <th style="text-align:center">Nama Prestasi</th>
                           <th style="text-align:center">Tgl Raih</th>
                           <th style="text-align:center">Siswa</th>
                           <th style="text-align:center">Kelas</th>
                           <th style="text-align:center">Jenis</th>
                           <th style="text-align:center">Juara</th>
                           <th style="text-align:center">Pusprenas</th>
                           <th style="text-align:center">Foto</th>
                           <th style="text-align:center" width="280">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_prestasi_siswa_smp">
                     <?php if ($prestasi_siswa_smp_counts > 0): ?>
                     <?php foreach($prestasi_siswa_smps as $row): ?>
                        <tr>
                           <td>
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $row->id_prestasi; ?>">
                           </td>
                           <td>
                              <?= _ent($row->nama_prestasi); ?>
                              <?php if(!empty($row->judul)): ?>
                              <br><small class="text-muted"><?= _ent($row->judul); ?></small>
                              <?php endif; ?>
                           </td>
                           <td style="text-align:center;white-space:nowrap">
                              <?= _ent($row->tgl_raih); ?>
                           </td>
                           <td>
                              <?php if (!empty($row->id_siswa)): ?>
                              <?= anchor('administrator/siswa_smp_aktif/view/'.$row->id_siswa.'?popup=show', $row->siswa_smp_aktif_nama_lengkap, array('class' => 'popup-view')); ?>
                              <?php else: ?>
                              <span class="text-muted">-</span>
                              <?php endif; ?>
                              <br>
                              <?php
                                $st = isset($row->is_approved) ? (int)$row->is_approved : 0;
                                $st_label = '';
                                $st_class = '';
                                if ($st === 1) { $st_label = 'Disetujui'; $st_class = 'label-success'; }
                                elseif ($st === 2) { $st_label = 'Ditolak'; $st_class = 'label-danger'; }
                                else { $st_label = 'Belum Disetujui'; $st_class = 'label-warning'; }
                              ?>
                              <span class="label <?= $st_class; ?> status-label"><?= $st_label; ?></span>
                           </td>
                           <td style="text-align:center"><?= !empty($row->kelas_smp_label) ? _ent($row->kelas_smp_label) : '<span class="text-muted">-</span>'; ?></td>
                           <td><?= !empty($row->jenis_prestasi) ? _ent($row->jenis_prestasi) : '<span class="text-muted">-</span>'; ?></td>
                           <td style="text-align:center"><strong><?= _ent($row->juara); ?></strong></td>
                           <td style="text-align:center">
                              <?php
                                $pusprenas_val = isset($row->kurasi_pusprenas) ? strtoupper($row->kurasi_pusprenas) : 'TIDAK';
                                if ($pusprenas_val === 'YA') {
                                    echo '<span class="label label-success badge-pusprenas"><i class="fa fa-check-circle"></i> Ya</span>';
                                    if (!empty($row->link_pusprenas)) {
                                        echo '<br><a href="' . _ent($row->link_pusprenas) . '" target="_blank" class="text-primary" style="font-size:11px"><i class="fa fa-external-link"></i> Link</a>';
                                    }
                                } else {
                                    echo '<span class="label label-default badge-pusprenas">Tidak</span>';
                                }
                              ?>
                           </td>
                           <td style="text-align:center">
                              <?php if (!empty($row->foto_prestasi)): ?>
                                <?php if (is_image($row->foto_prestasi)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_siswa_smp/' . $row->foto_prestasi; ?>">
                                  <img src="<?= BASE_URL . 'uploads/prestasi_siswa_smp/' . $row->foto_prestasi; ?>" alt="foto" width="40px">
                                </a>
                                <?php else: ?>
                                <a href="<?= BASE_URL . 'uploads/prestasi_siswa_smp/' . $row->foto_prestasi; ?>">
                                  <img src="<?= get_icon_file($row->foto_prestasi); ?>" alt="foto" width="40px">
                                </a>
                                <?php endif; ?>
                              <?php else: ?>
                              <span class="text-muted">-</span>
                              <?php endif; ?>
                           </td>
                           <td style="white-space:nowrap;text-align:center">
                              <?php is_allowed('prestasi_siswa_smp_view', function() use ($row){?>
                              <a href="<?= site_url('administrator/prestasi_siswa_smp/view/' . $row->id_prestasi); ?>" class="btn btn-action btn-action-view btn-sm">
                                 <i class="fa fa-newspaper-o"></i> Detail
                              </a>
                              <?php }) ?>
                              <?php is_allowed('prestasi_siswa_smp_update', function() use ($row){?>
                              <a href="<?= site_url('administrator/prestasi_siswa_smp/edit/' . $row->id_prestasi); ?>" class="btn btn-action btn-action-edit btn-sm">
                                 <i class="fa fa-edit"></i> Edit
                              </a>
                              <?php }) ?>
                              <?php is_allowed('prestasi_siswa_smp_delete', function() use ($row){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/prestasi_siswa_smp/delete/' . $row->id_prestasi); ?>" class="btn btn-action btn-action-delete btn-sm remove-data">
                                 <i class="fa fa-trash"></i> Hapus
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="10" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">Data prestasi siswa SMP tidak tersedia</span>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <!-- Bulk Action & Pagination -->
               <div class="row" style="margin-top:15px">
                  <div class="col-md-6">
                     <div class="input-group" style="max-width:360px">
                        <select class="form-control" name="bulk" id="bulk">
                           <option value="">-- Bulk Action --</option>
                           <option value="disetujui">Disetujui</option>
                           <option value="ditolak">Ditolak</option>
                           <option value="delete">Hapus Terpilih</option>
                        </select>
                        <span class="input-group-btn">
                           <button type="button" class="btn btn-flat btn-default" id="apply">Terapkan</button>
                        </span>
                        <input type="hidden" id="st" name="st">
                     </div>
                  </div>
                  <div class="col-md-6 text-right">
                     <div class="dataTables_paginate paging_simple_numbers">
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
               </form>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- Page script -->
<script>
$(document).ready(function(){

   // Init datepicker
   if ($.fn.datepicker) {
      $('.datepicker').datepicker({
         format: 'dd/mm/yyyy',
         autoclose: true,
         todayHighlight: true
      });
      // hilangkan tooltip URL/ghost yang ditambahkan datepicker
      $('.datepicker').removeAttr('title');
   } else {
      $('.datepicker').attr('type', 'date');
   }

   // Convert datepicker dd/mm/yyyy -> Y-m-d sebelum submit
   $('#form_prestasi_siswa_smp').on('submit', function(e) {
      var fromEl = $('#filter_tgl_from');
      var toEl = $('#filter_tgl_to');
      var from = fromEl.val();
      var to = toEl.val();
      if (from && from.indexOf('/') !== -1) {
         var p = from.split('/');
         if (p.length === 3) fromEl.val(p[2] + '-' + p[1] + '-' + p[0]);
      }
      if (to && to.indexOf('/') !== -1) {
         var p = to.split('/');
         if (p.length === 3) toEl.val(p[2] + '-' + p[1] + '-' + p[0]);
      }
   });

   // Delete single
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
      }, function(isConfirm){
         if (isConfirm) {
            document.location.href = url;
         }
      });
      return false;
   });

   // Bulk action
   $('#apply').click(function(){
      var bulk = $('#bulk');
      // ponytail: serialize hanya id tercentang + bulk + st, jangan seluruh form filter (cegah URL overflow)
      var checked_ids = $('#form_prestasi_siswa_smp input[name="id[]"]:checked').serialize();
      var st_val = '';
      if (bulk.val() == 'disetujui') {
         st_val = '1';
      } else if (bulk.val() == 'ditolak') {
         st_val = '2';
      }
      var serialize_bulk = checked_ids
         + '&bulk=' + encodeURIComponent(bulk.val())
         + '&st=' + encodeURIComponent(st_val);

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
         }, function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/prestasi_siswa_smp/delete?' + serialize_bulk;
            }
         });
         return false;
      }
      else if (bulk.val() == 'disetujui') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "Data berikut akan disetujui?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, disetujui",
            cancelButtonText: "Tidak, batal",
            closeOnConfirm: true,
            closeOnCancel: true
         }, function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/prestasi_siswa_smp/update_status?' + serialize_bulk;
            }
         });
         return false;
      }
      else if (bulk.val() == 'ditolak') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "Data berikut akan ditolak?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, ditolak",
            cancelButtonText: "Tidak, batal",
            closeOnConfirm: true,
            closeOnCancel: true
         }, function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/prestasi_siswa_smp/update_status?' + serialize_bulk;
            }
         });
         return false;
      }
      else if (bulk.val() == '') {
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

   // Check all
   var checkAll = $('#check_all');
   var checkboxes = $('input.check');

   checkAll.on('ifChecked ifUnchecked', function(event){
      if (event.type == 'ifChecked') {
         checkboxes.iCheck('check');
      } else {
         checkboxes.iCheck('uncheck');
      }
   });

   checkboxes.on('ifChanged', function(event){
      if(checkboxes.filter(':checked').length == checkboxes.length) {
         checkAll.prop('checked', 'checked');
      } else {
         checkAll.removeProp('checked');
      }
      checkAll.iCheck('update');
   });

});
</script>

<script>
$(function(){
   // Re-init datepicker when filter box expanded (fix visual bug)
   $('.filter-tambahan-box').on('shown.bs.collapse', function(){
      $('.datepicker').datepicker('update');
   });
});
</script>
