<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<style>
/* Stat card mini (infobox kecil) */
.labs-stat-card.labs-stat-card--mini { margin-bottom: 0; }
.labs-stat-card.labs-stat-card--mini .labs-stat-card__icon { width: 46px; font-size: 17px; }
.labs-stat-card.labs-stat-card--mini .labs-stat-card__body { padding: 8px 14px; }
.labs-stat-card.labs-stat-card--mini .labs-stat-card__value { font-size: 16px; }

/* Override DataTables agar seragam dengan labs-table */
.labs-table.dataTable thead th { background: linear-gradient(180deg, #FFFBF5, #F5F0EA); border-bottom: 2px solid var(--labs-border-strong); }
.labs-table.dataTable tbody td { border-bottom: 1px solid var(--labs-border); }
.labs-table.dataTable tbody tr:hover { background: var(--labs-bg-soft); }
</style>

<script type="text/javascript">
<?php if ($this->session->flashdata('success')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('success'))); ?>
   toastr.success("<?= $msg; ?>", "Berhasil", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } else if ($this->session->flashdata('error')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('error'))); ?>
   toastr.error("<?= $msg; ?>", "Error", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } else if ($this->session->flashdata('warning')) { ?>
   <?php $msg = str_replace("\n", "<br>", addslashes($this->session->flashdata('warning'))); ?>
   toastr.warning("<?= $msg; ?>", "Peringatan", { timeOut: 0, extendedTimeOut: 0, closeButton: true, tapToDismiss: false, escapeHtml: false });
<?php } else if ($this->session->flashdata('info')) { ?>
   toastr.info("<?= addslashes($this->session->flashdata('info')); ?>");
<?php } ?>
</script>

<!-- Content Header -->
<section class="content-header">
   <h1><i class="fa fa-exchange"></i> <?= cclang('transaksi') ?> <small class="labs-text-muted"><?= cclang('list_all'); ?></small></h1>
   <ol class="breadcrumb"><li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li><li class="active"><?= cclang('transaksi') ?></li></ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">

         <!-- Infobox Total Terbayar / Belum Terbayar -->
         <div class="row">
            <div class="col-md-3 col-sm-6">
               <div class="labs-stat-card labs-stat-card--mini">
                  <div class="labs-stat-card__icon labs-stat-card__icon--success"><i class="fa fa-check-circle"></i></div>
                  <div class="labs-stat-card__body">
                     <div class="labs-stat-card__label labs-fs-xs">Total Terbayar <span class="labs-text-muted">(<?= (int) $summary->jumlah_terbayar; ?> transaksi)</span></div>
                     <div class="labs-stat-card__value labs-text-success">Rp <?= number_format((int) $summary->total_terbayar, 0, ',', '.'); ?></div>
                  </div>
               </div>
            </div>
            <div class="col-md-3 col-sm-6">
               <div class="labs-stat-card labs-stat-card--mini">
                  <div class="labs-stat-card__icon labs-stat-card__icon--danger"><i class="fa fa-clock-o"></i></div>
                  <div class="labs-stat-card__body">
                     <div class="labs-stat-card__label labs-fs-xs">Total Belum Terbayar <span class="labs-text-muted">(<?= (int) $summary->jumlah_belum; ?> transaksi)</span></div>
                     <div class="labs-stat-card__value labs-text-danger">Rp <?= number_format((int) $summary->total_belum, 0, ',', '.'); ?></div>
                  </div>
               </div>
            </div>
         </div>

         <div class="labs-card">

            <!-- Card Header -->
            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-exchange"></i> Data Transaksi
                  <span class="labs-badge labs-badge--orange"><?= $transaksi_counts; ?> Data</span>
               </h3>
               <div>
                  <?php is_allowed('transaksi_export', function(){?>
                  <a class="labs-btn labs-btn--success" title="<?= cclang('export'); ?> <?= cclang('transaksi'); ?> (XLS)" href="<?= site_url('administrator/transaksi/export'); ?>"><i class="fa fa-file-excel-o"></i> Export XLS</a>
                  <?php }) ?>
                  <?php is_allowed('transaksi_export', function(){?>
                  <a class="labs-btn labs-btn--danger" title="<?= cclang('export'); ?> <?= cclang('transaksi'); ?> (PDF)" href="<?= site_url('administrator/transaksi/export_pdf'); ?>"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
                  <?php }) ?>
               </div>
            </div>

            <div class="labs-card__body">

               <!-- Filter Bar -->
               <form name="form_transaksi" id="form_transaksi" action="<?= base_url('administrator/transaksi/index'); ?>">
               <div class="labs-filter-inline">
                  <div class="labs-filter-inline__search">
                     <i class="fa fa-search labs-filter-inline__search-icon"></i>
                     <input type="text" class="form-control" name="q" id="filter" placeholder="Cari no. transaksi, nama siswa, NISN, no. peserta, VA, email..." value="<?= $this->input->get('q'); ?>">
                  </div>
                  <select class="form-control" name="f" id="field" style="width:180px;height:34px;font-size:12px">
                     <option value=""><?= cclang('all'); ?></option>
                     <option <?= $this->input->get('f') == 'no_transaksi' ? 'selected' : ''; ?> value="no_transaksi">No Transaksi</option>
                     <option <?= $this->input->get('f') == 'nama_bank' ? 'selected' : ''; ?> value="nama_bank">Nama Bank</option>
                     <option <?= $this->input->get('f') == 'va_number' ? 'selected' : ''; ?> value="va_number">Va Number</option>
                     <option <?= $this->input->get('f') == 'user_email' ? 'selected' : ''; ?> value="user_email">User Email</option>
                     <option <?= $this->input->get('f') == 'user_name' ? 'selected' : ''; ?> value="user_name">User Name</option>
                     <option <?= $this->input->get('f') == 'user_phone' ? 'selected' : ''; ?> value="user_phone">User Phone</option>
                     <option <?= $this->input->get('f') == 'description' ? 'selected' : ''; ?> value="description">Description</option>
                     <option <?= $this->input->get('f') == 'total_biaya' ? 'selected' : ''; ?> value="total_biaya">Total Biaya</option>
                     <option <?= $this->input->get('f') == 'status_transaksi' ? 'selected' : ''; ?> value="status_transaksi">Status Transaksi</option>
                     <option <?= $this->input->get('f') == 'expired_datetime' ? 'selected' : ''; ?> value="expired_datetime">Expired Datetime</option>
                     <option <?= $this->input->get('f') == 'created_at' ? 'selected' : ''; ?> value="created_at">Created At</option>
                     <option <?= $this->input->get('f') == 'updated_at' ? 'selected' : ''; ?> value="updated_at">Updated At</option>
                     <option disabled>-------- Biodata Siswa --------</option>
                     <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' : ''; ?> value="nama_lengkap">Nama Lengkap Siswa</option>
                     <option <?= $this->input->get('f') == 'nisn' ? 'selected' : ''; ?> value="nisn">NISN</option>
                     <option <?= $this->input->get('f') == 'no_peserta' ? 'selected' : ''; ?> value="no_peserta">No. Peserta</option>
                  </select>
                  <select class="form-control" name="s" id="sort" style="width:130px;height:34px;font-size:12px">
                     <option value="">Sort By</option>
                     <option <?= $this->input->get('s') == 'updated_at' ? 'selected' : ''; ?> value="updated_at">Updated At</option>
                     <option <?= $this->input->get('s') == 'created_at' ? 'selected' : ''; ?> value="created_at">Created At</option>
                  </select>
                  <select class="form-control" name="d" id="sort_type" style="width:110px;height:34px;font-size:12px">
                     <option <?= $this->input->get('d') == 'desc' ? 'selected' : ''; ?> value="desc">Descending</option>
                     <option <?= $this->input->get('d') == 'asc' ? 'selected' : ''; ?> value="asc">Ascending</option>
                  </select>
                  <button type="submit" class="labs-btn labs-btn--primary"><i class="fa fa-search"></i> Cari</button>
                  <a class="labs-btn labs-btn--default" href="<?= base_url('administrator/transaksi'); ?>"><i class="fa fa-undo"></i> Reset</a>
                  <?php if(!empty($this->input->get('q'))): ?>
                  <span class="labs-text-muted labs-fs-sm" style="align-self:center">
                     Hasil: <strong>"<?= $this->input->get('q'); ?>"</strong>
                  </span>
                  <?php endif; ?>
               </div>

               <!-- Data Table -->
               <div class="labs-table-wrap">
                  <div class="labs-table-scroll">
                  <table class="labs-table dataTable">
                     <thead>
                        <tr>
                           <th width="30">
                              <input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all">
                           </th>
                           <th><?= cclang('no_transaksi'); ?></th>
                           <th><?= cclang('nama_bank'); ?></th>
                           <th><?= cclang('va_number'); ?></th>
                           <th><?= cclang('user_email'); ?></th>
                           <th><?= cclang('user_name'); ?></th>
                           <th><?= cclang('user_phone'); ?></th>
                           <th>Biodata Siswa</th>
                           <th><?= cclang('description'); ?></th>
                           <th><?= cclang('jenjang'); ?></th>
                           <th><?= cclang('total_biaya'); ?></th>
                           <th>Akumulasi Payment</th>
                           <th><?= cclang('status_transaksi'); ?></th>
                           <th><?= cclang('expired_datetime'); ?></th>
                           <th><?= cclang('created_at'); ?></th>
                           <th><?= cclang('updated_at'); ?></th>
                           <th width="160">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_transaksi">
                     <?php if ($transaksi_counts > 0) : ?>
                     <?php foreach($transaksis as $transaksi): ?>
                        <tr>
                           <td class="labs-text-center">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $transaksi->id_transaksi; ?>">
                           </td>
                           <td><span class="labs-fw-semi"><?= _ent($transaksi->no_transaksi); ?></span></td>
                           <td class="labs-text-center"><span class="labs-badge labs-badge--default"><?= _ent($transaksi->nama_bank); ?></span></td>
                           <td><?= _ent($transaksi->va_number); ?></td>
                           <td class="labs-cell-muted"><?= _ent($transaksi->user_email); ?></td>
                           <td><?= _ent($transaksi->user_name); ?></td>
                           <td class="labs-cell-muted"><?= _ent($transaksi->user_phone); ?></td>
                           <!-- Biodata siswa dari join 4 tabel siswa_* -->
                           <?php
                              $biodata_nama = '';
                              $biodata_nisn = '';
                              $biodata_no_peserta = '';
                              foreach (array('sd', 'smp', 'sma', 'ft') as $t) {
                                 $id_field = 'id_siswa_' . $t;
                                 if (!empty($transaksi->$id_field)) {
                                    $biodata_nama = $transaksi->{'nama_lengkap_' . $t};
                                    $biodata_nisn = $transaksi->{'nisn_' . $t};
                                    $biodata_no_peserta = $transaksi->{'no_peserta_' . $t};
                                    break;
                                 }
                              }
                           ?>
                           <td>
                              <?php if ($biodata_nama != '') : ?>
                                 <div class="labs-name-cell">
                                    <span class="labs-avatar labs-avatar--sm"><i class="fa fa-user"></i></span>
                                    <span>
                                       <span class="labs-fw-semi"><?= _ent($biodata_nama); ?></span><br>
                                       <small class="labs-text-muted">NISN: <?= _ent($biodata_nisn != '' ? $biodata_nisn : '-'); ?> &bull; No. Peserta: <?= _ent($biodata_no_peserta != '' ? $biodata_no_peserta : '-'); ?></small>
                                    </span>
                                 </div>
                              <?php else : ?>
                                 <span class="labs-text-light"><i class="fa fa-user"></i> -</span>
                              <?php endif; ?>
                           </td>
                           <td class="labs-cell-muted"><?= _ent($transaksi->description); ?></td>
                           <td class="labs-text-center"><span class="labs-badge labs-badge--info labs-badge--solid"><?= _ent($transaksi->jenjang); ?></span></td>
                           <td class="labs-cell-numeric labs-fw-semi">Rp <?= number_format((int) $transaksi->total_biaya, 0, ',', '.'); ?></td>
                           <td class="labs-cell-numeric">Rp <?= number_format((int) $transaksi->payment_amount, 0, ',', '.'); ?></td>
                           <?php
                              if ($transaksi->status_transaksi == 0) {
                                 $status = "Menunggu Pembayaran";
                                 $status_badge = "warning";
                              } else {
                                 $status = "Pembayaran Berhasil";
                                 $status_badge = "success";
                              }
                           ?>
                           <td class="labs-text-center"><span class="labs-badge labs-badge--<?= $status_badge; ?>"><?= _ent($status); ?></span></td>
                           <td class="labs-cell-muted"><?= _ent($transaksi->expired_datetime); ?></td>
                           <td class="labs-cell-muted"><?= _ent($transaksi->created_at); ?></td>
                           <td class="labs-cell-muted"><?= _ent($transaksi->updated_at); ?></td>
                           <td style="white-space:nowrap">
                              <div class="labs-row-actions" style="justify-content:center">
                                 <?php is_allowed('transaksi_view', function() use ($transaksi){?>
                                 <a href="<?= site_url('administrator/transaksi/view/' . $transaksi->id_transaksi); ?>" class="labs-btn labs-btn--info labs-btn--sm" title="<?= cclang('view_button'); ?>"><i class="fa fa-newspaper-o"></i></a>
                                 <?php }) ?>
                                 <?php is_allowed('transaksi_update', function() use ($transaksi){?>
                                 <a href="<?= site_url('administrator/transaksi/edit/' . $transaksi->id_transaksi); ?>" class="labs-btn labs-btn--warning labs-btn--sm" title="<?= cclang('update_button'); ?>"><i class="fa fa-edit"></i></a>
                                 <?php }) ?>
                                 <?php is_allowed('transaksi_delete', function() use ($transaksi){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/transaksi/delete/' . $transaksi->id_transaksi); ?>" class="labs-btn labs-btn--danger labs-btn--sm remove-data" title="<?= cclang('remove_button'); ?>"><i class="fa fa-trash"></i></a>
                                 <?php }) ?>
                                 <a href="<?= site_url('administrator/transaksi/reactivate_va/' . $transaksi->id_transaksi); ?>" class="labs-btn labs-btn--default labs-btn--sm" title="Reactivate VA"><i class="fa fa-ban"></i></a>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else : ?>
                        <tr>
                           <td colspan="17">
                              <div class="labs-empty">
                                 <i class="fa fa-inbox"></i>
                                 <?php if(!empty($this->input->get('q'))): ?>
                                    <p>Data transaksi tidak ditemukan untuk pencarian "<strong><?= htmlspecialchars($this->input->get('q')); ?></strong>"</p>
                                 <?php else : ?>
                                    <p>Data transaksi tidak tersedia</p>
                                 <?php endif; ?>
                              </div>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>

               <!-- Bulk Action & Pagination -->
               <div class="labs-flex-between labs-mt-4">
                  <div class="labs-flex labs-flex-gap-2" style="align-items:center">
                     <select class="form-control" name="bulk" id="bulk" style="width:200px;height:34px;font-size:12px">
                        <option value="">-- Bulk Action --</option>
                        <option value="multi_reactivate_va">Reactivate VA</option>
                     </select>
                     <button type="button" class="labs-btn labs-btn--default" id="apply"><?= cclang('apply_button'); ?></button>
                  </div>
                  <div class="dataTables_paginate paging_simple_numbers">
                     <?= $pagination; ?>
                  </div>
               </div>
               </form>

            </div>
         </div>
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
      var serialize_bulk = $('#form_transaksi').serialize();

      if (bulk.val() == 'multi_reactivate_va') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "Activate this selected data?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/transaksi/multi_reactivate_va?' + serialize_bulk;
            }
          });

        return false;

      }
      else if(bulk.val() == '')  {
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
