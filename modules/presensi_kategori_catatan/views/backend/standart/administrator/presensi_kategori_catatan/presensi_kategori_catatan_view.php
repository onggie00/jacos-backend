
<section class="content-header">
    <h1>
        <?= cclang('presensi_kategori_catatan'); ?> <small><?= cclang('view', cclang('presensi_kategori_catatan')); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/presensi_kategori_catatan'); ?>"><?= cclang('presensi_kategori_catatan'); ?></a></li>
        <li class="active"><?= cclang('view'); ?></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-body">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header">
                            <h3 class="widget-user-username"><?= _ent($presensi_kategori_catatan->nama_kategori); ?></h3>
                            <h5 class="widget-user-desc"><?= cclang('view', cclang('presensi_kategori_catatan')); ?></h5>
                            <hr>
                        </div>
                        <table class="table table-striped">
                            <tr><th width="200"><?= cclang('id_kategori_catatan'); ?></th><td><?= $presensi_kategori_catatan->id_kategori_catatan; ?></td></tr>
                            <tr><th><?= cclang('nama_kategori'); ?></th><td><?= _ent($presensi_kategori_catatan->nama_kategori); ?></td></tr>
                            <tr><th><?= cclang('skor'); ?></th><td><?= (int)$presensi_kategori_catatan->skor; ?></td></tr>
                            <tr><th><?= cclang('is_custom'); ?></th><td><?= (int)$presensi_kategori_catatan->is_custom === 1 ? 'Ya' : 'Tidak'; ?></td></tr>
                            <tr><th><?= cclang('status'); ?></th><td><?= _ent($presensi_kategori_catatan->status); ?></td></tr>
                            <tr><th><?= cclang('created_at'); ?></th><td><?= $presensi_kategori_catatan->created_at; ?></td></tr>
                            <tr><th><?= cclang('updated_at'); ?></th><td><?= $presensi_kategori_catatan->updated_at; ?></td></tr>
                            <tr><th><?= cclang('updated_by'); ?></th><td><?= _ent($presensi_kategori_catatan->updated_by); ?></td></tr>
                        </table>
                        <div class="row-fluid col-md-7">
                            <a href="<?= site_url('administrator/presensi_kategori_catatan'); ?>" class="btn btn-flat btn-default btn_action"><i class="fa fa-undo"></i> Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
