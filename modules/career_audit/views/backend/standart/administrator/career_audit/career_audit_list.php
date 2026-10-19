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
         <div class="box box-warning">
            <div class="box-body">
               <div class="box box-widget widget-user-2">
                  <div class="widget-user-header">
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/audit.png" alt="Audit">
                     </div>
                     <h3 class="widget-user-username">Audit Karier</h3>
                     <h5 class="widget-user-desc">Semua aksi tercatat di modul ini <i class="label bg-yellow"><?= $audit_count; ?> entri</i></h5>
                     <hr>
                  </div>

                  <form action="<?= site_url('administrator/career_audit'); ?>" method="GET" class="form-inline" style="padding:0 4px 8px">
                     <div class="form-group" style="margin-right:6px">
                        <select name="actor_type" class="form-control">
                           <option value="">Semua aktor</option>
                           <?php foreach (array('admin'=>'Admin','candidate'=>'Kandidat','system'=>'Sistem') as $k => $v): ?>
                              <option value="<?= $k; ?>" <?= $filter['actor_type'] === $k ? 'selected' : ''; ?>><?= $v; ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="form-group" style="margin-right:6px">
                        <select name="action" class="form-control">
                           <option value="">Semua aksi</option>
                           <?php foreach ($aksis as $a): ?>
                              <option value="<?= html_escape($a['action']); ?>" <?= $filter['action'] === $a['action'] ? 'selected' : ''; ?>><?= html_escape($a['action']); ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="form-group" style="margin-right:6px">
                        <select name="object_type" class="form-control">
                           <option value="">Semua objek</option>
                           <?php foreach ($tipe_objeks as $t): ?>
                              <option value="<?= html_escape($t['object_type']); ?>" <?= $filter['object_type'] === $t['object_type'] ? 'selected' : ''; ?>><?= html_escape($t['object_type']); ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="form-group" style="margin-right:6px">
                        <input type="number" name="object_id" class="form-control" placeholder="ID objek" value="<?= (int) $filter['object_id']; ?>" style="width:110px">
                     </div>
                     <div class="form-group" style="margin-right:6px">
                        <input type="date" name="tgl_dari" class="form-control" value="<?= html_escape((string) $filter['tgl_dari']); ?>" title="Dari tanggal">
                     </div>
                     <div class="form-group" style="margin-right:6px">
                        <input type="date" name="tgl_sampai" class="form-control" value="<?= html_escape((string) $filter['tgl_sampai']); ?>" title="Sampai tanggal">
                     </div>
                     <button class="btn btn-flat btn-primary" type="submit"><i class="fa fa-search"></i> Filter</button>
                     <a class="btn btn-flat btn-default" href="<?= site_url('administrator/career_audit'); ?>">Reset</a>
                  </form>

                  <div class="table-responsive">
                  <table class="table table-bordered table-striped dataTable">
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
                              <span class="label <?= $a->actor_type === 'admin' ? 'bg-navy' : ($a->actor_type === 'candidate' ? 'bg-olive' : 'label-default'); ?>"><?= html_escape($a->actor_type); ?></span>
                              <?php if ($a->actor_type === 'admin' && (int) $a->actor_id): ?> <small>#<?= (int) $a->actor_id; ?></small><?php endif; ?>
                           </td>
                           <td><span class="label label-default"><?= html_escape($a->action); ?></span></td>
                           <td>
                              <?php if ($a->object_type === 'career_application'): ?>
                                 <a href="<?= site_url('administrator/career_pelamar/view/' . (int) $a->object_id); ?>"><?= html_escape($a->object_type); ?> #<?= (int) $a->object_id; ?></a>
                              <?php elseif ($a->object_type === 'career_job_posting'): ?>
                                 <a href="<?= site_url('administrator/career_lowongan/edit/' . (int) $a->object_id); ?>"><?= html_escape($a->object_type); ?> #<?= (int) $a->object_id; ?></a>
                              <?php else: ?>
                                 <?= html_escape($a->object_type); ?><?php if ((int) $a->object_id): ?> #<?= (int) $a->object_id; ?><?php endif; ?>
                              <?php endif; ?>
                           </td>
                           <td><code class="small" style="word-break:break-all"><?php if ($a->action === 'simpan_nilai_tahap' AND ! $bisa_skor): ?>(memerlukan izin career_pelamar_score_view)<?php else: ?><?= html_escape($a->detail_json); ?><?php endif; ?></code></td>
                           <td><small><?= html_escape($a->ip_address); ?></small></td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if ($audit_count == 0): ?>
                        <tr><td colspan="6" class="text-center">Belum ada entri yang cocok.</td></tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
                  <?php $qs_ = isset($qs) ? $qs : ''; ?>
                  <?php $cur_ = isset($cur_offset) ? (int) $cur_offset : 0; ?>
                  <?php $per_ = isset($per_page) ? (int) $per_page : 10; ?>
                  <div style="padding:10px">
                     <?php if ($cur_ > 0): ?>
                        <a class="btn btn-default btn-flat" href="<?= site_url('administrator/career_audit/index/' . max(0, $cur_ - $per_)) . ($qs_ ? '?' . $qs_ : ''); ?>">&laquo; Sebelumnya</a>
                     <?php endif; ?>
                     <?php if ($cur_ + $per_ < (int) $total_rows): ?>
                        <a class="btn btn-default btn-flat" href="<?= site_url('administrator/career_audit/index/' . ($cur_ + $per_)) . ($qs_ ? '?' . $qs_ : ''); ?>">Berikutnya &raquo;</a>
                     <?php endif; ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
