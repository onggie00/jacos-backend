
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('ppsbb_siswa_smp') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('ppsbb_siswa_smp') ?></li>
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
                        <?php is_allowed('ppsbb_siswa_smp_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('ppsbb_siswa_smp')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/ppsbb_siswa_smp/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('ppsbb_siswa_smp')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('ppsbb_siswa_smp_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('ppsbb_siswa_smp') ?>']); ?>" href="<?= site_url('administrator/ppsbb_siswa_smp/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('ppsbb_siswa_smp_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('ppsbb_siswa_smp') ?>']); ?>" href="<?= site_url('administrator/ppsbb_siswa_smp/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('ppsbb_siswa_smp') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('ppsbb_siswa_smp')]); ?>  <i class="label bg-yellow"><?= $ppsbb_siswa_smp_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_ppsbb_siswa_smp" id="form_ppsbb_siswa_smp" action="<?= base_url('administrator/ppsbb_siswa_smp/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('id_siswa_smp') ?></th>
                           <th> <?= cclang('jenis_prestasi') ?></th>
                           <th> <?= cclang('nama_prestasi') ?></th>
                           <th> <?= cclang('keterangan_prestasi') ?></th>
                           <th> <?= cclang('keterangan_prestasi_lainnya') ?></th>
                           <th> <?= cclang('tahun_prestasi') ?></th>
                           <th> <?= cclang('sertifikat') ?></th>
                           <th> <?= cclang('jenis_jenjang') ?></th>
                           <th> <?= cclang('jenis_lomba') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_ppsbb_siswa_smp">
                     <?php foreach($ppsbb_siswa_smps as $ppsbb_siswa_smp): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $ppsbb_siswa_smp->id_ppsbb_siswa_smp; ?>">
                           </td>
                                                       
                           <td><?= _ent($ppsbb_siswa_smp->id_siswa_smp); ?></td> 
                           <td><?php if  ($ppsbb_siswa_smp->jenis_prestasi) {

                              echo anchor('administrator/jenis_prestasi/view/'.$ppsbb_siswa_smp->jenis_prestasi.'?popup=show', $ppsbb_siswa_smp->jenis_prestasi_jenis_prestasi, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($ppsbb_siswa_smp->nama_prestasi); ?></td> 
                           <td><?php if  ($ppsbb_siswa_smp->keterangan_prestasi) {

                              echo anchor('administrator/keterangan_prestasi/view/'.$ppsbb_siswa_smp->keterangan_prestasi.'?popup=show', $ppsbb_siswa_smp->keterangan_prestasi_keterangan_prestasi, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($ppsbb_siswa_smp->keterangan_prestasi_lainnya); ?></td> 
                           <td><?= _ent($ppsbb_siswa_smp->tahun_prestasi); ?></td> 
                           <td>
                              <?php if (!empty($ppsbb_siswa_smp->sertifikat)): ?>
                                <?php if (is_image($ppsbb_siswa_smp->sertifikat)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/ppsbb_siswa_smp/' . $ppsbb_siswa_smp->sertifikat; ?>">
                                  <img src="<?= BASE_URL . 'uploads/ppsbb_siswa_smp/' . $ppsbb_siswa_smp->sertifikat; ?>" class="image-responsive" alt="image ppsbb_siswa_smp" title="sertifikat ppsbb_siswa_smp" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/ppsbb_siswa_smp/' . $ppsbb_siswa_smp->sertifikat; ?>">
                                   <img src="<?= get_icon_file($ppsbb_siswa_smp->sertifikat); ?>" class="image-responsive image-icon" alt="image ppsbb_siswa_smp" title="sertifikat <?= $ppsbb_siswa_smp->sertifikat; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td><?php if  ($ppsbb_siswa_smp->jenis_jenjang) {

                              echo anchor('administrator/jenis_jenjang/view/'.$ppsbb_siswa_smp->jenis_jenjang.'?popup=show', $ppsbb_siswa_smp->jenis_jenjang_jenis_jenjang, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($ppsbb_siswa_smp->jenis_lomba) {

                              echo anchor('administrator/jenis_lomba/view/'.$ppsbb_siswa_smp->jenis_lomba.'?popup=show', $ppsbb_siswa_smp->jenis_lomba_jenis_lomba, ['class' => 'popup-view']); }?> </td>
                             
                           <td width="200">
                            
                                                              <?php is_allowed('ppsbb_siswa_smp_view', function() use ($ppsbb_siswa_smp){?>
                                 <a href="<?= site_url('administrator/ppsbb_siswa_smp/single_pdf/' .$ppsbb_siswa_smp->id_ppsbb_siswa_smp); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/ppsbb_siswa_smp/view/' . $ppsbb_siswa_smp->id_ppsbb_siswa_smp); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('ppsbb_siswa_smp_update', function() use ($ppsbb_siswa_smp){?>
                              <a href="<?= site_url('administrator/ppsbb_siswa_smp/edit/' . $ppsbb_siswa_smp->id_ppsbb_siswa_smp); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('ppsbb_siswa_smp_delete', function() use ($ppsbb_siswa_smp){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/ppsbb_siswa_smp/delete/' . $ppsbb_siswa_smp->id_ppsbb_siswa_smp); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($ppsbb_siswa_smp_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Ppsbb Siswa SMP data is not available
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
                            <option <?= $this->input->get('f') == 'id_siswa_smp' ? 'selected' :''; ?> value="id_siswa_smp">Id Siswa Smp</option>
                           <option <?= $this->input->get('f') == 'jenis_prestasi' ? 'selected' :''; ?> value="jenis_prestasi">Jenis Prestasi</option>
                           <option <?= $this->input->get('f') == 'nama_prestasi' ? 'selected' :''; ?> value="nama_prestasi">Nama Prestasi</option>
                           <option <?= $this->input->get('f') == 'keterangan_prestasi' ? 'selected' :''; ?> value="keterangan_prestasi">Keterangan Prestasi</option>
                           <option <?= $this->input->get('f') == 'keterangan_prestasi_lainnya' ? 'selected' :''; ?> value="keterangan_prestasi_lainnya">Keterangan Prestasi (Lainnya)</option>
                           <option <?= $this->input->get('f') == 'tahun_prestasi' ? 'selected' :''; ?> value="tahun_prestasi">Tahun Prestasi</option>
                           <option <?= $this->input->get('f') == 'sertifikat' ? 'selected' :''; ?> value="sertifikat">Sertifikat</option>
                           <option <?= $this->input->get('f') == 'jenis_jenjang' ? 'selected' :''; ?> value="jenis_jenjang">Jenis Jenjang</option>
                           <option <?= $this->input->get('f') == 'jenis_lomba' ? 'selected' :''; ?> value="jenis_lomba">Jenis Lomba</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/ppsbb_siswa_smp');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_ppsbb_siswa_smp').serialize();

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
               document.location.href = BASE_URL + '/administrator/ppsbb_siswa_smp/delete?' + serialize_bulk;      
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