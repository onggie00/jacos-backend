
<script type="text/javascript">
</script>
<section class="content-header">
    <h1>
        <?= cclang('presensi_kategori_catatan'); ?> <small><?= cclang('new', cclang('presensi_kategori_catatan')); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/presensi_kategori_catatan'); ?>"><?= cclang('presensi_kategori_catatan'); ?></a></li>
        <li class="active"><?= cclang('new'); ?></li>
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
                                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="User Avatar">
                            </div>
                            <h3 class="widget-user-username"><?= cclang('presensi_kategori_catatan'); ?></h3>
                            <h5 class="widget-user-desc"><?= cclang('new', cclang('presensi_kategori_catatan')); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', array(
                            'name'    => 'form_presensi_kategori_catatan',
                            'class'   => 'form-horizontal form-step',
                            'id'      => 'form_presensi_kategori_catatan',
                            'enctype' => 'multipart/form-data',
                            'method'  => 'POST'
                        )); ?>

                        <div class="form-group">
                            <label for="nama_kategori" class="col-sm-2 control-label">Nama Kategori <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_kategori" id="nama_kategori" placeholder="Nama Kategori" value="<?= set_value('nama_kategori'); ?>">
                                <small class="info help-block">Max Length : 100.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="skor" class="col-sm-2 control-label">Skor <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <input type="number" min="0" class="form-control" name="skor" id="skor" placeholder="0" value="<?= set_value('skor', 0); ?>">
                                <small class="info help-block">Skor pelanggaran (semua di-set 0 dulu).</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="is_custom" class="col-sm-2 control-label">Kategori Custom?</label>
                            <div class="col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_custom" id="is_custom" value="1" <?= set_checkbox('is_custom', '1'); ?>>
                                        Ya (centang untuk kategori "Lainnya" — FE akan trigger input jenis pelanggaran + skor)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status" class="col-sm-2 control-label">Status <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="status" id="status">
                                    <option value="aktif" <?= set_select('status', 'aktif', true); ?>>Aktif</option>
                                    <option value="nonaktif" <?= set_select('status', 'nonaktif'); ?>>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="message"></div>
                        <div class="row-fluid col-md-7 container-button-bottom">
                            <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="<?= cclang('save_button'); ?>">
                                <i class="fa fa-save"></i> <?= cclang('save_button'); ?>
                            </button>
                            <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save" data-stype='back' title="<?= cclang('save_and_go_the_list_button'); ?>">
                                <i class="ion ion-ios-list-outline"></i> <?= cclang('save_and_go_the_list_button'); ?>
                            </a>
                            <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="<?= cclang('cancel_button'); ?>">
                                <i class="fa fa-undo"></i> <?= cclang('cancel_button'); ?>
                            </a>
                            <span class="loading loading-hide">
                                <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg">
                                <i><?= cclang('loading_saving_data'); ?></i>
                            </span>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
$(document).ready(function(){
    $('#btn_cancel').click(function(){
        swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes!",
            cancelButtonText: "No!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                window.location.href = BASE_URL + 'administrator/presensi_kategori_catatan';
            }
        });
        return false;
    });

    $('.btn_save').click(function(){
        $('.message').fadeOut();
        var form = $('#form_presensi_kategori_catatan');
        var data_post = form.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
        $('.loading').show();

        $.ajax({
            url: BASE_URL + '/administrator/presensi_kategori_catatan/add_save',
            type: 'POST',
            dataType: 'json',
            data: data_post,
        })
        .done(function(res) {
            $('form').find('.form-group').removeClass('has-error');
            $('form').find('.error-input').remove();
            if (res.success) {
                if (save_type == 'back') {
                    window.location.href = res.redirect;
                    return;
                }
                $('.message').printMessage({message: res.message});
                $('.message').fadeIn();
                $('form')[0].reset();
            } else {
                if (res.errors) {
                    $.each(res.errors, function(index, val) {
                        $('form #' + index).parents('.form-group').addClass('has-error');
                        $('form #' + index).parents('.form-group').find('small').prepend('<div class="error-input">' + val + '</div>');
                    });
                }
                $('.message').printMessage({message: res.message, type: 'warning'});
            }
        })
        .fail(function() {
            $('.message').printMessage({message: 'Error save data', type: 'warning'});
        })
        .always(function() {
            $('.loading').hide();
            $('html, body').animate({scrollTop: $(document).height()}, 2000);
        });
        return false;
    });
});
</script>
