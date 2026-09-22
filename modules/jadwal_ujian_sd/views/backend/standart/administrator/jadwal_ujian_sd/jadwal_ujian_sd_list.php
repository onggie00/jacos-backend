
<script type="text/javascript">
   <?php if ($this->session->flashdata('success')) { ?>
      toastr.success("<?php echo $this->session->flashdata('success'); ?>");
   <?php } else if ($this->session->flashdata('error')) {  ?>
      toastr.error("<?php echo $this->session->flashdata('error'); ?>");
   <?php } else if ($this->session->flashdata('warning')) {  ?>
      toastr.warning("<?php echo $this->session->flashdata('warning'); ?>");
   <?php } else if ($this->session->flashdata('info')) {  ?>
      toastr.info("<?php echo $this->session->flashdata('info'); ?>");
   <?php } ?>
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('jadwal_ujian_sd') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('jadwal_ujian_sd') ?></li>
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
                        <a class="btn btn-flat btn-success" title="Import Mata Pelajaran Ujian" data-toggle="modal" data-target="#modal_import"><i class="fa fa-pencil"></i> Import Jadwal Ujian</a>
                        <?php is_allowed('jadwal_ujian_sd_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('jadwal_ujian_sd')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/jadwal_ujian_sd/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('jadwal_ujian_sd')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('jadwal_ujian_sd_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('jadwal_ujian_sd') ?>" href="<?= site_url('administrator/jadwal_ujian_sd/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('jadwal_ujian_sd') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('jadwal_ujian_sd')]); ?>  <i class="label bg-yellow"><?= $jadwal_ujian_sd_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_jadwal_ujian_sd" id="form_jadwal_ujian_sd" action="<?= base_url('administrator/jadwal_ujian_sd/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('id_mapel') ?></th>
                           <th> <?= cclang('id_jenis_ujian') ?></th>
                           <th> <?= cclang('id_tingkatan') ?></th>
                           <th> <?= cclang('daftar_kelas') ?></th>
                           <th> <?= cclang('tanggal') ?></th>
                           <th> <?= cclang('hari') ?></th>
                           <th> <?= cclang('jam_mulai') ?></th>
                           <th> <?= cclang('jam_selesai') ?></th>
                           <th> <?= cclang('id_tahun_ajaran') ?></th>
                           <th> <?= cclang('semester') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_jadwal_ujian_sd">
                     <?php foreach($jadwal_ujian_sds as $jadwal_ujian_sd): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $jadwal_ujian_sd->id_jadwal; ?>">
                           </td>
                                                       
                           <td><?php if  ($jadwal_ujian_sd->id_mapel) {

                              echo anchor('administrator/ujian_mapel_sd/view/'.$jadwal_ujian_sd->id_mapel.'?popup=show', $jadwal_ujian_sd->ujian_mapel_sd_nama_mapel, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($jadwal_ujian_sd->id_jenis_ujian) {

                              echo anchor('administrator/jenis_ujian/view/'.$jadwal_ujian_sd->id_jenis_ujian.'?popup=show', $jadwal_ujian_sd->jenis_ujian_nama_ujian, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($jadwal_ujian_sd->id_tingkatan) {

                              echo anchor('administrator/tingkatan_sd/view/'.$jadwal_ujian_sd->id_tingkatan.'?popup=show', $jadwal_ujian_sd->tingkatan_sd_label, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= join_multi_select($jadwal_ujian_sd->daftar_kelas, 'kelas_sd', 'label', 'label'); ?></td> 
                           <td><?= _ent($jadwal_ujian_sd->tanggal); ?></td> 
                           <td><?= _ent($jadwal_ujian_sd->hari); ?></td> 
                           <td><?= date("H:i", strtotime($jadwal_ujian_sd->jam_mulai)); ?></td> 
                           <td><?= date("H:i", strtotime($jadwal_ujian_sd->jam_selesai)); ?></td> 
                           <td><?php if  ($jadwal_ujian_sd->id_tahun_ajaran) {

                              echo anchor('administrator/tahun_ajaran/view/'.$jadwal_ujian_sd->id_tahun_ajaran.'?popup=show', $jadwal_ujian_sd->tahun_ajaran_label, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($jadwal_ujian_sd->semester); ?></td> 
                           <td width="200">
                              <?php is_allowed('jadwal_ujian_sd_update', function() use ($jadwal_ujian_sd){?>
                              <a href="<?= site_url('administrator/jadwal_ujian_sd/edit/' . $jadwal_ujian_sd->id_jadwal); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('jadwal_ujian_sd_delete', function() use ($jadwal_ujian_sd){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/jadwal_ujian_sd/delete/' . $jadwal_ujian_sd->id_jadwal); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($jadwal_ujian_sd_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Jadwal Ujian SD data is not available
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
                            <option <?= $this->input->get('f') == 'id_mapel' ? 'selected' :''; ?> value="id_mapel">Mata Pelajaran</option>
                           <option <?= $this->input->get('f') == 'id_jenis_ujian' ? 'selected' :''; ?> value="id_jenis_ujian">Jenis Ujian</option>
                           <option <?= $this->input->get('f') == 'id_tingkatan' ? 'selected' :''; ?> value="id_tingkatan">Tingkatan</option>
                           <option <?= $this->input->get('f') == 'daftar_kelas' ? 'selected' :''; ?> value="daftar_kelas">Daftar Kelas</option>
                           <option <?= $this->input->get('f') == 'tanggal' ? 'selected' :''; ?> value="tanggal">Tanggal Ujian</option>
                           <option <?= $this->input->get('f') == 'hari' ? 'selected' :''; ?> value="hari">Hari</option>
                           <option <?= $this->input->get('f') == 'jam_mulai' ? 'selected' :''; ?> value="jam_mulai">Jam Mulai</option>
                           <option <?= $this->input->get('f') == 'jam_selesai' ? 'selected' :''; ?> value="jam_selesai">Jam Selesai</option>
                           <option <?= $this->input->get('f') == 'id_tahun_ajaran' ? 'selected' :''; ?> value="id_tahun_ajaran">Tahun Ajaran</option>
                           <option <?= $this->input->get('f') == 'semester' ? 'selected' :''; ?> value="semester">Semester</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/jadwal_ujian_sd');?>" title="<?= cclang('reset_filter'); ?>">
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

<!-- ============ MODAL IMPORT DATA JADWAL UJIAN  =============== -->
<div class="modal fade" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Import Jadwal Ujian </h3>
         </div>
         <form action="<?= base_url('administrator/jadwal_ujian_sd/import_jadwal/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">File</label>
                  <div class="col-xs-8">
                     <input name="file_import" class="form-control" type="file"  accept=".xls,.xlsx" required>
                  </div>
               </div>
               <span>*Pastikan penamaan Mapel sudah benar. (huruf besar kecil)</span><br/>
               <span>*Disarankan menggunakan huruf kapital agar tidak ada kesalahan saat mengeja mapel</span><br/>
               <span>*Semua jadwal ujian saat ini akan dijadikan sebagai jadwal yang valid dan dimasukkan kedalam kartu ujian masing-masing siswa</span><br/>
               <span>*File yang akan diimport akan sesuai format file hasil export Excel</span><br/>
            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Tutup</button>
               <button type="submit" class="btn btn-info btn_save">Import</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL IMPORT DATA JADWAL UJIAN -->

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
      var serialize_bulk = $('#form_jadwal_ujian_sd').serialize();

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
               document.location.href = BASE_URL + '/administrator/jadwal_ujian_sd/delete?' + serialize_bulk;      
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