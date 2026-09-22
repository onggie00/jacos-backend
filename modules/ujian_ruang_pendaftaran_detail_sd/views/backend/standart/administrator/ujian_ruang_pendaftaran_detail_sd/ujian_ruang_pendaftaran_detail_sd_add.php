
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Detail Peserta Ujian PSB SD        <small><?= cclang('new', ['Detail Peserta Ujian PSB SD']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/ujian_ruang_pendaftaran_detail_sd'); ?>">Detail Peserta Ujian PSB SD</a></li>
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
                            <h3 class="widget-user-username">Detail Peserta Ujian PSB SD</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Detail Peserta Ujian PSB SD']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_ujian_ruang_pendaftaran_detail_sd', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_ujian_ruang_pendaftaran_detail_sd', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_ruang_pendaftaran" class="col-sm-2 control-label">Ruang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_ruang_pendaftaran" id="id_ruang_pendaftaran" data-placeholder="Select Ruang" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('ujian_ruang_pendaftaran_sd') as $row): ?>
                                    <option value="<?= $row->id_ruang_pendaftaran ?>"><?= $row->nama_ruang; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Ruang Pendaftaran</b> Max Length : 11.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nomor_awal" class="col-sm-2 control-label">Nomor Peserta Awal
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomor_awal" id="nomor_awal" placeholder="Nomor Peserta Awal" value="<?= set_value('nomor_awal'); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nomor_akhir" class="col-sm-2 control-label">Nomor Peserta Akhir
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomor_akhir" id="nomor_akhir" placeholder="Nomor Peserta Akhir" value="<?= set_value('nomor_akhir'); ?>" required>
                            </div>
                        </div>
                        
                                                <div class="form-group hidden">
                            <label for="nomor_peserta" class="col-sm-2 control-label">Nomor Peserta 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select multiple class="form-control chosen chosen-select-deselect" name="nomor_peserta[]" id="nomor_peserta" data-placeholder="Select Nomor Peserta" >
                                    <option value=""></option>
                                    <?php foreach ($this->mymodel->withquery("select no_peserta, nama_lengkap from siswa_sd where no_peserta != '' order by no_peserta ASC", "result") as $row): ?>
                                    <option value="<?= $row->no_peserta ?>"><?= $row->no_peserta.' - '.$row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Nomor Peserta</b> Max Length : 30.</small>
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
              window.location.href = BASE_URL + 'administrator/ujian_ruang_pendaftaran_detail_sd';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_ujian_ruang_pendaftaran_detail_sd = $('#form_ujian_ruang_pendaftaran_detail_sd');
        var data_post = form_ujian_ruang_pendaftaran_detail_sd.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/ujian_ruang_pendaftaran_detail_sd/add_save',
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