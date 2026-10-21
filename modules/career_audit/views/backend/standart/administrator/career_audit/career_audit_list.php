<!-- Muat bundle desain labs-ui -->
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<!-- Gaya tambahan khusus halaman audit (labs-ui tidak menyediakan komponen pas) -->
<style>
   /* .career-audit-code: potongan JSON detail pada kolom Detail agar tidak melebar berlebihan */
   .career-audit-code {
      word-break: break-all;
   }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Audit Karier <small>riwayat baca-saja</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Audit Karier</li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="labs-card">
            <div class="labs-card__header">
               <div class="labs-card__title"><i class="fa fa-history"></i> Audit Karier</div>
               <span class="labs-badge labs-badge--default"><?= $audit_count; ?> entri</span>
            </div>
            <div class="labs-card__body">
               <p class="labs-cell-muted">Semua aksi tercatat di modul ini</p>

               <form action="<?= site_url('administrator/career_audit'); ?>" method="GET" class="labs-filter-inline">
                  <select name="actor_type" class="form-control">
                     <option value="">Semua aktor</option>
                     <?php foreach (array('admin'=>'Admin','candidate'=>'Kandidat','system'=>'Sistem') as $k => $v): ?>
                        <option value="<?= $k; ?>" <?= $filter['actor_type'] === $k ? 'selected' : ''; ?>><?= $v; ?></option>
                     <?php endforeach; ?>
                  </select>
                  <select name="action" class="form-control">
                     <option value="">Semua aksi</option>
                     <?php foreach ($aksis as $a): ?>
                        <option value="<?= html_escape($a['action']); ?>" <?= $filter['action'] === $a['action'] ? 'selected' : ''; ?>><?= html_escape($a['action']); ?></option>
                     <?php endforeach; ?>
                  </select>
                  <select name="object_type" class="form-control">
                     <option value="">Semua objek</option>
                     <?php foreach ($tipe_objeks as $t): ?>
                        <option value="<?= html_escape($t['object_type']); ?>" <?= $filter['object_type'] === $t['object_type'] ? 'selected' : ''; ?>><?= html_escape($t['object_type']); ?></option>
                     <?php endforeach; ?>
                  </select>
                  <div class="labs-filter-inline__search">
                     <span class="labs-filter-inline__search-icon"><i class="fa fa-hashtag"></i></span>
                     <input type="number" name="object_id" class="form-control" placeholder="ID objek" value="<?= (int) $filter['object_id']; ?>">
                  </div>
                  <input type="date" name="tgl_dari" class="form-control" value="<?= html_escape((string) $filter['tgl_dari']); ?>" title="Dari tanggal">
                  <input type="date" name="tgl_sampai" class="form-control" value="<?= html_escape((string) $filter['tgl_sampai']); ?>" title="Sampai tanggal">
                  <button class="labs-btn labs-btn--primary labs-btn--sm" type="submit"><i class="fa fa-search"></i> Filter</button>
                  <a class="labs-btn labs-btn--default labs-btn--sm" href="<?= site_url('administrator/career_audit'); ?>">Reset</a>
               </form>

               <div class="labs-table-wrap">
                  <div class="labs-table-scroll">
                     <table class="labs-table">
                        <thead>
                           <tr>
                              <th width="160">Waktu</th>
                              <th width="100">Aktor</th>
                              <th width="220">Aksi</th>
                              <th width="180">Objek</th>
                              <th>Detail</th>
                              <th width="130">IP</th>
                           </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($audits as $a): ?>
                           <tr>
                              <td><?= html_escape($a->created_at); ?></td>
                              <td>
                                 <span class="labs-badge <?= $a->actor_type === 'admin' ? 'labs-badge--info' : ($a->actor_type === 'candidate' ? 'labs-badge--success' : 'labs-badge--default'); ?>"><span class="dot"></span><?= html_escape($a->actor_type); ?></span>
                                 <?php if ($a->actor_type === 'admin' && (int) $a->actor_id): ?> <small>#<?= (int) $a->actor_id; ?></small><?php endif; ?>
                              </td>
                              <td><span class="labs-badge labs-badge--default"><?= html_escape($a->action); ?></span></td>
                              <td>
                                 <?php if ($a->object_type === 'career_application'): ?>
                                    <a href="<?= site_url('administrator/career_pelamar/view/' . (int) $a->object_id); ?>"><?= html_escape($a->object_type); ?> #<?= (int) $a->object_id; ?></a>
                                 <?php elseif ($a->object_type === 'career_job_posting'): ?>
                                    <a href="<?= site_url('administrator/career_lowongan/edit/' . (int) $a->object_id); ?>"><?= html_escape($a->object_type); ?> #<?= (int) $a->object_id; ?></a>
                                 <?php else: ?>
                                    <?= html_escape($a->object_type); ?><?php if ((int) $a->object_id): ?> #<?= (int) $a->object_id; ?><?php endif; ?>
                                 <?php endif; ?>
                              </td>
                              <td><code class="small career-audit-code"><?php if ($a->action === 'simpan_nilai_tahap' AND ! $bisa_skor): ?>(memerlukan izin career_pelamar_score_view)<?php else: ?><?= html_escape($a->detail_json); ?><?php endif; ?></code></td>
                              <td><small><?= html_escape($a->ip_address); ?></small></td>
                           </tr>
                        <?php endforeach; ?>
                        <?php if ($audit_count == 0): ?>
                           <tr class="labs-empty-row"><td colspan="6"><div class="labs-empty"><i class="fa fa-inbox"></i><p>Belum ada entri yang cocok.</p></div></td></tr>
                        <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
               </div>

               <?php $qs_ = isset($qs) ? $qs : ''; ?>
               <?php $cur_ = isset($cur_offset) ? (int) $cur_offset : 0; ?>
               <?php $per_ = isset($per_page) ? (int) $per_page : 10; ?>
               <div class="labs-flex labs-flex-gap-2 labs-mt-4">
                  <?php if ($cur_ > 0): ?>
                     <a class="labs-btn labs-btn--default labs-btn--sm" href="<?= site_url('administrator/career_audit/index/' . max(0, $cur_ - $per_)) . ($qs_ ? '?' . $qs_ : ''); ?>">&laquo; Sebelumnya</a>
                  <?php endif; ?>
                  <?php if ($cur_ + $per_ < (int) $total_rows): ?>
                     <a class="labs-btn labs-btn--default labs-btn--sm" href="<?= site_url('administrator/career_audit/index/' . ($cur_ + $per_)) . ($qs_ ? '?' . $qs_ : ''); ?>">Berikutnya &raquo;</a>
                  <?php endif; ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
