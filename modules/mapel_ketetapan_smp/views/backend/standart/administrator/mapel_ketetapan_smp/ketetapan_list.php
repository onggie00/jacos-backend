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
.btn-action-edit { border-color: #f39c12; color: #f39c12 !important; }
.btn-action-edit:hover { background: #f39c12; color: #fff !important; }
.btn-action-delete { border-color: #e74c3c; color: #e74c3c !important; }
.btn-action-delete:hover { background: #e74c3c; color: #fff !important; }

.tipe-hari { background: #d4edda; }
.tipe-jam { background: #cce5ff; }
.badge-hari { background: #28a745; }
.badge-jam { background: #007bff; }
</style>

<script type="text/javascript">
</script>

<!-- Content Header -->
<section class="content-header">
   <h1>
      Ketetapan Mapel <small>Aturan khusus hari/jam per mapel</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">Alokasi Mapel</a></li>
      <li class="active">Ketetapan</li>
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
                  <i class="fa fa-gavel"></i> Data Ketetapan Mapel
                  <span class="label bg-yellow" style="margin-left:10px"><?= $ketetapan_counts; ?> Data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('mapel_ketetapan_smp_add', function(){?>
                  <a class="btn btn-sm btn-success" href="<?= site_url('administrator/mapel_ketetapan_smp/add'); ?>">
                     <i class="fa fa-plus"></i> Tambah Ketetapan
                  </a>
                  <?php }) ?>
                  <a class="btn btn-sm btn-default" href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">
                     <i class="fa fa-arrow-left"></i> Kembali
                  </a>
               </div>
            </div>

            <div class="box-body">
               <!-- Info -->
               <div class="alert alert-info">
                  <i class="fa fa-info-circle"></i>
                  <strong>Panduan:</strong>
                  <ul style="margin-bottom:0">
                     <li><strong>Tipe Hari:</strong> Mapel hanya boleh dijadwalkan pada hari tertentu (nilai = id_hari, dipisah koma)</li>
                     <li><strong>Tipe Jam:</strong> Mapel hanya boleh dijadwalkan pada jam_ke tertentu (nilai = jam_ke, dipisah koma)</li>
                  </ul>
               </div>

               <!-- Search Form -->
               <form name="form_ketetapan" id="form_ketetapan" action="<?= base_url('administrator/mapel_ketetapan_smp/index'); ?>">
               <div class="row" style="margin-bottom:15px">
                  <div class="col-md-5">
                     <div class="input-group">
                        <input type="text" class="form-control" name="q" id="filter" placeholder="Cari ketetapan..." value="<?= $this->input->get('q'); ?>">
                        <span class="input-group-btn">
                           <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Cari</button>
                           <?php if(!empty($this->input->get('q'))): ?>
                           <a class="btn btn-flat btn-default" href="<?= base_url('administrator/mapel_ketetapan_smp'); ?>"><i class="fa fa-undo"></i></a>
                           <?php endif; ?>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control chosen chosen-select" name="f" id="field">
                        <option value="">Semua Kolom</option>
                        <option <?= $this->input->get('f') == 'kode_mapel' ? 'selected' : ''; ?> value="kode_mapel">Kode Mapel</option>
                        <option <?= $this->input->get('f') == 'tipe_ketetapan' ? 'selected' : ''; ?> value="tipe_ketetapan">Tipe</option>
                     </select>
                  </div>
               </div>

               <!-- Data Table -->
               <div class="table-responsive"> 
                  <table class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr>
                           <th width="30">
                              <input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all">
                           </th>
                           <th style="text-align:center" width="50">No</th>
                           <th style="text-align:center">Kode Mapel</th>
                           <th style="text-align:center">Tipe</th>
                           <th style="text-align:center">Nilai</th>
                           <th style="text-align:center">Keterangan</th>
                           <th style="text-align:center" width="150">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php if($ketetapan_counts > 0): ?>
                     <?php 
                     $no = isset($offset) ? $offset : 0;
                     foreach($ketetapans as $ketetapan): 
                        $no++;
                        
                        // Format nilai display
                        $nilai_display = '';
                        if ($ketetapan->tipe_ketetapan == 'hari') {
                            $hari_map = array(1=>'Senin', 2=>'Selasa', 3=>'Rabu', 4=>'Kamis', 5=>'Jumat');
                            $nilai_arr = explode(',', $ketetapan->nilai);
                            $hari_names = array();
                            foreach ($nilai_arr as $n) {
                                $hari_names[] = isset($hari_map[(int)$n]) ? $hari_map[(int)$n] : $n;
                            }
                            $nilai_display = implode(', ', $hari_names) . ' (' . $ketetapan->nilai . ')';
                        } else {
                            $nilai_arr = explode(',', $ketetapan->nilai);
                            $nilai_display = 'Jam ke ' . implode(', ', $nilai_arr);
                        }
                     ?>
                        <tr class="<?= ($ketetapan->tipe_ketetapan == 'hari') ? 'tipe-hari' : 'tipe-jam'; ?>">
                           <td>
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $ketetapan->id_ketetapan; ?>">
                           </td>
                           <td style="text-align:center"><?= $no; ?></td>
                           <td style="text-align:center">
                              <strong><?= _ent($ketetapan->kode_mapel); ?></strong>
                           </td>
                           <td style="text-align:center">
                              <?php if($ketetapan->tipe_ketetapan == 'hari'): ?>
                                 <span class="label badge-hari">HARI</span>
                              <?php else: ?>
                                 <span class="label badge-jam">JAM</span>
                              <?php endif; ?>
                           </td>
                           <td><?= $nilai_display; ?></td>
                           <td><?= _ent($ketetapan->keterangan); ?></td>
                           <td style="text-align:center">
                              <?php is_allowed('mapel_ketetapan_smp_update', function() use ($ketetapan){?>
                              <a href="<?= site_url('administrator/mapel_ketetapan_smp/edit/' . $ketetapan->id_ketetapan); ?>" class="btn btn-action btn-action-edit btn-sm">
                                 <i class="fa fa-edit"></i> Edit
                              </a>
                              <?php }) ?>
                              <?php is_allowed('mapel_ketetapan_smp_delete', function() use ($ketetapan){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/mapel_ketetapan_smp/delete/' . $ketetapan->id_ketetapan); ?>" class="btn btn-action btn-action-delete btn-sm remove-data">
                                 <i class="fa fa-trash"></i> Hapus
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="7" class="text-center" style="padding:30px">
                              <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i><br>
                              <span style="color:#999">Belum ada ketetapan</span>
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
         text: "Data yang dihapus tidak dapat dikembalikan",
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
      var serialize_bulk = $('#form_ketetapan').serialize();

      if (bulk.val() == 'delete') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "Data yang dihapus tidak dapat dikembalikan",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            closeOnConfirm: true,
            closeOnCancel: true
         }, function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/mapel_ketetapan_smp/delete?' + serialize_bulk;      
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
