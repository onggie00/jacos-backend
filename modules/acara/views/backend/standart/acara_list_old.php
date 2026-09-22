
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('acara') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('acara') ?></li>
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
                        <?php is_allowed('acara_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('acara')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/acara/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('acara')]); ?></a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('acara') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('acara')]); ?>  <i class="label bg-yellow"><?= $acara_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_acara" id="form_acara" action="<?= base_url('administrator/acara/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                              <th>
                                 <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                              </th>
                              <th style="text-align: center"> <?= cclang('nama_acara') ?></th>
                              <th style="text-align: center"> <?= cclang('qr_code') ?></th>
                              <th style="text-align: center"> <?= cclang('waktu_mulai') .' & '. cclang('waktu_selesai') ?></th>
                              <th style="text-align: center"> Peserta</th>
                              <th style="text-align: center"> <?= cclang('lokasi') ?></th>
                              <th style="text-align: center"> <?= cclang('is_certificated') ?></th>
                              <th style="text-align: center"> Detail</th>
                              <th style="text-align: center">Action</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_acara">
                     <?php foreach($acaras as $acara): ?>
                        <tr>
                           <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $acara->id_acara; ?>">
                           </td>
                                                       
                           <td><?= _ent($acara->nama_acara); ?></td>
                           <td style="text-align: center">
                              <?php if (!empty($acara->qr_code)): ?>
                                 <?php if (is_image($acara->qr_code)): ?>
                                 <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code; ?>">
                                    <img src="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code; ?>" class="image-responsive" alt="image acara" title="qr_code acara" width="40px">
                                 </a>
                                 <br/>
                                 <button class="btn btn-success btn-xs" type="button" title="View qr_code acara"><?= $acara->unique_code; ?></button>
                              <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code; ?>">
                                   <img src="<?= get_icon_file($acara->qr_code); ?>" class="image-responsive image-icon" alt="image acara" title="qr_code <?= $acara->qr_code; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td><?= formatHari($acara->waktu_mulai).' - '.formatHari($acara->waktu_selesai).' <br/>('. formatTanggal($acara->waktu_mulai) . ' - ' . formatTanggal($acara->waktu_selesai).')'; ?></td> 
                           <td style="text-align: center"><a href="<?= site_url('administrator/acara_presensi?q=' . $acara->id_acara. '&f=id_acara'); ?>" class="btn btn-primary btn-xl">Detail Peserta</a></td> 
                           <td style="text-align: center"><?= _ent($acara->lokasi); ?></td>
                           <td style="text-align: center"><?= (!empty($acara->is_certificated)) ? 'Ya' : 'Tidak'; ?></td>
                           <td style="text-align: center">
                              <a href="<?= site_url('administrator/acara/view/' . $acara->id_acara); ?>" class="btn btn-info btn-xl"><i class="fa fa-newspaper-o"></i> Detail Acara</a> 
                              <a href="<?= site_url('administrator/acara_presensi/export?q=' . $acara->id_acara.'&f=id_acara'); ?>" class="btn btn-success btn-xl"><i class="fa fa-file-excel-o"></i> XLS Peserta</a>
                           </td>
                           
                           <td width="200">
                              <?php is_allowed('acara_update', function() use ($acara){?>
                              <a href="<?= site_url('administrator/acara/edit/' . $acara->id_acara); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('acara_delete', function() use ($acara){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/acara/delete/' . $acara->id_acara); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($acara_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Acara data is not available
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
                            <option <?= $this->input->get('f') == 'nama_acara' ? 'selected' :''; ?> value="nama_acara">Nama</option>
                           <option <?= $this->input->get('f') == 'unique_code' ? 'selected' :''; ?> value="unique_code">Code</option>
                           <option <?= $this->input->get('f') == 'keterangan' ? 'selected' :''; ?> value="keterangan">Keterangan</option>
                           <option <?= $this->input->get('f') == 'qr_code' ? 'selected' :''; ?> value="qr_code">QR Code</option>
                           <option <?= $this->input->get('f') == 'waktu_mulai' ? 'selected' :''; ?> value="waktu_mulai">Waktu Mulai</option>
                           <option <?= $this->input->get('f') == 'waktu_selesai' ? 'selected' :''; ?> value="waktu_selesai">Waktu Selesai</option>
                           <option <?= $this->input->get('f') == 'lokasi' ? 'selected' :''; ?> value="lokasi">Lokasi</option>
                           <option <?= $this->input->get('f') == 'is_certificated' ? 'selected' :''; ?> value="is_certificated">Certificated?</option>
                           <option <?= $this->input->get('f') == 'no_certificate' ? 'selected' :''; ?> value="no_certificate">Nomor Sertifikat</option>
                           <option <?= $this->input->get('f') == 'file_certificate' ? 'selected' :''; ?> value="file_certificate">Certificate Template</option>
                           <option <?= $this->input->get('f') == 'file_certificate_back' ? 'selected' :''; ?> value="file_certificate_back">Certificate Template Back</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/acara');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_acara').serialize();

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
               document.location.href = BASE_URL + '/administrator/acara/delete?' + serialize_bulk;      
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