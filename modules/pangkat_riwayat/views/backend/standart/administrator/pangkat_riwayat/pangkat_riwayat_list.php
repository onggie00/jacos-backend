
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('pangkat_riwayat') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('pangkat_riwayat') ?></li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row" >
      
      <!-- Promotion Recommendation Section -->
      <div class="col-md-12">
         <div class="box box-success">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-line-chart"></i> Rekomendasi Kenaikan Pangkat</h3>
               <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
               </div>
            </div>
            <div class="box-body">
               <div class="row">
                  <!-- Chart Section -->
                  <div class="col-md-4">
                     <div class="chart-responsive">
                        <canvas id="promotionChart" height="200"></canvas>
                     </div>
                  </div>
                  
                  <!-- Summary Cards -->
                  <div class="col-md-8">
                     <div class="row">
                        <div class="col-md-6">
                           <div class="info-box bg-green">
                              <span class="info-box-icon"><i class="fa fa-arrow-circle-up"></i></span>
                              <div class="info-box-content">
                                 <span class="info-box-text">Harus Naik Pangkat</span>
                                 <span class="info-box-number"><?= $promotion_data['chart_data']['must_promote'] ?> Orang</span>
                                 <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                 </div>
                                 <span class="progress-description">Sudah melewati 4 tahun</span>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="info-box bg-yellow">
                              <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                              <div class="info-box-content">
                                 <span class="info-box-text">Akan Naik Pangkat</span>
                                 <span class="info-box-number"><?= $promotion_data['chart_data']['will_promote'] ?> Orang</span>
                                 <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                 </div>
                                 <span class="progress-description">Mendekati 4 tahun</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     
                     <div class="row">
                        <div class="col-md-12">
                           <div class="alert alert-info">
                              <h5><i class="fa fa-info-circle"></i> Keterangan:</h5>
                              <ul style="margin-bottom: 0;">
                                 <li><b>"Harus Naik"</b> = TMT sudah lewat 4 tahun dari hari ini</li>
                                 <li><b>"Akan Naik"</b> = TMT belum mencapai 4 tahun</li>
                                 <li>Rekomendasi otomatis hilang jika pegawai sudah memiliki SK baru</li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Promotion Tables -->
               <div class="row">
                  <!-- Must Promote Table -->
                  <div class="col-md-6">
                     <div class="box box-success">
                        <div class="box-header with-border">
                           <h4 class="box-title"><i class="fa fa-arrow-circle-up"></i> Daftar Harus Naik Pangkat</h4>
                        </div>
                        <div class="box-body table-responsive no-padding">
                           <table class="table table-hover table-striped">
                              <thead>
                                 <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Pangkat/Gol</th>
                                    <th>TMT</th>
                                    <th>Tanggal Naik</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php if (!empty($promotion_data['must_promote'])): ?>
                                    <?php foreach ($promotion_data['must_promote'] as $idx => $item): ?>
                                       <tr>
                                          <td><?= $idx + 1 ?></td>
                                          <td><?= _ent($item->nama_lengkap_user) ?></td>
                                          <td><span class="label label-primary"><?= _ent($item->role_name) ?></span></td>
                                          <td><?= _ent($item->pangkat) ?> (<?= _ent($item->golongan) ?>)</td>
                                          <td><?= _ent($item->tmt) ?></td>
                                          <td><span class="label label-danger"><?= $item->promotion_date ?></span></td>
                                       </tr>
                                    <?php endforeach; ?>
                                 <?php else: ?>
                                    <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
                                 <?php endif; ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Will Promote Table -->
                  <div class="col-md-6">
                     <div class="box box-warning">
                        <div class="box-header with-border">
                           <h4 class="box-title"><i class="fa fa-clock"></i> Daftar Akan Naik Pangkat</h4>
                        </div>
                        <div class="box-body table-responsive no-padding">
                           <table class="table table-hover table-striped">
                              <thead>
                                 <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Pangkat/Gol</th>
                                    <th>TMT</th>
                                    <th>Tanggal Naik</th>
                                    <th>Sisa Hari</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php if (!empty($promotion_data['will_promote'])): ?>
                                    <?php foreach ($promotion_data['will_promote'] as $idx => $item): ?>
                                       <tr>
                                          <td><?= $idx + 1 ?></td>
                                          <td><?= _ent($item->nama_lengkap_user) ?></td>
                                          <td><span class="label label-primary"><?= _ent($item->role_name) ?></span></td>
                                          <td><?= _ent($item->pangkat) ?> (<?= _ent($item->golongan) ?>)</td>
                                          <td><?= _ent($item->tmt) ?></td>
                                          <td><span class="label label-warning"><?= $item->promotion_date ?></span></td>
                                          <td><span class="label label-info"><?= $item->days_remaining ?> hari</span></td>
                                       </tr>
                                    <?php endforeach; ?>
                                 <?php else: ?>
                                    <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
                                 <?php endif; ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      
      <!-- Existing Pangkat Riwayat Section -->
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-body ">
               <!-- Widget: user widget style 1 -->
               <div class="box box-widget widget-user-2">
                  <!-- Add the bg color to the header using any of the bg-* classes -->
                  <div class="widget-user-header ">
                     <div class="row pull-right">
                        <?php is_allowed('pangkat_riwayat_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('pangkat_riwayat')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/pangkat_riwayat/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('pangkat_riwayat')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('pangkat_riwayat_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('pangkat_riwayat') ?>" href="<?= site_url('administrator/pangkat_riwayat/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('pangkat_riwayat_add', function(){?>
                        <button type="button" class="btn btn-flat btn-info" data-toggle="modal" data-target="#modalImport"><i class="fa fa-upload"></i> Import Excel</button>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('pangkat_riwayat') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('pangkat_riwayat')]); ?>  <i class="label bg-yellow"><?= $pangkat_riwayat_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_pangkat_riwayat" id="form_pangkat_riwayat" action="<?= base_url('administrator/pangkat_riwayat/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('unit') ?></th>
                           <th> <?= cclang('nama_tabel') ?></th>
                           <th> <?= cclang('Nama Lengkap') ?></th>
                           <th> <?= cclang('npp') ?></th>
                           <th> <?= cclang('nomor') ?></th>
                           <th> <?= cclang('pangkat') ?></th>
                           <th> <?= cclang('golongan') ?></th>
                           <th> <?= cclang('tmt') ?></th>
                           <th> <?= cclang('is_sk_calon') ?></th>
                           <th> <?= cclang('is_cant_promoted') ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_pangkat_riwayat">
                     <?php foreach($pangkat_riwayats as $pangkat_riwayat): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $pangkat_riwayat->id_pangkat; ?>">
                           </td>
                                                       
                           <td><?= _ent($pangkat_riwayat->unit); ?></td> 
                           <td><?php if  ($pangkat_riwayat->nama_tabel) {

                              echo anchor('administrator/presensi_setting_role/view/'.$pangkat_riwayat->nama_tabel.'?popup=show', $pangkat_riwayat->presensi_setting_role_nama_role, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($pangkat_riwayat->nama_lengkap_user); ?></td>
                             
                           <td><?= _ent($pangkat_riwayat->npp); ?></td> 
                           <td><?= _ent($pangkat_riwayat->nomor); ?></td> 
                           <td><?= _ent($pangkat_riwayat->pangkat); ?></td> 
                           <td><?= _ent($pangkat_riwayat->golongan); ?></td> 
                           <td><?= _ent($pangkat_riwayat->tmt); ?></td> 
                           <td><?= _ent($pangkat_riwayat->is_sk_calon); ?></td> 
                           <td><?= _ent($pangkat_riwayat->is_cant_promoted); ?></td> 
                           <td width="200">
                           
                           <?php is_allowed('pangkat_riwayat_view', function() use ($pangkat_riwayat){?>
                              <a href="<?= site_url('administrator/pangkat_riwayat/view/' . $pangkat_riwayat->id_pangkat); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('pangkat_riwayat_update', function() use ($pangkat_riwayat){?>
                              <a href="<?= site_url('administrator/pangkat_riwayat/edit/' . $pangkat_riwayat->id_pangkat); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('pangkat_riwayat_delete', function() use ($pangkat_riwayat){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/pangkat_riwayat/delete/' . $pangkat_riwayat->id_pangkat); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($pangkat_riwayat_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Pangkat Riwayat data is not available
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
                            <option <?= $this->input->get('f') == 'unit' ? 'selected' :''; ?> value="unit">Unit</option>
                           <option <?= $this->input->get('f') == 'nama_tabel' ? 'selected' :''; ?> value="nama_tabel">Role</option>
                           <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' :''; ?> value="nama_lengkap">Nama Lengkap</option>
                           <option <?= $this->input->get('f') == 'npp' ? 'selected' :''; ?> value="npp">NPP</option>
                           <option <?= $this->input->get('f') == 'nomor' ? 'selected' :''; ?> value="nomor">Nomor</option>
                           <option <?= $this->input->get('f') == 'pangkat' ? 'selected' :''; ?> value="pangkat">Pangkat</option>
                           <option <?= $this->input->get('f') == 'golongan' ? 'selected' :''; ?> value="golongan">Golongan</option>
                           <option <?= $this->input->get('f') == 'tmt' ? 'selected' :''; ?> value="tmt">TMT</option>
                           <option <?= $this->input->get('f') == 'is_sk_calon' ? 'selected' :''; ?> value="is_sk_calon">SK Calon?</option>
                           <option <?= $this->input->get('f') == 'is_cant_promoted' ? 'selected' :''; ?> value="is_cant_promoted">Sudah Maksimal?</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/pangkat_riwayat');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_pangkat_riwayat').serialize();

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
               document.location.href = BASE_URL + '/administrator/pangkat_riwayat/delete?' + serialize_bulk;      
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

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function(){
   // Promotion Chart
   var ctx = document.getElementById('promotionChart').getContext('2d');
   var promotionChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
         labels: ['Harus Naik', 'Akan Naik'],
         datasets: [{
            data: [<?= $promotion_data['chart_data']['must_promote'] ?>, <?= $promotion_data['chart_data']['will_promote'] ?>],
            backgroundColor: ['#00a65a', '#f39c12'],
            borderWidth: 2,
            borderColor: '#fff'
         }]
      },
      options: {
         responsive: true,
         maintainAspectRatio: false,
         plugins: {
            legend: {
               position: 'bottom',
               labels: {
                  padding: 20,
                  usePointStyle: true,
                  pointStyle: 'circle'
               }
            },
            tooltip: {
               callbacks: {
                  label: function(context) {
                     var label = context.label || '';
                     var value = context.parsed || 0;
                     return label + ': ' + value + ' Orang';
                  }
               }
            }
         }
      }
   });
});
</script>

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalImportLabel"><i class="fa fa-upload"></i> Import Data Pangkat Riwayat</h4>
      </div>
      <form id="formImport" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group">
            <label for="file_import">Pilih File Excel (.xls, .xlsx)</label>
            <input type="file" name="file_import" id="file_import" accept=".xls,.xlsx" required>
            <small class="help-block">
              <b>Format file:</b> Gunakan format yang sama dengan file export. <br>
              <b>Kolom yang diperlukan:</b> Unit, Role, Nama Lengkap, NPP, Nomor, Pangkat, Golongan, TMT, SK Calon, Sudah Maksimal<br>
              <b>Catatan:</b> <br>
              - Role diisi dengan nama role (contoh: OFFICE, SD, SMP, SMA, dll)<br>
              - Nama Lengkap harus sesuai dengan data di tabel role yang dipilih<br>
              - SK Calon dan Sudah Maksimal diisi dengan "Ya" atau "Tidak"<br>
              - <b>Validasi:</b> Jika NPP atau Nomor SK sudah ada, data akan diupdate. Jika belum ada, data baru akan ditambahkan.
            </small>
          </div>
          <div id="import-progress" style="display:none;">
            <div class="progress">
              <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                Mengupload...
              </div>
            </div>
          </div>
          <div id="import-result"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
          <button type="submit" class="btn btn-primary" id="btn-import"><i class="fa fa-upload"></i> Import</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  $('#formImport').on('submit', function(e){
    e.preventDefault();
    
    var fileInput = $('#file_import')[0];
    if (fileInput.files.length === 0) {
      swal('Peringatan', 'Pilih file terlebih dahulu!', 'warning');
      return;
    }
    
    var formData = new FormData(this);
    
    $('#btn-import').prop('disabled', true);
    $('#import-progress').show();
    $('#import-result').html('');
    
    $.ajax({
      url: BASE_URL + '/administrator/pangkat_riwayat/import',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
    })
    .done(function(res) {
      if (res.success) {
        var alertType = 'success';
        var html = '<div class="alert alert-' + alertType + '">' + res.message + '</div>';
        
        if (res.errors && res.errors.length > 0) {
          html += '<div class="alert alert-warning"><b>Error Detail:</b><ul>';
          $.each(res.errors, function(i, err){
            html += '<li>' + err + '</li>';
          });
          html += '</ul></div>';
        }
        
        $('#import-result').html(html);
        
        // Reload page after 2 seconds
        setTimeout(function(){
          window.location.reload();
        }, 2000);
      } else {
        $('#import-result').html('<div class="alert alert-danger">' + res.message + '</div>');
      }
    })
    .fail(function() {
      $('#import-result').html('<div class="alert alert-danger">Terjadi kesalahan saat upload</div>');
    })
    .always(function() {
      $('#btn-import').prop('disabled', false);
      $('#import-progress').hide();
    });
  });
  
  // Reset modal when closed
  $('#modalImport').on('hidden.bs.modal', function(){
    $('#formImport')[0].reset();
    $('#import-result').html('');
    $('#import-progress').hide();
  });
});
</script>