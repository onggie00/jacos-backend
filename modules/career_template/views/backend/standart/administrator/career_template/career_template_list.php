<!-- Muat bundle desain labs-ui -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Master Persyaratan <small>daftar</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Master Persyaratan</li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">
            <div class="labs-card__header">
               <div class="labs-card__title">Master Persyaratan <small>template persyaratan lowongan kerja</small></div>
               <div class="labs-row-actions">
                  <span class="labs-badge labs-badge--default"><?= $template_counts; ?> item</span>
                  <?php is_allowed('career_template_add', function(){ ?>
                  <a class="labs-btn labs-btn--success labs-btn--sm btn_add_new" href="<?= site_url('administrator/career_template/add'); ?>"><i class="fa fa-plus-square-o"></i> Tambah</a>
                  <?php }) ?>
               </div>
            </div>
            <div class="labs-card__body">

               <form name="form_career_template" id="form_career_template" action="<?= base_url('administrator/career_template/index'); ?>" method="GET" class="labs-filter-inline">
                  <div class="labs-filter-inline__search">
                     <span class="labs-filter-inline__search-icon"><i class="fa fa-search"></i></span>
                     <input type="text" class="form-control" name="q" placeholder="Cari nama persyaratan..." value="<?= html_escape($q); ?>">
                  </div>
                  <select name="status" class="form-control">
                     <option value="">Semua status</option>
                     <option value="active"   <?= $status === 'active' ? 'selected' : ''; ?>>Aktif</option>
                     <option value="inactive" <?= $status === 'inactive' ? 'selected' : ''; ?>>Nonaktif</option>
                  </select>
                  <button class="labs-btn labs-btn--primary labs-btn--sm" type="submit"><i class="fa fa-search"></i> Cari</button>
                  <a class="labs-btn labs-btn--default labs-btn--sm" href="<?= site_url('administrator/career_template'); ?>">Reset</a>
               </form>

               <div class="labs-table-wrap">
                  <div class="labs-table-scroll">
                     <table class="labs-table">
                        <thead>
                           <tr>
                              <th width="60">ID</th>
                              <th>Nama</th>
                              <th width="16%">Deskripsi</th>
                              <th width="12%">Status</th>
                              <th width="130">Dipakai</th>
                              <th width="180">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="tbody_career_template">
                        <?php foreach ($templates as $t): ?>
                           <tr>
                              <td><?= (int) $t->id_requirement_template; ?></td>
                              <td><?= html_escape($t->name); ?></td>
                              <td class="labs-cell-muted"><?php $this->load->helper('text'); ?><?= html_escape(character_limiter((string) $t->description, 60)); ?></td>
                              <td>
                                 <?php if ((int) $t->is_active === 1): ?>
                                    <span class="labs-badge labs-badge--success"><span class="dot"></span>Aktif</span>
                                 <?php else: ?>
                                    <span class="labs-badge labs-badge--default">Nonaktif</span>
                                 <?php endif; ?>
                              </td>
                              <td><?php if ($this->model_career_template->used_by_postings($t->id_requirement_template)) { echo '<span class="labs-badge labs-badge--info"><span class="dot"></span>Ya</span>'; } else { echo '<span class="labs-badge labs-badge--default">Belum</span>'; } ?></td>
                              <td>
                                 <div class="labs-row-actions">
                                 <?php is_allowed('career_template_update', function() use ($t){ ?>
                                    <a href="<?= site_url('administrator/career_template/edit/' . $t->id_requirement_template); ?>" class="labs-btn labs-btn--default labs-btn--xs"><i class="fa fa-edit"></i> Ubah</a>
                                 <?php }) ?>
                                 <?php is_allowed('career_template_delete', function() use ($t){ ?>
                                    <a href="javascript:void(0);" data-href="<?= site_url('administrator/career_template/delete/' . $t->id_requirement_template); ?>" class="labs-btn labs-btn--danger labs-btn--xs remove-data"><i class="fa fa-close"></i> Hapus</a>
                                 <?php }) ?>
                                 </div>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        <?php if ($template_counts == 0): ?>
                           <tr class="labs-empty-row"><td colspan="6"><div class="labs-empty"><i class="fa fa-inbox"></i><p>Belum ada template.</p></div></td></tr>
                        <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
               </div>
               <div class="labs-mt-4"><?= $pagination; ?></div>

            </div>
         </div>
      </div>
   </div>
</section>

<div id="career_csrf_holder" style="display:none"><?= isset($csrf_field) ? $csrf_field : ''; ?></div>
<script>
// HIGH #2/#7: hapus via POST + CSRF, bukan GET link.
document.addEventListener('DOMContentLoaded', function () {
   document.querySelectorAll('.remove-data').forEach(function (btn) {
      btn.addEventListener('click', function (ev) {
         ev.preventDefault();
         if (!window.confirm('Hapus template ini? Penghapusan permanen untuk yang belum pernah dipakai.')) { return; }
         var tokEl = document.querySelector('#career_csrf_holder input[name=career_csrf]');
         var fd = new FormData();
         if (tokEl) { fd.append('career_csrf', tokEl.value); }
         btn.disabled = true;
         fetch(btn.getAttribute('data-href'), { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function (r) { return r.json().catch(function () { return {}; }); })
            .then(function (j) {
               window.alert(j.message || (j.success ? 'Berhasil.' : 'Gagal.'));
               window.location.reload();
            })
            .catch(function () { btn.disabled = false; window.alert('Gagal menghubungi server.'); });
      });
   });
});
</script>
