

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Manajemen Ekskul FT        <small>Edit Manajemen Ekskul FT</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/ekskul_manajemen_ft'); ?>">Manajemen Ekskul FT</a></li>
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
                            <h3 class="widget-user-username">Manajemen Ekskul FT</h3>
                            <h5 class="widget-user-desc">Edit Manajemen Ekskul FT</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/ekskul_manajemen_ft/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_ekskul_manajemen_ft', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_ekskul_manajemen_ft', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_ekskul" class="col-sm-2 control-label">Ekstrakurikuler 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_ekskul" id="id_ekskul" data-placeholder="Select Ekstrakurikuler" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('ekskul') as $row): ?>
                                    <option <?=  $row->id_ekskul ==  $ekskul_manajemen_ft->id_ekskul ? 'selected' : ''; ?> value="<?= $row->id_ekskul ?>"><?= $row->nama; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Ekskul</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="id_guru_pembina" class="col-sm-2 control-label">Pembina 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_guru_pembina" id="id_guru_pembina" data-placeholder="Select Pembina" >
                                    <option value=""></option>
                                    <?php
                                        $get_pembina = $this->mymodel->withquery("select * from guru_ft order by nama_lengkap asc", "result");
                                    ?>
                                    <?php foreach ($get_pembina as $row => $value): ?>
                                    <option value="<?= $value->id_guru ?>" <?= ($ekskul_manajemen_ft->id_guru_pembina == $value->id_guru && $ekskul_manajemen_ft->jenjang_pembina == 'ft') ? 'selected' : ''; ?>><?= $value->nama_lengkap; ?> (FT)</option>
                                    <?php endforeach; ?>
                                    <?php
                                        $get_pembina = $this->mymodel->withquery("select * from guru_sma order by nama_lengkap asc", "result");
                                    ?>
                                    <?php foreach ($get_pembina as $row => $value): ?>
                                    <option value="<?= $value->id_guru ?>" <?= ($ekskul_manajemen_ft->id_guru_pembina == $value->id_guru && $ekskul_manajemen_ft->jenjang_pembina == 'sma') ? 'selected' : ''; ?>><?= $value->nama_lengkap; ?> (SMA)</option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                <b>Input Id Guru Pembina</b> Max Length : 11.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="id_ekskul" class="col-sm-2 control-label">Jenjang Pembina 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="jenjang_pembina" id="jenjang_pembina" data-placeholder="Select Jenjang Pembina" >
                                    <option value="ft" <?=  $ekskul_manajemen_ft->jenjang_pembina == "ft" ? 'selected' : ''; ?>>FT</option>
                                    <option value="sma" <?=  $ekskul_manajemen_ft->jenjang_pembina == "sma" ? 'selected' : ''; ?>>SMA</option>
                                </select>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="nama" class="col-sm-2 control-label">Nama 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama" value="<?= set_value('nama', $ekskul_manajemen_ft->nama); ?>">
                                <small class="info help-block">
                                <b>Input Nama</b> Max Length : 200.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="keterangan" class="col-sm-2 control-label">Keterangan 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan" value="<?= set_value('keterangan', $ekskul_manajemen_ft->keterangan); ?>">
                                <small class="info help-block">
                                <b>Input Keterangan</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="email_pelatih" class="col-sm-2 control-label">Email 
                            </label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email_pelatih" id="email_pelatih" placeholder="Email" value="<?= set_value('email_pelatih', $ekskul_manajemen_ft->email_pelatih); ?>">
                                <small class="info help-block">
                                <b>Input Email Pelatih</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="notelp_pelatih" class="col-sm-2 control-label">Nomor Telepon 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="notelp_pelatih" id="notelp_pelatih" placeholder="Nomor Telepon" value="<?= set_value('notelp_pelatih', $ekskul_manajemen_ft->notelp_pelatih); ?>">
                                <small class="info help-block">
                                <b>Input Notelp Pelatih</b> Max Length : 14.</small>
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
              window.location.href = BASE_URL + 'administrator/ekskul_manajemen_ft';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_ekskul_manajemen_ft = $('#form_ekskul_manajemen_ft');
        var data_post = form_ekskul_manajemen_ft.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_ekskul_manajemen_ft.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#ekskul_manajemen_ft_image_galery').find('li').attr('qq-file-id');
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