

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-edit"></i> Program Anggaran
        <?php if (!empty($jenjang_context)): ?>
        <span class="jenjang-lock"><i class="fa fa-lock"></i> Jenjang: <?= strtoupper($jenjang_context); ?></span>
        <?php else: ?>
        <small>Edit Program Anggaran</small>
        <?php endif; ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/program_anggaran' . (!empty($jenjang_context) ? '/' . $jenjang_context : '')); ?>">Program Anggaran</a></li>
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
                            <h3 class="widget-user-username">Program Anggaran</h3>
                            <h5 class="widget-user-desc">Edit Program Anggaran</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/program_anggaran/edit_save/'.$this->uri->segment(4)), array(
                            'name'    => 'form_program_anggaran', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_program_anggaran', 
                            'method'  => 'POST'
                            )); ?>
                         
                                                <div class="form-group ">
                            <label for="nomor_program" class="col-sm-2 control-label">Nomor Program 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomor_program" id="nomor_program" placeholder="Nomor Program" value="<?= set_value('nomor_program', $program_anggaran->nomor_program); ?>">
                                <small class="info help-block">
                                <b>Input Nomor Program</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nama_program" class="col-sm-2 control-label">Nama Program 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_program" id="nama_program" placeholder="Nama Program" value="<?= set_value('nama_program', $program_anggaran->nama_program); ?>">
                                <small class="info help-block">
                                <b>Input Nama Program</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tahun_ajaran" id="tahun_ajaran" placeholder="Tahun Ajaran" value="<?= set_value('tahun_ajaran', $program_anggaran->tahun_ajaran); ?>">
                                <small class="info help-block">
                                <b>Input Tahun Ajaran</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jenis_kegiatan" class="col-sm-2 control-label">Jenis Kegiatan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="jenis_kegiatan" id="jenis_kegiatan" placeholder="Jenis Kegiatan" value="<?= set_value('jenis_kegiatan', $program_anggaran->jenis_kegiatan); ?>">
                                <small class="info help-block">
                                <b>Input Jenis Kegiatan</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nominal_okr" class="col-sm-2 control-label">Nominal OKR 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="nominal_okr" id="nominal_okr" placeholder="Nominal OKR" value="<?= set_value('nominal_okr', $program_anggaran->nominal_okr); ?>">
                                <small class="info help-block">
                                <b>Input Nominal Okr</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <?php if (!empty($jenjang_context) && strtolower($jenjang_context) === 'sma'): ?>
                                    <select class="form-control chosen chosen-select" name="jenjang" id="jenjang" data-placeholder="Select Jenjang">
                                        <option value=""></option>
                                        <option value="SMA" <?= $program_anggaran->jenjang == "SMA" ? 'selected="selected"' : ''; ?>>SMA</option>
                                        <option value="FT" <?= $program_anggaran->jenjang == "FT" ? 'selected="selected"' : ''; ?>>FRANCE TRACK</option>
                                    </select>
                                    <small class="info help-block">Admin SMA dapat memilih data untuk SMA atau FT.</small>
                                <?php elseif (!empty($jenjang_context)): ?>
                                    <input type="hidden" name="jenjang" id="jenjang" value="<?= strtoupper($jenjang_context); ?>">
                                    <input type="text" class="form-control" value="<?= strtoupper($jenjang_context); ?>" disabled="disabled">
                                    <small class="info help-block">Jenjang terkunci sesuai akses URL.</small>
                                <?php else: ?>
                                <select  class="form-control chosen chosen-select" name="jenjang" id="jenjang" data-placeholder="Select Jenjang" >
                                    <option value=""></option>
                                    <option <?= $program_anggaran->jenjang == "SD" ? 'selected' :''; ?> value="SD">SD</option>
                                    <option <?= $program_anggaran->jenjang == "SMP" ? 'selected' :''; ?> value="SMP">SMP</option>
                                    <option <?= $program_anggaran->jenjang == "SMA" ? 'selected' :''; ?> value="SMA">SMA</option>
                                    <option <?= $program_anggaran->jenjang == "FT" ? 'selected' :''; ?> value="FT">FRANCE TRACK</option>
                                    </select>
                                <?php endif; ?>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="is_active" class="col-sm-2 control-label">Aktif? 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="is_active" id="is_active" data-placeholder="Select Aktif?" >
                                    <option value=""></option>
                                    <option <?= $program_anggaran->is_active == "0" ? 'selected' :''; ?> value="0">Tidak Aktif</option>
                                    <option <?= $program_anggaran->is_active == "1" ? 'selected' :''; ?> value="1">Aktif</option>
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
              window.location.href = BASE_URL + 'administrator/program_anggaran<?= !empty($jenjang_context) ? '/' . $jenjang_context : ''; ?>';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_program_anggaran = $('#form_program_anggaran');
        var data_post = form_program_anggaran.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_program_anggaran.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#program_anggaran_image_galery').find('li').attr('qq-file-id');
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