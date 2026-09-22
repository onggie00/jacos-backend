

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Catatan Presensi Pelajaran        <small>Edit Catatan Presensi Pelajaran</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/presensi_catatan_pelajaran'); ?>">Catatan Presensi Pelajaran</a></li>
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
                            <h3 class="widget-user-username">Catatan Presensi Pelajaran</h3>
                            <h5 class="widget-user-desc">Edit Catatan Presensi Pelajaran</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/presensi_catatan_pelajaran/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_presensi_catatan_pelajaran', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_presensi_catatan_pelajaran', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="jenjang" id="jenjang" data-placeholder="Select Jenjang" >
                                    <option value=""></option>
                                    <option <?= $presensi_catatan_pelajaran->jenjang == "sd" ? 'selected' :''; ?> value="sd">SD</option>
                                    <option <?= $presensi_catatan_pelajaran->jenjang == "smp" ? 'selected' :''; ?> value="smp">SMP</option>
                                    <option <?= $presensi_catatan_pelajaran->jenjang == "sma" ? 'selected' :''; ?> value="sma">SMA</option>
                                    <option <?= $presensi_catatan_pelajaran->jenjang == "ft" ? 'selected' :''; ?> value="ft">FT</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Jenjang</b> Max Length : 5.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_siswa_aktif" class="col-sm-2 control-label">NIS 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Select NIS" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_sd_aktif') as $row): ?>
                                    <option <?=  $row->id_siswa_sd_aktif ==  $presensi_catatan_pelajaran->id_siswa_aktif ? 'selected' : ''; ?> value="<?= $row->id_siswa_sd_aktif ?>"><?= $row->nis; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Siswa Aktif</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="nama_lengkap" class="col-sm-2 control-label">Nama Lengkap 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" value="<?= set_value('nama_lengkap', $presensi_catatan_pelajaran->nama_lengkap); ?>">
                                <small class="info help-block">
                                <b>Input Nama Lengkap</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="hari" class="col-sm-2 control-label">Hari 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="hari" id="hari" placeholder="Hari" value="<?= set_value('hari', $presensi_catatan_pelajaran->hari); ?>">
                                <small class="info help-block">
                                <b>Input Hari</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tanggal_waktu" class="col-sm-2 control-label">Tanggal Waktu 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="tanggal_waktu"  placeholder="Tanggal Waktu" id="tanggal_waktu" value="<?= set_value('tanggal_waktu', $presensi_catatan_pelajaran->tanggal_waktu); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="status_hadir" class="col-sm-2 control-label">Status Kehadiran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="status_hadir" id="status_hadir" data-placeholder="Select Status Kehadiran" >
                                    <option value=""></option>
                                    <option <?= $presensi_catatan_pelajaran->status_hadir == "Hadir" ? 'selected' :''; ?> value="Hadir">Hadir</option>
                                    <option <?= $presensi_catatan_pelajaran->status_hadir == "Terlambat" ? 'selected' :''; ?> value="Terlambat">Terlambat</option>
                                    <option <?= $presensi_catatan_pelajaran->status_hadir == "Izin" ? 'selected' :''; ?> value="Izin">Izin</option>
                                    <option <?= $presensi_catatan_pelajaran->status_hadir == "Alfa" ? 'selected' :''; ?> value="Alfa">Alfa</option>
                                    <option <?= $presensi_catatan_pelajaran->status_hadir == "Sakit" ? 'selected' :''; ?> value="Sakit">Sakit</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Status Hadir</b> Max Length : 30.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="keterangan" class="col-sm-2 control-label">Keterangan 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan" value="<?= set_value('keterangan', $presensi_catatan_pelajaran->keterangan); ?>">
                                <small class="info help-block">
                                <b>Input Keterangan</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jam_ke" class="col-sm-2 control-label">Jam Ke- 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="jam_ke" id="jam_ke" placeholder="Jam Ke-" value="<?= set_value('jam_ke', $presensi_catatan_pelajaran->jam_ke); ?>">
                                <small class="info help-block">
                                <b>Input Jam Ke</b> Max Length : 11.</small>
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
              window.location.href = BASE_URL + 'administrator/presensi_catatan_pelajaran';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_presensi_catatan_pelajaran = $('#form_presensi_catatan_pelajaran');
        var data_post = form_presensi_catatan_pelajaran.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_presensi_catatan_pelajaran.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#presensi_catatan_pelajaran_image_galery').find('li').attr('qq-file-id');
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