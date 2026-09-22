

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Catatan Kartu Peserta        <small>Edit Catatan Kartu Peserta</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/catatan_kartu_peserta'); ?>">Catatan Kartu Peserta</a></li>
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
                            <h3 class="widget-user-username">Catatan Kartu Peserta</h3>
                            <h5 class="widget-user-desc">Edit Catatan Kartu Peserta</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/catatan_kartu_peserta/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_catatan_kartu_peserta', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_catatan_kartu_peserta', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="catatan_persiapan" class="col-sm-2 control-label">Catatan Persiapan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="catatan_persiapan" name="catatan_persiapan" rows="10" cols="80"> <?= set_value('catatan_persiapan', $catatan_kartu_peserta->catatan_persiapan); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="catatan_perhatikan" class="col-sm-2 control-label">Catatan Perhatikan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="catatan_perhatikan" name="catatan_perhatikan" rows="10" cols="80"> <?= set_value('catatan_perhatikan', $catatan_kartu_peserta->catatan_perhatikan); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="keterangan_ujian" class="col-sm-2 control-label">Keterangan Ujian 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="keterangan_ujian" name="keterangan_ujian" rows="10" cols="80"> <?= set_value('keterangan_ujian', $catatan_kartu_peserta->keterangan_ujian); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group  wrapper-options-crud">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang 
                            </label>
                            <div class="col-sm-8">
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input disabled <?= $catatan_kartu_peserta->jenjang == "sd" ? "checked" : ""; ?> type="radio" class="flat-red" name="jenjang" value="sd"> SD                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input disabled <?= $catatan_kartu_peserta->jenjang == "smp" ? "checked" : ""; ?> type="radio" class="flat-red" name="jenjang" value="smp"> SMP                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input disabled <?= $catatan_kartu_peserta->jenjang == "sma" ? "checked" : ""; ?> type="radio" class="flat-red" name="jenjang" value="sma"> SMA                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input disabled <?= $catatan_kartu_peserta->jenjang == "ft" ? "checked" : ""; ?> type="radio" class="flat-red" name="jenjang" value="ft"> France Track                                    </label>
                                    </div>
                                    </select>
                                <div class="row-fluid clear-both">
                                <small class="info help-block">
                                </small>
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
<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
<!-- Page script -->
<script>
    $(document).ready(function(){
       
      
      CKEDITOR.replace('catatan_persiapan'); 
      var catatan_persiapan = CKEDITOR.instances.catatan_persiapan;
            CKEDITOR.replace('catatan_perhatikan'); 
      var catatan_perhatikan = CKEDITOR.instances.catatan_perhatikan;
            CKEDITOR.replace('keterangan_ujian'); 
      var keterangan_ujian = CKEDITOR.instances.keterangan_ujian;
                   
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
              window.location.href = BASE_URL + 'administrator/catatan_kartu_peserta';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#catatan_persiapan').val(catatan_persiapan.getData());
                $('#catatan_perhatikan').val(catatan_perhatikan.getData());
                $('#keterangan_ujian').val(keterangan_ujian.getData());
                    
        var form_catatan_kartu_peserta = $('#form_catatan_kartu_peserta');
        var data_post = form_catatan_kartu_peserta.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_catatan_kartu_peserta.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#catatan_kartu_peserta_image_galery').find('li').attr('qq-file-id');
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