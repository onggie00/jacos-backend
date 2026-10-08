<section class="content-header">
   <h1>Detail Cache<small></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/setting_bahasa/cache'); ?>">Cache</a></li>
      <li class="active">Detail</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-8">
         <div class="box box-warning">
            <div class="box-body">
               <table class="table table-bordered">
                  <tr><th style="width:160px;">Hash</th><td style="word-break:break-all;"><?= html_escape($row->hash_sumber); ?></td></tr>
                  <tr><th>Kode bahasa</th><td><?= html_escape($row->kode_bahasa); ?></td></tr>
                  <tr><th>Teks asal</th><td style="word-break:break-word;"><?= html_escape($row->teks_asal); ?></td></tr>
                  <tr><th>Teks hasil</th><td style="word-break:break-word;"><?= html_escape($row->teks_hasil); ?></td></tr>
                  <tr><th>Hit count</th><td><?= (int) $row->hit_count; ?></td></tr>
                  <tr><th>Terakhir dipakai</th><td><?= html_escape($row->last_used_at); ?></td></tr>
                  <tr><th>Dibuat</th><td><?= html_escape($row->created_at); ?></td></tr>
               </table>
               <a class="btn btn-warning" href="<?= site_url('administrator/setting_bahasa/cache_koreksi/' . (int) $row->id_translation_cache); ?>">Koreksi (buat Override)</a>
               <a class="btn btn-default" href="<?= site_url('administrator/setting_bahasa/cache'); ?>">Kembali</a>
            </div>
         </div>
      </div>
   </div>
</section>
