
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('ekskul_presensi_pelatih_ft') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('ekskul_presensi_pelatih_ft') ?></li>
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
                        <?php is_allowed('ekskul_presensi_pelatih_ft_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('ekskul_presensi_pelatih_ft')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/ekskul_presensi_pelatih_ft/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('ekskul_presensi_pelatih_ft')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('ekskul_presensi_pelatih_ft_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('ekskul_presensi_pelatih_ft') ?>']); ?>" href="<?= site_url('administrator/ekskul_presensi_pelatih_ft/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('ekskul_presensi_pelatih_ft_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('ekskul_presensi_pelatih_ft') ?>']); ?>" href="<?= site_url('administrator/ekskul_presensi_pelatih_ft/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('ekskul_presensi_pelatih_ft') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('ekskul_presensi_pelatih_ft')]); ?>  <i class="label bg-yellow"><?= $ekskul_presensi_pelatih_ft_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_ekskul_presensi_pelatih_ft" id="form_ekskul_presensi_pelatih_ft" action="<?= base_url('administrator/ekskul_presensi_pelatih_ft/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th> <?= cclang('id') ?></th>
                           <th> <?= cclang('id_ekskul') ?></th>
                           <th> <?= cclang('id_pelatih') ?></th>
                           <th> <?= cclang('hari_absen') ?></th>
                           <th> <?= cclang('tanggal_absen') ?></th>
                           <th> <?= cclang('waktu_absen') ?></th>
                           <th> <?= cclang('status_absen') ?></th>
                           <th> <?= cclang('keterangan_presensi') ?></th>
                                                   </tr>
                     </thead>
                     <tbody id="tbody_ekskul_presensi_pelatih_ft">
                     <?php foreach($ekskul_presensi_pelatih_fts as $ekskul_presensi_pelatih_ft): ?>
                        <tr>
                                                       
                           <td><?= _ent($ekskul_presensi_pelatih_ft->id); ?></td> 
                           <td><?php if  ($ekskul_presensi_pelatih_ft->id_ekskul) {

                              echo anchor('administrator/ekskul/view/'.$ekskul_presensi_pelatih_ft->id_ekskul.'?popup=show', $ekskul_presensi_pelatih_ft->ekskul_nama, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($ekskul_presensi_pelatih_ft->id_pelatih) {

                              echo anchor('administrator/ekskul_manajemen_ft/view/'.$ekskul_presensi_pelatih_ft->id_pelatih.'?popup=show', $ekskul_presensi_pelatih_ft->ekskul_manajemen_ft_nama, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($ekskul_presensi_pelatih_ft->hari_absen); ?></td> 
                           <td><?= _ent($ekskul_presensi_pelatih_ft->tanggal_absen); ?></td> 
                           <td><?= _ent($ekskul_presensi_pelatih_ft->waktu_absen); ?></td> 
                           <td><?= _ent($ekskul_presensi_pelatih_ft->status_absen); ?></td> 
                           <td><?= _ent($ekskul_presensi_pelatih_ft->keterangan_presensi); ?></td> 
                                                   </tr>
                      <?php endforeach; ?>
                      <?php if ($ekskul_presensi_pelatih_ft_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Presensi Pelatih FT data is not available
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
                            <option <?= $this->input->get('f') == 'id' ? 'selected' :''; ?> value="id">Id</option>
                           <option <?= $this->input->get('f') == 'id_ekskul' ? 'selected' :''; ?> value="id_ekskul">Ekstrakurikuler</option>
                           <option <?= $this->input->get('f') == 'id_pelatih' ? 'selected' :''; ?> value="id_pelatih">Pelatih</option>
                           <option <?= $this->input->get('f') == 'hari_absen' ? 'selected' :''; ?> value="hari_absen">Hari Presensi</option>
                           <option <?= $this->input->get('f') == 'tanggal_absen' ? 'selected' :''; ?> value="tanggal_absen">Tanggal Presensi</option>
                           <option <?= $this->input->get('f') == 'waktu_absen' ? 'selected' :''; ?> value="waktu_absen">Waktu Presensi</option>
                           <option <?= $this->input->get('f') == 'status_absen' ? 'selected' :''; ?> value="status_absen">Status</option>
                           <option <?= $this->input->get('f') == 'keterangan_presensi' ? 'selected' :''; ?> value="keterangan_presensi">Keterangan</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/ekskul_presensi_pelatih_ft');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_ekskul_presensi_pelatih_ft').serialize();

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
               document.location.href = BASE_URL + '/administrator/ekskul_presensi_pelatih_ft/delete?' + serialize_bulk;      
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