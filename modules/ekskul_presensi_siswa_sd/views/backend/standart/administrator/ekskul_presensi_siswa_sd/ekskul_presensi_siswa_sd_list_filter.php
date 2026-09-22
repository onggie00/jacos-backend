
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('ekskul_presensi_siswa_sd') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('ekskul_presensi_siswa_sd') ?></li>
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
                        <a class="btn btn-flat btn-success" title="Filter Presensi" data-toggle="modal" data-target="#modal_filter"><i class="fa fa-users"></i> Filter Presensi</a>
                        <?php is_allowed('ekskul_presensi_siswa_sd_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('ekskul_presensi_siswa_sd') ?>" href="<?= site_url('administrator/ekskul_presensi_siswa_sd/export?').'id_ekskul='.$this->input->get('id_ekskul').'&bulan='.$this->input->get('bulan').'&tahun='.$this->input->get('tahun'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('ekskul_presensi_siswa_sd') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('ekskul_presensi_siswa_sd')]); ?>  <i class="label bg-yellow"><?= $value_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_ekskul_presensi_siswa_sd" id="form_ekskul_presensi_siswa_sd" action="<?= base_url('administrator/ekskul_presensi_siswa_sd/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                           <th>
                           <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                           <th> <?= cclang('id_siswa_aktif') ?></th>
                           <th> <?= cclang('id_ekskul') ?></th>
                           <th> <?= cclang('hari_absen') ?></th>
                           <th> <?= cclang('tanggal_absen') ?></th>
                           <th> <?= cclang('waktu_absen') ?></th>
                           <th> <?= cclang('status_absen') ?></th>
                           <th> <?= cclang('keterangan_presensi') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_ekskul_presensi_siswa_sd">
                     <?php foreach($data_presensi as $key => $value): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $value->id; ?>">
                           </td>
                                                       
                           <td><?php if  ($value->id_siswa_aktif) {

                              echo anchor('administrator/siswa_sd_aktif/view/'.$value->id_siswa_aktif.'?popup=show', $value->nama_lengkap, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($value->id_ekskul) {

                              echo anchor('administrator/ekskul/view/'.$value->id_ekskul.'?popup=show', $value->nama_ekskul, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($value->hari_absen); ?></td> 
                           <td><?= _ent($value->tanggal_absen); ?></td> 
                           <td><?= _ent($value->waktu_absen); ?></td> 
                           <td><?= _ent($value->status_absen); ?></td> 
                           <td><?= _ent($value->keterangan_presensi); ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('ekskul_presensi_siswa_sd_view', function() use ($value){?>
                                 <a href="<?= site_url('administrator/ekskul_presensi_siswa_sd/single_pdf/' .$value->id); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/ekskul_presensi_siswa_sd/view/' . $value->id); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('ekskul_presensi_siswa_sd_update', function() use ($value){?>
                              <a href="<?= site_url('administrator/ekskul_presensi_siswa_sd/edit/' . $value->id); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('ekskul_presensi_siswa_sd_delete', function() use ($value){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/ekskul_presensi_siswa_sd/delete/' . $value->id); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($value_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Presensi Siswa SD data is not available
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
                           <option value="delete">Delete</option>
                        </select>
                     </div>
                     <div class="col-sm-2 padd-left-0 ">
                        <button type="button" class="btn btn-flat" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>"><?= cclang('apply_button'); ?></button>
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
<!-- ============ MODAL GENERATE  =============== -->
<div class="modal fade" id="modal_filter" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Filter Presensi</h3>
         </div>
         <form action="<?= base_url('administrator/ekskul_presensi_siswa_sd/filter_presensi'); ?>" method="get" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Ekstrakurikuler</label>
                  <div class="col-xs-8">
                     <select name="id_ekskul" class="form-control">
                        <?php 
                           $list_ekskul = $this->mymodel->withquery("select * from ekskul where jenjang = 'sd' order by nama ASC","result");
                           foreach ($list_ekskul as $key => $value) {
                              if ($value->id_ekskul == $this->input->get('id_ekskul')) {
                                 echo "<option value='".$value->id_ekskul."' selected>".$value->nama."</option>";
                              }
                              else{
                                 echo "<option value='".$value->id_ekskul."'>".$value->nama."</option>";
                              }
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
                              if ($this->input->get("bulan") == $i) {
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
                              if ($this->input->get("tahun") == $list) {
                                 echo "<option value='".$list."' selected>".$list."</option>";
                              }
                              else{
                                 echo "<option value='".$list."'>".$list."</option>";
                              }
                           }
                           for ($i=0; $i <=25; $i++) { 
                              $list = $thn + $i;
                              if ($this->input->get("tahun") == $list) {
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
               <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
               <button type="submit" class="btn btn-info btn_save">Terapkan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL GENERATE -->

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
      var serialize_bulk = $('#form_ekskul_presensi_siswa_sd').serialize();

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
               document.location.href = BASE_URL + '/administrator/ekskul_presensi_siswa_sd/delete?' + serialize_bulk;      
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