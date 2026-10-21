<?php
/** Inisial nama untuk avatar daftar pelamar. */
if ( ! function_exists('labs_initials')) {
   function labs_initials($name) {
      $name = trim((string) $name);
      if ($name === '') { return '?'; }
      $parts = preg_split('/\\s+/', $name);
      $ini = strtoupper(substr($parts[0], 0, 1));
      if (count($parts) > 1) { $ini .= strtoupper(substr($parts[count($parts) - 1], 0, 1)); }
      return $ini;
   }
}
?>
<!-- Muat bundle desain labs-ui -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Pelamar <small>daftar</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Pelamar</li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">
            <div class="labs-card__header">
               <div class="labs-card__title">Pelamar <small>lamaran lintas lowongan</small></div>
               <span class="labs-badge labs-badge--default"><?= $pelamar_counts; ?> lamaran</span>
            </div>
            <div class="labs-card__body">

               <form action="<?= site_url('administrator/career_pelamar'); ?>" method="GET" class="labs-filter-inline">
                  <select name="id_job_posting" class="form-control">
                     <option value="">Semua lowongan</option>
                     <?php foreach ($lowongans as $l): ?>
                        <option value="<?= (int) $l->id_job_posting; ?>" <?= $id_job_posting === (int) $l->id_job_posting ? 'selected' : ''; ?>><?= html_escape($l->title); ?></option>
                     <?php endforeach; ?>
                  </select>
                  <select name="status" class="form-control">
                     <option value="">Semua status</option>
                     <?php $sts = array('perlu_verifikasi'=>'Perlu verifikasi','draft'=>'Draf','submitted'=>'Terkirim','in_process'=>'Dalam Proses','accepted'=>'Diterima','not_accepted'=>'Tidak Diterima','withdrawn'=>'Dibatalkan'); foreach ($sts as $k => $v): ?>
                        <option value="<?= $k; ?>" <?= $status_pilihan === $k ? 'selected' : ''; ?>><?= $v; ?></option>
                     <?php endforeach; ?>
                  </select>
                  <select name="stage_id" class="form-control">
                     <option value="">Semua tahap</option>
                     <?php foreach ($stages as $s): ?>
                        <option value="<?= (int) $s->id_stage; ?>" <?= $stage_id === (int) $s->id_stage ? 'selected' : ''; ?>>Tahap <?= (int) $s->stage_order; ?> — <?= html_escape($s->name); ?></option>
                     <?php endforeach; ?>
                  </select>
                  <div class="labs-filter-inline__search">
                     <span class="labs-filter-inline__search-icon"><i class="fa fa-user"></i></span>
                     <input type="text" name="q" class="form-control" placeholder="Nama / email..." value="<?= html_escape($q); ?>">
                  </div>
                  <button class="labs-btn labs-btn--primary labs-btn--sm" type="submit"><i class="fa fa-search"></i> Filter</button>
                  <a class="labs-btn labs-btn--default labs-btn--sm" href="<?= site_url('administrator/career_pelamar'); ?>">Reset</a>
               </form>

               <!-- ringkasan hitung per status (satu query) -->
               <div class="labs-row-actions labs-mt-3">
                  <?php foreach ($ringkasan as $k_ => $c_): ?>
                     <span class="labs-badge labs-badge--default"><?= $k_; ?>: <?= (int) $c_; ?></span>
                  <?php endforeach; ?>
               </div>

               <div class="labs-table-wrap labs-mt-3">
                  <div class="labs-table-scroll">
                     <table class="labs-table">
                        <thead>
                           <tr>
                              <th width="55">ID</th>
                              <th>Kandidat</th>
                              <th>Lowongan</th>
                              <th width="12%">Status</th>
                              <th width="14%">Kirim</th>
                              <th width="70">Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pelamars as $l): ?>
                           <tr>
                              <td><?= (int) $l->id_application; ?></td>
                              <td class="labs-name-cell">
                                 <span class="labs-avatar labs-avatar--sm"><?= labs_initials(html_escape($l->full_name)); ?></span>
                                 <span><?= html_escape($l->full_name); ?><br><small class="text-muted"><?= html_escape($l->email); ?></small></span>
                              </td>
                              <td><?= html_escape($l->posting_title); ?></td>
                              <td>
                                 <?php
                                    $cls = array('draft'=>'labs-badge--default','submitted'=>'labs-badge--solid labs-badge--primary','in_process'=>'labs-badge--warning','accepted'=>'labs-badge--success','not_accepted'=>'labs-badge--danger','withdrawn'=>'labs-badge--default');
                                    $lbl = array('draft'=>'Draf','submitted'=>'Terkirim','in_process'=>'Dalam Proses','accepted'=>'Diterima','not_accepted'=>'Tidak Diterima','withdrawn'=>'Dibatalkan');
                                 ?>
                                 <span class="labs-badge <?= isset($cls[$l->status]) ? $cls[$l->status] : 'labs-badge--default'; ?>"><span class="dot"></span><?= isset($lbl[$l->status]) ? $lbl[$l->status] : $l->status; ?></span>
                              </td>
                              <td class="labs-cell-muted"><?= $l->submitted_at !== NULL ? html_escape($l->submitted_at) : '-'; ?></td>
                              <td>
                                 <div class="labs-row-actions">
                                 <a href="<?= site_url('administrator/career_pelamar/view/' . $l->id_application); ?>" class="labs-btn labs-btn--default labs-btn--xs"><i class="fa fa-newspaper-o"></i> Detail</a>
                                 </div>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        <?php if ($pelamar_counts == 0): ?>
                           <tr class="labs-empty-row"><td colspan="6"><div class="labs-empty"><i class="fa fa-inbox"></i><p>Belum ada lamaran yang cocok.</p></div></td></tr>
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
