
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
        Perguruan Tinggi        <small><?= cclang('new', ['Perguruan Tinggi']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/pt_perguruan_tinggi'); ?>">Perguruan Tinggi</a></li>
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
                            <h3 class="widget-user-username">Perguruan Tinggi</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Perguruan Tinggi']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_pt_perguruan_tinggi', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_pt_perguruan_tinggi', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nama_pt" class="col-sm-2 control-label">Perguruan Tinggi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_pt" id="nama_pt" placeholder="Perguruan Tinggi" value="<?= set_value('nama_pt'); ?>">
                                <small class="info help-block">
                                <b>Input Nama Pt</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="inisial" class="col-sm-2 control-label">Singkatan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="inisial" id="inisial" placeholder="Singkatan" value="<?= set_value('inisial'); ?>">
                                <small class="info help-block">
                                <b>Input Inisial</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="provinsi" class="col-sm-2 control-label">Provinsi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="provinsi" id="provinsi" data-placeholder="Select Provinsi" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('provinces') as $row): ?>
                                    <option value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Provinsi</b> Max Length : 100.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="kota" class="col-sm-2 control-label">Kota 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="kota" id="kota" data-placeholder="Select Kota" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('regencies') as $row): ?>
                                    <option value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Kota</b> Max Length : 120.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="logo_ptn" class="col-sm-2 control-label">Logo PTN 
                            </label>
                            <div class="col-sm-8">
                                <div id="pt_perguruan_tinggi_logo_ptn_galery"></div>
                                <input class="data_file" name="pt_perguruan_tinggi_logo_ptn_uuid" id="pt_perguruan_tinggi_logo_ptn_uuid" type="hidden" value="<?= set_value('pt_perguruan_tinggi_logo_ptn_uuid'); ?>">
                                <input class="data_file" name="pt_perguruan_tinggi_logo_ptn_name" id="pt_perguruan_tinggi_logo_ptn_name" type="hidden" value="<?= set_value('pt_perguruan_tinggi_logo_ptn_name'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="deskripsi" class="col-sm-2 control-label">Deskripsi 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="deskripsi" id="deskripsi" placeholder="Deskripsi" value="<?= set_value('deskripsi'); ?>">
                                <small class="info help-block">
                                <b>Input Deskripsi</b> Max Length : 255.</small>
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
              window.location.href = BASE_URL + 'administrator/pt_perguruan_tinggi';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_pt_perguruan_tinggi = $('#form_pt_perguruan_tinggi');
        var data_post = form_pt_perguruan_tinggi.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/pt_perguruan_tinggi/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('.steps li').removeClass('error');
          $('form').find('.error-input').remove();
          if(res.success) {
            var id_logo_ptn = $('#pt_perguruan_tinggi_logo_ptn_galery').find('li').attr('qq-file-id');
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            if (typeof id_logo_ptn !== 'undefined') {
                    $('#pt_perguruan_tinggi_logo_ptn_galery').fineUploader('deleteFile', id_logo_ptn);
                }
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
                
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

       $('#pt_perguruan_tinggi_logo_ptn_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/pt_perguruan_tinggi/upload_logo_ptn_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/pt_perguruan_tinggi/delete_logo_ptn_file',
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
                   var uuid = $('#pt_perguruan_tinggi_logo_ptn_galery').fineUploader('getUuid', id);
                   $('#pt_perguruan_tinggi_logo_ptn_uuid').val(uuid);
                   $('#pt_perguruan_tinggi_logo_ptn_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#pt_perguruan_tinggi_logo_ptn_uuid').val();
                  $.get(BASE_URL + '/administrator/pt_perguruan_tinggi/delete_logo_ptn_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#pt_perguruan_tinggi_logo_ptn_uuid').val('');
                  $('#pt_perguruan_tinggi_logo_ptn_name').val('');
                }
              }
          }
      }); /*end logo_ptn galery*/
              
 
       

      
    
    
    }); /*end doc ready*/
</script>