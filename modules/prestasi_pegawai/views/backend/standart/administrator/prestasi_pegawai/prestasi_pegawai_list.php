
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('prestasi_pegawai') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('prestasi_pegawai') ?></li>
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
                        <?php is_allowed('prestasi_pegawai_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('prestasi_pegawai')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/prestasi_pegawai/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('prestasi_pegawai')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('prestasi_pegawai_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('prestasi_pegawai') ?>']); ?>" href="<?= site_url('administrator/prestasi_pegawai/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('prestasi_pegawai_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('prestasi_pegawai') ?>']); ?>" href="<?= site_url('administrator/prestasi_pegawai/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('prestasi_pegawai') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('prestasi_pegawai')]); ?>  <i class="label bg-yellow"><?= $prestasi_pegawai_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_prestasi_pegawai" id="form_prestasi_pegawai" action="<?= base_url('administrator/prestasi_pegawai/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('nama_prestasi') ?></th>
                           <th> <?= cclang('id_pegawai') ?></th>
                           <th> <?= cclang('keterangan') ?></th>
                           <th> <?= cclang('file_prestasi') ?></th>
                           <th> <?= cclang('foto_prestasi') ?></th>
                           <th> <?= cclang('tgl_raih') ?></th>
                           <th> <?= cclang('is_approve') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_prestasi_pegawai">
                     <?php foreach($prestasi_pegawais as $prestasi_pegawai): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $prestasi_pegawai->id_prestasi; ?>">
                           </td>
                                                       
                           <td><?= _ent($prestasi_pegawai->nama_prestasi); ?></td> 
                           <td><?php if  ($prestasi_pegawai->id_pegawai) {

                              echo anchor('administrator/pegawai/view/'.$prestasi_pegawai->id_pegawai.'?popup=show', $prestasi_pegawai->pegawai_nama_lengkap, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($prestasi_pegawai->keterangan); ?></td> 
                           <td>
                              <?php if (!empty($prestasi_pegawai->file_prestasi)): ?>
                                <?php if (is_image($prestasi_pegawai->file_prestasi)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_pegawai/' . $prestasi_pegawai->file_prestasi; ?>">
                                  <img src="<?= BASE_URL . 'uploads/prestasi_pegawai/' . $prestasi_pegawai->file_prestasi; ?>" class="image-responsive" alt="image prestasi_pegawai" title="file_prestasi prestasi_pegawai" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/prestasi_pegawai/' . $prestasi_pegawai->file_prestasi; ?>">
                                   <img src="<?= get_icon_file($prestasi_pegawai->file_prestasi); ?>" class="image-responsive image-icon" alt="image prestasi_pegawai" title="file_prestasi <?= $prestasi_pegawai->file_prestasi; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td>
                              <?php if (!empty($prestasi_pegawai->foto_prestasi)): ?>
                                <?php if (is_image($prestasi_pegawai->foto_prestasi)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/prestasi_pegawai/' . $prestasi_pegawai->foto_prestasi; ?>">
                                  <img src="<?= BASE_URL . 'uploads/prestasi_pegawai/' . $prestasi_pegawai->foto_prestasi; ?>" class="image-responsive" alt="image prestasi_pegawai" title="foto_prestasi prestasi_pegawai" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/prestasi_pegawai/' . $prestasi_pegawai->foto_prestasi; ?>">
                                   <img src="<?= get_icon_file($prestasi_pegawai->foto_prestasi); ?>" class="image-responsive image-icon" alt="image prestasi_pegawai" title="foto_prestasi <?= $prestasi_pegawai->foto_prestasi; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td><?= _ent($prestasi_pegawai->tgl_raih); ?></td> 
                           <td><?= _ent($prestasi_pegawai->is_approve); ?></td> 
                           <td width="200">
                            
                                                              <?php is_allowed('prestasi_pegawai_view', function() use ($prestasi_pegawai){?>
                                 <a href="<?= site_url('administrator/prestasi_pegawai/single_pdf/' .$prestasi_pegawai->id_prestasi); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/prestasi_pegawai/view/' . $prestasi_pegawai->id_prestasi); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('prestasi_pegawai_update', function() use ($prestasi_pegawai){?>
                              <a href="<?= site_url('administrator/prestasi_pegawai/edit/' . $prestasi_pegawai->id_prestasi); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('prestasi_pegawai_delete', function() use ($prestasi_pegawai){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/prestasi_pegawai/delete/' . $prestasi_pegawai->id_prestasi); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($prestasi_pegawai_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Prestasi Pegawai data is not available
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
                            <option <?= $this->input->get('f') == 'nama_prestasi' ? 'selected' :''; ?> value="nama_prestasi">Nama Prestasi</option>
                           <option <?= $this->input->get('f') == 'id_pegawai' ? 'selected' :''; ?> value="id_pegawai">Id Pegawai</option>
                           <option <?= $this->input->get('f') == 'keterangan' ? 'selected' :''; ?> value="keterangan">Keterangan</option>
                           <option <?= $this->input->get('f') == 'file_prestasi' ? 'selected' :''; ?> value="file_prestasi">File Prestasi</option>
                           <option <?= $this->input->get('f') == 'foto_prestasi' ? 'selected' :''; ?> value="foto_prestasi">Foto Prestasi</option>
                           <option <?= $this->input->get('f') == 'tgl_raih' ? 'selected' :''; ?> value="tgl_raih">Tgl Raih</option>
                           <option <?= $this->input->get('f') == 'is_approve' ? 'selected' :''; ?> value="is_approve">Is Approve</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/prestasi_pegawai');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_prestasi_pegawai').serialize();

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
               document.location.href = BASE_URL + '/administrator/prestasi_pegawai/delete?' + serialize_bulk;      
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