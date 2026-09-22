
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('pt_to_siswa') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('pt_to_siswa') ?></li>
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
                        <?php is_allowed('pt_to_siswa_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" style="display:none" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('pt_to_siswa')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/pt_to_siswa/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('pt_to_siswa')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('pt_to_siswa_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('pt_to_siswa') ?>" href="<?= site_url('administrator/pt_to_siswa/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-success" title="Import Data UTBK Siswa" data-toggle="modal" data-target="#modal_import"><i class="fa fa-pencil"></i> Import Data UTBK</a>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('pt_to_siswa') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('pt_to_siswa')]); ?>  <i class="label bg-yellow"><?= $pt_to_siswa_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_pt_to_siswa" id="form_pt_to_siswa" action="<?= base_url('administrator/pt_to_siswa/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('jenjang') ?></th>
                           <th> <?= cclang('id_siswa_aktif') ?></th>
                           <th> <?= cclang('id_kelas') ?></th>
                           <th> <?= cclang('nilai_utbk1') ?></th>
                           <th> <?= cclang('nilai_utbk2') ?></th>
                           <th> <?= cclang('nilai_utbk3') ?></th>
                           <th> <?= cclang('nilai_utbk4') ?></th>
                           <th> <?= cclang('nilai_nasional') ?></th>
                           <th> <?= "Tahun Ajaran" ?></th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_pt_to_siswa">
                     <?php foreach($pt_to_siswas as $pt_to_siswa): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $pt_to_siswa->id; ?>">
                           </td>
                                                       
                           <td><?= _ent($pt_to_siswa->jenjang); ?></td> 
                           <td><?php if  ($pt_to_siswa->id_siswa_aktif) {
                              $kolom = "siswa_".strtolower($pt_to_siswa->jenjang)."_aktif_nama_lengkap";
                              echo anchor('administrator/siswa_'.strtolower($pt_to_siswa->jenjang).'_aktif/view/'.$pt_to_siswa->id_siswa_aktif.'?popup=show', $pt_to_siswa->$kolom, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($pt_to_siswa->id_kelas) {
                              $kolom_kelas = "kelas_".strtolower($pt_to_siswa->jenjang)."_label";
                              echo anchor('administrator/kelas_'.strtolower($pt_to_siswa->jenjang).'/view/'.$pt_to_siswa->id_kelas.'?popup=show', $pt_to_siswa->$kolom_kelas, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($pt_to_siswa->nilai_utbk1); ?></td> 
                           <td><?= _ent($pt_to_siswa->nilai_utbk2); ?></td> 
                           <td><?= _ent($pt_to_siswa->nilai_utbk3); ?></td> 
                           <td><?= _ent($pt_to_siswa->nilai_utbk4); ?></td> 
                           <td><?= _ent($pt_to_siswa->nilai_nasional); ?></td>
                           <td><?= _ent($pt_to_siswa->tahun_ajaran); ?></td>
                           <td width="200">
                            
                                                              <?php is_allowed('pt_to_siswa_view', function() use ($pt_to_siswa){?>
                              <a href="<?= site_url('administrator/pt_to_siswa/view/' . $pt_to_siswa->id); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('pt_to_siswa_update', function() use ($pt_to_siswa){?>
                              <a href="<?= site_url('administrator/pt_to_siswa/edit/' . $pt_to_siswa->id); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('pt_to_siswa_delete', function() use ($pt_to_siswa){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/pt_to_siswa/delete/' . $pt_to_siswa->id); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($pt_to_siswa_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           UTBK Siswa data is not available
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
                           <option <?= $this->input->get('f') == 'id_siswa_aktif' ? 'selected' :''; ?> value="id_siswa_aktif">Siswa</option>
                           <option <?= $this->input->get('f') == 'id_kelas' ? 'selected' :''; ?> value="id_kelas">Kelas</option>
                           <option <?= $this->input->get('f') == 'nilai_nasional' ? 'selected' :''; ?> value="nilai_nasional">Nilai Nasional</option>
                           <option <?= $this->input->get('f') == 'tahun_ajaran' ? 'selected' :''; ?> value="tahun_ajaran">Tahun Ajaran</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/pt_to_siswa');?>" title="<?= cclang('reset_filter'); ?>">
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
<!-- ============ MODAL IMPORT DATA SISWA  =============== -->
<div class="modal fade" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Import Data UTBK Siswa </h3>
         </div>
         <form action="<?= base_url('administrator/pt_to_siswa/import_utbk/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">File</label>
                  <div class="col-xs-8">
                     <input name="file_import" class="form-control" type="file"  accept=".xls,.xlsx" required>
                  </div>
               </div>
               <span>*Kolom Tahun ajaran sesuai periode UTBK.</span><br/>
               <span>*Pastikan seluruh data nilai adalah benar dan valid sesuai dengan hasil UTBK.</span><br/>
               <span>*Silahkan isi nilai dengan 0 apabila ingin mengosongkan nilai UTBK.</span><br/>
               <span>*Data yang akan diimport akan sesuai format file hasil export Excel</span><br/>
            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Tutup</button>
               <button type="submit" class="btn btn-info btn_save">Import</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL IMPORT DATA SISWA -->

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
      var serialize_bulk = $('#form_pt_to_siswa').serialize();

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
               document.location.href = BASE_URL + '/administrator/pt_to_siswa/delete?' + serialize_bulk;      
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