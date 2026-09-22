<?php if (!empty($catatan)): ?>
<div class="table-responsive">
   <table class="table table-bordered table-striped" style="margin:0">
      <thead>
         <tr style="background:#f8f9fa">
            <th style="width:80px;text-align:center">Jam Ke</th>
            <th style="width:120px;text-align:center">Status</th>
            <th>Keterangan</th>
            <th style="width:160px">Dicatat Oleh</th>
            <th style="width:150px">Waktu</th>
         </tr>
      </thead>
      <tbody>
      <?php foreach ($catatan as $c): ?>
         <tr>
            <td style="text-align:center"><?= _ent($c->jam_ke); ?></td>
            <td style="text-align:center"><?= _ent($c->status_hadir); ?></td>
            <td><?= _ent($c->keterangan); ?></td>
            <td><?= _ent($c->updated_by); ?></td>
            <td><?= date('d-m-Y H:i', strtotime($c->tanggal_waktu)); ?></td>
         </tr>
      <?php endforeach; ?>
      </tbody>
   </table>
</div>
<?php else: ?>
<div class="text-center" style="padding:30px">
   <i class="fa fa-inbox" style="font-size:40px;color:#ddd"></i>
   <p style="margin-top:10px;color:#999">Tidak ada catatan pelajaran untuk siswa pada tanggal ini.</p>
</div>
<?php endif; ?>
