

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Presensi Setting Time        <small>Edit Presensi Setting Time</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/presensi_setting_shift'); ?>">Presensi Setting Time</a></li>
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
                            <h3 class="widget-user-username">Presensi Setting Time</h3>
                            <h5 class="widget-user-desc">Edit Presensi Setting Time</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/presensi_setting_shift/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_presensi_setting_shift', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_presensi_setting_shift', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_role" class="col-sm-2 control-label">Role 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_role" id="id_role" data-placeholder="Select Role" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('presensi_setting_role') as $row): ?>
                                    <option <?=  $row->id ==  $presensi_setting_shift->id_role ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->nama_role; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Role</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="hari" class="col-sm-2 control-label">Hari 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="hari[]" id="hari" data-placeholder="Select Hari" multiple >
                                    <option value=""></option>
                                    <option <?= in_array('senin', explode(',', $presensi_setting_shift->hari)) ? 'selected' : ''; ?>  value="senin">Senin</option>
                                    <option <?= in_array('selasa', explode(',', $presensi_setting_shift->hari)) ? 'selected' : ''; ?>  value="selasa">Selasa</option>
                                    <option <?= in_array('rabu', explode(',', $presensi_setting_shift->hari)) ? 'selected' : ''; ?>  value="rabu">Rabu</option>
                                    <option <?= in_array('kamis', explode(',', $presensi_setting_shift->hari)) ? 'selected' : ''; ?>  value="kamis">Kamis</option>
                                    <option <?= in_array('jumat', explode(',', $presensi_setting_shift->hari)) ? 'selected' : ''; ?>  value="jumat">Jumat</option>
                                    <option <?= in_array('sabtu', explode(',', $presensi_setting_shift->hari)) ? 'selected' : ''; ?>  value="sabtu">Sabtu</option>
                                    <option <?= in_array('minggu', explode(',', $presensi_setting_shift->hari)) ? 'selected' : ''; ?>  value="minggu">Minggu</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Hari</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="start_checkin" class="col-sm-2 control-label">Masuk Start 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="start_checkin" id="start_checkin" value="<?= set_value('presensi_setting_shift_start_checkin_name', $presensi_setting_shift->start_checkin); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="limit_checkin" class="col-sm-2 control-label">Masuk Cutoff 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="limit_checkin" id="limit_checkin" value="<?= set_value('presensi_setting_shift_limit_checkin_name', $presensi_setting_shift->limit_checkin); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="start_checkout" class="col-sm-2 control-label">Pulang Start 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="start_checkout" id="start_checkout" value="<?= set_value('presensi_setting_shift_start_checkout_name', $presensi_setting_shift->start_checkout); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="limit_checkout" class="col-sm-2 control-label">Pulang Cutoff 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="limit_checkout" id="limit_checkout" value="<?= set_value('presensi_setting_shift_limit_checkout_name', $presensi_setting_shift->limit_checkout); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="updated_by" class="col-sm-2 control-label">Diubah Oleh 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="updated_by" id="updated_by" data-placeholder="Select Diubah Oleh" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('aauth_users') as $row): ?>
                                    <option <?=  $row->id ==  $presensi_setting_shift->updated_by ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->email; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Updated By</b> Max Length : 150.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="updated_at" class="col-sm-2 control-label">Diubah Tgl 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="updated_at"  placeholder="Diubah Tgl" id="updated_at" value="<?= set_value('updated_at', $presensi_setting_shift->updated_at); ?>">
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
              window.location.href = BASE_URL + 'administrator/presensi_setting_shift';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_presensi_setting_shift = $('#form_presensi_setting_shift');
        var data_post = form_presensi_setting_shift.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_presensi_setting_shift.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#presensi_setting_shift_image_galery').find('li').attr('qq-file-id');
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