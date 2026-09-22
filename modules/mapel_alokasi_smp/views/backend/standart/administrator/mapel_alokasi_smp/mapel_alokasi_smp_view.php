<section class="content-header">
   <h1>
      Detail Alokasi <small>View</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">Mapel Alokasi</a></li>
      <li class="active">Detail</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-info-circle"></i> Detail Alokasi Mapel
               </h3>
               <div class="box-tools pull-right">
                  <a class="btn btn-sm btn-default" href="<?= site_url('administrator/mapel_alokasi_smp'); ?>">
                     <i class="fa fa-arrow-left"></i> Kembali
                  </a>
               </div>
            </div>
            <div class="box-body">
               <table class="table table-bordered">
                  <tr>
                     <th width="200">Kelas</th>
                     <td><?= _ent($alokasi->kelas_label); ?></td>
                  </tr>
                  <tr>
                     <th>Mapel</th>
                     <td>
                        <span class="label label-info"><?= _ent($alokasi->mapel_kode); ?></span>
                        <?= _ent($alokasi->mapel_nama); ?>
                     </td>
                  </tr>
                  <tr>
                     <th>Guru</th>
                     <td><?= _ent($alokasi->guru_nama_lengkap); ?></td>
                  </tr>
                  <tr>
                     <th>Kode Mapel Guru</th>
                     <td><?= _ent($alokasi->guru_kode_mapel); ?></td>
                  </tr>
                  <tr>
                     <th>Jam Target / Minggu</th>
                     <td><strong><?= $alokasi->jam_target; ?></strong> jam</td>
                  </tr>
                  <tr>
                     <th>Jam Terpenuhi</th>
                     <td><?= $alokasi->jam_terpenuhi; ?> jam</td>
                  </tr>
                  <tr>
                     <th>Total Jam (Hard Cap)</th>
                     <td><?= $alokasi->total_jam; ?> jam</td>
                  </tr>
                  <tr>
                     <th>Status</th>
                     <td>
                        <?php if($alokasi->status == 'final'): ?>
                           <span class="label label-success">FINAL</span>
                        <?php else: ?>
                           <span class="label label-warning">DRAFT</span>
                        <?php endif; ?>
                     </td>
                  </tr>
               </table>
            </div>
         </div>
      </div>
   </div>
</section>
