

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
        Pengaturan Popup Banner        <small>Edit Pengaturan Popup Banner</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/apps_banner'); ?>">Pengaturan Popup Banner</a></li>
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
                            <h3 class="widget-user-username">Pengaturan Popup Banner</h3>
                            <h5 class="widget-user-desc">Edit Pengaturan Popup Banner</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/apps_banner/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_apps_banner', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_apps_banner', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="img_file" class="col-sm-2 control-label">File 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="apps_banner_img_file_galery"></div>
                                <input class="data_file data_file_uuid" name="apps_banner_img_file_uuid" id="apps_banner_img_file_uuid" type="hidden" value="<?= set_value('apps_banner_img_file_uuid'); ?>">
                                <input class="data_file" name="apps_banner_img_file_name" id="apps_banner_img_file_name" type="hidden" value="<?= set_value('apps_banner_img_file_name', $apps_banner->img_file); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="title" class="col-sm-2 control-label">Title 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="<?= set_value('title', $apps_banner->title); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="description" class="col-sm-2 control-label">Description 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="description" id="description" placeholder="Description" value="<?= set_value('description', $apps_banner->description); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="jenjang[]" id="jenjang" data-placeholder="Select Jenjang" multiple >
                                    <option value=""></option>
                                    <option <?= in_array('sd', explode(',', $apps_banner->jenjang)) ? 'selected' : ''; ?>  value="sd">SD</option>
                                    <option <?= in_array('smp', explode(',', $apps_banner->jenjang)) ? 'selected' : ''; ?>  value="smp">SMP</option>
                                    <option <?= in_array('sma', explode(',', $apps_banner->jenjang)) ? 'selected' : ''; ?>  value="sma">SMA</option>
                                    <option <?= in_array('ft', explode(',', $apps_banner->jenjang)) ? 'selected' : ''; ?>  value="ft">FT</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="role" class="col-sm-2 control-label">Role 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="role[]" id="role" data-placeholder="Select Role" multiple >
                                    <option value=""></option>
                                    <option <?= in_array('siswa', explode(',', $apps_banner->role)) ? 'selected' : ''; ?>  value="siswa">Siswa</option>
                                    <option <?= in_array('ortu', explode(',', $apps_banner->role)) ? 'selected' : ''; ?>  value="ortu">Orang Tua</option>
                                    <option <?= in_array('guru', explode(',', $apps_banner->role)) ? 'selected' : ''; ?>  value="guru">Guru</option>
                                    <option <?= in_array('pegawai', explode(',', $apps_banner->role)) ? 'selected' : ''; ?>  value="pegawai">Pegawai</option>
                                    <option <?= in_array('pimpinan', explode(',', $apps_banner->role)) ? 'selected' : ''; ?>  value="pimpinan">Pimpinan</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="no_urut" class="col-sm-2 control-label">Urutan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="no_urut" id="no_urut" placeholder="Urutan" value="<?= set_value('no_urut', $apps_banner->no_urut); ?>">
                                <small class="info help-block">
                                <b>Input No Urut</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="is_default" class="col-sm-2 control-label">Default? 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="is_default" id="is_default" data-placeholder="Select Default?" >
                                    <option value=""></option>
                                    <option <?= $apps_banner->is_default == "0" ? 'selected' :''; ?> value="0">Not Default</option>
                                    <option <?= $apps_banner->is_default == "1" ? 'selected' :''; ?> value="1">Default</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="is_active" class="col-sm-2 control-label">Active? 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="is_active" id="is_active" data-placeholder="Select Active?" >
                                    <option value=""></option>
                                    <option <?= $apps_banner->is_active == "0" ? 'selected' :''; ?> value="0">Inactive</option>
                                    <option <?= $apps_banner->is_active == "1" ? 'selected' :''; ?> value="1">Active</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tanggal_aktif" class="col-sm-2 control-label">Tanggal Aktif 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="tanggal_aktif"  placeholder="Tanggal Aktif" id="tanggal_aktif" value="<?= set_value('apps_banner_tanggal_aktif_name', $apps_banner->tanggal_aktif); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                       
                                                 
                                                <div class="form-group ">
                            <label for="tanggal_selesai" class="col-sm-2 control-label">Tanggal Selesai 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="tanggal_selesai"  placeholder="Tanggal Selesai" id="tanggal_selesai" value="<?= set_value('apps_banner_tanggal_selesai_name', $apps_banner->tanggal_selesai); ?>">
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
              window.location.href = BASE_URL + 'administrator/apps_banner';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_apps_banner = $('#form_apps_banner');
        var data_post = form_apps_banner.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_apps_banner.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#apps_banner_image_galery').find('li').attr('qq-file-id');
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

       $('#apps_banner_img_file_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/apps_banner/upload_img_file_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/apps_banner/delete_img_file_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/apps_banner/get_img_file_file/<?= $apps_banner->id_banner; ?>',
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
                   var uuid = $('#apps_banner_img_file_galery').fineUploader('getUuid', id);
                   $('#apps_banner_img_file_uuid').val(uuid);
                   $('#apps_banner_img_file_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#apps_banner_img_file_uuid').val();
                  $.get(BASE_URL + '/administrator/apps_banner/delete_img_file_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#apps_banner_img_file_uuid').val('');
                  $('#apps_banner_img_file_name').val('');
                }
              }
          }
      }); /*end img_file galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>