

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
        Rapor FT        <small>Edit Rapor FT</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/siswa_ft_aktif_raport'); ?>">Rapor FT</a></li>
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
                            <h3 class="widget-user-username">Rapor FT</h3>
                            <h5 class="widget-user-desc">Edit Rapor FT</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/siswa_ft_aktif_raport/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_siswa_ft_aktif_raport', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_siswa_ft_aktif_raport', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nis" class="col-sm-2 control-label">NIS 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nis" id="nis" placeholder="NIS" value="<?= set_value('nis', $siswa_ft_aktif_raport->nis); ?>">
                                <small class="info help-block">
                                <b>Input Nis</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nama_lengkap" class="col-sm-2 control-label">Nama Lengkap 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" value="<?= set_value('nama_lengkap', $siswa_ft_aktif_raport->nama_lengkap); ?>">
                                <small class="info help-block">
                                <b>Input Nama Lengkap</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_siswa_aktif" class="col-sm-2 control-label">Detail Siswa 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Select Detail Siswa" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_ft_aktif') as $row): ?>
                                    <option <?=  $row->id_siswa_ft_aktif ==  $siswa_ft_aktif_raport->id_siswa_aktif ? 'selected' : ''; ?> value="<?= $row->id_siswa_ft_aktif ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Siswa Aktif</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tahun_ajaran" id="tahun_ajaran" placeholder="Tahun Ajaran" value="<?= set_value('tahun_ajaran', $siswa_ft_aktif_raport->tahun_ajaran); ?>">
                                <small class="info help-block">
                                <b>Input Tahun Ajaran</b> Max Length : 30.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jenis_ujian" class="col-sm-2 control-label">Jenis Ujian 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="jenis_ujian" id="jenis_ujian" placeholder="Jenis Ujian" value="<?= set_value('jenis_ujian', $siswa_ft_aktif_raport->jenis_ujian); ?>">
                                <small class="info help-block">
                                <b>Input Jenis Ujian</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tanggal_sync_valid" class="col-sm-2 control-label">Tanggal Sync SPP 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="tanggal_sync_valid"  placeholder="Tanggal Sync SPP" id="tanggal_sync_valid" value="<?= set_value('siswa_ft_aktif_raport_tanggal_sync_valid_name', $siswa_ft_aktif_raport->tanggal_sync_valid); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                       
                                                 
                                                <div class="form-group ">
                            <label for="file_raport" class="col-sm-2 control-label">File Rapor 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="siswa_ft_aktif_raport_file_raport_galery"></div>
                                <input class="data_file data_file_uuid" name="siswa_ft_aktif_raport_file_raport_uuid" id="siswa_ft_aktif_raport_file_raport_uuid" type="hidden" value="<?= set_value('siswa_ft_aktif_raport_file_raport_uuid'); ?>">
                                <input class="data_file" name="siswa_ft_aktif_raport_file_raport_name" id="siswa_ft_aktif_raport_file_raport_name" type="hidden" value="<?= set_value('siswa_ft_aktif_raport_file_raport_name', $siswa_ft_aktif_raport->file_raport); ?>">
                                <small class="info help-block">
                                <b>Input File Raport</b> Max Length : 255.</small>
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
              window.location.href = BASE_URL + 'administrator/siswa_ft_aktif_raport';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_siswa_ft_aktif_raport = $('#form_siswa_ft_aktif_raport');
        var data_post = form_siswa_ft_aktif_raport.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_siswa_ft_aktif_raport.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#siswa_ft_aktif_raport_image_galery').find('li').attr('qq-file-id');
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

       $('#siswa_ft_aktif_raport_file_raport_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/siswa_ft_aktif_raport/upload_file_raport_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/siswa_ft_aktif_raport/delete_file_raport_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/siswa_ft_aktif_raport/get_file_raport_file/<?= $siswa_ft_aktif_raport->id; ?>',
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
                   var uuid = $('#siswa_ft_aktif_raport_file_raport_galery').fineUploader('getUuid', id);
                   $('#siswa_ft_aktif_raport_file_raport_uuid').val(uuid);
                   $('#siswa_ft_aktif_raport_file_raport_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#siswa_ft_aktif_raport_file_raport_uuid').val();
                  $.get(BASE_URL + '/administrator/siswa_ft_aktif_raport/delete_file_raport_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#siswa_ft_aktif_raport_file_raport_uuid').val('');
                  $('#siswa_ft_aktif_raport_file_raport_name').val('');
                }
              }
          }
      }); /*end file_raport galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>