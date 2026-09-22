

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Pramubhakti        <small>Edit Pramubhakti</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/pramubhakti'); ?>">Pramubhakti</a></li>
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
                            <h3 class="widget-user-username">Pramubhakti</h3>
                            <h5 class="widget-user-desc">Edit Pramubhakti</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/pramubhakti/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_pramubhakti', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_pramubhakti', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nama_lengkap" class="col-sm-2 control-label">Nama Lengkap 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" value="<?= set_value('nama_lengkap', $pramubhakti->nama_lengkap); ?>">
                                <small class="info help-block">
                                <b>Input Nama Lengkap</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nik" class="col-sm-2 control-label">NIK 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nik" id="nik" placeholder="NIK" value="<?= set_value('nik', $pramubhakti->nik); ?>">
                                <small class="info help-block">
                                <b>Input Nik</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nuptk" class="col-sm-2 control-label">NUPTK 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nuptk" id="nuptk" placeholder="NUPTK" value="<?= set_value('nuptk', $pramubhakti->nuptk); ?>">
                                <small class="info help-block">
                                <b>Input Nuptk</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="npp" class="col-sm-2 control-label">NPP 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npp" id="npp" placeholder="NPP" value="<?= set_value('npp', $pramubhakti->npp); ?>">
                                <small class="info help-block">
                                <b>Input Npp</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="no_telp" class="col-sm-2 control-label">No Telp 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="no_telp" id="no_telp" placeholder="No Telp" value="<?= set_value('no_telp', $pramubhakti->no_telp); ?>">
                                <small class="info help-block">
                                <b>Input No Telp</b> Max Length : 25.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_posisi" class="col-sm-2 control-label">Posisi 
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="id_posisi" id="id_posisi" placeholder="Posisi" value="<?= set_value('id_posisi', $pramubhakti->id_posisi); ?>">
                                <small class="info help-block">
                                <b>Input Id Posisi</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="unit" class="col-sm-2 control-label">Unit 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="unit" id="unit" placeholder="Unit" value="<?= set_value('unit', $pramubhakti->unit); ?>">
                                <small class="info help-block">
                                <b>Input Unit</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="status_kepegawaian" class="col-sm-2 control-label">Status Kepegawaian 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="status_kepegawaian" id="status_kepegawaian" placeholder="Status Kepegawaian" value="<?= set_value('status_kepegawaian', $pramubhakti->status_kepegawaian); ?>">
                                <small class="info help-block">
                                <b>Input Status Kepegawaian</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="emp_code" class="col-sm-2 control-label">Emp Code 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="emp_code" id="emp_code" placeholder="Emp Code" value="<?= set_value('emp_code', $pramubhakti->emp_code); ?>">
                                <small class="info help-block">
                                <b>Input Emp Code</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="token" class="col-sm-2 control-label">Token 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="token" id="token" placeholder="Token" value="<?= set_value('token', $pramubhakti->token); ?>">
                                <small class="info help-block">
                                <b>Input Token</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="token_expired" class="col-sm-2 control-label">Token Expired 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="token_expired"  placeholder="Token Expired" id="token_expired" value="<?= set_value('token_expired', $pramubhakti->token_expired); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="email_ms_office" class="col-sm-2 control-label">Email Ms Office 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email_ms_office" id="email_ms_office" placeholder="Email Ms Office" value="<?= set_value('email_ms_office', $pramubhakti->email_ms_office); ?>">
                                <small class="info help-block">
                                <b>Input Email Ms Office</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="no_kk" class="col-sm-2 control-label">No Kk 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="no_kk" id="no_kk" placeholder="No Kk" value="<?= set_value('no_kk', $pramubhakti->no_kk); ?>">
                                <small class="info help-block">
                                <b>Input No Kk</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tgl_lahir" class="col-sm-2 control-label">Tgl Lahir 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="tgl_lahir"  placeholder="Tgl Lahir" id="tgl_lahir" value="<?= set_value('pramubhakti_tgl_lahir_name', $pramubhakti->tgl_lahir); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                       
                                                 
                                                <div class="form-group ">
                            <label for="foto_profil" class="col-sm-2 control-label">Foto Profil 
                            </label>
                            <div class="col-sm-8">
                                <textarea id="foto_profil" name="foto_profil" rows="10" cols="80"> <?= set_value('foto_profil', $pramubhakti->foto_profil); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="presensi_role" class="col-sm-2 control-label">Role 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="presensi_role" id="presensi_role" placeholder="Role" value="<?= set_value('presensi_role', $pramubhakti->presensi_role); ?>">
                                <small class="info help-block">
                                <b>Input Presensi Role</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="device_id" class="col-sm-2 control-label">Device Id 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="device_id" id="device_id" placeholder="Device Id" value="<?= set_value('device_id', $pramubhakti->device_id); ?>">
                                <small class="info help-block">
                                <b>Input Device Id</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="created_at" class="col-sm-2 control-label">Created At 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="created_at"  placeholder="Created At" id="created_at" value="<?= set_value('created_at', $pramubhakti->created_at); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="deleted_at" class="col-sm-2 control-label">Deleted At 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="deleted_at"  placeholder="Deleted At" id="deleted_at" value="<?= set_value('deleted_at', $pramubhakti->deleted_at); ?>">
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
       
      
      CKEDITOR.replace('foto_profil'); 
      var foto_profil = CKEDITOR.instances.foto_profil;
                   
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
              window.location.href = BASE_URL + 'administrator/pramubhakti';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#foto_profil').val(foto_profil.getData());
                    
        var form_pramubhakti = $('#form_pramubhakti');
        var data_post = form_pramubhakti.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_pramubhakti.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#pramubhakti_image_galery').find('li').attr('qq-file-id');
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
      
       
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>