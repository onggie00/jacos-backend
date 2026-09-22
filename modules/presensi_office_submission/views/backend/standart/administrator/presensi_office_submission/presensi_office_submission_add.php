
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
        Presensi Office Submission        <small><?= cclang('new', ['Presensi Office Submission']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/presensi_office_submission'); ?>">Presensi Office Submission</a></li>
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
                            <h3 class="widget-user-username">Presensi Office Submission</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Presensi Office Submission']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_presensi_office_submission', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_presensi_office_submission', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                        
                        <div class="form-group ">
                            <label for="npp" class="col-sm-2 control-label">Role User 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control" name="tipe_user" id="tipe_user" data-placeholder="Select Role User" >
                                    <option value="" selected></option>
                                    <option value="pegawai">Karyawan</option>
                                    <option value="guru_sd">GURU SD</option>
                                    <option value="guru_smp">GURU SMP</option>
                                    <option value="guru_sma">GURU SMA</option>
                                    <option value="pimpinan_sd">PIMPINAN SD</option>
                                    <option value="pimpinan_smp">PIMPINAN SMP</option>
                                    <option value="pimpinan_sma">PIMPINAN SMA</option>
                                </select>
                                <small class="info help-block">
                                <b>Input Role User</b> Max Length : 30.</small>
                            </div>
                        </div>

                                                <div class="form-group ">
                            <label for="npp" class="col-sm-2 control-label">NPP 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npp" id="npp" placeholder="NPP" value="<?= set_value('npp'); ?>" readonly>
                                <small class="info help-block">
                                <b>Input Npp</b> Max Length : 30.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nama_lengkap" class="col-sm-2 control-label">Nama Lengkap 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="nama_lengkap" id="nama_lengkap" data-placeholder="Select Nama Lengkap" >
                                    <option value=""></option>
                                </select>
                                <small class="info help-block">
                                <b>Input Nama Lengkap</b> Max Length : 30.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="submission_code" class="col-sm-2 control-label">Jenis 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="submission_code" id="submission_code" data-placeholder="Select Jenis" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('presensi_submission_type') as $row): ?>
                                    <option value="<?= $row->code ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Submission Code</b> Max Length : 10.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="notes" class="col-sm-2 control-label">Keterangan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="notes" name="notes" rows="5" cols="80"><?= set_value('Notes'); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="file_submission" class="col-sm-2 control-label">File 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="presensi_office_submission_file_submission_galery"></div>
                                <input class="data_file" name="presensi_office_submission_file_submission_uuid" id="presensi_office_submission_file_submission_uuid" type="hidden" value="<?= set_value('presensi_office_submission_file_submission_uuid'); ?>">
                                <input class="data_file" name="presensi_office_submission_file_submission_name" id="presensi_office_submission_file_submission_name" type="hidden" value="<?= set_value('presensi_office_submission_file_submission_name'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group  wrapper-options-crud">
                            <label for="submission_status" class="col-sm-2 control-label">Status Approval 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input type="radio" class="flat-red" name="submission_status" value="0"> Menunggu Persetujuan                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input type="radio" class="flat-red" name="submission_status" value="1"> Disetujui                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input type="radio" class="flat-red" name="submission_status" value="2"> Ditolak                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input type="radio" class="flat-red" name="submission_status" value="3"> Perlu Validasi Lebih Lanjut                                    </label>
                                    </div>
                                    </select>
                                <div class="row-fluid clear-both">
                                <small class="info help-block">
                                </small>
                                </div>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="date_start" class="col-sm-2 control-label">Tanggal Mulai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="date_start"  placeholder="Tanggal Mulai" id="date_start">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="date_end" class="col-sm-2 control-label">Tanggal Selesai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="date_end"  placeholder="Tanggal Selesai" id="date_end">
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
                                <input type="text" class="form-control" name="role" id="role" placeholder="Role" value="<?= set_value('role'); ?>" readonly>
                                <small class="info help-block">
                                <b>Input Role</b> Max Length : 30.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="head_role" class="col-sm-2 control-label">Head Role (optional) 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="head_role" id="head_role" placeholder="Head Role (optional)" value="<?= set_value('head_role'); ?>" readonly>
                                <small class="info help-block">
                                <b>Input Head Role</b> Max Length : 30.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="updated_by" class="col-sm-2 control-label">Diubah Oleh 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="updated_by" id="updated_by" placeholder="Diubah Oleh" value="<?= set_value('updated_by'); ?>">
                                <small class="info help-block">
                                <b>Input Updated By</b> Max Length : 255.</small>
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

      CKEDITOR.replace('notes'); 
      var notes = CKEDITOR.instances.notes;
                   
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
              window.location.href = BASE_URL + 'administrator/presensi_office_submission';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#notes').val(notes.getData());
                    
        var form_presensi_office_submission = $('#form_presensi_office_submission');
        var data_post = form_presensi_office_submission.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/presensi_office_submission/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('.steps li').removeClass('error');
          $('form').find('.error-input').remove();
          if(res.success) {
            var id_file_submission = $('#presensi_office_submission_file_submission_galery').find('li').attr('qq-file-id');
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            if (typeof id_file_submission !== 'undefined') {
                    $('#presensi_office_submission_file_submission_galery').fineUploader('deleteFile', id_file_submission);
                }
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
            notes.setData('');
                
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

       $('#presensi_office_submission_file_submission_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/presensi_office_submission/upload_file_submission_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/presensi_office_submission/delete_file_submission_file',
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
                   var uuid = $('#presensi_office_submission_file_submission_galery').fineUploader('getUuid', id);
                   $('#presensi_office_submission_file_submission_uuid').val(uuid);
                   $('#presensi_office_submission_file_submission_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#presensi_office_submission_file_submission_uuid').val();
                  $.get(BASE_URL + '/administrator/presensi_office_submission/delete_file_submission_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#presensi_office_submission_file_submission_uuid').val('');
                  $('#presensi_office_submission_file_submission_name').val('');
                }
              }
          }
      }); /*end file_submission galery*/

    //get user when tipe_user clicked
    $('#tipe_user').change(function() {
            var tipe_user = $(this).val();
            var $select = $('#nama_lengkap');
            $('#npp').val('');
            $('#role').val('');
            $('#head_role').val('');
            
            // Reset siswa & tagihan
            $select.empty().append("<option value=''>Pilih Tipe User Dahulu</option>");
            $select.prop('disabled', true);

        $.ajax({
            url: BASE_URL + '/administrator/presensi_office_submission/get_user',
            type: 'POST',
            data: { tipe_user: tipe_user },
            dataType: 'html',
            success: function(response) {
                $select.empty().html(response);
                $select.prop('disabled', false);
                $select.trigger('chosen:updated');
            },
            error: function() {
                alert('Gagal mengambil data siswa.');
            }
        });
    });

    $('#nama_lengkap').change(function() {
        var nama_lengkap = $(this).val();
        var npp = nama_lengkap.split('|')[0];
        var presensi_role = nama_lengkap.split('|')[2];
        var head_role = nama_lengkap.split('|')[3];
        $('#npp').val(npp);
        $('#role').val(presensi_role);
        if (head_role == "NULL") {
            $('#head_role').val('');
        } else {
            $('#head_role').val(head_role);
        }
    });

    }); /*end doc ready*/
</script>