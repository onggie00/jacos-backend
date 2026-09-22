
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
        Prestasi Siswa SMP        <small><?= cclang('new', ['Prestasi Siswa SMP']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/prestasi_siswa_smp'); ?>">Prestasi Siswa SMP</a></li>
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
                            <h3 class="widget-user-username">Prestasi Siswa SMP</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Prestasi Siswa SMP']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_prestasi_siswa_smp', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_prestasi_siswa_smp', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nama_prestasi" class="col-sm-2 control-label">Nama Prestasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_prestasi" id="nama_prestasi" placeholder="Nama Prestasi" value="<?= set_value('nama_prestasi'); ?>">
                                <small class="info help-block">
                                <b>Input Nama Prestasi</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tgl_raih" class="col-sm-2 control-label">Tanggal Raih 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="tgl_raih"  placeholder="Tanggal Raih" id="tgl_raih">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="juara" class="col-sm-2 control-label">Juara 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="juara" id="juara" placeholder="Juara" value="<?= set_value('juara'); ?>">
                                <small class="info help-block">
                                <b>Input Juara</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="file_prestasi" class="col-sm-2 control-label">File Prestasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="prestasi_siswa_smp_file_prestasi_galery"></div>
                                <input class="data_file" name="prestasi_siswa_smp_file_prestasi_uuid" id="prestasi_siswa_smp_file_prestasi_uuid" type="hidden" value="<?= set_value('prestasi_siswa_smp_file_prestasi_uuid'); ?>">
                                <input class="data_file" name="prestasi_siswa_smp_file_prestasi_name" id="prestasi_siswa_smp_file_prestasi_name" type="hidden" value="<?= set_value('prestasi_siswa_smp_file_prestasi_name'); ?>">
                                <small class="info help-block">
                                <b>Input File Prestasi</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="foto_prestasi" class="col-sm-2 control-label">Foto 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="prestasi_siswa_smp_foto_prestasi_galery"></div>
                                <input class="data_file" name="prestasi_siswa_smp_foto_prestasi_uuid" id="prestasi_siswa_smp_foto_prestasi_uuid" type="hidden" value="<?= set_value('prestasi_siswa_smp_foto_prestasi_uuid'); ?>">
                                <input class="data_file" name="prestasi_siswa_smp_foto_prestasi_name" id="prestasi_siswa_smp_foto_prestasi_name" type="hidden" value="<?= set_value('prestasi_siswa_smp_foto_prestasi_name'); ?>">
                                <small class="info help-block">
                                <b>Input Foto Prestasi</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_siswa" class="col-sm-2 control-label">Siswa 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_siswa" id="id_siswa" data-placeholder="Select Siswa" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_smp_aktif') as $row): ?>
                                    <option value="<?= $row->id_siswa_smp_aktif ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Siswa</b> Max Length : 11.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="jenis_prestasi_id" class="col-sm-2 control-label">Tingkatan Prestasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="jenis_prestasi_id" id="jenis_prestasi_id" data-placeholder="Select Tingkatan Prestasi" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('jenis_prestasi') as $row): ?>
                                    <option value="<?= $row->id_jenis_prestasi ?>"><?= $row->jenis_prestasi; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Jenis Prestasi Id</b> Max Length : 11.</small>
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
                                                 
                                                <div class="form-group ">
                            <label for="tanggal_posting" class="col-sm-2 control-label">Tanggal Posting 
                            <i class="required">*</i>
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
                            <label for="is_approved" class="col-sm-2 control-label">Disetujui? 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="is_approved" id="is_approved" data-placeholder="Select Disetujui?" >
                                    <option value=""></option>
                                    <option value="0">Belum Disetujui</option>
                                    <option value="1">Disetujui</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Is Approved</b> Max Length : 6.</small>
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
              window.location.href = BASE_URL + 'administrator/prestasi_siswa_smp';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#konten').val(konten.getData());
                    
        var form_prestasi_siswa_smp = $('#form_prestasi_siswa_smp');
        var data_post = form_prestasi_siswa_smp.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/prestasi_siswa_smp/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('.steps li').removeClass('error');
          $('form').find('.error-input').remove();
          if(res.success) {
            var id_file_prestasi = $('#prestasi_siswa_smp_file_prestasi_galery').find('li').attr('qq-file-id');
            var id_foto_prestasi = $('#prestasi_siswa_smp_foto_prestasi_galery').find('li').attr('qq-file-id');
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            if (typeof id_file_prestasi !== 'undefined') {
                    $('#prestasi_siswa_smp_file_prestasi_galery').fineUploader('deleteFile', id_file_prestasi);
                }
            if (typeof id_foto_prestasi !== 'undefined') {
                    $('#prestasi_siswa_smp_foto_prestasi_galery').fineUploader('deleteFile', id_foto_prestasi);
                }
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
            konten.setData('');
                
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

       $('#prestasi_siswa_smp_file_prestasi_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/prestasi_siswa_smp/upload_file_prestasi_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/prestasi_siswa_smp/delete_file_prestasi_file',
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
                   var uuid = $('#prestasi_siswa_smp_file_prestasi_galery').fineUploader('getUuid', id);
                   $('#prestasi_siswa_smp_file_prestasi_uuid').val(uuid);
                   $('#prestasi_siswa_smp_file_prestasi_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#prestasi_siswa_smp_file_prestasi_uuid').val();
                  $.get(BASE_URL + '/administrator/prestasi_siswa_smp/delete_file_prestasi_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#prestasi_siswa_smp_file_prestasi_uuid').val('');
                  $('#prestasi_siswa_smp_file_prestasi_name').val('');
                }
              }
          }
      }); /*end file_prestasi galery*/
                     var params = {};
       params[csrf] = token;

       $('#prestasi_siswa_smp_foto_prestasi_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/prestasi_siswa_smp/upload_foto_prestasi_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/prestasi_siswa_smp/delete_foto_prestasi_file',
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
                   var uuid = $('#prestasi_siswa_smp_foto_prestasi_galery').fineUploader('getUuid', id);
                   $('#prestasi_siswa_smp_foto_prestasi_uuid').val(uuid);
                   $('#prestasi_siswa_smp_foto_prestasi_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#prestasi_siswa_smp_foto_prestasi_uuid').val();
                  $.get(BASE_URL + '/administrator/prestasi_siswa_smp/delete_foto_prestasi_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#prestasi_siswa_smp_foto_prestasi_uuid').val('');
                  $('#prestasi_siswa_smp_foto_prestasi_name').val('');
                }
              }
          }
      }); /*end foto_prestasi galery*/
              
 
       

      
    
    
    }); /*end doc ready*/
</script>