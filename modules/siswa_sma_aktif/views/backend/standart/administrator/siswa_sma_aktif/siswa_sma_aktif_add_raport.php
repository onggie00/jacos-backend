<!-- Fine Uploader Gallery CSS file
    ====================================================================== -->
    <link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Fine Uploader jQuery JS file
    ====================================================================== -->
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Siswa SMA Aktif <small><?= cclang('new', ['Siswa SMA Aktif']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a href="<?= site_url('administrator/siswa_sma_aktif'); ?>">Siswa SMA Aktif</a></li>
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
                            <h3 class="widget-user-username">Tambah Raport</h3>
                            <h5 class="widget-user-desc">Tambah raport Siswa SMA aktif</h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_siswa_sma_aktif',
                            'class'   => 'form-horizontal form-step',
                            'id'      => 'form_siswa_sma_aktif',
                            'enctype' => 'multipart/form-data',
                            'method'  => 'POST'
                        ]); ?>

                        <div class="form-group form-add">
                            <label for="id_siswa_sma" class="col-sm-2 control-label">Id Siswa SMA
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="id_siswa_sma[]" id="id_siswa_sma" data-placeholder="Select Id Siswa SMA">
                                    <option value=""></option>
                                    <?php foreach (get_all_siswa_aktif_sma() as $row) : ?>
                                        <option value="<?= $row->id_siswa_sma_aktif ?>"><?= $row->nama_lengkap . "(" . $row->label . ")"; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="file_raport" class="col-sm-2 control-label">File Raport
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="file_raport_galery"></div>
                                <input class="data_file data_file_uuid" name="file_raport_uuid[]" id="file_raport_uuid" type="hidden" value="<?= set_value('file_raport_uuid'); ?>">
                                <input class="data_file" name="file_raport_name[]" id="file_raport_name" type="hidden" value="<?= set_value('file_raport_name'); ?>">
                            </div>
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
                        <a class="btn btn-flat btn-success" id="addForm" title="Add form">
                            <i class="fa fa-plus"></i> Add Form
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
<!-- Page script -->
<script>
    $(document).ready(function() {
        var i = 1
        var params = {};
        params[csrf] = token;
        $("#id_siswa_sma").select2()
        $("#addForm").click(function() {
            i++
            var newField = `<div class="form-group form-add">
                            <label for="id_siswa_sma" class="col-sm-2 control-label">Id Siswa SMA
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="id_siswa_sma[]" id="id_siswa_sma_${i}" data-placeholder="Select Id Siswa SMA">
                                    <option value=""></option>
                                    <?php foreach (get_all_siswa_aktif_sma() as $row) : ?>
                                        <option value="<?= $row->id_siswa_sma_aktif ?>"><?= $row->nama_lengkap . "(" . $row->label . ")"; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="file_raport" class="col-sm-2 control-label">File Raport<i class="required">*</i></label>
                            <div class="col-sm-8">
                                <div id="file_raport_galery_${i}"></div>
                                <input class="data_file data_file_uuid" name="file_raport_uuid[]" id="file_raport_uuid_${i}" type="hidden">
                                <input class="data_file" name="file_raport_name[]" id="file_raport_name_${i}" type="hidden" ></div>
                            </div>`;
            $("#form_siswa_sma_aktif").append(newField);
            $("#id_siswa_sma_" + i).select2()
            fineUpload(i)
        });

        function fineUpload(i) {
            $('#file_raport_galery_' + i).fineUploader({
                template: 'qq-template-gallery',
                request: {
                    endpoint: BASE_URL + '/administrator/siswa_sma_aktif/upload_file_raport_file',
                    params: params
                },
                deleteFile: {
                    enabled: true,
                    endpoint: BASE_URL + '/administrator/siswa_sma_aktif/delete_file_raport_file',
                },
                thumbnails: {
                    placeholders: {
                        waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                        notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                    }
                },
                multiple: false,
                validation: {
                    allowedExtensions: ["pdf"],
                    sizeLimit: 0,
                },
                showMessage: function(msg) {
                    toastr['error'](msg);
                },
                callbacks: {
                    onComplete: function(id, name, xhr) {
                        if (xhr.success) {
                            var uuid = $('#file_raport_galery_' + i).fineUploader('getUuid', id);
                            $('#file_raport_uuid_' + i).val(uuid);
                            $('#file_raport_name_' + i).val(xhr.uploadName);
                        } else {
                            toastr['error'](xhr.error);
                        }
                    },
                    onSubmit: function(id, name) {
                        var uuid = $('#file_raport_uuid_' + i).val();
                        $.get(BASE_URL + '/administrator/siswa_sma_aktif/delete_file_raport_file/' + uuid);
                    },
                    onDeleteComplete: function(id, xhr, isError) {
                        if (isError == false) {
                            $('#file_raport_uuid_' + i).val('');
                            $('#file_raport_name_' + i).val('');
                        }
                    }
                }
            }); /*end file_raport galery*/

        }

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
                        window.location.href = BASE_URL + 'administrator/siswa_sma_aktif';
                    }
                });

            return false;
        }); /*end btn cancel*/

        $('.btn_save').click(function() {
            $('.message').fadeOut();

            var form_siswa_sma_aktif = $('#form_siswa_sma_aktif');
            var data_post = form_siswa_sma_aktif.serializeArray();
            var save_type = $(this).attr('data-stype');

            data_post.push({
                name: 'save_type',
                value: save_type
            });

            $('.loading').show();

            $.ajax({
                    url: BASE_URL + '/administrator/siswa_sma_aktif/add_raport_save',
                    type: 'POST',
                    dataType: 'json',
                    data: data_post,
                })
                .done(function(res) {
                    $('form').find('.form-group').removeClass('has-error');
                    $('.steps li').removeClass('error');
                    $('form').find('.error-input').remove();
                    if (res.success) {

                        if (save_type == 'back') {
                            window.location.href = res.redirect;
                            return;
                        }

                        $('.message').printMessage({
                            message: res.message
                        });
                        $('.message').fadeIn();
                        resetForm();
                        $('.chosen option').prop('selected', false).trigger('chosen:updated');

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

        $('#file_raport_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/siswa_sma_aktif/upload_file_raport_file',
                params: params
            },
            deleteFile: {
                enabled: true,
                endpoint: BASE_URL + '/administrator/siswa_sma_aktif/delete_file_raport_file',
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            multiple: false,
            validation: {
                allowedExtensions: ["pdf"],
                sizeLimit: 0,
            },
            showMessage: function(msg) {
                toastr['error'](msg);
            },
            callbacks: {
                onComplete: function(id, name, xhr) {
                    if (xhr.success) {
                        var uuid = $('#file_raport_galery').fineUploader('getUuid', id);
                        $('#file_raport_uuid').val(uuid);
                        $('#file_raport_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit: function(id, name) {
                    var uuid = $('#file_raport_uuid').val();
                    $.get(BASE_URL + '/administrator/siswa_sma_aktif/delete_file_raport_file/' + uuid);
                },
                onDeleteComplete: function(id, xhr, isError) {
                    if (isError == false) {
                        $('#file_raport_uuid').val('');
                        $('#file_raport_name').val('');
                    }
                }
            }
        }); /*end file_raport galery*/

    }); /*end doc ready*/
</script>