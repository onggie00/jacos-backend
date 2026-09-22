<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.css">
<style>
.btn-action{color:#fff !important;text-decoration:none !important;}
.btn-action:hover,.btn-action:focus{color:#fff !important;text-decoration:none !important;opacity:.85;}
</style>
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('presensi_office') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('presensi_office') ?></li>
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
                     <div class="row pull-right hidden">
                        <?php is_allowed('presensi_office_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('presensi_office')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/presensi_office/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('presensi_office')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('presensi_office_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('presensi_office') ?>']); ?>" href="<?= site_url('administrator/presensi_office/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('presensi_office_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('presensi_office') ?>']); ?>" href="<?= site_url('administrator/presensi_office/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('presensi_office') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('presensi_office')]); ?>  <i class="label bg-yellow"><?= $presensi_office_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_presensi_office" id="form_presensi_office" action="<?= base_url('administrator/presensi_office/index'); ?>">
                  

                  <div class="table-responsive hidden"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('npp') ?></th>
                           <th> <?= cclang('nama_lengkap') ?></th>
                           <th> <?= cclang('presensi_date') ?></th>
                           <th> <?= cclang('check_in') ?></th>
                           <th> <?= cclang('check_out') ?></th>
                           <th> <?= cclang('presensi_device') ?></th>
                           <th> <?= cclang('keterangan') ?></th>
                           <th> <?= cclang('file_report') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_presensi_office">
                     <?php foreach($presensi_offices as $presensi_office): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $presensi_office->id_presensi; ?>">
                           </td>
                                                       
                           <td><?= _ent($presensi_office->npp); ?></td> 
                           <td><?= _ent($presensi_office->nama_lengkap); ?></td> 
                           <td><?= _ent($presensi_office->presensi_date); ?></td> 
                           <td><?= _ent($presensi_office->check_in); ?></td> 
                           <td><?= _ent($presensi_office->check_out); ?></td> 
                           <td><?= _ent($presensi_office->presensi_device); ?></td> 
                           <td><?= _ent($presensi_office->keterangan); ?></td> 
                           <td>
                              <?php if (!empty($presensi_office->file_report)): ?>
                                <?php if (is_image($presensi_office->file_report)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/presensi_office/' . $presensi_office->file_report; ?>">
                                  <img src="<?= BASE_URL . 'uploads/presensi_office/' . $presensi_office->file_report; ?>" class="image-responsive" alt="image presensi_office" title="file_report presensi_office" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/presensi_office/' . $presensi_office->file_report; ?>">
                                   <img src="<?= get_icon_file($presensi_office->file_report); ?>" class="image-responsive image-icon" alt="image presensi_office" title="file_report <?= $presensi_office->file_report; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td width="200">
                            
                                                              <?php is_allowed('presensi_office_view', function() use ($presensi_office){?>
                                 <a href="<?= site_url('administrator/presensi_office/single_pdf/' .$presensi_office->id_presensi); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/presensi_office/view/' . $presensi_office->id_presensi); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('presensi_office_update', function() use ($presensi_office){?>
                              <a href="<?= site_url('administrator/presensi_office/edit/' . $presensi_office->id_presensi); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('presensi_office_delete', function() use ($presensi_office){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/presensi_office/delete/' . $presensi_office->id_presensi); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($presensi_office_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Presensi Office data is not available
                           </td>
                         </tr>
                      <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>
               <!-- /.widget-user -->
               <div class="row hidden">
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
                            <option <?= $this->input->get('f') == 'npp' ? 'selected' :''; ?> value="npp">NPP</option>
                           <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' :''; ?> value="nama_lengkap">Nama Lengkap</option>
                           <option <?= $this->input->get('f') == 'presensi_date' ? 'selected' :''; ?> value="presensi_date">Date</option>
                           <option <?= $this->input->get('f') == 'check_in' ? 'selected' :''; ?> value="check_in">Check In</option>
                           <option <?= $this->input->get('f') == 'check_out' ? 'selected' :''; ?> value="check_out">Check Out</option>
                           <option <?= $this->input->get('f') == 'presensi_device' ? 'selected' :''; ?> value="presensi_device">Device</option>
                           <option <?= $this->input->get('f') == 'keterangan' ? 'selected' :''; ?> value="keterangan">Keterangan</option>
                           <option <?= $this->input->get('f') == 'file_report' ? 'selected' :''; ?> value="file_report">File Report</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/presensi_office');?>" title="<?= cclang('reset_filter'); ?>">
                        <i class="fa fa-undo"></i>
                        </a>
                     </div>
                  </div>
                  </form>
                  <div class="col-md-4">
                     <div class="dataTables_paginate paging_simple_numbers pull-right" id="example2_paginate" >
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>

               <div class="form-group row">
                  <form id="form_filter" method="post">
                     <input id="nama_tabel" type="hidden" name="nama_tabel" value="<?= $this->uri->segment(3); ?>">
                     <div class="col-md-2">
                     <label>Start Date</label>
                     <!-- <select class="form-control" name="start_date" id="start_date"></select> -->
                     <input type="date" name="start_date" id="start_date" class="form-control">
                     </div>
                     <div class="col-md-2">
                     <label>End Date</label>
                     <!-- <select class="form-control" name="end_date" id="end_date"></select> -->
                     <input type="date" name="end_date" id="end_date" class="form-control">
                     </div>
                     <div class="col-md-3" style="margin-top: 25px;">
                     <button id="btn_filter" class="btn btn-primary">Filter</button>
                     <button id="btn_sync" class="btn btn-info">
                        <span id="sync_text">Synchronize</span>
                        <span id="sync_loading" style="display:none; margin-left:8px;">
                           <i class="fa fa-spinner fa-spin"></i>
                        </span>
                     </button>
                     <span id="sync_loading" style="display:none; margin-left:8px;">⏳ Sync ing proggress</span>
                     <button id="btn_export" class="btn btn-success">Export Monthly</button>
                     </div>
                     <!--select & search user-->
                     <div class="form-group col-md-5" style="margin-top: 25px;">
                        <div class="col-md-7">
                           <select class="form-control chosen chosen-select-deselect" name="npp[]" id="npp" data-placeholder="Select User" multiple>
                              <option value=""></option>
                              <?php foreach ($list_user as $row) : ?>
                                 <option value="<?= $row->npp ?>"><?= $row->nama_lengkap." - ".strtoupper($row->presensi_role); ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                        <button id="btn_export_personal" class="btn btn-success">Export Personal</button>
                     </div>
                  </form>
               </div>
               <div class="chart" id="chart-presensi" style="height: 200px;"></div>

               <!--CHART PRESENSI OFFICE-->
               
            </div>
            <!--/box body -->
         </div>
         <!--/box -->
      </div>
   </div>

<!--Datatable Start-->
<div class="row">
      <!-- /.content -->
      <div class="col-md-12" style="margin-top: 0px;" >
      <div class="box box-info">
            <div class="box-header with-border">
               <h3 class="box-title"> Persentase Presensi</h3>

               <div class="box-tools pull-right">
                  <button id="btn_export_persentase" class="btn btn-success">Export <i class="fa fa-file-excel-o"></i></button>
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
               </div>
            </div>
            <div class="box-body chart-responsive">
               
            <div id="test_table">
               <table id="detail_table_persentase" class="table table-striped table-bordered" cellspacing="0" width="100%">
               <thead>
                  <tr>
                     <th>NPP</th>
                     <th>Fullname</th>
                     <th>Periode</th>
                     <th>Total Kehadiran</th>
                     <th>Total Keterlambatan</th>
                     <th>Total Tidak Hadir</th>
                     <th>Total Pulang Cepat</th>
                     <th>Persentase Kehadiran</th>
                     <th>Persentase Keterlambatan</th>
                     <th>Persentase Tidak Hadir</th>
                     <th>Persentase Pulang Cepat</th>
                  </tr>
               </thead>
                  <tbody></tbody>
               </table>
            </div>
            </div>
            <!-- /.box-body -->
         </div>
         <!-- /.box -->
      </div>
   </div>
<!--Datatable End-->

<!--Datatable Start-->
   <div class="row">
      <!-- /.content -->
      <div class="col-md-12" style="margin-top: 0px;" >
      <div class="box box-info">
            <div class="box-header with-border">
               <h3 class="box-title"> Detail Presensi</h3>

               <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
               </div>
            </div>
            <div class="box-body chart-responsive">
               
            <div id="test_table">
               <table id="detail_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
               <thead>
                  <tr>
                     <th>NO</th>
                     <th>NPP</th>
                     <th>FULLNAME</th>
                     <th>DATE</th>
                     <th>WORK START</th>
                     <th>WORK END</th>
                     <th>DAY</th>
                     <th>DEVICE</th>
                     <th>DESCRIPTION</th>
                     <th>REPORT</th>
                     <th>ACTION</th>
                  </tr>
               </thead>
                  <tbody></tbody>
               </table>
            </div>
            </div>
            <!-- /.box-body -->
         </div>
         <!-- /.box -->
      </div>
   </div>
<!--Datatable End-->
</section>
<!-- /.content -->

<!-- Page script -->
<script src="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.bootstrap.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.bootstrap.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.html5.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.print.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.colVis.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script>
  $(document).ready(function(){

   $.ajax({
      type: "POST",
      url: "<?= site_url('administrator/presensi_office/chart_presensi'); ?>",
      data: {
         nama_tabel: $("#nama_tabel").val(),
         start_date: $("#start_date").val(),
         end_date: $("#end_date").val(),
      },
      success: function (res) {

         let raw = JSON.parse(res);
         let chartData = [];
         let roles = [];

         $.each(raw, function (idx, item) {

               let row = {
                  tanggal: item.tanggal
               };

               Object.keys(item).forEach(function (key) {
                  if (key !== 'tanggal') {
                     row[key] = parseInt(item[key]);

                     if (!roles.includes(key)) {
                           roles.push(key);
                     }
                  }
               });

               chartData.push(row);
         });

         $("#chart-presensi").empty();

         new Morris.Bar({
               element: 'chart-presensi',
               resize: true,
               data: chartData,
               xkey: 'tanggal',
               ykeys: roles,
               labels: roles,
               barColors: ['#00A65A', '#4285F4', '#EA4335', '#FF8000'],
               hideHover: 'auto'
         });
      },
      error: function () {
         alert('error');
      }
   });
   //datatable siswa
   $('#detail_table').DataTable( {
      // paging: true,
      // pageLength : 10,
      // searching: true,
      // ordering: true,
      // processing: true,
      // serverSide: true,
        ajax: {
          url: "<?= site_url('administrator/presensi_office/table_data'); ?>",
          type: 'POST',
          data: {
            nama_tabel: "<?= $this->uri->segment(3); ?>",
            start_date: "<?= date('Y-m-d'); ?>",
            end_date: "<?= date('Y-m-d'); ?>",
          }
        },
        columns: [
            { data: 'id_presensi', name: 'id_presensi' },
            { data: 'npp', name: 'npp' },
            { data: 'nama_lengkap', name: 'nama_lengkap' },
            { data: 'presensi_date', name: 'presensi_date' },
            { data: 'check_in', name: 'check_in' },
            { data: 'check_out', name: 'check_out' },
            { data: 'presensi_hari', name: 'presensi_hari' },
            { data: 'presensi_device', name: 'presensi_device' },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'file_report', name: 'file_report' },
            { data: 'action', name: 'action' },
         ],
         columnDefs: [
            { targets: [0,1,3,4,5,6,7,8,10], className: "text-center" },
            { targets: [], className: "text-right" },
            { targets: '_all', className: 'dt-head-center' }
         ],
         order: [[ 9, 'desc' ],[ 0, 'desc' ]],
         scrollX: true,
         responsive: true
   });
   window.detailTable = $('#detail_table').DataTable();

   //persentase default
   $('#detail_table_persentase').DataTable( {
            // paging: true,
            // pageLength : 10,
            // search: true,
            // ordering: true,
            // processing: true,
            // serverSide: true,
            ajax: {
            url: "<?= site_url('administrator/presensi_office/persentase_data'); ?>",
            type: 'POST',
            data: {
               nama_tabel: "<?= $this->uri->segment(3); ?>",
               start_date: "<?= date('Y-m-d'); ?>",
               end_date: "<?= date('Y-m-d'); ?>",
            }
         },
         columnDefs: [
               { targets: '_all', className: "text-center" },
               // { targets: [0,1,2,3,4,5,6,7,8,9], className: "text-center" },
               { targets: '_all', className: 'dt-head-center' },
            ],
         columns: [
               { data: 'npp', name: 'npp' },
               { data: 'nama_lengkap', name: 'nama_lengkap' },
               { data: 'bulan', name: 'bulan' },
               { data: 'total_kehadiran', name: 'total_kehadiran' },
               { data: 'total_terlambat', name: 'total_terlambat' },
               { data: 'total_tidak_hadir', name: 'total_tidak_hadir' },
               { data: 'total_pulang_cepat', name: 'total_pulang_cepat' },
               { data: 'persentase_kehadiran', name: 'persentase_kehadiran' },
               { data: 'persentase_terlambat', name: 'persentase_terlambat' },
               { data: 'persentase_tidak_hadir', name: 'persentase_tidak_hadir' },
               { data: 'persentase_pulang_cepat', name: 'persentase_pulang_cepat' }
            ],
            order: [[ 1, 'asc' ], [ 2, 'asc' ]],
            scrollX: true,
            responsive: true
         });

      $("#btn_filter").click(function(e) {
        e.preventDefault();
         $.ajax({
            type: "POST",
            url: "<?= site_url('administrator/presensi_office/chart_presensi'); ?>",
            data: {
               nama_tabel: $("#nama_tabel").val(),
               start_date: $("#start_date").val(),
               end_date: $("#end_date").val(),
            },
            success: function (res) {

               let raw = JSON.parse(res);
               let chartData = [];
               let roles = [];

               $.each(raw, function (idx, item) {

                     let row = {
                        tanggal: item.tanggal
                     };

                     Object.keys(item).forEach(function (key) {
                        if (key !== 'tanggal') {
                           row[key] = parseInt(item[key]);

                           if (!roles.includes(key)) {
                                 roles.push(key);
                           }
                        }
                     });

                     chartData.push(row);
               });

               $("#chart-presensi").empty();

               new Morris.Bar({
                     element: 'chart-presensi',
                     resize: true,
                     data: chartData,
                     xkey: 'tanggal',
                     ykeys: roles,
                     labels: roles,
                     barColors: ['#00A65A', '#4285F4', '#EA4335', '#FF8000'],
                     hideHover: 'auto'
               });
            },
            error: function () {
               alert('error');
            }
         });

//Persentase
//datatable reinitalization
         $('#detail_table_persentase').DataTable().destroy();
         $('#detail_table_persentase').DataTable( {
            // paging: true,
            // pageLength : 10,
            // search: true,
            // ordering: true,
            // processing: true,
            // serverSide: true,
            ajax: {
            url: "<?= site_url('administrator/presensi_office/persentase_data'); ?>",
            type: 'POST',
            data: {
               nama_tabel: "<?= $this->uri->segment(3); ?>",
               start_date: $("#start_date").val(),
               end_date: $("#end_date").val(),
            }
         },
         columnDefs: [
               { targets: '_all', className: "text-center" },
               // { targets: [0,1,2,3,4,5,6,7,8,9], className: "text-center" },
               { targets: '_all', className: 'dt-head-center' },
            ],
         columns: [
               { data: 'npp', name: 'npp' },
               { data: 'nama_lengkap', name: 'nama_lengkap' },
               { data: 'bulan', name: 'bulan' },
               { data: 'total_kehadiran', name: 'total_kehadiran' },
               { data: 'total_terlambat', name: 'total_terlambat' },
               { data: 'total_tidak_hadir', name: 'total_tidak_hadir' },
               { data: 'total_pulang_cepat', name: 'total_pulang_cepat' },
               { data: 'persentase_kehadiran', name: 'persentase_kehadiran' },
               { data: 'persentase_terlambat', name: 'persentase_terlambat' },
               { data: 'persentase_tidak_hadir', name: 'persentase_tidak_hadir' },
               { data: 'persentase_pulang_cepat', name: 'persentase_pulang_cepat' }
            ],
            order: [[ 1, 'asc' ], [ 2, 'asc' ]],
            scrollX: true,
            responsive: true
         });

//Detail Presensi
        //datatable reinitalization
         $('#detail_table').DataTable().destroy();
         $('#detail_table').DataTable( {
            // paging: true,
            // pageLength : 10,
            // search: true,
            // ordering: true,
            // processing: true,
            // serverSide: true,
            ajax: {
            url: "<?= site_url('administrator/presensi_office/table_data'); ?>",
            type: 'POST',
            data: {
               nama_tabel: "<?= $this->uri->segment(3); ?>",
               start_date: $("#start_date").val(),
               end_date: $("#end_date").val(),
            }
         },
         columns: [
               { data: 'id_presensi', name: 'id_presensi' },
               { data: 'npp', name: 'npp' },
               { data: 'nama_lengkap', name: 'nama_lengkap' },
               { data: 'presensi_date', name: 'presensi_date' },
               { data: 'check_in', name: 'check_in' },
               { data: 'check_out', name: 'check_out' },
               { data: 'presensi_hari', name: 'presensi_hari' },
               { data: 'presensi_device', name: 'presensi_device' },
               { data: 'keterangan', name: 'keterangan' },
               { data: 'file_report', name: 'file_report' },
               { data: 'action', name: 'action' },
            ],
            columnDefs: [
               { targets: [0,1,3,4,5,6,7,8,10], className: "text-center" },
               { targets: [2], className: "text-right" },
               { targets: '_all', className: 'dt-head-center' },
            ],
            order: [[ 9, 'desc' ],[ 0, 'desc' ]],
            scrollX: true,
            responsive: true
         });
         window.detailTable = $('#detail_table').DataTable();

      });

      $("#btn_sync").click(function(e) {
         e.preventDefault();

         $.ajax({
            type: "GET",
            url: "<?= site_url('/apiapp/face_recognition/get_today_presensi_non_siswa'); ?>",
            data: {
                  page_size: 10000,
                  start_time: $("#start_date").val()+" 00:00:00",
                  end_time: $("#end_date").val()+" 23:59:59",
            },

            beforeSend: function() {
                  $("#btn_sync").prop("disabled", true);
                  $("#sync_text").text("Synchronizing...");
                  $("#sync_loading").show();
            },

            success: function (res) {
                  alert('Synchronize Successfully');
                  $("#btn_filter").click();
            },

            error: function () {
                  alert('Error');
            },

            complete: function() {
                  $("#btn_sync").prop("disabled", false);
                  $("#sync_text").text("Synchronize");
                  $("#sync_loading").hide();
            }
         });
      });

      $("#btn_export").click(function(e) {
         e.preventDefault();
         let role = $("#nama_tabel").val();
         let start_date = $("#start_date").val();
         let end_date = $("#end_date").val();

         window.location.href = "<?= site_url('administrator/presensi_office/export_harian'); ?>?role="+role+"&start_date="+start_date+"&end_date="+end_date;
      });

      $("#btn_export_personal").click(function(e) {
         e.preventDefault();
         let role = $("#nama_tabel").val();
         let start_date = $("#start_date").val();
         let end_date = $("#end_date").val();
         let npp = $("#npp").val();

         window.location.href = "<?= site_url('administrator/presensi_office/export_personal'); ?>?role="+role+"&start_date="+start_date+"&end_date="+end_date+"&npp="+npp;
      });

      $("#btn_export_persentase").click(function(e) {
         e.preventDefault();
         let role = $("#nama_tabel").val();
         let start_date = $("#start_date").val();
         let end_date = $("#end_date").val();

         window.location.href = "<?= site_url('administrator/presensi_office/export_excel_persentase'); ?>?nama_tabel="+role+"&start_date="+start_date+"&end_date="+end_date;
      });
   
    //approve/decline laporan presensi via AJAX — update datatable saja, halaman tidak di-refresh
    $(document).on('click', '.btn-action', function(){
      var url = $(this).attr('data-url');
      var btn = $(this);
      swal({
          title: "Konfirmasi",
          text: "Setuju untuk memproses laporan ini?",
          type: "warning",
          showCancelButton: true,
          confirmButtonText: "Ya, proses",
          cancelButtonText: "Batal",
          closeOnConfirm: true,
          closeOnCancel: true
        },
        function(isConfirm){
          if (!isConfirm) return;
          btn.prop('disabled', true);
          $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function (res) {
              if (res.status) {
                window.detailTable.ajax.reload(null, false);
              } else {
                btn.prop('disabled', false);
                swal("Gagal", res.message || "Terjadi kesalahan", "error");
              }
            },
            error: function () {
              btn.prop('disabled', false);
              swal("Error", "Terjadi kesalahan jaringan", "error");
            }
          });
        });
      return false;
    });

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
      var serialize_bulk = $('#form_presensi_office').serialize();

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
               document.location.href = BASE_URL + '/administrator/presensi_office/delete?' + serialize_bulk;      
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