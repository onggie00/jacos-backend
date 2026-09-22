

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
        Mailbox Mail Signed        <small>Edit Mailbox Mail Signed</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/mailbox_mail_signed'); ?>">Mailbox Mail Signed</a></li>
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
                            <h3 class="widget-user-username">Mailbox Mail Signed</h3>
                            <h5 class="widget-user-desc">Edit Mailbox Mail Signed</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/mailbox_mail_signed/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_mailbox_mail_signed', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_mailbox_mail_signed', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_mail" class="col-sm-2 control-label">Mail 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_mail" id="id_mail" data-placeholder="Select Mail" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('mailbox_mail') as $row): ?>
                                    <option <?=  $row->id_mail ==  $mailbox_mail_signed->id_mail ? 'selected' : ''; ?> value="<?= $row->id_mail ?>"><?= $row->mailbox_title; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Mail</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="sign_name" class="col-sm-2 control-label">Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="sign_name" id="sign_name" placeholder="Name" value="<?= set_value('sign_name', $mailbox_mail_signed->sign_name); ?>">
                                <small class="info help-block">
                                <b>Input Sign Name</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="sign_position" class="col-sm-2 control-label">Position 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="sign_position" name="sign_position" rows="5" class="textarea form-control"><?= set_value('sign_position', $mailbox_mail_signed->sign_position); ?></textarea>
                                <small class="info help-block">
                                <b>Input Sign Position</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="sign_signature" class="col-sm-2 control-label">Signature File 
                            </label>
                            <div class="col-sm-8">
                                <div id="mailbox_mail_signed_sign_signature_galery"></div>
                                <input class="data_file data_file_uuid" name="mailbox_mail_signed_sign_signature_uuid" id="mailbox_mail_signed_sign_signature_uuid" type="hidden" value="<?= set_value('mailbox_mail_signed_sign_signature_uuid'); ?>">
                                <input class="data_file" name="mailbox_mail_signed_sign_signature_name" id="mailbox_mail_signed_sign_signature_name" type="hidden" value="<?= set_value('mailbox_mail_signed_sign_signature_name', $mailbox_mail_signed->sign_signature); ?>">
                                <small class="info help-block">
                                <b>Input Sign Signature</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="sign_order" class="col-sm-2 control-label">Order 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="sign_order" id="sign_order" placeholder="Order" value="<?= set_value('sign_order', $mailbox_mail_signed->sign_order); ?>">
                                <small class="info help-block">
                                <b>Input Sign Order</b> Max Length : 2.</small>
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
              window.location.href = BASE_URL + 'administrator/mailbox_mail_signed';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_mailbox_mail_signed = $('#form_mailbox_mail_signed');
        var data_post = form_mailbox_mail_signed.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_mailbox_mail_signed.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#mailbox_mail_signed_image_galery').find('li').attr('qq-file-id');
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

       $('#mailbox_mail_signed_sign_signature_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/mailbox_mail_signed/upload_sign_signature_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/mailbox_mail_signed/delete_sign_signature_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/mailbox_mail_signed/get_sign_signature_file/<?= $mailbox_mail_signed->id_mail_signed; ?>',
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
                   var uuid = $('#mailbox_mail_signed_sign_signature_galery').fineUploader('getUuid', id);
                   $('#mailbox_mail_signed_sign_signature_uuid').val(uuid);
                   $('#mailbox_mail_signed_sign_signature_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#mailbox_mail_signed_sign_signature_uuid').val();
                  $.get(BASE_URL + '/administrator/mailbox_mail_signed/delete_sign_signature_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#mailbox_mail_signed_sign_signature_uuid').val('');
                  $('#mailbox_mail_signed_sign_signature_name').val('');
                }
              }
          }
      }); /*end sign_signature galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>