

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Status Daftar Ulang Smp        <small>Edit Status Daftar Ulang Smp</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/status_daftar_ulang_smp'); ?>">Status Daftar Ulang Smp</a></li>
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
                            <h3 class="widget-user-username">Status Daftar Ulang Smp</h3>
                            <h5 class="widget-user-desc">Edit Status Daftar Ulang Smp</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/status_daftar_ulang_smp/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_status_daftar_ulang_smp', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_status_daftar_ulang_smp', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group  wrapper-options-crud">
                            <label for="status" class="col-sm-2 control-label">Status 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $status_daftar_ulang_smp->status == "0" ? "checked" : ""; ?> type="radio" class="flat-red" name="status" value="0"> Menunggu Aktivasi                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $status_daftar_ulang_smp->status == "1" ? "checked" : ""; ?> type="radio" class="flat-red" name="status" value="1"> Aktivasi Virtual Account                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $status_daftar_ulang_smp->status == "2" ? "checked" : ""; ?> type="radio" class="flat-red" name="status" value="2"> Pembayaran Berhasil                                    </label>
                                    </div>
                                    </select>
                                    <div class="row-fluid clear-both">
                                <small class="info help-block text-danger">
                                *Saat mengaktifkan virtual account maka siswa akan mendapatkan slip_pembayaran yang dapat dilihat pada <b>Cek Data</b> dan Email siswa</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group  wrapper-options-crud">
                            <label for="status" class="col-sm-2 control-label">Custom Total Biaya (Optional)
                            </label>
                            <div class="col-sm-8">
                                <input type="number" name="custom_total_payment" value="<?= ($status_daftar_ulang_smp->custom_payment)?$status_daftar_ulang_smp->custom_payment:0;?>">
                                <div class="row-fluid clear-both">
                                <small class="info help-block text-danger">
                                *Isi jika ada perubahan total biaya, jika tidak bisa dikosongi</small>
                                </div>
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
              window.location.href = BASE_URL + 'administrator/status_daftar_ulang_smp';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_status_daftar_ulang_smp = $('#form_status_daftar_ulang_smp');
        var data_post = form_status_daftar_ulang_smp.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_status_daftar_ulang_smp.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#status_daftar_ulang_smp_image_galery').find('li').attr('qq-file-id');
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