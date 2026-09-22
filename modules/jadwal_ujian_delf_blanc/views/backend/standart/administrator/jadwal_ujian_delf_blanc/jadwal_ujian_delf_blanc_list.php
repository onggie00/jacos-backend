
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('jadwal_ujian_delf_blanc') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('jadwal_ujian_delf_blanc') ?></li>
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
                        <?php is_allowed('jadwal_ujian_delf_blanc_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('jadwal_ujian_delf_blanc')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/jadwal_ujian_delf_blanc/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('jadwal_ujian_delf_blanc')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('jadwal_ujian_delf_blanc_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('jadwal_ujian_delf_blanc') ?>']); ?>" href="<?= site_url('administrator/jadwal_ujian_delf_blanc/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('jadwal_ujian_delf_blanc_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('jadwal_ujian_delf_blanc') ?>']); ?>" href="<?= site_url('administrator/jadwal_ujian_delf_blanc/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('jadwal_ujian_delf_blanc') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('jadwal_ujian_delf_blanc')]); ?>  <i class="label bg-yellow"><?= $jadwal_ujian_delf_blanc_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_jadwal_ujian_delf_blanc" id="form_jadwal_ujian_delf_blanc" action="<?= base_url('administrator/jadwal_ujian_delf_blanc/index'); ?>">
                  

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
                           <th> <?= cclang('tanggal') ?></th>
                           <th> <?= cclang('ruang') ?></th>
                           <th> <?= cclang('hari') ?></th>
                           <th> <?= cclang('jam_mulai') ?></th>
                           <th> <?= cclang('jam_selesai') ?></th>
                           <th> <?= cclang('id_tahun_ajaran') ?></th>
                           <th> <?= cclang('semester') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_jadwal_ujian_delf_blanc">
                     <?php foreach($jadwal_ujian_delf_blancs as $jadwal_ujian_delf_blanc): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $jadwal_ujian_delf_blanc->id_jadwal; ?>">
                           </td>
                                                       
                           <td><?php if  ($jadwal_ujian_delf_blanc->id_mapel) {

                              echo anchor('administrator/mata_pelajaran_ft/view/'.$jadwal_ujian_delf_blanc->id_mapel.'?popup=show', $jadwal_ujian_delf_blanc->mata_pelajaran_ft_nama_mapel, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($jadwal_ujian_delf_blanc->id_jenis_ujian) {

                              echo anchor('administrator/jenis_ujian/view/'.$jadwal_ujian_delf_blanc->id_jenis_ujian.'?popup=show', $jadwal_ujian_delf_blanc->jenis_ujian_nama_ujian, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($jadwal_ujian_delf_blanc->id_tingkatan) {

                              echo anchor('administrator/tingkatan_ft/view/'.$jadwal_ujian_delf_blanc->id_tingkatan.'?popup=show', $jadwal_ujian_delf_blanc->tingkatan_ft_label, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($jadwal_ujian_delf_blanc->tanggal); ?></td> 
                           <td><?= _ent($jadwal_ujian_delf_blanc->ruang); ?></td> 
                           <td><?= _ent($jadwal_ujian_delf_blanc->hari); ?></td> 
                           <td><?= _ent($jadwal_ujian_delf_blanc->jam_mulai); ?></td> 
                           <td><?= _ent($jadwal_ujian_delf_blanc->jam_selesai); ?></td> 
                           <td><?php if  ($jadwal_ujian_delf_blanc->id_tahun_ajaran) {

                              echo anchor('administrator/tahun_ajaran/view/'.$jadwal_ujian_delf_blanc->id_tahun_ajaran.'?popup=show', $jadwal_ujian_delf_blanc->tahun_ajaran_label, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($jadwal_ujian_delf_blanc->semester); ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('jadwal_ujian_delf_blanc_view', function() use ($jadwal_ujian_delf_blanc){?>
                                 <a href="<?= site_url('administrator/jadwal_ujian_delf_blanc/single_pdf/' .$jadwal_ujian_delf_blanc->id_jadwal); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/jadwal_ujian_delf_blanc/view/' . $jadwal_ujian_delf_blanc->id_jadwal); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('jadwal_ujian_delf_blanc_update', function() use ($jadwal_ujian_delf_blanc){?>
                              <a href="<?= site_url('administrator/jadwal_ujian_delf_blanc/edit/' . $jadwal_ujian_delf_blanc->id_jadwal); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('jadwal_ujian_delf_blanc_delete', function() use ($jadwal_ujian_delf_blanc){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/jadwal_ujian_delf_blanc/delete/' . $jadwal_ujian_delf_blanc->id_jadwal); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($jadwal_ujian_delf_blanc_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Jadwal Ujian Delf Blanc data is not available
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
                            <option <?= $this->input->get('f') == 'id_mapel' ? 'selected' :''; ?> value="id_mapel">Id Mapel</option>
                           <option <?= $this->input->get('f') == 'id_jenis_ujian' ? 'selected' :''; ?> value="id_jenis_ujian">Id Jenis Ujian</option>
                           <option <?= $this->input->get('f') == 'id_tingkatan' ? 'selected' :''; ?> value="id_tingkatan">Id Tingkatan</option>
                           <option <?= $this->input->get('f') == 'tanggal' ? 'selected' :''; ?> value="tanggal">Tanggal</option>
                           <option <?= $this->input->get('f') == 'ruang' ? 'selected' :''; ?> value="ruang">Ruang</option>
                           <option <?= $this->input->get('f') == 'hari' ? 'selected' :''; ?> value="hari">Hari</option>
                           <option <?= $this->input->get('f') == 'jam_mulai' ? 'selected' :''; ?> value="jam_mulai">Jam Mulai</option>
                           <option <?= $this->input->get('f') == 'jam_selesai' ? 'selected' :''; ?> value="jam_selesai">Jam Selesai</option>
                           <option <?= $this->input->get('f') == 'id_tahun_ajaran' ? 'selected' :''; ?> value="id_tahun_ajaran">Id Tahun Ajaran</option>
                           <option <?= $this->input->get('f') == 'semester' ? 'selected' :''; ?> value="semester">Semester</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/jadwal_ujian_delf_blanc');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_jadwal_ujian_delf_blanc').serialize();

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
               document.location.href = BASE_URL + '/administrator/jadwal_ujian_delf_blanc/delete?' + serialize_bulk;      
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