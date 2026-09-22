<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap.min.css">
<style>
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
.btn-action-matrix { border-color: #9b59b6; color: #9b59b6 !important; }
.btn-action-matrix:hover { background: #9b59b6; color: #fff !important; }
.btn-action-delete { border-color: #e74c3c; color: #e74c3c !important; }
.btn-action-delete:hover { background: #e74c3c; color: #fff !important; }

.stat-card {
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
    text-align: center;
}
.stat-card .number { font-size: 24px; font-weight: bold; }
.stat-card .label-text { font-size: 12px; color: #666; }
.card-blue { background: #e3f2fd; border: 1px solid #90caf9; }
.card-green { background: #e8f5e9; border: 1px solid #a5d6a7; }

.table th { background: #f8f9fa; font-weight: 600; font-size: 13px; vertical-align: middle; }
.table td { font-size: 13px; vertical-align: middle; }
</style>

<!-- Content Header -->
<section class="content-header">
   <h1>
      Jadwal Pelajaran SMP <small>Hasil generate jadwal</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Jadwal Pelajaran SMP</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <!-- Stats -->
      <div class="col-md-3">
         <div class="stat-card card-blue">
            <div class="number"><?= $stats['total']; ?></div>
            <div class="label-text">Total Jadwal</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="stat-card card-green">
            <div class="number"><?= count($stats['per_kelas']); ?></div>
            <div class="label-text">Kelas Terjadwal</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="stat-card card-blue">
            <div class="number"><?= count($stats['per_guru']); ?></div>
            <div class="label-text">Guru Terjadwal</div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="stat-card card-green">
            <div class="number"><?= count($kelas_list); ?></div>
            <div class="label-text">Total Kelas</div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-calendar"></i> Data Jadwal Pelajaran
                  <span class="label bg-yellow" style="margin-left:10px"><?= $jadwal_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <!-- Matrix View Dropdown -->
                  <div class="btn-group">
                     <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-th"></i> Matrix View <span class="caret"></span>
                     </button>
                     <ul class="dropdown-menu dropdown-menu-right" style="max-height:300px;overflow-y:auto">
                        <?php 
                        $current_tingkatan = 0;
                        $tingkatan_label = array(1 => 'Kelas 7', 2 => 'Kelas 8', 3 => 'Kelas 9');
                        foreach ($kelas_list as $kelas): 
                            if ($kelas->id_tingkatan != $current_tingkatan) {
                                $current_tingkatan = $kelas->id_tingkatan;
                                echo '<li class="dropdown-header">' . $tingkatan_label[$current_tingkatan] . '</li>';
                            }
                        ?>
                        <li><a href="<?= site_url('administrator/jadwal_pelajaran_smp/matrix/' . $kelas->id_kelas_smp); ?>"><?= $kelas->label; ?></a></li>
                        <?php endforeach; ?>
                     </ul>
                  </div>
                  
                  <a class="btn btn-sm btn-warning" href="<?= site_url('administrator/jadwal_pelajaran_smp/ringkasan_guru'); ?>" title="Ringkasan Guru">
                     <i class="fa fa-users"></i> Ringkasan Guru
                  </a>
                  
                  <?php is_allowed('jadwal_pelajaran_smp_export', function(){?>
                  <div class="btn-group">
                     <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-file-excel-o"></i> Export Excel <span class="caret"></span>
                     </button>
                     <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="<?= site_url('administrator/jadwal_pelajaran_smp/export'); ?>"><i class="fa fa-file-excel-o"></i> Export Semua</a></li>
                        <li class="divider"></li>
                        <li class="dropdown-header">Export Per Kelas</li>
                        <?php 
                        $current_t = 0;
                        $t_label = array(1 => 'Kelas 7', 2 => 'Kelas 8', 3 => 'Kelas 9');
                        foreach ($kelas_list as $k): 
                            if ($k->id_tingkatan != $current_t) {
                                $current_t = $k->id_tingkatan;
                                echo '<li class="dropdown-header">' . $t_label[$current_t] . '</li>';
                            }
                        ?>
                        <li><a href="<?= site_url('administrator/jadwal_pelajaran_smp/export_kelas/' . $k->id_kelas_smp); ?>"><?= $k->label; ?></a></li>
                        <?php endforeach; ?>
                     </ul>
                  </div>
                  <?php }) ?>
               </div>
            </div>

            <div class="box-body">
               <!-- Search Form -->
               <form name="form_jadwal" id="form_jadwal" action="<?= base_url('administrator/jadwal_pelajaran_smp/index'); ?>">
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-4">
                     <div class="input-group">
                        <input type="text" class="form-control" name="q" id="filter" placeholder="Cari jadwal..." value="<?= $this->input->get('q'); ?>">
                        <span class="input-group-btn">
                           <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Cari</button>
                           <?php if(!empty($this->input->get('q'))): ?>
                           <a class="btn btn-flat btn-default" href="<?= base_url('administrator/jadwal_pelajaran_smp'); ?>"><i class="fa fa-undo"></i></a>
                           <?php endif; ?>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control chosen chosen-select" name="f" id="field">
                        <option value="">Semua Kolom</option>
                        <option <?= $this->input->get('f') == 'guru_smp.nama_lengkap' ? 'selected' : ''; ?> value="guru_smp.nama_lengkap">Guru</option>
                        <option <?= $this->input->get('f') == 'kelas_smp.label' ? 'selected' : ''; ?> value="kelas_smp.label">Kelas</option>
                        <option <?= $this->input->get('f') == 'mata_pelajaran_smp.nama_mapel' ? 'selected' : ''; ?> value="mata_pelajaran_smp.nama_mapel">Mapel</option>
                        <option <?= $this->input->get('f') == 'pelajaran_hari.hari' ? 'selected' : ''; ?> value="pelajaran_hari.hari">Hari</option>
                     </select>
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
                           <th style="text-align:center">Hari</th>
                           <th style="text-align:center">Jam</th>
                           <th style="text-align:center">Kelas</th>
                           <th style="text-align:center">Mapel</th>
                           <th style="text-align:center">Guru</th>
                           <th style="text-align:center" width="150">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php if($jadwal_counts > 0): ?>
                     <?php foreach($jadwals as $jadwal): ?>
                        <tr>
                           <td>
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $jadwal->id_jadwal_pelajaran; ?>">
                           </td>
                           <td style="text-align:center">
                              <strong><?= _ent($jadwal->nama_hari); ?></strong>
                           </td>
                           <td style="text-align:center">
                              <?= _ent($jadwal->jam_ke); ?>. <?= _ent($jadwal->jam_pelajaran); ?>
                           </td>
                           <td style="text-align:center">
                              <strong><?= _ent($jadwal->kelas_label); ?></strong>
                           </td>
                           <td>
                              <span class="label label-info"><?= _ent($jadwal->mapel_kode); ?></span>
                              <?= _ent($jadwal->mapel_nama); ?>
                           </td>
                           <td><?= _ent($jadwal->guru_nama); ?></td>
                           <td style="text-align:center">
                              <?php is_allowed('jadwal_pelajaran_smp_view', function() use ($jadwal){?>
                              <a href="<?= site_url('administrator/jadwal_pelajaran_smp/view/' . $jadwal->id_jadwal_pelajaran); ?>" class="btn btn-action btn-action-view btn-sm">
                                 <i class="fa fa-eye"></i>
                              </a>
                              <?php }) ?>
                              <a href="<?= site_url('administrator/jadwal_pelajaran_smp/matrix/' . $jadwal->id_kelas_smp); ?>" class="btn btn-action btn-action-matrix btn-sm" title="Matrix View">
                                 <i class="fa fa-th"></i>
                              </a>
                              <?php is_allowed('jadwal_pelajaran_smp_delete', function() use ($jadwal){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/jadwal_pelajaran_smp/delete/' . $jadwal->id_jadwal_pelajaran); ?>" class="btn btn-action btn-action-delete btn-sm remove-data">
                                 <i class="fa fa-trash"></i>
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="7" class="text-center" style="padding:30px">
                              <i class="fa fa-calendar-o" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">Belum ada jadwal. <a href="<?= site_url('administrator/mapel_alokasi_smp/fase1'); ?>">Generate dari Fase 1</a></span>
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

   // Delete single
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({
         title: "<?= cclang('are_you_sure'); ?>",
         text: "Jadwal yang dihapus tidak dapat dikembalikan",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Ya, Hapus!",
         cancelButtonText: "Batal",
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
      var serialize_bulk = $('#form_jadwal').serialize();

      if (bulk.val() == 'delete') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "Jadwal yang dihapus tidak dapat dikembalikan",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            closeOnConfirm: true,
            closeOnCancel: true
         }, function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/jadwal_pelajaran_smp/delete?' + serialize_bulk;      
            }
         });
         return false;
      } else if(bulk.val() == '') {
         swal({
            title: "Upss",
            text: "Pilih aksi terlebih dahulu",
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

   checkAll.on('ifChecked ifUnchecked', function(event) {   
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
