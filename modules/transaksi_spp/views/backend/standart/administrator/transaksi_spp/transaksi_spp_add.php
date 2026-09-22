<!-- Fine Uploader Gallery CSS file
    ====================================================================== -->
<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<!-- Fine Uploader jQuery JS file
    ====================================================================== -->
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Transaksi Spp <small><?= cclang('new', ['Transaksi Spp']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a href="<?= site_url('administrator/transaksi_spp'); ?>">Transaksi Spp</a></li>
        <li class="active"><?= cclang('new'); ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-body ">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header ">
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="User Avatar">
                            </div>
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username">Transaksi Spp</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Transaksi Spp']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_transaksi_spp',
                            'class'   => 'form-horizontal form-step',
                            'id'      => 'form_transaksi_spp',
                            'enctype' => 'multipart/form-data',
                            'method'  => 'POST'
                        ]); ?>

                        <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="jenjang" id="jenjang" data-placeholder="Select Jenjang">
                                    <option value="sd">SD</option>
                                    <option value="smp">SMP</option>
                                    <option value="sma">SMA</option>
                                    <option value="ft">France Track</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="id_siswa_aktif" class="col-sm-2 control-label">Id Siswa Aktif
                            </label>
                            <div class="col-sm-8" id="list-sd">
                                <select class="form-control chosen chosen-select-deselect" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Select Siswa">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_sd_aktif') as $row) : ?>
                                        <option value="<?= $row->id_siswa_sd_aktif ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-8" id="list-smp">
                                <select class="form-control chosen chosen-select-deselect" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Select Siswa">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_smp_aktif') as $row) : ?>
                                        <option value="<?= $row->id_siswa_smp_aktif ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-8" id="list-sma">
                                <select class="form-control chosen chosen-select-deselect" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Select Siswa">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_sma_aktif') as $row) : ?>
                                        <option value="<?= $row->id_siswa_sma_aktif ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-8" id="list-ft">
                                <select class="form-control chosen chosen-select-deselect" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Select Siswa">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_ft_aktif') as $row) : ?>
                                        <option value="<?= $row->id_siswa_ft_aktif ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="tahun_ajaran" id="tahun_ajaran" data-placeholder="Select Tahun Ajaran">
                                <option value=""></option>
                                    <?php foreach (db_get_all_data('tahun_ajaran') as $row) : ?>
                                        <option value="<?= $row->code ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>

                                </select>
                            </div>
                        </div>

                        <div class="message"></div>
                        <div class="row-fluid col-md-7 container-button-bottom">
                            <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="<?= cclang('save_button'); ?> (Ctrl+s)">
                                <i class="fa fa-save"></i> <?= cclang('save_button'); ?>
                            </button>
                            <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save" data-stype='back' title="<?= cclang('save_and_go_the_list_button'); ?> (Ctrl+d)">
                                <i class="ion ion-ios-list-outline"></i> <?= cclang('save_and_go_the_list_button'); ?>
                            </a>
                            <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="<?= cclang('cancel_button'); ?> (Ctrl+x)">
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
                <!--/box body -->
            </div>
            <!--/box -->
        </div>
    </div>
</section>
<!-- /.content -->
<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
<!-- Page script -->
<script>
    $(document).ready(function() {
        $("#list-sd").show()
        $("#list-smp").hide()
        $("#list-sma").hide()
        $("#list-ft").hide()
        
        $("#jenjang").change(function() {
            var jenjang = $("#jenjang").val()
            if (jenjang == "sd") {
                $("#list-sd").show()
                $("#list-smp").hide()
                $("#list-sma").hide()
                $("#list-ft").hide()
            } else if (jenjang == "smp") {
                $("#list-smp").show()
                $("#list-sd").hide()
                $("#list-sma").hide()
                $("#list-ft").hide()
            } else if (jenjang == "sma") {
                $("#list-sma").show()
                $("#list-smp").hide()
                $("#list-sd").hide()
                $("#list-ft").hide()
            } else {
                $("#list-ft").show()
                $("#list-smp").hide()
                $("#list-sma").hide()
                $("#list-sd").hide()
            }
            console.log()

        });
        $('#btn_cancel').click(function() {
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
                },
                function(isConfirm) {
                    if (isConfirm) {
                        window.location.href = BASE_URL + 'administrator/transaksi_spp';
                    }
                });

            return false;
        }); /*end btn cancel*/

        $('.btn_save').click(function() {
            $('.message').fadeOut();
            $('#payment_response').val(payment_response.getData());

            var form_transaksi_spp = $('#form_transaksi_spp');
            var data_post = form_transaksi_spp.serializeArray();
            var save_type = $(this).attr('data-stype');

            data_post.push({
                name: 'save_type',
                value: save_type
            });

            $('.loading').show();

            $.ajax({
                    url: BASE_URL + '/administrator/transaksi_spp/add_save',
                    type: 'POST',
                    dataType: 'json',
                    data: data_post,
                })
                .done(function(res) {
                    $('form').find('.form-group').removeClass('has-error');
                    $('.steps li').removeClass('error');
                    $('form').find('.error-input').remove();
                    if (res.success) {
                        // var id_file_kwitansi = $('#transaksi_spp_file_kwitansi_galery').find('li').attr('qq-file-id');

                        if (save_type == 'back') {
                            window.location.href = res.redirect;
                            return;
                        }

                        $('.message').printMessage({
                            message: res.message
                        });
                        $('.message').fadeIn();
                        resetForm();
                        // if (typeof id_file_kwitansi !== 'undefined') {
                        //     $('#transaksi_spp_file_kwitansi_galery').fineUploader('deleteFile', id_file_kwitansi);
                        // }
                        $('.chosen option').prop('selected', false).trigger('chosen:updated');
                        payment_response.setData('');

                    } else {
                        if (res.errors) {

                            $.each(res.errors, function(index, val) {
                                $('form #' + index).parents('.form-group').addClass('has-error');
                                $('form #' + index).parents('.form-group').find('small').prepend(`
                      <div class="error-input">` + val + `</div>
                      `);
                            });
                            $('.steps li').removeClass('error');
                            $('.content section').each(function(index, el) {
                                if ($(this).find('.has-error').length) {
                                    $('.steps li:eq(' + index + ')').addClass('error').find('a').trigger('click');
                                }
                            });
                        }
                        $('.message').printMessage({
                            message: res.message,
                            type: 'warning'
                        });
                    }

                })
                .fail(function() {
                    $('.message').printMessage({
                        message: 'Error save data',
                        type: 'warning'
                    });
                })
                .always(function() {
                    $('.loading').hide();
                    $('html, body').animate({
                        scrollTop: $(document).height()
                    }, 2000);
                });

            return false;
        }); /*end btn save*/

        var params = {};
        params[csrf] = token;

        $('#transaksi_spp_file_kwitansi_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/transaksi_spp/upload_file_kwitansi_file',
                params: params
            },
            deleteFile: {
                enabled: true,
                endpoint: BASE_URL + '/administrator/transaksi_spp/delete_file_kwitansi_file',
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            multiple: false,
            validation: {
                allowedExtensions: ["*"],
                sizeLimit: 0,
            },
            showMessage: function(msg) {
                toastr['error'](msg);
            },
            callbacks: {
                onComplete: function(id, name, xhr) {
                    if (xhr.success) {
                        var uuid = $('#transaksi_spp_file_kwitansi_galery').fineUploader('getUuid', id);
                        $('#transaksi_spp_file_kwitansi_uuid').val(uuid);
                        $('#transaksi_spp_file_kwitansi_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit: function(id, name) {
                    var uuid = $('#transaksi_spp_file_kwitansi_uuid').val();
                    $.get(BASE_URL + '/administrator/transaksi_spp/delete_file_kwitansi_file/' + uuid);
                },
                onDeleteComplete: function(id, xhr, isError) {
                    if (isError == false) {
                        $('#transaksi_spp_file_kwitansi_uuid').val('');
                        $('#transaksi_spp_file_kwitansi_name').val('');
                    }
                }
            }
        }); /*end file_kwitansi galery*/







    }); /*end doc ready*/
</script>