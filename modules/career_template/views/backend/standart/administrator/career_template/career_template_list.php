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
         <div class="box box-warning">
            <div class="box-body">
               <div class="box box-widget widget-user-2">
                  <div class="widget-user-header">
                     <div class="row pull-right">
                        <?php is_allowed('career_template_add', function(){ ?>
                        <a class="btn btn-flat btn-success btn_add_new" href="<?= site_url('administrator/career_template/add'); ?>"><i class="fa fa-plus-square-o"></i> Tambah</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="Daftar">
                     </div>
                     <h3 class="widget-user-username">Master Persyaratan</h3>
                     <h5 class="widget-user-desc">Template persyaratan lowongan kerja <i class="label bg-yellow"><?= $template_counts; ?> item</i></h5>
                  </div>

                  <form name="form_career_template" id="form_career_template" action="<?= base_url('administrator/career_template/index'); ?>" method="GET">
                     <div class="row">
                        <div class="col-md-5">
                           <input type="text" class="form-control" name="q" placeholder="Cari nama persyaratan..." value="<?= html_escape($q); ?>">
                        </div>
                        <div class="col-md-3">
                           <select name="status" class="form-control">
                              <option value="">Semua status</option>
                              <option value="active"   <?= $status === 'active' ? 'selected' : ''; ?>>Aktif</option>
                              <option value="inactive" <?= $status === 'inactive' ? 'selected' : ''; ?>>Nonaktif</option>
                           </select>
                        </div>
                        <div class="col-md-2">
                           <button class="btn btn-flat btn-primary" type="submit"><i class="fa fa-search"></i> Cari</button>
                        </div>
                        <div class="col-md-2">
                           <a class="btn btn-flat btn-default" href="<?= site_url('administrator/career_template'); ?>">Reset</a>
                        </div>
                     </div>
                  </form>

                  <div class="table-responsive" style="margin-top:10px">
                  <table class="table table-bordered table-striped dataTable">
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
                           <td><?php $this->load->helper('text'); ?><?= html_escape(character_limiter((string) $t->description, 60)); ?></td>
                           <td>
                              <?php if ((int) $t->is_active === 1): ?>
                                 <span class="label label-success">Aktif</span>
                              <?php else: ?>
                                 <span class="label label-default">Nonaktif</span>
                              <?php endif; ?>
                           </td>
                           <td><?php if ($this->model_career_template->used_by_postings($t->id_requirement_template)) { echo '<span class="label label-info">Ya</span>'; } else { echo '<span class="label label-default">Belum</span>'; } ?></td>
                           <td>
                              <?php is_allowed('career_template_update', function() use ($t){ ?>
                                 <a href="<?= site_url('administrator/career_template/edit/' . $t->id_requirement_template); ?>" class="label-default"><i class="fa fa-edit"></i> Ubah</a>
                              <?php }) ?>
                              <?php is_allowed('career_template_delete', function() use ($t){ ?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/career_template/delete/' . $t->id_requirement_template); ?>" class="label-default remove-data"><i class="fa fa-close"></i> Hapus</a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if ($template_counts == 0): ?>
                        <tr><td colspan="6" class="text-center">Belum ada template.</td></tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
                  <div style="padding:10px"><?= $pagination; ?></div>
               </div>
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
