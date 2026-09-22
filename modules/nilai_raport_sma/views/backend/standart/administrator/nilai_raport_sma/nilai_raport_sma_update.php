

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
        Nilai Raport Sma        <small>Edit Nilai Raport Sma</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/nilai_raport_sma'); ?>">Nilai Raport Sma</a></li>
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
                            <h3 class="widget-user-username">Nilai Raport Sma</h3>
                            <h5 class="widget-user-desc">Edit Nilai Raport Sma</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/nilai_raport_sma/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_nilai_raport_sma', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_nilai_raport_sma', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="b_inggris" class="col-sm-2 control-label">B Inggris 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="b_inggris" id="b_inggris" placeholder="B Inggris" value="<?= set_value('b_inggris', $nilai_raport_sma->b_inggris); ?>">
                                <small class="info help-block">
                                <b>Input B Inggris</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="b_inggris2" class="col-sm-2 control-label">B Inggris2 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="b_inggris2" id="b_inggris2" placeholder="B Inggris2" value="<?= set_value('b_inggris2', $nilai_raport_sma->b_inggris2); ?>">
                                <small class="info help-block">
                                <b>Input B Inggris2</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="b_inggris3" class="col-sm-2 control-label">B Inggris3 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="b_inggris3" id="b_inggris3" placeholder="B Inggris3" value="<?= set_value('b_inggris3', $nilai_raport_sma->b_inggris3); ?>">
                                <small class="info help-block">
                                <b>Input B Inggris3</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="b_inggris4" class="col-sm-2 control-label">B Inggris4 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="b_inggris4" id="b_inggris4" placeholder="B Inggris4" value="<?= set_value('b_inggris4', $nilai_raport_sma->b_inggris4); ?>">
                                <small class="info help-block">
                                <b>Input B Inggris4</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="b_indonesia" class="col-sm-2 control-label">B Indonesia 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="b_indonesia" id="b_indonesia" placeholder="B Indonesia" value="<?= set_value('b_indonesia', $nilai_raport_sma->b_indonesia); ?>">
                                <small class="info help-block">
                                <b>Input B Indonesia</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="b_indonesia2" class="col-sm-2 control-label">B Indonesia2 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="b_indonesia2" id="b_indonesia2" placeholder="B Indonesia2" value="<?= set_value('b_indonesia2', $nilai_raport_sma->b_indonesia2); ?>">
                                <small class="info help-block">
                                <b>Input B Indonesia2</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="b_indonesia3" class="col-sm-2 control-label">B Indonesia3 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="b_indonesia3" id="b_indonesia3" placeholder="B Indonesia3" value="<?= set_value('b_indonesia3', $nilai_raport_sma->b_indonesia3); ?>">
                                <small class="info help-block">
                                <b>Input B Indonesia3</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="b_indonesia4" class="col-sm-2 control-label">B Indonesia4 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="b_indonesia4" id="b_indonesia4" placeholder="B Indonesia4" value="<?= set_value('b_indonesia4', $nilai_raport_sma->b_indonesia4); ?>">
                                <small class="info help-block">
                                <b>Input B Indonesia4</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="ipa" class="col-sm-2 control-label">Ipa 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="ipa" id="ipa" placeholder="Ipa" value="<?= set_value('ipa', $nilai_raport_sma->ipa); ?>">
                                <small class="info help-block">
                                <b>Input Ipa</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="ipa2" class="col-sm-2 control-label">Ipa2 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="ipa2" id="ipa2" placeholder="Ipa2" value="<?= set_value('ipa2', $nilai_raport_sma->ipa2); ?>">
                                <small class="info help-block">
                                <b>Input Ipa2</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="ipa3" class="col-sm-2 control-label">Ipa3 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="ipa3" id="ipa3" placeholder="Ipa3" value="<?= set_value('ipa3', $nilai_raport_sma->ipa3); ?>">
                                <small class="info help-block">
                                <b>Input Ipa3</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="ipa4" class="col-sm-2 control-label">Ipa4 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="ipa4" id="ipa4" placeholder="Ipa4" value="<?= set_value('ipa4', $nilai_raport_sma->ipa4); ?>">
                                <small class="info help-block">
                                <b>Input Ipa4</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="ips" class="col-sm-2 control-label">Ips 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="ips" id="ips" placeholder="Ips" value="<?= set_value('ips', $nilai_raport_sma->ips); ?>">
                                <small class="info help-block">
                                <b>Input Ips</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="ips2" class="col-sm-2 control-label">Ips2 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="ips2" id="ips2" placeholder="Ips2" value="<?= set_value('ips2', $nilai_raport_sma->ips2); ?>">
                                <small class="info help-block">
                                <b>Input Ips2</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="ips3" class="col-sm-2 control-label">Ips3 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="ips3" id="ips3" placeholder="Ips3" value="<?= set_value('ips3', $nilai_raport_sma->ips3); ?>">
                                <small class="info help-block">
                                <b>Input Ips3</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="ips4" class="col-sm-2 control-label">Ips4 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="ips4" id="ips4" placeholder="Ips4" value="<?= set_value('ips4', $nilai_raport_sma->ips4); ?>">
                                <small class="info help-block">
                                <b>Input Ips4</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="matematika" class="col-sm-2 control-label">Matematika 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="matematika" id="matematika" placeholder="Matematika" value="<?= set_value('matematika', $nilai_raport_sma->matematika); ?>">
                                <small class="info help-block">
                                <b>Input Matematika</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="matematika2" class="col-sm-2 control-label">Matematika2 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="matematika2" id="matematika2" placeholder="Matematika2" value="<?= set_value('matematika2', $nilai_raport_sma->matematika2); ?>">
                                <small class="info help-block">
                                <b>Input Matematika2</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="matematika3" class="col-sm-2 control-label">Matematika3 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="matematika3" id="matematika3" placeholder="Matematika3" value="<?= set_value('matematika3', $nilai_raport_sma->matematika3); ?>">
                                <small class="info help-block">
                                <b>Input Matematika3</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="matematika4" class="col-sm-2 control-label">Matematika4 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="matematika4" id="matematika4" placeholder="Matematika4" value="<?= set_value('matematika4', $nilai_raport_sma->matematika4); ?>">
                                <small class="info help-block">
                                <b>Input Matematika4</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="file_raport" class="col-sm-2 control-label">File Raport 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="nilai_raport_sma_file_raport_galery"></div>
                                <input class="data_file data_file_uuid" name="nilai_raport_sma_file_raport_uuid" id="nilai_raport_sma_file_raport_uuid" type="hidden" value="<?= set_value('nilai_raport_sma_file_raport_uuid'); ?>">
                                <input class="data_file" name="nilai_raport_sma_file_raport_name" id="nilai_raport_sma_file_raport_name" type="hidden" value="<?= set_value('nilai_raport_sma_file_raport_name', $nilai_raport_sma->file_raport); ?>">
                                <small class="info help-block">
                                <b>Input File Raport</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="file_raport2" class="col-sm-2 control-label">File Raport2 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="nilai_raport_sma_file_raport2_galery"></div>
                                <input class="data_file data_file_uuid" name="nilai_raport_sma_file_raport2_uuid" id="nilai_raport_sma_file_raport2_uuid" type="hidden" value="<?= set_value('nilai_raport_sma_file_raport2_uuid'); ?>">
                                <input class="data_file" name="nilai_raport_sma_file_raport2_name" id="nilai_raport_sma_file_raport2_name" type="hidden" value="<?= set_value('nilai_raport_sma_file_raport2_name', $nilai_raport_sma->file_raport2); ?>">
                                <small class="info help-block">
                                <b>Input File Raport2</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="file_raport3" class="col-sm-2 control-label">File Raport3 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="nilai_raport_sma_file_raport3_galery"></div>
                                <input class="data_file data_file_uuid" name="nilai_raport_sma_file_raport3_uuid" id="nilai_raport_sma_file_raport3_uuid" type="hidden" value="<?= set_value('nilai_raport_sma_file_raport3_uuid'); ?>">
                                <input class="data_file" name="nilai_raport_sma_file_raport3_name" id="nilai_raport_sma_file_raport3_name" type="hidden" value="<?= set_value('nilai_raport_sma_file_raport3_name', $nilai_raport_sma->file_raport3); ?>">
                                <small class="info help-block">
                                <b>Input File Raport3</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="file_raport4" class="col-sm-2 control-label">File Raport4 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="nilai_raport_sma_file_raport4_galery"></div>
                                <input class="data_file data_file_uuid" name="nilai_raport_sma_file_raport4_uuid" id="nilai_raport_sma_file_raport4_uuid" type="hidden" value="<?= set_value('nilai_raport_sma_file_raport4_uuid'); ?>">
                                <input class="data_file" name="nilai_raport_sma_file_raport4_name" id="nilai_raport_sma_file_raport4_name" type="hidden" value="<?= set_value('nilai_raport_sma_file_raport4_name', $nilai_raport_sma->file_raport4); ?>">
                                <small class="info help-block">
                                <b>Input File Raport4</b> Max Length : 255.</small>
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
              window.location.href = BASE_URL + 'administrator/nilai_raport_sma';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_nilai_raport_sma = $('#form_nilai_raport_sma');
        var data_post = form_nilai_raport_sma.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_nilai_raport_sma.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#nilai_raport_sma_image_galery').find('li').attr('qq-file-id');
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

       $('#nilai_raport_sma_file_raport_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/nilai_raport_sma/upload_file_raport_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/nilai_raport_sma/delete_file_raport_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/nilai_raport_sma/get_file_raport_file/<?= $nilai_raport_sma->id_nilai_raport_sma; ?>',
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
                   var uuid = $('#nilai_raport_sma_file_raport_galery').fineUploader('getUuid', id);
                   $('#nilai_raport_sma_file_raport_uuid').val(uuid);
                   $('#nilai_raport_sma_file_raport_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#nilai_raport_sma_file_raport_uuid').val();
                  $.get(BASE_URL + '/administrator/nilai_raport_sma/delete_file_raport_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#nilai_raport_sma_file_raport_uuid').val('');
                  $('#nilai_raport_sma_file_raport_name').val('');
                }
              }
          }
      }); /*end file_raport galey*/
                            var params = {};
       params[csrf] = token;

       $('#nilai_raport_sma_file_raport2_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/nilai_raport_sma/upload_file_raport2_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/nilai_raport_sma/delete_file_raport2_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/nilai_raport_sma/get_file_raport2_file/<?= $nilai_raport_sma->id_nilai_raport_sma; ?>',
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
                   var uuid = $('#nilai_raport_sma_file_raport2_galery').fineUploader('getUuid', id);
                   $('#nilai_raport_sma_file_raport2_uuid').val(uuid);
                   $('#nilai_raport_sma_file_raport2_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#nilai_raport_sma_file_raport2_uuid').val();
                  $.get(BASE_URL + '/administrator/nilai_raport_sma/delete_file_raport2_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#nilai_raport_sma_file_raport2_uuid').val('');
                  $('#nilai_raport_sma_file_raport2_name').val('');
                }
              }
          }
      }); /*end file_raport2 galey*/
                            var params = {};
       params[csrf] = token;

       $('#nilai_raport_sma_file_raport3_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/nilai_raport_sma/upload_file_raport3_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/nilai_raport_sma/delete_file_raport3_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/nilai_raport_sma/get_file_raport3_file/<?= $nilai_raport_sma->id_nilai_raport_sma; ?>',
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
                   var uuid = $('#nilai_raport_sma_file_raport3_galery').fineUploader('getUuid', id);
                   $('#nilai_raport_sma_file_raport3_uuid').val(uuid);
                   $('#nilai_raport_sma_file_raport3_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#nilai_raport_sma_file_raport3_uuid').val();
                  $.get(BASE_URL + '/administrator/nilai_raport_sma/delete_file_raport3_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#nilai_raport_sma_file_raport3_uuid').val('');
                  $('#nilai_raport_sma_file_raport3_name').val('');
                }
              }
          }
      }); /*end file_raport3 galey*/
                            var params = {};
       params[csrf] = token;

       $('#nilai_raport_sma_file_raport4_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/nilai_raport_sma/upload_file_raport4_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/nilai_raport_sma/delete_file_raport4_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/nilai_raport_sma/get_file_raport4_file/<?= $nilai_raport_sma->id_nilai_raport_sma; ?>',
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
                   var uuid = $('#nilai_raport_sma_file_raport4_galery').fineUploader('getUuid', id);
                   $('#nilai_raport_sma_file_raport4_uuid').val(uuid);
                   $('#nilai_raport_sma_file_raport4_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#nilai_raport_sma_file_raport4_uuid').val();
                  $.get(BASE_URL + '/administrator/nilai_raport_sma/delete_file_raport4_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#nilai_raport_sma_file_raport4_uuid').val('');
                  $('#nilai_raport_sma_file_raport4_name').val('');
                }
              }
          }
      }); /*end file_raport4 galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>