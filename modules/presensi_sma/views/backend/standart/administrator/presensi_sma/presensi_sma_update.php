

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Presensi SMA        <small>Edit Presensi SMA</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/presensi_sma'); ?>">Presensi SMA</a></li>
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
                            <h3 class="widget-user-username">Presensi SMA</h3>
                            <h5 class="widget-user-desc">Edit Presensi SMA</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/presensi_sma/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_presensi_sma', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_presensi_sma', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_siswa_aktif" class="col-sm-2 control-label">Siswa Aktif 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Select Siswa Aktif" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_sma_aktif') as $row): ?>
                                    <option <?=  $row->id_siswa_sma_aktif ==  $presensi_sma->id_siswa_aktif ? 'selected' : ''; ?> value="<?= $row->id_siswa_sma_aktif ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Siswa Aktif</b> Max Length : 50.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="wifi_ssid" class="col-sm-2 control-label">WIFI SSID 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="wifi_ssid" id="wifi_ssid" data-placeholder="Select WIFI SSID" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('pengaturan_whitelist_ssid') as $row): ?>
                                    <option <?=  $row->nama_ssid ==  $presensi_sma->wifi_ssid ? 'selected' : ''; ?> value="<?= $row->nama_ssid ?>"><?= $row->nama_ssid; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Wifi Ssid</b> Max Length : 50.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="wifi_ip" class="col-sm-2 control-label">IP 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="wifi_ip" id="wifi_ip" placeholder="IP" value="<?= set_value('wifi_ip', $presensi_sma->wifi_ip); ?>">
                                <small class="info help-block">
                                <b>Input Wifi Ip</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="hari_absen" class="col-sm-2 control-label">Hari Presensi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="hari_absen" id="hari_absen" placeholder="Hari Presensi" value="<?= set_value('hari_absen', $presensi_sma->hari_absen); ?>">
                                <small class="info help-block">
                                <b>Input Hari Absen</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="waktu_absen" class="col-sm-2 control-label">Waktu Presensi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="waktu_absen" id="waktu_absen" value="<?= set_value('presensi_sma_waktu_absen_name', $presensi_sma->waktu_absen); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tanggal_absen" class="col-sm-2 control-label">Tanggal Presensi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="tanggal_absen"  placeholder="Tanggal Presensi" id="tanggal_absen" value="<?= set_value('presensi_sma_tanggal_absen_name', $presensi_sma->tanggal_absen); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                       
                                                 
                                                <div class="form-group ">
                            <label for="status_absen" class="col-sm-2 control-label">Status Presensi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="status_absen" id="status_absen" placeholder="Status Presensi" value="<?= set_value('status_absen', $presensi_sma->status_absen); ?>">
                                <small class="info help-block">
                                <b>Input Status Absen</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="alasan_terlambat" class="col-sm-2 control-label">Alasan Keterlambatan 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="alasan_terlambat" id="alasan_terlambat" placeholder="Alasan Keterlambatan" value="<?= set_value('alasan_terlambat', $presensi_sma->alasan_terlambat); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_izin" class="col-sm-2 control-label">Izin 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_izin" id="id_izin" data-placeholder="Select Izin" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('izin_siswa_sma') as $row): ?>
                                    <option <?=  $row->id ==  $presensi_sma->id_izin ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->jenis_izin; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Izin</b> Max Length : 11.</small>
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
              window.location.href = BASE_URL + 'administrator/presensi_sma';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_presensi_sma = $('#form_presensi_sma');
        var data_post = form_presensi_sma.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_presensi_sma.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#presensi_sma_image_galery').find('li').attr('qq-file-id');
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