<section class="content-header">
   <h1>
      Detail Jadwal <small>View</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/jadwal_pelajaran_smp'); ?>">Jadwal Pelajaran</a></li>
      <li class="active">Detail</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title">
                  <i class="fa fa-info-circle"></i> Detail Jadwal Pelajaran
               </h3>
               <div class="box-tools pull-right">
                  <a class="btn btn-sm btn-default" href="<?= site_url('administrator/jadwal_pelajaran_smp'); ?>">
                     <i class="fa fa-arrow-left"></i> Kembali
                  </a>
               </div>
            </div>
            <div class="box-body">
               <table class="table table-bordered">
                  <tr>
                     <th width="200">Hari</th>
                     <td><strong><?= _ent($jadwal->nama_hari); ?></strong></td>
                  </tr>
                  <tr>
                     <th>Jam</th>
                     <td>Jam ke <?= _ent($jadwal->jam_ke); ?> (<?= _ent($jadwal->jam_pelajaran); ?>)</td>
                  </tr>
                  <tr>
                     <th>Kelas</th>
                     <td><strong><?= _ent($jadwal->kelas_label); ?></strong></td>
                  </tr>
                  <tr>
                     <th>Mapel</th>
                     <td>
                        <span class="label label-info"><?= _ent($jadwal->mapel_kode); ?></span>
                        <?= _ent($jadwal->mapel_nama); ?>
                     </td>
                  </tr>
                  <tr>
                     <th>Guru</th>
                     <td><?= _ent($jadwal->guru_nama); ?></td>
                  </tr>
                  <tr>
                     <th>Kode Mapel</th>
                     <td><?= _ent($jadwal->kode_mapel); ?></td>
                  </tr>
               </table>
            </div>
         </div>
      </div>
   </div>
</section>
