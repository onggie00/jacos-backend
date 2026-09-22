<script type="text/javascript">
</script>

<!-- Content Header -->
<section class="content-header">
   <h1>
      <?= cclang('activity_user') ?>
      <small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('activity_user') ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">

<?php if (!empty($recent_activities)): ?>
<!-- Notification Bar -->
<div id="notification-bar" style="margin-bottom:15px;">
   <div class="box box-success box-solid" style="margin-bottom:0;">
      <div class="box-header with-border">
         <h3 class="box-title" style="cursor:pointer;" id="btn-toggle-notification">
            <i class="fa fa-bell"></i> Aktivitas Terakhir (3 hari terakhir)
            <i class="fa fa-chevron-down" id="notif-toggle-icon" style="margin-left:8px;font-size:12px;"></i>
         </h3>
         <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" id="btn-minimize-notification" title="Minimize/Expand notifikasi">
               <i class="fa fa-minus"></i>
            </button>
         </div>
      </div>
      <div class="box-body" id="notification-body" style="padding:0;">
         <div class="table-responsive" style="margin:0;">
         <table class="table table-condensed table-hover" style="margin-bottom:0;">
            <thead>
               <tr class="bg-green">
                  <th width="40">No</th>
                  <th>Keterangan</th>
                  <th>Endpoint</th>
                  <th width="160">Tanggal</th>
                  <th width="130">Updated By</th>
                  <th width="100">Aksi</th>
               </tr>
            </thead>
            <tbody>
               <?php $rno = 1; foreach($recent_activities as $ra): ?>
               <tr>
                  <td class="text-center"><?= $rno++; ?></td>
                  <td><strong><?= _ent($ra->keterangan); ?></strong></td>
                  <td><code><?= _ent($ra->endpoint ?? '-'); ?></code></td>
                  <td><i class="fa fa-clock-o"></i> <?= _ent($ra->created_at); ?></td>
                  <td><i class="fa fa-user"></i> <?= _ent($ra->updated_by); ?></td>
                  <td class="text-center">
                     <button type="button"
                             class="btn btn-xs btn-success btn-flat btn-view-detail"
                             title="Lihat Detail"
                             data-id="<?= $ra->id_activity ?>"
                             data-keterangan="<?= htmlspecialchars($ra->keterangan, ENT_QUOTES) ?>"
                             data-endpoint="<?= htmlspecialchars($ra->endpoint ?? '', ENT_QUOTES) ?>"
                             data-value="<?= htmlspecialchars($ra->value ?? '', ENT_QUOTES) ?>"
                             data-created_at="<?= htmlspecialchars($ra->created_at, ENT_QUOTES) ?>"
                             data-updated_by="<?= htmlspecialchars($ra->updated_by, ENT_QUOTES) ?>"
                             data-ip_address="<?= htmlspecialchars($ra->ip_address ?? '', ENT_QUOTES) ?>">
                        <i class="fa fa-eye"></i>
                     </button>
                     <?php is_allowed('activity_user_update', function() use ($ra){?>
                     <a href="<?= site_url('administrator/activity_user/edit/' . $ra->id_activity); ?>"
                        class="btn btn-xs btn-warning btn-flat" title="Edit">
                        <i class="fa fa-pencil"></i>
                     </a>
                     <?php }) ?>
                  </td>
               </tr>
               <?php endforeach; ?>
            </tbody>
         </table>
         </div>
      </div>
   </div>
</div>
<?php endif; ?>

   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">

            <!-- Box Header -->
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-history"></i> Log Aktivitas
                  <span class="label label-info"><?= $activity_user_counts; ?> data</span>
               </h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('activity_user_export', function(){?>
                  <a class="btn btn-success btn-sm btn-flat" title="Export XLS" href="<?= site_url('administrator/activity_user/export'); ?>">
                     <i class="fa fa-file-excel-o"></i> Export XLS
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <!-- Box Body -->
            <div class="box-body">

               <!-- Search & Filter Bar -->
               <form name="form_activity_user" id="form_activity_user" action="<?= base_url('administrator/activity_user/index'); ?>">
               <div class="row" style="margin-bottom:15px;">
                  <div class="col-md-5">
                     <div class="input-group">
                        <input type="text" class="form-control" name="q" id="filter" placeholder="Cari aktivitas..." value="<?= htmlspecialchars($this->input->get('q')); ?>">
                        <span class="input-group-btn">
                           <button type="submit" class="btn btn-primary btn-flat" name="sbtn" id="sbtn">
                              <i class="fa fa-search"></i> Cari
                           </button>
                           <a class="btn btn-default btn-flat" name="reset" id="reset" href="<?= base_url('administrator/activity_user'); ?>" title="Reset filter">
                              <i class="fa fa-undo"></i>
                           </a>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control chosen chosen-select" name="f" id="field">
                        <option value="">-- Semua Kolom --</option>
                        <option <?= $this->input->get('f') == 'keterangan' ? 'selected' : ''; ?> value="keterangan">Keterangan</option>
                        <option <?= $this->input->get('f') == 'value' ? 'selected' : ''; ?> value="value">Value</option>
                        <option <?= $this->input->get('f') == 'created_at' ? 'selected' : ''; ?> value="created_at">Tanggal</option>
                        <option <?= $this->input->get('f') == 'updated_by' ? 'selected' : ''; ?> value="updated_by">Updated By</option>
                     </select>
                  </div>
                  <div class="col-md-2">
                     <select class="form-control chosen chosen-select" name="f_endpoint" id="filter_endpoint">
                        <option value="">-- Semua Endpoint --</option>
                        <?php
                        $endpoints = array_unique(array_map(function($r){ return $r->endpoint ?? ''; }, $activity_users));
                        sort($endpoints);
                        foreach ($endpoints as $ep) {
                           if (!empty($ep)) {
                              $sel = ($this->input->get('f_endpoint') == $ep) ? 'selected' : '';
                              echo "<option value=\"".htmlspecialchars($ep)."\" {$sel}>".htmlspecialchars($ep)."</option>";
                           }
                        }
                        ?>
                     </select>
                  </div>
               </div>
               </form>

               <!-- Table -->
               <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover">
                  <thead>
                     <tr>
                        <th width="40">No</th>
                        <th width="180">Keterangan</th>
                        <th>Endpoint</th>
                        <th>Value</th>
                        <th width="160">Tanggal</th>
                        <th width="150">Updated By</th>
                        <th width="100">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php if (empty($activity_users)): ?>
                        <tr>
                           <td colspan="7" class="text-center text-muted">
                              <i class="fa fa-inbox fa-2x" style="display:block;margin:10px 0;"></i>
                              Tidak ada data aktivitas
                           </td>
                        </tr>
                     <?php else: ?>
                        <?php $no = ($this->uri->segment(4)) ? $this->uri->segment(4) + 1 : 1; ?>
                        <?php foreach($activity_users as $activity_user): ?>
                        <tr>
                           <td class="text-center"><?= $no++; ?></td>
                           <td><strong><?= _ent($activity_user->keterangan); ?></strong></td>
                           <td><code><?= _ent($activity_user->endpoint ?? '-'); ?></code></td>
                           <td>
                              <?php
                              $val = $activity_user->value ?? '';
                              if (!empty($val) && strlen($val) > 60) {
                                 echo '<span title="'.htmlspecialchars($val).'">' . _ent(substr($val, 0, 60)) . '...</span>';
                              } else {
                                 echo _ent($val) ?: '<span class="text-muted">-</span>';
                              }
                              ?>
                           </td>
                           <td><i class="fa fa-clock-o"></i> <?= _ent($activity_user->created_at); ?></td>
                           <td><i class="fa fa-user"></i> <?= _ent($activity_user->updated_by); ?></td>
                           <td class="text-center">
                              <button type="button"
                                      class="btn btn-sm btn-default btn-flat btn-view-detail"
                                      title="Lihat Detail"
                                      data-id="<?= $activity_user->id_activity ?>"
                                      data-keterangan="<?= htmlspecialchars($activity_user->keterangan, ENT_QUOTES) ?>"
                                      data-endpoint="<?= htmlspecialchars($activity_user->endpoint ?? '', ENT_QUOTES) ?>"
                                      data-value="<?= htmlspecialchars($activity_user->value ?? '', ENT_QUOTES) ?>"
                                      data-created_at="<?= htmlspecialchars($activity_user->created_at, ENT_QUOTES) ?>"
                                      data-updated_by="<?= htmlspecialchars($activity_user->updated_by, ENT_QUOTES) ?>"
                                      data-ip_address="<?= htmlspecialchars($activity_user->ip_address ?? '', ENT_QUOTES) ?>">
                                 <i class="fa fa-eye"></i>
                              </button>
                              <?php is_allowed('activity_user_update', function() use ($activity_user){?>
                              <a href="<?= site_url('administrator/activity_user/edit/' . $activity_user->id_activity); ?>"
                                 class="btn btn-sm btn-warning btn-flat" title="Edit">
                                 <i class="fa fa-pencil"></i>
                              </a>
                              <?php }) ?>
                              <?php is_allowed('activity_user_delete', function() use ($activity_user){?>
                              <button type="button"
                                      class="btn btn-sm btn-danger btn-flat btn-delete"
                                      title="Hapus"
                                      data-href="<?= site_url('administrator/activity_user/delete/' . $activity_user->id_activity); ?>">
                                 <i class="fa fa-trash"></i>
                              </button>
                              <?php }) ?>
                           </td>
                        </tr>
                        <?php endforeach; ?>
                     <?php endif; ?>
                  </tbody>
               </table>
               </div>

               <!-- Bulk + Pagination -->
               <div class="row" style="margin-top:10px;">
                  <div class="col-md-6">
                     <div class="input-group" style="max-width:350px;">
                        <select class="form-control" name="bulk" id="bulk">
                           <option value="">-- Aksi Massal --</option>
                           <option value="delete">Hapus Terpilih</option>
                        </select>
                        <span class="input-group-btn">
                           <button type="button" class="btn btn-warning btn-flat" name="apply" id="apply">
                              <i class="fa fa-check"></i> Terapkan
                           </button>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="pull-right">
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-info-circle"></i> Detail Aktivitas</h4>
         </div>
         <div class="modal-body">
            <table class="table table-bordered table-condensed">
               <tr>
                  <th width="140" class="bg-gray-light">ID</th>
                  <td id="modal-id"></td>
               </tr>
               <tr>
                  <th class="bg-gray-light">Keterangan</th>
                  <td id="modal-keterangan"></td>
               </tr>
               <tr>
                  <th class="bg-gray-light">Endpoint</th>
                  <td><code id="modal-endpoint"></code></td>
               </tr>
               <tr>
                  <th class="bg-gray-light">Tanggal</th>
                  <td id="modal-created_at"></td>
               </tr>
               <tr>
                  <th class="bg-gray-light">Updated By</th>
                  <td id="modal-updated_by"></td>
               </tr>
               <tr>
                  <th class="bg-gray-light">IP Address</th>
                  <td id="modal-ip_address"></td>
               </tr>
            </table>

            <h5><i class="fa fa-code"></i> Value (JSON)</h5>
            <div id="modal-value-table" style="max-height:400px;overflow-y:auto;">
               <p class="text-muted">Tidak ada data value</p>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">
               <i class="fa fa-times"></i> Tutup
            </button>
         </div>
      </div>
   </div>
</div>

</section>
<!-- /.content -->

<!-- Page Script -->
<script>
$(document).ready(function(){

   // Minimize/Expand notification bar
   function toggleNotification() {
      var $body = $('#notification-body');
      var $icon = $('#notif-toggle-icon');
      $body.slideToggle(200);
      $icon.toggleClass('fa-chevron-down fa-chevron-up');
      var minimized = $body.is(':hidden') ? '1' : '0';
      sessionStorage.setItem('activity_notif_minimized', minimized);
   }
   $('#btn-minimize-notification').click(toggleNotification);
   $('#btn-toggle-notification').click(toggleNotification);
   // Restore state from session
   if (sessionStorage.getItem('activity_notif_minimized') === '1') {
      $('#notification-body').hide();
      $('#notif-toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
   }

   // View detail modal (delegated event for dynamic elements)
   $(document).on('click', '.btn-view-detail', function(){
      var btn = $(this);
      $('#modal-id').text(btn.data('id'));
      $('#modal-keterangan').text(btn.data('keterangan'));
      $('#modal-endpoint').text(btn.data('endpoint') || '-');
      $('#modal-created_at').text(btn.data('created_at'));
      $('#modal-updated_by').text(btn.data('updated_by'));
      $('#modal-ip_address').text(btn.data('ip_address') || '-');

      var rawValue = btn.data('value');
      var $container = $('#modal-value-table');

      // Handle empty/null
      if (rawValue === undefined || rawValue === null || rawValue === '') {
         $container.html('<p class="text-muted"><i class="fa fa-minus-circle"></i> Tidak ada data value</p>');
      }
      // jQuery .data() auto-parses JSON string to object
      else if (typeof rawValue === 'object') {
         $container.html(jsonToHtmlTable(rawValue));
      }
      // String: try parse JSON, fallback to plain text
      else {
         var strValue = String(rawValue).trim();
         if (strValue === '') {
            $container.html('<p class="text-muted"><i class="fa fa-minus-circle"></i> Tidak ada data value</p>');
         } else {
            try {
               var parsed = JSON.parse(strValue);
               $container.html(jsonToHtmlTable(parsed));
            } catch(e) {
               $container.html('<pre style="background:#f5f5f5;padding:10px;border-radius:4px;max-height:300px;overflow:auto;">' + escapeHtml(strValue) + '</pre>');
            }
         }
      }

      $('#modalDetail').modal('show');
   });

   // Delete action
   $(document).on('click', '.btn-delete', function(){
      var url = $(this).data('href');
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
      }, function(isConfirm){
         if (isConfirm) {
            document.location.href = url;
         }
      });
   });

   // Bulk action
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_activity_user').serialize();

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
         }, function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/activity_user/delete?' + serialize_bulk;
            }
         });
      } else {
         swal({
            title: "Upss",
            text: "<?= cclang('please_choose_bulk_action_first'); ?>",
            type: "warning",
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Okay!"
         });
      }
      return false;
   });

});

// Check if object is change-log format: all values are {lama, baru}
function isChangeLog(obj) {
   if (typeof obj !== 'object' || obj === null || Array.isArray(obj)) return false;
   var keys = Object.keys(obj);
   if (keys.length === 0) return false;
   return keys.every(function(k) {
      var v = obj[k];
      return typeof v === 'object' && v !== null && v.hasOwnProperty('lama') && v.hasOwnProperty('baru');
   });
}

// Format field name: snake_case -> Title Case
function formatFieldName(str) {
   return str.replace(/_/g, ' ').replace(/\b\w/g, function(c){ return c.toUpperCase(); });
}

// Convert JSON to HTML table
function jsonToHtmlTable(obj) {
   if (typeof obj !== 'object' || obj === null) {
      return '<table class="table table-bordered table-condensed table-striped"><tr><td>' + escapeHtml(String(obj)) + '</td></tr></table>';
   }

   // Change-log format: {field: {lama, baru}}
   if (isChangeLog(obj)) {
      var html = '<table class="table table-bordered table-condensed table-striped">';
      html += '<thead><tr class="bg-light-blue">';
      html += '<th style="width:200px;">Field</th>';
      html += '<th>Sebelumnya</th>';
      html += '<th>Sesudah</th>';
      html += '</tr></thead><tbody>';
      Object.keys(obj).forEach(function(field) {
         var lama = obj[field].lama;
         var baru = obj[field].baru;
         var isSame = (String(lama) === String(baru));
         html += '<tr>';
         html += '<th class="bg-gray-light">' + escapeHtml(formatFieldName(field)) + '</th>';
         html += '<td>' + (lama === null || lama === '' ? '<span class="text-muted">-</span>' : escapeHtml(String(lama))) + '</td>';
         if (isSame) {
            html += '<td class="text-muted">' + escapeHtml(String(baru)) + ' <small>(sama)</small></td>';
         } else {
            html += '<td class="text-success"><strong>' + escapeHtml(String(baru)) + '</strong></td>';
         }
         html += '</tr>';
      });
      html += '</tbody></table>';
      return html;
   }

   if (Array.isArray(obj)) {
      if (obj.length === 0) return '<p class="text-muted">Array kosong</p>';
      if (typeof obj[0] === 'object' && obj[0] !== null) {
         var keys = [];
         obj.forEach(function(item){
            Object.keys(item).forEach(function(k){
               if (keys.indexOf(k) === -1) keys.push(k);
            });
         });
         var html = '<table class="table table-bordered table-condensed table-striped"><thead><tr>';
         keys.forEach(function(k){ html += '<th>' + escapeHtml(k) + '</th>'; });
         html += '</tr></thead><tbody>';
         obj.forEach(function(item){
            html += '<tr>';
            keys.forEach(function(k){ html += '<td>' + formatCellValue(item[k]) + '</td>'; });
            html += '</tr>';
         });
         html += '</tbody></table>';
         return html;
      }
      var html = '<table class="table table-bordered table-condensed table-striped"><thead><tr><th>Index</th><th>Value</th></tr></thead><tbody>';
      obj.forEach(function(item, i){ html += '<tr><td>' + i + '</td><td>' + formatCellValue(item) + '</td></tr>'; });
      html += '</tbody></table>';
      return html;
   }

   var keys = Object.keys(obj);
   if (keys.length === 0) return '<p class="text-muted">Object kosong</p>';
   var html = '<table class="table table-bordered table-condensed table-striped"><thead><tr><th>Field</th><th>Value</th></tr></thead><tbody>';
   keys.forEach(function(k){
      html += '<tr><th class="bg-gray-light" style="width:200px;">' + escapeHtml(k) + '</th><td>' + formatCellValue(obj[k]) + '</td></tr>';
   });
   html += '</tbody></table>';
   return html;
}

function formatCellValue(val) {
   if (val === null || val === undefined) return '<span class="text-muted">null</span>';
   if (typeof val === 'object') return jsonToHtmlTable(val);
   if (typeof val === 'boolean') return val ? '<span class="label label-success">true</span>' : '<span class="label label-danger">false</span>';
   return escapeHtml(String(val));
}

function escapeHtml(str) {
   var div = document.createElement('div');
   div.appendChild(document.createTextNode(str));
   return div.innerHTML;
}
</script>
