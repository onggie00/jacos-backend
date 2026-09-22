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
        Pegawai <small><?= cclang('new', ['Pegawai']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a href="<?= site_url('administrator/pegawai'); ?>">Pegawai</a></li>
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
                            <h3 class="widget-user-username">Tambah Slip Gaji</h3>
                            <h5 class="widget-user-desc">Tambah Slip Gaji Pegawai</h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_pegawai',
                            'class'   => 'form-horizontal form-step',
                            'id'      => 'form_pegawai',
                            'enctype' => 'multipart/form-data',
                            'method'  => 'POST'
                        ]); ?>

                        <div class="form-group ">
                            <label for="id_pegawai" class="col-sm-2 control-label">ID Pegawai
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="id_pegawai[]" id="id_pegawai" data-placeholder="Select ID Pegawai">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('pegawai') as $row) : ?>
                                        <option value="<?= $row->id_pegawai ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>


                        <div class="form-group ">
                            <label for="slip_gaji" class="col-sm-2 control-label">Slip Gaji
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="slip_gaji_galery"></div>
                                <input class="data_file data_file_uuid" name="slip_gaji_uuid[]" id="slip_gaji_uuid" type="hidden" value="<?= set_value('slip_gaji_uuid'); ?>">
                                <input class="data_file" name="slip_gaji_name[]" id="slip_gaji_name" type="hidden" value="<?= set_value('slip_gaji_name'); ?>">
                            </div>
                        </div>

                        <div id="content-add-form"></div>
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
        $("#id_pegawai").select2()
        $("#addForm").click(function() {
            i++
            var newField = `<div class="form-group ">
                            <label for="id_pegawai" class="col-sm-2 control-label">ID Pegawai
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="id_pegawai[]" id="id_pegawai_${i}" data-placeholder="Select ID Pegawai">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('pegawai') as $row) : ?>
                                        <option value="<?= $row->id_pegawai ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>


                        <div class="form-group ">
                            <label for="slip_gaji" class="col-sm-2 control-label">Slip Gaji
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="slip_gaji_galery_${i}"></div>
                                <input class="data_file data_file_uuid" name="slip_gaji_uuid[]" id="slip_gaji_uuid_${i}" type="hidden" value="<?= set_value('slip_gaji_uuid'); ?>">
                                <input class="data_file" name="slip_gaji_name[]" id="slip_gaji_name_${i}" type="hidden" value="<?= set_value('slip_gaji_name'); ?>">
                            </div>
                        </div>`;
            $("#content-add-form").append(newField);
            $("#id_pegawai_" + i).select2()
            fineUpload(i)
        });

        function fineUpload(i) {
            $('#slip_gaji_galery_'+i).fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/pegawai/upload_slip_gaji_file',
                params: params
            },
            deleteFile: {
                enabled: true,
                endpoint: BASE_URL + '/administrator/pegawai/delete_slip_gaji_file',
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
                        var uuid = $('#slip_gaji_galery_'+i).fineUploader('getUuid', id);
                        $('#slip_gaji_uuid_'+i).val(uuid);
                        $('#slip_gaji_name_'+i).val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit: function(id, name) {
                    var uuid = $('#slip_gaji_uuid_'+i).val();
                    $.get(BASE_URL + '/administrator/pegawai/delete_slip_gaji_file/' + uuid);
                },
                onDeleteComplete: function(id, xhr, isError) {
                    if (isError == false) {
                        $('#slip_gaji_uuid_'+i).val('');
                        $('#slip_gaji_name_'+i).val('');
                    }
                }
            }
        }); /*end slip_gaji galery*/

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
                        window.location.href = BASE_URL + 'administrator/pegawai';
                    }
                });

            return false;
        }); /*end btn cancel*/

        $('.btn_save').click(function() {
            $('.message').fadeOut();

            var form_pegawai = $('#form_pegawai');
            var data_post = form_pegawai.serializeArray();
            var save_type = $(this).attr('data-stype');

            data_post.push({
                name: 'save_type',
                value: save_type
            });

            $('.loading').show();

            $.ajax({
                    url: BASE_URL + '/administrator/pegawai/add_slip_gaji_save',
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

        $('#slip_gaji_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/pegawai/upload_slip_gaji_file',
                params: params
            },
            deleteFile: {
                enabled: true,
                endpoint: BASE_URL + '/administrator/pegawai/delete_slip_gaji_file',
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
                        var uuid = $('#slip_gaji_galery').fineUploader('getUuid', id);
                        $('#slip_gaji_uuid').val(uuid);
                        $('#slip_gaji_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit: function(id, name) {
                    var uuid = $('#slip_gaji_uuid').val();
                    $.get(BASE_URL + '/administrator/pegawai/delete_slip_gaji_file/' + uuid);
                },
                onDeleteComplete: function(id, xhr, isError) {
                    if (isError == false) {
                        $('#slip_gaji_uuid').val('');
                        $('#slip_gaji_name').val('');
                    }
                }
            }
        }); /*end slip_gaji galery*/



    }); /*end doc ready*/
</script>