

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Ruang Kelas        <small>Edit Ruang Kelas</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/ujian_ruang_kelas'); ?>">Ruang Kelas</a></li>
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
                            <h3 class="widget-user-username">Ruang Kelas</h3>
                            <h5 class="widget-user-desc">Edit Ruang Kelas</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/ujian_ruang_kelas/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_ujian_ruang_kelas', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_ujian_ruang_kelas', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_ruang" class="col-sm-2 control-label">Nama Ruangan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_ruang" id="id_ruang" data-placeholder="Select Nama Ruangan" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('ujian_ruang') as $row): ?>
                                    <option <?=  $row->id_ruang ==  $ujian_ruang_kelas->id_ruang ? 'selected' : ''; ?> value="<?= $row->id_ruang ?>"><?= $row->nama_ruang; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="kapasitas" class="col-sm-2 control-label">Kapasitas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="kapasitas" id="kapasitas" placeholder="Kapasitas" value="<?= set_value('kapasitas', $ujian_ruang_kelas->kapasitas); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="kelas" class="col-sm-2 control-label">Kelas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kelas" id="kelas" placeholder="Kelas" value="<?= set_value('kelas', $ujian_ruang_kelas->kelas); ?>">
                                <small class="info help-block">
                                <b>Input Kelas</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jenjang_kelas" class="col-sm-2 control-label">Jenjang Kelas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="jenjang_kelas" id="jenjang_kelas" data-placeholder="Select Jenjang Kelas" >
                                    <option value=""></option>
                                    <option <?= $ujian_ruang_kelas->jenjang_kelas == "SD" ? 'selected' :''; ?> value="SD">SD</option>
                                    <option <?= $ujian_ruang_kelas->jenjang_kelas == "SMP" ? 'selected' :''; ?> value="SMP">SMP</option>
                                    <option <?= $ujian_ruang_kelas->jenjang_kelas == "SMA" ? 'selected' :''; ?> value="SMA">SMA</option>
                                    <option <?= $ujian_ruang_kelas->jenjang_kelas == "FT" ? 'selected' :''; ?> value="FT">FT</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="id_tingkatan" class="col-sm-2 control-label">Tingkatan Kelas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                    <select  class="form-control chosen chosen-select" name="id_tingkatan" id="id_tingkatan" data-placeholder="Select Tingkatan Kelas" >
                                        <option value=""></option>
                                        <?php
                                            if (!empty($list_tingkatan)) {
                                                foreach ($list_tingkatan as $key => $value) {
                                                    if ($value['selected'] == 1) {
                                                        echo "<option value='".$value['id_tingkatan']."' selected>".$value['label']." (".$value['jenjang'].")"."</option>";
                                                    }
                                                    else{
                                                        echo "<option value='".$value['id_tingkatan']."'>".$value['label']." (".$value['jenjang'].")"."</option>";
                                                    }
                                                }
                                            }
                                        ?>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nomor_peserta_awal" class="col-sm-2 control-label">Nomor Peserta Awal 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomor_peserta_awal" id="nomor_peserta_awal" placeholder="Nomor Peserta Awal" value="<?= set_value('nomor_peserta_awal', $ujian_ruang_kelas->nomor_peserta_awal); ?>">
                                <small class="info help-block">
                                <b>Input Nomor Peserta Awal</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nomor_peserta_akhir" class="col-sm-2 control-label">Nomor Peserta Akhir 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomor_peserta_akhir" id="nomor_peserta_akhir" placeholder="Nomor Peserta Akhir" value="<?= set_value('nomor_peserta_akhir', $ujian_ruang_kelas->nomor_peserta_akhir); ?>">
                                <small class="info help-block">
                                <b>Input Nomor Peserta Akhir</b> Max Length : 10.</small>
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
              window.location.href = BASE_URL + 'administrator/ujian_ruang_kelas';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_ujian_ruang_kelas = $('#form_ujian_ruang_kelas');
        var data_post = form_ujian_ruang_kelas.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_ujian_ruang_kelas.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#ujian_ruang_kelas_image_galery').find('li').attr('qq-file-id');
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