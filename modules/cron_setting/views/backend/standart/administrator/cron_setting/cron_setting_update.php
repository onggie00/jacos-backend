

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Cron Setting        <small>Edit Cron Setting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/cron_setting'); ?>">Cron Setting</a></li>
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
                            <h3 class="widget-user-username">Cron Setting</h3>
                            <h5 class="widget-user-desc">Edit Cron Setting</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/cron_setting/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_cron_setting', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_cron_setting', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nama_cron" class="col-sm-2 control-label">Nama CRON 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="nama_cron" id="nama_cron" data-placeholder="Select Nama CRON" >
                                    <option value=""></option>
                                    <option <?= $cron_setting->nama_cron == "cron_presensi_siswa" ? 'selected' :''; ?> value="cron_presensi_siswa">Cron Presensi Siswa</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Nama Cron</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_siswa_aktif" class="col-sm-2 control-label">Siswa Aktif 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Select Siswa Aktif" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_sd_aktif') as $row): ?>
                                    <option <?=  $row->id_siswa_sd_aktif ==  $cron_setting->id_siswa_aktif ? 'selected' : ''; ?> value="<?= $row->id_siswa_sd_aktif ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Siswa Aktif</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="jenjang" id="jenjang" data-placeholder="Select Jenjang" >
                                    <option value=""></option>
                                    <option <?= $cron_setting->jenjang == "sd" ? 'selected' :''; ?> value="sd">SD</option>
                                    <option <?= $cron_setting->jenjang == "smp" ? 'selected' :''; ?> value="smp">SMP</option>
                                    <option <?= $cron_setting->jenjang == "sma" ? 'selected' :''; ?> value="sma">SMA</option>
                                    <option <?= $cron_setting->jenjang == "ft" ? 'selected' :''; ?> value="ft">FT</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Jenjang</b> Max Length : 3.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_kelas" class="col-sm-2 control-label">Kelas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_kelas" id="id_kelas" data-placeholder="Select Kelas" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('kelas_sd') as $row): ?>
                                    <option <?=  $row->id_kelas_sd ==  $cron_setting->id_kelas ? 'selected' : ''; ?> value="<?= $row->id_kelas_sd ?>"><?= $row->nama_kelas; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Kelas</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="is_active" class="col-sm-2 control-label">Aktif? 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="is_active" id="is_active" data-placeholder="Select Aktif?" >
                                    <option value=""></option>
                                    <option <?= $cron_setting->is_active == "0" ? 'selected' :''; ?> value="0">Nonaktif</option>
                                    <option <?= $cron_setting->is_active == "1" ? 'selected' :''; ?> value="1">Aktif</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Is Active</b> Max Length : 1.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="date_deactivate" class="col-sm-2 control-label">Tanggal Nonaktifkan Ulang 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="date_deactivate"  placeholder="Tanggal Nonaktifkan Ulang" id="date_deactivate" value="<?= set_value('cron_setting_date_deactivate_name', $cron_setting->date_deactivate); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                       
                                                 
                                                <div class="form-group ">
                            <label for="date_reactivate" class="col-sm-2 control-label">Tanggal Pengaktifan Ulang 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="date_reactivate"  placeholder="Tanggal Pengaktifan Ulang" id="date_reactivate" value="<?= set_value('cron_setting_date_reactivate_name', $cron_setting->date_reactivate); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                       
                                                 
                                                <div class="form-group ">
                            <label for="updated_at" class="col-sm-2 control-label">Tanggal Update 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="updated_at"  placeholder="Tanggal Update" id="updated_at" value="<?= set_value('updated_at', $cron_setting->updated_at); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="updated_by" class="col-sm-2 control-label">Diupdate Oleh? 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="updated_by" id="updated_by" data-placeholder="Select Diupdate Oleh?" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('aauth_users') as $row): ?>
                                    <option <?=  $row->id ==  $cron_setting->updated_by ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->email; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Updated By</b> Max Length : 11.</small>
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
              window.location.href = BASE_URL + 'administrator/cron_setting';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_cron_setting = $('#form_cron_setting');
        var data_post = form_cron_setting.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_cron_setting.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#cron_setting_image_galery').find('li').attr('qq-file-id');
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