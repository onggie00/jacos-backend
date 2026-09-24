
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Siswa KB Aktif        <small><?= cclang('new', ['Siswa KB Aktif']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/siswa_kb_aktif'); ?>">Siswa KB Aktif</a></li>
        <li class="active"><?= cclang('new'); ?></li>
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
                            <h3 class="widget-user-username">Siswa KB Aktif</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Siswa KB Aktif']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_siswa_kb_aktif', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_siswa_kb_aktif', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                         <div class="form-group ">
                            <label for="id_siswa_kb" class="col-sm-2 control-label">Id Siswa KB 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_siswa_kb" id="id_siswa_kb" data-placeholder="Select Id Siswa KB" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('siswa_kb') as $row): ?>
                                    <option value="<?= $row->id_siswa_kb ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Siswa KB</b> Max Length : 11.</small>
                            </div>
                        </div>
                        
                                                <div class="form-group ">
                            <label for="nama_lengkap" class="col-sm-2 control-label">Nama Lengkap 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" value="<?= set_value('nama_lengkap'); ?>">
                                <small class="info help-block">
                                <b>Input Nama Lengkap</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nis" class="col-sm-2 control-label">Nis 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nis" id="nis" placeholder="Nis" value="<?= set_value('nis'); ?>">
                                <small class="info help-block">
                                <b>Input Nis</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="id_kelas" class="col-sm-2 control-label"> Kelas 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_kelas" id="id_kelas" data-placeholder="Select Id Kelas" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('kelas_kb') as $row): ?>
                                    <option value="<?= $row->id_kelas_kb ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input  Kelas</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="id_tahun_ajaran" class="col-sm-2 control-label"> Tahun Ajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_tahun_ajaran" id="id_tahun_ajaran" data-placeholder="Select Id Tahun Ajaran" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('tahun_ajaran') as $row): ?>
                                    <option value="<?= $row->id_tahun_ajaran ?>"><?= $row->label; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="nomor_peserta_ujian" class="col-sm-2 control-label">Nomor Peserta Ujian
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomor_peserta_ujian" id="nomor_peserta_ujian" placeholder="nomor_peserta_ujian" value="">
                                <small class="info help-block">
                                    <b>Input Nomor Peserta Ujian</b>.</small>
                            </div>
                        </div>


                                                <div class="form-group ">
                            <label for="kewarganegaraan" class="col-sm-2 control-label">Kewarganegaraan 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kewarganegaraan" id="kewarganegaraan" placeholder="Kewarganegaraan" value="<?= set_value('kewarganegaraan'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nik" class="col-sm-2 control-label">Nik 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nik" id="nik" placeholder="Nik" value="<?= set_value('nik'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="golongan_darah" class="col-sm-2 control-label">Golongan Darah 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="golongan_darah" id="golongan_darah" placeholder="Golongan Darah" value="<?= set_value('golongan_darah'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="telp" class="col-sm-2 control-label">Telp 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="telp" id="telp" placeholder="Telp" value="<?= set_value('telp'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="pendidikan_ayah" class="col-sm-2 control-label">Pendidikan Ayah 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="pendidikan_ayah" id="pendidikan_ayah" placeholder="Pendidikan Ayah" value="<?= set_value('pendidikan_ayah'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="pendidikan_ibu" class="col-sm-2 control-label">Pendidikan Ibu 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="pendidikan_ibu" id="pendidikan_ibu" placeholder="Pendidikan Ibu" value="<?= set_value('pendidikan_ibu'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="penghasilan_ayah" class="col-sm-2 control-label">Penghasilan Ayah 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="penghasilan_ayah" id="penghasilan_ayah" placeholder="Penghasilan Ayah" value="<?= set_value('penghasilan_ayah'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="penghasilan_ibu" class="col-sm-2 control-label">Penghasilan Ibu 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="penghasilan_ibu" id="penghasilan_ibu" placeholder="Penghasilan Ibu" value="<?= set_value('penghasilan_ibu'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tgl_lahir_ayah" class="col-sm-2 control-label">Tgl Lahir Ayah 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tgl_lahir_ayah" id="tgl_lahir_ayah" placeholder="Tgl Lahir Ayah" value="<?= set_value('tgl_lahir_ayah'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tgl_lahir_ibu" class="col-sm-2 control-label">Tgl Lahir Ibu 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tgl_lahir_ibu" id="tgl_lahir_ibu" placeholder="Tgl Lahir Ibu" value="<?= set_value('tgl_lahir_ibu'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="spp_custom" class="col-sm-2 control-label">SPP Khusus <span class="text-danger">Optional</span>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="spp_custom" id="spp_custom" placeholder="SPP Khusus" value="0">
                                <small class="info help-block">Nominal SPP khusus (jika ada)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="spp_type" class="col-sm-2 control-label">SPP Type <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="spp_type" id="spp_type">
                                    <option value="FULL">FULL</option>
                                    <option value="HALF">HALF (50%)</option>
                                    <option value="FREE">FREE (Rp 0)</option>
                                </select>
                                <small class="info help-block">FULL = nominal utuh, HALF = potongan 50%, FREE = tidak bayar</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="acc_ujian" class="col-sm-2 control-label">Acc Ujian <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="acc_ujian" id="acc_ujian">
                                    <option value="1">Boleh</option>
                                    <option value="0">Tidak Boleh</option>
                                </select>
                                <small class="info help-block">Apakah siswa diperbolehkan ujian walaupun belum bayar SPP</small>
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
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
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
              window.location.href = BASE_URL + 'administrator/siswa_kb_aktif';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_siswa_kb_aktif = $('#form_siswa_kb_aktif');
        var data_post = form_siswa_kb_aktif.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/siswa_kb_aktif/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('.steps li').removeClass('error');
          $('form').find('.error-input').remove();
          if(res.success) {
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
                
          } else {
            if (res.errors) {
                
                $.each(res.errors, function(index, val) {
                    $('form #'+index).parents('.form-group').addClass('has-error');
                    $('form #'+index).parents('.form-group').find('small').prepend(`
                      <div class="error-input">`+val+`</div>
                      `);
                });
                $('.steps li').removeClass('error');
                $('.content section').each(function(index, el) {
                    if ($(this).find('.has-error').length) {
                        $('.steps li:eq('+index+')').addClass('error').find('a').trigger('click');
                    }
                });
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
      
       
 
       

      
    
    
    }); /*end doc ready*/
</script>