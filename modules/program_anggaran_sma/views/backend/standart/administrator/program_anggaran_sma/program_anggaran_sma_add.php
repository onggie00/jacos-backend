
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
        <i class="fa fa-money"></i> Program Anggaran Sma
        <small><?= cclang('new', ['Program Anggaran Sma']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/program_anggaran_sma'); ?>">Program Anggaran Sma</a></li>
        <li class="active"><?= cclang('new'); ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?= form_open('', [
                'name'    => 'form_program_anggaran_sma',
                'class'   => 'form-horizontal form-step',
                'id'      => 'form_program_anggaran_sma',
                'enctype' => 'multipart/form-data',
                'method'  => 'POST'
            ]); ?>

            <!-- Box 1: Pilih Program -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list-ol"></i> Pilih Program</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="nomor_program" class="col-sm-2 control-label">Nomor Program <i class="required">*</i></label>
                        <div class="col-sm-8">
                            <select class="form-control chosen chosen-select-deselect" onchange="get_program()" name="nomor_program" id="nomor_program" data-placeholder="Pilih Nomor Program">
                                <option value=""></option>
                                <?php foreach ($this->mymodel->withquery("SELECT * FROM program_anggaran WHERE is_active = '1' AND jenjang IN ('SMA','FT') ORDER BY jenjang, nomor_program", "result") as $row): ?>
                                <option value="<?= $row->nomor_program ?>"><?= $row->nomor_program . ' - ' . $row->nama_program . ' (' . $row->jenjang . ')'; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="info help-block">Pilih program anggaran SMA atau FT. Opsi menampilkan nomor, nama, dan jenjang program.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 2: Detail Program -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-info-circle"></i> Detail Program</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="jenis_kegiatan" class="col-sm-2 control-label">Jenis Kegiatan</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="jenis_kegiatan" id="jenis_kegiatan" placeholder="Jenis Kegiatan" value="<?= set_value('jenis_kegiatan'); ?>" readonly>
                            <small class="info help-block">Terisi otomatis dari master program.</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sub_jenis_kegiatan" class="col-sm-2 control-label">Sub Jenis Kegiatan</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="sub_jenis_kegiatan" id="sub_jenis_kegiatan" placeholder="Sub Jenis Kegiatan" value="<?= set_value('sub_jenis_kegiatan'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nama_program" class="col-sm-2 control-label">Nama Program <i class="required">*</i></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="nama_program" id="nama_program" placeholder="Nama Program" value="<?= set_value('nama_program'); ?>" readonly>
                            <small class="info help-block">Terisi otomatis dari master program.</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="tahun_ajaran" id="tahun_ajaran" placeholder="Tahun Ajaran" value="<?= set_value('tahun_ajaran'); ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_program" class="col-sm-2 control-label">Tanggal Program <i class="required">*</i></label>
                        <div class="col-sm-6">
                            <div class="input-group date">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control pull-right datepicker" name="tanggal_program" placeholder="Tanggal Program" id="tanggal_program">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 3: Anggaran -->
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-calculator"></i> Anggaran</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="nominal_okr_display" class="col-sm-2 control-label">Nominal OKR <i class="required">*</i></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon">Rp</div>
                                <input type="text" class="form-control" id="nominal_okr_display" placeholder="Nominal OKR" value="" readonly>
                            </div>
                            <input type="hidden" name="nominal_okr" id="nominal_okr" value="<?= set_value('nominal_okr'); ?>">
                            <small class="info help-block">Nominal OKR di master program. Terisi otomatis saat memilih nomor program.</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nominal_pengajuan_display" class="col-sm-2 control-label">Nominal Pengajuan <i class="required">*</i></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon">Rp</div>
                                <input type="text" class="form-control" id="nominal_pengajuan_display" placeholder="Nominal Pengajuan" value="" autocomplete="off">
                            </div>
                            <input type="hidden" name="nominal_pengajuan" id="nominal_pengajuan" value="<?= set_value('nominal_pengajuan'); ?>">
                            <input type="hidden" id="sisa_okr" value="0">
                            <small class="info help-block">
                                <b>Max. Saldo OKR untuk kegiatan ini adalah : <span id="sisa_okr_text">0</span></b>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 4: Informasi Kegiatan -->
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-file-text-o"></i> Informasi Kegiatan</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="dasar_pelaksanaan_kegiatan" class="col-sm-2 control-label">Dasar Pelaksanaan Kegiatan</label>
                        <div class="col-sm-8">
                            <textarea id="dasar_pelaksanaan_kegiatan" name="dasar_pelaksanaan_kegiatan" class="form-control" rows="3"><?= set_value('dasar_pelaksanaan_kegiatan'); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tema_kegiatan" class="col-sm-2 control-label">Tema Kegiatan</label>
                        <div class="col-sm-8">
                            <textarea id="tema_kegiatan" name="tema_kegiatan" class="form-control" rows="3"><?= set_value('tema_kegiatan'); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tujuan_kegiatan" class="col-sm-2 control-label">Tujuan Kegiatan</label>
                        <div class="col-sm-8">
                            <textarea id="tujuan_kegiatan" name="tujuan_kegiatan" class="form-control" rows="3"><?= set_value('tujuan_kegiatan'); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gambaran_acara_kegiatan" class="col-sm-2 control-label">Gambaran Acara Kegiatan</label>
                        <div class="col-sm-8">
                            <textarea id="gambaran_acara_kegiatan" name="gambaran_acara_kegiatan" class="form-control" rows="3"><?= set_value('gambaran_acara_kegiatan'); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hasil_yang_diharapkan" class="col-sm-2 control-label">Output / Hasil yang Diharapkan</label>
                        <div class="col-sm-8">
                            <textarea id="hasil_yang_diharapkan" name="hasil_yang_diharapkan" class="form-control" rows="3"><?= set_value('hasil_yang_diharapkan'); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tempat_dan_waktu_pelaksanaan" class="col-sm-2 control-label">Tempat &amp; Waktu Pelaksanaan</label>
                        <div class="col-sm-8">
                            <textarea id="tempat_dan_waktu_pelaksanaan" name="tempat_dan_waktu_pelaksanaan" class="form-control" rows="3"><?= set_value('tempat_dan_waktu_pelaksanaan'); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mitra_kegiatan" class="col-sm-2 control-label">Mitra Kegiatan</label>
                        <div class="col-sm-8">
                            <textarea id="mitra_kegiatan" name="mitra_kegiatan" class="form-control" rows="3"><?= set_value('mitra_kegiatan'); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="kepanitiaan" class="col-sm-2 control-label">Kepanitiaan</label>
                        <div class="col-sm-8">
                            <textarea id="kepanitiaan" name="kepanitiaan" class="form-control" rows="3"><?= set_value('kepanitiaan'); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 5: Upload Proposal -->
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-cloud-upload"></i> Upload Proposal</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="file_proposal_pengajuan" class="col-sm-2 control-label">Proposal Pengajuan</label>
                        <div class="col-sm-8">
                            <div id="program_anggaran_sma_file_proposal_pengajuan_galery"></div>
                            <input class="data_file" name="program_anggaran_sma_file_proposal_pengajuan_uuid" id="program_anggaran_sma_file_proposal_pengajuan_uuid" type="hidden" value="<?= set_value('program_anggaran_sma_file_proposal_pengajuan_uuid'); ?>">
                            <input class="data_file" name="program_anggaran_sma_file_proposal_pengajuan_name" id="program_anggaran_sma_file_proposal_pengajuan_name" type="hidden" value="<?= set_value('program_anggaran_sma_file_proposal_pengajuan_name'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="file_proposal_keuangan" class="col-sm-2 control-label">Proposal Keuangan</label>
                        <div class="col-sm-8">
                            <div id="program_anggaran_sma_file_proposal_keuangan_galery"></div>
                            <input class="data_file" name="program_anggaran_sma_file_proposal_keuangan_uuid" id="program_anggaran_sma_file_proposal_keuangan_uuid" type="hidden" value="<?= set_value('program_anggaran_sma_file_proposal_keuangan_uuid'); ?>">
                            <input class="data_file" name="program_anggaran_sma_file_proposal_keuangan_name" id="program_anggaran_sma_file_proposal_keuangan_name" type="hidden" value="<?= set_value('program_anggaran_sma_file_proposal_keuangan_name'); ?>">
                        </div>
                    </div>
                </div>
                <div class="box-footer text-right">
                    <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype="stay" title="<?= cclang('save_button'); ?> (Ctrl+s)">
                        <i class="fa fa-save"></i> <?= cclang('save_button'); ?>
                    </button>
                    <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save" data-stype="back" title="<?= cclang('save_and_go_the_list_button'); ?> (Ctrl+d)">
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
            </div>

            <?= form_close(); ?>
        </div>
    </div>
</section>

<!-- Page script -->
<script>
    function formatRupiah(angka) {
        if (!angka || angka == 0) return '0';
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    function get_program(){
        var nomor_program = $('#nomor_program').val();
        if (!nomor_program) {
            $('#nama_program').val('');
            $('#tahun_ajaran').val('');
            $('#jenis_kegiatan').val('');
            $('#nominal_okr').val('');
            $('#nominal_okr_display').val('');
            $('#sisa_okr').val(0);
            $('#sisa_okr_text').html('0');
            return;
        }

        $.ajax({
            type: "GET",
            url: BASE_URL + "administrator/program_anggaran_sma/get_program/" + nomor_program,
            success: function(data) {
                var result = $.parseJSON(data);
                $('#nama_program').val(result.nama_program);
                $('#tahun_ajaran').val(result.tahun_ajaran);
                $('#jenis_kegiatan').val(result.jenis_kegiatan);
                $('#nominal_okr').val(result.nominal_okr);
                $('#nominal_okr_display').val(formatRupiah(result.nominal_okr));
                $('#sisa_okr').val(result.sisa_okr);
                $('#sisa_okr_text').html(formatRupiah(result.sisa_okr));
            }
        });
    }

    function resetForm() {
        $('#form_program_anggaran_sma').trigger('reset');
        $('.chosen').val('').trigger('chosen:updated');
        $('#nominal_okr').val('');
        $('#nominal_okr_display').val('');
        $('#nominal_pengajuan').val('');
        $('#nominal_pengajuan_display').val('');
        $('#sisa_okr').val(0);
        $('#sisa_okr_text').html('0');
    }

    $(document).ready(function(){
        // Format nominal pengajuan saat user mengetik
        $('#nominal_pengajuan_display').on('input', function() {
            var raw = $(this).val().replace(/\D/g, '');
            $(this).val(formatRupiah(raw));
            $('#nominal_pengajuan').val(raw);

            var sisa = parseInt($('#sisa_okr').val()) || 0;
            if (parseInt(raw) > sisa) {
                $(this).parents('.form-group').addClass('has-error');
            } else {
                $(this).parents('.form-group').removeClass('has-error');
            }
        });

        // Repopulate formatted pengajuan jika ada old value (validation fail)
        var oldPengajuan = $('#nominal_pengajuan').val();
        if (oldPengajuan) {
            $('#nominal_pengajuan_display').val(formatRupiah(oldPengajuan));
        }

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
            },
            function(isConfirm){
                if (isConfirm) {
                    window.location.href = BASE_URL + 'administrator/program_anggaran_sma';
                }
            });
            return false;
        });

        $('.btn_save').click(function(){
            $('.message').fadeOut();

            var form_program_anggaran_sma = $('#form_program_anggaran_sma');
            var data_post = form_program_anggaran_sma.serializeArray();
            var save_type = $(this).attr('data-stype');

            data_post.push({name: 'save_type', value: save_type});

            $('.loading').show();

            $.ajax({
                url: BASE_URL + '/administrator/program_anggaran_sma/add_save',
                type: 'POST',
                dataType: 'json',
                data: data_post,
            })
            .done(function(res) {
                $('form').find('.form-group').removeClass('has-error');
                $('.steps li').removeClass('error');
                $('form').find('.error-input').remove();
                if(res.success) {
                    var id_file_proposal_pengajuan = $('#program_anggaran_sma_file_proposal_pengajuan_galery').find('li').attr('qq-file-id');
                    var id_file_proposal_keuangan = $('#program_anggaran_sma_file_proposal_keuangan_galery').find('li').attr('qq-file-id');

                    if (save_type == 'back') {
                        window.location.href = res.redirect;
                        return;
                    }

                    $('.message').printMessage({message : res.message});
                    $('.message').fadeIn();
                    resetForm();
                    if (typeof id_file_proposal_pengajuan !== 'undefined') {
                        $('#program_anggaran_sma_file_proposal_pengajuan_galery').fineUploader('deleteFile', id_file_proposal_pengajuan);
                    }
                    if (typeof id_file_proposal_keuangan !== 'undefined') {
                        $('#program_anggaran_sma_file_proposal_keuangan_galery').fineUploader('deleteFile', id_file_proposal_keuangan);
                    }
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
                    $('.message').printMessage({message : res.message, type : 'warning'});
                }
            })
            .fail(function() {
                $('.message').printMessage({message : 'Error save data', type : 'warning'});
            })
            .always(function() {
                $('.loading').hide();
                $('html, body').animate({ scrollTop: $(document).height() }, 2000);
            });

            return false;
        });

        var params = {};
        params[csrf] = token;

        $('#program_anggaran_sma_file_proposal_pengajuan_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/program_anggaran_sma/upload_file_proposal_pengajuan_file',
                params : params
            },
            deleteFile: {
                enabled: true,
                endpoint: BASE_URL + '/administrator/program_anggaran_sma/delete_file_proposal_pengajuan_file',
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            multiple : false,
            validation: {
                allowedExtensions: ["*"],
                sizeLimit : 0,
            },
            showMessage: function(msg) {
                toastr['error'](msg);
            },
            callbacks: {
                onComplete : function(id, name, xhr) {
                    if (xhr.success) {
                        var uuid = $('#program_anggaran_sma_file_proposal_pengajuan_galery').fineUploader('getUuid', id);
                        $('#program_anggaran_sma_file_proposal_pengajuan_uuid').val(uuid);
                        $('#program_anggaran_sma_file_proposal_pengajuan_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit : function(id, name) {
                    var uuid = $('#program_anggaran_sma_file_proposal_pengajuan_uuid').val();
                    $.get(BASE_URL + '/administrator/program_anggaran_sma/delete_file_proposal_pengajuan_file/' + uuid);
                },
                onDeleteComplete : function(id, xhr, isError) {
                    if (isError == false) {
                        $('#program_anggaran_sma_file_proposal_pengajuan_uuid').val('');
                        $('#program_anggaran_sma_file_proposal_pengajuan_name').val('');
                    }
                }
            }
        });

        $('#program_anggaran_sma_file_proposal_keuangan_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/program_anggaran_sma/upload_file_proposal_keuangan_file',
                params : params
            },
            deleteFile: {
                enabled: true,
                endpoint: BASE_URL + '/administrator/program_anggaran_sma/delete_file_proposal_keuangan_file',
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            multiple : false,
            validation: {
                allowedExtensions: ["*"],
                sizeLimit : 0,
            },
            showMessage: function(msg) {
                toastr['error'](msg);
            },
            callbacks: {
                onComplete : function(id, name, xhr) {
                    if (xhr.success) {
                        var uuid = $('#program_anggaran_sma_file_proposal_keuangan_galery').fineUploader('getUuid', id);
                        $('#program_anggaran_sma_file_proposal_keuangan_uuid').val(uuid);
                        $('#program_anggaran_sma_file_proposal_keuangan_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit : function(id, name) {
                    var uuid = $('#program_anggaran_sma_file_proposal_keuangan_uuid').val();
                    $.get(BASE_URL + '/administrator/program_anggaran_sma/delete_file_proposal_keuangan_file/' + uuid);
                },
                onDeleteComplete : function(id, xhr, isError) {
                    if (isError == false) {
                        $('#program_anggaran_sma_file_proposal_keuangan_uuid').val('');
                        $('#program_anggaran_sma_file_proposal_keuangan_name').val('');
                    }
                }
            }
        });

    });
</script>
