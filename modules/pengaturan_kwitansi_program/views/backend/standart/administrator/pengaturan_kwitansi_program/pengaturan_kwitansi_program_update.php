

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Pengaturan Kwitansi Program        <small>Edit Pengaturan Kwitansi Program</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/pengaturan_kwitansi_program'); ?>">Pengaturan Kwitansi Program</a></li>
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
                            <h3 class="widget-user-username">Pengaturan Kwitansi Program</h3>
                            <h5 class="widget-user-desc">Edit Pengaturan Kwitansi Program</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/pengaturan_kwitansi_program/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_pengaturan_kwitansi_program', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_pengaturan_kwitansi_program', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="kepala_sekolah_nama" class="col-sm-2 control-label">Kepala Sekolah (Nama) 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kepala_sekolah_nama" id="kepala_sekolah_nama" placeholder="Kepala Sekolah (Nama)" value="<?= set_value('kepala_sekolah_nama', $pengaturan_kwitansi_program->kepala_sekolah_nama); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="kepala_sekolah_npp" class="col-sm-2 control-label">Kepala Sekolah (NPP) 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kepala_sekolah_npp" id="kepala_sekolah_npp" placeholder="Kepala Sekolah (NPP)" value="<?= set_value('kepala_sekolah_npp', $pengaturan_kwitansi_program->kepala_sekolah_npp); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="kepala_tu_nama" class="col-sm-2 control-label">Kepala TU (Nama) 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kepala_tu_nama" id="kepala_tu_nama" placeholder="Kepala TU (Nama)" value="<?= set_value('kepala_tu_nama', $pengaturan_kwitansi_program->kepala_tu_nama); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="kepala_tu_npp" class="col-sm-2 control-label">Kepala TU (NPP) 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kepala_tu_npp" id="kepala_tu_npp" placeholder="Kepala TU (NPP)" value="<?= set_value('kepala_tu_npp', $pengaturan_kwitansi_program->kepala_tu_npp); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="kepala_sekretariat_nama" class="col-sm-2 control-label">Kepala Sekretariat (Nama) 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kepala_sekretariat_nama" id="kepala_sekretariat_nama" placeholder="Kepala Sekretariat (Nama)" value="<?= set_value('kepala_sekretariat_nama', $pengaturan_kwitansi_program->kepala_sekretariat_nama); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="kepala_sekretariat_npp" class="col-sm-2 control-label">Kepala Sekretariat (NPP) 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kepala_sekretariat_npp" id="kepala_sekretariat_npp" placeholder="Kepala Sekretariat (NPP)" value="<?= set_value('kepala_sekretariat_npp', $pengaturan_kwitansi_program->kepala_sekretariat_npp); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="jenjang" id="jenjang" data-placeholder="Select Jenjang" >
                                    <option value=""></option>
                                    <option <?= $pengaturan_kwitansi_program->jenjang == "SD" ? 'selected' :''; ?> value="SD">SD</option>
                                    <option <?= $pengaturan_kwitansi_program->jenjang == "SMP" ? 'selected' :''; ?> value="SMP">SMP</option>
                                    <option <?= $pengaturan_kwitansi_program->jenjang == "SMA" ? 'selected' :''; ?> value="SMA">SMA</option>
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
              window.location.href = BASE_URL + 'administrator/pengaturan_kwitansi_program';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_pengaturan_kwitansi_program = $('#form_pengaturan_kwitansi_program');
        var data_post = form_pengaturan_kwitansi_program.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_pengaturan_kwitansi_program.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#pengaturan_kwitansi_program_image_galery').find('li').attr('qq-file-id');
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