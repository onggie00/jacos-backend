<script type="text/javascript">
</script>

<style>
/* Rating Radio */
.rating-group { display: inline-flex; }
.rating-group label { margin: 0 3px; cursor: pointer; }
.rating-group input[type="radio"] { display: none; }
.rating-group .rating-label { 
   display: inline-block; padding: 6px 12px; border: 1px solid #ddd; 
   border-radius: 3px; font-size: 12px; transition: all 0.2s;
}
.rating-group input[type="radio"]:checked + .rating-label { 
   background: #3498db; color: #fff; border-color: #3498db; 
}
.rating-label-1 { background: #e74c3c !important; color: #fff !important; }
.rating-label-2 { background: #f39c12 !important; color: #fff !important; }
.rating-label-3 { background: #2ecc71 !important; color: #fff !important; }
.rating-label-4 { background: #27ae60 !important; color: #fff !important; }
</style>

<!-- Content Header -->
<section class="content-header">
    <h1>
        Presensi Tadarus        <small><?= cclang('new', ['Presensi Tadarus']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/presensi_tadarus'); ?>">Presensi Tadarus</a></li>
        <li class="active"><?= cclang('new'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row" >
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
                            <h3 class="widget-user-username">Presensi Tadarus</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Presensi Tadarus']); ?></h5>
                            <hr>
                        </div>
                    <?= form_open('', array(
                        'name'    => 'form_presensi_tadarus', 
                        'class'   => 'form-horizontal', 
                        'id'      => 'form_presensi_tadarus', 
                        'enctype' => 'multipart/form-data', 
                        'method'  => 'POST'
                    )); ?>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tanggal <i class="required">*</i></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control datetimepicker" name="tanggal" id="tanggal" placeholder="Pilih tanggal" value="<?= set_value('tanggal'); ?>" required>
                            <small class="text-muted">Tadarus biasanya hari Kamis</small>
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
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                                <option value="Alfa">Alfa</option>
                            </select>
                        </div>
                    </div>

                    <div id="nilai_section">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Kehadiran <i class="required">*</i></label>
                            <div class="col-sm-6">
                                <div class="rating-group">
                                    <input type="radio" name="kehadiran" id="k1" value="1"><label for="k1" class="rating-label" title="Perlu Bimbingan">1</label>
                                    <input type="radio" name="kehadiran" id="k2" value="2"><label for="k2" class="rating-label" title="Cukup">2</label>
                                    <input type="radio" name="kehadiran" id="k3" value="3"><label for="k3" class="rating-label" title="Baik">3</label>
                                    <input type="radio" name="kehadiran" id="k4" value="4" checked><label for="k4" class="rating-label" title="Sangat Baik">4</label>
                                </div>
                                <small class="text-muted">1=Perlu Bimbingan, 2=Cukup, 3=Baik, 4=Sangat Baik (bobot 40%)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Kelengkapan <i class="required">*</i></label>
                            <div class="col-sm-6">
                                <div class="rating-group">
                                    <input type="radio" name="kelengkapan" id="l1" value="1"><label for="l1" class="rating-label" title="Perlu Bimbingan">1</label>
                                    <input type="radio" name="kelengkapan" id="l2" value="2"><label for="l2" class="rating-label" title="Cukup">2</label>
                                    <input type="radio" name="kelengkapan" id="l3" value="3"><label for="l3" class="rating-label" title="Baik">3</label>
                                    <input type="radio" name="kelengkapan" id="l4" value="4" checked><label for="l4" class="rating-label" title="Sangat Baik">4</label>
                                </div>
                                <small class="text-muted">1=Perlu Bimbingan, 2=Cukup, 3=Baik, 4=Sangat Baik (bobot 30%)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Adab <i class="required">*</i></label>
                            <div class="col-sm-6">
                                <div class="rating-group">
                                    <input type="radio" name="adab" id="a1" value="1"><label for="a1" class="rating-label" title="Perlu Bimbingan">1</label>
                                    <input type="radio" name="adab" id="a2" value="2"><label for="a2" class="rating-label" title="Cukup">2</label>
                                    <input type="radio" name="adab" id="a3" value="3"><label for="a3" class="rating-label" title="Baik">3</label>
                                    <input type="radio" name="adab" id="a4" value="4" checked><label for="a4" class="rating-label" title="Sangat Baik">4</label>
                                </div>
                                <small class="text-muted">1=Perlu Bimbingan, 2=Cukup, 3=Baik, 4=Sangat Baik (bobot 20%)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Keaktifan <i class="required">*</i></label>
                            <div class="col-sm-6">
                                <div class="rating-group">
                                    <input type="radio" name="keaktifan" id="i1" value="1"><label for="i1" class="rating-label" title="Perlu Bimbingan">1</label>
                                    <input type="radio" name="keaktifan" id="i2" value="2"><label for="i2" class="rating-label" title="Cukup">2</label>
                                    <input type="radio" name="keaktifan" id="i3" value="3"><label for="i3" class="rating-label" title="Baik">3</label>
                                    <input type="radio" name="keaktifan" id="i4" value="4" checked><label for="i4" class="rating-label" title="Sangat Baik">4</label>
                                </div>
                                <small class="text-muted">1=Perlu Bimbingan, 2=Cukup, 3=Baik, 4=Sangat Baik (bobot 10%)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Preview Total</label>
                            <div class="col-sm-4">
                                <div class="well well-sm" style="margin:0">
                                    <strong id="preview_total">100.00</strong> 
                                    <span id="preview_predikat" class="badge-predikat badge-a">A</span>
                                    <span id="preview_deskripsi" class="text-muted">Sangat Baik</span>
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
                <!--/box body -->
            </div>
            <!--/box -->
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

        $.get(BASE_URL + '/administrator/presensi_tadarus/get_tingkatan', {jenjang: jenjang}, function(data){
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

        $.get(BASE_URL + '/administrator/presensi_tadarus/get_kelas', {jenjang: jenjang, id_tingkatan: id_tingkatan}, function(data){
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

        $.get(BASE_URL + '/administrator/presensi_tadarus/get_siswa', {jenjang: jenjang, id_kelas: id_kelas}, function(data){
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
        if (status != 'Hadir') {
            $('#nilai_section').hide();
        } else {
            $('#nilai_section').show();
        }
    });

    // Preview total
    $('input[type=radio]').change(function(){
        hitungPreview();
    });

    function hitungPreview() {
        var k = parseInt($('input[name=kehadiran]:checked').val()) || 0;
        var l = parseInt($('input[name=kelengkapan]:checked').val()) || 0;
        var a = parseInt($('input[name=adab]:checked').val()) || 0;
        var i = parseInt($('input[name=keaktifan]:checked').val()) || 0;

        var total = (k/4*100)*0.4 + (l/4*100)*0.3 + (a/4*100)*0.2 + (i/4*100)*0.1;
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
                window.location.href = BASE_URL + 'administrator/presensi_tadarus';
            }
        });
        return false;
    });

    // Save button
    $('.btn_save').click(function(){
        $('.message').fadeOut();
        var form = $('#form_presensi_tadarus');
        var data_post = form.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});

        // Auto-fill kelas label from selected option
        var kelas_label = $('#id_kelas option:selected').text();
        data_post.push({name: 'kelas', value: kelas_label});

        $('.loading').show();

        $.ajax({
            url: BASE_URL + '/administrator/presensi_tadarus/add_save',
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
                // Reset form
                $('#form_presensi_tadarus')[0].reset();
                $('.chosen option').prop('selected', false).trigger('chosen:updated');
                $('input[name=kehadiran][value=4]').prop('checked', true);
                $('input[name=kelengkapan][value=4]').prop('checked', true);
                $('input[name=adab][value=4]').prop('checked', true);
                $('input[name=keaktifan][value=4]').prop('checked', true);
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

    <?php if (!empty($is_locked) && count($list_jenjang) == 1): ?>
    // Auto-load tingkatan when jenjang is locked
    $(document).ready(function(){
        $('#jenjang').trigger('change');
    });
    <?php endif; ?>

}); /*end doc ready*/
</script>
