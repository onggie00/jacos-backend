<section class="content-header">
   <h1>Cache Terjemahan<small>Hanya lihat; koreksi lewat Override</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Cache</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <ul class="nav nav-tabs">
            <li><a href="<?= site_url('administrator/setting_bahasa'); ?>">Bahasa User</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/override'); ?>">Override</a></li>
            <li class="active"><a href="<?= site_url('administrator/setting_bahasa/cache'); ?>">Cache</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/statistik'); ?>">Statistik</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/status'); ?>">Status</a></li>
         </ul>
         <div class="box box-warning">
            <div class="box-body">
<?php if ($rows === null): ?>
               <div class="alert alert-danger">Tabel translation_cache tidak dapat dibaca. Hubungi administrator database.</div>
<?php else: ?>
               <form method="get" action="<?= site_url('administrator/setting_bahasa/cache/index'); ?>" class="form-inline" style="margin-bottom:10px;">
                  <select name="kode" class="form-control input-sm">
                     <option value="">Semua bahasa</option>
                     <?php foreach ($bahasa_opt as $kode => $nama): ?>
                     <option value="<?= html_escape($kode); ?>" <?= ($filter['kode'] === $kode ? 'selected' : ''); ?>><?= html_escape($nama); ?></option>
                     <?php endforeach; ?>
                  </select>
                  <input type="text" name="q" class="form-control input-sm" placeholder="Cari teks (min. 3 karakter)..." value="<?= html_escape($filter['q']); ?>">
                  <select name="urut" class="form-control input-sm">
                     <option value="">Terbaru dibuat</option>
                     <option value="hit" <?= ($filter['urut'] === 'hit' ? 'selected' : ''); ?>>Terbanyak dipakai</option>
                     <option value="last_used" <?= ($filter['urut'] === 'last_used' ? 'selected' : ''); ?>>Terakhir dipakai</option>
                  </select>
                  <button type="submit" class="btn btn-sm btn-primary">Filter</button>
               </form>
               <div class="table-responsive">
               <table class="table table-bordered table-striped">
                  <thead><tr><th>Bahasa</th><th>Teks asal</th><th>Teks hasil</th><th>Hit</th><th>Terakhir dipakai</th><th>Dibuat</th><th>Aksi</th></tr></thead>
                  <tbody>
                  <?php if (empty($rows)): ?>
                     <tr><td colspan="7" class="text-muted">Tidak ada data.</td></tr>
                  <?php endif; ?>
                  <?php foreach ($rows as $row): ?>
                     <tr>
                        <td><?= html_escape($row->kode_bahasa); ?></td>
                        <td style="max-width:280px; word-break:break-word;"><?= html_escape(mb_substr($row->teks_asal, 0, 120)); ?><?= (strlen($row->teks_asal) > 120 ? '…' : ''); ?></td>
                        <td style="max-width:280px; word-break:break-word;"><?= html_escape(mb_substr($row->teks_hasil, 0, 120)); ?><?= (strlen($row->teks_hasil) > 120 ? '…' : ''); ?></td>
                        <td><?= (int) $row->hit_count; ?></td>
                        <td><?= html_escape($row->last_used_at); ?></td>
                        <td><?= html_escape($row->created_at); ?></td>
                        <td>
                           <a class="btn btn-xs btn-default" href="<?= site_url('administrator/setting_bahasa/cache_detail/' . (int) $row->id_translation_cache); ?>">Lihat</a>
                           <a class="btn btn-xs btn-warning" href="<?= site_url('administrator/setting_bahasa/cache_koreksi/' . (int) $row->id_translation_cache); ?>">Koreksi</a>
                        </td>
                     </tr>
                  <?php endforeach; ?>
                  </tbody>
               </table>
               </div>
               <?= $pagination; ?>
<?php endif; ?>
            </div>
         </div>
      </div>
   </div>
</section>
