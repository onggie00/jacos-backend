

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
        Presensi Office        <small>Edit Presensi Office</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/presensi_office'); ?>">Presensi Office</a></li>
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
                            <h3 class="widget-user-username">Presensi Office</h3>
                            <h5 class="widget-user-desc">Edit Presensi Office</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/presensi_office/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_presensi_office', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_presensi_office', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="npp" class="col-sm-2 control-label">NPP 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npp" id="npp" placeholder="NPP" value="<?= set_value('npp', $presensi_office->npp); ?>">
                                <small class="info help-block">
                                <b>Input Npp</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nama_lengkap" class="col-sm-2 control-label">Nama Lengkap 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" value="<?= set_value('nama_lengkap', $presensi_office->nama_lengkap); ?>">
                                <small class="info help-block">
                                <b>Input Nama Lengkap</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="presensi_date" class="col-sm-2 control-label">Date 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="presensi_date"  placeholder="Date" id="presensi_date" value="<?= set_value('presensi_office_presensi_date_name', $presensi_office->presensi_date); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                       
                                                 
                                                <div class="form-group ">
                            <label for="presensi_hari" class="col-sm-2 control-label">Hari 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="presensi_hari" id="presensi_hari" placeholder="Hari" value="<?= set_value('presensi_hari', $presensi_office->presensi_hari); ?>">
                                <small class="info help-block">
                                <b>Input Presensi Hari</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="check_in" class="col-sm-2 control-label">Check In 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="check_in" id="check_in" value="<?= set_value('presensi_office_check_in_name', $presensi_office->check_in); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="check_out" class="col-sm-2 control-label">Check Out 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="check_out" id="check_out" value="<?= set_value('presensi_office_check_out_name', $presensi_office->check_out); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="role" class="col-sm-2 control-label">Role 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="role" id="role" placeholder="Role" value="<?= set_value('role', $presensi_office->role); ?>">
                                <small class="info help-block">
                                <b>Input Role</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="presensi_device" class="col-sm-2 control-label">Device 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="presensi_device" id="presensi_device" placeholder="Device" value="<?= set_value('presensi_device', $presensi_office->presensi_device); ?>">
                                <small class="info help-block">
                                <b>Input Presensi Device</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="status_presensi" class="col-sm-2 control-label">Status 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="status_presensi" id="status_presensi" placeholder="Status" value="<?= set_value('status_presensi', $presensi_office->status_presensi); ?>">
                                <small class="info help-block">
                                <b>Input Status Presensi</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="status_presensi_selesai" class="col-sm-2 control-label">Status Presensi Pulang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="status_presensi_selesai" id="status_presensi_selesai" placeholder="Status Presensi Pulang" value="<?= set_value('status_presensi_selesai', $presensi_office->status_presensi_selesai); ?>">
                                <small class="info help-block">
                                <b>Input Status Presensi Selesai</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="keterangan" class="col-sm-2 control-label">Keterangan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan" value="<?= set_value('keterangan', $presensi_office->keterangan); ?>">
                                <small class="info help-block">
                                <b>Input Keterangan</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="file_report" class="col-sm-2 control-label">File Report 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="presensi_office_file_report_galery"></div>
                                <input class="data_file data_file_uuid" name="presensi_office_file_report_uuid" id="presensi_office_file_report_uuid" type="hidden" value="<?= set_value('presensi_office_file_report_uuid'); ?>">
                                <input class="data_file" name="presensi_office_file_report_name" id="presensi_office_file_report_name" type="hidden" value="<?= set_value('presensi_office_file_report_name', $presensi_office->file_report); ?>">
                                <small class="info help-block">
                                <b>Input File Report</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="report_status" class="col-sm-2 control-label">Report Status 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="report_status" id="report_status" placeholder="Report Status" value="<?= set_value('report_status', $presensi_office->report_status); ?>">
                                <small class="info help-block">
                                <b>Input Report Status</b> Max Length : 1.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_izin" class="col-sm-2 control-label">Izin 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="id_izin" id="id_izin" placeholder="Izin" value="<?= set_value('id_izin', $presensi_office->id_izin); ?>">
                                <small class="info help-block">
                                <b>Input Id Izin</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="created_at" class="col-sm-2 control-label">Created At 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="created_at"  placeholder="Created At" id="created_at" value="<?= set_value('created_at', $presensi_office->created_at); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="updated_at" class="col-sm-2 control-label">Updated At 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="updated_at"  placeholder="Updated At" id="updated_at" value="<?= set_value('updated_at', $presensi_office->updated_at); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="updated_by" class="col-sm-2 control-label">Updated By 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="updated_by" id="updated_by" placeholder="Updated By" value="<?= set_value('updated_by', $presensi_office->updated_by); ?>">
                                <small class="info help-block">
                                <b>Input Updated By</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="deleted_at" class="col-sm-2 control-label">Deleted At 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="deleted_at"  placeholder="Deleted At" id="deleted_at" value="<?= set_value('deleted_at', $presensi_office->deleted_at); ?>">
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
              window.location.href = BASE_URL + 'administrator/presensi_office';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_presensi_office = $('#form_presensi_office');
        var data_post = form_presensi_office.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_presensi_office.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#presensi_office_image_galery').find('li').attr('qq-file-id');
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

       $('#presensi_office_file_report_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/presensi_office/upload_file_report_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/presensi_office/delete_file_report_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/presensi_office/get_file_report_file/<?= $presensi_office->id_presensi; ?>',
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
                   var uuid = $('#presensi_office_file_report_galery').fineUploader('getUuid', id);
                   $('#presensi_office_file_report_uuid').val(uuid);
                   $('#presensi_office_file_report_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#presensi_office_file_report_uuid').val();
                  $.get(BASE_URL + '/administrator/presensi_office/delete_file_report_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#presensi_office_file_report_uuid').val('');
                  $('#presensi_office_file_report_name').val('');
                }
              }
          }
      }); /*end file_report galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>