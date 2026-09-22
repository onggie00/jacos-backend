<!-- labs-ui assets -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-toggle.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-counter.js"></script>
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<style>
@media (min-width: 992px) {
   .col-md-2-4 { width: 20%; float: left; padding-left: 15px; padding-right: 15px; }
}
@media (max-width: 991px) {
   .col-md-2-4 { width: 50%; float: left; padding-left: 15px; padding-right: 15px; }
}
.labs-file-preview { cursor: pointer; transition: var(--labs-transition-fast); }
.labs-file-preview:hover { transform: scale(1.05); }
.labs-heic-notice { background: var(--labs-warning-soft); border: 1px solid var(--labs-warning); border-radius: var(--labs-radius); padding: 12px; text-align: center; }
</style>

<section class="content-header">
   <h1>
      Anggota Ekskul SMP
      <small class="labs-text-muted">Manajemen Data</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Anggota Ekskul SMP</li>
   </ol>
</section>

<section class="content">

   <?php if ($this->session->flashdata('success')): ?>
      <script>toastr.success("<?= $this->session->flashdata('success'); ?>");</script>
   <?php elseif ($this->session->flashdata('error')): ?>
      <script>toastr.error("<?= $this->session->flashdata('error'); ?>");</script>
   <?php endif; ?>

   <!-- STAT CARDS -->
   <div class="row">
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--primary">
               <i class="fa fa-users"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Total Anggota</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->total); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--success">
               <i class="fa fa-check-circle"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Aktif</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->aktif); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--danger">
               <i class="fa fa-times-circle"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Tidak Aktif</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->tidak_aktif); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--info">
               <i class="fa fa-graduation-cap"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Ekskul Aktif</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->ekskul_count); ?>">0</div>
            </div>
         </div>
      </div>
      <div class="col-md-2-4 col-sm-6">
         <div class="labs-stat-card">
            <div class="labs-stat-card__icon labs-stat-card__icon--warning">
               <i class="fa fa-money"></i>
            </div>
            <div class="labs-stat-card__body">
               <div class="labs-stat-card__label">Sudah Bayar</div>
               <div class="labs-stat-card__value" data-counter data-target="<?= intval($stats->sudah_bayar); ?>">0</div>
            </div>
         </div>
      </div>
   </div>

   <!-- MAIN LIST -->
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">

            <div class="labs-card__header">
               <h3 class="labs-card__title">
                  <i class="fa fa-list-alt"></i>
                  Daftar Anggota
                  <span class="labs-badge labs-badge--default labs-ml-2"><?= $ekskul_member_smp_counts; ?> data</span>
               </h3>
               <div>
                  <?php is_allowed('ekskul_member_smp_add', function(){ ?>
                  <a class="labs-btn labs-btn--success labs-btn--sm" href="<?= site_url('administrator/ekskul_member_smp/add'); ?>" title="Tambah Anggota (Ctrl+a)">
                     <i class="fa fa-plus-square-o"></i> Tambah
                  </a>
                  <?php }) ?>
               </div>
            </div>

            <div class="labs-card__body">

               <!-- INLINE FILTER -->
               <form action="<?= base_url('administrator/ekskul_member_smp/filter'); ?>" method="GET" class="labs-filter-inline">
                  <select class="form-control" name="id_ekskul[]" title="Ekstrakurikuler" id="filterEkskul">
                     <option value="">Semua Ekskul</option>
                     <?php
                        $list_ekskul = $this->mymodel->withquery("select id_ekskul, nama from ekskul where jenjang = 'smp' order by nama ASC", "result");
                        $selected_ekskul = isset($_GET['id_ekskul']) ? array_filter((array)$_GET['id_ekskul']) : array();
                        foreach ($list_ekskul as $e) {
                            $sel = in_array($e->id_ekskul, $selected_ekskul) ? 'selected' : '';
                            echo '<option value="' . $e->id_ekskul . '" ' . $sel . '>' . $e->nama . '</option>';
                        }
                     ?>
                  </select>

                  <select class="form-control" name="id_tingkatan" title="Tingkatan" id="filterTingkatan">
                     <option value="">Semua Tingkatan</option>
                     <?php
                        $list_tingkatan = $this->mymodel->withquery("select id_tingkatan_smp, label from tingkatan_smp order by label ASC", "result");
                        $selected_tingkatan = isset($_GET['id_tingkatan']) ? $_GET['id_tingkatan'] : '';
                        foreach ($list_tingkatan as $t) {
                            $sel = ($t->id_tingkatan_smp == $selected_tingkatan) ? 'selected' : '';
                            echo '<option value="' . $t->id_tingkatan_smp . '" ' . $sel . '>' . $t->label . '</option>';
                        }
                     ?>
                  </select>

                  <select class="form-control" name="id_kelas" title="Kelas" id="filterKelas">
                     <option value="">Semua Kelas</option>
                     <?php
                        $list_kelas = $this->mymodel->withquery("select id_kelas_smp, label, id_tingkatan from kelas_smp order by label ASC", "result");
                        $selected_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : '';
                        foreach ($list_kelas as $k) {
                            $sel = ($k->id_kelas_smp == $selected_kelas) ? 'selected' : '';
                            echo '<option value="' . $k->id_kelas_smp . '" data-tingkatan="' . $k->id_tingkatan . '" ' . $sel . '>' . $k->label . '</option>';
                        }
                     ?>
                  </select>

                  <select class="form-control" name="status_member" title="Status">
                     <option value="">Semua Status</option>
                     <option value="1" <?= (isset($_GET['status_member']) && $_GET['status_member'] === '1') ? 'selected' : ''; ?>>Aktif</option>
                     <option value="0" <?= (isset($_GET['status_member']) && $_GET['status_member'] === '0') ? 'selected' : ''; ?>>Tidak Aktif</option>
                  </select>

                  <select class="form-control" name="file_pembayaran" title="File Pembayaran">
                     <option value="">Semua File Pembayaran</option>
                     <option value="sudah_upload" <?= (isset($_GET['file_pembayaran']) && $_GET['file_pembayaran'] === 'sudah_upload') ? 'selected' : ''; ?>>Sudah Upload</option>
                     <option value="belum_upload" <?= (isset($_GET['file_pembayaran']) && $_GET['file_pembayaran'] === 'belum_upload') ? 'selected' : ''; ?>>Belum Upload</option>
                  </select>

                  <select class="form-control" name="tahun_ajaran" title="Tahun Ajaran">
                     <option value="">Semua Tahun</option>
                     <?php
                        $list_ta = $this->mymodel->withquery("select distinct label from tahun_ajaran order by tanggal_mulai DESC", "result");
                        $selected_ta = isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '';
                        foreach ($list_ta as $t) {
                            $sel = ($t->label == $selected_ta) ? 'selected' : '';
                            echo '<option value="' . $t->label . '" ' . $sel . '>' . $t->label . '</option>';
                        }
                     ?>
                  </select>

                  <select class="form-control" name="semester" title="Semester">
                     <option value="">Semua Semester</option>
                     <option value="1" <?= (isset($_GET['semester']) && $_GET['semester'] === '1') ? 'selected' : ''; ?>>Ganjil</option>
                     <option value="2" <?= (isset($_GET['semester']) && $_GET['semester'] === '2') ? 'selected' : ''; ?>>Genap</option>
                  </select>

                  <div class="labs-filter-inline__search">
                     <i class="fa fa-search labs-filter-inline__search-icon"></i>
                     <input type="text" class="form-control" name="nama_lengkap" placeholder="Cari nama siswa..." value="<?= htmlspecialchars(isset($_GET['nama_lengkap']) ? $_GET['nama_lengkap'] : '', ENT_QUOTES); ?>">
                  </div>

                  <button type="submit" class="labs-btn labs-btn--primary labs-btn--sm"><i class="fa fa-filter"></i></button>
                  <a href="<?= base_url('administrator/ekskul_member_smp'); ?>" class="labs-btn labs-btn--default labs-btn--sm" title="Reset"><i class="fa fa-undo"></i></a>
               </form>

               <!-- EXPORT BUTTONS (only when filter active) -->
               <?php
                  $has_filter = !empty($_GET['id_ekskul']) || !empty($_GET['tahun_ajaran']) || !empty($_GET['semester']) || !empty($_GET['id_kelas']) || !empty($_GET['id_tingkatan']) || !empty($_GET['status_member']) || !empty($_GET['file_pembayaran']) || !empty($_GET['nama_lengkap']);
                  $filter_param = '';
                  if ($has_filter) {
                      $parts = array();
                      if (!empty($_GET['id_ekskul'])) {
                          foreach ((array)$_GET['id_ekskul'] as $eid) {
                              if ($eid !== '') $parts[] = 'id_ekskul[]=' . $eid;
                          }
                      }
                      if (!empty($_GET['tahun_ajaran'])) $parts[] = 'tahun_ajaran=' . urlencode($_GET['tahun_ajaran']);
                      if (!empty($_GET['semester'])) $parts[] = 'semester=' . $_GET['semester'];
                      if (!empty($_GET['id_kelas'])) $parts[] = 'id_kelas=' . $_GET['id_kelas'];
                      if (!empty($_GET['id_tingkatan'])) $parts[] = 'id_tingkatan=' . $_GET['id_tingkatan'];
                      if (isset($_GET['status_member']) && $_GET['status_member'] !== '') $parts[] = 'status_member=' . $_GET['status_member'];
                      if (isset($_GET['file_pembayaran']) && $_GET['file_pembayaran'] !== '') $parts[] = 'file_pembayaran=' . $_GET['file_pembayaran'];
                      if (!empty($_GET['nama_lengkap'])) $parts[] = 'nama_lengkap=' . urlencode($_GET['nama_lengkap']);
                      if ($parts) $filter_param = '?' . implode('&', $parts);
                  }
               ?>
               <?php if ($has_filter): ?>
               <div class="labs-filter-active labs-mb-3">
                  <span class="labs-text-muted labs-fs-sm">Filter aktif:</span>
                  <?php
                     $active_filters = array();
                     if (!empty($_GET['id_ekskul'])) {
                         $active_ekskul = array();
                         foreach ((array) $_GET['id_ekskul'] as $eid) {
                             foreach ($list_ekskul as $e) if ((string) $e->id_ekskul === (string) $eid) $active_ekskul[] = $e->nama;
                         }
                         if ($active_ekskul) $active_filters[] = 'Ekskul: ' . implode(', ', $active_ekskul);
                     }
                     if (!empty($_GET['id_tingkatan'])) {
                         foreach ($list_tingkatan as $t) if ((string) $t->id_tingkatan_smp === (string) $_GET['id_tingkatan']) $active_filters[] = 'Tingkatan: ' . $t->label;
                     }
                     if (!empty($_GET['id_kelas'])) {
                         foreach ($list_kelas as $k) if ((string) $k->id_kelas_smp === (string) $_GET['id_kelas']) $active_filters[] = 'Kelas: ' . $k->label;
                     }
                     if (isset($_GET['status_member']) && $_GET['status_member'] !== '') $active_filters[] = 'Status: ' . ($_GET['status_member'] === '1' ? 'Aktif' : 'Tidak Aktif');
                     if (isset($_GET['file_pembayaran']) && $_GET['file_pembayaran'] !== '') $active_filters[] = 'File Pembayaran: ' . ($_GET['file_pembayaran'] === 'sudah_upload' ? 'Sudah Upload' : 'Belum Upload');
                     if (!empty($_GET['tahun_ajaran'])) $active_filters[] = 'Tahun: ' . $_GET['tahun_ajaran'];
                     if (!empty($_GET['semester'])) $active_filters[] = 'Semester: ' . ($_GET['semester'] === '1' ? 'Ganjil' : 'Genap');
                     if (!empty($_GET['nama_lengkap'])) $active_filters[] = 'Nama: ' . $_GET['nama_lengkap'];
                     foreach ($active_filters as $active_filter):
                  ?>
                     <span class="labs-badge labs-badge--orange"><?= htmlspecialchars($active_filter, ENT_QUOTES); ?></span>
                  <?php endforeach; ?>
               </div>
               <div style="margin-bottom:12px;display:flex;gap:8px;flex-wrap:wrap">
                  <?php is_allowed('ekskul_member_smp_export', function() use ($filter_param) { ?>
                  <a class="labs-btn labs-btn--success labs-btn--sm" href="<?= site_url('administrator/ekskul_member_smp/export') . $filter_param; ?>">
                     <i class="fa fa-file-excel-o"></i> Export Pilihan Siswa
                  </a>
                  <a class="labs-btn labs-btn--warning labs-btn--sm" href="<?= site_url('administrator/ekskul_member_smp/export_list_anggota') . $filter_param; ?>">
                     <i class="fa fa-file-excel-o"></i> Export List Anggota
                  </a>
                  <a class="labs-btn labs-btn--info labs-btn--sm" href="<?= site_url('administrator/ekskul_member_smp/export_rekap_pembayaran') . $filter_param; ?>">
                     <i class="fa fa-file-excel-o"></i> Export Rekap Pembayaran
                  </a>
                  <!-- EXPORT TANDA TANGAN (modal, selalu tampil) -->
                     <?php is_allowed('ekskul_member_smp_export', function() { ?>
                     <a class="labs-btn labs-btn--primary labs-btn--sm" href="javascript:void(0);" data-toggle="modal" data-target="#modal_export_ttd">
                        <i class="fa fa-file-excel-o"></i> Export Form Kehadiran
                     </a>
                     <?php }) ?>
                  <?php }) ?>
               </div>
               <?php endif; ?>

               <!-- TABLE -->
               <form name="form_ekskul_member_smp" id="form_ekskul_member_smp">
               <div class="labs-table-wrap">
                  <div class="labs-table-scroll">
                  <table class="labs-table">
                     <thead>
                        <tr>
                           <th style="width:30px"><input type="checkbox" class="flat-red" id="check_all" title="Pilih semua"></th>
                           <th class="sortable" data-sort-col="1">Ekstrakurikuler <i class="fa fa-sort"></i></th>
                           <th class="sortable" data-sort-col="2">Nama Siswa <i class="fa fa-sort"></i></th>
                           <th>NIS</th>
                           <th class="sortable" data-sort-col="4">Kelas <i class="fa fa-sort"></i></th>
                           <th class="labs-cell-numeric">Biaya</th>
                           <th>File Bayar</th>
                           <th class="sortable" data-sort-col="7">Status <i class="fa fa-sort"></i></th>
                           <th>Tahun / Semester</th>
                           <th class="labs-cell-numeric">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php foreach($data_list as $row): ?>
                        <tr>
                           <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= $row->id_member; ?>"></td>
                           <td>
                              <div class="labs-fw-semi"><?= _ent($row->nama_ekskul); ?></div>
                           </td>
                           <td>
                              <div class="labs-name-cell">
                                 <span class="labs-avatar"><?= strtoupper(substr(trim($row->nama_lengkap), 0, 2)); ?></span>
                                 <div>
                                    <div class="labs-fw-semi"><?= _ent($row->nama_lengkap); ?></div>
                                 </div>
                              </div>
                           </td>
                           <td><?= _ent($row->nis); ?></td>
                           <td><span class="labs-badge labs-badge--default"><?= _ent($row->nama_kelas); ?></span></td>
                           <td class="labs-cell-numeric"><?= formatIDR($row->biaya); ?></td>
                           <td>
                              <?php if (!empty($row->file_pembayaran)): ?>
                                 <?php
                                    $file_url = BASE_URL . 'uploads/ekskul_member_smp/' . $row->file_pembayaran;
                                    $ext = strtolower(pathinfo($row->file_pembayaran, PATHINFO_EXTENSION));
                                    $is_heic = in_array($ext, array('heic', 'heif'));
                                 ?>
                                 <button type="button" class="labs-btn labs-btn--sm labs-btn--primary labs-file-preview btn-view-file"
                                    data-url="<?= $file_url; ?>"
                                    data-ext="<?= $ext; ?>"
                                    data-name="<?= _ent($row->nama_lengkap); ?>"
                                    title="Lihat File">
                                    <i class="fa fa-<?= $is_heic ? 'image' : 'eye'; ?>"></i> Lihat
                                 </button>
                              <?php else: ?>
                                 <span class="labs-cell-muted">-</span>
                              <?php endif; ?>
                           </td>
                           <td>
                              <?php if ($row->status_member == 1): ?>
                                 <span class="labs-badge labs-badge--success"><span class="dot"></span> Aktif</span>
                              <?php else: ?>
                                 <span class="labs-badge labs-badge--danger"><span class="dot"></span> Tidak Aktif</span>
                              <?php endif; ?>
                           </td>
                           <td>
                              <div class="labs-fs-sm"><?= _ent($row->tahun_ajaran); ?></div>
                              <div class="labs-fs-xs labs-text-muted">Semester <?= ($row->semester == 1) ? 'Ganjil' : (($row->semester == 2) ? 'Genap' : '-'); ?></div>
                           </td>
                           <td class="labs-cell-numeric">
                              <?php is_allowed('ekskul_member_smp_update', function() use ($row){ ?>
                              <a href="<?= site_url('administrator/ekskul_member_smp/edit/' . $row->id_member); ?>" class="labs-btn labs-btn--warning labs-btn--sm" title="Edit">
                                 <i class="fa fa-edit"></i>
                              </a>
                              <?php }) ?>
                              <?php is_allowed('ekskul_member_smp_delete', function() use ($row){ ?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/ekskul_member_smp/delete/' . $row->id_member); ?>" class="labs-btn labs-btn--danger labs-btn--sm remove-data" title="Hapus">
                                 <i class="fa fa-close"></i>
                              </a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if ($value_counts == 0): ?>
                        <tr class="labs-empty-row">
                           <td colspan="10">
                              <div class="labs-empty">
                                 <i class="fa fa-inbox"></i>
                                 <p>Belum ada data anggota ekstrakurikuler</p>
                              </div>
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>
               </form>

               <!-- BULK + PAGINATION -->
               <div class="labs-flex-between labs-mt-4">
                  <div style="display:flex;gap:8px;align-items:center">
                     <select class="form-control" name="bulk" id="bulk" style="width:140px">
                        <option value="">Bulk Action</option>
                        <option value="delete">Delete</option>
                        <option value="aktif">Aktifkan</option>
                        <option value="tdk_aktif">Nonaktifkan</option>
                     </select>
                     <button type="button" class="labs-btn labs-btn--default labs-btn--sm" id="apply">Apply</button>
                  </div>
                  <div>
                     <div class="labs-text-muted labs-fs-sm labs-mb-2" style="text-align:right">
                        Total: <strong><?= $value_counts; ?></strong> data
                     </div>
                     <?= $pagination; ?>
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL EXPORT TANDA TANGAN -->
<div class="modal fade labs-modal" id="modal_export_ttd" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-file-excel-o"></i> Export Tanda Tangan / Form Kehadiran</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <form id="form_export_ttd" onsubmit="return false;">
            <div class="modal-body">
               <div class="form-group">
                  <label>Ekstrakurikuler <span class="text-danger">*</span></label>
                  <select id="ttd_ekskul" class="form-control chosen chosen-select" multiple="multiple" data-placeholder="Cari & pilih ekstrakurikuler..." style="width:100%;">
                     <?php foreach ($list_ekskul as $e) { ?>
                        <option value="<?= $e->id_ekskul; ?>"><?= htmlspecialchars($e->nama, ENT_QUOTES); ?></option>
                     <?php } ?>
                  </select>
               </div>
               <div class="row">
                  <div class="col-xs-6">
                     <div class="form-group">
                        <label>Bulan</label>
                        <select id="ttd_bulan" class="form-control">
                           <?php for ($i = 1; $i <= 12; $i++) { ?>
                              <option value="<?= $i; ?>" <?= date('n') == $i ? 'selected' : ''; ?>><?= formatBulan(date('Y-m-d', strtotime(date('Y') . '-' . $i . '-1'))); ?></option>
                           <?php } ?>
                        </select>
                     </div>
                  </div>
                  <div class="col-xs-6">
                     <div class="form-group">
                        <label>Tahun</label>
                        <select id="ttd_tahun" class="form-control">
                           <?php $thn_sekarang = date('Y'); for ($i = $thn_sekarang - 25; $i <= $thn_sekarang + 25; $i++) { ?>
                              <option value="<?= $i; ?>" <?= $i == $thn_sekarang ? 'selected' : ''; ?>><?= $i; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="labs-btn labs-btn--default labs-btn--sm" data-dismiss="modal">Batal</button>
               <button type="button" class="labs-btn labs-btn--success labs-btn--sm" id="btn_export_ttd_submit">Export</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- END MODAL EXPORT TANDA TANGAN -->

<!-- MODAL FILE PREVIEW -->
<div class="modal fade labs-modal" id="modalFilePreview" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-file-image-o"></i> File Pembayaran</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body" id="filePreviewContent" style="text-align:center;min-height:200px">
            <div class="labs-text-center" style="padding:40px 0">
               <i class="fa fa-spinner fa-spin fa-3x labs-text-muted"></i>
            </div>
         </div>
         <div class="modal-footer">
            <button class="labs-btn labs-btn--default" data-dismiss="modal">
               <i class="fa fa-times"></i> Tutup
            </button>
            <a href="#" class="labs-btn labs-btn--primary" id="btnDownloadFile" target="_blank" download>
               <i class="fa fa-download"></i> Download
            </a>
         </div>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){

   // ============ TINGKATAN → KELAS CASCADE ============
   var $tingkatan = $('#filterTingkatan');
   var $kelas = $('#filterKelas');
   var all_kelas_options = $kelas.find('option').clone();

   $tingkatan.on('change', function(){
      var tid = $(this).val();
      $kelas.empty().append(all_kelas_options.filter('[value=""]').clone());
      if (tid === '') {
         $kelas.append(all_kelas_options.filter('[data-tingkatan]').clone());
      } else {
         $kelas.append(all_kelas_options.filter('[data-tingkatan="' + tid + '"]').clone());
      }
      $kelas.val('');
   });
   // trigger on page load if tingkatan already selected
   if ($tingkatan.val()) $tingkatan.trigger('change');

   // ============ FILE PREVIEW MODAL ============
   $('.btn-view-file').click(function(){
      var url = $(this).data('url');
      var ext = $(this).data('ext');
      var name = $(this).data('name');
      var content = $('#filePreviewContent');

      $('#modalFilePreview .modal-title').html('<i class="fa fa-file-image-o"></i> File Pembayaran — ' + name);
      $('#btnDownloadFile').attr('href', url);
      content.html('<div class="labs-text-center" style="padding:40px 0"><i class="fa fa-spinner fa-spin fa-3x labs-text-muted"></i></div>');
      $('#modalFilePreview').modal('show');

      if (ext === 'pdf') {
         content.html('<iframe src="' + url + '" style="width:100%;height:500px;border:none;border-radius:8px"></iframe>');
      } else if (ext === 'heic' || ext === 'heif') {
         // HEIC: try Safari native first, then heic2any WASM fallback
         content.html('<div class="labs-text-center" style="padding:20px 0"><i class="fa fa-cog fa-spin fa-2x labs-text-muted"></i><p class="labs-text-muted labs-mt-2">Mengkonversi HEIC...</p></div>');
         tryHeicPreview(url, content);
      } else if (['jpg','jpeg','png','gif','webp','bmp'].indexOf(ext) !== -1) {
         content.html('<img src="' + url + '" style="max-width:100%;max-height:500px;border-radius:8px;box-shadow:var(--labs-shadow)" alt="File Pembayaran">');
      } else {
         content.html('<div class="labs-empty"><i class="fa fa-file-o" style="font-size:48px"></i><p>File: ' + ext.toUpperCase() + '</p><p class="labs-text-muted">Preview tidak tersedia untuk tipe file ini</p></div>');
      }
   });

   function showHeicFallback(container, msg) {
      container.html('<div class="labs-heic-notice"><i class="fa fa-exclamation-triangle" style="font-size:32px;color:var(--labs-warning);display:block;margin-bottom:12px"></i><p style="font-weight:600;margin:0 0 8px 0">' + (msg || 'Gagal menampilkan HEIC') + '</p><p class="labs-text-muted" style="margin:0;font-size:13px">Silakan download file untuk melihat di aplikasi lain.</p></div>');
   }

   function tryHeicPreview(url, container) {
      // 1) Safari/iOS can display HEIC natively via <img> tag
      var testImg = new Image();
      testImg.onload = function() {
         container.html('<img src="' + url + '" style="max-width:100%;max-height:500px;border-radius:8px;box-shadow:var(--labs-shadow)" alt="File Pembayaran">');
      };
      testImg.onerror = function() {
         // 2) Server-side conversion via Imagick
         convertHeicServer(url, container);
      };
      testImg.src = url;
   }

   function convertHeicServer(url, container) {
      var filename = url.split('/').pop();
      var convertUrl = '<?= site_url('administrator/ekskul_member_smp/convert_heic'); ?>?file=' + encodeURIComponent(filename);
      $.getJSON(convertUrl)
         .done(function(res) {
            if (res.success && res.url) {
               container.html('<img src="' + res.url + '" style="max-width:100%;max-height:500px;border-radius:8px;box-shadow:var(--labs-shadow)" alt="File Pembayaran">');
            } else {
               showHeicFallback(container, res.error || 'Gagal mengkonversi HEIC');
            }
         })
         .fail(function(xhr, status, err) {
            showHeicFallback(container, 'Gagal mengkonversi HEIC [' + status + '] ' + (err || '') + ' — Cek console (F12) untuk detail');
            console.error('HEIC convert error:', status, err, xhr.responseText);
         });
   }

   // ============ TABLE SORT ============
   $('.sortable').css('cursor', 'pointer').click(function(){
      var $header = $(this);
      var col = parseInt($header.data('sort-col'), 10);
      var ascending = !$header.data('ascending');
      var $tbody = $('.labs-table tbody');
      var rows = $tbody.find('tr').get().filter(function(row){ return !$(row).hasClass('labs-empty-row'); });
      rows.sort(function(a, b){
         var av = $.trim($(a).children('td').eq(col).text()).toLowerCase();
         var bv = $.trim($(b).children('td').eq(col).text()).toLowerCase();
         return (av < bv ? -1 : (av > bv ? 1 : 0)) * (ascending ? 1 : -1);
      });
      $.each(rows, function(_, row){ $tbody.append(row); });
      $('.sortable').data('ascending', false).find('i').attr('class', 'fa fa-sort');
      $header.data('ascending', ascending).find('i').attr('class', ascending ? 'fa fa-sort-asc' : 'fa fa-sort-desc');
   });

   // ============ REMOVE ============
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

   // ============ BULK ACTIONS ============
   $('#apply').click(function(){
      var bulk = $('#bulk');
      var serialize_bulk = $('#form_ekskul_member_smp').serialize();

      if (bulk.val() == '') {
         swal({ title: "Upss", text: "<?= cclang('please_choose_bulk_action_first'); ?>", type: "warning", confirmButtonText: "Okay!" });
         return false;
      }

      if (serialize_bulk.indexOf('id%5B%5D') === -1 && serialize_bulk.indexOf('id[]=') === -1) {
         swal({ title: "Upss", text: "Pilih minimal satu data terlebih dahulu", type: "warning", confirmButtonText: "Okay!" });
         return false;
      }

      var urls = {
         'delete': 'administrator/ekskul_member_smp/delete?',
         'aktif': 'administrator/ekskul_member_smp/activate_member?',
         'tdk_aktif': 'administrator/ekskul_member_smp/deactivate_member?'
      };
      var labels = {
         'delete': { title: "<?= cclang('are_you_sure'); ?>", text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>", confirm: "<?= cclang('yes_delete_it'); ?>" },
         'aktif': { title: "Aktifkan anggota?", text: "Semua anggota terpilih akan diaktifkan", confirm: "Ya, Aktifkan" },
         'tdk_aktif': { title: "Nonaktifkan anggota?", text: "Semua anggota terpilih akan dinonaktifkan", confirm: "Ya, Nonaktifkan" }
      };

      var lbl = labels[bulk.val()];
      swal({
         title: lbl.title,
         text: lbl.text,
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: lbl.confirm,
         cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
         closeOnConfirm: true,
         closeOnCancel: true
      },
      function(isConfirm){
         if (isConfirm) {
            document.location.href = BASE_URL + '/' + urls[bulk.val()] + serialize_bulk;
         }
      });
      return false;
   });

   // ============ EXPORT TANDA TANGAN ============
   $('#btn_export_ttd_submit').click(function(){
      var ekskul = $('#ttd_ekskul').val() || [];
      if (ekskul.length === 0) {
         swal({ title: "Upss", text: "Silahkan pilih ekskul terlebih dahulu", type: "warning", confirmButtonText: "Okay!" });
         return false;
      }
      var bulan = $('#ttd_bulan').val();
      var tahun = $('#ttd_tahun').val();
      var params = ekskul.map(function(id){ return 'id_ekskul[]=' + encodeURIComponent(id); });
      params.push('bulan=' + bulan, 'tahun=' + tahun);
      $('#modal_export_ttd').modal('hide');
      window.location.href = '<?= site_url('administrator/ekskul_member_smp/export_tanda_tangan'); ?>?' + params.join('&');
      return false;
   });

   // ============ CHECK ALL ============
   var checkAll = $('#check_all');
   var checkboxes = $('input.check');
   checkAll.on('ifChecked ifUnchecked', function(event) {
      if (event.type == 'ifChecked') { checkboxes.iCheck('check'); }
      else { checkboxes.iCheck('uncheck'); }
   });
   checkboxes.on('ifChanged', function(event){
      if(checkboxes.filter(':checked').length == checkboxes.length) {
         checkAll.prop('checked', 'checked');
      } else {
         checkAll.removeProp('checked');
      }
      checkAll.iCheck('update');
   });


});
</script>
