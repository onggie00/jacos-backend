

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Ujian Ruang Detail        <small>Edit Ujian Ruang Detail</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/ujian_ruang_detail'); ?>">Ujian Ruang Detail</a></li>
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
                            <h3 class="widget-user-username">Ujian Ruang Detail</h3>
                            <h5 class="widget-user-desc">Edit Ujian Ruang Detail</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/ujian_ruang_detail/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_ujian_ruang_detail', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_ujian_ruang_detail', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_ruang" class="col-sm-2 control-label">Ruang Ujian 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_ruang" id="id_ruang" data-placeholder="Select Ruang Ujian" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('ujian_ruang') as $row): ?>
                                    <option <?=  $row->id_ruang ==  $ujian_ruang_detail->id_ruang ? 'selected' : ''; ?> value="<?= $row->id_ruang ?>"><?= $row->nama_ruang; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Ruang</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="jenjang" class="col-sm-2 control-label">Jenjang 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="jenjang" id="jenjang" placeholder="Jenjang" value="<?= set_value('jenjang', $ujian_ruang_detail->jenjang); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_ruang_kelas" class="col-sm-2 control-label">Kelas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_ruang_kelas" id="id_ruang_kelas" data-placeholder="Select Kelas" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('ujian_ruang_kelas') as $row): ?>
                                    <option <?=  $row->id_ruang_kelas ==  $ujian_ruang_detail->id_ruang_kelas ? 'selected' : ''; ?> value="<?= $row->id_ruang_kelas ?>"><?= $row->kelas; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Ruang Kelas</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="nomor_peserta_ujian" class="col-sm-2 control-label">Nomor Peserta Ujian 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomor_peserta_ujian" id="nomor_peserta_ujian" placeholder="Nomor Peserta Ujian" value="<?= set_value('nomor_peserta_ujian', $ujian_ruang_detail->nomor_peserta_ujian); ?>">
                                <small class="info help-block">
                                <b>Input Nomor Peserta Ujian</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group  wrapper-options-crud">
                            <label for="boleh_ujian" class="col-sm-2 control-label">Boleh Ujian? 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $ujian_ruang_detail->boleh_ujian == "YA" ? "checked" : ""; ?> type="radio" class="flat-red" name="boleh_ujian" value="YA"> YA                                    </label>
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                    <label>
                                    <input <?= $ujian_ruang_detail->boleh_ujian == "TIDAK" ? "checked" : ""; ?> type="radio" class="flat-red" name="boleh_ujian" value="TIDAK"> TIDAK                                    </label>
                                    </div>
                                    </select>
                                <div class="row-fluid clear-both">
                                <small class="info help-block">
                                </small>
                                </div>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nama_siswa" class="col-sm-2 control-label">Nama Siswa 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_siswa" id="nama_siswa" placeholder="Nama Siswa" value="<?= set_value('nama_siswa', $ujian_ruang_detail->nama_siswa); ?>">
                                <small class="info help-block">
                                <b>Input Nama Siswa</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="password" class="col-sm-2 control-label">Password
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="password" id="password" placeholder="Nama Siswa" value="<?= set_value('password', $ujian_ruang_detail->password); ?>">
                                <small class="info help-block">
                                <b>Input Password.</small>
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
              window.location.href = BASE_URL + 'administrator/ujian_ruang_detail';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_ujian_ruang_detail = $('#form_ujian_ruang_detail');
        var data_post = form_ujian_ruang_detail.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_ujian_ruang_detail.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#ujian_ruang_detail_image_galery').find('li').attr('qq-file-id');
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