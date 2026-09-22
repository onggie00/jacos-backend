<!-- <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.6/dist/loadingoverlay.min.js"></script> -->

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Siswa Sma Aktif <small>Edit Siswa Sma Aktif</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a href="<?= site_url('administrator/siswa_sma_aktif'); ?>">Siswa Sma Aktif</a></li>
        <li class="active">Edit</li>
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
                            <h3 class="widget-user-username">Siswa Sma Aktif</h3>
                            <h5 class="widget-user-desc">Edit Siswa Sma Aktif</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/siswa_sma_aktif/edit_save/' . $this->uri->segment(4)), [
                            'name'    => 'form_siswa_sma_aktif',
                            'class'   => 'form-horizontal form-step',
                            'id'      => 'form_siswa_sma_aktif',
                            'method'  => 'POST'
                        ]); ?>

                        <div class="form-group ">
                            <label for="nama_lengkap" class="col-sm-2 control-label">Nama Lengkap
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" value="<?= set_value('nama_lengkap', $siswa_sma_aktif->nama_lengkap); ?>">
                                <small class="info help-block">
                                    <b>Input Nama Lengkap</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="nis" class="col-sm-2 control-label">Nis
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nis" id="nis" placeholder="Nis" value="<?= set_value('nis', $siswa_sma_aktif->nis); ?>">
                                <small class="info help-block">
                                    <b>Input Nis</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="id_tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="id_tahun_ajaran" id="id_tahun_ajaran" data-placeholder="Select Id Tahun Ajaran">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('tahun_ajaran') as $row) : ?>
                                        <option <?= $row->id_tahun_ajaran ==  $siswa_sma_aktif->id_tahun_ajaran ? 'selected' : ''; ?> value="<?= $row->id_tahun_ajaran ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                    <b>Input Tahun Ajaran</b> Max Length : 11.</small>
                            </div>
                        </div>



                        <div class="form-group ">
                            <label for="id_kelas" class="col-sm-2 control-label"> Kelas
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="id_kelas" id="id_kelas" data-placeholder="Select Id Kelas">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('kelas_sma') as $row) : ?>
                                        <option <?= $row->id_kelas_sma ==  $siswa_sma_aktif->id_kelas ? 'selected' : ''; ?> value="<?= $row->id_kelas_sma ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                    <b>Input  Kelas</b> Max Length : 11.</small>
                            </div>
                        </div>



                        <div class="form-group ">
                            <label for="id_siswa_sma" class="col-sm-2 control-label"> Siswa Sma
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="id_siswa_sma" id="id_siswa_sma" data-placeholder="Select Id Siswa Sma">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_sma') as $row) : ?>
                                        <option <?= $row->id_siswa_sma ==  $siswa_sma_aktif->id_siswa_sma ? 'selected' : ''; ?> value="<?= $row->id_siswa_sma ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                    <b>Input Id Siswa Sma</b> Max Length : 11.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="nomor_peserta_ujian" class="col-sm-2 control-label">Nomor Peserta Ujian
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomor_peserta_ujian" id="nomor_peserta_ujian" placeholder="nomor_peserta_ujian" value="<?= set_value('nomor_peserta_ujian', $siswa_sma_aktif->nomor_peserta_ujian); ?>">
                                <small class="info help-block">
                                    <b>Input Nomor Peserta Ujian</b>.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="kewarganegaraan" class="col-sm-2 control-label">Kewarganegaraan
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kewarganegaraan" id="kewarganegaraan" placeholder="Kewarganegaraan" value="<?= set_value('kewarganegaraan', $siswa_sma_aktif->kewarganegaraan); ?>">
                                <small class="info help-block">
                                    <b>Input Kewarganegaraan</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="nik" class="col-sm-2 control-label">Nik
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nik" id="nik" placeholder="Nik" value="<?= set_value('nik', $siswa_sma_aktif->nik); ?>">
                                <small class="info help-block">
                                    <b>Input Nik</b> Max Length : 20.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="golongan_darah" class="col-sm-2 control-label">Golongan Darah
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="golongan_darah" id="golongan_darah" placeholder="Golongan Darah" value="<?= set_value('golongan_darah', $siswa_sma_aktif->golongan_darah); ?>">
                                <small class="info help-block">
                                    <b>Input Golongan Darah</b> Max Length : 5.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="telp" class="col-sm-2 control-label">Telp
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="telp" id="telp" placeholder="Telp" value="<?= set_value('telp', $siswa_sma_aktif->telp); ?>">
                                <small class="info help-block">
                                    <b>Input Telp</b> Max Length : 20.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="pendidikan_ayah" class="col-sm-2 control-label">Pendidikan Ayah
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="pendidikan_ayah" id="pendidikan_ayah" placeholder="Pendidikan Ayah" value="<?= set_value('pendidikan_ayah', $siswa_sma_aktif->pendidikan_ayah); ?>">
                                <small class="info help-block">
                                    <b>Input Pendidikan Ayah</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="pendidikan_ibu" class="col-sm-2 control-label">Pendidikan Ibu
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="pendidikan_ibu" id="pendidikan_ibu" placeholder="Pendidikan Ibu" value="<?= set_value('pendidikan_ibu', $siswa_sma_aktif->pendidikan_ibu); ?>">
                                <small class="info help-block">
                                    <b>Input Pendidikan Ibu</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="penghasilan_ayah" class="col-sm-2 control-label">Penghasilan Ayah
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="penghasilan_ayah" id="penghasilan_ayah" placeholder="Penghasilan Ayah" value="<?= set_value('penghasilan_ayah', $siswa_sma_aktif->penghasilan_ayah); ?>">
                                <small class="info help-block">
                                    <b>Input Penghasilan Ayah</b> Max Length : 11.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="penghasilan_ibu" class="col-sm-2 control-label">Penghasilan Ibu
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="penghasilan_ibu" id="penghasilan_ibu" placeholder="Penghasilan Ibu" value="<?= set_value('penghasilan_ibu', $siswa_sma_aktif->penghasilan_ibu); ?>">
                                <small class="info help-block">
                                    <b>Input Penghasilan Ibu</b> Max Length : 11.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="tgl_lahir_ayah" class="col-sm-2 control-label">Tgl Lahir Ayah
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                                <div class="input-group date col-sm-8">
                                    <input type="text" class="form-control pull-right datepicker" name="tgl_lahir_ayah" placeholder="Tgl Lahir Ayah" id="tgl_lahir_ayah" value="<?= set_value('siswa_sma_aktif_tgl_lahir_ayah_name', $siswa_sma_aktif->tgl_lahir_ayah); ?>">
                                </div>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>


                        <div class="form-group ">
                            <label for="tgl_lahir_ibu" class="col-sm-2 control-label">Tgl Lahir Ibu
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                                <div class="input-group date col-sm-8">
                                    <input type="text" class="form-control pull-right datepicker" name="tgl_lahir_ibu" placeholder="Tgl Lahir Ibu" id="tgl_lahir_ibu" value="<?= set_value('siswa_sma_aktif_tgl_lahir_ibu_name', $siswa_sma_aktif->tgl_lahir_ibu); ?>">
                                </div>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="spp_custom" class="col-sm-2 control-label">SPP Khusus <span class="text-danger">Optional</span>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="spp_custom" id="spp_custom" placeholder="SPP khusus" value="<?= set_value('spp_custom', $siswa_sma_aktif->spp_custom); ?>"
                                    oninput="formatRupiah(this)">
                                <small class="info help-block">Nominal SPP khusus (jika ada)</small>
                                <script type="text/javascript">
                                function formatRupiah(el) {
                                    var raw = el.value.replace(/[^0-9]/g, '');
                                    var formatted = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                    el.value = formatted || '';
                                }
                                document.addEventListener('DOMContentLoaded', function() {
                                    var form = document.querySelector('form');
                                    if (form) form.addEventListener('submit', function(e) {
                                        var el = document.getElementById('spp_custom');
                                        if (el) {
                                            var raw = el.value.replace(/[^0-9]/g, '');
                                            el.value = raw;
                                        }
                                    });
                                });
                                </script>
                                <small class="info help-block">Nominal SPP khusus (jika ada)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="spp_type" class="col-sm-2 control-label">SPP Type <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="spp_type" id="spp_type">
                                    <option <?= ($siswa_sma_aktif->spp_type == 'FULL') ? 'selected' : ''; ?> value="FULL">FULL</option>
                                    <option <?= ($siswa_sma_aktif->spp_type == 'HALF') ? 'selected' : ''; ?> value="HALF">HALF (50%)</option>
                                    <option <?= ($siswa_sma_aktif->spp_type == 'FREE') ? 'selected' : ''; ?> value="FREE">FREE (Rp 0)</option>
                                </select>
                                <small class="info help-block">FULL = nominal utuh, HALF = potongan 50%, FREE = tidak bayar</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="acc_ujian" class="col-sm-2 control-label">Acc Ujian <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="acc_ujian" id="acc_ujian">
                                    <option <?= ($siswa_sma_aktif->acc_ujian == 1 || empty($siswa_sma_aktif->acc_ujian)) ? 'selected' : ''; ?> value="1">Boleh</option>
                                    <option <?= ($siswa_sma_aktif->acc_ujian == 0) ? 'selected' : ''; ?> value="0">Tidak Boleh</option>
                                </select>
                                <small class="info help-block">Apakah siswa diperbolehkan ujian walaupun belum bayar SPP</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="email_ms_office" class="col-sm-2 control-label">Email Ms Office 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email_ms_office" id="email_ms_office" placeholder="Email Office" value="<?= set_value('email_ms_office', $siswa_sma_aktif->email_ms_office); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="email_ms_office_ortu" class="col-sm-2 control-label">Email Ms Office Ortu
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email_ms_office_ortu" id="email_ms_office_ortu" placeholder="Email Office Ortu" value="<?= set_value('email_ms_office_ortu', $siswa_sma_aktif->email_ms_office_ortu); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="restore_siswa" class="col-sm-2 control-label">Status Akun Siswa
                            </label>
                            <div class="col-sm-8">
                                <?php if (!empty($siswa_sma_aktif->deleted_at_siswa)): ?>
                                    <div style="padding:10px;background:#fde8e8;border:1px solid #e74c3c;border-radius:4px;margin-bottom:8px">
                                        <span class="text-danger"><i class="fa fa-times-circle"></i> <b>Akun Siswa Terhapus</b></span><br>
                                        <small class="text-danger"><i class="fa fa-calendar"></i> Dihapus pada: <?= date('d M Y H:i', strtotime($siswa_sma_aktif->deleted_at_siswa)); ?></small>
                                    </div>
                                    <select class="form-control chosen chosen-select" name="restore_siswa" id="restore_siswa">
                                        <option value="keep">Biarkan Terhapus</option>
                                        <option value="restore" selected><i class="fa fa-undo"></i> Pulihkan Akun Siswa</option>
                                    </select>
                                    <small class="info help-block text-muted">Pilih "Pulihkan" untuk mengaktifkan kembali akun siswa</small>
                                <?php else: ?>
                                    <div style="padding:10px;background:#e8f8e8;border:1px solid #27ae60;border-radius:4px">
                                        <span class="text-success"><i class="fa fa-check-circle"></i> <b>Akun Siswa Aktif</b></span>
                                        <input type="hidden" name="restore_siswa" value="keep">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="restore_ortu" class="col-sm-2 control-label">Status Akun Ortu
                            </label>
                            <div class="col-sm-8">
                                <?php if (!empty($siswa_sma_aktif->deleted_at_ortu)): ?>
                                    <div style="padding:10px;background:#fde8e8;border:1px solid #e74c3c;border-radius:4px;margin-bottom:8px">
                                        <span class="text-danger"><i class="fa fa-times-circle"></i> <b>Akun Ortu Terhapus</b></span><br>
                                        <small class="text-danger"><i class="fa fa-calendar"></i> Dihapus pada: <?= date('d M Y H:i', strtotime($siswa_sma_aktif->deleted_at_ortu)); ?></small>
                                    </div>
                                    <select class="form-control chosen chosen-select" name="restore_ortu" id="restore_ortu">
                                        <option value="keep">Biarkan Terhapus</option>
                                        <option value="restore" selected><i class="fa fa-undo"></i> Pulihkan Akun Ortu</option>
                                    </select>
                                    <small class="info help-block text-muted">Pilih "Pulihkan" untuk mengaktifkan kembali akun ortu</small>
                                <?php else: ?>
                                    <div style="padding:10px;background:#e8f8e8;border:1px solid #27ae60;border-radius:4px">
                                        <span class="text-success"><i class="fa fa-check-circle"></i> <b>Akun Ortu Aktif</b></span>
                                        <input type="hidden" name="restore_ortu" value="keep">
                                    </div>
                                <?php endif; ?>
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
<!-- Page script -->
<script>
    $(document).ready(function() {



        $('#btn_cancel').click(function() {
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
                    url: form_siswa_sma_aktif.attr('action'),
                    type: 'POST',
                    dataType: 'json',
                    data: data_post,
                })
                .done(function(res) {
                    $('form').find('.form-group').removeClass('has-error');
                    $('form').find('.error-input').remove();
                    $('.steps li').removeClass('error');
                    if (res.success) {
                        var id = $('#siswa_sma_aktif_image_galery').find('li').attr('qq-file-id');
                        if (save_type == 'back') {
                            window.location.href = res.redirect;
                            return;
                        }

                        $('.message').printMessage({
                            message: res.message
                        });
                        $('.message').fadeIn();
                        $('.data_file_uuid').val('');

                    } else {
                        if (res.errors) {
                            parseErrorField(res.errors);
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





        function chained_id_tahun_ajaran(selected, complete) {
            var val = $('#id_tahun_ajaran').val();
            $.LoadingOverlay('show')
            return $.ajax({
                    url: BASE_URL + '/administrator/siswa_sma_aktif/ajax_id_tahun_ajaran/' + val,
                    dataType: 'JSON',
                })
                .done(function(res) {
                    var html = '<option value=""></option>';
                    $.each(res, function(index, val) {
                        html += '<option ' + (selected == val.id_tahun_ajaran ? 'selected' : '') + ' value="' + val.id_tahun_ajaran + '">' + val.label + '</option>'
                    });
                    $('#id_tahun_ajaran').html(html);
                    $('#id_tahun_ajaran').trigger('chosen:updated');
                    if (typeof complete != 'undefined') {
                        complete();
                    }

                })
                .fail(function() {
                    toastr['error']('Error', 'Getting data fail')
                })
                .always(function() {
                    $.LoadingOverlay('hide')
                });
        }


        $('#id_tahun_ajaran').change(function(event) {
            chained_id_tahun_ajaran('')
        });

        function chained_id_kelas(selected, complete) {
            var val = $('#id_kelas').val();
            $.LoadingOverlay('show')
            return $.ajax({
                    url: BASE_URL + '/administrator/siswa_sma_aktif/ajax_id_kelas/' + val,
                    dataType: 'JSON',
                })
                .done(function(res) {
                    var html = '<option value=""></option>';
                    $.each(res, function(index, val) {
                        html += '<option ' + (selected == val.id_kelas_sma ? 'selected' : '') + ' value="' + val.id_kelas_sma + '">' + val.label + '</option>'
                    });
                    $('#id_kelas').html(html);
                    $('#id_kelas').trigger('chosen:updated');
                    if (typeof complete != 'undefined') {
                        complete();
                    }

                })
                .fail(function() {
                    toastr['error']('Error', 'Getting data fail')
                })
                .always(function() {
                    $.LoadingOverlay('hide')
                });
        }


        $('#id_kelas').change(function(event) {
            chained_id_kelas('')
        });

        function chained_id_siswa_sma(selected, complete) {
            var val = $('#id_siswa_sma').val();
            $.LoadingOverlay('show')
            return $.ajax({
                    url: BASE_URL + '/administrator/siswa_sma_aktif/ajax_id_siswa_sma/' + val,
                    dataType: 'JSON',
                })
                .done(function(res) {
                    var html = '<option value=""></option>';
                    $.each(res, function(index, val) {
                        html += '<option ' + (selected == val.id_siswa_sma ? 'selected' : '') + ' value="' + val.id_siswa_sma + '">' + val.nama_lengkap + '</option>'
                    });
                    $('#id_siswa_sma').html(html);
                    $('#id_siswa_sma').trigger('chosen:updated');
                    if (typeof complete != 'undefined') {
                        complete();
                    }

                })
                .fail(function() {
                    toastr['error']('Error', 'Getting data fail')
                })
                .always(function() {
                    $.LoadingOverlay('hide')
                });
        }


        $('#id_siswa_sma').change(function(event) {
            chained_id_siswa_sma('')
        });

        async function chain() {
            await chained_id_tahun_ajaran("<?= $siswa_sma_aktif->id_tahun_ajaran ?>");
            await chained_id_kelas("<?= $siswa_sma_aktif->id_kelas ?>");
            await chained_id_siswa_sma("<?= $siswa_sma_aktif->id_siswa_sma ?>");
        }

        chain();




    }); /*end doc ready*/
</script>