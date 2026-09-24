<!-- Fine Uploader Gallery CSS file
    ====================================================================== -->
<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<!-- Fine Uploader jQuery JS file
    ====================================================================== -->
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
    function domo() {

        // Binding keys
        $('*').bind('keydown', 'Ctrl+s', function assets() {
            $('#btn_save').trigger('click');
            return false;
        });

        $('*').bind('keydown', 'Ctrl+x', function assets() {
            $('#btn_cancel').trigger('click');
            return false;
        });

        $('*').bind('keydown', 'Ctrl+d', function assets() {
            $('.btn_save_back').trigger('click');
            return false;
        });

    }

    jQuery(document).ready(domo());
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Siswa SD <small>Edit Siswa SD</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a href="<?= site_url('administrator/siswa_tk'); ?>">Siswa SD</a></li>
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
                            <h3 class="widget-user-username">Siswa SD</h3>
                            <h5 class="widget-user-desc">Edit Siswa SD</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/siswa_tk/edit_save/' . $this->uri->segment(4)), [
                            'name'    => 'form_siswa_tk',
                            'class'   => 'form-horizontal form-step',
                            'id'      => 'form_siswa_tk',
                            'method'  => 'POST'
                        ]); ?>

                        <div class="form-group ">
                            <label for="nama_lengkap" class="col-sm-2 control-label">Nama Lengkap
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="nama_lengkap" name="nama_lengkap" rows="5" class="textarea form-control"><?= set_value('nama_lengkap', $siswa_tk->nama_lengkap); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="email" class="col-sm-2 control-label">Email
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?= set_value('email', $siswa_tk->email); ?>">
                                <small class="info help-block">
                                    <b>Input Email</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="email_ms_office" class="col-sm-2 control-label">Email Ms. Office
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email_ms_office" id="email_ms_office" placeholder="Email Ms. Office" value="<?= set_value('email_ms_office', $siswa_tk->email_ms_office); ?>">
                                <small class="info help-block">
                                    <b>Input Email Ms Office</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="nisn" class="col-sm-2 control-label">NISN
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nisn" id="nisn" placeholder="NISN" value="<?= set_value('nisn', $siswa_tk->nisn); ?>">
                                <small class="info help-block">
                                    <b>Input Nisn</b> Max Length : 30.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="tempat_lahir" class="col-sm-2 control-label">Tempat Lahir
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" placeholder="Tempat Lahir" value="<?= set_value('tempat_lahir', $siswa_tk->tempat_lahir); ?>">
                                <small class="info help-block">
                                    <b>Input Tempat Lahir</b> Max Length : 100.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="tgl_lahir" class="col-sm-2 control-label">Tanggal Lahir
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                                <div class="input-group date col-sm-8">
                                    <input type="text" class="form-control pull-right datepicker" name="tgl_lahir" placeholder="Tanggal Lahir" id="tgl_lahir" value="<?= set_value('siswa_tk_tgl_lahir_name', $siswa_tk->tgl_lahir); ?>">
                                </div>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>


                        <div class="form-group  wrapper-options-crud">
                            <label for="jenis_kelamin" class="col-sm-2 control-label">Jenis Kelamin
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div class="col-md-3 padding-left-0">
                                    <label>
                                        <input <?= $siswa_tk->jenis_kelamin == "Laki-laki" ? "checked" : ""; ?> type="radio" class="flat-red" name="jenis_kelamin" value="Laki-laki"> laki-laki </label>
                                </div>
                                <div class="col-md-3 padding-left-0">
                                    <label>
                                        <input <?= $siswa_tk->jenis_kelamin == "Perempuan" ? "checked" : ""; ?> type="radio" class="flat-red" name="jenis_kelamin" value="Perempuan"> perempuan </label>
                                </div>
                                </select>
                                <div class="row-fluid clear-both">
                                    <small class="info help-block">
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="agama" class="col-sm-2 control-label">Agama
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="agama" id="agama" placeholder="Agama" value="<?= set_value('agama', $siswa_tk->agama); ?>">
                                <small class="info help-block">
                                    <b>Input Agama</b> Max Length : 20.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="email_ms_office_ortu" class="col-sm-2 control-label">Email Ms. Office Ortu
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email_ms_office_ortu" id="email_ms_office_ortu" placeholder="Email Ms. Office Ortu" value="<?= set_value('email_ms_office_ortu', $siswa_tk->email_ms_office_ortu); ?>">
                                <small class="info help-block">
                                    <b>Input Email Ms Office Ortu</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="nama_ibu" class="col-sm-2 control-label">Nama Ibu
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_ibu" id="nama_ibu" placeholder="Nama Ibu" value="<?= set_value('nama_ibu', $siswa_tk->nama_ibu); ?>">
                                <small class="info help-block">
                                    <b>Input Nama Ibu</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="pekerjaan_ibu" class="col-sm-2 control-label">Pekerjaan Ibu
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="pekerjaan_ibu" id="pekerjaan_ibu" placeholder="Pekerjaan Ibu" value="<?= set_value('pekerjaan_ibu', $siswa_tk->pekerjaan_ibu); ?>">
                                <small class="info help-block">
                                    <b>Input Pekerjaan Ibu</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="notelp_ibu" class="col-sm-2 control-label">No. Telepon Ibu
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="notelp_ibu" id="notelp_ibu" placeholder="No. Telepon Ibu" value="<?= set_value('notelp_ibu', $siswa_tk->notelp_ibu); ?>">
                                <small class="info help-block">
                                    <b>Input Notelp Ibu</b> Max Length : 14.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="nama_ayah" class="col-sm-2 control-label">Nama Ayah
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_ayah" id="nama_ayah" placeholder="Nama Ayah" value="<?= set_value('nama_ayah', $siswa_tk->nama_ayah); ?>">
                                <small class="info help-block">
                                    <b>Input Nama Ayah</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="pekerjaan_ayah" class="col-sm-2 control-label">Pekerjaan Ayah
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="pekerjaan_ayah" id="pekerjaan_ayah" placeholder="Pekerjaan Ayah" value="<?= set_value('pekerjaan_ayah', $siswa_tk->pekerjaan_ayah); ?>">
                                <small class="info help-block">
                                    <b>Input Pekerjaan Ayah</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="notelp_ayah" class="col-sm-2 control-label">No. Telepon Ayah
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="notelp_ayah" id="notelp_ayah" placeholder="No. Telepon Ayah" value="<?= set_value('notelp_ayah', $siswa_tk->notelp_ayah); ?>">
                                <small class="info help-block">
                                    <b>Input Notelp Ayah</b> Max Length : 14.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="alamat" class="col-sm-2 control-label">Alamat
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="alamat" name="alamat" rows="10" cols="80"> <?= set_value('alamat', $siswa_tk->alamat); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="provinsi" class="col-sm-2 control-label">Provinsi
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="provinsi" id="provinsi" data-placeholder="Select Provinsi">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('provinces') as $row) : ?>
                                        <option <?= $row->id ==  $siswa_tk->provinsi ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>



                        <div class="form-group ">
                            <label for="kelurahan" class="col-sm-2 control-label">Kelurahan
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="kelurahan" id="kelurahan" data-placeholder="Select Kelurahan">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('areas') as $row) : ?>
                                        <option <?= $row->id ==  $siswa_tk->kelurahan ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                    <b>Input Kelurahan</b> Max Length : 11.</small>
                            </div>
                        </div>



                        <div class="form-group ">
                            <label for="kecamatan" class="col-sm-2 control-label">Kecamatan
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="kecamatan" id="kecamatan" data-placeholder="Select Kecamatan">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('districts') as $row) : ?>
                                        <option <?= $row->id ==  $siswa_tk->kecamatan ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                    <b>Input Kecamatan</b> Max Length : 11.</small>
                            </div>
                        </div>



                        <div class="form-group ">
                            <label for="kota" class="col-sm-2 control-label">Kota
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="kota" id="kota" data-placeholder="Select Kota">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('regencies') as $row) : ?>
                                        <option <?= $row->id ==  $siswa_tk->kota ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                    <b>Input Kota</b> Max Length : 11.</small>
                            </div>
                        </div>



                        <div class="form-group ">
                            <label for="kode_pos" class="col-sm-2 control-label">Kode Pos
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kode_pos" id="kode_pos" placeholder="Kode Pos" value="<?= set_value('kode_pos', $siswa_tk->kode_pos); ?>">
                                <small class="info help-block">
                                    <b>Input Kode Pos</b> Max Length : 8.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="sekolah_asal" class="col-sm-2 control-label">Sekolah Asal
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="sekolah_asal" name="sekolah_asal" rows="5" class="textarea form-control"><?= set_value('sekolah_asal', $siswa_tk->sekolah_asal); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="foto_peserta" class="col-sm-2 control-label">Foto Peserta
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="siswa_tk_foto_peserta_galery"></div>
                                <input class="data_file data_file_uuid" name="siswa_tk_foto_peserta_uuid" id="siswa_tk_foto_peserta_uuid" type="hidden" value="<?= set_value('siswa_tk_foto_peserta_uuid'); ?>">
                                <input class="data_file" name="siswa_tk_foto_peserta_name" id="siswa_tk_foto_peserta_name" type="hidden" value="<?= set_value('siswa_tk_foto_peserta_name', $siswa_tk->foto_peserta); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="akte_lahir" class="col-sm-2 control-label">Akte Lahir
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="siswa_tk_akte_lahir_galery"></div>
                                <input class="data_file data_file_uuid" name="siswa_tk_akte_lahir_uuid" id="siswa_tk_akte_lahir_uuid" type="hidden" value="<?= set_value('siswa_tk_akte_lahir_uuid'); ?>">
                                <input class="data_file" name="siswa_tk_akte_lahir_name" id="siswa_tk_akte_lahir_name" type="hidden" value="<?= set_value('siswa_tk_akte_lahir_name', $siswa_tk->akte_lahir); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="kartu_keluarga" class="col-sm-2 control-label">Kartu Keluraga
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="siswa_tk_kartu_keluarga_galery"></div>
                                <input class="data_file data_file_uuid" name="siswa_tk_kartu_keluarga_uuid" id="siswa_tk_kartu_keluarga_uuid" type="hidden" value="<?= set_value('siswa_tk_kartu_keluarga_uuid'); ?>">
                                <input class="data_file" name="siswa_tk_kartu_keluarga_name" id="siswa_tk_kartu_keluarga_name" type="hidden" value="<?= set_value('siswa_tk_kartu_keluarga_name', $siswa_tk->kartu_keluarga); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="sumber_informasi" class="col-sm-2 control-label">Sumber Informasi
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="sumber_informasi" id="sumber_informasi" placeholder="Sumber Informasi" value="<?= set_value('sumber_informasi', $siswa_tk->sumber_informasi); ?>">
                                <small class="info help-block">
                                    <b>Input Sumber Informasi</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="alasan_tertarik" class="col-sm-2 control-label">Alasan Tertarik
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="alasan_tertarik" id="alasan_tertarik" placeholder="Alasan Tertarik" value="<?= set_value('alasan_tertarik', $siswa_tk->alasan_tertarik); ?>">
                                <small class="info help-block">
                                    <b>Input Alasan Tertarik</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="id_tingkatan" class="col-sm-2 control-label">Tingkatan
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select name="id_tingkatan" id="id_tingkatan" class="form-control">
                                    <option value="">- Pilih Tingkatan -</option>
                                    <?php foreach (db_get_all_data('tingkatan_tk') as $row): ?>
                                    <option value="<?= $row->id_tingkatan_tk ?>" <?= ($siswa_tk->id_tingkatan == $row->id_tingkatan_tk) ? 'selected' : ''; ?>><?= $row->label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group  wrapper-options-crud">
                            <label for="is_mutasi" class="col-sm-2 control-label">Mutasi?
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div class="col-md-3 padding-left-0">
                                    <label>
                                        <input <?= $siswa_tk->is_mutasi == "1" ? "checked" : ""; ?> type="radio" class="flat-red" name="is_mutasi" value="1"> Ya </label>
                                </div>
                                <div class="col-md-3 padding-left-0">
                                    <label>
                                        <input <?= $siswa_tk->is_mutasi == "2" ? "checked" : ""; ?> type="radio" class="flat-red" name="is_mutasi" value="2"> Tidak </label>
                                </div>
                                </select>
                                <div class="row-fluid clear-both">
                                    <small class="info help-block">
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="kelas_mutasi" class="col-sm-2 control-label">Kelas Mutasi
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="kelas_mutasi" id="kelas_mutasi" placeholder="Kelas Mutasi" value="<?= set_value('kelas_mutasi', $siswa_tk->kelas_mutasi); ?>">
                                <small class="info help-block">
                                    <b>Input Kelas Mutasi</b> Max Length : 11.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="status_lulus" class="col-sm-2 control-label">Status Lulus
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="status_lulus" id="status_lulus" data-placeholder="Select Status Lulus">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('status_lulus') as $row) : ?>
                                        <option <?= $row->id_status_lulus ==  $siswa_tk->status_lulus ? 'selected' : ''; ?> value="<?= $row->id_status_lulus ?>"><?= $row->status_lulus; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block text-danger">
                                    *Setelah dinyatakan lulus, dapat mengaktifkan virtual account untuk pembayaran daftar ulang pada menu <b>Status Daftar Ulang</b> 
                                </small>
                            </div>
                        </div>

                        <div id="tgl_daftar_ulang">
                            <div class="form-group ">
                                <label for="tgl_daftar_ulang" class="col-sm-2 control-label">Tanggal Daftar Ulang
                                    <i class="required">*</i>
                                </label>
                                <div class="col-sm-6">
                                    <div class="input-group date col-sm-8">
                                        <input type="text" class="form-control pull-right datepicker" name="tgl_daftar_ulang" placeholder="Tanggal Daftar Ulang" id="tgl_daftar_ulang" value="<?= set_value('tgl_daftar_ulang', $siswa_tk->tgl_daftar_ulang); ?>">
                                    </div>
                                    <small class="info help-block">
                                    </small>
                                </div>
                            </div>
                        </div>


                        <div class="form-group ">
                            <label for="no_peserta" class="col-sm-2 control-label">No Peserta Ujian
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="no_peserta" id="no_peserta" placeholder="No Peserta Ujian" value="<?= set_value('no_peserta', $siswa_tk->no_peserta); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="no_transaksi" class="col-sm-2 control-label">No Transaksi
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="no_transaksi" id="no_transaksi" placeholder="No Transaksi" value="<?= set_value('no_transaksi', $siswa_tk->no_transaksi); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="provinsi_sekolah" class="col-sm-2 control-label">Provinsi Sekolah Asal
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="provinsi_sekolah" id="provinsi_sekolah" data-placeholder="Select Provinsi Sekolah Asal">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('provinces') as $row) : ?>
                                        <option <?= $row->id ==  $siswa_tk->provinsi_sekolah ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>



                        <div class="form-group ">
                            <label for="kota_sekolah" class="col-sm-2 control-label">Kota Sekolah Asal
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="kota_sekolah" id="kota_sekolah" data-placeholder="Select Kota Sekolah Asal">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('regencies') as $row) : ?>
                                        <option <?= $row->id ==  $siswa_tk->kota_sekolah ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>



                        <div class="form-group ">
                            <label for="kecamatan_sekolah" class="col-sm-2 control-label">Kecamatan Sekolah Asal
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="kecamatan_sekolah" id="kecamatan_sekolah" data-placeholder="Select Kecamatan Sekolah Asal">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('districts') as $row) : ?>
                                        <option <?= $row->id ==  $siswa_tk->kecamatan_sekolah ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>



                        <div class="form-group ">
                            <label for="kelurahan_sekolah" class="col-sm-2 control-label">Kelurahan Sekolah Asal
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="kelurahan_sekolah" id="kelurahan_sekolah" data-placeholder="Select Kelurahan Sekolah Asal">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('areas') as $row) : ?>
                                        <option <?= $row->id ==  $siswa_tk->kelurahan_sekolah ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>



                        <div class="form-group ">
                            <label for="va_number" class="col-sm-2 control-label">Virtual Account
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="va_number" id="va_number" placeholder="Virtual Account" value="<?= set_value('va_number', $siswa_tk->va_number); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="va_number" class="col-sm-2 control-label">Tampilkan data
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="is_show" id="is_show" data-placeholder="Pilih status">
                                    <option value=""></option>
                                    <option value="1" selected>Iya</option>
                                    <option value="0">Tidak</option>
                                </select>                                
                                <small class="info help-block text-danger">
                                    *Perhatian hati-hati untuk menampilkan data kembali perlu update secara langsung pada database
                                </small>
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
        if($( "#status_lulus" ).val()==2){
            $("#tgl_daftar_ulang").show();
        }else{
            $("#tgl_daftar_ulang").hide();
        }
        
        $( "#status_lulus" ).change(function() {
            var val=$(this).val()
            if(val==2){
                $("#tgl_daftar_ulang").show();
            }else{
                $("#tgl_daftar_ulang").hide();
            }
        });

        CKEDITOR.replace('alamat');
        var alamat = CKEDITOR.instances.alamat;

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
                        window.location.href = BASE_URL + 'administrator/siswa_tk';
                    }
                });

            return false;
        }); /*end btn cancel*/

        $('.btn_save').click(function() {
            $('.message').fadeOut();
            $('#alamat').val(alamat.getData());

            var form_siswa_tk = $('#form_siswa_tk');
            var data_post = form_siswa_tk.serializeArray();
            var save_type = $(this).attr('data-stype');
            data_post.push({
                name: 'save_type',
                value: save_type
            });

            $('.loading').show();

            $.ajax({
                    url: form_siswa_tk.attr('action'),
                    type: 'POST',
                    dataType: 'json',
                    data: data_post,
                })
                .done(function(res) {
                    $('form').find('.form-group').removeClass('has-error');
                    $('form').find('.error-input').remove();
                    $('.steps li').removeClass('error');
                    if (res.success) {
                        var id = $('#siswa_tk_image_galery').find('li').attr('qq-file-id');
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

        var params = {};
        params[csrf] = token;

        $('#siswa_tk_foto_peserta_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/siswa_tk/upload_foto_peserta_file',
                params: params
            },
            deleteFile: {
                enabled: true, // defaults to false
                endpoint: BASE_URL + '/administrator/siswa_tk/delete_foto_peserta_file'
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            session: {
                endpoint: BASE_URL + 'administrator/siswa_tk/get_foto_peserta_file/<?= $siswa_tk->id_siswa_tk; ?>',
                refreshOnRequest: true
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
                        var uuid = $('#siswa_tk_foto_peserta_galery').fineUploader('getUuid', id);
                        $('#siswa_tk_foto_peserta_uuid').val(uuid);
                        $('#siswa_tk_foto_peserta_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit: function(id, name) {
                    var uuid = $('#siswa_tk_foto_peserta_uuid').val();
                    $.get(BASE_URL + '/administrator/siswa_tk/delete_foto_peserta_file/' + uuid);
                },
                onDeleteComplete: function(id, xhr, isError) {
                    if (isError == false) {
                        $('#siswa_tk_foto_peserta_uuid').val('');
                        $('#siswa_tk_foto_peserta_name').val('');
                    }
                }
            }
        }); /*end foto_peserta galey*/
        var params = {};
        params[csrf] = token;

        $('#siswa_tk_akte_lahir_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/siswa_tk/upload_akte_lahir_file',
                params: params
            },
            deleteFile: {
                enabled: true, // defaults to false
                endpoint: BASE_URL + '/administrator/siswa_tk/delete_akte_lahir_file'
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            session: {
                endpoint: BASE_URL + 'administrator/siswa_tk/get_akte_lahir_file/<?= $siswa_tk->id_siswa_tk; ?>',
                refreshOnRequest: true
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
                        var uuid = $('#siswa_tk_akte_lahir_galery').fineUploader('getUuid', id);
                        $('#siswa_tk_akte_lahir_uuid').val(uuid);
                        $('#siswa_tk_akte_lahir_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit: function(id, name) {
                    var uuid = $('#siswa_tk_akte_lahir_uuid').val();
                    $.get(BASE_URL + '/administrator/siswa_tk/delete_akte_lahir_file/' + uuid);
                },
                onDeleteComplete: function(id, xhr, isError) {
                    if (isError == false) {
                        $('#siswa_tk_akte_lahir_uuid').val('');
                        $('#siswa_tk_akte_lahir_name').val('');
                    }
                }
            }
        }); /*end akte_lahir galey*/
        var params = {};
        params[csrf] = token;

        $('#siswa_tk_kartu_keluarga_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/siswa_tk/upload_kartu_keluarga_file',
                params: params
            },
            deleteFile: {
                enabled: true, // defaults to false
                endpoint: BASE_URL + '/administrator/siswa_tk/delete_kartu_keluarga_file'
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            session: {
                endpoint: BASE_URL + 'administrator/siswa_tk/get_kartu_keluarga_file/<?= $siswa_tk->id_siswa_tk; ?>',
                refreshOnRequest: true
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
                        var uuid = $('#siswa_tk_kartu_keluarga_galery').fineUploader('getUuid', id);
                        $('#siswa_tk_kartu_keluarga_uuid').val(uuid);
                        $('#siswa_tk_kartu_keluarga_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit: function(id, name) {
                    var uuid = $('#siswa_tk_kartu_keluarga_uuid').val();
                    $.get(BASE_URL + '/administrator/siswa_tk/delete_kartu_keluarga_file/' + uuid);
                },
                onDeleteComplete: function(id, xhr, isError) {
                    if (isError == false) {
                        $('#siswa_tk_kartu_keluarga_uuid').val('');
                        $('#siswa_tk_kartu_keluarga_name').val('');
                    }
                }
            }
        }); /*end kartu_keluarga galey*/




        async function chain() {}

        chain();




    }); /*end doc ready*/
</script>