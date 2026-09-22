
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-plus-circle"></i> Program Anggaran
        <?php if (!empty($jenjang_context)): ?>
        <span class="jenjang-lock"><i class="fa fa-lock"></i> Jenjang: <?= strtoupper($jenjang_context); ?></span>
        <?php else: ?>
        <small><?= cclang('new', ['Program Anggaran']); ?> </small>
        <?php endif; ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/program_anggaran' . (!empty($jenjang_context) ? '/' . $jenjang_context : '')); ?>">Program Anggaran</a></li>
        <li class="active"><?= cclang('new'); ?></li>
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
                            <h5 class="widget-user-desc"><?= cclang('new', ['Program Anggaran']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', array(
                            'name'    => 'form_program_anggaran', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_program_anggaran', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            )); ?>
                         
                                                <div class="form-group ">
                            <label for="nomor_program" class="col-sm-2 control-label">Nomor Program 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomor_program" id="nomor_program" placeholder="Nomor Program" value="<?= set_value('nomor_program'); ?>">
                                <small class="info help-block">
                                <b>Input Nomor Program</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nama_program" class="col-sm-2 control-label">Nama Program 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_program" id="nama_program" placeholder="Nama Program" value="<?= set_value('nama_program'); ?>">
                                <small class="info help-block">
                                <b>Input Nama Program</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tahun_ajaran" id="tahun_ajaran" placeholder="Tahun Ajaran" value="<?= set_value('tahun_ajaran'); ?>">
                                <small class="info help-block">
                                <b>Input Tahun Ajaran</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jenis_kegiatan" class="col-sm-2 control-label">Jenis Kegiatan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="jenis_kegiatan" id="jenis_kegiatan" placeholder="Jenis Kegiatan" value="<?= set_value('jenis_kegiatan'); ?>">
                                <small class="info help-block">
                                <b>Input Jenis Kegiatan</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nominal_okr" class="col-sm-2 control-label">Nominal OKR 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="nominal_okr" id="nominal_okr" placeholder="Nominal OKR" value="<?= set_value('nominal_okr'); ?>">
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
                                    <?php $selected_jenjang = set_value('jenjang', 'SMA'); ?>
                                    <select class="form-control chosen chosen-select" name="jenjang" id="jenjang" data-placeholder="Select Jenjang">
                                        <option value=""></option>
                                        <option value="SMA" <?= ($selected_jenjang === 'SMA') ? 'selected="selected"' : ''; ?>>SMA</option>
                                        <option value="FT" <?= ($selected_jenjang === 'FT') ? 'selected="selected"' : ''; ?>>FRANCE TRACK</option>
                                    </select>
                                    <small class="info help-block">Admin SMA dapat memilih data untuk SMA atau FT.</small>
                                <?php elseif (!empty($jenjang_context)): ?>
                                    <input type="hidden" name="jenjang" id="jenjang" value="<?= strtoupper($jenjang_context); ?>">
                                    <input type="text" class="form-control" value="<?= strtoupper($jenjang_context); ?>" disabled="disabled">
                                    <small class="info help-block">Jenjang terkunci sesuai akses URL.</small>
                                <?php else: ?>
                                <select  class="form-control chosen chosen-select" name="jenjang" id="jenjang" data-placeholder="Select Jenjang" >
                                    <option value=""></option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="FT">FRANCE TRACK</option>
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
                                    <option value="0">Tidak Aktif</option>
                                    <option value="1">Aktif</option>
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
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
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
          url: BASE_URL + '/administrator/program_anggaran/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('.steps li').removeClass('error');
          $('form').find('.error-input').remove();
          if(res.success) {
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
                
          } else {
            if (res.errors) {
                
                $.each(res.errors, function(index, val) {
                    $('form #'+index).parents('.form-group').addClass('has-error');
                    $('form #'+index).parents('.form-group').find('small').prepend(`
                      <div class="error-input">`+val+`</div>
                      `);
                });
                $('.steps li').removeClass('error');
                $('.content section').each(function(index, el) {
                    if ($(this).find('.has-error').length) {
                        $('.steps li:eq('+index+')').addClass('error').find('a').trigger('click');
                    }
                });
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
      
       
 
       

      
    
    
    }); /*end doc ready*/
</script>