

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
        Agenda        <small>Edit Agenda</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/agenda'); ?>">Agenda</a></li>
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
                            <h3 class="widget-user-username">Agenda</h3>
                            <h5 class="widget-user-desc">Edit Agenda</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/agenda/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_agenda', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_agenda', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="judul" class="col-sm-2 control-label">Judul 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="judul" id="judul" placeholder="Judul" value="<?= set_value('judul', $agenda->judul); ?>">
                                <small class="info help-block">
                                <b>Input Judul</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="deskripsi" class="col-sm-2 control-label">Deskripsi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="deskripsi" name="deskripsi" rows="10" cols="80"> <?= set_value('deskripsi', $agenda->deskripsi); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="img_thumbnail" class="col-sm-2 control-label">Img Thumbnail 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="agenda_img_thumbnail_galery"></div>
                                <input class="data_file data_file_uuid" name="agenda_img_thumbnail_uuid" id="agenda_img_thumbnail_uuid" type="hidden" value="<?= set_value('agenda_img_thumbnail_uuid'); ?>">
                                <input class="data_file" name="agenda_img_thumbnail_name" id="agenda_img_thumbnail_name" type="hidden" value="<?= set_value('agenda_img_thumbnail_name', $agenda->img_thumbnail); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="img_agenda" class="col-sm-2 control-label">Img Agenda 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="agenda_img_agenda_galery"></div>
                                <input class="data_file data_file_uuid" name="agenda_img_agenda_uuid" id="agenda_img_agenda_uuid" type="hidden" value="<?= set_value('agenda_img_agenda_uuid'); ?>">
                                <input class="data_file" name="agenda_img_agenda_name" id="agenda_img_agenda_name" type="hidden" value="<?= set_value('agenda_img_agenda_name', $agenda->img_agenda); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="created_at" class="col-sm-2 control-label">Created At 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="created_at"  placeholder="Created At" id="created_at" value="<?= set_value('created_at', $agenda->created_at); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="jenjang" id="jenjang" data-placeholder="Select Jenjang" >
                                    <option value=""></option>
                                    <option <?= $agenda->jenjang == "sd" ? 'selected' :''; ?> value="sd">SD</option>
                                    <option <?= $agenda->jenjang == "smp" ? 'selected' :''; ?> value="smp">SMP</option>
                                    <option <?= $agenda->jenjang == "sma" ? 'selected' :''; ?> value="sma">SMA</option>
                                    <option <?= $agenda->jenjang == "ft" ? 'selected' :''; ?> value="ft">France Track</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_kategori" class="col-sm-2 control-label">Id Kategori 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_kategori" id="id_kategori" data-placeholder="Select Id Kategori" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('kategori_agenda') as $row): ?>
                                    <option <?=  $row->id ==  $agenda->id_kategori ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->nama_kategori; ?></option>
                                    <?php endforeach; ?>  
                                </select>
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
       
      
      CKEDITOR.replace('deskripsi'); 
      var deskripsi = CKEDITOR.instances.deskripsi;
                   
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
              window.location.href = BASE_URL + 'administrator/agenda';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#deskripsi').val(deskripsi.getData());
                    
        var form_agenda = $('#form_agenda');
        var data_post = form_agenda.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_agenda.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#agenda_image_galery').find('li').attr('qq-file-id');
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

       $('#agenda_img_thumbnail_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/agenda/upload_img_thumbnail_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/agenda/delete_img_thumbnail_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/agenda/get_img_thumbnail_file/<?= $agenda->id_agenda; ?>',
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
                   var uuid = $('#agenda_img_thumbnail_galery').fineUploader('getUuid', id);
                   $('#agenda_img_thumbnail_uuid').val(uuid);
                   $('#agenda_img_thumbnail_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#agenda_img_thumbnail_uuid').val();
                  $.get(BASE_URL + '/administrator/agenda/delete_img_thumbnail_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#agenda_img_thumbnail_uuid').val('');
                  $('#agenda_img_thumbnail_name').val('');
                }
              }
          }
      }); /*end img_thumbnail galey*/
                            var params = {};
       params[csrf] = token;

       $('#agenda_img_agenda_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/agenda/upload_img_agenda_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/agenda/delete_img_agenda_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/agenda/get_img_agenda_file/<?= $agenda->id_agenda; ?>',
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
                   var uuid = $('#agenda_img_agenda_galery').fineUploader('getUuid', id);
                   $('#agenda_img_agenda_uuid').val(uuid);
                   $('#agenda_img_agenda_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#agenda_img_agenda_uuid').val();
                  $.get(BASE_URL + '/administrator/agenda/delete_img_agenda_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#agenda_img_agenda_uuid').val('');
                  $('#agenda_img_agenda_name').val('');
                }
              }
          }
      }); /*end img_agenda galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>