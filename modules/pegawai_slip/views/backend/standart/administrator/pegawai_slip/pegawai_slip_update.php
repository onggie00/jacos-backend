

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Slip Gaji & Tunjangan Pegawai        <small>Edit Slip Gaji & Tunjangan Pegawai</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/pegawai_slip'); ?>">Slip Gaji & Tunjangan Pegawai</a></li>
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
                            <h3 class="widget-user-username">Slip Gaji & Tunjangan Pegawai</h3>
                            <h5 class="widget-user-desc">Edit Slip Gaji & Tunjangan Pegawai</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/pegawai_slip/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_pegawai_slip', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_pegawai_slip', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_slip" class="col-sm-2 control-label">Id Slip 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="id_slip" id="id_slip" placeholder="Id Slip" value="<?= set_value('id_slip', $pegawai_slip->id_slip); ?>">
                                <small class="info help-block">
                                <b>Input Id Slip</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_pegawai" class="col-sm-2 control-label">Pegawai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Pegawai" value="<?= set_value('id_pegawai', $pegawai_slip->nama_lengkap); ?>" required/>
                                <small class="info help-block">
                                <b>Input Id Pegawai</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="npp" class="col-sm-2 control-label">NPP 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npp" id="npp" placeholder="NPP" value="<?= set_value('npp', $pegawai_slip->npp); ?>">
                                <small class="info help-block">
                                <b>Input Npp</b> Max Length : 30.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="golongan" class="col-sm-2 control-label">Golongan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="golongan" id="golongan" placeholder="Golongan" value="<?= set_value('golongan', $pegawai_slip->golongan); ?>">
                                <small class="info help-block">
                                <b>Input Golongan</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="jabatan" class="col-sm-2 control-label">Jabatan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="jabatan" id="jabatan" placeholder="Jabatan" value="<?= set_value('jabatan', $pegawai_slip->jabatan); ?>">
                                <small class="info help-block">
                                <b>Input Jabatan</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="gaji_pokok" class="col-sm-2 control-label">Gaji Pokok 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="gaji_pokok" id="gaji_pokok" placeholder="Gaji Pokok" value="<?= set_value('gaji_pokok', $pegawai_slip->gaji_pokok); ?>">
                                <small class="info help-block">
                                <b>Input Gaji Pokok</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_istri" class="col-sm-2 control-label">Tunjangan Istri 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_istri" id="tunjangan_istri" placeholder="Tunjangan Istri" value="<?= set_value('tunjangan_istri', $pegawai_slip->tunjangan_istri); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Istri</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_anak" class="col-sm-2 control-label">Tunjangan Anak 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_anak" id="tunjangan_anak" placeholder="Tunjangan Anak" value="<?= set_value('tunjangan_anak', $pegawai_slip->tunjangan_anak); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Anak</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_pengelolaan" class="col-sm-2 control-label">Tunjangan Pengelolaan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_pengelolaan" id="tunjangan_pengelolaan" placeholder="Tunjangan Pengelolaan" value="<?= set_value('tunjangan_pengelolaan', $pegawai_slip->tunjangan_pengelolaan); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Pengelolaan</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_jabatan" class="col-sm-2 control-label">Tunjangan Jabatan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_jabatan" id="tunjangan_jabatan" placeholder="Tunjangan Jabatan" value="<?= set_value('tunjangan_jabatan', $pegawai_slip->tunjangan_jabatan); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Jabatan</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_kesejahteraan" class="col-sm-2 control-label">Tunjangan Kesejahteraan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_kesejahteraan" id="tunjangan_kesejahteraan" placeholder="Tunjangan Kesejahteraan" value="<?= set_value('tunjangan_kesejahteraan', $pegawai_slip->tunjangan_kesejahteraan); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Kesejahteraan</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_masa_kerja" class="col-sm-2 control-label">Tunjangan Masa Kerja 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_masa_kerja" id="tunjangan_masa_kerja" placeholder="Tunjangan Masa Kerja" value="<?= set_value('tunjangan_masa_kerja', $pegawai_slip->tunjangan_masa_kerja); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Masa Kerja</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_fungsional" class="col-sm-2 control-label">Tunjangan Fungsional 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_fungsional" id="tunjangan_fungsional" placeholder="Tunjangan Fungsional" value="<?= set_value('tunjangan_fungsional', $pegawai_slip->tunjangan_fungsional); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Fungsional</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="total_kehadiran" class="col-sm-2 control-label">Total Kehadiran 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="total_kehadiran" id="total_kehadiran" placeholder="Total Kehadiran" value="<?= set_value('total_kehadiran', $pegawai_slip->total_kehadiran); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="rupiah_per_kehadiran" class="col-sm-2 control-label">Rupiah Per Kehadiran 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="rupiah_per_kehadiran" id="rupiah_per_kehadiran" placeholder="Rupiah Per Kehadiran" value="<?= set_value('rupiah_per_kehadiran', $pegawai_slip->rupiah_per_kehadiran); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_kehadiran" class="col-sm-2 control-label">Tunjangan Kehadiran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_kehadiran" id="tunjangan_kehadiran" placeholder="Tunjangan Kehadiran" value="<?= set_value('tunjangan_kehadiran', $pegawai_slip->tunjangan_kehadiran); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Kehadiran</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="total_mengajar" class="col-sm-2 control-label">Total Mengajar 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="total_mengajar" id="total_mengajar" placeholder="Total Mengajar" value="<?= set_value('total_mengajar', $pegawai_slip->total_mengajar); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="rupiah_per_mengajar" class="col-sm-2 control-label">Rupiah Per Mengajar 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="rupiah_per_mengajar" id="rupiah_per_mengajar" placeholder="Rupiah Per Mengajar" value="<?= set_value('rupiah_per_mengajar', $pegawai_slip->rupiah_per_mengajar); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_mengajar" class="col-sm-2 control-label">Tunjangan Mengajar 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_mengajar" id="tunjangan_mengajar" placeholder="Tunjangan Mengajar" value="<?= set_value('tunjangan_mengajar', $pegawai_slip->tunjangan_mengajar); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Mengajar</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="total_piket" class="col-sm-2 control-label">Total Piket 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="total_piket" id="total_piket" placeholder="Total Piket" value="<?= set_value('total_piket', $pegawai_slip->total_piket); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="rupiah_per_piket" class="col-sm-2 control-label">Rupiah Per Piket 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="rupiah_per_piket" id="rupiah_per_piket" placeholder="Rupiah Per Piket" value="<?= set_value('rupiah_per_piket', $pegawai_slip->rupiah_per_piket); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_piket" class="col-sm-2 control-label">Tunjangan Piket 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_piket" id="tunjangan_piket" placeholder="Tunjangan Piket" value="<?= set_value('tunjangan_piket', $pegawai_slip->tunjangan_piket); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Piket</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_wali_kelas" class="col-sm-2 control-label">Tunjangan Wali Kelas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_wali_kelas" id="tunjangan_wali_kelas" placeholder="Tunjangan Wali Kelas" value="<?= set_value('tunjangan_wali_kelas', $pegawai_slip->tunjangan_wali_kelas); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Wali Kelas</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_pembina" class="col-sm-2 control-label">Tunjangan Pembina 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_pembina" id="tunjangan_pembina" placeholder="Tunjangan Pembina" value="<?= set_value('tunjangan_pembina', $pegawai_slip->tunjangan_pembina); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Pembina</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="insentif_ft" class="col-sm-2 control-label">Insentif France Track 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="insentif_ft" id="insentif_ft" placeholder="Insentif France Track" value="<?= set_value('insentif_ft', $pegawai_slip->insentif_ft); ?>">
                                <small class="info help-block">
                                <b>Input Insentif Ft</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tunjangan_insentif" class="col-sm-2 control-label">Tunjangan Insentif 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tunjangan_insentif" id="tunjangan_insentif" placeholder="Tunjangan Insentif" value="<?= set_value('tunjangan_insentif', $pegawai_slip->tunjangan_insentif); ?>">
                                <small class="info help-block">
                                <b>Input Tunjangan Insentif</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="bonus" class="col-sm-2 control-label">Bonus 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bonus" id="bonus" placeholder="Bonus" value="<?= set_value('bonus', $pegawai_slip->bonus); ?>">
                                <small class="info help-block">
                                <b>Input Bonus</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="honor" class="col-sm-2 control-label">Honor 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="honor" id="honor" placeholder="Honor" value="<?= set_value('honor', $pegawai_slip->honor); ?>">
                                <small class="info help-block">
                                <b>Input Honor</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="periode_mulai" class="col-sm-2 control-label">Periode Mulai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" name="periode_mulai" id="periode_mulai" placeholder="Periode Mulai" value="<?=  $pegawai_slip->periode_mulai; ?>">
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="periode_selesai" class="col-sm-2 control-label">Periode Selesai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" name="periode_selesai" id="periode_selesai" placeholder="Periode selesai" value="<?=  $pegawai_slip->periode_selesai; ?>">
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
              window.location.href = BASE_URL + 'administrator/pegawai_slip';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_pegawai_slip = $('#form_pegawai_slip');
        var data_post = form_pegawai_slip.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_pegawai_slip.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#pegawai_slip_image_galery').find('li').attr('qq-file-id');
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