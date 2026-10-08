<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pengaturan Bahasa<small>Konfigurasi bahasa user</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Pengaturan Bahasa</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <ul class="nav nav-tabs">
            <li class="active"><a href="<?= site_url('administrator/setting_bahasa'); ?>">Bahasa User</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/override'); ?>">Override</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/cache'); ?>">Cache</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/statistik'); ?>">Statistik</a></li>
            <li><a href="<?= site_url('administrator/setting_bahasa/status'); ?>">Status</a></li>
         </ul>
         <div class="box box-warning">
            <div class="box-body">
<?php if ($rows === null): ?>
               <div class="alert alert-danger">Tabel setting_bahasa tidak dapat dibaca. Hubungi administrator database.</div>
<?php else: ?>
               <div class="row" style="margin-bottom:10px;">
                  <div class="col-md-6">
                     <b>Per bahasa:</b>
                     <?php if (!empty($ringkasan['per_bahasa'])): foreach ($ringkasan['per_bahasa'] as $pb): ?>
                        <span class="label bg-blue"><?= html_escape($pb->kode_bahasa); ?>: <?= (int) $pb->jumlah; ?></span>
                     <?php endforeach; endif; ?>
                  </div>
                  <div class="col-md-6">
                     <b>Per role:</b>
                     <?php if (!empty($ringkasan['per_role'])): foreach ($ringkasan['per_role'] as $pr): ?>
                        <span class="label bg-olive"><?= html_escape($pr->role); ?>: <?= (int) $pr->jumlah; ?></span>
                     <?php endforeach; endif; ?>
                  </div>
               </div>
               <form method="get" action="<?= site_url('administrator/setting_bahasa/index'); ?>" class="form-inline" style="margin-bottom:10px;">
                  <select name="role" class="form-control input-sm">
                     <option value="">Semua role</option>
                     <?php foreach (array('siswa','ortu','guru','pegawai','pimpinan') as $r): ?>
                     <option value="<?= $r; ?>" <?= ($filter['role'] === $r ? 'selected' : ''); ?>><?= $r; ?></option>
                     <?php endforeach; ?>
                  </select>
                  <select name="kode" class="form-control input-sm">
                     <option value="">Semua bahasa</option>
                     <?php foreach ((array) $bahasa_cfg as $kode => $b): ?>
                     <option value="<?= html_escape($kode); ?>" <?= ($filter['kode'] === $kode ? 'selected' : ''); ?>><?= html_escape($b['nama']); ?></option>
                     <?php endforeach; ?>
                  </select>
                  <select name="jenjang" class="form-control input-sm">
                     <option value="">Semua jenjang</option>
                     <?php foreach (array('kb','tk','sd','smp','sma','ft') as $j): ?>
                     <option value="<?= $j; ?>" <?= ($filter['jenjang'] === $j ? 'selected' : ''); ?>><?= strtoupper($j); ?></option>
                     <?php endforeach; ?>
                  </select>
                  <input type="text" name="q" class="form-control input-sm" placeholder="Cari email..." value="<?= html_escape($filter['q']); ?>">
                  <button type="submit" class="btn btn-sm btn-primary">Filter</button>
               </form>
               <div class="table-responsive">
               <table class="table table-bordered table-striped">
                  <thead><tr><th>Email</th><th>Nama</th><th>Role</th><th>Jenjang</th><th>Bahasa</th><th>Diubah</th></tr></thead>
                  <tbody>
                  <?php foreach ($rows as $row): ?>
                     <tr>
                        <td><?= html_escape($row->email_ms_office); ?></td>
                        <td><?= html_escape($row->nama_user); ?></td>
                        <td><?= html_escape($row->role); ?></td>
                        <td><?= html_escape($row->jenjang !== '' ? $row->jenjang : '-'); ?></td>
                        <td><?= html_escape($row->kode_bahasa); ?></td>
                        <td><?= html_escape($row->updated_at); ?></td>
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
