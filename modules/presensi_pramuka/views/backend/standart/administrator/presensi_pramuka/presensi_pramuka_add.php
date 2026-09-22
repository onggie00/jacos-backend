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
        Presensi Pramuka <small><?= cclang('new', ['Presensi Pramuka']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a href="<?= site_url('administrator/presensi_pramuka'); ?>">Presensi Pramuka</a></li>
        <li class="active"><?= cclang('new'); ?></li>
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
                            <h5 class="widget-user-desc"><?= cclang('new', ['Presensi Pramuka']); ?></h5>
                            <hr>
                        </div>
                    <?= form_open('', array(
                        'name'    => 'form_presensi_pramuka',
                        'class'   => 'form-horizontal',
                        'id'      => 'form_presensi_pramuka',
                        'enctype' => 'multipart/form-data',
                        'method'  => 'POST'
                    )); ?>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tanggal <i class="required">*</i></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control datetimepicker" name="tanggal" id="tanggal" placeholder="Pilih tanggal" value="<?= set_value('tanggal', date('Y-m-d H:i:s')); ?>" required>
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
                                    <option value="<?= $j['code']; ?>"><?= $j['label']; ?></option>
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
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Siswa <i class="required">*</i></label>
                        <div class="col-sm-4">
                            <select class="form-control chosen chosen-select" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Pilih Siswa" required>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status Hadir <i class="required">*</i></label>
                        <div class="col-sm-4">
                            <select class="form-control chosen chosen-select" name="status_hadir" id="status_hadir" data-placeholder="Pilih Status" required>
                                <option value="Hadir" selected>Hadir</option>
                                <option value="Terlambat">Terlambat</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                                <option value="Alfa">Alfa</option>
                            </select>
                        </div>
                    </div>

                    <div id="nilai_section">
                        <!-- 1. Kehadiran -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Nilai Kehadiran</label>
                            <div class="col-sm-6">
                                <div class="nilai-slider-wrap">
                                    <input type="range" name="kehadiran" id="kehadiran" min="0" max="100" value="100" step="1">
                                    <span class="nilai-display" id="kehadiran_display">100</span>
                                </div>
                                <small class="text-muted">Rubrik: Hadir (100), Terlambat (75), Sakit/Izin (50), Alfa (0)</small>
                            </div>
                        </div>

                        <!-- 2. Kelengkapan Atribut -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Kelengkapan Atribut</label>
                            <div class="col-sm-4">
                                <select class="form-control chosen chosen-select" name="status_kelengkapan" id="status_kelengkapan">
                                    <option value="Lengkap" selected>Lengkap (Bedge, Hasduk & Ring)</option>
                                    <option value="Kurang Lengkap">Kurang Lengkap</option>
                                    <option value="Tidak Menggunakan Atribut">Tidak Menggunakan Atribut</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Nilai Atribut</label>
                            <div class="col-sm-6">
                                <div class="nilai-slider-wrap">
                                    <input type="range" name="kelengkapan" id="kelengkapan" min="0" max="100" value="100" step="1">
                                    <span class="nilai-display" id="kelengkapan_display">100</span>
                                </div>
                                <small class="text-muted">Rubrik: Lengkap (100), Kurang Lengkap (90), Tidak Menggunakan Atribut (80)</small>
                            </div>
                        </div>

                        <!-- 3. Keaktifan -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Keaktifan</label>
                            <div class="col-sm-4">
                                <select class="form-control chosen chosen-select" name="status_keaktifan" id="status_keaktifan">
                                    <option value="Aktif" selected>Aktif</option>
                                    <option value="Kurang Aktif">Kurang Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Nilai Keaktifan</label>
                            <div class="col-sm-6">
                                <div class="nilai-slider-wrap">
                                    <input type="range" name="keaktifan" id="keaktifan" min="0" max="100" value="100" step="1">
                                    <span class="nilai-display" id="keaktifan_display">100</span>
                                </div>
                                <small class="text-muted">Rubrik: Aktif (100), Kurang Aktif (90), Tidak Aktif (80)</small>
                            </div>
                        </div>

                        <!-- Total & Preview -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Total Nilai & Predikat</label>
                            <div class="col-sm-6">
                                <p class="form-control-static" style="font-size:16px">
                                    <strong id="preview_total">100.00</strong> —
                                    <span class="badge-predikat badge-a" id="preview_predikat">A (Sangat Baik)</span>
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

    // Cascading Dropdowns
    $('#jenjang').on('change', function(){
        var jenjang = $(this).val();
        $('#id_tingkatan').html('<option value=""></option>').trigger('chosen:updated');
        $('#id_kelas').html('<option value=""></option>').trigger('chosen:updated');
        $('#id_siswa_aktif').html('<option value=""></option>').trigger('chosen:updated');

        if (jenjang) {
            $.get(BASE_URL + '/administrator/presensi_pramuka/get_tingkatan', {jenjang: jenjang}, function(res){
                var resp = typeof res === 'object' ? res : JSON.parse(res);
                if (resp.status === 'success') {
                    var opts = '<option value=""></option>';
                    $.each(resp.data, function(i, item){
                        opts += '<option value="' + item.id + '">' + item.label + '</option>';
                    });
                    $('#id_tingkatan').html(opts).trigger('chosen:updated');
                }
            });
            $.get(BASE_URL + '/administrator/presensi_pramuka/get_kelas', {jenjang: jenjang}, function(res){
                var resp = typeof res === 'object' ? res : JSON.parse(res);
                if (resp.status === 'success') {
                    var opts = '<option value=""></option>';
                    $.each(resp.data, function(i, item){
                        opts += '<option value="' + item.id_kelas + '">' + item.label + '</option>';
                    });
                    $('#id_kelas').html(opts).trigger('chosen:updated');
                }
            });
        }
    });

    $('#id_tingkatan').on('change', function(){
        var jenjang = $('#jenjang').val();
        var id_tingkatan = $(this).val();
        $('#id_kelas').html('<option value=""></option>').trigger('chosen:updated');
        $('#id_siswa_aktif').html('<option value=""></option>').trigger('chosen:updated');

        if (jenjang) {
            $.get(BASE_URL + '/administrator/presensi_pramuka/get_kelas', {jenjang: jenjang, id_tingkatan: id_tingkatan}, function(res){
                var resp = typeof res === 'object' ? res : JSON.parse(res);
                if (resp.status === 'success') {
                    var opts = '<option value=""></option>';
                    $.each(resp.data, function(i, item){
                        opts += '<option value="' + item.id_kelas + '">' + item.label + '</option>';
                    });
                    $('#id_kelas').html(opts).trigger('chosen:updated');
                }
            });
        }
    });

    $('#id_kelas').on('change', function(){
        var jenjang = $('#jenjang').val();
        var id_kelas = $(this).val();
        $('#id_siswa_aktif').html('<option value=""></option>').trigger('chosen:updated');

        if (jenjang && id_kelas) {
            $.get(BASE_URL + '/administrator/presensi_pramuka/get_siswa', {jenjang: jenjang, id_kelas: id_kelas}, function(res){
                var resp = typeof res === 'object' ? res : JSON.parse(res);
                if (resp.status === 'success') {
                    var opts = '<option value=""></option>';
                    $.each(resp.data, function(i, item){
                        var nis = item.nis ? ' (' + item.nis + ')' : '';
                        opts += '<option value="' + item.id_siswa_aktif + '">' + item.nama_lengkap + nis + '</option>';
                    });
                    $('#id_siswa_aktif').html(opts).trigger('chosen:updated');
                }
            });
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
            url: BASE_URL + '/administrator/presensi_pramuka/add_save',
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
                resetForm();
                $('#form_presensi_pramuka')[0].reset();
                $('.chosen-select').trigger('chosen:updated');
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
