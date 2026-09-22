
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('laporan_kinerja_pimpinan') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('laporan_kinerja_pimpinan') ?></li>
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
                        <a class="btn btn-flat btn-success" title="Import Laporan Kinerja Pimpinan" data-toggle="modal" data-target="#modal_import"> Import Laporan Kinerja Pimpinan</a>
                        <?php is_allowed('laporan_kinerja_pimpinan_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('laporan_kinerja_pimpinan') ?>" href="<?= site_url('administrator/laporan_kinerja_pimpinan/export?q='.$this->input->get('q').'&f='.$this->input->get('f')); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('laporan_kinerja_pimpinan_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new hidden" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('laporan_kinerja_pimpinan')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/laporan_kinerja_pimpinan/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('laporan_kinerja_pimpinan')]); ?></a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('laporan_kinerja_pimpinan') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('laporan_kinerja_pimpinan')]); ?>  <i class="label bg-yellow"><?= $laporan_kinerja_pimpinan_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_laporan_kinerja_pimpinan" id="form_laporan_kinerja_pimpinan" action="<?= base_url('administrator/laporan_kinerja_pimpinan/index'); ?>">
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
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                           <th class="text-center"> <?= cclang('id_pimpinan') ?></th>
                           <th class="text-center"> <?= "NPP"; ?></th>
                           <th class="text-center"> <?= cclang('jenjang') ?></th>
                           <th class="text-center"> <?= cclang('jabatan') ?></th>
                           <th class="text-center"> Kepribadian Sosial</th>
                           <th class="text-center"> Leadership</th>
                           <th class="text-center"> Pengembangan Sekolah</th>
                           <th class="text-center"> Bidang Tugas Wakil Akademik/Kesiswaan</th>
                           <th class="text-center"> <?= cclang('total_skor') ?></th>
                           <th class="text-center"> <?= cclang('rank') ?></th>
                           <th class="text-center"> <?= cclang('tahun_ajaran') ?></th>
                           <th class="text-center">Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_laporan_kinerja_pimpinan">
                     <?php foreach($laporan_kinerja_pimpinans as $laporan_kinerja_pimpinan): ?>
                        <tr>
                           <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $laporan_kinerja_pimpinan->id_laporan; ?>">
                           </td>
                        
                           <td><?php if  ($laporan_kinerja_pimpinan->id_pimpinan) {
                              $nama_kolom = "pimpinan_".strtolower($laporan_kinerja_pimpinan->jenjang)."_nama_lengkap";
                              $kolom_npp = "npp_".strtolower($laporan_kinerja_pimpinan->jenjang);
                              echo anchor('administrator/pimpinan_'.strtolower($laporan_kinerja_pimpinan->jenjang).'/view/'.$laporan_kinerja_pimpinan->id_pimpinan.'?popup=show', $laporan_kinerja_pimpinan->$nama_kolom, ['class' => 'popup-view']); }?> </td>
                           <td class="text-center"><?= _ent($laporan_kinerja_pimpinan->$kolom_npp); ?></td>
                           <td class="text-center"><?= _ent($laporan_kinerja_pimpinan->jenjang); ?></td> 
                           <td class="text-center"><?= _ent($laporan_kinerja_pimpinan->jabatan); ?></td> 
                           <td class="text-center"><?= _ent($laporan_kinerja_pimpinan->kepribadian_sosial); ?></td> 
                           <td class="text-center"><?= _ent($laporan_kinerja_pimpinan->leadership); ?></td> 
                           <td class="text-center"><?= _ent($laporan_kinerja_pimpinan->pengembangan_sekolah); ?></td> 
                           <td class="text-center"><?= _ent($laporan_kinerja_pimpinan->bidang_tugas_wakil_akademik_kesiswaan); ?></td>
                           <td class="text-center"><?= _ent($laporan_kinerja_pimpinan->total_skor); ?></td> 
                           <td class="text-center"><?= _ent($laporan_kinerja_pimpinan->rank); ?></td> 
                           <td class="text-center"><?= _ent($laporan_kinerja_pimpinan->tahun_ajaran); ?></td> 
                           <td class="text-center" width="200">
                           
                           <?php is_allowed('laporan_kinerja_pimpinan_view', function() use ($laporan_kinerja_pimpinan){?>
                              <a target="_blank" href="<?= site_url('apiapp/export_rapor_kinerja_pimpinan?jenjang='.$laporan_kinerja_pimpinan->jenjang.'&id=' .$laporan_kinerja_pimpinan->id_laporan); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/laporan_kinerja_pimpinan/view/' . $laporan_kinerja_pimpinan->id_laporan); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <br/>
                              <?php is_allowed('laporan_kinerja_pimpinan_update', function() use ($laporan_kinerja_pimpinan){?>
                              <a href="<?= site_url('administrator/laporan_kinerja_pimpinan/edit/' . $laporan_kinerja_pimpinan->id_laporan); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('laporan_kinerja_pimpinan_delete', function() use ($laporan_kinerja_pimpinan){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/laporan_kinerja_pimpinan/delete/' . $laporan_kinerja_pimpinan->id_laporan); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($laporan_kinerja_pimpinan_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Laporan Kinerja Pimpinan data is not available
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
                            <option <?= $this->input->get('f') == 'id_pimpinan' ? 'selected' :''; ?> value="id_pimpinan">Pimpinan</option>
                           <option <?= $this->input->get('f') == 'jenjang' ? 'selected' :''; ?> value="jenjang">Unit</option>
                           <option <?= $this->input->get('f') == 'jabatan' ? 'selected' :''; ?> value="jabatan">Jabatan</option>
                           <option <?= $this->input->get('f') == 'total_skor' ? 'selected' :''; ?> value="total_skor">Total Skor</option>
                           <option <?= $this->input->get('f') == 'rank' ? 'selected' :''; ?> value="rank">Rank</option>
                           <option <?= $this->input->get('f') == 'tahun_ajaran' ? 'selected' :''; ?> value="tahun_ajaran">Tahun Ajaran</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/laporan_kinerja_pimpinan');?>" title="<?= cclang('reset_filter'); ?>">
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
         <form action="<?= base_url('administrator/laporan_kinerja_pimpinan/import'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
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
                     <input type="text" class="form-control" name="tahun_ajaran" placeholder="<?= date("Y")."/".(date("Y")+1); ?>" required>
                     <small class="info help-block">
                        Tahun Ajaran dengan format <b>Tahun/Tahun</b>
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
      var serialize_bulk = $('#form_laporan_kinerja_pimpinan').serialize();

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
               document.location.href = BASE_URL + '/administrator/laporan_kinerja_pimpinan/delete?' + serialize_bulk;      
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