
<script type="text/javascript">
</script>
<section class="content-header">
    <h1>
        <?= cclang('presensi_jam_catatan'); ?> <small><?= cclang('view_button', cclang('presensi_jam_catatan')); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/presensi_jam_catatan'); ?>"><?= cclang('presensi_jam_catatan'); ?></a></li>
        <li class="active"><?= cclang('view_button'); ?></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-body">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header">
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/preview.png" alt="User Avatar">
                            </div>
                            <h3 class="widget-user-username"><?= cclang('presensi_jam_catatan'); ?></h3>
                            <h5 class="widget-user-desc"><?= cclang('view_button', cclang('presensi_jam_catatan')); ?></h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-md-offset-3">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover">
                                        <tr>
                                            <th width="200"><?= cclang('id_jam_catatan'); ?></th>
                                            <td><?= $presensi_jam_catatan->id_jam_catatan; ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= cclang('jam'); ?></th>
                                            <td><?= _ent($presensi_jam_catatan->jam); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= cclang('is_lainnya'); ?></th>
                                            <td><?= (int)$presensi_jam_catatan->is_lainnya === 1 ? '<span class="label label-info">Lainnya</span>' : '-'; ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <a href="<?= site_url('administrator/presensi_jam_catatan'); ?>" class="btn btn-flat btn-default"><i class="fa fa-undo"></i> <?= cclang('back_button'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
