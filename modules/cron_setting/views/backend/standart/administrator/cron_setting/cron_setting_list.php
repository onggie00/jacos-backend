
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('cron_setting') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('cron_setting') ?></li>
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
                        <a class="btn btn-flat btn-success" title="Pilihan Setting" data-toggle="modal" data-target="#modal_pilihan"> Pilihan Setting</a>
                        <?php is_allowed('cron_setting_add', function(){?>
                        <a class="hidden btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('cron_setting')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/cron_setting/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('cron_setting')]); ?></a>
                        <?php }) ?>
                        
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('cron_setting') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('cron_setting')]); ?>  <i class="label bg-yellow"><?= $cron_setting_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_cron_setting" id="form_cron_setting" action="<?= base_url('administrator/cron_setting/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                           <th>
                           <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('nama_cron') ?></th>
                           <th> <?= cclang('id_siswa_aktif') ?></th>
                           <th> <?= cclang('jenjang') ?></th>
                           <th> <?= cclang('id_kelas') ?></th>
                           <th> <?= cclang('is_active') ?></th>
                           <th> <?= cclang('date_deactivate') ?></th>
                           <th> <?= cclang('date_reactivate') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_cron_setting">
                     <?php foreach($cron_settings as $cron_setting): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $cron_setting->id_cron_setting; ?>">
                           </td>
                                                       
                           <td><?= _ent($cron_setting->nama_cron); ?></td> 
                           <td><?php if  ($cron_setting->id_siswa_aktif) {
                              $kolom_siswa = "siswa_".$cron_setting->jenjang."_aktif_nama_lengkap";
                              echo anchor('administrator/siswa_'.$cron_setting->jenjang.'_aktif/view/'.$cron_setting->id_siswa_aktif.'?popup=show', $cron_setting->$kolom_siswa, ['class' => 'popup-view']); }?> </td>
                           
                           <td><?= _ent($cron_setting->jenjang); ?></td> 
                           <td><?php if  ($cron_setting->id_kelas) {
                              $kolom_kelas = "kelas_".$cron_setting->jenjang."_nama_kelas";
                              echo anchor('administrator/kelas_'.$cron_setting->jenjang.'/view/'.$cron_setting->id_kelas.'?popup=show', $cron_setting->$kolom_kelas, ['class' => 'popup-view']); }?> </td>
                           
                           <td><?= (!empty($cron_setting->is_active)) ? "Aktif" :"Nonaktif" ; ?></td> 
                           <td><?= (!empty($cron_setting->date_deactivate)) ? date('d-m-Y', strtotime($cron_setting->date_deactivate)) : "-"; ?></td> 
                           <td><?= (!empty($cron_setting->date_reactivate)) ? date('d-m-Y', strtotime($cron_setting->date_reactivate)) : "-"; ?></td> 
                           <td width="200">
                           
                           <?php is_allowed('cron_setting_view', function() use ($cron_setting){?>
                              <a href="<?= site_url('administrator/cron_setting/view/' . $cron_setting->id_cron_setting); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('cron_setting_update', function() use ($cron_setting){?>
                              <a href="<?= site_url('administrator/cron_setting/edit/' . $cron_setting->id_cron_setting); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('cron_setting_delete', function() use ($cron_setting){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/cron_setting/delete/' . $cron_setting->id_cron_setting); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                              <?php }) ?>

                           </td>                        </tr>
                     <?php endforeach; ?>
                     <?php if ($cron_setting_counts == 0) :?>
                        <tr>
                           <td colspan="100">
                           Cron Setting data is not available
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
                            <option <?= $this->input->get('f') == 'nama_cron' ? 'selected' :''; ?> value="nama_cron">Nama CRON</option>
                           <option <?= $this->input->get('f') == 'id_siswa_aktif' ? 'selected' :''; ?> value="id_siswa_aktif">Siswa Aktif</option>
                           <option <?= $this->input->get('f') == 'jenjang' ? 'selected' :''; ?> value="jenjang">Jenjang</option>
                           <option <?= $this->input->get('f') == 'id_kelas' ? 'selected' :''; ?> value="id_kelas">Kelas</option>
                           <option <?= $this->input->get('f') == 'is_active' ? 'selected' :''; ?> value="is_active">Aktif?</option>
                           <option <?= $this->input->get('f') == 'date_deactivate' ? 'selected' :''; ?> value="date_deactivate">Tanggal Nonaktifkan Ulang</option>
                           <option <?= $this->input->get('f') == 'date_reactivate' ? 'selected' :''; ?> value="date_reactivate">Tanggal Pengaktifan Ulang</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/cron_setting');?>" title="<?= cclang('reset_filter'); ?>">
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

<!-- ============ MODAL PILIHAN  =============== -->
<div class="modal fade" id="modal_pilihan" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Pilihan Setting CRON</h3>
         </div>
         <form action="<?= base_url('administrator/cron_setting/set_cron'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Jenjang</label>
                  <div class="col-xs-8" style="margin-left: 20px">
                     <label class="col-sm-2 checkbox-inline">
                     <input type="checkbox" name="jenjang[]" id="sd" value="sd">SD</label>
                     <label class="col-sm-2 checkbox-inline">
                     <input type="checkbox" name="jenjang[]" id="smp" value="smp">SMP</label>
                     <label class="col-sm-2 checkbox-inline">
                     <input type="checkbox" name="jenjang[]" id="sma" value="sma">SMA</label>
                     <label class="col-sm-2 checkbox-inline">
                     <input type="checkbox" name="jenjang[]" id="ft" value="ft">FT</label>
                  </div>
               </div>

               <div class="form-group">
               <?php
                  $kelas_sd = $this->mymodel->withquery("select * from kelas_sd where label not like '%mutasi%' and label not like '%alumni%' order by id_tingkatan asc, label asc","result");
                  $kelas_smp = $this->mymodel->withquery("select * from kelas_smp where label not like '%mutasi%' and label not like '%alumni%' order by id_tingkatan asc, label asc","result");
                  $kelas_sma = $this->mymodel->withquery("select * from kelas_sma where label not like '%mutasi%' and label not like '%alumni%' order by id_tingkatan asc, label asc","result");
                  $kelas_ft = $this->mymodel->withquery("select * from kelas_ft where label not like '%mutasi%' and label not like '%alumni%' order by id_tingkatan asc, label asc","result");
               ?>
                  <label class="control-label col-xs-3">Kelas</label>
                  <div class="col-xs-8">
                     <div class="col-sm-3">
                        <div class="row" style="margin-left: 10px;"><label>SD</label></div>
                        <div class="row" style="height:200px; overflow-y: scroll">
                           <?php
                              foreach ($kelas_sd as $key => $value) {
                           ?>
                              <div class="col" style="margin-left: 20px;">
                                 <label class="checkbox-inline">
                                 <input type="checkbox" name="id_kelas_sd[]" id="sd" value="<?= $value->id_kelas_sd; ?>"><?= $value->label; ?></label>
                              </div>
                           <?php
                              }
                           ?>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="row" style="margin-left: 10px;"><label>SMP</label></div>
                        <div class="row" style="height:200px; overflow-y: scroll">
                        <?php
                              foreach ($kelas_smp as $key => $value) {
                           ?>
                              <div class="col" style="margin-left: 20px;">
                                 <label class="checkbox-inline">
                                 <input type="checkbox" name="id_kelas_smp[]" id="smp" value="<?= $value->id_kelas_smp; ?>"><?= $value->label; ?></label>
                              </div>
                           <?php
                              }
                           ?>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="row" style="margin-left: 10px;"><label>SMA</label></div>
                        <div class="row" style="height:200px; overflow-y: scroll">
                           <?php
                              foreach ($kelas_sma as $key => $value) {
                           ?>
                              <div class="col" style="margin-left: 20px;">
                                 <label class="checkbox-inline">
                                 <input type="checkbox" name="id_kelas_sma[]" id="sma" value="<?= $value->id_kelas_sma; ?>"><?= $value->label; ?></label>
                              </div>
                           <?php
                              }
                           ?>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="row" style="margin-left: 10px;"><label>FT</label></div>
                        <div class="row" style="height:200px; overflow-y: scroll">
                           <?php
                              foreach ($kelas_ft as $key => $value) {
                           ?>
                              <div class="col" style="margin-left: 10px;">
                                 <label class="checkbox-inline">
                                 <input type="checkbox" name="id_kelas_ft[]" id="ft" value="<?= $value->id_kelas_ft; ?>"><?= $value->label; ?></label>
                              </div>
                           <?php
                              }
                           ?>
                        </div>
                     </div>

                  </div>
               </div>

               <div class="form-group" style="text-align: center;font-weight: bold">
                  Setting Manual
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Status Aktif</label>
                  <div class="col-xs-8">
                     <select name="is_active" class="form-control">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                     </select>
                  </div>
               </div>

               <div class="form-group" style="text-align: center;font-weight: bold">
                  Setting Otomatis
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Tanggal Nonaktif</label>
                  <div class="col-xs-8">
                     <input type="date" class="form-control" name="date_deactivate" />
                  </div>
               </div>

               <div class="form-group">
                  <label class="control-label col-xs-3">Tanggal Aktivasi ulang</label>
                  <div class="col-xs-8">
                     <input type="date" class="form-control" name="date_reactivate" />
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
<!--END MODAL PILIHAN -->

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
      var serialize_bulk = $('#form_cron_setting').serialize();

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
               document.location.href = BASE_URL + '/administrator/cron_setting/delete?' + serialize_bulk;      
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