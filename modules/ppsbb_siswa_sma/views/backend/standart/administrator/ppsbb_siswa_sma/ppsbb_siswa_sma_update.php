

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
        PPSBB Siswa SMA        <small>Edit PPSBB Siswa SMA</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/ppsbb_siswa_sma'); ?>">PPSBB Siswa SMA</a></li>
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
                            <h3 class="widget-user-username">PPSBB Siswa SMA</h3>
                            <h5 class="widget-user-desc">Edit PPSBB Siswa SMA</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/ppsbb_siswa_sma/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_ppsbb_siswa_sma', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_ppsbb_siswa_sma', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_siswa_sma" class="col-sm-2 control-label">Id Siswa Sma 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_siswa_sma" id="id_siswa_sma" data-placeholder="Select Id Siswa Sma" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_sma') as $row): ?>
                                    <option <?=  $row->id_siswa_sma ==  $ppsbb_siswa_sma->id_siswa_sma ? 'selected' : ''; ?> value="<?= $row->id_siswa_sma ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Siswa Sma</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="jenis_prestasi" class="col-sm-2 control-label">Jenis Prestasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="jenis_prestasi" id="jenis_prestasi" data-placeholder="Select Jenis Prestasi" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('jenis_prestasi') as $row): ?>
                                    <option <?=  $row->id_jenis_prestasi ==  $ppsbb_siswa_sma->jenis_prestasi ? 'selected' : ''; ?> value="<?= $row->id_jenis_prestasi ?>"><?= $row->jenis_prestasi; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Jenis Prestasi</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="nama_prestasi" class="col-sm-2 control-label">Nama Prestasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_prestasi" id="nama_prestasi" placeholder="Nama Prestasi" value="<?= set_value('nama_prestasi', $ppsbb_siswa_sma->nama_prestasi); ?>">
                                <small class="info help-block">
                                <b>Input Nama Prestasi</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="keterangan_prestasi" class="col-sm-2 control-label">Keterangan Prestasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="keterangan_prestasi" id="keterangan_prestasi" data-placeholder="Select Keterangan Prestasi" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('keterangan_prestasi') as $row): ?>
                                    <option <?=  $row->id_keterangan_prestasi ==  $ppsbb_siswa_sma->keterangan_prestasi ? 'selected' : ''; ?> value="<?= $row->id_keterangan_prestasi ?>"><?= $row->keterangan_prestasi; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Keterangan Prestasi</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="keterangan_prestasi_lainnya" class="col-sm-2 control-label">Keterangan Prestasi Lainnya 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="keterangan_prestasi_lainnya" id="keterangan_prestasi_lainnya" placeholder="Keterangan Prestasi Lainnya" value="<?= set_value('keterangan_prestasi_lainnya', $ppsbb_siswa_sma->keterangan_prestasi_lainnya); ?>">
                                <small class="info help-block">
                                <b>Input Keterangan Prestasi Lainnya</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tahun_prestasi" class="col-sm-2 control-label">Tahun Prestasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="tahun_prestasi" id="tahun_prestasi" placeholder="Tahun Prestasi" value="<?= set_value('tahun_prestasi', $ppsbb_siswa_sma->tahun_prestasi); ?>">
                                <small class="info help-block">
                                <b>Input Tahun Prestasi</b> Max Length : 4.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="sertifikat" class="col-sm-2 control-label">Sertifikat 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="ppsbb_siswa_sma_sertifikat_galery"></div>
                                <input class="data_file data_file_uuid" name="ppsbb_siswa_sma_sertifikat_uuid" id="ppsbb_siswa_sma_sertifikat_uuid" type="hidden" value="<?= set_value('ppsbb_siswa_sma_sertifikat_uuid'); ?>">
                                <input class="data_file" name="ppsbb_siswa_sma_sertifikat_name" id="ppsbb_siswa_sma_sertifikat_name" type="hidden" value="<?= set_value('ppsbb_siswa_sma_sertifikat_name', $ppsbb_siswa_sma->sertifikat); ?>">
                                <small class="info help-block">
                                <b>Input Sertifikat</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="jenis_jenjang" class="col-sm-2 control-label">Jenis Jenjang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="jenis_jenjang" id="jenis_jenjang" data-placeholder="Select Jenis Jenjang" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('jenis_jenjang') as $row): ?>
                                    <option <?=  $row->id_jenis_jenjang ==  $ppsbb_siswa_sma->jenis_jenjang ? 'selected' : ''; ?> value="<?= $row->id_jenis_jenjang ?>"><?= $row->jenis_jenjang; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Jenis Jenjang</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="jenis_lomba" class="col-sm-2 control-label">Jenis Lomba 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="jenis_lomba" id="jenis_lomba" data-placeholder="Select Jenis Lomba" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('jenis_lomba') as $row): ?>
                                    <option <?=  $row->id_jenis_lomba ==  $ppsbb_siswa_sma->jenis_lomba ? 'selected' : ''; ?> value="<?= $row->id_jenis_lomba ?>"><?= $row->jenis_lomba; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Jenis Lomba</b> Max Length : 11.</small>
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
              window.location.href = BASE_URL + 'administrator/ppsbb_siswa_sma';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_ppsbb_siswa_sma = $('#form_ppsbb_siswa_sma');
        var data_post = form_ppsbb_siswa_sma.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_ppsbb_siswa_sma.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#ppsbb_siswa_sma_image_galery').find('li').attr('qq-file-id');
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

       $('#ppsbb_siswa_sma_sertifikat_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/ppsbb_siswa_sma/upload_sertifikat_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/ppsbb_siswa_sma/delete_sertifikat_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/ppsbb_siswa_sma/get_sertifikat_file/<?= $ppsbb_siswa_sma->id_ppsbb_siswa_sma; ?>',
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
                   var uuid = $('#ppsbb_siswa_sma_sertifikat_galery').fineUploader('getUuid', id);
                   $('#ppsbb_siswa_sma_sertifikat_uuid').val(uuid);
                   $('#ppsbb_siswa_sma_sertifikat_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#ppsbb_siswa_sma_sertifikat_uuid').val();
                  $.get(BASE_URL + '/administrator/ppsbb_siswa_sma/delete_sertifikat_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#ppsbb_siswa_sma_sertifikat_uuid').val('');
                  $('#ppsbb_siswa_sma_sertifikat_name').val('');
                }
              }
          }
      }); /*end sertifikat galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>