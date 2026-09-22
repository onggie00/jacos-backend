

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Ruang Ujian PSB FT        <small>Edit Ruang Ujian PSB FT</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/ujian_ruang_pendaftaran_ft'); ?>">Ruang Ujian PSB FT</a></li>
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
                            <h3 class="widget-user-username">Ruang Ujian PSB FT</h3>
                            <h5 class="widget-user-desc">Edit Ruang Ujian PSB FT</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/ujian_ruang_pendaftaran_ft/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_ujian_ruang_pendaftaran_ft', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_ujian_ruang_pendaftaran_ft', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="nama_ruang" class="col-sm-2 control-label">Nama Ruang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_ruang" id="nama_ruang" placeholder="Nama Ruang" value="<?= set_value('nama_ruang', $ujian_ruang_pendaftaran_ft->nama_ruang); ?>">
                                <small class="info help-block">
                                <b>Input Nama Ruang</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="judul_ujian" class="col-sm-2 control-label">Ujian 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="judul_ujian" id="judul_ujian" placeholder="Ujian" value="<?= set_value('judul_ujian', $ujian_ruang_pendaftaran_ft->judul_ujian); ?>">
                                <small class="info help-block">
                                <b>Input Judul Ujian</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="tahun_ajaran" id="tahun_ajaran" data-placeholder="Select Tahun Ajaran" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('tahun_ajaran_psb') as $row): ?>
                                    <option <?=  $row->label ==  $ujian_ruang_pendaftaran_ft->tahun_ajaran ? 'selected' : ''; ?> value="<?= $row->label ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Tahun Ajaran</b> Max Length : 20.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="kepala_sekolah" class="col-sm-2 control-label">Kepala Sekolah 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="kepala_sekolah" id="kepala_sekolah" data-placeholder="Select Kepala Sekolah" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('data_kepala_sekolah') as $row): ?>
                                    <option <?=  $row->nama_kepsek ==  $ujian_ruang_pendaftaran_ft->kepala_sekolah ? 'selected' : ''; ?> value="<?= $row->nama_kepsek ?>"><?= $row->nama_kepsek; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Kepala Sekolah</b> Max Length : 150.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="lokasi_ujian" class="col-sm-2 control-label">Lokasi Ujian
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="lokasi_ujian" id="lokasi_ujian" placeholder="Online / Labschool Cibubur" value="<?= set_value('lokasi_ujian', $ujian_ruang_pendaftaran_ft->lokasi_ujian); ?>">
                                <small class="info help-block">
                                    <b>Lokasi Ujian (Online / Labschool Cibubur)</b>
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="meeting_id" class="col-sm-2 control-label">Meeting ID (Zoom Code)
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="meeting_id" id="meeting_id" placeholder="123 456 789" value="<?= set_value('meeting_id', $ujian_ruang_pendaftaran_ft->meeting_id); ?>">
                                <small class="info help-block">
                                    <b>Input Meeting ID</b>
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="meeting_url" class="col-sm-2 control-label">Meeting URL
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="meeting_url" id="meeting_url" placeholder="https://zoom.us" value="<?= set_value('meeting_url', $ujian_ruang_pendaftaran_ft->meeting_url); ?>">
                                <small class="info help-block">
                                    <b>Input Meeting URL</b>
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="meeting_password" class="col-sm-2 control-label">Meeting Password
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="meeting_password" id="meeting_password" placeholder="Meeting Password (Zoom)" value="<?= set_value('meeting_password', $ujian_ruang_pendaftaran_ft->meeting_password); ?>">
                                <small class="info help-block">
                                    <b>Input Meeting Password</b>
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="maks_peserta" class="col-sm-2 control-label">Maksimal Peserta
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="maks_peserta" id="maks_peserta" placeholder="Maksimal Peserta" value="<?= set_value('maks_peserta', $ujian_ruang_pendaftaran_ft->maks_peserta); ?>">
                                <small class="info help-block">
                                    <b>Input Maks Peserta</b>
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
              window.location.href = BASE_URL + 'administrator/ujian_ruang_pendaftaran_ft';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_ujian_ruang_pendaftaran_ft = $('#form_ujian_ruang_pendaftaran_ft');
        var data_post = form_ujian_ruang_pendaftaran_ft.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_ujian_ruang_pendaftaran_ft.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#ujian_ruang_pendaftaran_ft_image_galery').find('li').attr('qq-file-id');
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