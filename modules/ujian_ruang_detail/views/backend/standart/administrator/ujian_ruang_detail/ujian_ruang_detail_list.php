
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('ujian_ruang_detail') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('ujian_ruang_detail') ?></li>
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
                        <a class="btn btn-flat btn-success" style="display:none;" title="Sync Data" href="<?= site_url('administrator/ujian_ruang_detail/sync_data'); ?><?= (!empty($_GET['q'])) ? "?q=".$_GET['q'] : ""; ?><?= (!empty($_GET['f'])) ? "&f=".$_GET['f'] : ""; ?>"><i class="fa fa-users" ></i> Sync Data</a>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('ujian_ruang_detail')." ".$_GET["q"] ?></h3>
                     <h5 class="widget-user-desc">Total Peserta Ujian <?= $_GET['q']; ?>  <i class="label bg-yellow"><?= $ujian_ruang_detail_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>
                  <div class="row" style="width:90%;padding-left:5px;">
                  <form action="<?= site_url('administrator/ujian_ruang_detail/sync_data'); ?>" method="get">
                     <input type="hidden" name="q" value="<?= $_GET['q']; ?>">
                     <input type="hidden" name="f" value="<?= $_GET['f']; ?>">
                     <div class="col-md-3">
                        <label for="tanggal_terakhir_bayar">Pilih Tanggal Terakhir Pembayaran</label>
                     </div>
                     <div class="col-md-2">
                        <input type="date" name="tanggal_terakhir_bayar" id="tanggal_terakhir_bayar" class="form-control" value="<?= $tanggal_terakhir_bayar; ?>" required>
                     </div>
                     <div class="col-md-7">
                        <button class="btn btn-success" type="submit"><i class="fa fa-users" ></i> Sync Data</button>
                        <a class="btn btn-primary" target="_blank" href="<?= site_url('administrator/ujian_ruang_detail/export')."?q=".$_GET['q']."&f=jenjang"; ?>"><i class="fa fa-file-excel-o" ></i> Export Peserta Ujian</a>
                        <a class="btn btn-flat btn-primary" title="Import Peserta Ujian" data-toggle="modal" data-target="#modal_import"><i class="fa fa-upload"></i> Import Peserta Ujian</a>
                        <a class="btn btn-flat btn-danger" title="Delete All Peserta Ujian" data-toggle="modal" data-target="#modal_delete_all"><i class="fa fa-trash"></i> Delete All Peserta</a>
                     </div>
                  </form>
                  </div>

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

                  <form name="form_ujian_ruang_detail" id="form_ujian_ruang_detail" action="<?= base_url('administrator/ujian_ruang_detail/index'); ?>">
                  

                  <div class="table-responsive" style="margin:10px 0"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                           <th style="text-align: center;"> <?= cclang('id_ruang') ?></th>
                           <th style="text-align: center;"> <?= cclang('jenjang') ?></th>
                           <th style="text-align: center;"> <?= cclang('id_ruang_kelas') ?></th>
                           <th style="text-align: center;"> <?= cclang('nomor_peserta_ujian') ?></th>
                           <th style="text-align: center;"> <?= cclang('boleh_ujian') ?></th>
                           <th style="text-align: center;"> <?= cclang('nama_siswa') ?></th>
                           <th style="text-align: center;">Urutan Bangku</th>
                           <th style="text-align: center;">Password</th>
                           <th style="text-align: center;">Tgl Unduh Kartu Ujian</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_ujian_ruang_detail">
                     <?php foreach($ujian_ruang_details as $ujian_ruang_detail): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $ujian_ruang_detail->id_detail; ?>">
                           </td>
                                                       
                           <td><?php if  ($ujian_ruang_detail->id_ruang) {

                              echo anchor('administrator/ujian_ruang/view/'.$ujian_ruang_detail->id_ruang.'?popup=show', $ujian_ruang_detail->ujian_ruang_nama_ruang, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($ujian_ruang_detail->jenjang); ?></td> 
                           <td class="text-center"><?php if  ($ujian_ruang_detail->id_ruang_kelas) {

                              echo anchor('administrator/ujian_ruang_kelas/view/'.$ujian_ruang_detail->id_ruang_kelas.'?popup=show', $ujian_ruang_detail->ujian_ruang_kelas_kelas, ['class' => 'popup-view']); }?> </td>
                             
                           <td class="text-center"><?= _ent($ujian_ruang_detail->nomor_peserta_ujian); ?></td> 
                           <td class="text-center"><?= _ent($ujian_ruang_detail->boleh_ujian); ?></td> 
                           <td><?= _ent($ujian_ruang_detail->nama_siswa); ?></td>
                           <td class="text-center"><?= _ent($ujian_ruang_detail->urutan_kursi); ?></td>
                           <td class="text-center"><?= (empty($ujian_ruang_detail->password)) ? "-" : $ujian_ruang_detail->password; ?></td>
                           <td class="text-center"><?= (!empty($ujian_ruang_detail->tgl_download_kartu)) ? date("d-m-Y H:i", strtotime($ujian_ruang_detail->tgl_download_kartu)) : ""; ?></td>
                           <td width="200">

                              <?php is_allowed('ujian_ruang_detail_update', function() use ($ujian_ruang_detail){?>
                              <a href="<?= site_url('administrator/ujian_ruang_detail/edit/' . $ujian_ruang_detail->id_detail); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('ujian_ruang_detail_delete', function() use ($ujian_ruang_detail){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/ujian_ruang_detail/delete/' . $ujian_ruang_detail->id_detail); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($ujian_ruang_detail_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Ujian Ruang Detail data is not available
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
                     <div class="col-sm-3 padd-left-0 " style="display: block;" >
                        <input type="text" class="form-control" name="q" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('q'); ?>">
                     </div>
                     <div class="col-sm-3 padd-left-0 " style="display: block;" >
                        <select type="text" class="form-control chosen chosen-select" name="f" id="field" >
                           <option value=""><?= cclang('all'); ?></option>
                           <option <?= $this->input->get('f') == 'jenjang' ? 'selected' :''; ?> value="jenjang">Jenjang</option>
                           <option <?= $this->input->get('f') == 'nomor_peserta_ujian' ? 'selected' :''; ?> value="nomor_peserta_ujian">Nomor Peserta Ujian</option>
                           <option <?= $this->input->get('f') == 'boleh_ujian' ? 'selected' :''; ?> value="boleh_ujian">Boleh Ujian?</option>
                           <option <?= $this->input->get('f') == 'nama_siswa' ? 'selected' :''; ?> value="nama_siswa">Nama Siswa</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 " style="display: block;">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 " style="display: block;">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/ujian_ruang_detail');?>" title="<?= cclang('reset_filter'); ?>">
                        <i class="fa fa-undo"></i>
                        </a>
                     </div>
                  </div>
                  </form>                  <div class="col-md-4">
                     <div class="dataTables_paginate paging_simple_numbers pull-right" id="example2_paginate" >
                        <?= (!empty($pagination)) ? $pagination : ""; ?>
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
            <h3 class="modal-title" id="myModalLabel">Import Peserta Ujian Terbaru </h3>
         </div>
         <form action="<?= base_url('administrator/ujian_ruang_detail/import_peserta_ujian/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Jenjang</label>
                  <div class="col-xs-8">
                     <select class="form-control" name="jenjang" required>
                        <option value="SD" <?= ($_GET['q'] == "SD" ) ? "selected" : ""; ?>>SD</option>
                        <option value="SMP" <?= ($_GET['q'] == "SMP" ) ? "selected" : ""; ?>>SMP</option>
                        <option value="SMA" <?= ($_GET['q'] == "SMA" ) ? "selected" : ""; ?>>SMA</option>
                        <option value="FT" <?= ($_GET['q'] == "FT" ) ? "selected" : ""; ?>>FT</option>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-xs-3">File</label>
                  <div class="col-xs-8">
                     <input name="file_import" class="form-control" type="file"  accept=".xls,.xlsx" required>
                  </div>
               </div>
               <span>*Pastikan seluruh siswa telah mempunyai NIS.</span><br/>
               <span>*Pastikan data ruangan ujian telah tersedia sebelum proses import, bila belum ada silahkan menambahkan dahulu di menu ruang ujian</span><br/>
               <span>*Pastikan data excel yang diimport sudah benar dan tidak kosong, karena akan menimpa data peserta ujian sebelumnya</span><br/>
               <span>*File yang akan diimport akan sesuai format file hasil export Excel</span><br/>
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

<!-- ============ MODAL DELETE ALL DATA SISWA  =============== -->
<div class="modal fade" id="modal_delete_all" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Konfirmasi Hapus Semua Data Peserta Ujian </h3>
         </div>
         <form action="<?= base_url('administrator/ujian_ruang_detail/delete_all/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Jenjang</label>
                  <div class="col-xs-8">
                     <select class="form-control" name="jenjang" required>
                        <option value="SD" <?= ($_GET['q'] == "SD" ) ? "selected" : ""; ?>>SD</option>
                        <option value="SMP" <?= ($_GET['q'] == "SMP" ) ? "selected" : ""; ?>>SMP</option>
                        <option value="SMA" <?= ($_GET['q'] == "SMA" ) ? "selected" : ""; ?>>SMA</option>
                        <option value="FT" <?= ($_GET['q'] == "FT" ) ? "selected" : ""; ?>>FT</option>
                     </select>
                  </div>
               </div>
               <span>*Pastikan sudah memilih jenjang sesuai dengan jenjang.</span><br/>
            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Tutup</button>
               <button type="submit" class="btn btn-danger btn_save">Delete</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL DELETE ALL DATA SISWA -->

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
      var serialize_bulk = $('#form_ujian_ruang_detail').serialize();

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
               document.location.href = BASE_URL + '/administrator/ujian_ruang_detail/delete?' + serialize_bulk;      
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