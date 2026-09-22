<script type="text/javascript">
   <?php if ($this->session->flashdata('success')) { ?>
      toastr.success("<?php echo $this->session->flashdata('success'); ?>");
   <?php } else if ($this->session->flashdata('error')) {  ?>
      toastr.error("<?php echo $this->session->flashdata('error'); ?>");
   <?php } else if ($this->session->flashdata('warning')) {  ?>
      toastr.warning("<?php echo $this->session->flashdata('warning'); ?>");
   <?php } else if ($this->session->flashdata('info')) {  ?>
      toastr.info("<?php echo $this->session->flashdata('info'); ?>");
   <?php } ?>
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('status_daftar_ulang_smp') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('status_daftar_ulang_smp') ?></li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row">

      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-body ">
               <!-- Widget: user widget style 1 -->
               <div class="box box-widget widget-user-2">
                  <!-- Add the bg color to the header using any of the bg-* classes -->
                  <div class="widget-user-header ">
                     <div class="row pull-right">
                        <?php is_allowed('status_daftar_ulang_smp_export', function () { ?>
                           <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('status_daftar_ulang_smp') ?>']); ?>" href="<?= site_url('administrator/status_daftar_ulang_smp/export'); ?>"><i class="fa fa-file-excel-o"></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('status_daftar_ulang_smp_export', function () { ?>
                           <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('status_daftar_ulang_smp') ?>']); ?>" href="<?= site_url('administrator/status_daftar_ulang_smp/export_pdf'); ?>"><i class="fa fa-file-pdf-o"></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('status_daftar_ulang_smp') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('status_daftar_ulang_smp')]); ?> <i class="label bg-yellow"><?= $status_daftar_ulang_smp_counts; ?> <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_status_daftar_ulang_smp" id="form_status_daftar_ulang_smp" action="<?= base_url('administrator/status_daftar_ulang_smp/index'); ?>">


                     <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                           <thead>
                              <tr class="">
                                 <th>
                                    <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                                 </th>
                                 <th> <?= cclang('nama') ?></th>
                                 <th> Email </th>
                                 <th> <?= cclang('status') ?></th>
                                 <th> Slip Pembayaran </th>
                                 <th> <?= cclang('kwitansi') ?></th>
                                 <th> <?= cclang('kartu_sementara') ?></th>
                                 <th> <?= cclang('tanggal_lulus') ?></th>
                                 <th> Tanggal Daftar Ulang</th>
                                 <th>Action</th>
                              </tr>
                           </thead>
                           <tbody id="tbody_status_daftar_ulang_smp">
                              <?php foreach ($status_daftar_ulang_smps as $status_daftar_ulang_smp) : ?>
                                 <tr>
                                    <td width="5">
                                       <input type="checkbox" class="flat-red check" name="id[]" value="<?= $status_daftar_ulang_smp->id_daftar_ulang; ?>">
                                    </td>

                                    <td><?= _ent($status_daftar_ulang_smp->nama_lengkap); ?></td>
                                    <td><?= _ent($status_daftar_ulang_smp->email); ?></td>
                                    <?php
                                       if ($status_daftar_ulang_smp->status == 0) {
                                          $status = "Menunggu Aktivasi";
                                       } else if ($status_daftar_ulang_smp->status == 1) {
                                          $status = "Virtual Account Aktif (Menunggu Pembayaran)";
                                       } else if ($status_daftar_ulang_smp->status == 2) {
                                          $status = "Pembayaran Berhasil";
                                       }
                                       ?>
                                    <td><?= _ent($status); ?></td>
                                    <td>
                                       <?php if (!empty($status_daftar_ulang_smp->slip_pembayaran)) : ?>
                                          <?php $no_transaksi = $this->mymodel->withquery("select no_transaksi from transaksi where user_email like '%".$status_daftar_ulang_smp->email."%' and user_name like '%".$this->db->escape_like_str($status_daftar_ulang_smp->nama_lengkap)."%' order by id_transaksi desc","row")->no_transaksi;  ?>
                                          <a href="<?php echo 'https://psb.labschoolcibubur.sch.id/slip-pembayaran/'.urlencode($status_daftar_ulang_smp->email).'/'.'ft/'.$this->db->escape_like_str($status_daftar_ulang_smp->nama_lengkap).'?no_transaksi='.$no_transaksi; ?>" style="text-align: center;" target="_blank">Slip Pembayaran Digital</a>
                                          <!-- <?php if (is_image($status_daftar_ulang_smp->slip_pembayaran)) : ?>
                                             <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/slip_pembayaran/' . $status_daftar_ulang_smp->slip_pembayaran; ?>">
                                                <img src="<?php //echo BASE_URL . 'uploads/slip_pembayaran/' . $status_daftar_ulang_smp->slip_pembayaran; ?>" class="image-responsive" alt="image slip_pembayaran" title="slip_pembayaran" width="40px">
                                             </a>
                                          <?php else : ?>
                                             <a href="<?php //echo BASE_URL . 'uploads/slip_pembayaran/' . $status_daftar_ulang_smp->slip_pembayaran; ?>">
                                                <img src="<?php //echo get_icon_file($status_daftar_ulang_smp->slip_pembayaran); ?>" class="image-responsive image-icon" alt="image slip_pembayaran" title="slip_pembayaran <?= $status_daftar_ulang_smp->slip_pembayaran; ?>" width="40px">
                                             </a>
                                          <?php endif; ?> -->
                                       <?php endif; ?>
                                    </td>
                                    <td>
                                       <?php if (!empty($status_daftar_ulang_smp->kwitansi)) : ?>
                                          <?php if (is_image($status_daftar_ulang_smp->kwitansi)) : ?>
                                             <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/kwitansi/' . $status_daftar_ulang_smp->kwitansi; ?>">
                                                <img src="<?= BASE_URL . 'uploads/kwitansi/' . $status_daftar_ulang_smp->kwitansi; ?>" class="image-responsive" alt="image kwitansi" title="kwitansi" width="40px">
                                             </a>
                                          <?php else : ?>
                                             <a href="<?= BASE_URL . 'uploads/kwitansi/' . $status_daftar_ulang_smp->kwitansi; ?>">
                                                <img src="<?= get_icon_file($status_daftar_ulang_smp->kwitansi); ?>" class="image-responsive image-icon" alt="image kwitansi" title="kwitansi <?= $status_daftar_ulang_smp->kwitansi; ?>" width="40px">
                                             </a>
                                          <?php endif; ?>
                                       <?php endif; ?>
                                    </td>
                                    <td>
                                       <?php if (!empty($status_daftar_ulang_smp->kartu_sementara)) : ?>
                                          <?php if (is_image($status_daftar_ulang_smp->kartu_sementara)) : ?>
                                             <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/kartu_siswa_sementara/' . $status_daftar_ulang_smp->kartu_sementara; ?>">
                                                <img src="<?= BASE_URL . 'uploads/kartu_siswa_sementara/' . $status_daftar_ulang_smp->kartu_sementara; ?>" class="image-responsive" alt="image kartu_sementara" title="kartu_sementara" width="40px">
                                             </a>
                                          <?php else : ?>
                                             <a href="<?= BASE_URL . 'uploads/kartu_siswa_sementara/' . $status_daftar_ulang_smp->kartu_sementara; ?>">
                                                <img src="<?= get_icon_file($status_daftar_ulang_smp->kartu_sementara); ?>" class="image-responsive image-icon" alt="image kartu_sementara" title="kartu_sementara <?= $status_daftar_ulang_smp->kartu_sementara; ?>" width="40px">
                                             </a>
                                          <?php endif; ?>
                                       <?php endif; ?>
                                    </td>
                                    <td><?= _ent($status_daftar_ulang_smp->tanggal_lulus); ?></td>
                                    <td><?= _ent($status_daftar_ulang_smp->tgl_daftar_ulang); ?></td>
                                    <td width="200">

                                       <?php is_allowed('status_daftar_ulang_smp_view', function () use ($status_daftar_ulang_smp) { ?>
                                          <a href="<?= site_url('administrator/status_daftar_ulang_smp/single_pdf/' . $status_daftar_ulang_smp->id_daftar_ulang); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                                             <a href="<?= site_url('administrator/status_daftar_ulang_smp/view/' . $status_daftar_ulang_smp->id_daftar_ulang); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                                             <?php }) ?>
                                             <?php is_allowed('status_daftar_ulang_smp_update', function () use ($status_daftar_ulang_smp) { ?>
                                                <a href="<?= site_url('administrator/status_daftar_ulang_smp/edit/' . $status_daftar_ulang_smp->id_daftar_ulang); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                                             <?php }) ?>
                                             <?php is_allowed('status_daftar_ulang_smp_delete', function () use ($status_daftar_ulang_smp) { ?>
                                                <a href="javascript:void(0);" data-href="<?= site_url('administrator/status_daftar_ulang_smp/delete/' . $status_daftar_ulang_smp->id_daftar_ulang); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                                             <?php }) ?>

                                    </td>
                                 </tr>
                              <?php endforeach; ?>
                              <?php if ($status_daftar_ulang_smp_counts == 0) : ?>
                                 <tr>
                                    <td colspan="100">
                                       Status Daftar Ulang Smp data is not available
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
                     <div class="col-sm-2 padd-left-0 ">
                        <select type="text" class="form-control chosen chosen-select" name="bulk" id="bulk" placeholder="Site Email">
                           <option value="">Bulk</option>
                           <option value="1">Aktivasi VA</option>
                           <option value="2">Siswa Aktif</option>
                        </select>
                        <input type="hidden" id="st" name="st">
                     </div>
                     <div class="col-sm-2 padd-left-0 ">
                        <button type="button" class="btn btn-flat" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>"><?= cclang('apply_button'); ?></button>
                     </div>
                     <div class="col-sm-3 padd-left-0  ">
                        <input type="text" class="form-control" name="q" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('q'); ?>">
                     </div>
                     <div class="col-sm-3 padd-left-0 ">
                        <select type="text" class="form-control chosen chosen-select" name="f" id="field">
                           <option value=""><?= cclang('all'); ?></option>
                           <option <?= $this->input->get('f') == 'id_siswa_smp' ? 'selected' : ''; ?> value="id_siswa_smp">Id Siswa Smp</option>
                           <option <?= $this->input->get('f') == 'status' ? 'selected' : ''; ?> value="status">Status</option>
                           <option <?= $this->input->get('f') == 'slip_pembayaran' ? 'selected' : ''; ?> value="slip_pembayaran">Slip Pembayaran</option>
                           <option <?= $this->input->get('f') == 'kwitansi' ? 'selected' : ''; ?> value="kwitansi">Kwitansi</option>
                           <option <?= $this->input->get('f') == 'kartu_sementara' ? 'selected' : ''; ?> value="kartu_sementara">Kartu Sementara</option>
                           <option <?= $this->input->get('f') == 'tanggal_lulus' ? 'selected' : ''; ?> value="tanggal_lulus">Tanggal Lulus</option>
                           <option <?= $this->input->get('f') == 'tgl_daftar_ulang' ? 'selected' : ''; ?> value="tgl_daftar_ulang">Tgl Daftar Ulang</option>
                        </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                           Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/status_daftar_ulang_smp'); ?>" title="<?= cclang('reset_filter'); ?>">
                           <i class="fa fa-undo"></i>
                        </a>
                     </div>
                  </div>
                  </form>
                  <div class="col-md-4">
                     <div class="dataTables_paginate paging_simple_numbers pull-right" id="example2_paginate">
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
<!-- ============ MODAL ADD BARANG =============== -->
<div class="modal fade" id="modal_add_new" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Tambah Siswa Baru (Khusus Siswa Aktif)</h3>
         </div>
         <form action="<?= base_url('administrator/status_daftar_ulang_smp/siswa_aktif/'); ?>" method="post" class="form-horizontal" id="form-aktif">
            <div class="modal-body">

               <div class="form-group mb-3">
                  <label class="control-label col-xs-3">Kelas</label>
                  <div class="col-xs-8">
                     <select class="form-control chosen chosen-select-deselect" name="kelas_smp" id="kelas_smp" data-placeholder="Select Tahun Ajaran" required>
                        <option value=""></option>
                        <?php foreach (db_get_all_data('kelas_smp') as $row) : ?>
                           <option value="<?= $row->id_kelas_smp ?>"><?= $row->label; ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="form-group mb-3">
                  <label class="control-label col-xs-3">Tahun Ajaran</label>
                  <div class="col-xs-8">
                     <select class="form-control chosen chosen-select-deselect" name="tahun_ajaran" id="tahun_ajaran" data-placeholder="Select Tahun Ajaran" required>
                        <option value=""></option>
                        <?php foreach (db_get_all_data('tahun_ajaran') as $row) : ?>
                           <option value="<?= $row->id_tahun_ajaran ?>"><?= $row->label; ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="form-group mb-3">
                  <label for="spp_custom" class="col-xs-3 control-label">SPP Khusus <span class="text-danger">Optional</span>
                  </label>
                  <div class="col-xs-8">
                     <input type="text" class="form-control" name="spp_custom" id="spp_custom" placeholder="SPP khusus">
                     <small class="info help-block">
                     </small>
                  </div>
               </div>
            </div>
            <input type="hidden" name="ids" id="ids">
            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Tutup</button>
               <button class="btn btn-info btn_save">Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL ADD BARANG-->
<!-- Page script -->
<script>
   $(document).ready(function() {

      $('.remove-data').click(function() {

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
            function(isConfirm) {
               if (isConfirm) {
                  document.location.href = url;
               }
            });

         return false;
      });


      $('#apply').click(function() {

         var bulk = $('#bulk');
         var serialize_bulk = $('#form_status_daftar_ulang_smp').serialize();

         if (bulk.val() == '1') {
            swal({
                  title: "<?= cclang('Aktivasi Virtual Account'); ?>",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "<?= cclang('Ya'); ?>",
                  cancelButtonText: "<?= cclang('Batal'); ?>",
                  closeOnConfirm: true,
                  closeOnCancel: true
               },
               function(isConfirm) {
                  if (isConfirm) {
                     $('#st').val('1');
                     document.location.href = BASE_URL + '/administrator/status_daftar_ulang_smp/update_status?' + serialize_bulk;
                  }
               });

            return false;

         } else if (bulk.val() == '2') {
            $('#modal_add_new').modal('show');
            var form_aktif = $('#form-aktif');

            var serialize = $('#form_status_daftar_ulang_smp').serializeArray()
            // data_post.append('ids',serialize);
            $('#ids').val(JSON.stringify(serialize));
            var data_post = form_aktif.serializeArray();
            console.log(data_post);
         } else if (bulk.val() == '') {
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

      }); /*end appliy click*/


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

      checkboxes.on('ifChanged', function(event) {
         if (checkboxes.filter(':checked').length == checkboxes.length) {
            checkAll.prop('checked', 'checked');
         } else {
            checkAll.removeProp('checked');
         }
         checkAll.iCheck('update');
      });

   }); /*end doc ready*/
</script>