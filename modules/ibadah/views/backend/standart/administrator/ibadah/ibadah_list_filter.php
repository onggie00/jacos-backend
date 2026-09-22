<?php
// Inisial untuk avatar
function ibadah_filter_initials($name) {
    $name = trim($name);
    if ($name === '') return '?';
    $parts = preg_split('/\s+/', $name);
    if (count($parts) >= 2) {
        return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
    }
    return strtoupper(mb_substr($parts[0], 0, 2));
}

$get = $this->input->get();
$jenjang_label = strtoupper($jenjang);
?>

<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<section class="content-header">
   <h1>
      Presensi Ibadah
      <small class="labs-text-muted">Filter Presensi per Jenjang</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/ibadah'); ?>">Ibadah</a></li>
      <li class="active">Filter Presensi</li>
   </ol>
</section>

<section class="content">

   <!-- STAT CARDS -->
   <div class="row">
      <div class="col-md-3 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--primary">
               <i class="fa fa-bookmark"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Total Presensi</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->total); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--success">
               <i class="fa fa-users"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Data Siswa Ibadah</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->siswa_unik); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--warning">
               <i class="fa fa-calendar"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Periode</div>
               <div class="labs-stat-card__value" style="font-size:14px">
                  <?php if ($stats->tanggal_mulai && $stats->tanggal_akhir): ?>
                     <?= formatTanggal($stats->tanggal_mulai); ?> - <?= formatTanggal($stats->tanggal_akhir); ?>
                  <?php else: ?>
                     -
                  <?php endif; ?>
               </div>
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
                  Presensi Ibadah - <?= $jenjang_label; ?>
                  <span class="labs-badge labs-badge--default labs-ml-2"><?= $value_counts; ?> data</span>
               </h3>
               <div>
                  <a class="labs-btn labs-btn--info labs-btn--sm" title="Filter Presensi Ibadah" data-toggle="modal" data-target="#modal_filter">
                     <i class="fa fa-users"></i> Filter Presensi
                  </a>
                  <?php is_allowed('ibadah_export', function() use ($get) {?>
                  <a class="labs-btn labs-btn--success labs-btn--sm" title="Export Excel" 
                     href="<?= site_url('administrator/ibadah/export?' . http_build_query($get)); ?>">
                     <i class="fa fa-file-excel-o"></i> Export XLS
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="labs-card__body">

               <!-- FILTER INFO (static) -->
               <?php
                  $has_filter = !empty($get['id_tahun_ajaran']) || !empty($get['start_date']) || !empty($get['id_tingkatan']) || !empty($get['id_kelas']);
                  $tingkatan_label = '';
                  $kelas_label = '';
                  $tahun_label = '';
                  if (!empty($get['id_tingkatan']) && !empty($jenjang)) {
                     $r = $this->mymodel->withquery("SELECT label FROM tingkatan_{$jenjang} WHERE id_tingkatan_{$jenjang} = '" . $this->db->escape_str($get['id_tingkatan']) . "'", 'row');
                     if ($r) $tingkatan_label = $r->label;
                  }
                  if (!empty($get['id_kelas']) && !empty($jenjang)) {
                     $r = $this->mymodel->withquery("SELECT label FROM kelas_{$jenjang} WHERE id_kelas_{$jenjang} = '" . $this->db->escape_str($get['id_kelas']) . "'", 'row');
                     if ($r) $kelas_label = $r->label;
                  }
                  if (!empty($get['id_tahun_ajaran'])) {
                     $r = $this->mymodel->withquery("SELECT label FROM tahun_ajaran WHERE id_tahun_ajaran = '" . $this->db->escape_str($get['id_tahun_ajaran']) . "'", 'row');
                     if ($r) $tahun_label = $r->label;
                  }
               ?>
               <?php if ($has_filter): ?>
               <div style="margin-bottom:16px;padding:10px 16px;background:#F5F5F4;border-radius:8px;display:flex;gap:12px;flex-wrap:wrap;align-items:center">
                  <span class="labs-text-muted labs-fs-sm"><i class="fa fa-filter"></i> Filter aktif:</span>
                  <?php if (!empty($get['jenjang'])): ?>
                     <span class="labs-badge labs-badge--default">Jenjang: <?= strtoupper(_ent($get['jenjang'])); ?></span>
                  <?php endif; ?>
                  <?php if (!empty($tahun_label)): ?>
                     <span class="labs-badge labs-badge--info">Tahun Ajaran: <?= _ent($tahun_label); ?></span>
                  <?php endif; ?>
                  <?php if (!empty($tingkatan_label)): ?>
                     <span class="labs-badge labs-badge--info">Tingkatan: <?= _ent($tingkatan_label); ?></span>
                  <?php endif; ?>
                  <?php if (!empty($kelas_label)): ?>
                     <span class="labs-badge labs-badge--info">Kelas: <?= _ent($kelas_label); ?></span>
                  <?php endif; ?>
                  <?php if (!empty($get['start_date']) && !empty($get['end_date'])): ?>
                     <span class="labs-badge labs-badge--info"><?= formatTanggal($get['start_date']); ?> - <?= formatTanggal($get['end_date']); ?></span>
                  <?php endif; ?>
               </div>
               <?php endif; ?>

               <!-- TABLE -->
               <form name="form_presensi" id="form_presensi" method="POST">
               <div class="labs-table-wrap">
                  <div class="labs-table-scroll">
                  <table class="labs-table">
                     <thead>
                        <tr>
                           <th style="width:30px"><input type="checkbox" id="check_all" title="Pilih semua"></th>
                           <th>Siswa</th>
                           <th>Kelas</th>
                           <th>Jenjang</th>
                           <th>Jenis Ibadah</th>
                           <th>Tanggal</th>
                           <th>Waktu</th>
                           <th>Rakaat</th>
                           <th>Keterangan</th>
                           <th class="labs-cell-numeric">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php foreach($data_presensi as $value):
                        $nama = '';
                        if ($value->jenjang == 'ft') $nama = $value->siswa_ft_aktif_nama_lengkap;
                        elseif ($value->jenjang == 'sma') $nama = $value->siswa_sma_aktif_nama_lengkap;
                        elseif ($value->jenjang == 'smp') $nama = $value->siswa_smp_aktif_nama_lengkap;
                        $initials = ibadah_filter_initials($nama);
                     ?>
                        <tr>
                           <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $value->id; ?>"></td>
                           <td>
                              <div class="labs-name-cell">
                                 <span class="labs-avatar"><?= $initials; ?></span>
                                 <div>
                                    <?php if ($value->id_siswa_aktif): ?>
                                       <?php
                                          $view_url = 'administrator/siswa_' . $value->jenjang . '_aktif/view/' . $value->id_siswa_aktif . '?popup=show';
                                       ?>
                                       <div class="labs-fw-semi"><?= anchor($view_url, _ent($nama), array('class' => 'popup-view')); ?></div>
                                    <?php else: ?>
                                       <div class="labs-fw-semi labs-text-muted">-</div>
                                    <?php endif; ?>
                                 </div>
                              </div>
                           </td>
                           <td><?= _ent($value->nama_kelas); ?></td>
                           <td><span class="labs-badge labs-badge--info"><?= strtoupper(_ent($value->jenjang)); ?></span></td>
                           <td>
                              <?php if (!empty($value->ibadah_setting_ibadah)): ?>
                                 <?= anchor('administrator/ibadah_setting/view/' . $value->id_ibadah . '?popup=show', $value->ibadah_setting_ibadah, array('class' => 'popup-view')); ?>
                              <?php else: ?>
                                 <span class="labs-cell-muted">-</span>
                              <?php endif; ?>
                           </td>
                           <td>
                              <div class="labs-fs-sm"><?= formatTanggal($value->tanggal); ?></div>
                           </td>
                           <td>
                              <div class="labs-fs-sm"><?= date("H:i", strtotime($value->waktu)); ?> WIB</div>
                           </td>
                           <td><?= _ent($value->jumlah_rakaat); ?></td>
                           <td><?= _ent($value->keterangan); ?></td>
                           <td class="labs-cell-numeric">
                              <?php is_allowed('ibadah_update', function() use ($value){?>
                              <a href="<?= site_url('administrator/ibadah/edit/' . $value->id); ?>" class="labs-btn labs-btn--default labs-btn--sm" title="Edit">
                                 <i class="fa fa-edit"></i>
                              </a>
                              <?php }) ?>
                              <?php is_allowed('ibadah_delete', function() use ($value){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/ibadah/delete/' . $value->id); ?>" class="labs-btn labs-btn--danger labs-btn--sm remove-data" title="Hapus">
                                 <i class="fa fa-close"></i>
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if ($value_counts == 0): ?>
                        <tr class="labs-empty-row">
                           <td colspan="10">
                              <div class="labs-empty">
                                 <i class="fa fa-inbox"></i>
                                 <p>Belum ada data presensi ibadah</p>
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
                  <div class="labs-flex" style="gap:8px;align-items:center">
                     <select class="form-control input-sm" name="bulk" id="bulk" style="width:120px">
                        <option value="">Bulk</option>
                        <option value="delete">Delete</option>
                     </select>
                     <button type="button" class="labs-btn labs-btn--default labs-btn--sm" id="apply" title="Apply bulk action">
                        Apply
                     </button>
                     <span class="labs-text-muted labs-fs-sm labs-ml-2">
                        Total: <strong><?= $value_counts; ?></strong> data
                     </span>
                  </div>
                  <div><?= $pagination; ?></div>
               </div>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- ============ MODAL FILTER =============== -->
<div class="modal fade labs-modal" id="modal_filter" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-users"></i> Filter Presensi Ibadah</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <form action="<?= base_url('administrator/ibadah/filter_presensi'); ?>" method="get" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Tahun Ajaran</label>
                  <div class="col-xs-8">
                     <select name="id_tahun_ajaran" class="form-control">
                        <option value="">-</option>
                        <?php 
                           $list_tahun_ajaran = $this->mymodel->withquery("SELECT * FROM tahun_ajaran", "result");
                           foreach ($list_tahun_ajaran as $key => $value) {
                              $selected = (!empty($get['id_tahun_ajaran']) && $get['id_tahun_ajaran'] == $value->id_tahun_ajaran) ? 'selected' : '';
                              echo "<option value='" . $value->id_tahun_ajaran . "' {$selected}>" . $value->label . "</option>";
                           }
                        ?>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Jenjang</label>
                  <div class="col-xs-8">
                     <select name="jenjang" class="form-control">
                        <option value="sma" <?= (!empty($get['jenjang']) && $get['jenjang'] == 'sma') ? 'selected' : ''; ?>>SMA</option>
                        <option value="ft" <?= (!empty($get['jenjang']) && $get['jenjang'] == 'ft') ? 'selected' : ''; ?>>FT</option>
                        <option value="smp" <?= (!empty($get['jenjang']) && $get['jenjang'] == 'smp') ? 'selected' : ''; ?>>SMP</option>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Tingkatan</label>
                  <div class="col-xs-8">
                     <select name="id_tingkatan" class="form-control" id="filter_tingkatan">
                        <option value="">-</option>
                     </select>
                     <small class="labs-text-muted">Kosongkan bila memilih export per kelas</small>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Kelas</label>
                  <div class="col-xs-8">
                     <select name="id_kelas" class="form-control" id="filter_kelas">
                        <option value="">-</option>
                     </select>
                     <small class="labs-text-muted">Kosongkan bila memilih export per tingkat</small>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Tanggal Mulai - Selesai</label>
                  <div class="col-xs-4">
                     <input type="date" class="form-control" name="start_date" value="<?= !empty($get['start_date']) ? _ent($get['start_date']) : ''; ?>" required>
                  </div>
                  <div class="col-xs-4">
                     <input type="date" class="form-control" name="end_date" value="<?= !empty($get['end_date']) ? _ent($get['end_date']) : ''; ?>" required>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button class="labs-btn labs-btn--default" data-dismiss="modal">Batal</button>
               <button type="submit" class="labs-btn labs-btn--info"><i class="fa fa-check"></i> Terapkan</button>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){

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
      var serialize_bulk = $('#form_presensi').serialize();

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
               document.location.href = BASE_URL + '/administrator/ibadah/delete?' + serialize_bulk;      
            }
         });
         return false;
      } else if (bulk.val() == '') {
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

   // Dynamic tingkatan & kelas based on jenjang
   var tingkatanData = {
      'sma': <?= json_encode($this->mymodel->withquery("SELECT id_tingkatan_sma AS id, label FROM tingkatan_sma ORDER BY label ASC", "result")); ?>,
      'ft': <?= json_encode($this->mymodel->withquery("SELECT id_tingkatan_ft AS id, label FROM tingkatan_ft ORDER BY label ASC", "result")); ?>,
      'smp': <?= json_encode($this->mymodel->withquery("SELECT id_tingkatan_smp AS id, label FROM tingkatan_smp ORDER BY label ASC", "result")); ?>
   };
   var kelasData = {
      'sma': <?= json_encode($this->mymodel->withquery("SELECT id_kelas_sma AS id, label FROM kelas_sma ORDER BY label ASC", "result")); ?>,
      'ft': <?= json_encode($this->mymodel->withquery("SELECT id_kelas_ft AS id, label FROM kelas_ft ORDER BY label ASC", "result")); ?>,
      'smp': <?= json_encode($this->mymodel->withquery("SELECT id_kelas_smp AS id, label FROM kelas_smp ORDER BY label ASC", "result")); ?>
   };
   var currentTingkat = '<?= !empty($get['id_tingkatan']) ? $get['id_tingkatan'] : ''; ?>';
   var currentKelas = '<?= !empty($get['id_kelas']) ? $get['id_kelas'] : ''; ?>';

   function loadFilterOptions(jenjang) {
      var $tingkatan = $('#filter_tingkatan');
      var $kelas = $('#filter_kelas');
      $tingkatan.html('<option value="">-</option>');
      $kelas.html('<option value="">-</option>');

      var tList = tingkatanData[jenjang] || [];
      for (var i = 0; i < tList.length; i++) {
         var sel = (String(tList[i].id) === String(currentTingkat)) ? 'selected' : '';
         $tingkatan.append('<option value="' + tList[i].id + '" ' + sel + '>' + tList[i].label + '</option>');
      }
      var kList = kelasData[jenjang] || [];
      for (var i = 0; i < kList.length; i++) {
         var sel = (String(kList[i].id) === String(currentKelas)) ? 'selected' : '';
         $kelas.append('<option value="' + kList[i].id + '" ' + sel + '>' + kList[i].label + '</option>');
      }
   }

   var initJenjang = $('select[name="jenjang"]').val();
   loadFilterOptions(initJenjang);

   $('select[name="jenjang"]').change(function(){
      currentTingkat = '';
      currentKelas = '';
      loadFilterOptions($(this).val());
   });

   // Check all
   var checkAll = $('#check_all');
   var checkboxes = $('input.check');

   checkAll.on('ifChecked ifUnchecked', function(event) {   
      if (event.type == 'ifChecked') {
         checkboxes.iCheck('check');
      } else {
         checkboxes.iCheck('uncheck');
      }
   });

   checkboxes.on('ifChanged', function(event){
      if (checkboxes.filter(':checked').length == checkboxes.length) {
         checkAll.prop('checked', 'checked');
      } else {
         checkAll.removeProp('checked');
      }
      checkAll.iCheck('update');
   });

});
</script>
