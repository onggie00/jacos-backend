
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        TKA Guru SMP        <small><?= cclang('new', ['TKA Guru SMP']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/laporan_tka_guru_smp'); ?>">TKA Guru SMP</a></li>
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
                            <h3 class="widget-user-username">TKA Guru SMP</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['TKA Guru SMP']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_laporan_tka_guru_smp', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_laporan_tka_guru_smp', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_guru" class="col-sm-2 control-label">Guru 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_guru" id="id_guru" data-placeholder="Select Guru" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('guru_smp') as $row): ?>
                                    <option value="<?= $row->id_guru ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Guru</b> Max Length : 11.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="unit_kerja" class="col-sm-2 control-label">Unit Kerja 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="unit_kerja" id="unit_kerja" placeholder="Unit Kerja" value="<?= set_value('unit_kerja'); ?>">
                                <small class="info help-block">
                                <b>Input Unit Kerja</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="umur" class="col-sm-2 control-label">Umur 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="umur" id="umur" placeholder="Umur" value="<?= set_value('umur'); ?>">
                                <small class="info help-block">
                                <b>Input Umur</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="masa_kerja" class="col-sm-2 control-label">Masa Kerja 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="masa_kerja" id="masa_kerja" placeholder="Masa Kerja" value="<?= set_value('masa_kerja'); ?>">
                                <small class="info help-block">
                                <b>Input Masa Kerja</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="golongan" class="col-sm-2 control-label">Golongan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="golongan" id="golongan" placeholder="Golongan" value="<?= set_value('golongan'); ?>">
                                <small class="info help-block">
                                <b>Input Golongan</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="mata_pelajaran" class="col-sm-2 control-label">Mata Pelajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="mata_pelajaran" id="mata_pelajaran" placeholder="Mata Pelajaran" value="<?= set_value('mata_pelajaran'); ?>">
                                <small class="info help-block">
                                <b>Input Mata Pelajaran</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="bhs_indonesia" class="col-sm-2 control-label">Bahasa Indonesia 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bhs_indonesia" id="bhs_indonesia" placeholder="Bahasa Indonesia" value="<?= set_value('bhs_indonesia'); ?>">
                                <small class="info help-block">
                                <b>Input Bhs Indonesia</b> Max Length : 5.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="bhs_inggris" class="col-sm-2 control-label">Bahasa Inggris 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bhs_inggris" id="bhs_inggris" placeholder="Bahasa Inggris" value="<?= set_value('bhs_inggris'); ?>">
                                <small class="info help-block">
                                <b>Input Bhs Inggris</b> Max Length : 5.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="numerasi" class="col-sm-2 control-label">Numerasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="numerasi" id="numerasi" placeholder="Numerasi" value="<?= set_value('numerasi'); ?>">
                                <small class="info help-block">
                                <b>Input Numerasi</b> Max Length : 5.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="total_skor" class="col-sm-2 control-label">Total Skor 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="total_skor" id="total_skor" placeholder="Total Skor" value="<?= set_value('total_skor'); ?>">
                                <small class="info help-block">
                                <b>Input Total Skor</b> Max Length : 5.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tahun" class="col-sm-2 control-label">Tahun 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tahun" id="tahun" placeholder="Tahun" value="<?= set_value('tahun'); ?>">
                                <small class="info help-block">
                                <b>Input Tahun</b> Max Length : 50.</small>
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
              window.location.href = BASE_URL + 'administrator/laporan_tka_guru_smp';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_laporan_tka_guru_smp = $('#form_laporan_tka_guru_smp');
        var data_post = form_laporan_tka_guru_smp.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/laporan_tka_guru_smp/add_save',
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