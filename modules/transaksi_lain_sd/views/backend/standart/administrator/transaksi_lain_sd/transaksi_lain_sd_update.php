

<!-- Fine Uploader Gallery CSS file
    ====================================================================== -->
<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<!-- Fine Uploader jQuery JS file
    ====================================================================== -->
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        List Tagihan SD        <small>Edit List Tagihan SD</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/transaksi_lain_sd'); ?>">List Tagihan SD</a></li>
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
                            <h3 class="widget-user-username">List Tagihan SD</h3>
                            <h5 class="widget-user-desc">Edit List Tagihan SD</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/transaksi_lain_sd/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_transaksi_lain_sd', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_transaksi_lain_sd', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_siswa_aktif" class="col-sm-2 control-label">Siswa Aktif 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_siswa_aktif" id="id_siswa_aktif" data-placeholder="Select Siswa Aktif" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_sd_aktif') as $row): ?>
                                    <option <?=  $row->id_siswa_sd_aktif ==  $transaksi_lain_sd->id_siswa_aktif ? 'selected' : ''; ?> value="<?= $row->id_siswa_sd_aktif ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Siswa Aktif</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="id_transaksi_lain" class="col-sm-2 control-label">Tagihan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_transaksi_lain" id="id_transaksi_lain" data-placeholder="Select Tagihan" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('transaksi_lain_sd_manajemen') as $row): ?>
                                    <option <?=  $row->id ==  $transaksi_lain_sd->id_transaksi_lain ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->nama_transaksi; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Transaksi Lain</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="tanggal_bayar" class="col-sm-2 control-label">Tanggal Pembayaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="tanggal_bayar"  placeholder="Tanggal Pembayaran" id="tanggal_bayar" value="<?= set_value('tanggal_bayar', $transaksi_lain_sd->tanggal_bayar); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="va_number" class="col-sm-2 control-label">VA Number 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="va_number" id="va_number" placeholder="VA Number" value="<?= set_value('va_number', $transaksi_lain_sd->va_number); ?>">
                                <small class="info help-block">
                                <b>Input Va Number</b> Max Length : 30.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="kode_tagihan" class="col-sm-2 control-label">Nomor Tagihan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kode_tagihan" id="kode_tagihan" placeholder="Nomor Tagihan" value="<?= set_value('kode_tagihan', $transaksi_lain_sd->kode_tagihan); ?>">
                                <small class="info help-block">
                                <b>Input Kode Tagihan</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                        
                                                  
                                                <div class="form-group ">
                            <label for="nominal_bayar" class="col-sm-2 control-label">Nominal Bayar 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="nominal_bayar" id="nominal_bayar" placeholder="Nominal Bayar" value="<?= set_value('nominal_bayar', $transaksi_lain_sd->nominal_bayar); ?>">
                                <small class="info help-block">
                                <b>Input Nominal Bayar</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="status_transaksi" class="col-sm-2 control-label">Status Transaksi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="status_transaksi" id="status_transaksi" data-placeholder="Select Status Transaksi" >
                                    <option value=""></option>
                                    <option <?= $transaksi_lain_sd->status_transaksi == "0" ? 'selected' :''; ?> value="0">Belum Dibayar</option>
                                    <option <?= $transaksi_lain_sd->status_transaksi == "1" ? 'selected' :''; ?> value="1">Menunggu Pembayaran</option>
                                    <option <?= $transaksi_lain_sd->status_transaksi == "2" ? 'selected' :''; ?> value="2">Lunas</option>
                                    <option <?= $transaksi_lain_sd->status_transaksi == "3" ? 'selected' :''; ?> value="3">Expired</option>
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
              window.location.href = BASE_URL + 'administrator/transaksi_lain_sd';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_transaksi_lain_sd = $('#form_transaksi_lain_sd');
        var data_post = form_transaksi_lain_sd.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_transaksi_lain_sd.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#transaksi_lain_sd_image_galery').find('li').attr('qq-file-id');
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
      
                     var params = {};
       params[csrf] = token;

       $('#transaksi_lain_sd_file_kwitansi_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/transaksi_lain_sd/upload_file_kwitansi_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/transaksi_lain_sd/delete_file_kwitansi_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/transaksi_lain_sd/get_file_kwitansi_file/<?= $transaksi_lain_sd->id; ?>',
             refreshOnRequest:true
           },
          multiple : false,
          validation: {
              allowedExtensions: ["*"],
              sizeLimit : 0,
                        },
          showMessage: function(msg) {
              toastr['error'](msg);
          },
          callbacks: {
              onComplete : function(id, name, xhr) {
                if (xhr.success) {
                   var uuid = $('#transaksi_lain_sd_file_kwitansi_galery').fineUploader('getUuid', id);
                   $('#transaksi_lain_sd_file_kwitansi_uuid').val(uuid);
                   $('#transaksi_lain_sd_file_kwitansi_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#transaksi_lain_sd_file_kwitansi_uuid').val();
                  $.get(BASE_URL + '/administrator/transaksi_lain_sd/delete_file_kwitansi_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#transaksi_lain_sd_file_kwitansi_uuid').val('');
                  $('#transaksi_lain_sd_file_kwitansi_name').val('');
                }
              }
          }
      }); /*end file_kwitansi galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>