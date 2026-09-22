

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Acara Presensi        <small>Edit Acara Presensi</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/acara_presensi'); ?>">Acara Presensi</a></li>
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
                            <h3 class="widget-user-username">Acara Presensi</h3>
                            <h5 class="widget-user-desc">Edit Acara Presensi</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/acara_presensi/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_acara_presensi', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_acara_presensi', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_acara" class="col-sm-2 control-label">Acara 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_acara" id="id_acara" data-placeholder="Select Acara" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('acara') as $row): ?>
                                    <option <?=  $row->id_acara ==  $acara_presensi->id_acara ? 'selected' : ''; ?> value="<?= $row->id_acara ?>"><?= $row->nama_acara; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Acara</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="npp" class="col-sm-2 control-label">NPP 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npp" id="npp" placeholder="NPP" value="<?= set_value('npp', $acara_presensi->npp); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="peserta" class="col-sm-2 control-label">Peserta 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="peserta" id="peserta" placeholder="Peserta" value="<?= set_value('peserta', $acara_presensi->peserta); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="role" class="col-sm-2 control-label">Role 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="role" id="role" data-placeholder="Select Role" >
                                    <option value=""></option>
                                    <option <?= $acara_presensi->role == "pegawai" ? 'selected' :''; ?> value="pegawai">Pegawai</option>
                                    <option <?= $acara_presensi->role == "pimpinan" ? 'selected' :''; ?> value="pimpinan">Pimpinan</option>
                                    <option <?= $acara_presensi->role == "guru_sd" ? 'selected' :''; ?> value="guru_sd">Guru SD</option>
                                    <option <?= $acara_presensi->role == "guru_smp" ? 'selected' :''; ?> value="guru_smp">Guru SMP</option>
                                    <option <?= $acara_presensi->role == "guru_sma" ? 'selected' :''; ?> value="guru_sma">Guru SMA</option>
                                    <option <?= $acara_presensi->role == "guru_ft" ? 'selected' :''; ?> value="guru_ft">Guru FT</option>
                                    <option <?= $acara_presensi->role == "narasumber" ? 'selected' :''; ?> value="narasumber">Narasumber</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="role_lainnya" class="col-sm-2 control-label">Lainnya 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="role_lainnya" id="role_lainnya" placeholder="Lainnya" value="<?= set_value('role_lainnya', $acara_presensi->role_lainnya); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="waktu_presensi" class="col-sm-2 control-label">Waktu Presensi 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="waktu_presensi"  placeholder="Waktu Presensi" id="waktu_presensi" value="<?= set_value('waktu_presensi', $acara_presensi->waktu_presensi); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="waktu_presensi_selesai" class="col-sm-2 control-label">Waktu Presensi Selesai 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="waktu_presensi_selesai"  placeholder="Waktu Presensi Selesai" id="waktu_presensi_selesai" value="<?= set_value('waktu_presensi_selesai', $acara_presensi->waktu_presensi_selesai); ?>">
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
              window.location.href = BASE_URL + 'administrator/acara_presensi';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_acara_presensi = $('#form_acara_presensi');
        var data_post = form_acara_presensi.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_acara_presensi.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#acara_presensi_image_galery').find('li').attr('qq-file-id');
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