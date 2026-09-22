
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('presensi_catatan_pelajaran') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('presensi_catatan_pelajaran') ?></li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row" >
      
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-body ">
               <!-- Widget: user widget style 1 -->
               <div class="box box-widget widget-user-2">
                  <!-- Add the bg color to the header using any of the bg-* classes -->
                  <div class="widget-user-header ">
                     <div class="row pull-right">
                        <?php is_allowed('presensi_catatan_pelajaran_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('presensi_catatan_pelajaran')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/presensi_catatan_pelajaran/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('presensi_catatan_pelajaran')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('presensi_catatan_pelajaran_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('presensi_catatan_pelajaran') ?>']); ?>" href="<?= site_url('administrator/presensi_catatan_pelajaran/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('presensi_catatan_pelajaran_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('presensi_catatan_pelajaran') ?>']); ?>" href="<?= site_url('administrator/presensi_catatan_pelajaran/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('presensi_catatan_pelajaran') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('presensi_catatan_pelajaran')]); ?>  <i class="label bg-yellow"><?= $presensi_catatan_pelajaran_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_presensi_catatan_pelajaran" id="form_presensi_catatan_pelajaran" action="<?= base_url('administrator/presensi_catatan_pelajaran/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('jenjang') ?></th>
                           <th> <?= cclang('id_siswa_aktif') ?></th>
                           <th> <?= cclang('nama_lengkap') ?></th>
                           <th> <?= cclang('hari') ?></th>
                           <th> <?= cclang('tanggal_waktu') ?></th>
                           <th> <?= cclang('status_hadir') ?></th>
                           <th> <?= cclang('keterangan') ?></th>
                           <th> <?= cclang('jam_ke') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_presensi_catatan_pelajaran">
                     <?php foreach($presensi_catatan_pelajarans as $presensi_catatan_pelajaran): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $presensi_catatan_pelajaran->id_presensi_catatan; ?>">
                           </td>
                                                       
                           <td><?= _ent($presensi_catatan_pelajaran->jenjang); ?></td> 
                           <td><?php if  ($presensi_catatan_pelajaran->id_siswa_aktif) {

                              echo anchor('administrator/siswa_sd_aktif/view/'.$presensi_catatan_pelajaran->id_siswa_aktif.'?popup=show', $presensi_catatan_pelajaran->siswa_sd_aktif_nis, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($presensi_catatan_pelajaran->nama_lengkap); ?></td> 
                           <td><?= _ent($presensi_catatan_pelajaran->hari); ?></td> 
                           <td><?= _ent($presensi_catatan_pelajaran->tanggal_waktu); ?></td> 
                           <td><?= _ent($presensi_catatan_pelajaran->status_hadir); ?></td> 
                           <td><?= _ent($presensi_catatan_pelajaran->keterangan); ?></td> 
                           <td><?= _ent($presensi_catatan_pelajaran->jam_ke); ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('presensi_catatan_pelajaran_view', function() use ($presensi_catatan_pelajaran){?>
                                 <a href="<?= site_url('administrator/presensi_catatan_pelajaran/single_pdf/' .$presensi_catatan_pelajaran->id_presensi_catatan); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/presensi_catatan_pelajaran/view/' . $presensi_catatan_pelajaran->id_presensi_catatan); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('presensi_catatan_pelajaran_update', function() use ($presensi_catatan_pelajaran){?>
                              <a href="<?= site_url('administrator/presensi_catatan_pelajaran/edit/' . $presensi_catatan_pelajaran->id_presensi_catatan); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('presensi_catatan_pelajaran_delete', function() use ($presensi_catatan_pelajaran){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/presensi_catatan_pelajaran/delete/' . $presensi_catatan_pelajaran->id_presensi_catatan); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($presensi_catatan_pelajaran_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Catatan Presensi Pelajaran data is not available
                           </td>
                         </tr>
                      <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>
               <hr>
               <!-- /.widget-user -->
               <div class="row">
                  <div class="col-md-8">
                     <div class="col-sm-2 padd-left-0 " >
                        <select type="text" class="form-control chosen chosen-select" name="bulk" id="bulk" placeholder="Site Email" >
                           <option value="">Bulk</option>
                                                     <option value="delete">Delete</option>
                                                  </select>
                     </div>
                     <div class="col-sm-2 padd-left-0 ">
                        <button type="button" class="btn btn-flat" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>"><?= cclang('apply_button'); ?></button>
                     </div>
                     <div class="col-sm-3 padd-left-0  " >
                        <input type="text" class="form-control" name="q" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('q'); ?>">
                     </div>
                     <div class="col-sm-3 padd-left-0 " >
                        <select type="text" class="form-control chosen chosen-select" name="f" id="field" >
                           <option value=""><?= cclang('all'); ?></option>
                            <option <?= $this->input->get('f') == 'jenjang' ? 'selected' :''; ?> value="jenjang">Jenjang</option>
                           <option <?= $this->input->get('f') == 'id_siswa_aktif' ? 'selected' :''; ?> value="id_siswa_aktif">NIS</option>
                           <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' :''; ?> value="nama_lengkap">Nama Lengkap</option>
                           <option <?= $this->input->get('f') == 'hari' ? 'selected' :''; ?> value="hari">Hari</option>
                           <option <?= $this->input->get('f') == 'tanggal_waktu' ? 'selected' :''; ?> value="tanggal_waktu">Tanggal Waktu</option>
                           <option <?= $this->input->get('f') == 'status_hadir' ? 'selected' :''; ?> value="status_hadir">Status Kehadiran</option>
                           <option <?= $this->input->get('f') == 'keterangan' ? 'selected' :''; ?> value="keterangan">Keterangan</option>
                           <option <?= $this->input->get('f') == 'jam_ke' ? 'selected' :''; ?> value="jam_ke">Jam Ke-</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/presensi_catatan_pelajaran');?>" title="<?= cclang('reset_filter'); ?>">
                        <i class="fa fa-undo"></i>
                        </a>
                     </div>
                  </div>
                  </form>                  <div class="col-md-4">
                     <div class="dataTables_paginate paging_simple_numbers pull-right" id="example2_paginate" >
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
            </div>
            <!--/box body -->
         </div>
         <!--/box -->
      </div>
   </div>
</section>
<!-- /.content -->

<!-- Page script -->
<script>
  $(document).ready(function(){
   
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
      var serialize_bulk = $('#form_presensi_catatan_pelajaran').serialize();

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
               document.location.href = BASE_URL + '/administrator/presensi_catatan_pelajaran/delete?' + serialize_bulk;      
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