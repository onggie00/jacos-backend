
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Pengaturan Diskon SPP        <small><?= cclang('new', ['Pengaturan Diskon SPP']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/pengaturan_diskon_spp'); ?>">Pengaturan Diskon SPP</a></li>
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
                            <h3 class="widget-user-username">Pengaturan Diskon SPP</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Pengaturan Diskon SPP']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_pengaturan_diskon_spp', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_pengaturan_diskon_spp', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="jenjang" id="jenjang">
                                    <option value="">-- Pilih jenjang --</option>
                                    <?php foreach ($jenjangs as $val => $label): ?>
                                    <option value="<?= $val; ?>" <?= set_select('jenjang', $val); ?>><?= $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                <b>Jenjang tagihan SPP</b> yang berlaku pengaturan ini.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_tingkatan" class="col-sm-2 control-label">Tingkatan</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="id_tingkatan" id="id_tingkatan">
                                    <option value="">Semua tingkatan (default)</option>
                                    <?php foreach ($tingkatans as $ting): ?>
                                    <option value="<?= $ting->id_tingkatan; ?>" <?= set_select('id_tingkatan', $ting->id_tingkatan); ?>><?= _ent($ting->label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                <b>Kosong</b> = berlaku untuk semua tingkatan di jenjang tsb; pilih tingkatan utk override khusus (mis. tarif beasiswa).</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="min_pembayaran" class="col-sm-2 control-label">Minimal Bulan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="min_pembayaran" id="min_pembayaran" placeholder="12" value="<?= set_value('min_pembayaran', 12); ?>">
                                <small class="info help-block">
                                <b>Jumlah minimal bulan</b> tagihan digabung dalam 1 VA agar diskon berlaku (12 bulan = 1 TA).</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="persen_diskon" class="col-sm-2 control-label">Persen Diskon (%)</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="persen_diskon" id="persen_diskon" placeholder="3" value="<?= set_value('persen_diskon', 3); ?>">
                                <small class="info help-block">
                                <b>Potongan persen</b> dari total nominal tagihan (bukan kelipatan bulan). Nominal persis (tanpa pembulatan).</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="aktif" class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="aktif" id="aktif">
                                    <option value="1" <?= set_select('aktif', '1', true); ?>>Aktif</option>
                                    <option value="0" <?= set_select('aktif', '0'); ?>>Nonaktif</option>
                                </select>
                                <small class="info help-block">
                                <b>Nonaktif</b> = pengaturan diabaikan saat hitung diskon.</small>
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

      // reload opsi tingkatan saat jenjang berubah
      $('#jenjang').change(function(){
        var j = $(this).val();
        var cur = $('#id_tingkatan').val();
        $.post(BASE_URL + 'administrator/pengaturan_diskon_spp/tingkatan_options', {jenjang: j}, function(res){
          var sel = $('#id_tingkatan');
          sel.find('option:not(:first)').remove();
          if (res && res.success) {
            $.each(res.options, function(i, o){
              sel.append($('<option/>').val(o.id_tingkatan).text(o.label));
            });
          }
          sel.val('');
        }, 'json');
      });
                          
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
              window.location.href = BASE_URL + 'administrator/pengaturan_diskon_spp';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_pengaturan_diskon_spp = $('#form_pengaturan_diskon_spp');
        var data_post = form_pengaturan_diskon_spp.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/pengaturan_diskon_spp/add_save',
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
