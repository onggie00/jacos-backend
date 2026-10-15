<section class="content-header">
   <h1>Status Sistem<small>Switch bahasa</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Status</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <ul class="nav nav-tabs">
            <li><a href="<?= site_url('administrator/setting_bahasa'); ?>">Bahasa User</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/override'); ?>">Override</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/cache'); ?>">Cache</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/warmup'); ?>">Warm-up</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/statistik'); ?>">Statistik</a></li>
            <li class="active"><a href="<?= site_url('administrator/setting_bahasa/status'); ?>">Status</a></li>
         </ul>
      </div>
   </div>
   <div class="row">
      <div class="col-md-6">
         <div class="box box-warning">
            <div class="box-header"><h3 class="box-title">Status Translator</h3></div>
            <div class="box-body">
               <table class="table table-bordered">
                  <tr><th style="width:220px;">Translator aktif</th>
                      <td><span class="label <?= $enabled ? 'bg-green' : 'bg-red'; ?>"><?= $enabled ? 'AKTIF' : 'NONAKTIF'; ?></span></td></tr>
                  <tr><th>Endpoint aktif</th>
                      <td><?php if (empty($endpoints)): ?><span class="text-muted">Tidak ada</span><?php else: ?>
                          <ul style="margin:0; padding-left:18px;"><?php foreach ($endpoints as $e): ?><li><?= html_escape($e); ?></li><?php endforeach; ?></ul>
                          <?php endif; ?></td></tr>
                  <tr><th>Circuit breaker</th>
                      <td><?= $breaker_open ? '<span class="label bg-red">TERBUKA — sisa ' . (int) $breaker_sisa . ' detik</span>' : '<span class="label bg-green">TERTUTUP</span>'; ?></td></tr>
                  <tr><th>DEEPL_API_KEY terisi</th>
                      <td><?= $key_ada ? '<span class="label bg-green">YA</span>' : '<span class="label bg-red">TIDAK</span>'; ?></td></tr>
               </table>
            </div>
         </div>
         <div class="box box-warning">
            <div class="box-header"><h3 class="box-title">Bahasa didukung</h3></div>
            <div class="box-body">
               <table class="table table-bordered">
                  <thead><tr><th>Kode</th><th>Nama</th><th>Target DeepL</th><th>Target Google</th></tr></thead>
                  <tbody>
                  <?php foreach ((array) $bahasa_cfg as $kode => $b): ?>
                     <tr><td><?= html_escape($kode); ?></td><td><?= html_escape($b['nama']); ?></td><td><?= html_escape($b['deepl']); ?></td><td><?= html_escape($b['google']); ?></td></tr>
                  <?php endforeach; ?>
                  </tbody>
               </table>
               <p>Jumlah entri kamus termuat (en-GB): <b><?= (int) $kamus_entries; ?></b></p>
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="box box-warning">
            <div class="box-header"><h3 class="box-title">Uji Lookup</h3></div>
            <div class="box-body">
               <p class="help-block">Menampilkan dari sumber mana teks akan dilayani. Tidak memanggil DeepL/Google.</p>
               <form method="post" action="<?= site_url('administrator/setting_bahasa/uji_lookup'); ?>">
                  <input type="hidden" name="translator_csrf" value="<?= html_escape($translator_csrf); ?>">
                  <div class="form-group">
                     <label>Teks Indonesia</label>
                     <textarea name="uji_teks" class="form-control" rows="2" maxlength="500" required></textarea>
                  </div>
                  <div class="form-group">
                     <label>Bahasa target</label>
                     <select name="uji_kode" class="form-control">
                        <?php foreach ((array) $bahasa_cfg as $kode => $b): if ($kode === 'id') continue; ?>
                        <option value="<?= html_escape($kode); ?>"><?= html_escape($kode . ' - ' . $b['nama']); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <button type="submit" class="btn btn-primary">Uji</button>
               </form>
               <?php $uji = $this->session->userdata('translator_uji_lookup'); ?>
               <?php if (is_array($uji) && isset($uji['sumber'])): ?>
               <div class="alert alert-info" style="margin-top:10px;">
                  <div>Teks ternormalisasi: <b><?= html_escape($uji['teks_asal']); ?></b></div>
                  <div>Sumber: <b><?= html_escape($uji['sumber']); ?></b></div>
                  <?php if ($uji['sumber'] === 'deepl'): ?>
                  <div class="text-muted">Belum ada di kamus/override/cache — akan diterjemahkan DeepL saat dipakai.</div>
                  <?php else: ?>
                  <div>Hasil: <b><?= html_escape($uji['hasil']); ?></b></div>
                  <?php endif; ?>
               </div>
               <?php endif; ?>
            </div>
         </div>
      </div>
   </div>
</section>
