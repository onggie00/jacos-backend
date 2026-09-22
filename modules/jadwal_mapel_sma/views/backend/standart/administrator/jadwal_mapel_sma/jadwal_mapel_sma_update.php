

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Jadwal Mapel Sma        <small>Edit Jadwal Mapel Sma</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/jadwal_mapel_sma'); ?>">Jadwal Mapel Sma</a></li>
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
                            <h3 class="widget-user-username">Jadwal Mapel Sma</h3>
                            <h5 class="widget-user-desc">Edit Jadwal Mapel Sma</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/jadwal_mapel_sma/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_jadwal_mapel_sma', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_jadwal_mapel_sma', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="jam_mulai" class="col-sm-2 control-label">Jam Mulai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="jam_mulai" id="jam_mulai" value="<?= set_value('jadwal_mapel_sma_jam_mulai_name', $jadwal_mapel_sma->jam_mulai); ?>">
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
                              <input type="text" class="form-control pull-right timepicker" name="jam_selesai" id="jam_selesai" value="<?= set_value('jadwal_mapel_sma_jam_selesai_name', $jadwal_mapel_sma->jam_selesai); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group  wrapper-options-crud">
                            <label for="hari" class="col-sm-2 control-label">Hari 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $jadwal_mapel_sma->hari == "senin" ? "checked" : ""; ?> type="radio" class="flat-red" name="hari" value="senin"> Senin                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $jadwal_mapel_sma->hari == "selasa" ? "checked" : ""; ?> type="radio" class="flat-red" name="hari" value="selasa"> Selasa                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $jadwal_mapel_sma->hari == "rabu" ? "checked" : ""; ?> type="radio" class="flat-red" name="hari" value="rabu"> Rabu                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $jadwal_mapel_sma->hari == "kamis" ? "checked" : ""; ?> type="radio" class="flat-red" name="hari" value="kamis"> Kamis                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $jadwal_mapel_sma->hari == "jumat" ? "checked" : ""; ?> type="radio" class="flat-red" name="hari" value="jumat"> Jumat                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $jadwal_mapel_sma->hari == "sabtu" ? "checked" : ""; ?> type="radio" class="flat-red" name="hari" value="sabtu"> Sabtu                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $jadwal_mapel_sma->hari == "minggu" ? "checked" : ""; ?> type="radio" class="flat-red" name="hari" value="minggu"> Minggu                                    </label>
                                    </div>
                                    </select>
                                <div class="row-fluid clear-both">
                                <small class="info help-block">
                                </small>
                                </div>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_kelas" class="col-sm-2 control-label">Id Kelas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_kelas" id="id_kelas" data-placeholder="Select Id Kelas" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('kelas_sma') as $row): ?>
                                    <option <?=  $row->id_kelas_sma ==  $jadwal_mapel_sma->id_kelas ? 'selected' : ''; ?> value="<?= $row->id_kelas_sma ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Kelas</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="id_mapel" class="col-sm-2 control-label">Id Mapel 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_mapel" id="id_mapel" data-placeholder="Select Id Mapel" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('mata_pelajaran_sma') as $row): ?>
                                    <option <?=  $row->id_mapel ==  $jadwal_mapel_sma->id_mapel ? 'selected' : ''; ?> value="<?= $row->id_mapel ?>"><?= $row->nama_mapel; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Mapel</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="id_guru" class="col-sm-2 control-label">Id Guru 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_guru" id="id_guru" data-placeholder="Select Id Guru" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('guru_sma') as $row): ?>
                                    <option <?=  $row->id_guru ==  $jadwal_mapel_sma->id_guru ? 'selected' : ''; ?> value="<?= $row->id_guru ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Guru</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="ruang_kelas" class="col-sm-2 control-label">Ruang Kelas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="ruang_kelas" id="ruang_kelas" placeholder="Ruang Kelas" value="<?= set_value('ruang_kelas', $jadwal_mapel_sma->ruang_kelas); ?>">
                                <small class="info help-block">
                                <b>Input Ruang Kelas</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_tahun_ajaran" class="col-sm-2 control-label">Id Tahun Ajaran 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_tahun_ajaran" id="id_tahun_ajaran" data-placeholder="Select Id Tahun Ajaran" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('tahun_ajaran') as $row): ?>
                                    <option <?=  $row->id_tahun_ajaran ==  $jadwal_mapel_sma->id_tahun_ajaran ? 'selected' : ''; ?> value="<?= $row->id_tahun_ajaran ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>  
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
              window.location.href = BASE_URL + 'administrator/jadwal_mapel_sma';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_jadwal_mapel_sma = $('#form_jadwal_mapel_sma');
        var data_post = form_jadwal_mapel_sma.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_jadwal_mapel_sma.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#jadwal_mapel_sma_image_galery').find('li').attr('qq-file-id');
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