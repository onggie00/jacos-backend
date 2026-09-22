
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
        Ekstrakurikuler        <small><?= cclang('new', ['Ekstrakurikuler']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/ekskul'); ?>">Ekstrakurikuler</a></li>
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
                            <h3 class="widget-user-username">Ekstrakurikuler</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Ekstrakurikuler']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_ekskul', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_ekskul', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nama" class="col-sm-2 control-label">Ekstrakurikuler 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama" id="nama" placeholder="Ekstrakurikuler" value="<?= set_value('nama'); ?>">
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
                                <input class="data_file" name="ekskul_foto_uuid" id="ekskul_foto_uuid" type="hidden" value="<?= set_value('ekskul_foto_uuid'); ?>">
                                <input class="data_file" name="ekskul_foto_name" id="ekskul_foto_name" type="hidden" value="<?= set_value('ekskul_foto_name'); ?>">
                                <small class="info help-block">
                                <b>Input Foto</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="judul" class="col-sm-2 control-label">Judul 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="judul" id="judul" placeholder="Judul" value="<?= set_value('judul'); ?>">
                                <small class="info help-block">
                                <b>Input Judul</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="konten" class="col-sm-2 control-label">Konten 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="konten" name="konten" rows="5" cols="80"><?= set_value('Konten'); ?></textarea>
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
                                    <input type="checkbox" class="flat-red" name="hari[]" value="senin"> Senin                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input type="checkbox" class="flat-red" name="hari[]" value="selasa"> Selasa                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input type="checkbox" class="flat-red" name="hari[]" value="rabu"> Rabu                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input type="checkbox" class="flat-red" name="hari[]" value="kamis"> Kamis                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input type="checkbox" class="flat-red" name="hari[]" value="jumat"> Jumat                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input type="checkbox" class="flat-red" name="hari[]" value="sabtu"> Sabtu                                    </label>
                                    </div>
                                    <div class="col-md-3  padding-left-0">
                                    <label>
                                    <input type="checkbox" class="flat-red" name="hari[]" value="minggu"> Minggu                                    </label>
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
                              <input type="text" class="form-control pull-right timepicker" name="jam" id="jam">
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
                              <input type="text" class="form-control pull-right datetimepicker" name="tanggal_posting"  id="tanggal_posting">
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
                                <input type="text" class="form-control" name="nominal_biaya" id="nominal_biaya" placeholder="Nominal Biaya" value="<?= set_value('nominal_biaya'); ?>">
                                <small class="info help-block">
                                <b>Input Nominal Biaya</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="link_daftar" class="col-sm-2 control-label">Link Pendaftaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="link_daftar" name="link_daftar" rows="5" cols="80"><?= set_value('Link Daftar'); ?></textarea>
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
                                    <option value="sd">SD</option>
                                    <option value="smp">SMP</option>
                                    <option value="sma">SMA</option>
                                    <option value="ft">France Track</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Jenjang</b> Max Length : 20.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="tahun_ajaran_aktif" class="col-sm-2 control-label">Tahun Ajaran Aktif
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tahun_ajaran_aktif" id="tahun_ajaran_aktif" placeholder="Tahun Ajaran Aktif" >
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
                                    <option value="1">Ganjil</option>
                                    <option value="2">Genap</option>
                                </select>
                                <small class="info help-block"><b>Kosongkan jika ekskul tidak diadakan.</b> </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="bank" class="col-sm-2 control-label">Bank
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bank" id="bank" placeholder="Bank" >
                                <small class="info help-block">
                                <b>Input Bank</b> Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="rekening" class="col-sm-2 control-label">Rekening
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="rekening" id="rekening" placeholder="Rekening" >
                                <small class="info help-block">
                                <b>Input Rekening</b> Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="atas_nama_rekening" class="col-sm-2 control-label">Atas Nama Rekening
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="atas_nama_rekening" id="atas_nama_rekening" placeholder="Atas Nama Rekening" >
                                <small class="info help-block">
                                <b>Input Atas Nama Rekening</b> Max Length : 100.</small>
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
          url: BASE_URL + '/administrator/ekskul/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('.steps li').removeClass('error');
          $('form').find('.error-input').remove();
          if(res.success) {
            var id_foto = $('#ekskul_foto_galery').find('li').attr('qq-file-id');
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            if (typeof id_foto !== 'undefined') {
                    $('#ekskul_foto_galery').fineUploader('deleteFile', id_foto);
                }
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
            konten.setData('');
            link_daftar.setData('');
                
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

       $('#ekskul_foto_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/ekskul/upload_foto_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/ekskul/delete_foto_file',
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
      }); /*end foto galery*/
              
 
       

      
    
    
    }); /*end doc ready*/
</script>