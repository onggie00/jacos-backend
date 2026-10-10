
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Kuesioner PSB <small>Tambah Pertanyaan</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/web_psb_pertanyaan'); ?>">Kuesioner PSB</a></li>
        <li class="active">Tambah</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row" >
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-body ">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header ">
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="Tambah">
                            </div>
                            <h3 class="widget-user-username">Kuesioner PSB</h3>
                            <h5 class="widget-user-desc">Tambah Pertanyaan Kuesioner</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/web_psb_pertanyaan/add_save'), [
                            'name'    => 'form_web_psb_pertanyaan',
                            'class'   => 'form-horizontal form-step',
                            'id'      => 'form_web_psb_pertanyaan',
                            'method'  => 'POST'
                            ]); ?>

                        <div class="form-group ">
                            <label for="pertanyaan" class="col-sm-2 control-label">Pertanyaan
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="pertanyaan" id="pertanyaan" rows="4" placeholder="Tulis pertanyaan kuesioner"><?= set_value('pertanyaan'); ?></textarea>
                                <small class="info help-block"><b>Pertanyaan yang diisi calon siswa di form web PSB (step 5)</b></small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="step" class="col-sm-2 control-label">Step
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="step" id="step" placeholder="Contoh: 5" value="<?= set_value('step'); ?>">
                                <small class="info help-block"><b>Step form pendaftaran</b></small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="no_urut" class="col-sm-2 control-label">No Urut
                            </label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="no_urut" id="no_urut" placeholder="No urut tampil" value="<?= set_value('no_urut'); ?>">
                                <small class="info help-block"><b>Urutan tampil pertanyaan dalam step</b></small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="tipe_input" class="col-sm-2 control-label">Tipe Input
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-5">
                                <select class="form-control" name="tipe_input" id="tipe_input">
                                    <?php foreach ($tipe_input_list as $k => $v) { ?>
                                    <option value="<?= $k; ?>" <?= set_value('tipe_input') == $k ? 'selected' : ''; ?>><?= $v; ?></option>
                                    <?php } ?>
                                </select>
                                <small class="info help-block"><b>radio = satu jawaban, check = boleh lebih dari satu</b></small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="min_input" class="col-sm-2 control-label">Min Input
                            </label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="min_input" id="min_input" placeholder="Min jawaban" value="<?= set_value('min_input'); ?>">
                            </div>
                            <label for="max_input" class="col-sm-2 control-label">Max Input
                            </label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="max_input" id="max_input" placeholder="Max jawaban" value="<?= set_value('max_input'); ?>">
                                <small class="info help-block"><b>Untuk tipe check: batas jumlah kotak centang (min/max), radio = 1</b></small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-3">
                                <select class="form-control" name="jenjang" id="jenjang">
                                    <?php foreach ($jenjang_list as $k => $v) { ?>
                                    <option value="<?= $k; ?>" <?= set_value('jenjang') == $k ? 'selected' : ''; ?>><?= $v; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="is_ppsbb" class="col-sm-2 control-label">Jalur PPSBB
                            </label>
                            <div class="col-sm-8">
                                <label>
                                    <input type="checkbox" class="flat-red" name="is_ppsbb" value="1" <?= set_value('is_ppsbb') ? 'checked' : ''; ?>> Pertanyaan untuk jalur PPSBB
                                </label>
                                <small class="info help-block"><b>Centang bila pertanyaan khusus jalur PPSBB (bukan PSB reguler)</b></small>
                            </div>
                        </div>

                         <div class="message"></div>
                        <div class="row-fluid col-md-7 container-button-bottom">
                            <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="<?= cclang('save_button'); ?> (Ctrl+s)">
                            <i class="fa fa-save" ></i> <?= cclang('save_button'); ?>
                            </button>
                            <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save_back" data-stype='back' title="<?= cclang('save_and_go_the_list_button'); ?> (Ctrl+d)">
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
            </div>
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
            text: "Data yang sudah diisi akan hilang!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya!",
            cancelButtonText: "Tidak",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm){
            if (isConfirm) {
              window.location.href = BASE_URL + 'administrator/web_psb_pertanyaan';
            }
          });
        return false;
      });

      $('.btn_save').click(function(){
        $('.message').fadeOut();
        var form = $('#form_web_psb_pertanyaan');
        var data_post = form.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
        $('.loading').show();
        $.ajax({
          url: form.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          if(res.success) {
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
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
        });
        return false;
      });
    });
</script>
