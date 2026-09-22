

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Guru Piket SMA        <small>Edit Guru Piket SMA</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/guru_sma_piket'); ?>">Guru Piket SMA</a></li>
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
                            <h3 class="widget-user-username">Guru Piket SMA</h3>
                            <h5 class="widget-user-desc">Edit Guru Piket SMA</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/guru_sma_piket/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_guru_sma_piket', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_guru_sma_piket', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_guru" class="col-sm-2 control-label">Guru 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_guru" id="id_guru" data-placeholder="Select Guru" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('guru_sma') as $row): ?>
                                    <option <?=  $row->id_guru ==  $guru_sma_piket->id_guru ? 'selected' : ''; ?> value="<?= $row->id_guru ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Guru</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                        <div class="form-group ">
                            <label for="id_kelas" class="col-sm-2 control-label">Kelas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_kelas" id="id_kelas" data-placeholder="Select Kelas" >
                                    <option value=""></option>
                                    <?php
                                      $data_kelas_sma = array();
                                      $get_kelas_sma = $this->mymodel->withquery("select id_kelas_sma, label from kelas_sma order by id_tingkatan ASC, nama_kelas ASC","result");
                                      $get_kelas_ft = $this->mymodel->withquery("select id_kelas_ft, label from kelas_ft order by id_tingkatan ASC, nama_kelas ASC","result");
                                      foreach ($get_kelas_sma as $row) {
                                        if ($guru_sma_piket->jenjang == "sma") {
                                          if ($guru_sma_piket->id_kelas == $row->id_kelas_sma) {
                                            echo "<option value='".$row->id_kelas_sma."' selected>".$row->label."</option>";
                                          } else {
                                            echo "<option value='".$row->id_kelas_sma."'>".$row->label."</option>";
                                          }
                                        }
                                      }
                                      foreach ($get_kelas_ft as $row) {
                                        if ($guru_sma_piket->jenjang == "ft") {
                                          if ($guru_sma_piket->id_kelas == $row->id_kelas_ft) {
                                            echo "<option value='".$row->id_kelas_ft."' selected>".$row->label."</option>";
                                          } else {
                                            echo "<option value='".$row->id_kelas_ft."'>".$row->label."</option>";
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
                            <label for="jenjang" class="col-sm-2 control-label">Unit 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="jenjang" id="jenjang" data-placeholder="Select Unit" >
                                    <option value=""></option>
                                    <option value="SMA" <?= ($guru_sma_piket->jenjang == "sma") ? 'selected' : ""; ?>>SMA</option>
                                    <option value="FT" <?= ($guru_sma_piket->jenjang == "ft") ? 'selected' : ""; ?>>FT</option>
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
              window.location.href = BASE_URL + 'administrator/guru_sma_piket';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_guru_sma_piket = $('#form_guru_sma_piket');
        var data_post = form_guru_sma_piket.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_guru_sma_piket.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#guru_sma_piket_image_galery').find('li').attr('qq-file-id');
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