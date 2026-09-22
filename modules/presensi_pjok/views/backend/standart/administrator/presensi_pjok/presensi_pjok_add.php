<script type="text/javascript">
</script>

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
        Presensi PJOK        <small><?= cclang('new', ['Presensi PJOK']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/presensi_pjok'); ?>">Presensi PJOK</a></li>
        <li class="active"><?= cclang('new'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row" >
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-body ">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header ">
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="User Avatar">
                            </div>
                            <h3 class="widget-user-username">Presensi PJOK</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Presensi PJOK']); ?></h5>
                            <hr>
                        </div>
                    <?= form_open('', array(
                        'name'    => 'form_presensi_pjok',
                        'class'   => 'form-horizontal',
                        'id'      => 'form_presensi_pjok',
                        'enctype' => 'multipart/form-data',
                        'method'  => 'POST'
                    )); ?>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tanggal <i class="required">*</i></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control datetimepicker" name="tanggal" id="tanggal" placeholder="Pilih tanggal" value="<?= set_value('tanggal'); ?>" required>
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
                                <option value="">-- Pilih Jenjang dulu --</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Kelas <i class="required">*</i></label>
                        <div class="col-sm-4">
                            <select class="form-control chosen chosen-select" name="id_kelas" id="id_kelas" data-placeholder="Pilih Kelas" required>
                                <option value="">-- Pilih Tingkatan dulu --</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Siswa <i class="required">*</i></label>
                        <div class="col-sm-6">
                            <select class="form-control chosen chosen-select" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Pilih Siswa" required>
                                <option value="">-- Pilih Kelas dulu --</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status Hadir <i class="required">*</i></label>
                        <div class="col-sm-4">
                            <select class="form-control chosen chosen-select" name="status_hadir" id="status_hadir" required>
                                <option value="Hadir" selected>Hadir</option>
                                <option value="Terlambat">Terlambat</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                                <option value="Alfa">Alfa</option>
                            </select>
                        </div>
                    </div>

                    <div id="nilai_section">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Kehadiran (0-100) <i class="required">*</i></label>
                            <div class="col-sm-6">
                                <div class="nilai-slider-wrap">
                                    <input type="range" name="kehadiran" id="kehadiran" min="0" max="100" value="80" step="1">
                                    <span class="nilai-display" id="kehadiran_display">80</span>
                                </div>
                                <small class="text-muted">Geser slider untuk menentukan nilai kehadiran (0-100)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Status Keaktifan <i class="required">*</i></label>
                            <div class="col-sm-4">
                                <select class="form-control chosen chosen-select" name="status_keaktifan" id="status_keaktifan" required>
                                    <option value="Sangat Aktif">Sangat Aktif</option>
                                    <option value="Aktif" selected>Aktif</option>
                                    <option value="Cukup Aktif">Cukup Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Keaktifan (0-100) <i class="required">*</i></label>
                            <div class="col-sm-6">
                                <div class="nilai-slider-wrap">
                                    <input type="range" name="keaktifan" id="keaktifan" min="0" max="100" value="80" step="1">
                                    <span class="nilai-display" id="keaktifan_display">80</span>
                                </div>
                                <small class="text-muted">Geser slider untuk menentukan nilai keaktifan (0-100)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Preview Total</label>
                            <div class="col-sm-4">
                                <div class="well well-sm" style="margin:0">
                                    <strong id="preview_total">80.00</strong>
                                    <span id="preview_predikat" class="badge-predikat badge-b">B</span>
                                    <span id="preview_deskripsi" class="text-muted">Baik</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="message"></div>
                    <div class="row-fluid col-md-7 container-button-bottom">
                        <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="Simpan (Ctrl+s)">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                        <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save_back" data-stype='back' title="Simpan & Kembali (Ctrl+d)">
                            <i class="ion ion-ios-list-outline"></i> Simpan & Kembali
                        </a>
                        <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="Batal (Ctrl+x)">
                            <i class="fa fa-undo"></i> Batal
                        </a>
                        <span class="loading loading-hide">
                            <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg">
                            <i>Menyimpan...</i>
                        </span>
                    </div>
                    <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Page script -->
<script>
$(document).ready(function(){

    // Cascade: Jenjang -> Tingkatan
    $('#jenjang').change(function(){
        var jenjang = $(this).val().toLowerCase();
        var $tingkatan = $('#id_tingkatan');
        var $kelas = $('#id_kelas');
        var $siswa = $('#id_siswa_aktif');

        $tingkatan.html('<option value="">-- Pilih --</option>');
        $kelas.html('<option value="">-- Pilih Tingkatan dulu --</option>');
        $siswa.html('<option value="">-- Pilih Kelas dulu --</option>');

        if (!jenjang) {
            $tingkatan.trigger('chosen:updated');
            $kelas.trigger('chosen:updated');
            $siswa.trigger('chosen:updated');
            return;
        }

        $.get(BASE_URL + '/administrator/presensi_pjok/get_tingkatan', {jenjang: jenjang}, function(data){
            var items = JSON.parse(data);
            $.each(items, function(i, item){
                $tingkatan.append('<option value="' + item.id + '">' + item.label + '</option>');
            });
            $tingkatan.trigger('chosen:updated');
        });
    });

    // Cascade: Tingkatan -> Kelas
    $('#id_tingkatan').change(function(){
        var id_tingkatan = $(this).val();
        var jenjang = $('#jenjang').val().toLowerCase();
        var $kelas = $('#id_kelas');
        var $siswa = $('#id_siswa_aktif');

        $kelas.html('<option value="">-- Pilih --</option>');
        $siswa.html('<option value="">-- Pilih Kelas dulu --</option>');

        if (!jenjang) {
            $kelas.trigger('chosen:updated');
            $siswa.trigger('chosen:updated');
            return;
        }

        $.get(BASE_URL + '/administrator/presensi_pjok/get_kelas', {jenjang: jenjang, id_tingkatan: id_tingkatan}, function(data){
            var items = JSON.parse(data);
            $.each(items, function(i, item){
                $kelas.append('<option value="' + item.id_kelas + '">' + item.label + '</option>');
            });
            $kelas.trigger('chosen:updated');
        });
    });

    // Cascade: Kelas -> Siswa
    $('#id_kelas').change(function(){
        var id_kelas = $(this).val();
        var jenjang = $('#jenjang').val().toLowerCase();
        var $siswa = $('#id_siswa_aktif');

        $siswa.html('<option value="">-- Pilih --</option>');

        if (!id_kelas) {
            $siswa.trigger('chosen:updated');
            return;
        }

        $.get(BASE_URL + '/administrator/presensi_pjok/get_siswa', {jenjang: jenjang, id_kelas: id_kelas}, function(data){
            var items = JSON.parse(data);
            $.each(items, function(i, item){
                $siswa.append('<option value="' + item.id_siswa_aktif + '">' + item.nama_lengkap + ' (NIS: ' + item.nis + ')</option>');
            });
            $siswa.trigger('chosen:updated');
        });
    });

    // Status hadir toggle nilai section
    $('#status_hadir').change(function(){
        var status = $(this).val();
        if (status != 'Hadir' && status != 'Terlambat') {
            $('#nilai_section').hide();
        } else {
            $('#nilai_section').show();
        }
    });

    // Slider display update
    $('#kehadiran').on('input change', function(){
        $('#kehadiran_display').text($(this).val());
        updateSliderColor($(this), '#kehadiran_display');
        hitungPreview();
    });

    $('#keaktifan').on('input change', function(){
        $('#keaktifan_display').text($(this).val());
        updateSliderColor($(this), '#keaktifan_display');
        hitungPreview();
    });

    function updateSliderColor($slider, displayId) {
        var val = parseInt($slider.val());
        var color = val >= 90 ? '#27ae60' : val >= 80 ? '#3498db' : val >= 70 ? '#f39c12' : val >= 60 ? '#e67e22' : '#e74c3c';
        $(displayId).css('background', color);
    }

    function hitungPreview() {
        var k = parseInt($('#kehadiran').val()) || 0;
        var a = parseInt($('#keaktifan').val()) || 0;

        var total = (k + a) / 2;
        total = total.toFixed(2);

        var predikat = 'E', deskripsi = 'Perlu Pembinaan Intensif', badge = 'badge-e';
        if (total >= 90) { predikat = 'A'; deskripsi = 'Sangat Baik'; badge = 'badge-a'; }
        else if (total >= 80) { predikat = 'B'; deskripsi = 'Baik'; badge = 'badge-b'; }
        else if (total >= 70) { predikat = 'C'; deskripsi = 'Cukup'; badge = 'badge-c'; }
        else if (total >= 60) { predikat = 'D'; deskripsi = 'Perlu Bimbingan'; badge = 'badge-d'; }

        $('#preview_total').text(total);
        $('#preview_predikat').attr('class', 'badge-predikat ' + badge).text(predikat);
        $('#preview_deskripsi').text(deskripsi);
    }

    // Cancel button
    $('#btn_cancel').click(function(){
        swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "Data yang belum disimpan akan hilang",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, Keluar!",
            cancelButtonText: "Batal",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                window.location.href = BASE_URL + 'administrator/presensi_pjok';
            }
        });
        return false;
    });

    // Save button
    $('.btn_save').click(function(){
        $('.message').fadeOut();
        var form = $('#form_presensi_pjok');
        var data_post = form.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});

        var kelas_label = $('#id_kelas option:selected').text();
        data_post.push({name: 'kelas', value: kelas_label});

        $('.loading').show();

        $.ajax({
            url: BASE_URL + '/administrator/presensi_pjok/add_save',
            type: 'POST',
            dataType: 'json',
            data: data_post,
        })
        .done(function(res) {
            $('form').find('.form-group').removeClass('has-error');
            $('form').find('.error-input').remove();

            if(res.success) {
                if (save_type == 'back') {
                    window.location.href = res.redirect;
                    return;
                }
                $('.message').printMessage({message : res.message});
                $('.message').fadeIn();
                $('#form_presensi_pjok')[0].reset();
                $('.chosen option').prop('selected', false).trigger('chosen:updated');
                $('#kehadiran').val(80).trigger('change');
                $('#keaktifan').val(80).trigger('change');
                hitungPreview();
            } else {
                if (res.errors) {
                    $.each(res.errors, function(index, val) {
                        $('form #'+index).parents('.form-group').addClass('has-error');
                        $('form #'+index).parents('.form-group').find('small').prepend('<div class="error-input">' + val + '</div>');
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

    // Initial preview
    hitungPreview();
    updateSliderColor($('#kehadiran'), '#kehadiran_display');
    updateSliderColor($('#keaktifan'), '#keaktifan_display');

    <?php if (!empty($is_locked) && count($list_jenjang) == 1): ?>
    $(document).ready(function(){
        $('#jenjang').trigger('change');
    });
    <?php endif; ?>

});
</script>
