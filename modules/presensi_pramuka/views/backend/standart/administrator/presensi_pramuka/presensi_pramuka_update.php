<style>
.nilai-slider-wrap { margin: 5px 0; }
.nilai-slider-wrap input[type="range"] { width: 80%; cursor: pointer; }
.nilai-display { display: inline-block; min-width: 45px; text-align: center; font-weight: 700; font-size: 16px; padding: 2px 8px; border-radius: 4px; color: #fff; background: #27ae60; }
.badge-predikat { padding: 3px 8px; border-radius: 3px; font-weight: bold; font-size: 11px; }
.badge-a { background: #27ae60; color: #fff; }
.badge-b { background: #3498db; color: #fff; }
.badge-c { background: #f39c12; color: #fff; }
.badge-d { background: #e67e22; color: #fff; }
.badge-e { background: #e74c3c; color: #fff; }
</style>

<!-- Content Header -->
<section class="content-header">
    <h1>
        Presensi Pramuka <small><?= cclang('update', ['Presensi Pramuka']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a href="<?= site_url('administrator/presensi_pramuka'); ?>">Presensi Pramuka</a></li>
        <li class="active"><?= cclang('update'); ?></li>
    </ol>
</section>

<!-- Main content -->
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
                            <h3 class="widget-user-username">Presensi Pramuka</h3>
                            <h5 class="widget-user-desc"><?= cclang('update', ['Presensi Pramuka']); ?></h5>
                            <hr>
                        </div>
                    <?= form_open(base_url('administrator/presensi_pramuka/edit_save/' . $this->uri->segment(4)), array(
                        'name'    => 'form_presensi_pramuka',
                        'class'   => 'form-horizontal',
                        'id'      => 'form_presensi_pramuka',
                        'enctype' => 'multipart/form-data',
                        'method'  => 'POST'
                    )); ?>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tanggal <i class="required">*</i></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control datetimepicker" name="tanggal" id="tanggal" placeholder="Pilih tanggal" value="<?= set_value('tanggal', $presensi_pramuka->tanggal); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Jenjang <i class="required">*</i></label>
                        <div class="col-sm-4">
                            <?php if (!empty($is_locked) && count($list_jenjang) == 1): ?>
                                <input type="text" class="form-control" value="<?= $list_jenjang[0]['label']; ?>" disabled>
                                <input type="hidden" name="jenjang" id="jenjang" value="<?= $list_jenjang[0]['code']; ?>">
                            <?php else: ?>
                                <select class="form-control chosen chosen-select" name="jenjang" id="jenjang" data-placeholder="Pilih Jenjang" required>
                                    <option value=""></option>
                                    <?php foreach ($list_jenjang as $j): ?>
                                    <option value="<?= $j['code']; ?>" <?= ($presensi_pramuka->jenjang == $j['code']) ? 'selected' : ''; ?>><?= $j['label']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tingkatan</label>
                        <div class="col-sm-4">
                            <select class="form-control chosen chosen-select" name="id_tingkatan" id="id_tingkatan" data-placeholder="Pilih Tingkatan">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Kelas</label>
                        <div class="col-sm-4">
                            <select class="form-control chosen chosen-select" name="id_kelas" id="id_kelas" data-placeholder="Pilih Kelas">
                                <option value=""></option>
                                <?php if (!empty($kelas_list)): ?>
                                    <?php foreach ($kelas_list as $k): ?>
                                    <option value="<?= $k->id_kelas; ?>" <?= ($presensi_pramuka->kelas == $k->label) ? 'selected' : ''; ?>><?= $k->label; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Siswa <i class="required">*</i></label>
                        <div class="col-sm-4">
                            <select class="form-control chosen chosen-select" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Pilih Siswa" required>
                                <option value="<?= $presensi_pramuka->id_siswa_aktif; ?>" selected><?= $presensi_pramuka->nama_lengkap; ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status Hadir <i class="required">*</i></label>
                        <div class="col-sm-4">
                            <select class="form-control chosen chosen-select" name="status_hadir" id="status_hadir" data-placeholder="Pilih Status" required>
                                <option value="Hadir" <?= ($presensi_pramuka->status_hadir == 'Hadir') ? 'selected' : ''; ?>>Hadir</option>
                                <option value="Terlambat" <?= ($presensi_pramuka->status_hadir == 'Terlambat') ? 'selected' : ''; ?>>Terlambat</option>
                                <option value="Sakit" <?= ($presensi_pramuka->status_hadir == 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                                <option value="Izin" <?= ($presensi_pramuka->status_hadir == 'Izin') ? 'selected' : ''; ?>>Izin</option>
                                <option value="Alfa" <?= ($presensi_pramuka->status_hadir == 'Alfa') ? 'selected' : ''; ?>>Alfa</option>
                            </select>
                        </div>
                    </div>

                    <div id="nilai_section">
                        <!-- 1. Kehadiran -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Nilai Kehadiran</label>
                            <div class="col-sm-6">
                                <div class="nilai-slider-wrap">
                                    <input type="range" name="kehadiran" id="kehadiran" min="0" max="100" value="<?= (int) $presensi_pramuka->kehadiran; ?>" step="1">
                                    <span class="nilai-display" id="kehadiran_display"><?= (int) $presensi_pramuka->kehadiran; ?></span>
                                </div>
                                <small class="text-muted">Rubrik: Hadir (100), Terlambat (75), Sakit/Izin (50), Alfa (0)</small>
                            </div>
                        </div>

                        <!-- 2. Kelengkapan Atribut -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Kelengkapan Atribut</label>
                            <div class="col-sm-4">
                                <select class="form-control chosen chosen-select" name="status_kelengkapan" id="status_kelengkapan">
                                    <option value="Lengkap" <?= ($presensi_pramuka->status_kelengkapan == 'Lengkap') ? 'selected' : ''; ?>>Lengkap (Bedge, Hasduk & Ring)</option>
                                    <option value="Kurang Lengkap" <?= ($presensi_pramuka->status_kelengkapan == 'Kurang Lengkap') ? 'selected' : ''; ?>>Kurang Lengkap</option>
                                    <option value="Tidak Menggunakan Atribut" <?= ($presensi_pramuka->status_kelengkapan == 'Tidak Menggunakan Atribut') ? 'selected' : ''; ?>>Tidak Menggunakan Atribut</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Nilai Atribut</label>
                            <div class="col-sm-6">
                                <div class="nilai-slider-wrap">
                                    <input type="range" name="kelengkapan" id="kelengkapan" min="0" max="100" value="<?= (int) $presensi_pramuka->kelengkapan; ?>" step="1">
                                    <span class="nilai-display" id="kelengkapan_display"><?= (int) $presensi_pramuka->kelengkapan; ?></span>
                                </div>
                                <small class="text-muted">Rubrik: Lengkap (100), Kurang Lengkap (90), Tidak Menggunakan Atribut (80)</small>
                            </div>
                        </div>

                        <!-- 3. Keaktifan -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Keaktifan</label>
                            <div class="col-sm-4">
                                <select class="form-control chosen chosen-select" name="status_keaktifan" id="status_keaktifan">
                                    <option value="Aktif" <?= ($presensi_pramuka->status_keaktifan == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="Kurang Aktif" <?= ($presensi_pramuka->status_keaktifan == 'Kurang Aktif') ? 'selected' : ''; ?>>Kurang Aktif</option>
                                    <option value="Tidak Aktif" <?= ($presensi_pramuka->status_keaktifan == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Nilai Keaktifan</label>
                            <div class="col-sm-6">
                                <div class="nilai-slider-wrap">
                                    <input type="range" name="keaktifan" id="keaktifan" min="0" max="100" value="<?= (int) $presensi_pramuka->keaktifan; ?>" step="1">
                                    <span class="nilai-display" id="keaktifan_display"><?= (int) $presensi_pramuka->keaktifan; ?></span>
                                </div>
                                <small class="text-muted">Rubrik: Aktif (100), Kurang Aktif (90), Tidak Aktif (80)</small>
                            </div>
                        </div>

                        <!-- Total & Preview -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Total Nilai & Predikat</label>
                            <div class="col-sm-6">
                                <p class="form-control-static" style="font-size:16px">
                                    <strong id="preview_total"><?= $presensi_pramuka->total_nilai; ?></strong> —
                                    <?php
                                    $p_info = get_predikat_pramuka($presensi_pramuka->total_nilai);
                                    $cls = $p_info['predikat'] == 'A' ? 'badge-a' : ($p_info['predikat'] == 'B' ? 'badge-b' : ($p_info['predikat'] == 'C' ? 'badge-c' : ($p_info['predikat'] == 'D' ? 'badge-d' : 'badge-e')));
                                    ?>
                                    <span class="badge-predikat <?= $cls; ?>" id="preview_predikat"><?= $p_info['predikat'] . ' (' . $p_info['deskripsi'] . ')'; ?></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="message"></div>

                    <div class="row-fluid col-md-7">
                        <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="Simpan (Ctrl+s)"><i class="fa fa-save"></i> <?= cclang('save_button'); ?></button>
                        <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save" data-stype='back' title="Simpan dan kembali ke daftar (Ctrl+d)"><i class="ion ion-ios-list-outline"></i> <?= cclang('save_and_go_the_list_button'); ?></a>
                        <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="Batal (Ctrl+x)"><i class="fa fa-undo"></i> <?= cclang('cancel_button'); ?></a>
                        <span class="loading loading-hide"><img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg"> <i><?= cclang('loading_saving_data'); ?></i></span>
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

    $('.chosen-select').chosen({width: '100%'});

    function getPredikat(total) {
        if (total >= 90) return {pred: 'A', desc: 'Sangat Baik', cls: 'badge-a'};
        if (total >= 80) return {pred: 'B', desc: 'Baik', cls: 'badge-b'};
        if (total >= 70) return {pred: 'C', desc: 'Cukup', cls: 'badge-c'};
        if (total >= 60) return {pred: 'D', desc: 'Perlu Bimbingan', cls: 'badge-d'};
        return {pred: 'E', desc: 'Perlu Pembinaan Intensif', cls: 'badge-e'};
    }

    function hitungTotal() {
        var status = $('#status_hadir').val();
        if (status === 'Sakit' || status === 'Izin' || status === 'Alfa') {
            var k = parseInt($('#kehadiran').val()) || 0;
            $('#preview_total').text(k.toFixed(2));
            var p = getPredikat(k);
            $('#preview_predikat').text(p.pred + ' (' + p.desc + ')').attr('class', 'badge-predikat ' + p.cls);
            return;
        }

        var kh = parseInt($('#kehadiran').val()) || 0;
        var kl = parseInt($('#kelengkapan').val()) || 0;
        var ka = parseInt($('#keaktifan').val()) || 0;

        var total = (kh + kl + ka) / 3;
        $('#preview_total').text(total.toFixed(2));
        var p = getPredikat(total);
        $('#preview_predikat').text(p.pred + ' (' + p.desc + ')').attr('class', 'badge-predikat ' + p.cls);
    }

    $('#kehadiran').on('input change', function(){
        $('#kehadiran_display').text($(this).val());
        hitungTotal();
    });

    $('#kelengkapan').on('input change', function(){
        $('#kelengkapan_display').text($(this).val());
        hitungTotal();
    });

    $('#keaktifan').on('input change', function(){
        $('#keaktifan_display').text($(this).val());
        hitungTotal();
    });

    $('#status_hadir').on('change', function(){
        var st = $(this).val();
        if (st === 'Hadir') {
            $('#kehadiran').val(100).trigger('input');
            $('#status_kelengkapan').val('Lengkap').trigger('chosen:updated').trigger('change');
            $('#status_keaktifan').val('Aktif').trigger('chosen:updated').trigger('change');
            $('#nilai_section').show();
        } else if (st === 'Terlambat') {
            $('#kehadiran').val(75).trigger('input');
            $('#nilai_section').show();
        } else if (st === 'Izin') {
            $('#kehadiran').val(50).trigger('input');
            $('#status_kelengkapan').val('-').trigger('chosen:updated');
            $('#status_keaktifan').val('-').trigger('chosen:updated');
            $('#kelengkapan').val(50).trigger('input');
            $('#keaktifan').val(50).trigger('input');
        } else if (st === 'Sakit') {
            $('#kehadiran').val(50).trigger('input');
            $('#status_kelengkapan').val('-').trigger('chosen:updated');
            $('#status_keaktifan').val('-').trigger('chosen:updated');
            $('#kelengkapan').val(50).trigger('input');
            $('#keaktifan').val(50).trigger('input');
        } else if (st === 'Alfa') {
            $('#kehadiran').val(0).trigger('input');
            $('#status_kelengkapan').val('-').trigger('chosen:updated');
            $('#status_keaktifan').val('-').trigger('chosen:updated');
            $('#kelengkapan').val(0).trigger('input');
            $('#keaktifan').val(0).trigger('input');
        }
        hitungTotal();
    });

    $('#status_kelengkapan').on('change', function(){
        var st = $(this).val();
        if (st === 'Lengkap') {
            $('#kelengkapan').val(100).trigger('input');
        } else if (st === 'Kurang Lengkap') {
            $('#kelengkapan').val(90).trigger('input');
        } else if (st === 'Tidak Menggunakan Atribut') {
            $('#kelengkapan').val(80).trigger('input');
        }
    });

    $('#status_keaktifan').on('change', function(){
        var st = $(this).val();
        if (st === 'Aktif') {
            $('#keaktifan').val(100).trigger('input');
        } else if (st === 'Kurang Aktif') {
            $('#keaktifan').val(90).trigger('input');
        } else if (st === 'Tidak Aktif') {
            $('#keaktifan').val(80).trigger('input');
        }
    });

    // Save Form
    $('#btn_cancel').click(function(){
        swal({
            title: "Batalkan perubahan?",
            text: "Data yang telah diinputkan tidak akan tersimpan",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, batalkan!",
            cancelButtonText: "Tidak",
            closeOnConfirm: true
        }, function(){
            window.location.href = BASE_URL + 'administrator/presensi_pramuka';
        });
        return false;
    });

    $('.btn_save').click(function(){
        $('.message').fadeOut();
        var form = $('#form_presensi_pramuka');
        var data_post = form.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});

        $('.loading').show();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: data_post,
        })
        .done(function(res) {
            if (res.success) {
                if (save_type == 'back') {
                    window.location.href = res.redirect;
                    return;
                }
                $('.message').printMessage({message : res.message});
                $('.message').fadeIn();
            } else {
                $('.message').printMessage({message : res.message, type : 'warning'});
                $('.message').fadeIn();
            }
        })
        .fail(function() {
            $('.message').printMessage({message : 'Gagal menyimpan data', type : 'warning'});
            $('.message').fadeIn();
        })
        .always(function() {
            $('.loading').hide();
            $('html, body').animate({ scrollTop: $(document).height() }, 2000);
        });

        return false;
    });
});
</script>
