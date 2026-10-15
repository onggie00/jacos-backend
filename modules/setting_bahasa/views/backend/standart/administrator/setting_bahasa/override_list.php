<section class="content-header">
   <h1>Override Terjemahan<small>Koreksi hasil terjemahan</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Override</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <ul class="nav nav-tabs">
            <li><a href="<?= site_url('administrator/setting_bahasa'); ?>">Bahasa User</a></li>
            <li class="active"><a href="<?= site_url('administrator/setting_bahasa/override'); ?>">Override</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/cache'); ?>">Cache</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/warmup'); ?>">Warm-up</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/statistik'); ?>">Statistik</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/status'); ?>">Status</a></li>
         </ul>
         <div class="box box-warning">
            <div class="box-body">
               <input type="hidden" name="translator_csrf_global" value="<?= html_escape($translator_csrf); ?>" id="translator_csrf_holder">
               <a class="btn btn-flat btn-success" style="margin-bottom:10px;" href="<?= site_url('administrator/setting_bahasa/override_add'); ?>"><i class="fa fa-plus-square-o"></i> Tambah Override</a>
<?php if ($rows === null): ?>
               <div class="alert alert-danger">Tabel translation_override tidak dapat dibaca. Hubungi administrator database.</div>
<?php else: ?>
               <form method="get" action="<?= site_url('administrator/setting_bahasa/override/index'); ?>" class="form-inline" style="margin-bottom:10px;">
                  <select name="kode" class="form-control input-sm">
                     <option value="">Semua bahasa</option>
                     <?php foreach ($bahasa_opt as $kode => $nama): ?>
                     <option value="<?= html_escape($kode); ?>" <?= ($filter['kode'] === $kode ? 'selected' : ''); ?>><?= html_escape($nama); ?></option>
                     <?php endforeach; ?>
                  </select>
                  <select name="aktif" class="form-control input-sm">
                     <option value="">Semua status</option>
                     <option value="1" <?= ($filter['aktif'] === '1' ? 'selected' : ''); ?>>Aktif</option>
                     <option value="0" <?= ($filter['aktif'] === '0' ? 'selected' : ''); ?>>Nonaktif</option>
                  </select>
                  <input type="text" name="q" class="form-control input-sm" placeholder="Cari teks..." value="<?= html_escape($filter['q']); ?>">
                  <button type="submit" class="btn btn-sm btn-primary">Filter</button>
               </form>
               <div class="table-responsive">
               <table class="table table-bordered table-striped">
                  <thead><tr><th>Bahasa</th><th>Teks asal</th><th>Teks hasil</th><th>Status</th><th>Diubah</th><?= isset($rows[0]->created_by) ? '<th>Oleh</th>' : ''; ?><th style="width:220px;">Aksi</th></tr></thead>
                  <tbody>
                  <?php if (empty($rows)): ?>
                     <tr><td colspan="6" class="text-muted">Tidak ada data.</td></tr>
                  <?php endif; ?>
                  <?php foreach ($rows as $row): ?>
                     <tr>
                        <td><?= html_escape($row->kode_bahasa); ?></td>
                        <td style="max-width:260px; word-break:break-word;"><?= html_escape($row->teks_asal); ?></td>
                        <td style="max-width:260px; word-break:break-word;"><?= html_escape($row->teks_hasil); ?></td>
                        <td><span class="label <?= $row->aktif ? 'bg-green' : 'bg-red'; ?>"><?= $row->aktif ? 'aktif' : 'nonaktif'; ?></span></td>
                        <td><?= html_escape($row->updated_at); ?></td>
                        <?php if (isset($rows[0]->created_by)): ?><td><?= html_escape($row->created_by . ($row->updated_by ? ' / ' . $row->updated_by : '')); ?></td><?php endif; ?>
                        <td>
                           <a class="btn btn-xs btn-default" href="<?= site_url('administrator/setting_bahasa/override_edit/' . (int) $row->id); ?>" title="Ubah">Ubah</a>
                           <form method="post" action="<?= site_url('administrator/setting_bahasa/override_toggle/' . (int) $row->id); ?>" style="display:inline;" onsubmit="return confirm('Ubah status override ini?');">
                              <input type="hidden" name="translator_csrf" value="<?= html_escape($translator_csrf); ?>">
                              <button class="btn btn-xs btn-warning" title="Aktif/Nonaktifkan"><?= $row->aktif ? 'Nonaktifkan' : 'Aktifkan'; ?></button>
                           </form>
                           <form method="post" action="<?= site_url('administrator/setting_bahasa/override_delete/' . (int) $row->id); ?>" style="display:inline;" onsubmit="return confirm('Hapus permanen override ini?');">
                              <input type="hidden" name="translator_csrf" value="<?= html_escape($translator_csrf); ?>">
                              <button class="btn btn-xs btn-danger" title="Hapus permanen">Hapus</button>
                           </form>
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
