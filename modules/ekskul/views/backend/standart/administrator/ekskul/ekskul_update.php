

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
        Ekstrakurikuler        <small>Edit Ekstrakurikuler</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/ekskul'); ?>">Ekstrakurikuler</a></li>
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
                            <h3 class="widget-user-username">Ekstrakurikuler</h3>
                            <h5 class="widget-user-desc">Edit Ekstrakurikuler</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/ekskul/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_ekskul', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_ekskul', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nama" class="col-sm-2 control-label">Ekstrakurikuler 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama" id="nama" placeholder="Ekstrakurikuler" value="<?= set_value('nama', $ekskul->nama); ?>">
                                <small class="info help-block">
                                <b>Input Nama</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="foto" class="col-sm-2 control-label">Foto 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="ekskul_foto_galery"></div>
                                <input class="data_file data_file_uuid" name="ekskul_foto_uuid" id="ekskul_foto_uuid" type="hidden" value="<?= set_value('ekskul_foto_uuid'); ?>">
                                <input class="data_file" name="ekskul_foto_name" id="ekskul_foto_name" type="hidden" value="<?= set_value('ekskul_foto_name', $ekskul->foto); ?>">
                                <small class="info help-block">
                                <b>Input Foto</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="judul" class="col-sm-2 control-label">Judul 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="judul" id="judul" placeholder="Judul" value="<?= set_value('judul', $ekskul->judul); ?>">
                                <small class="info help-block">
                                <b>Input Judul</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="konten" class="col-sm-2 control-label">Konten 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="konten" name="konten" rows="10" cols="80"> <?= set_value('konten', $ekskul->konten); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group  wrapper-options-crud">
                            <label for="hari" class="col-sm-2 control-label">Hari 
                            </label>
                            <div class="col-sm-8">
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input <?= in_array('senin', explode(',', $ekskul->hari)) ? 'checked' : ''; ?>  type="checkbox" class="flat-red" name="hari[]" value="senin"> Senin                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input <?= in_array('selasa', explode(',', $ekskul->hari)) ? 'checked' : ''; ?>  type="checkbox" class="flat-red" name="hari[]" value="selasa"> Selasa                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input <?= in_array('rabu', explode(',', $ekskul->hari)) ? 'checked' : ''; ?>  type="checkbox" class="flat-red" name="hari[]" value="rabu"> Rabu                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input <?= in_array('kamis', explode(',', $ekskul->hari)) ? 'checked' : ''; ?>  type="checkbox" class="flat-red" name="hari[]" value="kamis"> Kamis                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input <?= in_array('jumat', explode(',', $ekskul->hari)) ? 'checked' : ''; ?>  type="checkbox" class="flat-red" name="hari[]" value="jumat"> Jumat                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input <?= in_array('sabtu', explode(',', $ekskul->hari)) ? 'checked' : ''; ?>  type="checkbox" class="flat-red" name="hari[]" value="sabtu"> Sabtu                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input <?= in_array('minggu', explode(',', $ekskul->hari)) ? 'checked' : ''; ?>  type="checkbox" class="flat-red" name="hari[]" value="minggu"> Minggu                                    </label>
                                    </div>
                                                                        <div class="row-fluid clear-both">
                                    <small class="info help-block">
                                    <b>Input Hari</b> Max Length : 50.</small>
                                    </div>
                                    
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jam" class="col-sm-2 control-label">Jam 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="jam" id="jam" value="<?= set_value('ekskul_jam_name', $ekskul->jam); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tanggal_posting" class="col-sm-2 control-label">Tanggal Posting 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="tanggal_posting"  placeholder="Tanggal Posting" id="tanggal_posting" value="<?= set_value('tanggal_posting', $ekskul->tanggal_posting); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nominal_biaya" class="col-sm-2 control-label">Nominal Biaya 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nominal_biaya" id="nominal_biaya" placeholder="Nominal Biaya" value="<?= set_value('nominal_biaya', $ekskul->nominal_biaya); ?>">
                                <small class="info help-block">
                                <b>Input Nominal Biaya</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="link_daftar" class="col-sm-2 control-label">Link Pendaftaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="link_daftar" name="link_daftar" rows="10" cols="80"> <?= set_value('link_daftar', $ekskul->link_daftar); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="jenjang" id="jenjang" data-placeholder="Select Jenjang" >
                                    <option value=""></option>
                                    <option <?= $ekskul->jenjang == "sd" ? 'selected' :''; ?> value="sd">SD</option>
                                    <option <?= $ekskul->jenjang == "smp" ? 'selected' :''; ?> value="smp">SMP</option>
                                    <option <?= $ekskul->jenjang == "sma" ? 'selected' :''; ?> value="sma">SMA</option>
                                    <option <?= $ekskul->jenjang == "ft" ? 'selected' :''; ?> value="ft">France Track</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Jenjang</b> Max Length : 20.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="tahun_ajaran_aktif" class="col-sm-2 control-label">Tahun Ajaran Aktif
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tahun_ajaran_aktif" id="tahun_ajaran_aktif" placeholder="Tahun Ajaran Aktif" value="<?= set_value('tahun_ajaran_aktif', $ekskul->tahun_ajaran_aktif); ?>">
                                <small class="info help-block">
                                <b>Kosongkan jika ekskul tidak diadakan.</b> </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="semester_aktif" class="col-sm-2 control-label">Semester Aktif
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="semester_aktif" id="semester_aktif" data-placeholder="Select Semester Aktif" >
                                    <option value=""></option>
                                    <option value="1" <?php echo ($ekskul->semester_aktif == 1) ? "selected" :"" ; ?>>Ganjil</option>
                                    <option value="2" <?php echo ($ekskul->semester_aktif == 2) ? "selected" :"" ; ?>>Genap</option>
                                </select>
                                <small class="info help-block"><b>Kosongkan jika ekskul tidak diadakan.</b> </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="bank" class="col-sm-2 control-label">Bank
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bank" id="bank" placeholder="Bank" value="<?= set_value('bank', $ekskul->bank); ?>">
                                <small class="info help-block">
                                <b>Input Bank</b> Max Length : 50.</small>

                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="rekening" class="col-sm-2 control-label">Rekening
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="rekening" id="rekening" placeholder="Rekening" value="<?= set_value('rekening', $ekskul->rekening); ?>">
                                <small class="info help-block">
                                <b>Input Rekening</b> Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="atas_nama_rekening" class="col-sm-2 control-label">Atas Nama Rekening
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="atas_nama_rekening" id="atas_nama_rekening" placeholder="Atas Nama Rekening" value="<?= set_value('atas_nama_rekening', $ekskul->atas_nama_rekening); ?>">
                                <small class="info help-block">
                                <b>Input Rekening</b> Max Length : 100.</small>
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
       
      
      CKEDITOR.replace('konten'); 
      var konten = CKEDITOR.instances.konten;
            CKEDITOR.replace('link_daftar'); 
      var link_daftar = CKEDITOR.instances.link_daftar;
                   
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
              window.location.href = BASE_URL + 'administrator/ekskul';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#konten').val(konten.getData());
                $('#link_daftar').val(link_daftar.getData());
                    
        var form_ekskul = $('#form_ekskul');
        var data_post = form_ekskul.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_ekskul.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#ekskul_image_galery').find('li').attr('qq-file-id');
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

       $('#ekskul_foto_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/ekskul/upload_foto_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/ekskul/delete_foto_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/ekskul/get_foto_file/<?= $ekskul->id_ekskul; ?>',
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
                   var uuid = $('#ekskul_foto_galery').fineUploader('getUuid', id);
                   $('#ekskul_foto_uuid').val(uuid);
                   $('#ekskul_foto_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#ekskul_foto_uuid').val();
                  $.get(BASE_URL + '/administrator/ekskul/delete_foto_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#ekskul_foto_uuid').val('');
                  $('#ekskul_foto_name').val('');
                }
              }
          }
      }); /*end foto galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>