<section class="content-header">
   <h1>Warm-up Cache Terjemahan<small>Isi cache DeepL sebelum diaktifkan (tanpa menunggu user)</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Warm-up</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <ul class="nav nav-tabs">
            <li><a href="<?= site_url('administrator/setting_bahasa'); ?>">Bahasa User</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/override'); ?>">Override</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/cache'); ?>">Cache</a></li>
            <li class="active"><a href="<?= site_url('administrator/setting_bahasa/warmup'); ?>">Warm-up</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/statistik'); ?>">Statistik</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/status'); ?>">Status</a></li>
         </ul>
         <div class="box box-warning">
            <div class="box-body">
<?php if (!empty($lock_ada)): ?>
               <div class="alert alert-warning">Masih ada sesi warm-up berjalan (lock aktif, maksimal beberapa menit). Tunggu selesai atau jalankan setelah lock lewat.</div>
<?php endif; ?>
               <p class="text-muted">Hanya path whitelist aktif <strong>bersama=true</strong> yang diproses (konten bersama, tanpa data pribadi). Dry-run tidak memanggil DeepL. Eksekusi bertahap: tiap potongan maksimal 20 string / 4.000 karakter / 20 detik; berhenti otomatis bila kuota DeepL bulan ini akan melewati 90% limit (<?= number_format($limit); ?> karakter).</p>
               <form id="form-warmup" class="form-inline" onsubmit="return false;">
                  <input type="hidden" name="translator_csrf" id="w-csrf" value="<?= html_escape($translator_csrf); ?>">
                  <div class="form-group" style="margin-right:12px;">
                     <label>Endpoint</label>
                     <select name="endpoint" id="w-endpoint" class="form-control input-sm">
                        <option value="">— pilih —</option>
                        <?php foreach ($endpoint_opt as $ep): ?>
                        <option value="<?= html_escape($ep); ?>"><?= html_escape($ep); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="form-group" style="margin-right:12px;">
                     <label>atau Gelombang</label>
                     <select name="wave" id="w-wave" class="form-control input-sm">
                        <option value="">— pilih —</option>
                        <?php foreach ($wave_opt as $kode => $nama): ?>
                        <option value="<?= html_escape($kode); ?>"><?= html_escape($nama); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="form-group" style="margin-right:12px;">
                     <label>Bahasa</label>
                     <select name="lang" id="w-lang" class="form-control input-sm">
                        <?php foreach ($bahasa_opt as $kode => $nama): ?>
                        <option value="<?= html_escape($kode); ?>"><?= html_escape($nama); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="form-group" style="margin-right:12px;">
                     <label>Max chars/sesi</label>
                     <input type="number" name="max_chars" id="w-max" class="form-control input-sm" value="<?= (int) $max_chars_default; ?>" min="1" max="<?= (int) $max_chars_cap; ?>" style="width:110px;">
                  </div>
                  <button type="button" id="btn-hitung" class="btn btn-sm btn-info">Hitung</button>
                  <button type="button" id="btn-jalankan" class="btn btn-sm btn-primary">Jalankan</button>
                  <button type="button" id="btn-berhenti" class="btn btn-sm btn-danger" style="display:none;">Berhenti</button>
               </form>
               <div id="w-pesan" style="margin-top:12px;"></div>
               <div id="w-hasil" style="margin-top:12px;"></div>
               <div id="w-progres" style="margin-top:12px;display:none;">
                  <div class="progress" style="max-width:520px;">
                     <div id="w-bar" class="progress-bar progress-bar-primary" role="progressbar" style="width:0%;">0%</div>
                  </div>
                  <p id="w-progres-teks" class="text-muted"></p>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<script>
(function () {
   var jalan = false;
   var token = '';

   function pesan(tipe, teks) {
      document.getElementById('w-pesan').innerHTML =
         '<div class="alert alert-' + tipe + '" style="max-width:720px;">' + teks + '</div>';
   }
   function post(data, cb) {
      data.translator_csrf = document.getElementById('w-csrf').value;
      var f = new FormData();
      for (var k in data) { if (data[k] !== null && typeof data[k] !== 'undefined') f.append(k, data[k]); }
      fetch('<?= site_url('administrator/setting_bahasa/warmup_'); ?>' + data.aksi_url, { method: 'POST', body: f, credentials: 'same-origin' })
         .then(function (r) { return r.json(); })
         .then(cb)
         .catch(function (e) { jalan = false; tombol(); pesan('danger', 'Gagal menghubungi server: ' + e); });
   }
   function tombol() {
      document.getElementById('btn-berhenti').style.display = jalan ? '' : 'none';
      document.getElementById('btn-jalankan').disabled = jalan;
   }
   function scope() {
      return {
         endpoint: document.getElementById('w-endpoint').value,
         wave: document.getElementById('w-wave').value,
         lang: document.getElementById('w-lang').value
      };
   }

   document.getElementById('btn-hitung').addEventListener('click', function () {
      var s = scope();
      if (!s.endpoint && !s.wave) { pesan('warning', 'Pilih endpoint atau gelombang dulu.'); return; }
      pesan('info', 'Menghitung...');
      post({ aksi: 'hitung', aksi_url: 'hitung', endpoint: s.endpoint, wave: s.wave, lang: s.lang }, function (j) {
         if (!j.ok) { pesan('danger', j.err); return; }
         if (j.kosong) { pesan('warning', 'Tidak ada string ditemukan (tabel sumber kosong / tidak terbaca di database ini).'); return; }
         var t = j.per_tipe;
         var baris = [];
         for (var tp in t) { if (t[tp].n > 0) baris.push('tipe ' + tp + ': ' + t[tp].n + ' string / ' + t[tp].c + ' char'); }
         pesan('success',
            '<strong>' + (j.scope.mode === 'endpoint' ? 'Endpoint' : 'Gelombang') + ' ' + j.scope.kunci + '</strong> — bahasa ' + j.lang +
            '<br>' + baris.join(' &middot; ') +
            '<br><strong>Total ' + j.total_n + ' string / ' + j.total_c + ' char</strong>' +
            '<br>Belum tercover (kamus/override/cache): ' + j.belum_n + ' string / ' + j.belum_c + ' char' +
            (j.sumber_err ? '<br><span class="text-danger">Perhatian: sebagian tabel sumber tidak terbaca.</span>' : ''));
      });
   });

   document.getElementById('btn-jalankan').addEventListener('click', function () {
      if (jalan) return;
      var s = scope();
      if (!s.endpoint && !s.wave) { pesan('warning', 'Pilih endpoint atau gelombang dulu.'); return; }
      jalan = true; tombol();
      document.getElementById('w-hasil').innerHTML = '';
      document.getElementById('w-progres').style.display = '';
      pesan('info', 'Memulai sesi...');
      post({ aksi: 'mulai', aksi_url: 'jalankan', endpoint: s.endpoint, wave: s.wave, lang: s.lang, max_chars: document.getElementById('w-max').value }, function (j) {
         if (!j.ok) { jalan = false; tombol(); pesan('danger', j.err); return; }
         if (j.done) { jalan = false; tombol(); pesan('success', j.msg + (j.url_cache ? ' <a href="' + j.url_cache + '">Lihat halaman Cache</a>' : '')); return; }
         token = j.token;
         pesan('success', 'Sesi dimulai.');
         langkah();
      });
   });

   function langkah() {
      if (!jalan) return;
      post({ aksi: 'langkah', aksi_url: 'jalankan', token: token, lang: document.getElementById('w-lang').value }, function (j) {
         if (!j.ok) { jalan = false; tombol(); pesan('danger', j.err); return; }
         if (j.status === 'jeda' || j.status === 'berhenti') { jalan = false; tombol(); pesan('warning', j.alasan); return; }
         var pct = j.total_n > 0 ? Math.round(j.processed_n * 100 / j.total_n) : 100;
         document.getElementById('w-bar').style.width = pct + '%';
         document.getElementById('w-bar').textContent = pct + '%';
         document.getElementById('w-progres-teks').textContent =
            'Diproses ' + j.processed_n + '/' + j.total_n + ' string (' + j.processed_c + '/' + j.total_c + ' char) — DeepL ' + j.deepl_chars + ' char (billed ' + j.deepl_billed + '), gagal ' + j.gagal;
         if (j.done) {
            jalan = false; tombol();
            pesan('success', j.msg + (j.url_cache ? ' <a href="' + j.url_cache + '">Lihat halaman Cache</a>' : ''));
            return;
         }
         langkah();
      });
   }

   document.getElementById('btn-berhenti').addEventListener('click', function () {
      jalan = false; tombol();
      post({ aksi: 'henti', aksi_url: 'jalankan', lang: document.getElementById('w-lang').value }, function (j) {
         pesan('warning', 'Sesi dihentikan. Yang sudah diproses tersimpan di cache; klik Jalankan untuk melanjutkan (string yang sudah di cache dilewati).');
      });
   });
})();
</script>
