
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Jawaban Kuesioner PSB <small>Edit Jawaban</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/web_psb_pertanyaan'); ?>">Kuesioner PSB</a></li>
        <li class="active">Edit Jawaban</li>
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
                                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="Edit">
                            </div>
                            <h3 class="widget-user-username">Edit Jawaban</h3>
                            <h5 class="widget-user-desc">Pertanyaan #<?= _ent($pertanyaan->id_step_pertanyaan); ?> (step <?= _ent($pertanyaan->step); ?>, <?= strtoupper(_ent($pertanyaan->jenjang)); ?>)</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/web_psb_item/edit_save/'.$web_psb_item->id_web_psb_item), [
                            'name'    => 'form_web_psb_item',
                            'class'   => 'form-horizontal form-step',
                            'id'      => 'form_web_psb_item',
                            'method'  => 'POST'
                            ]); ?>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">Pertanyaan</label>
                            <div class="col-sm-8">
                                <p style="margin-top:5px"><?= nl2br(_ent($pertanyaan->pertanyaan)); ?></p>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="jawaban" class="col-sm-2 control-label">Jawaban
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="jawaban" id="jawaban" value="<?= set_value('jawaban', $web_psb_item->jawaban); ?>">
                                <small class="info help-block"><b>Pilihan yang tampil di web PSB (max 255 karakter)</b></small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="is_lainnya" class="col-sm-2 control-label">Jawaban Lainnya
                            </label>
                            <div class="col-sm-8">
                                <label>
                                    <input type="checkbox" class="flat-red" name="is_lainnya" value="1" <?= $web_psb_item->is_lainnya == 1 ? 'checked' : ''; ?>> Pilihan "Lainnya" — calon siswa boleh menulis isian sendiri
                                </label>
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
        window.location.href = BASE_URL + 'administrator/web_psb_item/?id_pertanyaan=<?= _ent($pertanyaan->id_step_pertanyaan); ?>';
        return false;
      });

      $('.btn_save').click(function(){
        $('.message').fadeOut();
        var form = $('#form_web_psb_item');
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
