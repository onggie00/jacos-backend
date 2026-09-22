<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('trans_bri-open') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('trans_bri_open') ?></li>
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
                     <input type="hidden" id="start_date" value="<?= $this->input->get('start_date'); ?>" >
                     <input type="hidden" id="end_date" value="<?= $this->input->get('end_date'); ?>" >

                        <?php is_allowed('trans_bri_open_export', function () { ?>
                           <button class="btn btn-flat btn-success" id="export" title="<?= cclang('export'); ?> <?= cclang('trans_bri_open') ?>']); ?>"><i class="fa fa-file-excel-o"></i> <?= cclang('export'); ?> XLS</button>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('trans_bri_open') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('trans_bri_open')]); ?> <i class="label bg-yellow"><?= $trans_bri_open_counts; ?> <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_trans_bri_open" id="form_trans_bri_open" action="<?= base_url('administrator/trans_bri_open/index'); ?>">


                     <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                           <thead>
                              <tr class="">
                                 <th>brivaNo</th>
                                 <th>custCode</th>
                                 <th>Nama</th>
                                 <th>Keterangan</th>
                                 <th>Jumlah</th>
                                 <th>Payment Date</th>
                                 <th>Teller id</th>
                                 <th>No Rek</th>

                              </tr>
                           </thead>
                           <tbody id="tbody_trans_bri_open">
                              <?php foreach ($trans_bris as $key => $trans_bri_open) : ?>
                                 <tr>
                                    <td><?= _ent($trans_bri_open['brivaNo']); ?></td>
                                    <td><?= _ent($trans_bri_open['custCode']); ?></td>
                                    <td><?= _ent($trans_bri_open['nama']); ?></td>
                                    <td><?= _ent($trans_bri_open['keterangan']); ?></td>
                                    <td><?= _ent($trans_bri_open['amount']); ?></td>
                                    <td><?= _ent($trans_bri_open['paymentDate']); ?></td>
                                    <td><?= _ent($trans_bri_open['tellerid']); ?></td>
                                    <td><?= _ent($trans_bri_open['no_rek']); ?></td>
                                 </tr>
                              <?php endforeach; ?>
                              <?php if ($trans_bri_open_counts == 0) : ?>
                                 <tr>
                                    <td colspan="100">
                                       Trans Bri data is not available
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
                     <!-- <div class="col-sm-2 padd-left-0 ">
                        <select type="text" class="form-control chosen chosen-select" name="bulk" id="bulk" placeholder="Site Email">
                           <option value="">Bulk</option>
                           <option value="delete">Delete</option>
                        </select>
                     </div> -->
                     <!-- <div class="col-sm-2 padd-left-0 ">
                        <button type="button" class="btn btn-flat" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>"><?= cclang('apply_button'); ?></button>
                     </div> -->
                     <!-- <div class="col-sm-3 padd-left-0  ">
                        <input type="text" class="form-control" name="q" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('q'); ?>">
                     </div> -->
                     <div class="col-sm-3 padd-left-0  ">
                        <input type="date" class="form-control" name="start_date" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('start_date'); ?>">
                     </div>
                     <div class="col-sm-3 padd-left-0  ">
                        <input type="date" class="form-control" name="end_date" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('end_date'); ?>">
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                           Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/trans_bri_open'); ?>" title="<?= cclang('reset_filter'); ?>">
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
         var serialize_bulk = $('#form_trans_bri_open').serialize();

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
               function(isConfirm) {
                  if (isConfirm) {
                     document.location.href = BASE_URL + '/administrator/trans_bri_open/delete?' + serialize_bulk;
                  }
               });

            return false;

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

      console.log($('#date').val())
      $('#export').click(function() {

         // var export = $('#export');
         document.location.href = BASE_URL + 'administrator/trans_bri_open/export?start_date=' + $('#start_date').val() + '&end_date=' + $('#end_date').val();
         
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