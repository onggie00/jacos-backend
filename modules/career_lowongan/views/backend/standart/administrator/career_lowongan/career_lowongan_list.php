<!-- Muat bundle desain labs-ui -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Lowongan Kerja <small>daftar</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Lowongan Kerja</li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">
            <div class="labs-card__header">
               <div class="labs-card__title">Lowongan Kerja <small>rekrutmen guru &amp; karyawan</small></div>
               <div class="labs-row-actions">
                  <span class="labs-badge labs-badge--default"><?= $lowongan_counts; ?> lowongan</span>
                  <?php is_allowed('career_lowongan_add', function(){ ?>
                  <a class="labs-btn labs-btn--success labs-btn--sm btn_add_new" href="<?= site_url('administrator/career_lowongan/add'); ?>"><i class="fa fa-plus-square-o"></i> Tambah</a>
                  <?php }) ?>
               </div>
            </div>
            <div class="labs-card__body">

               <form name="form_career_lowongan" id="form_career_lowongan" action="<?= base_url('administrator/career_lowongan/index'); ?>" method="GET" class="labs-filter-inline">
                  <div class="labs-filter-inline__search">
                     <span class="labs-filter-inline__search-icon"><i class="fa fa-search"></i></span>
                     <input type="text" class="form-control" name="q" placeholder="Cari judul lowongan..." value="<?= html_escape($q); ?>">
                  </div>
                  <select name="status" class="form-control">
                     <option value="">Semua status</option>
                     <option value="buka"  <?= $status === 'buka' ? 'selected' : ''; ?>>Terbuka</option>
                     <option value="draft" <?= $status === 'draft' ? 'selected' : ''; ?>>Draf</option>
                     <option value="tutup" <?= $status === 'tutup' ? 'selected' : ''; ?>>Tutup</option>
                  </select>
                  <button class="labs-btn labs-btn--primary labs-btn--sm" type="submit"><i class="fa fa-search"></i> Cari</button>
                  <a class="labs-btn labs-btn--default labs-btn--sm" href="<?= site_url('administrator/career_lowongan'); ?>">Reset</a>
               </form>

               <div class="labs-table-wrap">
                  <div class="labs-table-scroll">
                     <table class="labs-table">
                        <thead>
                           <tr>
                              <th width="55">ID</th>
                              <th>Judul</th>
                              <th width="15%">Pendidikan</th>
                              <th width="15%">Periode</th>
                              <th width="10%">Status</th>
                              <th width="10%">Lamaran</th>
                              <th width="160">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="tbody_career_lowongan">
                        <?php foreach ($lowongans as $l): ?>
                           <tr>
                              <td><?= (int) $l->id_job_posting; ?></td>
                              <td><?= html_escape($l->title); ?></td>
                              <td class="labs-cell-muted"><?= html_escape($l->education_level !== NULL ? $l->education_level : '-'); ?></td>
                              <td class="labs-cell-muted">
                                 <?= html_escape($l->opening_date !== NULL ? $l->opening_date : '?'); ?>
                                 s/d
                                 <?= html_escape($l->closing_date !== NULL ? $l->closing_date : '?'); ?>
                              </td>
                              <td>
                                 <?php if ($l->status_tampil === 'buka'): ?>
                                    <span class="labs-badge labs-badge--success"><span class="dot"></span>Terbuka</span>
                                 <?php elseif ($l->status_tampil === 'tutup'): ?>
                                    <span class="labs-badge labs-badge--danger"><span class="dot"></span>Tutup</span>
                                 <?php else: ?>
                                    <span class="labs-badge labs-badge--default">Draf</span>
                                 <?php endif; ?>
                                 <?php if ((int) $l->is_published === 1): ?><small class="text-muted">(terbit)</small><?php endif; ?>
                              </td>
                              <td><?php if ($l->punya_lamaran): ?><span class="labs-badge labs-badge--info"><span class="dot"></span>Ada</span><?php else: ?><span class="labs-badge labs-badge--default">Belum</span><?php endif; ?></td>
                              <td>
                                 <div class="labs-row-actions">
                                 <?php is_allowed('career_lowongan_view', function() use ($l){ ?>
                                    <a href="<?= site_url('administrator/career_lowongan/edit/' . $l->id_job_posting); ?>" class="labs-btn labs-btn--default labs-btn--xs"><i class="fa fa-newspaper-o"></i> Lihat</a>
                                 <?php }) ?>
                                 <?php is_allowed('career_lowongan_update', function() use ($l){ ?>
                                    <a href="<?= site_url('administrator/career_lowongan/edit/' . $l->id_job_posting); ?>" class="labs-btn labs-btn--default labs-btn--xs"><i class="fa fa-edit"></i> Ubah</a>
                                 <?php }) ?>
                                 <?php if (!$l->punya_lamaran){ is_allowed('career_lowongan_delete', function() use ($l){ ?>
                                    <a href="javascript:void(0);" data-href="<?= site_url('administrator/career_lowongan/delete/' . $l->id_job_posting); ?>" class="labs-btn labs-btn--danger labs-btn--xs remove-data"><i class="fa fa-close"></i> Hapus</a>
                                 <?php }); } ?>
                                 </div>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        <?php if ($lowongan_counts == 0): ?>
                           <tr class="labs-empty-row"><td colspan="7"><div class="labs-empty"><i class="fa fa-inbox"></i><p>Belum ada lowongan yang cocok.</p></div></td></tr>
                        <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
               </div>
               <div class="labs-mt-4"><?= $pagination; ?></div>
               <span style="display:none" id="csrf_holder"><?= $csrf_field; ?></span>

            </div>
         </div>
      </div>
   </div>
</section>
<script>
$(document).ready(function(){
   var CSRF = $('#csrf_holder input[name="career_csrf"]').val();

   // Hapus lowongan — konfirmasi + POST + CSRF (bukan GET).
   $('#tbody_career_lowongan').on('click', '.remove-data', function(){
      var url = $(this).data('href');
      if (!confirm('Hapus lowongan ini?')) { return false; }
      $.post(url, { career_csrf: CSRF },
         function(res){
            if (res.success) { window.location.href = BASE_URL + 'administrator/career_lowongan'; }
            else { alert(res.message || 'Gagal menghapus.'); }
         }, 'json').fail(function(){ alert('Ditolak (403).'); });
      return false;
   });
});
</script>
