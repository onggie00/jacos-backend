
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
        Guru Sma        <small><?= cclang('new', ['Guru Sma']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/guru_sma'); ?>">Guru Sma</a></li>
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
                            <h3 class="widget-user-username">Guru Sma</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Guru Sma']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_guru_sma', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_guru_sma', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nama_lengkap" class="col-sm-2 control-label">Nama Lengkap 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" value="<?= set_value('nama_lengkap'); ?>">
                                <small class="info help-block">
                                <b>Input Nama Lengkap</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nik" class="col-sm-2 control-label">Nik 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nik" id="nik" placeholder="Nik" value="<?= set_value('nik'); ?>">
                                <small class="info help-block">
                                <b>Input Nik</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nuptk" class="col-sm-2 control-label">Nuptk 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nuptk" id="nuptk" placeholder="Nuptk" value="<?= set_value('nuptk'); ?>">
                                <small class="info help-block">
                                <b>Input Nuptk</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="npp" class="col-sm-2 control-label">Npp 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npp" id="npp" placeholder="Npp" value="<?= set_value('npp'); ?>">
                                <small class="info help-block">
                                <b>Input Npp</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="npwp" class="col-sm-2 control-label">Npwp 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npwp" id="npwp" placeholder="Npwp" value="<?= set_value('npwp'); ?>">
                                <small class="info help-block">
                                <b>Input Npwp</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="alamat" class="col-sm-2 control-label">Alamat 
                            </label>
                            <div class="col-sm-8">
                                <textarea id="alamat" name="alamat" rows="5" cols="80"><?= set_value('Alamat'); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="agama" class="col-sm-2 control-label">Agama 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="agama" id="agama" data-placeholder="Select Agama" >
                                    <option value=""></option>
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Budha">Budha</option>
                                    <option value="Konghucu">Konghucu</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jenis_kelamin" class="col-sm-2 control-label">Jenis Kelamin 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="jenis_kelamin" id="jenis_kelamin" data-placeholder="Select Jenis Kelamin" >
                                    <option value=""></option>
                                    <option value="L">Laki-Laki</option>
                                    <option value="P">Perempuan</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="status_menikah" class="col-sm-2 control-label">Status Menikah 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="status_menikah" id="status_menikah" data-placeholder="Select Status Menikah" >
                                    <option value=""></option>
                                    <option value="sudah">Sudah Menikah</option>
                                    <option value="belum">Belum</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jumlah_anak" class="col-sm-2 control-label">Jumlah Anak 
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="jumlah_anak" id="jumlah_anak" placeholder="Jumlah Anak" value="<?= set_value('jumlah_anak'); ?>">
                                <small class="info help-block">
                                <b>Input Jumlah Anak</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="no_telp" class="col-sm-2 control-label">No Telp 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="no_telp" id="no_telp" placeholder="No Telp" value="<?= set_value('no_telp'); ?>">
                                <small class="info help-block">
                                <b>Input No Telp</b> Max Length : 25.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="email" class="col-sm-2 control-label">Email 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="<?= set_value('email'); ?>">
                                <small class="info help-block">
                                <b>Input Email</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_posisi" class="col-sm-2 control-label">Id Posisi 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_posisi" id="id_posisi" data-placeholder="Select Id Posisi" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('posisi_guru') as $row): ?>
                                    <option value="<?= $row->id_posisi ?>"><?= $row->nama_posisi; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Posisi</b> Max Length : 11.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="unit" class="col-sm-2 control-label">Unit 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="unit" id="unit" placeholder="Unit" value="<?= set_value('unit'); ?>">
                                <small class="info help-block">
                                <b>Input Unit</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_mapel" class="col-sm-2 control-label">Id Mapel 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_mapel" id="id_mapel" data-placeholder="Select Id Mapel" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('mata_pelajaran_sma') as $row): ?>
                                    <option value="<?= $row->id_mapel ?>"><?= $row->nama_mapel; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Mapel</b> Max Length : 11.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="status_kepegawaian" class="col-sm-2 control-label">Status Kepegawaian 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="status_kepegawaian" id="status_kepegawaian" placeholder="Status Kepegawaian" value="<?= set_value('status_kepegawaian'); ?>">
                                <small class="info help-block">
                                <b>Input Status Kepegawaian</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="informasi_kepala_pimpinan" class="col-sm-2 control-label">Informasi Kepala Pimpinan 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="informasi_kepala_pimpinan" id="informasi_kepala_pimpinan" placeholder="Informasi Kepala Pimpinan" value="<?= set_value('informasi_kepala_pimpinan'); ?>">
                                <small class="info help-block">
                                <b>Input Informasi Kepala Pimpinan</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="emp_code" class="col-sm-2 control-label">Emp Code 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="emp_code" id="emp_code" placeholder="Emp Code" value="<?= set_value('emp_code'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="token" class="col-sm-2 control-label">Token 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="token" id="token" placeholder="Token" value="<?= set_value('token'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="token_expired" class="col-sm-2 control-label">Token Expired 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="token_expired" id="token_expired" placeholder="Token Expired" value="<?= set_value('token_expired'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="email_ms_office" class="col-sm-2 control-label">Email Ms Office 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email_ms_office" id="email_ms_office" placeholder="Email Ms Office" value="<?= set_value('email_ms_office'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="foto_profil" class="col-sm-2 control-label">Foto Profil 
                            </label>
                            <div class="col-sm-8">
                                <div id="guru_sma_foto_profil_galery"></div>
                                <input class="data_file" name="guru_sma_foto_profil_uuid" id="guru_sma_foto_profil_uuid" type="hidden" value="<?= set_value('guru_sma_foto_profil_uuid'); ?>">
                                <input class="data_file" name="guru_sma_foto_profil_name" id="guru_sma_foto_profil_name" type="hidden" value="<?= set_value('guru_sma_foto_profil_name'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="no_kk" class="col-sm-2 control-label">No Kk 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="no_kk" id="no_kk" placeholder="No Kk" value="<?= set_value('no_kk'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tempat_lahir" class="col-sm-2 control-label">Tempat Lahir 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" placeholder="Tempat Lahir" value="<?= set_value('tempat_lahir'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tgl_lahir" class="col-sm-2 control-label">Tgl Lahir 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="tgl_lahir"  placeholder="Tgl Lahir" id="tgl_lahir">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="slip_gaji" class="col-sm-2 control-label">Slip Gaji 
                            </label>
                            <div class="col-sm-8">
                                <div id="guru_sma_slip_gaji_galery"></div>
                                <input class="data_file" name="guru_sma_slip_gaji_uuid" id="guru_sma_slip_gaji_uuid" type="hidden" value="<?= set_value('guru_sma_slip_gaji_uuid'); ?>">
                                <input class="data_file" name="guru_sma_slip_gaji_name" id="guru_sma_slip_gaji_name" type="hidden" value="<?= set_value('guru_sma_slip_gaji_name'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="presensi_role" class="col-sm-2 control-label">Presensi Role 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control " name="presensi_role"  placeholder="GURUSMA" id="presensi_role">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="keterangan_jabatan" class="col-sm-2 control-label">Keterangan Jabatan
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="keterangan_jabatan" id="keterangan_jabatan" placeholder="Keterangan Jabatan" value="<?= set_value('keterangan_jabatan'); ?>">
                                <small class="info help-block">
                                <b>Input Keterangan Jabatan</b> Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="jam_ajar" class="col-sm-2 control-label">Jam Ajar
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="jam_ajar" id="jam_ajar" placeholder="Jam Ajar" value="<?= set_value('jam_ajar'); ?>">
                                <small class="info help-block">
                                <b>Input Jam Ajar</b> Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="pendidikan_terakhir" class="col-sm-2 control-label">Pendidikan Terakhir
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="pendidikan_terakhir" id="pendidikan_terakhir" placeholder="Pendidikan Terakhir" value="<?= set_value('pendidikan_terakhir'); ?>">
                                <small class="info help-block">
                                <b>Input Pendidikan Terakhir</b> Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="universitas" class="col-sm-2 control-label">Universitas
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="universitas" id="universitas" placeholder="Universitas" value="<?= set_value('universitas'); ?>">
                                <small class="info help-block">
                                <b>Input Universitas</b> Max Length : 200.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="jurusan" class="col-sm-2 control-label">Jurusan
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="jurusan" id="jurusan" placeholder="Jurusan" value="<?= set_value('jurusan'); ?>">
                                <small class="info help-block">
                                <b>Input Jurusan</b> Max Length : 150.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="tahun_lulus" class="col-sm-2 control-label">Tahun Lulus
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="tahun_lulus" id="tahun_lulus" placeholder="Tahun Lulus" value="<?= set_value('tahun_lulus'); ?>">
                                <small class="info help-block">
                                <b>Input Tahun Lulus</b> Contoh : 2020.</small>
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
<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
<!-- Page script -->
<script>
    $(document).ready(function(){

                   CKEDITOR.replace('alamat'); 
      var alamat = CKEDITOR.instances.alamat;
                   
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
              window.location.href = BASE_URL + 'administrator/guru_sma';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#alamat').val(alamat.getData());
                    
        var form_guru_sma = $('#form_guru_sma');
        var data_post = form_guru_sma.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/guru_sma/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('.steps li').removeClass('error');
          $('form').find('.error-input').remove();
          if(res.success) {
            var id_foto_profil = $('#guru_sma_foto_profil_galery').find('li').attr('qq-file-id');
            var id_slip_gaji = $('#guru_sma_slip_gaji_galery').find('li').attr('qq-file-id');
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            if (typeof id_foto_profil !== 'undefined') {
                    $('#guru_sma_foto_profil_galery').fineUploader('deleteFile', id_foto_profil);
                }
            if (typeof id_slip_gaji !== 'undefined') {
                    $('#guru_sma_slip_gaji_galery').fineUploader('deleteFile', id_slip_gaji);
                }
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
            alamat.setData('');
                
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

       $('#guru_sma_foto_profil_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/guru_sma/upload_foto_profil_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/guru_sma/delete_foto_profil_file',
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
                   var uuid = $('#guru_sma_foto_profil_galery').fineUploader('getUuid', id);
                   $('#guru_sma_foto_profil_uuid').val(uuid);
                   $('#guru_sma_foto_profil_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#guru_sma_foto_profil_uuid').val();
                  $.get(BASE_URL + '/administrator/guru_sma/delete_foto_profil_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#guru_sma_foto_profil_uuid').val('');
                  $('#guru_sma_foto_profil_name').val('');
                }
              }
          }
      }); /*end foto_profil galery*/
                     var params = {};
       params[csrf] = token;

       $('#guru_sma_slip_gaji_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/guru_sma/upload_slip_gaji_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/guru_sma/delete_slip_gaji_file',
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
                   var uuid = $('#guru_sma_slip_gaji_galery').fineUploader('getUuid', id);
                   $('#guru_sma_slip_gaji_uuid').val(uuid);
                   $('#guru_sma_slip_gaji_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#guru_sma_slip_gaji_uuid').val();
                  $.get(BASE_URL + '/administrator/guru_sma/delete_slip_gaji_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#guru_sma_slip_gaji_uuid').val('');
                  $('#guru_sma_slip_gaji_name').val('');
                }
              }
          }
      }); /*end slip_gaji galery*/
              
 
       

      
    
    
    }); /*end doc ready*/
</script>