<?php
// Inisial untuk avatar
function ibadah_initials($name) {
    $name = trim($name);
    if ($name === '') return '?';
    $parts = preg_split('/\s+/', $name);
    if (count($parts) >= 2) {
        return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
    }
    return strtoupper(mb_substr($parts[0], 0, 2));
}
?>

<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<section class="content-header">
   <h1>
      Data Ibadah
      <small class="labs-text-muted">Daftar Presensi Ibadah Siswa</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Ibadah</li>
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
               <div class="labs-stat-card__label">Total Ibadah</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->total); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--success">
               <i class="fa fa-calendar-check-o"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Hari Ini</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->hari_ini); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--warning">
               <i class="fa fa-calendar"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Minggu Ini</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->minggu_ini); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--danger">
               <i class="fa fa-users"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Siswa Aktif</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->siswa_aktif); ?>">0</div>
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
                  Daftar Ibadah
                  <span class="labs-badge labs-badge--default labs-ml-2"><?= $ibadah_counts; ?> data</span>
               </h3>
               <div>
                  <a class="labs-btn labs-btn--info labs-btn--sm" title="Filter Presensi Ibadah" data-toggle="modal" data-target="#modal_filter">
                     <i class="fa fa-users"></i> Filter Presensi
                  </a>
                  <?php is_allowed('ibadah_export', function(){?>
                  <a class="labs-btn labs-btn--success labs-btn--sm" title="Export Excel" data-toggle="modal" data-target="#modal_export">
                     <i class="fa fa-file-excel-o"></i> Export XLS
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="labs-card__body">

               <!-- TABLE -->
               <form name="form_ibadah" id="form_ibadah" action="<?= base_url('administrator/ibadah/index'); ?>">
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
                           <th>Maps</th>
                           <th class="labs-cell-numeric">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php foreach($ibadahs as $ibadah):
                        $nama = '';
                        if ($ibadah->jenjang == 'ft') $nama = $ibadah->siswa_ft_aktif_nama_lengkap;
                        elseif ($ibadah->jenjang == 'sma') $nama = $ibadah->siswa_sma_aktif_nama_lengkap;
                        elseif ($ibadah->jenjang == 'smp') $nama = $ibadah->siswa_smp_aktif_nama_lengkap;
                        $initials = ibadah_initials($nama);

                        $nama_kelas = '';
                        if ($ibadah->jenjang == 'ft') $nama_kelas = $ibadah->nama_kelas_ft;
                        elseif ($ibadah->jenjang == 'sma') $nama_kelas = $ibadah->nama_kelas_sma;
                        elseif ($ibadah->jenjang == 'smp') $nama_kelas = $ibadah->nama_kelas_smp;

                        $jenjang_badge = 'default';
                        if ($ibadah->jenjang == 'sma') $jenjang_badge = 'info';
                        elseif ($ibadah->jenjang == 'ft') $jenjang_badge = 'success';
                        elseif ($ibadah->jenjang == 'smp') $jenjang_badge = 'warning';
                     ?>
                        <tr>
                           <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $ibadah->id; ?>"></td>
                           <td>
                              <div class="labs-name-cell">
                                 <span class="labs-avatar"><?= $initials; ?></span>
                                 <div>
                                    <?php if ($ibadah->id_siswa_aktif): ?>
                                       <?php
                                          $view_url = 'administrator/siswa_' . $ibadah->jenjang . '_aktif/view/' . $ibadah->id_siswa_aktif . '?popup=show';
                                       ?>
                                       <div class="labs-fw-semi"><?= anchor($view_url, _ent($nama), array('class' => 'popup-view')); ?></div>
                                    <?php else: ?>
                                       <div class="labs-fw-semi labs-text-muted">-</div>
                                    <?php endif; ?>
                                 </div>
                              </div>
                           </td>
                           <td><?= _ent($nama_kelas); ?></td>
                           <td><span class="labs-badge labs-badge--<?= $jenjang_badge; ?>"><?= strtoupper(_ent($ibadah->jenjang)); ?></span></td>
                           <td>
                              <?php if ($ibadah->id_ibadah): ?>
                                 <?= anchor('administrator/ibadah_setting/view/' . $ibadah->id_ibadah . '?popup=show', $ibadah->ibadah_setting_ibadah, array('class' => 'popup-view')); ?>
                              <?php else: ?>
                                 <span class="labs-cell-muted">-</span>
                              <?php endif; ?>
                           </td>
                           <td>
                              <div class="labs-fs-sm"><?= formatTanggal($ibadah->tanggal); ?></div>
                           </td>
                           <td>
                              <div class="labs-fs-sm"><?= date("H:i", strtotime($ibadah->waktu)); ?> WIB</div>
                           </td>
                           <td><?= _ent($ibadah->jumlah_rakaat); ?></td>
                           <td><?= _ent($ibadah->keterangan); ?></td>
                           <td>
                              <?php if (!empty($ibadah->latitude) && !empty($ibadah->longitude)): ?>
                                 <a target="_blank" class="labs-btn labs-btn--primary labs-btn--sm" href="https://www.google.com/maps/search/?api=1&query=<?= $ibadah->latitude; ?>,<?= $ibadah->longitude; ?>&zoom=12">
                                    <i class="fa fa-map-marker"></i>
                                 </a>
                              <?php else: ?>
                                 <span class="labs-cell-muted">-</span>
                              <?php endif; ?>
                           </td>
                           <td class="labs-cell-numeric">
                              <?php is_allowed('ibadah_update', function() use ($ibadah){?>
                              <a href="<?= site_url('administrator/ibadah/edit/' . $ibadah->id); ?>" class="labs-btn labs-btn--default labs-btn--sm" title="Edit">
                                 <i class="fa fa-edit"></i>
                              </a>
                              <?php }) ?>
                              <?php is_allowed('ibadah_delete', function() use ($ibadah){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/ibadah/delete/' . $ibadah->id); ?>" class="labs-btn labs-btn--danger labs-btn--sm remove-data" title="Hapus">
                                 <i class="fa fa-close"></i>
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if ($ibadah_counts == 0): ?>
                        <tr class="labs-empty-row">
                           <td colspan="11">
                              <div class="labs-empty">
                                 <i class="fa fa-inbox"></i>
                                 <p>Belum ada data ibadah</p>
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
                        Total: <strong><?= $ibadah_counts; ?></strong> data
                     </span>
                  </div>
                  <div><?= $pagination; ?></div>
               </div>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- ============ MODAL FILTER PRESENSI =============== -->
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
                              echo "<option value='" . $value->id_tahun_ajaran . "'>" . $value->label . "</option>";
                           }
                        ?>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Jenjang</label>
                  <div class="col-xs-8">
                     <select name="jenjang" class="form-control">
                        <option value="sma">SMA</option>
                        <option value="ft">FT</option>
                        <option value="smp">SMP</option>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Tingkatan</label>
                  <div class="col-xs-8">
                     <select name="id_tingkatan" class="form-control" id="filter_tingkatan">
                        <option value="">-</option>
                     </select>
                     <small class="labs-text-muted">Kosongkan bila memilih filter per kelas</small>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Kelas</label>
                  <div class="col-xs-8">
                     <select name="id_kelas" class="form-control" id="filter_kelas">
                        <option value="">-</option>
                     </select>
                     <small class="labs-text-muted">Kosongkan bila memilih filter per tingkat</small>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Tanggal Mulai - Selesai</label>
                  <div class="col-xs-4">
                     <input type="date" class="form-control" name="start_date" required>
                  </div>
                  <div class="col-xs-4">
                     <input type="date" class="form-control" name="end_date" required>
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

<!-- ============ MODAL EXPORT WARNING =============== -->
<div class="modal fade labs-modal" id="modal_export" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
         <div class="modal-header" style="background:linear-gradient(135deg, var(--labs-warning), #92400E)">
            <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Peringatan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
            <div class="labs-text-center" style="padding:10px 0">
               <i class="fa fa-filter" style="font-size:48px;color:var(--labs-warning);margin-bottom:12px;display:block"></i>
               <p style="font-size:14px;font-weight:600;color:var(--labs-text);margin:0 0 8px 0">Filter belum dipilih</p>
               <p class="labs-text-muted" style="margin:0">Silahkan filter presensi terlebih dahulu sebelum export data.</p>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="labs-btn labs-btn--warning" data-dismiss="modal"><i class="fa fa-check"></i> Mengerti</button>
         </div>
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
      var serialize_bulk = $('#form_ibadah').serialize();

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

   function loadFilterOptions(jenjang) {
      var $tingkatan = $('#filter_tingkatan');
      var $kelas = $('#filter_kelas');
      $tingkatan.html('<option value="">-</option>');
      $kelas.html('<option value="">-</option>');

      var tList = tingkatanData[jenjang] || [];
      for (var i = 0; i < tList.length; i++) {
         $tingkatan.append('<option value="' + tList[i].id + '">' + tList[i].label + '</option>');
      }
      var kList = kelasData[jenjang] || [];
      for (var i = 0; i < kList.length; i++) {
         $kelas.append('<option value="' + kList[i].id + '">' + kList[i].label + '</option>');
      }
   }

   // init on page load
   var initJenjang = $('select[name="jenjang"]').val();
   loadFilterOptions(initJenjang);

   // on change jenjang
   $('select[name="jenjang"]').change(function(){
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
