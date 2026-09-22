

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
        Data Kepala Sekolah        <small>Edit Data Kepala Sekolah</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/data_kepala_sekolah'); ?>">Data Kepala Sekolah</a></li>
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
                            <h3 class="widget-user-username">Data Kepala Sekolah</h3>
                            <h5 class="widget-user-desc">Edit Data Kepala Sekolah</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/data_kepala_sekolah/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_data_kepala_sekolah', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_data_kepala_sekolah', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nama_kepsek" class="col-sm-2 control-label">Nama Kepsek 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_kepsek" id="nama_kepsek" placeholder="Nama Kepsek" value="<?= set_value('nama_kepsek', $data_kepala_sekolah->nama_kepsek); ?>">
                                <small class="info help-block">
                                <b>Input Nama Kepsek</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="file_ttd" class="col-sm-2 control-label">File Ttd 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="data_kepala_sekolah_file_ttd_galery"></div>
                                <input class="data_file data_file_uuid" name="data_kepala_sekolah_file_ttd_uuid" id="data_kepala_sekolah_file_ttd_uuid" type="hidden" value="<?= set_value('data_kepala_sekolah_file_ttd_uuid'); ?>">
                                <input class="data_file" name="data_kepala_sekolah_file_ttd_name" id="data_kepala_sekolah_file_ttd_name" type="hidden" value="<?= set_value('data_kepala_sekolah_file_ttd_name', $data_kepala_sekolah->file_ttd); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="nrks" class="col-sm-2 control-label">Nrks 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nrks" id="nrks" placeholder="Nrks" value="<?= set_value('nrks', $data_kepala_sekolah->nrks); ?>">
                                <small class="info help-block">
                                <b>Input Nrks</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="jenjang" id="jenjang" data-placeholder="Select Jenjang" >
                                    <option value=""></option>
                                    <option <?= $data_kepala_sekolah->jenjang == "sd" ? 'selected' :''; ?> value="sd">SD</option>
                                    <option <?= $data_kepala_sekolah->jenjang == "smp" ? 'selected' :''; ?> value="smp">SMP</option>
                                    <option <?= $data_kepala_sekolah->jenjang == "sma" ? 'selected' :''; ?> value="sma">SMA</option>
                                    <option <?= $data_kepala_sekolah->jenjang == "ft" ? 'selected' :''; ?> value="ft">FT</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Jenjang</b> Max Length : 20.</small>
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
              window.location.href = BASE_URL + 'administrator/data_kepala_sekolah';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_data_kepala_sekolah = $('#form_data_kepala_sekolah');
        var data_post = form_data_kepala_sekolah.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_data_kepala_sekolah.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#data_kepala_sekolah_image_galery').find('li').attr('qq-file-id');
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

       $('#data_kepala_sekolah_file_ttd_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/data_kepala_sekolah/upload_file_ttd_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/data_kepala_sekolah/delete_file_ttd_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/data_kepala_sekolah/get_file_ttd_file/<?= $data_kepala_sekolah->id; ?>',
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
                   var uuid = $('#data_kepala_sekolah_file_ttd_galery').fineUploader('getUuid', id);
                   $('#data_kepala_sekolah_file_ttd_uuid').val(uuid);
                   $('#data_kepala_sekolah_file_ttd_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#data_kepala_sekolah_file_ttd_uuid').val();
                  $.get(BASE_URL + '/administrator/data_kepala_sekolah/delete_file_ttd_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#data_kepala_sekolah_file_ttd_uuid').val('');
                  $('#data_kepala_sekolah_file_ttd_name').val('');
                }
              }
          }
      }); /*end file_ttd galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>