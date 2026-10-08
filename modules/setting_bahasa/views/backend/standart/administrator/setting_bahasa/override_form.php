<section class="content-header">
   <h1><?= $mode === 'edit' ? 'Ubah Override' : 'Tambah Override'; ?><small>Koreksi hasil terjemahan</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/setting_bahasa/override'); ?>">Override</a></li>
      <li class="active"><?= $mode === 'edit' ? 'Ubah' : 'Tambah'; ?></li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-8">
         <div class="box box-warning">
            <div class="box-body">
               <?php if (!empty($prefill_asal) || !empty($prefill_hasil)): ?>
               <div class="alert alert-info">Teks diambil dari entri cache. Simpan untuk mengaktifkan override (berlaku pada request berikutnya, mengalahkan cache).</div>
               <?php endif; ?>
               <form method="post" action="<?= site_url($mode === 'edit' ? 'administrator/setting_bahasa/override_update/' . (int) $row->id : 'administrator/setting_bahasa/override_save'); ?>">
                  <input type="hidden" name="translator_csrf" value="<?= html_escape($translator_csrf); ?>">
                  <div class="form-group">
                     <label>Kode bahasa target</label>
                     <select name="kode_bahasa" class="form-control" required>
                        <?php foreach ($bahasa_opt as $kode => $nama): ?>
                        <option value="<?= html_escape($kode); ?>" <?= ($row && $row->kode_bahasa === $kode ? 'selected' : ''); ?>><?= html_escape($kode . ' - ' . $nama); ?></option>
                        <?php endforeach; ?>
                     </select>
                     <span class="help-block">Bahasa "id" tidak tersedia (bahasa sumber).</span>
                  </div>
                  <div class="form-group">
                     <label>Teks asal (Indonesia, maks 500 karakter)</label>
                     <textarea name="teks_asal" class="form-control" rows="3" maxlength="500" required><?= html_escape($row ? $row->teks_asal : $prefill_asal); ?></textarea>
                  </div>
                  <div class="form-group">
                     <label>Teks hasil (terjemahan final)</label>
                     <textarea name="teks_hasil" class="form-control" rows="3" required><?= html_escape($row ? $row->teks_hasil : $prefill_hasil); ?></textarea>
                  </div>
                  <div class="form-group">
                     <label>Tipe hash</label>
                     <select name="tipe" class="form-control">
                        <option value="teks" <?= ($row && strpos($row->teks_asal, '<') === false ? 'selected' : ''); ?>>teks</option>
                        <option value="html" <?= ($row && strpos($row->teks_asal, '<') !== false ? 'selected' : ''); ?>>html</option>
                     </select>
                     <span class="help-block">Pilih "html" bila teks asal berformat HTML (hash diprefix berbeda agar cocok dengan Translator).</span>
                  </div>
                  <div class="form-group">
                     <label>Status</label>
                     <select name="aktif" class="form-control">
                        <option value="1" <?= (!$row || $row->aktif ? 'selected' : ''); ?>>Aktif</option>
                        <option value="0" <?= ($row && !$row->aktif ? 'selected' : ''); ?>>Nonaktif</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label>Catatan (opsional)</label>
                     <input type="text" name="catatan" class="form-control" maxlength="255" value="<?= html_escape($row ? $row->catatan : ''); ?>">
                  </div>
                  <button type="submit" class="btn btn-primary">Simpan</button>
                  <a class="btn btn-default" href="<?= site_url('administrator/setting_bahasa/override'); ?>">Batal</a>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
