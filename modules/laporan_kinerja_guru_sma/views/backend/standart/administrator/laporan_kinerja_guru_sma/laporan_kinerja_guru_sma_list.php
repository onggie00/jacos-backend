
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('laporan_kinerja_guru_sma') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('laporan_kinerja_guru_sma') ?></li>
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
                        <a class="btn btn-flat btn-success" title="Import Laporan Kinerja Guru SMA" data-toggle="modal" data-target="#modal_import"> Import Laporan Kinerja Guru SMA</a>
                        <?php is_allowed('laporan_kinerja_guru_sma_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('laporan_kinerja_guru_sma') ?>" href="<?= site_url('administrator/laporan_kinerja_guru_sma/export?q='.$this->input->get('q').'&f='.$this->input->get('f')); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('laporan_kinerja_guru_sma') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('laporan_kinerja_guru_sma')]); ?>  <i class="label bg-yellow"><?= $laporan_kinerja_guru_sma_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_laporan_kinerja_guru_sma" id="form_laporan_kinerja_guru_sma" action="<?= base_url('administrator/laporan_kinerja_guru_sma/index'); ?>">
                  <?php
                     if (!empty($this->session->flashdata('success'))) {
                  ?>
                     <div class="alert alert-success alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Success!</strong> <?= $this->session->flashdata('success'); ?>
                     </div>
                  <?php
                     }
                  ?>
                  <?php
                     if (!empty($this->session->flashdata('failed'))) {
                  ?>
                     <div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Gagal!</strong> <?= $this->session->flashdata('failed'); ?>
                     </div>
                  <?php
                     }
                  ?>

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                           <th class="text-center">
                           <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                           <th class="text-center"> <?= cclang('id_guru') ?></th>
                           <th class="text-center"> <?= "NPP" ?></th>
                           <th class="text-center"> <?= cclang('unit_kerja') ?></th>
                           <th class="text-center"> <?= cclang('mata_pelajaran') ?></th>
                           <th class="text-center"> Kompetensi Pedagogik</th>
                           <th class="text-center"> Kompetensi Profesional</th>
                           <th class="text-center"> Kompetensi Kepribadian</th>
                           <th class="text-center"> Kompetensi Sosial</th>
                           <th class="text-center"> Leadership</th>
                           <th class="text-center"> <?= cclang('nilai_prestasi') ?></th>
                           <th class="text-center"> <?= cclang('nilai_presensi') ?></th>
                           <th class="text-center"> <?= "NILAI" ?></th>
                           <th class="text-center"> <?= cclang('tahun_ajaran') ?></th>
                           <th class="text-center">Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_laporan_kinerja_guru_sma">
                     <?php foreach($laporan_kinerja_guru_smas as $laporan_kinerja_guru_sma): ?>
                        <tr>
                           <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $laporan_kinerja_guru_sma->id_laporan; ?>">
                           </td>
                           
                           <td class="text-center"><?php if  ($laporan_kinerja_guru_sma->id_guru) {

                              echo anchor('administrator/guru_sma/view/'.$laporan_kinerja_guru_sma->id_guru.'?popup=show', $laporan_kinerja_guru_sma->guru_sma_nama_lengkap, ['class' => 'popup-view']); }?> </td>
                           <td class="text-center"><?= _ent($laporan_kinerja_guru_sma->npp); ?></td>
                           <td class="text-center"><?= _ent($laporan_kinerja_guru_sma->unit_kerja); ?></td> 
                           <td class="text-center"><?= _ent($laporan_kinerja_guru_sma->mata_pelajaran); ?></td> 
                           <td><?= _ent($laporan_kinerja_guru_sma->kompetensi_pedagogik); ?></td> 
                           <td><?= _ent($laporan_kinerja_guru_sma->kompetensi_profesional); ?></td> 
                           <td><?= _ent($laporan_kinerja_guru_sma->kompetensi_kepribadian); ?></td> 
                           <td><?= _ent($laporan_kinerja_guru_sma->kompetensi_sosial); ?></td> 
                           <td><?= _ent($laporan_kinerja_guru_sma->leadership); ?></td> 
                           <td><?= _ent($laporan_kinerja_guru_sma->nilai_prestasi); ?></td> 
                           <td><?= _ent($laporan_kinerja_guru_sma->nilai_presensi); ?></td> 
                           <td class="text-center"><?= _ent($laporan_kinerja_guru_sma->total_skor); ?></td>
                           <td class="text-center"><?= _ent($laporan_kinerja_guru_sma->tahun_ajaran); ?></td> 
                           <td class="text-center" width="200">
                           
                              <?php is_allowed('laporan_kinerja_guru_sma_view', function() use ($laporan_kinerja_guru_sma){?>
                                 <a target="_blank" href="<?= site_url('apiapp/export_rapor_kinerja?tipe_rapor=guru_sma&id=' .$laporan_kinerja_guru_sma->id_laporan); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/laporan_kinerja_guru_sma/view/' . $laporan_kinerja_guru_sma->id_laporan); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <br/>
                              <?php is_allowed('laporan_kinerja_guru_sma_update', function() use ($laporan_kinerja_guru_sma){?>
                              <a href="<?= site_url('administrator/laporan_kinerja_guru_sma/edit/' . $laporan_kinerja_guru_sma->id_laporan); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('laporan_kinerja_guru_sma_delete', function() use ($laporan_kinerja_guru_sma){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/laporan_kinerja_guru_sma/delete/' . $laporan_kinerja_guru_sma->id_laporan); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($laporan_kinerja_guru_sma_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Laporan Kinerja Guru SMA data is not available
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
                           <option <?= $this->input->get('f') == 'tahun_ajaran' ? 'selected' :''; ?> value="tahun_ajaran">Tahun Ajaran</option>
                        </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/laporan_kinerja_guru_sma');?>" title="<?= cclang('reset_filter'); ?>">
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

<!-- ============ MODAL IMPORT  =============== -->
<div class="modal fade" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Import Data Laporan Kinerja</h3>
         </div>
         <form action="<?= base_url('administrator/laporan_kinerja_guru_sma/import'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Upload File Excel</label>
                  <div class="col-xs-8">
                     <input type="file" class="form-control" name="file_upload" required>
                     <small class="info help-block">
                        Pastikan file yang diupload sudah benar, dan sesuai format yang di export oleh sistem.
                     </small>
                  </div>
               </div>

               <div class="form-group">
                  <label class="control-label col-xs-3">Tahun Ajaran</label>
                  <div class="col-xs-8">
                     <input type="text" class="form-control" name="tahun_ajaran" placeholder="<?= date("Y")."-".(date("Y")+1); ?>" required>
                     <small class="info help-block">
                        Tahun Ajaran dengan format <b>Tahun-Tahun</b>
                     </small>
                  </div>
               </div>

            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
               <button type="submit" class="btn btn-info btn_save">Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END IMPORT -->

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
      var serialize_bulk = $('#form_laporan_kinerja_guru_sma').serialize();

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
               document.location.href = BASE_URL + '/administrator/laporan_kinerja_guru_sma/delete?' + serialize_bulk;      
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