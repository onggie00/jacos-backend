
<!-- Fine Uploader Gallery CSS file
    ====================================================================== -->
<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Fine Uploader jQuery JS file
    ====================================================================== -->
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
    function domo(){

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

    jQuery(document).ready(domo);
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Siswa France Track        <small><?= cclang('new', ['Siswa France Track']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/siswa_ft'); ?>">Siswa France Track</a></li>
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
                            <h3 class="widget-user-username">Siswa France Track</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Siswa France Track']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_siswa_ft', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_siswa_ft', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nama_lengkap" class="col-sm-2 control-label">Nama Lengkap 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="nama_lengkap" name="nama_lengkap" rows="5" class="textarea form-control"><?= set_value('nama_lengkap'); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="email" class="col-sm-2 control-label">Email 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?= set_value('email'); ?>">
                                <small class="info help-block">
                                <b>Input Email</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="email_ms_office" class="col-sm-2 control-label">Email Ms. Office 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email_ms_office" id="email_ms_office" placeholder="Email Ms. Office" value="<?= set_value('email_ms_office'); ?>">
                                <small class="info help-block">
                                <b>Input Email Ms Office</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nisn" class="col-sm-2 control-label">NISN 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nisn" id="nisn" placeholder="NISN" value="<?= set_value('nisn'); ?>">
                                <small class="info help-block">
                                <b>Input Nisn</b> Max Length : 30.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tempat_lahir" class="col-sm-2 control-label">Tempat Lahir 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" placeholder="Tempat Lahir" value="<?= set_value('tempat_lahir'); ?>">
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
                              <input type="text" class="form-control pull-right datepicker" name="tgl_lahir"  placeholder="Tanggal Lahir" id="tgl_lahir">
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
                                    <input type="radio" class="flat-red" name="jenis_kelamin" value="Laki-laki"> laki-laki                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input type="radio" class="flat-red" name="jenis_kelamin" value="Perempuan"> perempuan                                    </label>
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
                                <input type="text" class="form-control" name="agama" id="agama" placeholder="Agama" value="<?= set_value('agama'); ?>">
                                <small class="info help-block">
                                <b>Input Agama</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="email_ms_office_ortu" class="col-sm-2 control-label">Email Ms. Office Ortu 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email_ms_office_ortu" id="email_ms_office_ortu" placeholder="Email Ms. Office Ortu" value="<?= set_value('email_ms_office_ortu'); ?>">
                                <small class="info help-block">
                                <b>Input Email Ms Office Ortu</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nama_ibu" class="col-sm-2 control-label">Nama Ibu 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_ibu" id="nama_ibu" placeholder="Nama Ibu" value="<?= set_value('nama_ibu'); ?>">
                                <small class="info help-block">
                                <b>Input Nama Ibu</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="pekerjaan_ibu" class="col-sm-2 control-label">Pekerjaan Ibu 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="pekerjaan_ibu" id="pekerjaan_ibu" placeholder="Pekerjaan Ibu" value="<?= set_value('pekerjaan_ibu'); ?>">
                                <small class="info help-block">
                                <b>Input Pekerjaan Ibu</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="notelp_ibu" class="col-sm-2 control-label">No. Telepon Ibu 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="notelp_ibu" id="notelp_ibu" placeholder="No. Telepon Ibu" value="<?= set_value('notelp_ibu'); ?>">
                                <small class="info help-block">
                                <b>Input Notelp Ibu</b> Max Length : 14.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nama_ayah" class="col-sm-2 control-label">Nama Ayah 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_ayah" id="nama_ayah" placeholder="Nama Ayah" value="<?= set_value('nama_ayah'); ?>">
                                <small class="info help-block">
                                <b>Input Nama Ayah</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="pekerjaan_ayah" class="col-sm-2 control-label">Pekerjaan Ayah 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="pekerjaan_ayah" id="pekerjaan_ayah" placeholder="Pekerjaan Ayah" value="<?= set_value('pekerjaan_ayah'); ?>">
                                <small class="info help-block">
                                <b>Input Pekerjaan Ayah</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="notelp_ayah" class="col-sm-2 control-label">No. Telepon Ayah 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="notelp_ayah" id="notelp_ayah" placeholder="No. Telepon Ayah" value="<?= set_value('notelp_ayah'); ?>">
                                <small class="info help-block">
                                <b>Input Notelp Ayah</b> Max Length : 14.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="alamat" class="col-sm-2 control-label">Alamat 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="alamat" name="alamat" rows="5" class="textarea form-control"><?= set_value('alamat'); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="provinsi" class="col-sm-2 control-label">Provinsi
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="provinsi" id="provinsi" data-placeholder="Select Provinsi">
                                    <option value=""></option>
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>


                        <div class="form-group ">
                            <label for="kota" class="col-sm-2 control-label">Kota
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="kota" id="kota" data-placeholder="Silahkan pilih provinsi terlebih dahulu">
                                </select>
                                <small class="info help-block">
                                    <b>Input Kota</b> Max Length : 11.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="kecamatan" class="col-sm-2 control-label">Kecamatan
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="kecamatan" id="kecamatan" data-placeholder="Silahkan pilih kota terlebih dahulu">
                                </select>
                                <small class="info help-block">
                                    <b>Input Kecamatan</b> Max Length : 11.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="kelurahan" class="col-sm-2 control-label">Kelurahan
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="kelurahan" id="kelurahan" data-placeholder="Silahkan pilih kecamatan terlebih dahulu">
                                </select>
                                <small class="info help-block">
                                    <b>Input Kelurahan</b> Max Length : 11.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="kode_pos" class="col-sm-2 control-label">Kode Pos 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kode_pos" id="kode_pos" placeholder="Kode Pos" value="<?= set_value('kode_pos'); ?>">
                                <small class="info help-block">
                                <b>Input Kode Pos</b> Max Length : 8.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="sekolah_asal" class="col-sm-2 control-label">Sekolah Asal 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="sekolah_asal" name="sekolah_asal" rows="5" class="textarea form-control"><?= set_value('sekolah_asal'); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="foto_peserta" class="col-sm-2 control-label">Foto Peserta 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="siswa_ft_foto_peserta_galery"></div>
                                <input class="data_file" name="siswa_ft_foto_peserta_uuid" id="siswa_ft_foto_peserta_uuid" type="hidden" value="<?= set_value('siswa_ft_foto_peserta_uuid'); ?>">
                                <input class="data_file" name="siswa_ft_foto_peserta_name" id="siswa_ft_foto_peserta_name" type="hidden" value="<?= set_value('siswa_ft_foto_peserta_name'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="akte_lahir" class="col-sm-2 control-label">Akte Lahir 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="siswa_ft_akte_lahir_galery"></div>
                                <input class="data_file" name="siswa_ft_akte_lahir_uuid" id="siswa_ft_akte_lahir_uuid" type="hidden" value="<?= set_value('siswa_ft_akte_lahir_uuid'); ?>">
                                <input class="data_file" name="siswa_ft_akte_lahir_name" id="siswa_ft_akte_lahir_name" type="hidden" value="<?= set_value('siswa_ft_akte_lahir_name'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="kartu_keluarga" class="col-sm-2 control-label">Kartu Keluraga 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="siswa_ft_kartu_keluarga_galery"></div>
                                <input class="data_file" name="siswa_ft_kartu_keluarga_uuid" id="siswa_ft_kartu_keluarga_uuid" type="hidden" value="<?= set_value('siswa_ft_kartu_keluarga_uuid'); ?>">
                                <input class="data_file" name="siswa_ft_kartu_keluarga_name" id="siswa_ft_kartu_keluarga_name" type="hidden" value="<?= set_value('siswa_ft_kartu_keluarga_name'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="sumber_informasi" class="col-sm-2 control-label">Sumber Informasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="sumber_informasi" id="sumber_informasi" placeholder="Sumber Informasi" value="<?= set_value('sumber_informasi'); ?>">
                                <small class="info help-block">
                                <b>Input Sumber Informasi</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="alasan_tertarik" class="col-sm-2 control-label">Alasan Tertarik 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="alasan_tertarik" id="alasan_tertarik" placeholder="Alasan Tertarik" value="<?= set_value('alasan_tertarik'); ?>">
                                <small class="info help-block">
                                <b>Input Alasan Tertarik</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="peminatan_ft" class="col-sm-2 control-label">Peminatan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="peminatan_ft" id="peminatan_ft" data-placeholder="Select Peminatan" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('peminatan_ft') as $row): ?>
                                    <option value="<?= $row->id_peminatan_ft ?>"><?= $row->peminatan; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Peminatan Ft</b> Max Length : 100.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="status_lulus" class="col-sm-2 control-label">Status Lulus 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="status_lulus" id="status_lulus" data-placeholder="Select Status Lulus" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('status_lulus',['id_status_lulus'=>2]) as $row): ?>
                                    <option value="<?= $row->id_status_lulus ?>"><?= $row->status_lulus; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tahun_ajaran" id="tahun_ajaran" placeholder="Tahun Ajaran" value="">
                                <small class="info help-block text-danger">
                                    *Periode Tahun Ajaran
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="gelombang" class="col-sm-2 control-label">Gelombang
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="gelombang" id="gelombang" placeholder="Gelombang" value="">
                                <small class="info help-block text-danger">
                                    *Periode Gelombang
                                </small>
                            </div>
                        </div>
                        
                                                <div class="message"></div>
                                                <div class="row-fluid col-md-7 container-button-bottom">
                           <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="<?= cclang('save_button'); ?> (Ctrl+s)">
                            <i class="fa fa-save" ></i> <?= cclang('save_button'); ?>
                            </button>
                            <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save" data-stype='back' title="<?= cclang('save_and_go_the_list_button'); ?> (Ctrl+d)">
                            <i class="ion ion-ios-list-outline" ></i> <?= cclang('save_and_go_the_list_button'); ?>
                            </a>
                            <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="<?= cclang('cancel_button'); ?> (Ctrl+x)">
                            <i class="fa fa-undo" ></i> <?= cclang('cancel_button'); ?>
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
    $(document).ready(function(){

        $("#kota").select2();$("#kecamatan").select2();$("#kelurahan").select2();
        $("#provinsi").select2({
            ajax: { 
                url: `<?= base_url() ?>apiapp/provinsi_web`,
                type: "get",
                dataType: 'json',
                headers: {
                    "x-api-key" : '<?= get_api_key() ?>'
                },
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term // search term
                    };
                },
                processResults: function (response) {
                    
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });

        $( "#provinsi" ).change(function() {
            $("#kota").select2({
                ajax: { 
                    url: `<?= base_url() ?>apiapp/kota_web?id_provinsi=`+$( "#provinsi" ).val(),
                    type: "get",
                    dataType: 'json',
                    headers: {
                        "x-api-key" : '<?= get_api_key() ?>'
                    },
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });

        $( "#kota" ).change(function() {
            $("#kecamatan").select2({
                ajax: { 
                    url: `<?= base_url() ?>apiapp/kecamatan_web?id_kota=`+$( "#kota" ).val(),
                    type: "get",
                    dataType: 'json',
                    headers: {
                        "x-api-key" : '<?= get_api_key() ?>'
                    },
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });

        $( "#kecamatan" ).change(function() {
            $("#kelurahan").select2({
                ajax: { 
                    url: `<?= base_url() ?>apiapp/kelurahan_web?id_kecamatan=`+$( "#kecamatan" ).val(),
                    type: "get",
                    dataType: 'json',
                    headers: {
                        "x-api-key" : '<?= get_api_key() ?>'
                    },
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });

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
              window.location.href = BASE_URL + 'administrator/siswa_ft';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_siswa_ft = $('#form_siswa_ft');
        var data_post = form_siswa_ft.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/siswa_ft/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('.steps li').removeClass('error');
          $('form').find('.error-input').remove();
          if(res.success) {
            var id_foto_peserta = $('#siswa_ft_foto_peserta_galery').find('li').attr('qq-file-id');
            var id_akte_lahir = $('#siswa_ft_akte_lahir_galery').find('li').attr('qq-file-id');
            var id_kartu_keluarga = $('#siswa_ft_kartu_keluarga_galery').find('li').attr('qq-file-id');
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            if (typeof id_foto_peserta !== 'undefined') {
                    $('#siswa_ft_foto_peserta_galery').fineUploader('deleteFile', id_foto_peserta);
                }
            if (typeof id_akte_lahir !== 'undefined') {
                    $('#siswa_ft_akte_lahir_galery').fineUploader('deleteFile', id_akte_lahir);
                }
            if (typeof id_kartu_keluarga !== 'undefined') {
                    $('#siswa_ft_kartu_keluarga_galery').fineUploader('deleteFile', id_kartu_keluarga);
                }
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
                
          } else {
            if (res.errors) {
                
                $.each(res.errors, function(index, val) {
                    $('form #'+index).parents('.form-group').addClass('has-error');
                    $('form #'+index).parents('.form-group').find('small').prepend(`
                      <div class="error-input">`+val+`</div>
                      `);
                });
                $('.steps li').removeClass('error');
                $('.content section').each(function(index, el) {
                    if ($(this).find('.has-error').length) {
                        $('.steps li:eq('+index+')').addClass('error').find('a').trigger('click');
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
      }); /*end btn save*/
      
              var params = {};
       params[csrf] = token;

       $('#siswa_ft_foto_peserta_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/siswa_ft/upload_foto_peserta_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/siswa_ft/delete_foto_peserta_file',
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
                   var uuid = $('#siswa_ft_foto_peserta_galery').fineUploader('getUuid', id);
                   $('#siswa_ft_foto_peserta_uuid').val(uuid);
                   $('#siswa_ft_foto_peserta_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#siswa_ft_foto_peserta_uuid').val();
                  $.get(BASE_URL + '/administrator/siswa_ft/delete_foto_peserta_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#siswa_ft_foto_peserta_uuid').val('');
                  $('#siswa_ft_foto_peserta_name').val('');
                }
              }
          }
      }); /*end foto_peserta galery*/
                     var params = {};
       params[csrf] = token;

       $('#siswa_ft_akte_lahir_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/siswa_ft/upload_akte_lahir_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/siswa_ft/delete_akte_lahir_file',
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
                   var uuid = $('#siswa_ft_akte_lahir_galery').fineUploader('getUuid', id);
                   $('#siswa_ft_akte_lahir_uuid').val(uuid);
                   $('#siswa_ft_akte_lahir_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#siswa_ft_akte_lahir_uuid').val();
                  $.get(BASE_URL + '/administrator/siswa_ft/delete_akte_lahir_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#siswa_ft_akte_lahir_uuid').val('');
                  $('#siswa_ft_akte_lahir_name').val('');
                }
              }
          }
      }); /*end akte_lahir galery*/
                     var params = {};
       params[csrf] = token;

       $('#siswa_ft_kartu_keluarga_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/siswa_ft/upload_kartu_keluarga_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/siswa_ft/delete_kartu_keluarga_file',
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
                   var uuid = $('#siswa_ft_kartu_keluarga_galery').fineUploader('getUuid', id);
                   $('#siswa_ft_kartu_keluarga_uuid').val(uuid);
                   $('#siswa_ft_kartu_keluarga_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#siswa_ft_kartu_keluarga_uuid').val();
                  $.get(BASE_URL + '/administrator/siswa_ft/delete_kartu_keluarga_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#siswa_ft_kartu_keluarga_uuid').val('');
                  $('#siswa_ft_kartu_keluarga_name').val('');
                }
              }
          }
      }); /*end kartu_keluarga galery*/
              
 
       

      
    
    
    }); /*end doc ready*/
</script>