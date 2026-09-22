

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
        Anggota Ekskul SD        <small>Edit Anggota Ekskul SD</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/ekskul_member_sd'); ?>">Anggota Ekskul SD</a></li>
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
                            <h3 class="widget-user-username">Anggota Ekskul SD</h3>
                            <h5 class="widget-user-desc">Edit Anggota Ekskul SD</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/ekskul_member_sd/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_ekskul_member_sd', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_ekskul_member_sd', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_ekskul" class="col-sm-2 control-label">Ekstrakurikuler 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_ekskul" id="id_ekskul" data-placeholder="Select Ekstrakurikuler" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('ekskul') as $row): ?>
                                    <option <?=  $row->id_ekskul ==  $ekskul_member_sd->id_ekskul ? 'selected' : ''; ?> value="<?= $row->id_ekskul ?>"><?= $row->nama; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Ekskul</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="id_siswa" class="col-sm-2 control-label">Siswa 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_siswa" id="id_siswa" data-placeholder="Select Siswa" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_sd_aktif') as $row): ?>
                                    <option <?=  $row->id_siswa_sd_aktif ==  $ekskul_member_sd->id_siswa ? 'selected' : ''; ?> value="<?= $row->id_siswa_sd_aktif ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Siswa</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="file_pembayaran" class="col-sm-2 control-label">Bukti Pembayaran 
                            </label>
                            <div class="col-sm-8">
                                <div id="ekskul_member_sd_file_pembayaran_galery"></div>
                                <input class="data_file data_file_uuid" name="ekskul_member_sd_file_pembayaran_uuid" id="ekskul_member_sd_file_pembayaran_uuid" type="hidden" value="<?= set_value('ekskul_member_sd_file_pembayaran_uuid'); ?>">
                                <input class="data_file" name="ekskul_member_sd_file_pembayaran_name" id="ekskul_member_sd_file_pembayaran_name" type="hidden" value="<?= set_value('ekskul_member_sd_file_pembayaran_name', $ekskul_member_sd->file_pembayaran); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="status_member" class="col-sm-2 control-label">Status 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="status_member" id="status_member" data-placeholder="Select Status" >
                                    <option value=""></option>
                                    <option <?= $ekskul_member_sd->status_member == "0" ? 'selected' :''; ?> value="0">TIdak Aktif</option>
                                    <option <?= $ekskul_member_sd->status_member == "1" ? 'selected' :''; ?> value="1">Aktif</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="status_member" class="col-sm-2 control-label">Tahun Ajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tahun_ajaran" id="tahun_ajaran" placeholder="<?= date('Y')."/".(date('Y', strtotime('+1 year'))); ?>" value="<?= $ekskul_member_sd->tahun_ajaran; ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="status_member" class="col-sm-2 control-label">Semester
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="semester" id="semester" placeholder="1 = Ganjil, 2 = Genap" value="<?= $ekskul_member_sd->semester; ?>">
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
              window.location.href = BASE_URL + 'administrator/ekskul_member_sd';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_ekskul_member_sd = $('#form_ekskul_member_sd');
        var data_post = form_ekskul_member_sd.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_ekskul_member_sd.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#ekskul_member_sd_image_galery').find('li').attr('qq-file-id');
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

       $('#ekskul_member_sd_file_pembayaran_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/ekskul_member_sd/upload_file_pembayaran_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/ekskul_member_sd/delete_file_pembayaran_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/ekskul_member_sd/get_file_pembayaran_file/<?= $ekskul_member_sd->id_member; ?>',
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
                   var uuid = $('#ekskul_member_sd_file_pembayaran_galery').fineUploader('getUuid', id);
                   $('#ekskul_member_sd_file_pembayaran_uuid').val(uuid);
                   $('#ekskul_member_sd_file_pembayaran_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#ekskul_member_sd_file_pembayaran_uuid').val();
                  $.get(BASE_URL + '/administrator/ekskul_member_sd/delete_file_pembayaran_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#ekskul_member_sd_file_pembayaran_uuid').val('');
                  $('#ekskul_member_sd_file_pembayaran_name').val('');
                }
              }
          }
      }); /*end file_pembayaran galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>