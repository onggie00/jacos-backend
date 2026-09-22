
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
        <i class="fa fa-money"></i> Program Anggaran SD
        <small>Edit Program Anggaran SD</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/program_anggaran_sd'); ?>">Program Anggaran SD</a></li>
        <li class="active">Edit</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?= form_open(base_url('administrator/program_anggaran_sd/edit_save/'.$this->uri->segment(4)), [
                'name'    => 'form_program_anggaran_sd',
                'class'   => 'form-horizontal form-step',
                'id'      => 'form_program_anggaran_sd',
                'method'  => 'POST'
            ]); ?>

            <!-- Box 1: Program -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list-ol"></i> Program</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="nomor_program" class="col-sm-2 control-label">Nomor Program <i class="required">*</i></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="nomor_program" id="nomor_program" placeholder="Nomor Program" value="<?= set_value('nomor_program', $program_anggaran_sd->nomor_program); ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="jenis_kegiatan" class="col-sm-2 control-label">Jenis Kegiatan</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="jenis_kegiatan" id="jenis_kegiatan" placeholder="Jenis Kegiatan" value="<?= set_value('jenis_kegiatan', $program_anggaran_sd->jenis_kegiatan); ?>" readonly>
                            <small class="info help-block"><b>Input Jenis Kegiatan</b> Max Length : 100.</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sub_jenis_kegiatan" class="col-sm-2 control-label">Sub Jenis Kegiatan</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="sub_jenis_kegiatan" id="sub_jenis_kegiatan" placeholder="Sub Jenis Kegiatan" value="<?= set_value('sub_jenis_kegiatan', $program_anggaran_sd->sub_jenis_kegiatan); ?>" <?php echo ($program_anggaran_sd->status_pengajuan == 4 || $program_anggaran_sd->status_pengajuan == 6) ? "readonly" : "" ; ?>>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nama_program" class="col-sm-2 control-label">Nama Program <i class="required">*</i></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="nama_program" id="nama_program" placeholder="Nama Program" value="<?= set_value('nama_program', $program_anggaran_sd->nama_program); ?>" readonly>
                            <small class="info help-block"><b>Input Nama Program</b> Max Length : 255.</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="tahun_ajaran" id="tahun_ajaran" placeholder="Tahun Ajaran" value="<?= set_value('tahun_ajaran', $program_anggaran_sd->tahun_ajaran); ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_program" class="col-sm-2 control-label">Tanggal Program <i class="required">*</i></label>
                        <div class="col-sm-6">
                            <div class="input-group date">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control pull-right datepicker" name="tanggal_program" placeholder="Tanggal Program" id="tanggal_program" value="<?= set_value('program_anggaran_sd_tanggal_program_name', $program_anggaran_sd->tanggal_program); ?>" <?php echo ($program_anggaran_sd->status_pengajuan == 6) ? "readonly" : "" ; ?>>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 2: Anggaran -->
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
                            <input type="hidden" name="nominal_okr" id="nominal_okr" value="<?= set_value('nominal_okr', $program_anggaran_sd->nominal_okr); ?>">
                            <small class="info help-block">Nominal OKR pada saat pengajuan dibuat.</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nominal_pengajuan_display" class="col-sm-2 control-label">Nominal Pengajuan <i class="required">*</i></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon">Rp</div>
                                <input type="text" class="form-control" id="nominal_pengajuan_display" placeholder="Nominal Pengajuan" value="" autocomplete="off" <?php echo ($program_anggaran_sd->status_pengajuan == 4 || $program_anggaran_sd->status_pengajuan == 6) ? "readonly" : "" ; ?>>
                            </div>
                            <input type="hidden" name="nominal_pengajuan" id="nominal_pengajuan" value="<?= set_value('nominal_pengajuan', $program_anggaran_sd->nominal_pengajuan); ?>">
                            <?php
                                $okr = $program_anggaran_sd->nominal_okr;
                                $this->db->where("nomor_program", $program_anggaran_sd->nomor_program);
                                $this->db->where("jenis_kegiatan", $program_anggaran_sd->jenis_kegiatan);
                                $this->db->where("id !=", $program_anggaran_sd->id);
                                $get_program_by_jenis_kegiatan = $this->db->get("program_anggaran_sd")->result();
                                $okr_terpakai = 0;
                                foreach ($get_program_by_jenis_kegiatan as $key => $value) {
                                    $okr_terpakai = $okr_terpakai + $value->nominal_pengajuan;
                                }
                                $okr = $okr - $okr_terpakai;
                            ?>
                            <input type="hidden" id="sisa_okr" value="<?= $okr; ?>">
                            <small class="info help-block">
                                <b>Max. Saldo OKR untuk kegiatan ini adalah : <span id="sisa_okr_text"><?= number_format($okr,0,"","."); ?></span></b>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 3: Informasi Kegiatan -->
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-file-text-o"></i> Informasi Kegiatan</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="dasar_pelaksanaan_kegiatan" class="col-sm-2 control-label">Dasar Pelaksanaan Kegiatan</label>
                        <div class="col-sm-8">
                            <textarea id="dasar_pelaksanaan_kegiatan" name="dasar_pelaksanaan_kegiatan" class="form-control" rows="3"><?= set_value('dasar_pelaksanaan_kegiatan', $program_anggaran_sd->dasar_pelaksanaan_kegiatan); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tema_kegiatan" class="col-sm-2 control-label">Tema Kegiatan</label>
                        <div class="col-sm-8">
                            <textarea id="tema_kegiatan" name="tema_kegiatan" class="form-control" rows="3"><?= set_value('tema_kegiatan', $program_anggaran_sd->tema_kegiatan); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tujuan_kegiatan" class="col-sm-2 control-label">Tujuan Kegiatan</label>
                        <div class="col-sm-8">
                            <textarea id="tujuan_kegiatan" name="tujuan_kegiatan" class="form-control" rows="3"><?= set_value('tujuan_kegiatan', $program_anggaran_sd->tujuan_kegiatan); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gambaran_acara_kegiatan" class="col-sm-2 control-label">Gambaran Acara Kegiatan</label>
                        <div class="col-sm-8">
                            <textarea id="gambaran_acara_kegiatan" name="gambaran_acara_kegiatan" class="form-control" rows="3"><?= set_value('gambaran_acara_kegiatan', $program_anggaran_sd->gambaran_acara_kegiatan); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hasil_yang_diharapkan" class="col-sm-2 control-label">Output / Hasil yang Diharapkan</label>
                        <div class="col-sm-8">
                            <textarea id="hasil_yang_diharapkan" name="hasil_yang_diharapkan" class="form-control" rows="3"><?= set_value('hasil_yang_diharapkan', $program_anggaran_sd->hasil_yang_diharapkan); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tempat_dan_waktu_pelaksanaan" class="col-sm-2 control-label">Tempat &amp; Waktu Pelaksanaan</label>
                        <div class="col-sm-8">
                            <textarea id="tempat_dan_waktu_pelaksanaan" name="tempat_dan_waktu_pelaksanaan" class="form-control" rows="3"><?= set_value('tempat_dan_waktu_pelaksanaan', $program_anggaran_sd->tempat_dan_waktu_pelaksanaan); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mitra_kegiatan" class="col-sm-2 control-label">Mitra Kegiatan</label>
                        <div class="col-sm-8">
                            <textarea id="mitra_kegiatan" name="mitra_kegiatan" class="form-control" rows="3"><?= set_value('mitra_kegiatan', $program_anggaran_sd->mitra_kegiatan); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="kepanitiaan" class="col-sm-2 control-label">Kepanitiaan</label>
                        <div class="col-sm-8">
                            <textarea id="kepanitiaan" name="kepanitiaan" class="form-control" rows="3"><?= set_value('kepanitiaan', $program_anggaran_sd->kepanitiaan); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 4: Upload Proposal -->
            <div class="box box-danger" style="<?php echo ($program_anggaran_sd->status_pengajuan != 1 && $program_anggaran_sd->status_pengajuan != 5) ? "display:none;" : "" ; ?>">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-cloud-upload"></i> Upload Proposal</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="file_proposal_pengajuan" class="col-sm-2 control-label">Proposal Pengajuan</label>
                        <div class="col-sm-8">
                            <div id="program_anggaran_sd_file_proposal_pengajuan_galery"></div>
                            <input class="data_file data_file_uuid" name="program_anggaran_sd_file_proposal_pengajuan_uuid" id="program_anggaran_sd_file_proposal_pengajuan_uuid" type="hidden" value="<?= set_value('program_anggaran_sd_file_proposal_pengajuan_uuid'); ?>">
                            <input class="data_file" name="program_anggaran_sd_file_proposal_pengajuan_name" id="program_anggaran_sd_file_proposal_pengajuan_name" type="hidden" value="<?= set_value('program_anggaran_sd_file_proposal_pengajuan_name', $program_anggaran_sd->file_proposal_pengajuan); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="file_proposal_keuangan" class="col-sm-2 control-label">Proposal Keuangan</label>
                        <div class="col-sm-8">
                            <div id="program_anggaran_sd_file_proposal_keuangan_galery"></div>
                            <input class="data_file data_file_uuid" name="program_anggaran_sd_file_proposal_keuangan_uuid" id="program_anggaran_sd_file_proposal_keuangan_uuid" type="hidden" value="<?= set_value('program_anggaran_sd_file_proposal_keuangan_uuid'); ?>">
                            <input class="data_file" name="program_anggaran_sd_file_proposal_keuangan_name" id="program_anggaran_sd_file_proposal_keuangan_name" type="hidden" value="<?= set_value('program_anggaran_sd_file_proposal_keuangan_name', $program_anggaran_sd->file_proposal_keuangan); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 5: Pencairan Dana -->
            <div class="box box-info" style="<?php echo ($this->session->userdata('user_keuangan') != true || $program_anggaran_sd->status_pengajuan != 4) ? "display:none;" : "" ; ?>">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-calendar-check-o"></i> Pencairan Dana</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="tanggal_pencairan" class="col-sm-2 control-label">Tanggal Pencairan Dana</label>
                        <div class="col-sm-6">
                            <div class="input-group date">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control pull-right datepicker" name="tanggal_pencairan" placeholder="Tanggal Pencairan Dana" id="tanggal_pencairan" value="<?= set_value('program_anggaran_sd_tanggal_pencairan_name', $program_anggaran_sd->tanggal_pencairan); ?>" <?php echo ($this->session->userdata('user_keuangan') != true || $program_anggaran_sd->status_pengajuan != 4) ? "readonly" : "" ; ?>>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 6: Upload Laporan -->
            <div class="box box-warning" style="<?php echo (($program_anggaran_sd->status_pengajuan != 4 && $program_anggaran_sd->status_pengajuan != 5 && $program_anggaran_sd->status_pengajuan != 6) ) ? "display:none;" : "" ; ?>">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-file-archive-o"></i> Upload Laporan</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="file_laporan_kegiatan" class="col-sm-2 control-label">Laporan Kegiatan</label>
                        <div class="col-sm-8">
                            <div id="program_anggaran_sd_file_laporan_kegiatan_galery"></div>
                            <input class="data_file data_file_uuid" name="program_anggaran_sd_file_laporan_kegiatan_uuid" id="program_anggaran_sd_file_laporan_kegiatan_uuid" type="hidden" value="<?= set_value('program_anggaran_sd_file_laporan_kegiatan_uuid'); ?>">
                            <input class="data_file" name="program_anggaran_sd_file_laporan_kegiatan_name" id="program_anggaran_sd_file_laporan_kegiatan_name" type="hidden" value="<?= set_value('program_anggaran_sd_file_laporan_kegiatan_name', $program_anggaran_sd->file_laporan_kegiatan); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="file_laporan_keuangan" class="col-sm-2 control-label">Laporan Keuangan Kegiatan</label>
                        <div class="col-sm-8">
                            <div id="program_anggaran_sd_file_laporan_keuangan_galery"></div>
                            <div id="program_anggaran_sd_file_laporan_keuangan_galery_listed">
                                <?php foreach ((array) explode(',', $program_anggaran_sd->file_laporan_keuangan) as $idx => $filename): ?>
                                    <input type="hidden" class="listed_file_uuid" name="program_anggaran_sd_file_laporan_keuangan_uuid[<?= $idx ?>]" value="" /><input type="hidden" class="listed_file_name" name="program_anggaran_sd_file_laporan_keuangan_name[<?= $idx ?>]" value="<?= $filename; ?>" />
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 7: Tombol Aksi -->
            <div class="box box-default">
                <div class="box-body text-right">
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
                    <div class="message"></div>
                </div>
            </div>

            <?= form_close(); ?>
        </div>
    </div>
</section>
<!-- /.content -->
<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
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

    function parseErrorField(errors) {
        $.each(errors, function(index, val) {
            $('form #' + index).parents('.form-group').addClass('has-error');
            $('form #' + index).parents('.form-group').find('small').prepend('<div class="error-input">' + val + '</div>');
        });
    }

    $(document).ready(function(){

        // Format OKR display
        $('#nominal_okr_display').val(formatRupiah($('#nominal_okr').val()));

        // Format nominal pengajuan saat user mengetik
        $('#nominal_pengajuan_display').on('input', function() {
            if ($(this).prop('readonly')) return;
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

        // Repopulate formatted pengajuan on load
        var oldPengajuan = $('#nominal_pengajuan').val();
        if (oldPengajuan) {
            $('#nominal_pengajuan_display').val(formatRupiah(oldPengajuan));
        }

        $('#btn_cancel').click(function(){
            swal({
                title: "Are you sure?",
                text: "the data that you have created will be in the exhaust!",
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
                    window.location.href = BASE_URL + 'administrator/program_anggaran_sd';
                }
            });
            return false;
        });

        $('.btn_save').click(function(){
            $('.message').fadeOut();

            var form_program_anggaran_sd = $('#form_program_anggaran_sd');
            var data_post = form_program_anggaran_sd.serializeArray();
            var save_type = $(this).attr('data-stype');
            data_post.push({name: 'save_type', value: save_type});

            $('.loading').show();

            $.ajax({
                url: form_program_anggaran_sd.attr('action'),
                type: 'POST',
                dataType: 'json',
                data: data_post,
            })
            .done(function(res) {
                $('form').find('.form-group').removeClass('has-error');
                $('form').find('.error-input').remove();
                $('.steps li').removeClass('error');
                if(res.success) {
                    var id = $('#program_anggaran_sd_image_galery').find('li').attr('qq-file-id');
                    if (save_type == 'back') {
                        window.location.href = res.redirect;
                        return;
                    }

                    $('.message').printMessage({message : res.message});
                    $('.message').fadeIn();
                    $('.data_file_uuid').val('');

                } else {
                    if (res.errors) {
                        parseErrorField(res.errors);
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

        $('#program_anggaran_sd_file_proposal_pengajuan_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/program_anggaran_sd/upload_file_proposal_pengajuan_file',
                params : params
            },
            deleteFile: {
                enabled: true,
                endpoint: BASE_URL + '/administrator/program_anggaran_sd/delete_file_proposal_pengajuan_file'
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            session : {
                endpoint: BASE_URL + 'administrator/program_anggaran_sd/get_file_proposal_pengajuan_file/<?= $program_anggaran_sd->id; ?>',
                refreshOnRequest:true
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
                        var uuid = $('#program_anggaran_sd_file_proposal_pengajuan_galery').fineUploader('getUuid', id);
                        $('#program_anggaran_sd_file_proposal_pengajuan_uuid').val(uuid);
                        $('#program_anggaran_sd_file_proposal_pengajuan_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit : function(id, name) {
                    var uuid = $('#program_anggaran_sd_file_proposal_pengajuan_uuid').val();
                    $.get(BASE_URL + '/administrator/program_anggaran_sd/delete_file_proposal_pengajuan_file/' + uuid);
                },
                onDeleteComplete : function(id, xhr, isError) {
                    if (isError == false) {
                        $('#program_anggaran_sd_file_proposal_pengajuan_uuid').val('');
                        $('#program_anggaran_sd_file_proposal_pengajuan_name').val('');
                    }
                }
            }
        });

        $('#program_anggaran_sd_file_proposal_keuangan_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/program_anggaran_sd/upload_file_proposal_keuangan_file',
                params : params
            },
            deleteFile: {
                enabled: true,
                endpoint: BASE_URL + '/administrator/program_anggaran_sd/delete_file_proposal_keuangan_file'
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            session : {
                endpoint: BASE_URL + 'administrator/program_anggaran_sd/get_file_proposal_keuangan_file/<?= $program_anggaran_sd->id; ?>',
                refreshOnRequest:true
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
                        var uuid = $('#program_anggaran_sd_file_proposal_keuangan_galery').fineUploader('getUuid', id);
                        $('#program_anggaran_sd_file_proposal_keuangan_uuid').val(uuid);
                        $('#program_anggaran_sd_file_proposal_keuangan_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit : function(id, name) {
                    var uuid = $('#program_anggaran_sd_file_proposal_keuangan_uuid').val();
                    $.get(BASE_URL + '/administrator/program_anggaran_sd/delete_file_proposal_keuangan_file/' + uuid);
                },
                onDeleteComplete : function(id, xhr, isError) {
                    if (isError == false) {
                        $('#program_anggaran_sd_file_proposal_keuangan_uuid').val('');
                        $('#program_anggaran_sd_file_proposal_keuangan_name').val('');
                    }
                }
            }
        });

        $('#program_anggaran_sd_file_laporan_kegiatan_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/program_anggaran_sd/upload_file_laporan_kegiatan_file',
                params : params
            },
            deleteFile: {
                enabled: true,
                endpoint: BASE_URL + '/administrator/program_anggaran_sd/delete_file_laporan_kegiatan_file'
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            session : {
                endpoint: BASE_URL + 'administrator/program_anggaran_sd/get_file_laporan_kegiatan_file/<?= $program_anggaran_sd->id; ?>',
                refreshOnRequest:true
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
                        var uuid = $('#program_anggaran_sd_file_laporan_kegiatan_galery').fineUploader('getUuid', id);
                        $('#program_anggaran_sd_file_laporan_kegiatan_uuid').val(uuid);
                        $('#program_anggaran_sd_file_laporan_kegiatan_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit : function(id, name) {
                    var uuid = $('#program_anggaran_sd_file_laporan_kegiatan_uuid').val();
                    $.get(BASE_URL + '/administrator/program_anggaran_sd/delete_file_laporan_kegiatan_file/' + uuid);
                },
                onDeleteComplete : function(id, xhr, isError) {
                    if (isError == false) {
                        $('#program_anggaran_sd_file_laporan_kegiatan_uuid').val('');
                        $('#program_anggaran_sd_file_laporan_kegiatan_name').val('');
                    }
                }
            }
        });

        $('#program_anggaran_sd_file_laporan_keuangan_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/program_anggaran_sd/upload_file_laporan_keuangan_file',
                params : params
            },
            deleteFile: {
                enabled: true,
                endpoint: BASE_URL + '/administrator/program_anggaran_sd/delete_file_laporan_keuangan_file',
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            session : {
                endpoint: BASE_URL + 'administrator/program_anggaran_sd/get_file_laporan_keuangan_file/<?= $program_anggaran_sd->id; ?>',
                refreshOnRequest:true
            },
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
                        var uuid = $('#program_anggaran_sd_file_laporan_keuangan_galery').fineUploader('getUuid', id);
                        $('#program_anggaran_sd_file_laporan_keuangan_galery_listed').append('<input type="hidden" class="listed_file_uuid" name="program_anggaran_sd_file_laporan_keuangan_uuid['+id+']" value="'+uuid+'" /><input type="hidden" class="listed_file_name" name="program_anggaran_sd_file_laporan_keuangan_name['+id+']" value="'+xhr.uploadName+'" />');
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onDeleteComplete : function(id, xhr, isError) {
                    if (isError == false) {
                        $('#program_anggaran_sd_file_laporan_keuangan_galery_listed').find('.listed_file_uuid[name="program_anggaran_sd_file_laporan_keuangan_uuid['+id+']"]').remove();
                        $('#program_anggaran_sd_file_laporan_keuangan_galery_listed').find('.listed_file_name[name="program_anggaran_sd_file_laporan_keuangan_name['+id+']"]').remove();
                    }
                }
            }
        });

        async function chain(){
        }

        chain();

    });
</script>
