

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Manajemen Tagihan FT        <small>Edit Manajemen Tagihan FT</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/transaksi_lain_ft_manajemen'); ?>">Manajemen Tagihan FT</a></li>
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
                            <h3 class="widget-user-username">Manajemen Tagihan FT</h3>
                            <h5 class="widget-user-desc">Edit Manajemen Tagihan FT</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/transaksi_lain_ft_manajemen/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_transaksi_lain_ft_manajemen', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_transaksi_lain_ft_manajemen', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_kategori" class="col-sm-2 control-label">Kategori 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_kategori" id="id_kategori" data-placeholder="Select Kategori" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('transaksi_lain_kategori') as $row): ?>
                                    <option <?=  $row->id_kategori ==  $transaksi_lain_ft_manajemen->id_kategori ? 'selected' : ''; ?> value="<?= $row->id_kategori ?>"><?= $row->nama_kategori; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Kategori</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="nama_transaksi" class="col-sm-2 control-label">Nama Tagihan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_transaksi" id="nama_transaksi" placeholder="Nama Tagihan" value="<?= set_value('nama_transaksi', $transaksi_lain_ft_manajemen->nama_transaksi); ?>">
                                <small class="info help-block">
                                <b>Input Nama Transaksi</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="keterangan" class="col-sm-2 control-label">Keterangan 
                            </label>
                            <div class="col-sm-8">
                                <textarea id="keterangan" name="keterangan" rows="10" cols="80"> <?= set_value('keterangan', $transaksi_lain_ft_manajemen->keterangan); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nominal" class="col-sm-2 control-label">Nominal 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="nominal" id="nominal" placeholder="Nominal" value="<?= set_value('nominal', $transaksi_lain_ft_manajemen->nominal); ?>">
                                <small class="info help-block">
                                <b>Input Nominal</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_tahun_ajaran" id="id_tahun_ajaran" data-placeholder="Select Tahun Ajaran" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('tahun_ajaran') as $row): ?>
                                    <option <?=  $row->id_tahun_ajaran ==  $transaksi_lain_ft_manajemen->id_tahun_ajaran ? 'selected' : ''; ?> value="<?= $row->id_tahun_ajaran ?>"><?= $row->id_tahun_ajaran; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Tahun Ajaran</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="id_tingkatan" class="col-sm-2 control-label">Tingkatan 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_tingkatan" id="id_tingkatan" data-placeholder="Select Tingkatan" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('tingkatan_ft') as $row): ?>
                                    <option <?=  $row->id_tingkatan_ft ==  $transaksi_lain_ft_manajemen->id_tingkatan ? 'selected' : ''; ?> value="<?= $row->id_tingkatan_ft ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Tingkatan</b> Max Length : 100.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="id_kelas" class="col-sm-2 control-label">Kelas 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="id_kelas[]" id="id_kelas" data-placeholder="Select Kelas" multiple >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('kelas_ft') as $row): ?>
                                    <option <?=  in_array($row->id_kelas_ft, explode(',', $transaksi_lain_ft_manajemen->id_kelas)) ? 'selected' : ''; ?> value="<?= $row->id_kelas_ft ?>"><?= $row->nama_kelas; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Kelas</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_siswa" class="col-sm-2 control-label">Siswa 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="id_siswa[]" id="id_siswa" data-placeholder="Select Siswa" multiple >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_ft_aktif') as $row): ?>
                                    <option <?=  in_array($row->id_siswa_ft_aktif, explode(',', $transaksi_lain_ft_manajemen->id_siswa)) ? 'selected' : ''; ?> value="<?= $row->id_siswa_ft_aktif ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Siswa</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tanggal_tagihan_mulai" class="col-sm-2 control-label">Tagihan Mulai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="tanggal_tagihan_mulai"  placeholder="Tagihan Mulai" id="tanggal_tagihan_mulai" value="<?= set_value('tanggal_tagihan_mulai', $transaksi_lain_ft_manajemen->tanggal_tagihan_mulai); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tanggal_tagihan_selesai" class="col-sm-2 control-label">Tagihan Selesai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="tanggal_tagihan_selesai"  placeholder="Tagihan Selesai" id="tanggal_tagihan_selesai" value="<?= set_value('tanggal_tagihan_selesai', $transaksi_lain_ft_manajemen->tanggal_tagihan_selesai); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tipe_bank" class="col-sm-2 control-label">Bank 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="tipe_bank" id="tipe_bank" data-placeholder="Select Bank" >
                                    <option value=""></option>
                                    <option <?= $transaksi_lain_ft_manajemen->tipe_bank == "BNI" ? 'selected' :''; ?> value="BNI">BNI</option>
                                    <option <?= $transaksi_lain_ft_manajemen->tipe_bank == "BRI" ? 'selected' :''; ?> value="BRI">BRI</option>
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
<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
<!-- Page script -->
<script>
    $(document).ready(function(){
       
      
      CKEDITOR.replace('keterangan'); 
      var keterangan = CKEDITOR.instances.keterangan;
                   
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
              window.location.href = BASE_URL + 'administrator/transaksi_lain_ft_manajemen';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#keterangan').val(keterangan.getData());
                    
        var form_transaksi_lain_ft_manajemen = $('#form_transaksi_lain_ft_manajemen');
        var data_post = form_transaksi_lain_ft_manajemen.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_transaksi_lain_ft_manajemen.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#transaksi_lain_ft_manajemen_image_galery').find('li').attr('qq-file-id');
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