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
         <div class="box box-warning">
            <div class="box-body">
               <div class="box box-widget widget-user-2">
                  <div class="widget-user-header">
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="Pelamar">
                     </div>
                     <h3 class="widget-user-username">Pelamar</h3>
                     <h5 class="widget-user-desc">Lamaran lintas lowongan <i class="label bg-yellow"><?= $pelamar_counts; ?> lamaran</i></h5>
                     <hr>
                  </div>

                  <form action="<?= site_url('administrator/career_pelamar'); ?>" method="GET" class="form-inline" style="padding:0 4px 8px">
                     <div class="form-group" style="margin-right:6px">
                        <select name="id_job_posting" class="form-control">
                           <option value="">Semua lowongan</option>
                           <?php foreach ($lowongans as $l): ?>
                              <option value="<?= (int) $l->id_job_posting; ?>" <?= $id_job_posting === (int) $l->id_job_posting ? 'selected' : ''; ?>><?= html_escape($l->title); ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="form-group" style="margin-right:6px">
                        <select name="status" class="form-control">
                           <option value="">Semua status</option>
                           <?php $sts = array('draft'=>'Draf','submitted'=>'Terkirim','in_process'=>'Dalam Proses','accepted'=>'Diterima','not_accepted'=>'Tidak Diterima','withdrawn'=>'Dibatalkan'); foreach ($sts as $k => $v): ?>
                              <option value="<?= $k; ?>" <?= $status === $k ? 'selected' : ''; ?>><?= $v; ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="form-group" style="margin-right:6px">
                        <select name="stage_id" class="form-control">
                           <option value="">Semua tahap</option>
                           <?php foreach ($stages as $s): ?>
                              <option value="<?= (int) $s->id_stage; ?>" <?= $stage_id === (int) $s->id_stage ? 'selected' : ''; ?>>Tahap <?= (int) $s->stage_order; ?> — <?= html_escape($s->name); ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="form-group" style="margin-right:6px">
                        <input type="text" name="q" class="form-control" placeholder="Nama / email..." value="<?= html_escape($q); ?>">
                     </div>
                     <button class="btn btn-flat btn-primary" type="submit"><i class="fa fa-search"></i> Filter</button>
                     <a class="btn btn-flat btn-default" href="<?= site_url('administrator/career_pelamar'); ?>">Reset</a>
                  </form>

                  <div class="table-responsive">
                  <table class="table table-bordered table-striped dataTable">
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
                           <td><?= html_escape($l->full_name); ?><br><small class="text-muted"><?= html_escape($l->email); ?></small></td>
                           <td><?= html_escape($l->posting_title); ?></td>
                           <td>
                              <?php
                                 $cls = array('draft'=>'label-default','submitted'=>'label-primary','in_process'=>'label-warning','accepted'=>'label-success','not_accepted'=>'label-danger','withdrawn'=>'label-default');
                                 $lbl = array('draft'=>'Draf','submitted'=>'Terkirim','in_process'=>'Dalam Proses','accepted'=>'Diterima','not_accepted'=>'Tidak Diterima','withdrawn'=>'Dibatalkan');
                              ?>
                              <span class="label <?= isset($cls[$l->status]) ? $cls[$l->status] : 'label-default'; ?>"><?= isset($lbl[$l->status]) ? $lbl[$l->status] : $l->status; ?></span>
                           </td>
                           <td><?= $l->submitted_at !== NULL ? html_escape($l->submitted_at) : '-'; ?></td>
                           <td>
                              <a href="<?= site_url('administrator/career_pelamar/view/' . $l->id_application); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> Detail</a>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if ($pelamar_counts == 0): ?>
                        <tr><td colspan="6" class="text-center">Belum ada lamaran yang cocok.</td></tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
                  <div style="padding:10px"><?= $pagination; ?></div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
