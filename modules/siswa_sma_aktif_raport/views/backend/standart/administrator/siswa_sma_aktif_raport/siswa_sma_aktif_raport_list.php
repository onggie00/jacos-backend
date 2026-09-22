
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('siswa_sma_aktif_raport') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('siswa_sma_aktif_raport') ?></li>
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
                        <?php is_allowed('siswa_sma_aktif_raport_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new hidden" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('siswa_sma_aktif_raport')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/siswa_sma_aktif_raport/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('siswa_sma_aktif_raport')]); ?></a>
                        <a class="btn btn-flat btn-primary" title="Import Raport Ujian" data-toggle="modal" data-target="#modal_import"><i class="fa fa-upload"></i> Import Raport Ujian</a>
                        <?php }) ?>
                        <?php is_allowed('siswa_sma_aktif_raport_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('siswa_sma_aktif_raport') ?>" href="<?= site_url('administrator/siswa_sma_aktif_raport/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('siswa_sma_aktif_raport') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('siswa_sma_aktif_raport')]); ?>  <i class="label bg-yellow"><?= $siswa_sma_aktif_raport_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_siswa_sma_aktif_raport" id="form_siswa_sma_aktif_raport" action="<?= base_url('administrator/siswa_sma_aktif_raport/index'); ?>">
                  
                  <?php if($this->session->flashdata('success')){?>
                  <div class="alert alert-success alert-dismissable">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                     <h4>  <i class="icon fa fa-check"></i> Success!</h4>
                     <?= $this->session->flashdata('success'); ?>
                  </div>
                  <?php }elseif($this->session->flashdata('error')){?>
                  <div class="alert alert-danger alert-dismissable">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                     <h4><i class="icon fa fa-ban"></i> Error!</h4>
                     <?= $this->session->flashdata('error'); ?>
                  </div>
                  <?php }?>

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                           <th> <?= cclang('nis') ?></th>
                           <th> <?= cclang('nama_lengkap') ?></th>
                           <th> <?= cclang('id_siswa_aktif') ?></th>
                           <th> <?= cclang('tahun_ajaran') ?></th>
                           <th> <?= cclang('jenis_ujian') ?></th>
                           <th> <?= cclang('tanggal_sync_valid') ?></th>
                           <th> <?= cclang('file_raport') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_siswa_sma_aktif_raport">
                     <?php foreach($siswa_sma_aktif_raports as $siswa_sma_aktif_raport): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $siswa_sma_aktif_raport->id; ?>">
                           </td>
                                                       
                           <td><?= _ent($siswa_sma_aktif_raport->nis); ?></td> 
                           <td><?= _ent($siswa_sma_aktif_raport->nama_lengkap); ?></td> 
                           <td><?php if  ($siswa_sma_aktif_raport->id_siswa_aktif) {

                              echo anchor('administrator/siswa_sma_aktif/view/'.$siswa_sma_aktif_raport->id_siswa_aktif.'?popup=show', $siswa_sma_aktif_raport->siswa_sma_aktif_nama_lengkap, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($siswa_sma_aktif_raport->tahun_ajaran); ?></td> 
                           <td><?= _ent($siswa_sma_aktif_raport->jenis_ujian); ?></td> 
                           <td><?= _ent($siswa_sma_aktif_raport->tanggal_sync_valid); ?></td> 
                           <td>
                              <?php if (!empty($siswa_sma_aktif_raport->file_raport)): ?>
                                <?php if (is_image($siswa_sma_aktif_raport->file_raport)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/siswa_sma_aktif_raport/' . $siswa_sma_aktif_raport->file_raport; ?>">
                                  <img src="<?= BASE_URL . 'uploads/siswa_sma_aktif_raport/' . $siswa_sma_aktif_raport->file_raport; ?>" class="image-responsive" alt="image siswa_sma_aktif_raport" title="file_raport siswa_sma_aktif_raport" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/siswa_sma_aktif_raport/' . $siswa_sma_aktif_raport->file_raport; ?>">
                                   <img src="<?= get_icon_file($siswa_sma_aktif_raport->file_raport); ?>" class="image-responsive image-icon" alt="image siswa_sma_aktif_raport" title="file_raport <?= $siswa_sma_aktif_raport->file_raport; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td width="200">
                           
                           <?php is_allowed('siswa_sma_aktif_raport_view', function() use ($siswa_sma_aktif_raport){?>
                              <a href="<?= site_url('administrator/siswa_sma_aktif_raport/single_pdf/' .$siswa_sma_aktif_raport->id); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/siswa_sma_aktif_raport/view/' . $siswa_sma_aktif_raport->id); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('siswa_sma_aktif_raport_update', function() use ($siswa_sma_aktif_raport){?>
                              <a href="<?= site_url('administrator/siswa_sma_aktif_raport/edit/' . $siswa_sma_aktif_raport->id); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('siswa_sma_aktif_raport_delete', function() use ($siswa_sma_aktif_raport){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/siswa_sma_aktif_raport/delete/' . $siswa_sma_aktif_raport->id); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                           <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($siswa_sma_aktif_raport_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Rapor SMA data is not available
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
                           <option value="nama_lengkap"><?= cclang('nama_lengkap'); ?></option>
                           <option value="nis"><?= cclang('nis'); ?></option>
                           <!-- <option value="kelas"><?php //echo cclang('kelas'); ?></option> -->
                           <option value="tahun_ajaran"><?= cclang('tahun_ajaran'); ?></option>
                        </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/siswa_sma_aktif_raport');?>" title="<?= cclang('reset_filter'); ?>">
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
            <h3 class="modal-title" id="myModalLabel">Import Raport SMA</h3>
         </div>
         <form action="<?= base_url('administrator/siswa_sma_aktif_raport/import_raport/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <!-- <div class="form-group">
                  <label class="control-label col-xs-3">Jenjang</label>
                  <div class="col-xs-8">
                     <select class="form-control" name="jenjang" required>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA">SMA</option>
                        <option value="FT">FT</option>
                     </select>
                  </div>
               </div> -->
               <div class="form-group">
                  <label class="control-label col-xs-3">Tahun Ajaran</label>
                  <div class="col-xs-8">
                     <?php
                        $tahun_ajaran = $this->db->order_by('label', 'desc')->get('tahun_ajaran');
                     ?>
                     <select class="form-control" name="tahun_ajaran" required>
                        <?php
                           foreach ($tahun_ajaran->result() as $row) {
                              $selected = ($selected_tahun_ajaran == $row->label) ? "selected" : null;
                              echo "<option value='$row->label' $selected>$row->label</option>";
                           }
                        ?>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Tanggal SPP Terakhir</label>
                  <div class="col-xs-8">
                     <input name="tanggal_sync_valid" class="form-control" type="date" required />
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Nama Ujian</label>
                  <div class="col-xs-8">
                     <input name="jenis_ujian" class="form-control" type="text" required />
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">Upload File (Multiple)</label>
                  <div class="col-xs-8">
                     <input name="file_raport[]" class="form-control" type="file"  accept=".pdf,.doc,.docx,.docs" multiple required>
                  </div>
               </div>
               <span>*Pastikan seluruh file telah mempunyai nama berdasarkan NIS siswa.</span><br/>
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
      var serialize_bulk = $('#form_siswa_sma_aktif_raport').serialize();

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
               document.location.href = BASE_URL + '/administrator/siswa_sma_aktif_raport/delete?' + serialize_bulk;      
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