

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Status Daftar Ulang Sd        <small>Edit Status Daftar Ulang Sd</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/status_daftar_ulang_sd'); ?>">Status Daftar Ulang Sd</a></li>
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
                            <h3 class="widget-user-username">Status Daftar Ulang Sd</h3>
                            <h5 class="widget-user-desc">Edit Status Daftar Ulang Sd</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/status_daftar_ulang_sd/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_status_daftar_ulang_sd', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_status_daftar_ulang_sd', 
                            'method'  => 'POST'
                            ]); ?>
                         <div class="form-group  wrapper-options-crud">
                            <label for="status" class="col-sm-2 control-label">Nama 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" value="<?= $status_daftar_ulang_sd->nama_lengkap?>" disabled>
                            </div>
                          </div>
                          <div class="form-group  wrapper-options-crud">
                            <label for="status" class="col-sm-2 control-label">Status 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $status_daftar_ulang_sd->status == "0" ? "checked" : ""; ?> type="radio" class="flat-red" id="status" name="status" value="0"> Menunggu Aktivasi                                    
                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $status_daftar_ulang_sd->status == "1" ? "checked" : ""; ?> type="radio" class="flat-red" id="status" name="status" value="1"> Aktivasi Virtual Account                                   
                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $status_daftar_ulang_sd->status == "2" ? "checked" : ""; ?> type="radio" class="flat-red" id="status" name="status" value="2"> Pembayaran Berhasil                                   
                                    </label>
                                    </div>
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
                                <input class="form-control" type="number" name="custom_total_payment" value="<?= ($status_daftar_ulang_sd->custom_payment)?$status_daftar_ulang_sd->custom_payment:0;?>">
                                <div class="row-fluid clear-both">
                                <small class="info help-block text-danger">
                                *Isi jika ada perubahan total biaya, jika tidak bisa dikosongi</small>
                                </div>
                              </div>
                        </div>
                        <div class="form-group  wrapper-options-crud">
                            <label for="status" class="col-sm-2 control-label">Virtual Account BRI 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" disabled value="<?= $status_daftar_ulang_sd->va_bri?>">
                            </div>
                        </div>
                        <!-- Riwayat bri -->
                        <div class="form-group  wrapper-options-crud">
                            <label for="status" class="col-sm-2 control-label">Riwayat Pembayaran
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                            <div class="table-responsive">
                              <table class="table table-bordered table-striped dataTable">
                                <thead>
                                    <tr class="">
                                      <th>No briva</th>
                                      <th>Jumlah</th>
                                      <th>Date</th>
                                      <th>ID Journal </th>
                                      <th>Terminal ID</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_trans_bri">
                                    <?php foreach ($riwayat as $key => $trans_bri) : ?>
                                      <?php 
                                        $date=substr($trans_bri->transaction_date,0,8);
                                        $year=substr($date,0,4);
                                        $month=substr($date,4,2);
                                        $day=substr($date,6,2);
                                        $time=substr($trans_bri->transaction_date,8,13);
                                        $hour=substr($time,0,2);
                                        $min=substr($time,2,2);
                                        $sec=substr($time,4,2);
                                        $transaction_date = $year."-".$month."-".$day." ".$hour.":".$min.":".$sec;  
                                      ?>
                                      <tr>
                                          <td><?=  _ent($trans_bri->briva_no); ?></td>
                                          <td><?= _ent($trans_bri->bill_amount); ?></td>
                                          <td><?= _ent($trans_bri->transaction_date); ?></td>
                                          <td><?= _ent($trans_bri->journal_id); ?></td>
                                          <td><?= _ent($trans_bri->terminal_id); ?></td>
                                      </tr>
                                    <?php endforeach; ?>
                                    <?php if ($trans_bri_counts == 0) : ?>
                                      <tr>
                                          <td colspan="100">
                                            Trans Bri data is not available
                                          </td>
                                      </tr>
                                    <?php endif; ?>
                                </tbody>
                              </table>
                            </div>
                            </div>
                        </div>

                        <div class="form-group  wrapper-options-crud">
                            <label for="status" class="col-sm-2 control-label">Total Pembayaran
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" disabled value="<?= $trans_bri_total?>">
                            </div>
                        </div>

                        <div class="form-group  wrapper-options-crud">
                            <label for="tipe_tagihan" class="col-sm-2 control-label">Tipe Tagihan
                              <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                              <div class="col-md-3 padding-left-0">
                                <label>
                                  <input <?= $status_daftar_ulang_sd->tipe_tagihan == "fixed" ? "checked" : ""; ?> type="radio" class="flat-red" id="tipe_tagihan" name="tipe_tagihan" value="fixed"> Fixed Payment
                                </label>
                              </div>
                              <div class="col-md-3 padding-left-0">
                                <label>
                                  <input <?= $status_daftar_ulang_sd->tipe_tagihan == "open" ? "checked" : ""; ?> type="radio" class="flat-red" id="tipe_tagihan" name="tipe_tagihan" value="open"> Open Payment
                                </label>
                              </div>
                            </div>
                        </div>
                        <div class="form-group  wrapper-options-crud">
                            <label for="tipe_tagihan" class="col-sm-2 control-label">Masa Tagihan VA
                            </label>
                            <div class="col-sm-8">
                              <input class="form-control pull-right datetimepicker" type="text" name="end_date_va" id="end_date_va" value="<?= (!empty($status_daftar_ulang_sd->end_date_va)) ? date('Y-m-d', strtotime($status_daftar_ulang_sd->end_date_va)) : date('Y-m-d', strtotime('+1 month'));?>">
                              <div class="row-fluid clear-both">
                                <small class="info help-block text-danger">
                                *Isi jika ada pembayaran open payment / ada tanggal tertentu, jika tidak bisa dikosongi (diisi default sesuai pengaturan sistem)</small>
                              </div>
                            </div>
                        </div>
                        <input type="hidden" name="jenjang" id="jenjang" value="sd">

                        <!-- detail bri
                        <div class="form-group  wrapper-options-crud">
                            <label for="status" class="col-sm-2 control-label">Detail Tagihan Pembayaran
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                            <div class="table-responsive">
                              <table class="table table-bordered table-striped dataTable">
                                <thead>
                                    <tr class="">
                                      <th>brivaNo</th>
                                      <th>Nama</th>
                                      <th>Keterangan</th>
                                      <th>Jumlah</th>
                                      <th>Status Bayar</th>
                                      <th>Expired Date</th>
                                      <th>Last Update</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_detail_bri">
                                      <tr>
                                          <td><?= _ent($detail_bri['BrivaNo'].$detail_bri['CustCode']); ?></td>
                                          <td><?= _ent($detail_bri['Nama']); ?></td>
                                          <td><?= _ent($detail_bri['Keterangan']); ?></td>
                                          <td><?= _ent($detail_bri['Amount']); ?></td>
                                          <td><?= _ent($detail_bri['statusBayar']); ?></td>
                                          <td><?= _ent($detail_bri['expiredDate']); ?></td>
                                          <td><?= _ent($detail_bri['lastUpdate']); ?></td>
                                      </tr>
                                    <?php if ($detail_bri == NULL) : ?>
                                      <tr>
                                          <td colspan="100">
                                            Detail Pembayaran is not available
                                          </td>
                                      </tr>
                                    <?php endif; ?>
                                </tbody>
                              </table>
                            </div>
                            </div>
                        </div> -->
                                                
                            <div class="message" style="padding-top:40px"></div>
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
      
      // $( "#status" ).change(function() {
      //   if(this.val()==1){

      //   }
      // });
             
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
              window.location.href = BASE_URL + 'administrator/status_daftar_ulang_sd';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_status_daftar_ulang_sd = $('#form_status_daftar_ulang_sd');
        var data_post = form_status_daftar_ulang_sd.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_status_daftar_ulang_sd.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#status_daftar_ulang_sd_image_galery').find('li').attr('qq-file-id');
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