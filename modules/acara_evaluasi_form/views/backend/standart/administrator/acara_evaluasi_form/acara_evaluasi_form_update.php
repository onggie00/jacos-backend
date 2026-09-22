

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Evaluasi Acara Form        <small>Edit Evaluasi Acara Form</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/acara_evaluasi_form'); ?>">Evaluasi Acara Form</a></li>
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
                            <h3 class="widget-user-username">Evaluasi Acara Form</h3>
                            <h5 class="widget-user-desc">Edit Evaluasi Acara Form</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/acara_evaluasi_form/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_acara_evaluasi_form', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_acara_evaluasi_form', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_acara" class="col-sm-2 control-label">Acara 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_acara" id="id_acara" data-placeholder="Select Acara" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('acara') as $row): ?>
                                    <option <?=  $row->id_acara ==  $acara_evaluasi_form->id_acara ? 'selected' : ''; ?> value="<?= $row->id_acara ?>"><?= $row->nama_acara; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Acara</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="pertanyaan" class="col-sm-2 control-label">Pertanyaan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="pertanyaan" name="pertanyaan" rows="10" cols="80"> <?= set_value('pertanyaan', $acara_evaluasi_form->pertanyaan); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tipe_pertanyaan" class="col-sm-2 control-label">Tipe Pertanyaan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="tipe_pertanyaan" id="tipe_pertanyaan" data-placeholder="Select Tipe Pertanyaan" >
                                    <option value=""></option>
                                    <option <?= $acara_evaluasi_form->tipe_pertanyaan == "text" ? 'selected' :''; ?> value="text">Teks</option>
                                    <option <?= $acara_evaluasi_form->tipe_pertanyaan == "number" ? 'selected' :''; ?> value="number">Angka</option>
                                    <option <?= $acara_evaluasi_form->tipe_pertanyaan == "checklist" ? 'selected' :''; ?> value="checklist">Ceklis / List (lebih dari 1 pilihan)</option>
                                    <option <?= $acara_evaluasi_form->tipe_pertanyaan == "radio" ? 'selected' :''; ?> value="radio">Opsi (1 pilihan)</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group  wrapper-options-crud">
                            <label for="is_required" class="col-sm-2 control-label">Wajib Diisi? 
                            </label>
                            <div class="col-sm-8">
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $acara_evaluasi_form->is_required == "0" ? "checked" : ""; ?> type="radio" class="flat-red" name="is_required" value="0"> TIDAK                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $acara_evaluasi_form->is_required == "1" ? "checked" : ""; ?> type="radio" class="flat-red" name="is_required" value="1"> YA                                    </label>
                                    </div>
                                    </select>
                                <div class="row-fluid clear-both">
                                <small class="info help-block">
                                </small>
                                </div>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jawaban_pertanyaan" class="col-sm-2 control-label">Jawaban Pertanyaan 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="jawaban_pertanyaan" id="jawaban_pertanyaan" placeholder="Jawaban Pertanyaan" value="<?= set_value('jawaban_pertanyaan', $acara_evaluasi_form->jawaban_pertanyaan); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="no_urut" class="col-sm-2 control-label">Nomor Pertanyaan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="no_urut" id="no_urut" placeholder="Nomor Pertanyaan" value="<?= set_value('no_urut', $acara_evaluasi_form->no_urut); ?>">
                                <small class="info help-block">
                                <b>Input No Urut</b> Max Length : 11.</small>
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
       
      
      CKEDITOR.replace('pertanyaan'); 
      var pertanyaan = CKEDITOR.instances.pertanyaan;
                   
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
              window.location.href = BASE_URL + 'administrator/acara_evaluasi_form';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#pertanyaan').val(pertanyaan.getData());
                    
        var form_acara_evaluasi_form = $('#form_acara_evaluasi_form');
        var data_post = form_acara_evaluasi_form.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_acara_evaluasi_form.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#acara_evaluasi_form_image_galery').find('li').attr('qq-file-id');
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