<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('ekskul_presensi_siswa_smp') ?><small class="labs-text-muted"><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('ekskul_presensi_siswa_smp') ?></li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">

            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-list-alt"></i>
                  <?= cclang('ekskul_presensi_siswa_smp') ?>
                  <span class="labs-badge labs-badge--default" style="margin-left:8px;"><?= $ekskul_presensi_siswa_smp_counts; ?> <?= cclang('items'); ?></span>
               </h3>
               <div>
                  <a class="labs-btn labs-btn--primary labs-btn--sm" title="Filter Presensi" data-toggle="modal" data-target="#modal_filter"><i class="fa fa-users"></i> Filter Presensi</a>
                  <?php is_allowed('ekskul_presensi_siswa_smp_export', function(){?>
                  <a class="labs-btn labs-btn--success labs-btn--sm" id="btn_export" title="Export Excel" href="javascript:void(0);"><i class="fa fa-file-excel-o"></i> <?= cclang('export'); ?> XLS</a>
                  <?php }) ?>
               </div>
            </div>

            <div class="labs-card__body">

               <form name="form_ekskul_presensi_siswa_smp" id="form_ekskul_presensi_siswa_smp" action="<?= base_url('administrator/ekskul_presensi_siswa_smp/index'); ?>">

                  <div class="labs-table-wrap">
                     <div class="labs-table-scroll">
                        <table class="labs-table">
                           <thead>
                              <tr>
                                 <th width="5">
                                    <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                                 </th>
                                 <th><?= cclang('id_siswa_aktif') ?></th>
                                 <th><?= cclang('id_ekskul') ?></th>
                                 <th><?= cclang('hari_absen') ?></th>
                                 <th><?= cclang('tanggal_absen') ?></th>
                                 <th><?= cclang('waktu_absen') ?></th>
                                 <th><?= cclang('status_absen') ?></th>
                                 <th><?= cclang('keterangan_presensi') ?></th>
                                 <th width="120">Action</th>
                              </tr>
                           </thead>
                           <tbody id="tbody_ekskul_presensi_siswa_smp">
                           <?php foreach($ekskul_presensi_siswa_smps as $ekskul_presensi_siswa_smp): ?>
                              <?php
                                 $status_cls = 'labs-badge--default';
                                 $status_val = strtolower(trim($ekskul_presensi_siswa_smp->status_absen));
                                 if ($status_val == 'hadir') $status_cls = 'labs-badge--success';
                                 elseif ($status_val == 'terlambat') $status_cls = 'labs-badge--orange';
                                 elseif ($status_val == 'tidak hadir') $status_cls = 'labs-badge--danger';
                              ?>
                              <tr>
                                 <td>
                                    <input type="checkbox" class="flat-red check" name="id[]" value="<?= $ekskul_presensi_siswa_smp->id; ?>">
                                 </td>
                                 <td><?php if ($ekskul_presensi_siswa_smp->id_siswa_aktif) {
                                       echo anchor('administrator/siswa_smp_aktif/view/'.$ekskul_presensi_siswa_smp->id_siswa_aktif.'?popup=show', $ekskul_presensi_siswa_smp->siswa_smp_aktif_nama_lengkap, ['class' => 'popup-view']); } ?>
                                 </td>
                                 <td><?php if ($ekskul_presensi_siswa_smp->id_ekskul) {
                                       echo anchor('administrator/ekskul/view/'.$ekskul_presensi_siswa_smp->id_ekskul.'?popup=show', $ekskul_presensi_siswa_smp->ekskul_nama, ['class' => 'popup-view']); } ?>
                                 </td>
                                 <td><?= _ent($ekskul_presensi_siswa_smp->hari_absen); ?></td>
                                 <td><?= _ent($ekskul_presensi_siswa_smp->tanggal_absen); ?></td>
                                 <td><?= _ent($ekskul_presensi_siswa_smp->waktu_absen); ?></td>
                                 <td><span class="labs-badge <?= $status_cls; ?>"><?= _ent($ekskul_presensi_siswa_smp->status_absen); ?></span></td>
                                 <td><?= _ent($ekskul_presensi_siswa_smp->keterangan_presensi); ?></td>
                                 <td>
                                    <?php is_allowed('ekskul_presensi_siswa_smp_view', function() use ($ekskul_presensi_siswa_smp){?>
                                    <a href="<?= site_url('administrator/ekskul_presensi_siswa_smp/view/' . $ekskul_presensi_siswa_smp->id); ?>" class="labs-btn labs-btn--icon labs-btn--xs labs-btn--info toltip" title="Detail"><i class="fa fa-eye"></i></a>
                                    <?php }) ?>
                                    <?php is_allowed('ekskul_presensi_siswa_smp_update', function() use ($ekskul_presensi_siswa_smp){?>
                                    <a href="<?= site_url('administrator/ekskul_presensi_siswa_smp/edit/' . $ekskul_presensi_siswa_smp->id); ?>" class="labs-btn labs-btn--icon labs-btn--xs labs-btn--primary toltip" title="Edit"><i class="fa fa-pencil"></i></a>
                                    <?php }) ?>
                                    <?php is_allowed('ekskul_presensi_siswa_smp_delete', function() use ($ekskul_presensi_siswa_smp){?>
                                    <a href="javascript:void(0);" data-href="<?= site_url('administrator/ekskul_presensi_siswa_smp/delete/' . $ekskul_presensi_siswa_smp->id); ?>" class="labs-btn labs-btn--icon labs-btn--xs labs-btn--danger remove-data toltip" title="Hapus"><i class="fa fa-trash"></i></a>
                                    <?php }) ?>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                           <?php if ($ekskul_presensi_siswa_smp_counts == 0) :?>
                              <tr class="labs-empty-row">
                                 <td colspan="100">
                                    Presensi Siswa SMP data is not available
                                 </td>
                              </tr>
                           <?php endif; ?>
                           </tbody>
                        </table>
                     </div>
                  </div>

            </div>

            <div class="labs-card__footer" style="display:flex;justify-content:space-between;align-items:center;padding:15px;">
               <div style="display:flex;align-items:center;gap:10px;">
                     <select type="text" class="form-control chosen chosen-select" name="bulk" id="bulk" placeholder="Site Email" style="width:150px;">
                        <option value="delete">Delete</option>
                     </select>
                     <button type="button" class="labs-btn labs-btn--default labs-btn--sm" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>"><?= cclang('apply_button'); ?></button>
               </div>
               <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  <?= $pagination; ?>
               </div>
               </form>
            </div>

         </div>
      </div>
   </div>
</section>
<!-- /.content -->

<!-- ============ MODAL FILTER =============== -->
<div class="modal fade labs-modal" id="modal_filter" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="modal-title">Filter Presensi</h3>
         </div>
         <form action="<?= base_url('administrator/ekskul_presensi_siswa_smp/filter_presensi'); ?>" method="get" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Ekstrakurikuler</label>
                  <div class="col-xs-8">
                     <select name="id_ekskul" class="form-control">
                        <?php
                           $list_ekskul = $this->mymodel->withquery("select * from ekskul where jenjang = 'smp' order by nama ASC","result");
                           foreach ($list_ekskul as $key => $value) {
                              echo "<option value='".$value->id_ekskul."'>".$value->nama."</option>";
                           }
                        ?>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Periode Presensi</label>
                  <div class="col-xs-5">
                     <select name="bulan" class="form-control" required>
                        <?php
                           for ($i=1; $i <= 12; $i++) {
                              if (date("m") == $i) {
                                 echo "<option value='".$i."' selected>".formatBulan(date("Y-m-d", strtotime(date("Y")."-".$i."-1")))."</option>";
                              }
                              else{
                                 echo "<option value='".$i."'>".formatBulan(date("Y-m-d", strtotime(date("Y")."-".$i."-1")))."</option>";
                              }
                           }
                        ?>
                     </select>
                  </div>
                  <div class="col-xs-3">
                     <select name="tahun" class="form-control" required>
                        <?php
                           $thn = date("Y");
                           for ($i=25; $i > 0; $i--) {
                              $list = $thn - $i;
                              echo "<option value='".$list."'>".$list."</option>";
                           }
                           for ($i=0; $i <=25; $i++) {
                              $list = $thn + $i;
                              if ($i == 0) {
                                 echo "<option value='".$list."' selected>".$list."</option>";
                              }
                              else{
                                 echo "<option value='".$list."'>".$list."</option>";
                              }
                           }
                        ?>
                     </select>
                  </div>
               </div>
            </div>

            <div class="modal-footer">
               <button class="labs-btn labs-btn--default labs-btn--sm" data-dismiss="modal" aria-hidden="true">Batal</button>
               <button type="submit" class="labs-btn labs-btn--primary labs-btn--sm btn_save">Terapkan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL FILTER -->

<!-- ============ MODAL EXPORT =============== -->
<div class="modal fade labs-modal" id="modal_export" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="modal-title">Notifikasi</h3>
         </div>
         <div class="modal-body">
            <h4 class="text-center">Silahkan pilih filter terlebih dahulu</h4>
         </div>
         <div class="modal-footer">
            <button class="labs-btn labs-btn--default labs-btn--sm" data-dismiss="modal" aria-hidden="true">Tutup</button>
         </div>
      </div>
   </div>
</div>
<!--END MODAL EXPORT -->

<!-- Page script -->
<script>
  $(document).ready(function(){

    // export guard: wajib pilih filter ekskul dulu
    $('#btn_export').click(function(){
      var params = new URLSearchParams(window.location.search);
      if (!params.get('id_ekskul')) {
         $('#modal_export').modal('show');
         return false;
      }
      var q = window.location.search;
      document.location.href = '<?= site_url('administrator/ekskul_presensi_siswa_smp/export'); ?>' + q;
      return false;
    });

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


    $('#apply').click(function(){

      var bulk = $('#bulk');
      var serialize_bulk = $('#form_ekskul_presensi_siswa_smp').serialize();

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
               document.location.href = BASE_URL + '/administrator/ekskul_presensi_siswa_smp/delete?' + serialize_bulk;
            }
          });

        return false;

      } else if(bulk.val() == '')  {
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

    });/*end appliy click*/


    //check all
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

  }); /*end doc ready*/
</script>
