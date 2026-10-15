<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Lowongan Kerja <small>daftar</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Lowongan Kerja</li>
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
                     <div class="row pull-right">
                        <?php is_allowed('career_lowongan_add', function(){ ?>
                        <a class="btn btn-flat btn-success btn_add_new" href="<?= site_url('administrator/career_lowongan/add'); ?>"><i class="fa fa-plus-square-o"></i> Tambah</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="Daftar">
                     </div>
                     <h3 class="widget-user-username">Lowongan Kerja</h3>
                     <h5 class="widget-user-desc">Rekrutmen guru &amp; karyawan <i class="label bg-yellow"><?= $lowongan_counts; ?> lowongan</i></h5>
                  </div>

                  <form name="form_career_lowongan" id="form_career_lowongan" action="<?= base_url('administrator/career_lowongan/index'); ?>" method="GET">
                     <div class="row">
                        <div class="col-md-5">
                           <input type="text" class="form-control" name="q" placeholder="Cari judul lowongan..." value="<?= html_escape($q); ?>">
                        </div>
                        <div class="col-md-3">
                           <select name="status" class="form-control">
                              <option value="">Semua status</option>
                              <option value="buka"  <?= $status === 'buka' ? 'selected' : ''; ?>>Terbuka</option>
                              <option value="draft" <?= $status === 'draft' ? 'selected' : ''; ?>>Draf</option>
                              <option value="tutup" <?= $status === 'tutup' ? 'selected' : ''; ?>>Tutup</option>
                           </select>
                        </div>
                        <div class="col-md-2">
                           <button class="btn btn-flat btn-primary" type="submit"><i class="fa fa-search"></i> Cari</button>
                        </div>
                        <div class="col-md-2">
                           <a class="btn btn-flat btn-default" href="<?= site_url('administrator/career_lowongan'); ?>">Reset</a>
                        </div>
                     </div>
                  </form>

                  <div class="table-responsive" style="margin-top:10px">
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr>
                           <th width="55">ID</th>
                           <th>Judul</th>
                           <th width="15%">Pendidikan</th>
                           <th width="15%">Periode</th>
                           <th width="10%">Status</th>
                           <th width="10%">Lamaran</th>
                           <th width="160">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_career_lowongan">
                     <?php foreach ($lowongans as $l): ?>
                        <tr>
                           <td><?= (int) $l->id_job_posting; ?></td>
                           <td><?= html_escape($l->title); ?></td>
                           <td><?= html_escape($l->education_level !== NULL ? $l->education_level : '-'); ?></td>
                           <td>
                              <?= html_escape($l->opening_date !== NULL ? $l->opening_date : '?'); ?>
                              s/d
                              <?= html_escape($l->closing_date !== NULL ? $l->closing_date : '?'); ?>
                           </td>
                           <td>
                              <?php if ($l->status_tampil === 'buka'): ?>
                                 <span class="label label-success">Terbuka</span>
                              <?php elseif ($l->status_tampil === 'tutup'): ?>
                                 <span class="label label-danger">Tutup</span>
                              <?php else: ?>
                                 <span class="label label-default">Draf</span>
                              <?php endif; ?>
                              <?php if ((int) $l->is_published === 1): ?><small class="text-muted">(terbit)</small><?php endif; ?>
                           </td>
                           <td><?php if ($l->punya_lamaran): ?><span class="label label-info">Ada</span><?php else: ?><span class="label label-default">Belum</span><?php endif; ?></td>
                           <td>
                              <?php is_allowed('career_lowongan_view', function() use ($l){ ?>
                                 <a href="<?= site_url('administrator/career_lowongan/edit/' . $l->id_job_posting); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> Lihat</a>
                              <?php }) ?>
                              <?php is_allowed('career_lowongan_update', function() use ($l){ ?>
                                 <a href="<?= site_url('administrator/career_lowongan/edit/' . $l->id_job_posting); ?>" class="label-default"><i class="fa fa-edit"></i> Ubah</a>
                              <?php }) ?>
                              <?php if (!$l->punya_lamaran){ is_allowed('career_lowongan_delete', function() use ($l){ ?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/career_lowongan/delete/' . $l->id_job_posting); ?>" class="label-default remove-data"><i class="fa fa-close"></i> Hapus</a>
                              <?php }); } ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php if ($lowongan_counts == 0): ?>
                        <tr><td colspan="7" class="text-center">Belum ada lowongan yang cocok.</td></tr>
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
