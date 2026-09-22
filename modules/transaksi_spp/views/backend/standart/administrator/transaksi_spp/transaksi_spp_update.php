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
    Transaksi Spp <small>Edit Transaksi Spp</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class=""><a href="<?= site_url('administrator/transaksi_spp'); ?>">Transaksi Spp</a></li>
    <li class="active">Edit</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
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
              <h3 class="widget-user-username">Transaksi Spp</h3>
              <h5 class="widget-user-desc">Edit Transaksi Spp</h5>
              <hr>
            </div>
            <?= form_open(base_url('administrator/transaksi_spp/edit_save/' . $this->uri->segment(4)), [
              'name'    => 'form_transaksi_spp',
              'class'   => 'form-horizontal form-step',
              'id'      => 'form_transaksi_spp',
              'method'  => 'POST'
            ]); ?>

            <div class="form-group ">
              <label for="user_email" class="col-sm-2 control-label">User Email
                <i class="required">*</i>
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="user_email" id="user_email" placeholder="User Email" value="<?= set_value('user_email', $transaksi_spp->user_email); ?>">
                <small class="info help-block">
                  <b>Input User Email</b> Max Length : 200.</small>
              </div>
            </div>

            <div class="form-group ">
              <label for="user_name" class="col-sm-2 control-label">User Name
                <i class="required">*</i>
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="user_name" id="user_name" placeholder="User Name" value="<?= set_value('user_name', $transaksi_spp->user_name); ?>">
                <small class="info help-block">
                  <b>Input User Name</b> Max Length : 200.</small>
              </div>
            </div>

            <div class="form-group ">
              <label for="va_number" class="col-sm-2 control-label">VA Number
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="va_number" id="va_number" placeholder="988XXXXXXXXXX" value="<?= set_value('va_number', $transaksi_spp->va_number); ?>">
                <small class="info help-block">
              </div>
            </div>

           <div class="form-group ">
              <label for="tota_biaya" class="col-sm-2 control-label">Total Biaya
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="total_biaya" id="total_biaya" placeholder="" value="<?= set_value('total_biaya', $transaksi_spp->total_biaya); ?>">
                <small class="info help-block">
              </div>
            </div>

            <div class="form-group  wrapper-options-crud">
              <label for="status_transaksi" class="col-sm-2 control-label">Status Transaksi
                <i class="required">*</i>
              </label>
              <div class="col-sm-8">
                <div class="col-md-3 padding-left-0">
                  <label>
                    <input <?= $transaksi_spp->status_transaksi == "0" ? "checked" : ""; ?> type="radio" class="flat-red" name="status_transaksi" value="0"> Menunggu Aktivasi VA </label>
                </div>
                <!-- <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $transaksi_spp->status_transaksi == "1" ? "checked" : ""; ?> type="radio" class="flat-red" name="status_transaksi" value="1"> Virtual Account Aktif (Menunggu Pembayaran)                                    </label>
                                    </div> -->
                <div class="col-md-3 padding-left-0">
                  <label>
                    <input <?= $transaksi_spp->status_transaksi == "2" ? "checked" : ""; ?> type="radio" class="flat-red" name="status_transaksi" value="2"> Pembayaran Berhasil </label>
                </div>
                </select>
                <div class="row-fluid clear-both">
                  <small class="info help-block">
                  </small>
                </div>
              </div>
            </div>

            <div class="form-group ">
              <label for="is_show" class="col-sm-2 control-label">Is Show
                <i class="required">*</i>
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="is_show" id="is_show" placeholder="Is Show" value="<?= set_value('is_show', $transaksi_spp->is_show); ?>">
                <small class="info help-block">
                  <b>Input Is Show</b> Max Length : 6.</small>
              </div>
            </div>

            <div class="message"></div>
            <div class="row-fluid col-md-7 container-button-bottom">
              <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="<?= cclang('save_button'); ?> (Ctrl+s)">
                <i class="fa fa-save"></i> <?= cclang('save_button'); ?>
              </button>
              <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save" data-stype='back' title="<?= cclang('save_and_go_the_list_button'); ?> (Ctrl+d)">
                <i class="ion ion-ios-list-outline"></i> <?= cclang('save_and_go_the_list_button'); ?>
              </a>
              <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="<?= cclang('cancel_button'); ?> (Ctrl+x)">
                <i class="fa fa-undo"></i> <?= cclang('cancel_button'); ?>
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
<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
<!-- Page script -->
<script>
  $(document).ready(function() {



    $('#btn_cancel').click(function() {
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
        function(isConfirm) {
          if (isConfirm) {
            window.location.href = BASE_URL + 'administrator/transaksi_spp';
          }
        });

      return false;
    }); /*end btn cancel*/

    $('.btn_save').click(function() {
      $('.message').fadeOut();

      var form_transaksi_spp = $('#form_transaksi_spp');
      var data_post = form_transaksi_spp.serializeArray();
      var save_type = $(this).attr('data-stype');
      data_post.push({
        name: 'save_type',
        value: save_type
      });

      $('.loading').show();

      $.ajax({
          url: form_transaksi_spp.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if (res.success) {
            var id = $('#transaksi_spp_image_galery').find('li').attr('qq-file-id');
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }

            $('.message').printMessage({
              message: res.message
            });
            $('.message').fadeIn();
            $('.data_file_uuid').val('');

          } else {
            if (res.errors) {
              parseErrorField(res.errors);
            }
            $('.message').printMessage({
              message: res.message,
              type: 'warning'
            });
          }

        })
        .fail(function() {
          $('.message').printMessage({
            message: 'Error save data',
            type: 'warning'
          });
        })
        .always(function() {
          $('.loading').hide();
          $('html, body').animate({
            scrollTop: $(document).height()
          }, 2000);
        });

      return false;
    }); /*end btn save*/





    async function chain() {}

    chain();




  }); /*end doc ready*/
</script>