

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
        Pimpinan Smp        <small>Edit Pimpinan Smp</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/pimpinan_smp'); ?>">Pimpinan Smp</a></li>
        <li class="active">Edit</li>
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
                            <h3 class="widget-user-username">Pimpinan Smp</h3>
                            <h5 class="widget-user-desc">Edit Pimpinan Smp</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/pimpinan_smp/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_pimpinan_smp', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_pimpinan_smp', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nama_lengkap" class="col-sm-2 control-label">Nama Lengkap 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" value="<?= set_value('nama_lengkap', $pimpinan_smp->nama_lengkap); ?>">
                                <small class="info help-block">
                                <b>Input Nama Lengkap</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nik" class="col-sm-2 control-label">Nik 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nik" id="nik" placeholder="Nik" value="<?= set_value('nik', $pimpinan_smp->nik); ?>">
                                <small class="info help-block">
                                <b>Input Nik</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nuptk" class="col-sm-2 control-label">Nuptk 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nuptk" id="nuptk" placeholder="Nuptk" value="<?= set_value('nuptk', $pimpinan_smp->nuptk); ?>">
                                <small class="info help-block">
                                <b>Input Nuptk</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="npp" class="col-sm-2 control-label">Npp 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npp" id="npp" placeholder="Npp" value="<?= set_value('npp', $pimpinan_smp->npp); ?>">
                                <small class="info help-block">
                                <b>Input Npp</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="npwp" class="col-sm-2 control-label">Npwp 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npwp" id="npwp" placeholder="Npwp" value="<?= set_value('npwp', $pimpinan_smp->npwp); ?>">
                                <small class="info help-block">
                                <b>Input Npwp</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="alamat" class="col-sm-2 control-label">Alamat 
                            </label>
                            <div class="col-sm-8">
                                <textarea id="alamat" name="alamat" rows="10" cols="80"> <?= set_value('alamat', $pimpinan_smp->alamat); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="agama" class="col-sm-2 control-label">Agama 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="agama" id="agama" data-placeholder="Select Agama" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('agama') as $row): ?>
                                    <option <?=  $row->value ==  $pimpinan_smp->agama ? 'selected' : ''; ?> value="<?= $row->value ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Agama</b> Max Length : 255.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="jenis_kelamin" class="col-sm-2 control-label">Jenis Kelamin 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="jenis_kelamin" id="jenis_kelamin" data-placeholder="Select Jenis Kelamin" >
                                    <option value=""></option>
                                    <option <?= $pimpinan_smp->jenis_kelamin == "L" ? 'selected' :''; ?> value="L">laki-laki</option>
                                    <option <?= $pimpinan_smp->jenis_kelamin == "P" ? 'selected' :''; ?> value="P">Perempuan</option>
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
                                    <option <?= $pimpinan_smp->status_menikah == "sudah" ? 'selected' :''; ?> value="sudah">sudah</option>
                                    <option <?= $pimpinan_smp->status_menikah == "belum" ? 'selected' :''; ?> value="belum">belum</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jumlah_anak" class="col-sm-2 control-label">Jumlah Anak 
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="jumlah_anak" id="jumlah_anak" placeholder="Jumlah Anak" value="<?= set_value('jumlah_anak', $pimpinan_smp->jumlah_anak); ?>">
                                <small class="info help-block">
                                <b>Input Jumlah Anak</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="no_telp" class="col-sm-2 control-label">No Telp 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="no_telp" id="no_telp" placeholder="No Telp" value="<?= set_value('no_telp', $pimpinan_smp->no_telp); ?>">
                                <small class="info help-block">
                                <b>Input No Telp</b> Max Length : 25.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="email" class="col-sm-2 control-label">Email 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="<?= set_value('email', $pimpinan_smp->email); ?>">
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
                                    <?php foreach (db_get_all_data('posisi_pimpinan') as $row): ?>
                                    <option <?=  $row->id_posisi ==  $pimpinan_smp->id_posisi ? 'selected' : ''; ?> value="<?= $row->id_posisi ?>"><?= $row->nama_posisi; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Posisi</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="satuan_pendidikan" class="col-sm-2 control-label">Satuan Pendidikan 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="satuan_pendidikan" id="satuan_pendidikan" data-placeholder="Select Satuan Pendidikan" >
                                    <option value=""></option>
                                    <option <?= $pimpinan_smp->satuan_pendidikan == "sd" ? 'selected' :''; ?> value="sd">sd</option>
                                    <option <?= $pimpinan_smp->satuan_pendidikan == "smp" ? 'selected' :''; ?> value="smp">smp</option>
                                    <option <?= $pimpinan_smp->satuan_pendidikan == "sma" ? 'selected' :''; ?> value="sma">sma</option>
                                    <option <?= $pimpinan_smp->satuan_pendidikan == "ft" ? 'selected' :''; ?> value="ft">ft</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="unit" class="col-sm-2 control-label">Unit 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="unit" id="unit" placeholder="Unit" value="<?= set_value('unit', $pimpinan_smp->unit); ?>">
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
                                    <?php foreach (db_get_all_data('mata_pelajaran_smp') as $row): ?>
                                    <option <?=  $row->id_mapel ==  $pimpinan_smp->id_mapel ? 'selected' : ''; ?> value="<?= $row->id_mapel ?>"><?= $row->nama_mapel; ?></option>
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
                                <input type="text" class="form-control" name="status_kepegawaian" id="status_kepegawaian" placeholder="Status Kepegawaian" value="<?= set_value('status_kepegawaian', $pimpinan_smp->status_kepegawaian); ?>">
                                <small class="info help-block">
                                <b>Input Status Kepegawaian</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="informasi_kepala_pimpinan" class="col-sm-2 control-label">Informasi Kepala Pimpinan 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="informasi_kepala_pimpinan" id="informasi_kepala_pimpinan" placeholder="Informasi Kepala Pimpinan" value="<?= set_value('informasi_kepala_pimpinan', $pimpinan_smp->informasi_kepala_pimpinan); ?>">
                                <small class="info help-block">
                                <b>Input Informasi Kepala Pimpinan</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="emp_code" class="col-sm-2 control-label">Emp Code 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="emp_code" id="emp_code" placeholder="Emp Code" value="<?= set_value('emp_code', $pimpinan_smp->emp_code); ?>">
                                <small class="info help-block">
                                <b>Input Emp Code</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="token" class="col-sm-2 control-label">Token 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="token" id="token" placeholder="Token" value="<?= set_value('token', $pimpinan_smp->token); ?>">
                                <small class="info help-block">
                                <b>Input Token</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="token_expired" class="col-sm-2 control-label">Token Expired 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="token_expired"  placeholder="Token Expired" id="token_expired" value="<?= set_value('token_expired', $pimpinan_smp->token_expired); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="email_ms_office" class="col-sm-2 control-label">Email Ms Office 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email_ms_office" id="email_ms_office" placeholder="Email Ms Office" value="<?= set_value('email_ms_office', $pimpinan_smp->email_ms_office); ?>">
                                <small class="info help-block">
                                <b>Input Email Ms Office</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="foto_profil" class="col-sm-2 control-label">Foto Profil 
                            </label>
                            <div class="col-sm-8">
                                <div id="pimpinan_smp_foto_profil_galery"></div>
                                <input class="data_file data_file_uuid" name="pimpinan_smp_foto_profil_uuid" id="pimpinan_smp_foto_profil_uuid" type="hidden" value="<?= set_value('pimpinan_smp_foto_profil_uuid'); ?>">
                                <input class="data_file" name="pimpinan_smp_foto_profil_name" id="pimpinan_smp_foto_profil_name" type="hidden" value="<?= set_value('pimpinan_smp_foto_profil_name', $pimpinan_smp->foto_profil); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="no_kk" class="col-sm-2 control-label">No Kk 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="no_kk" id="no_kk" placeholder="No Kk" value="<?= set_value('no_kk', $pimpinan_smp->no_kk); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tempat_lahir" class="col-sm-2 control-label">Tempat Lahir 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" placeholder="Tempat Lahir" value="<?= set_value('tempat_lahir', $pimpinan_smp->tempat_lahir); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tgl_lahir" class="col-sm-2 control-label">Tgl Lahir 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="tgl_lahir"  placeholder="Tgl Lahir" id="tgl_lahir" value="<?= set_value('pimpinan_smp_tgl_lahir_name', $pimpinan_smp->tgl_lahir); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                       
                                                 
                                                <div class="form-group ">
                            <label for="slip_gaji" class="col-sm-2 control-label">Slip Gaji 
                            </label>
                            <div class="col-sm-8">
                                <div id="pimpinan_smp_slip_gaji_galery"></div>
                                <input class="data_file data_file_uuid" name="pimpinan_smp_slip_gaji_uuid" id="pimpinan_smp_slip_gaji_uuid" type="hidden" value="<?= set_value('pimpinan_smp_slip_gaji_uuid'); ?>">
                                <input class="data_file" name="pimpinan_smp_slip_gaji_name" id="pimpinan_smp_slip_gaji_name" type="hidden" value="<?= set_value('pimpinan_smp_slip_gaji_name', $pimpinan_smp->slip_gaji); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="presensi_role" class="col-sm-2 control-label">Presensi Role 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control " name="presensi_role"  placeholder="SMP" value="<?= set_value('presensi_role', $pimpinan_smp->presensi_role); ?>" id="presensi_role">
                            </div>
                            <small class="info help-block">
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
<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
<!-- Page script -->
<script>
    $(document).ready(function(){
       
      
      CKEDITOR.replace('alamat'); 
      var alamat = CKEDITOR.instances.alamat;
                   
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
              window.location.href = BASE_URL + 'administrator/pimpinan_smp';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#alamat').val(alamat.getData());
                    
        var form_pimpinan_smp = $('#form_pimpinan_smp');
        var data_post = form_pimpinan_smp.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_pimpinan_smp.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#pimpinan_smp_image_galery').find('li').attr('qq-file-id');
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
      }); /*end btn save*/
      
                     var params = {};
       params[csrf] = token;

       $('#pimpinan_smp_foto_profil_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/pimpinan_smp/upload_foto_profil_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/pimpinan_smp/delete_foto_profil_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/pimpinan_smp/get_foto_profil_file/<?= $pimpinan_smp->id_pimpinan; ?>',
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
                   var uuid = $('#pimpinan_smp_foto_profil_galery').fineUploader('getUuid', id);
                   $('#pimpinan_smp_foto_profil_uuid').val(uuid);
                   $('#pimpinan_smp_foto_profil_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#pimpinan_smp_foto_profil_uuid').val();
                  $.get(BASE_URL + '/administrator/pimpinan_smp/delete_foto_profil_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#pimpinan_smp_foto_profil_uuid').val('');
                  $('#pimpinan_smp_foto_profil_name').val('');
                }
              }
          }
      }); /*end foto_profil galey*/
                            var params = {};
       params[csrf] = token;

       $('#pimpinan_smp_slip_gaji_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/pimpinan_smp/upload_slip_gaji_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/pimpinan_smp/delete_slip_gaji_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/pimpinan_smp/get_slip_gaji_file/<?= $pimpinan_smp->id_pimpinan; ?>',
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
                   var uuid = $('#pimpinan_smp_slip_gaji_galery').fineUploader('getUuid', id);
                   $('#pimpinan_smp_slip_gaji_uuid').val(uuid);
                   $('#pimpinan_smp_slip_gaji_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#pimpinan_smp_slip_gaji_uuid').val();
                  $.get(BASE_URL + '/administrator/pimpinan_smp/delete_slip_gaji_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#pimpinan_smp_slip_gaji_uuid').val('');
                  $('#pimpinan_smp_slip_gaji_name').val('');
                }
              }
          }
      }); /*end slip_gaji galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>