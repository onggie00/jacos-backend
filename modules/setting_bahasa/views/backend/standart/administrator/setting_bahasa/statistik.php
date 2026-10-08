<section class="content-header">
   <h1>Statistik Terjemahan<small>Cache &amp; kuota DeepL</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Statistik</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <ul class="nav nav-tabs">
            <li><a href="<?= site_url('administrator/setting_bahasa'); ?>">Bahasa User</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/override'); ?>">Override</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/cache'); ?>">Cache</a></li>
            <li class="active"><a href="<?= site_url('administrator/setting_bahasa/statistik'); ?>">Statistik</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/status'); ?>">Status</a></li>
         </ul>
      </div>
   </div>
   <div class="row">
      <div class="col-md-6">
         <div class="box box-warning">
            <div class="box-header"><h3 class="box-title">Cache Terjemahan</h3></div>
            <div class="box-body">
<?php if ($info_cache === false || $per_kode === null): ?>
               <div class="alert alert-danger">Tabel translation_cache tidak dapat dibaca. Hubungi administrator database.</div>
<?php else: ?>
               <p>Perkiraan jumlah baris: <b><?= number_format((float) $info_cache->rows_est); ?></b>
                  &middot; Perkiraan ukuran tabel: <b><?= number_format((float) $info_cache->bytes / 1048576, 2); ?> MB</b></p>
               <table class="table table-bordered table-striped">
                  <thead><tr><th>Kode bahasa</th><th>Jumlah entri</th><th>Total hit</th></tr></thead>
                  <tbody>
                  <?php foreach ($per_kode as $pk): ?>
                     <tr><td><?= html_escape($pk->kode_bahasa); ?></td><td><?= number_format((float) $pk->jumlah); ?></td><td><?= number_format((float) $pk->total_hit); ?></td></tr>
                  <?php endforeach; ?>
                  </tbody>
               </table>
               <p>Entri tidak terpakai lebih dari 90 hari: <b><?= number_format((float) $tak_terpakai); ?></b></p>
<?php endif; ?>
            </div>
         </div>
         <div class="box box-warning">
            <div class="box-header"><h3 class="box-title">20 Entri Terpopuler</h3></div>
            <div class="box-body">
               <table class="table table-bordered table-striped">
                  <thead><tr><th>Bahasa</th><th>Teks asal</th><th>Hit</th></tr></thead>
                  <tbody>
                  <?php if (empty($populer)): ?><tr><td colspan="3" class="text-muted">Tidak ada data.</td></tr><?php endif; ?>
                  <?php foreach ((array) $populer as $p): ?>
                     <tr><td><?= html_escape($p->kode_bahasa); ?></td><td style="word-break:break-word;"><?= html_escape(mb_substr($p->teks_asal, 0, 100)); ?></td><td><?= (int) $p->hit_count; ?></td></tr>
                  <?php endforeach; ?>
                  </tbody>
               </table>
            </div>
         </div>
         <div class="box box-warning">
            <div class="box-header"><h3 class="box-title">Override</h3></div>
            <div class="box-body">
               <p>Aktif: <b><?= (int) $ov_aktif; ?></b> &middot; Nonaktif: <b><?= (int) $ov_nonaktif; ?></b></p>
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="box box-warning">
            <div class="box-header"><h3 class="box-title">Kuota DeepL (bulan berjalan)</h3></div>
            <div class="box-body">
<?php if ($usage === null): ?>
               <div class="alert alert-danger">Tabel translation_usage_bulanan tidak dapat dibaca. Hubungi administrator database.</div>
<?php else: ?>
               <?php $r_ini = null; foreach ($usage as $u) { if ($u->bulan === date('Y-m') && $u->penyedia === 'deepl') { $r_ini = $u; break; } } ?>
               <?php if ($r_ini): ?>
                  <p>Karakter terpakai: <b><?= number_format((float) $r_ini->jumlah_karakter); ?></b> dari limit <b><?= number_format((float) $limit); ?></b>
                     (<?= number_format($limit > 0 ? ((float) $r_ini->jumlah_karakter / $limit * 100) : 0, 2); ?>%)</p>
                  <?php if ($limit > 0 && $r_ini->jumlah_karakter / $limit >= 0.8): ?>
                  <div class="alert alert-warning">Peringatan: pemakaian bulan ini telah mencapai 80% dari limit.</div>
                  <?php endif; ?>
               <?php else: ?>
                  <p class="text-muted">Belum ada pemakaian DeepL bulan ini.</p>
               <?php endif; ?>
               <h4>6 bulan terakhir</h4>
               <table class="table table-bordered table-striped">
                  <thead><tr><th>Bulan</th><th>Request</th><th>Karakter</th><th>String</th></tr></thead>
                  <tbody>
                  <?php if (empty($usage)): ?><tr><td colspan="4" class="text-muted">Tidak ada data.</td></tr><?php endif; ?>
                  <?php foreach ($usage as $u): ?>
                     <tr><td><?= html_escape($u->bulan); ?></td><td><?= number_format((float) $u->jumlah_request); ?></td><td><?= number_format((float) $u->jumlah_karakter); ?></td><td><?= number_format((float) $u->jumlah_string); ?></td></tr>
                  <?php endforeach; ?>
                  </tbody>
               </table>
<?php endif; ?>
               <form method="post" action="<?= site_url('administrator/setting_bahasa/usage_check'); ?>" class="form-inline">
                  <input type="hidden" name="translator_csrf" value="<?= html_escape($translator_csrf); ?>">
                  <button type="submit" class="btn btn-primary">Cek kuota di DeepL</button>
               </form>
               <?php if ($deepl_usage_err): ?>
               <div class="alert alert-danger" style="margin-top:10px;"><?= html_escape($deepl_usage_err); ?></div>
               <?php elseif ($deepl_usage): ?>
               <div class="alert alert-success" style="margin-top:10px;">
                  DeepL: terpakai <b><?= number_format((float) $deepl_usage['character_count']); ?></b>
                  dari <b><?= number_format((float) $deepl_usage['character_limit']); ?></b> karakter.
               </div>
               <?php endif; ?>
            </div>
         </div>
      </div>
   </div>
</section>
