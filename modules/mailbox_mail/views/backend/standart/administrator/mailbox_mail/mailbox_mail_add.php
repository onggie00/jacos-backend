
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
        Mailbox Mail        <small><?= cclang('new', ['Mailbox Mail']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/mailbox_mail'); ?>">Mailbox Mail</a></li>
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
                            <h3 class="widget-user-username">Mailbox Mail</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Mailbox Mail']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_mailbox_mail', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_mailbox_mail', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                        
                        <div class="form-group ">
                            <label for="mailbox_category" class="col-sm-2 control-label">Category 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="mailbox_category" id="mailbox_category" data-placeholder="Select Category" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('mailbox_category') as $row): ?>
                                    <option value="<?= $row->id_category ?>"><?= $row->category; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Mailbox Category</b> Max Length : 11.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="mail_recipient" class="col-sm-2 control-label">Filter Recipient
                            </label>
                            <div class="col-sm-1 col-sm-offset-1">
                                <label class="radio">
                                    <input type="radio" class="form-check" name="filter_recipient" id="filter_recipient" value="all">All</input>
                                </label>
                            </div>
                            <div class="col-sm-1">
                                <label class="radio">
                                    <input type="radio" class="form-check" name="filter_recipient" id="filter_recipient" value="sd">SD</input>
                                </label>
                            </div>
                            <div class="col-sm-1">
                                <label class="radio">
                                    <input type="radio" class="form-check" name="filter_recipient" id="filter_recipient" value="smp">SMP</input>
                                </label>
                            </div>
                            <div class="col-sm-1">
                                <label class="radio">
                                    <input type="radio" class="form-check" name="filter_recipient" id="filter_recipient" value="sma">SMA</input>
                                </label>
                            </div>
                            <div class="col-sm-1">
                                <label class="radio">
                                    <input type="radio" class="form-check" name="filter_recipient" id="filter_recipient" value="pegawai">Karyawan</input>
                                </label>
                            </div>
                            <div class="col-sm-1">
                                <label class="radio">
                                    <input type="radio" class="form-check" name="filter_recipient" id="filter_recipient" value="pimpinan">Pimpinan</input>
                                </label>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="list_recipient" class="col-sm-2 control-label">List Recipient 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select-deselect" name="list_recipient[]" id="list_recipient" data-placeholder="Select Recipient" multiple>
                                    <option value=""></option>
                                    <option value="all">All</option>
                                </select>
                                <small class="info help-block">
                                <b>Select Recipient</b> Multiple choice available</small>
                            </div>
                        </div>
                        
                        <div class="form-group ">
                            <label for="mailbox_title" class="col-sm-2 control-label">Title 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="mailbox_title" id="mailbox_title" placeholder="Title" value="<?= set_value('mailbox_title'); ?>">
                                <small class="info help-block">
                                <b>Kosongkan jika tanpa judul</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="mailbox_description" class="col-sm-2 control-label">Description 
                            </label>
                            <div class="col-sm-8">
                                <textarea id="mailbox_description" name="mailbox_description" rows="5" cols="80"><?= set_value('Mailbox Description'); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group  wrapper-options-crud">
                            <label for="is_confidential" class="col-sm-2 control-label">Is Confidential? 
                            </label>
                            <div class="col-sm-8">
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input type="radio" class="flat-red" name="is_confidential" value="0"> Tidak                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input type="radio" class="flat-red" name="is_confidential" value="1"> Ya                                    </label>
                                    </div>
                                    </select>
                                <div class="row-fluid clear-both">
                                <small class="info help-block">
                                <b>Input Is Confidential</b> Max Length : 1.</small>
                                </div>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="mail_number" class="col-sm-2 control-label">Mail Number 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="mail_number" id="mail_number" placeholder="Mail Number" value="<?= set_value('mail_number'); ?>">
                                <small class="info help-block">
                                <b>Input Mail Number</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="mail_content" class="col-sm-2 control-label">Mail Content 
                            </label>
                            <div class="col-sm-8">
                                <textarea id="mail_content" name="mail_content" rows="5" cols="80"><?= set_value('Mail Content'); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="mail_address" class="col-sm-2 control-label">Mail Address 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="mail_address" id="mail_address" placeholder="Mail Address" value="<?= set_value('mail_address'); ?>">
                                <small class="info help-block">
                                <b>Input Mail Address</b> Max Length : 130.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="mail_date" class="col-sm-2 control-label">Mail Date 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="mail_date"  placeholder="Mail Date" id="mail_date">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="mail_time" class="col-sm-2 control-label">Mail Time 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="mail_time" id="mail_time">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="mail_header_file" class="col-sm-2 control-label">Mail Header (KOP) 
                            </label>
                            <div class="col-sm-8">
                                <div id="mailbox_mail_mail_header_file_galery"></div>
                                <input class="data_file" name="mailbox_mail_mail_header_file_uuid" id="mailbox_mail_mail_header_file_uuid" type="hidden" value="<?= set_value('mailbox_mail_mail_header_file_uuid'); ?>">
                                <input class="data_file" name="mailbox_mail_mail_header_file_name" id="mailbox_mail_mail_header_file_name" type="hidden" value="<?= set_value('mailbox_mail_mail_header_file_name'); ?>">
                                <small class="info help-block">
                                <b>Input Mail Header File</b> Max Length : 255.</small>
                            </div>
                        </div>
                        
                        <div class="form-group ">
                            <label for="mail_show_date" class="col-sm-2 control-label">Show Date 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="mail_show_date"  placeholder="Mail Date" id="mail_show_date">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                        <div class="form-group ">
                            <label for="mail_show_time" class="col-sm-2 control-label">Show Time 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="mail_show_time" id="mail_show_time">
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

                   CKEDITOR.replace('mailbox_description'); 
      var mailbox_description = CKEDITOR.instances.mailbox_description;
            CKEDITOR.replace('mail_content'); 
      var mail_content = CKEDITOR.instances.mail_content;
                   
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
              window.location.href = BASE_URL + 'administrator/mailbox_mail';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#mailbox_description').val(mailbox_description.getData());
                $('#mail_content').val(mail_content.getData());
                    
        var form_mailbox_mail = $('#form_mailbox_mail');
        var data_post = form_mailbox_mail.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/mailbox_mail/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('.steps li').removeClass('error');
          $('form').find('.error-input').remove();
          if(res.success) {
            var id_mail_header_file = $('#mailbox_mail_mail_header_file_galery').find('li').attr('qq-file-id');
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            if (typeof id_mail_header_file !== 'undefined') {
                    $('#mailbox_mail_mail_header_file_galery').fineUploader('deleteFile', id_mail_header_file);
                }
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
            mailbox_description.setData('');
            mail_content.setData('');
                
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

       $('#mailbox_mail_mail_header_file_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/mailbox_mail/upload_mail_header_file_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/mailbox_mail/delete_mail_header_file_file',
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
                   var uuid = $('#mailbox_mail_mail_header_file_galery').fineUploader('getUuid', id);
                   $('#mailbox_mail_mail_header_file_uuid').val(uuid);
                   $('#mailbox_mail_mail_header_file_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#mailbox_mail_mail_header_file_uuid').val();
                  $.get(BASE_URL + '/administrator/mailbox_mail/delete_mail_header_file_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#mailbox_mail_mail_header_file_uuid').val('');
                  $('#mailbox_mail_mail_header_file_name').val('');
                }
              }
          }
      }); /*end mail_header_file galery*/

    $(`input[name="filter_recipient"][value="all"]`).prop('checked', true);
    //function filter recipient
    $(document).on('change', 'input[name="filter_recipient"]', function () {
    let type = $(this).val();

        $.ajax({
            url: "<?= base_url('administrator/mailbox_mail/get_user'); ?>",
            type: "POST",
            data: {
                filter: type
            },
            dataType: "json",
            beforeSend: function () {
                $('#list_recipient').html('<option>Loading...</option>');
            },
            success: function (res) {
                let option = '<option value="all">All</option>';
                $.each(res, function (i, v) {
                    option += `<option value="${v}">${v}</option>`;
                });

                $('#list_recipient').html(option);
                $('#list_recipient').val('').trigger('chosen:updated');
            },
            error: function () {
                $('#list_recipient').html('<option>Gagal memuat data</option>');
            }
        });
    });
    $(`input[name="filter_recipient"][value="all"]`).trigger('change');
    
}); /*end doc ready*/
</script>