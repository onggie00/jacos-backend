

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Jadwal Ujian SD        <small>Edit Jadwal Ujian SD</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/jadwal_ujian_sd'); ?>">Jadwal Ujian SD</a></li>
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
                            <h3 class="widget-user-username">Jadwal Ujian SD</h3>
                            <h5 class="widget-user-desc">Edit Jadwal Ujian SD</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/jadwal_ujian_sd/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_jadwal_ujian_sd', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_jadwal_ujian_sd', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_mapel" class="col-sm-2 control-label">Mata Pelajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_mapel" id="id_mapel" data-placeholder="Select Mata Pelajaran" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('ujian_mapel_sd') as $row): ?>
                                    <option <?=  $row->id_mapel ==  $jadwal_ujian_sd->id_mapel ? 'selected' : ''; ?> value="<?= $row->id_mapel ?>"><?= $row->nama_mapel; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Mapel</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="id_jenis_ujian" class="col-sm-2 control-label">Jenis Ujian 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_jenis_ujian" id="id_jenis_ujian" data-placeholder="Select Jenis Ujian" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('jenis_ujian') as $row): ?>
                                    <option <?=  $row->id_jenis_ujian ==  $jadwal_ujian_sd->id_jenis_ujian ? 'selected' : ''; ?> value="<?= $row->id_jenis_ujian ?>"><?= $row->nama_ujian; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Jenis Ujian</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="id_tingkatan" class="col-sm-2 control-label">Tingkatan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_tingkatan" id="id_tingkatan" data-placeholder="Select Tingkatan" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('tingkatan_sd') as $row): ?>
                                    <option <?=  $row->id_tingkatan_sd ==  $jadwal_ujian_sd->id_tingkatan ? 'selected' : ''; ?> value="<?= $row->id_tingkatan_sd ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Tingkatan</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="daftar_kelas" class="col-sm-2 control-label">Daftar Kelas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="daftar_kelas[]" id="daftar_kelas" data-placeholder="Select Daftar Kelas" multiple >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('kelas_sd') as $row): ?>
                                    <option <?=  in_array($row->label, explode(',', $jadwal_ujian_sd->daftar_kelas)) ? 'selected' : ''; ?> value="<?= $row->label ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tanggal" class="col-sm-2 control-label">Tanggal Ujian 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="tanggal"  placeholder="Tanggal Ujian" id="tanggal" value="<?= set_value('jadwal_ujian_sd_tanggal_name', $jadwal_ujian_sd->tanggal); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                       
                                                 
                                                <div class="form-group ">
                            <label for="hari" class="col-sm-2 control-label">Hari 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="hari" id="hari" placeholder="Hari" value="<?= set_value('hari', $jadwal_ujian_sd->hari); ?>">
                                <small class="info help-block">
                                <b>Input Hari</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jam_mulai" class="col-sm-2 control-label">Jam Mulai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="jam_mulai" id="jam_mulai" value="<?= set_value('jadwal_ujian_sd_jam_mulai_name', $jadwal_ujian_sd->jam_mulai); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jam_selesai" class="col-sm-2 control-label">Jam Selesai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="jam_selesai" id="jam_selesai" value="<?= set_value('jadwal_ujian_sd_jam_selesai_name', $jadwal_ujian_sd->jam_selesai); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_tahun_ajaran" id="id_tahun_ajaran" data-placeholder="Select Tahun Ajaran" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('tahun_ajaran') as $row): ?>
                                    <option <?=  $row->id_tahun_ajaran ==  $jadwal_ujian_sd->id_tahun_ajaran ? 'selected' : ''; ?> value="<?= $row->id_tahun_ajaran ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Tahun Ajaran</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="semester" class="col-sm-2 control-label">Semester 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="semester" id="semester" data-placeholder="Select Semester" >
                                    <option value=""></option>
                                    <option <?= $jadwal_ujian_sd->semester == "1" ? 'selected' :''; ?> value="1">1</option>
                                    <option <?= $jadwal_ujian_sd->semester == "2" ? 'selected' :''; ?> value="2">2</option>
                                    </select>
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
              window.location.href = BASE_URL + 'administrator/jadwal_ujian_sd';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_jadwal_ujian_sd = $('#form_jadwal_ujian_sd');
        var data_post = form_jadwal_ujian_sd.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_jadwal_ujian_sd.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#jadwal_ujian_sd_image_galery').find('li').attr('qq-file-id');
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