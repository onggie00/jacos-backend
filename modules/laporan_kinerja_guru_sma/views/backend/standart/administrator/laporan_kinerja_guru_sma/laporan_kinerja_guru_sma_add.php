
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Laporan Kinerja Guru SMA        <small><?= cclang('new', ['Laporan Kinerja Guru SMA']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/laporan_kinerja_guru_sma'); ?>">Laporan Kinerja Guru SMA</a></li>
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
                            <h3 class="widget-user-username">Laporan Kinerja Guru SMA</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Laporan Kinerja Guru SMA']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_laporan_kinerja_guru_sma', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_laporan_kinerja_guru_sma', 
                            'enctype' => 'multipart/form-data', 
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
                                    <option value="<?= $row->id_guru ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Guru</b> Max Length : 11.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="unit_kerja" class="col-sm-2 control-label">Unit Kerja 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="unit_kerja" id="unit_kerja" placeholder="Unit Kerja" value="<?= set_value('unit_kerja'); ?>">
                                <small class="info help-block">
                                <b>Input Unit Kerja</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="mata_pelajaran" class="col-sm-2 control-label">Mata Pelajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="mata_pelajaran" id="mata_pelajaran" placeholder="Mata Pelajaran" value="<?= set_value('mata_pelajaran'); ?>">
                                <small class="info help-block">
                                <b>Input Mata Pelajaran</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nilai_pimpinan" class="col-sm-2 control-label">Penilaian Pimpinan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nilai_pimpinan" id="nilai_pimpinan" placeholder="Penilaian Pimpinan" value="<?= set_value('nilai_pimpinan'); ?>">
                                <small class="info help-block">
                                <b>Input Nilai Pimpinan</b> Max Length : 2.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nilai_sejawat" class="col-sm-2 control-label">Penilaian Sejawat 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nilai_sejawat" id="nilai_sejawat" placeholder="Penilaian Sejawat" value="<?= set_value('nilai_sejawat'); ?>">
                                <small class="info help-block">
                                <b>Input Nilai Sejawat</b> Max Length : 2.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nilai_siswa" class="col-sm-2 control-label">Penilaian Siswa 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nilai_siswa" id="nilai_siswa" placeholder="Penilaian Siswa" value="<?= set_value('nilai_siswa'); ?>">
                                <small class="info help-block">
                                <b>Input Nilai Siswa</b> Max Length : 2.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nilai_sendiri" class="col-sm-2 control-label">Penilaian Sendiri 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nilai_sendiri" id="nilai_sendiri" placeholder="Penilaian Sendiri" value="<?= set_value('nilai_sendiri'); ?>">
                                <small class="info help-block">
                                <b>Input Nilai Sendiri</b> Max Length : 2.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nilai_prestasi" class="col-sm-2 control-label">Penilaian Prestasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nilai_prestasi" id="nilai_prestasi" placeholder="Penilaian Prestasi" value="<?= set_value('nilai_prestasi'); ?>">
                                <small class="info help-block">
                                <b>Input Nilai Prestasi</b> Max Length : 2.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nilai_presensi" class="col-sm-2 control-label">Penilaian Presensi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nilai_presensi" id="nilai_presensi" placeholder="Penilaian Presensi" value="<?= set_value('nilai_presensi'); ?>">
                                <small class="info help-block">
                                <b>Input Nilai Presensi</b> Max Length : 2.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="rank" class="col-sm-2 control-label">Rank 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="rank" id="rank" placeholder="Rank" value="<?= set_value('rank'); ?>">
                                <small class="info help-block">
                                <b>Input Rank</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tahun_ajaran" id="tahun_ajaran" placeholder="Tahun Ajaran" value="<?= set_value('tahun_ajaran'); ?>">
                                <small class="info help-block">
                                <b>Input Tahun Ajaran</b> Max Length : 50.</small>
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
              window.location.href = BASE_URL + 'administrator/laporan_kinerja_guru_sma';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_laporan_kinerja_guru_sma = $('#form_laporan_kinerja_guru_sma');
        var data_post = form_laporan_kinerja_guru_sma.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/laporan_kinerja_guru_sma/add_save',
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