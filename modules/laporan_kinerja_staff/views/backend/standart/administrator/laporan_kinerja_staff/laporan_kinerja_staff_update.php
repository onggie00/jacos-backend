

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Laporan Kinerja Staff        <small>Edit Laporan Kinerja Staff</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/laporan_kinerja_staff'); ?>">Laporan Kinerja Staff</a></li>
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
                            <h3 class="widget-user-username">Laporan Kinerja Staff</h3>
                            <h5 class="widget-user-desc">Edit Laporan Kinerja Staff</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/laporan_kinerja_staff/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_laporan_kinerja_staff', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_laporan_kinerja_staff', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="id_staff" class="col-sm-2 control-label">Staff 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_staff" id="id_staff" data-placeholder="Select Staff" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('pegawai') as $row): ?>
                                    <option <?=  $row->id_pegawai ==  $laporan_kinerja_staff->id_staff ? 'selected' : ''; ?> value="<?= $row->id_pegawai ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Id Staff</b> Max Length : 11.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="unit_kerja" class="col-sm-2 control-label">Unit Kerja 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="unit_kerja" id="unit_kerja" placeholder="Unit Kerja" value="<?= set_value('unit_kerja', $laporan_kinerja_staff->unit_kerja); ?>">
                                <small class="info help-block">
                                <b>Input Unit Kerja</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nilai_pimpinan" class="col-sm-2 control-label">Penilaian Pimpinan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nilai_pimpinan" id="nilai_pimpinan" placeholder="Penilaian Pimpinan" value="<?= set_value('nilai_pimpinan', $laporan_kinerja_staff->nilai_pimpinan); ?>">
                                <small class="info help-block">
                                <b>Input Nilai Pimpinan</b> Max Length : 2.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nilai_sejawat" class="col-sm-2 control-label">Penilaian Sejawat 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nilai_sejawat" id="nilai_sejawat" placeholder="Penilaian Sejawat" value="<?= set_value('nilai_sejawat', $laporan_kinerja_staff->nilai_sejawat); ?>">
                                <small class="info help-block">
                                <b>Input Nilai Sejawat</b> Max Length : 2.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nilai_sendiri" class="col-sm-2 control-label">Penilaian Sendiri 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nilai_sendiri" id="nilai_sendiri" placeholder="Penilaian Sendiri" value="<?= set_value('nilai_sendiri', $laporan_kinerja_staff->nilai_sendiri); ?>">
                                <small class="info help-block">
                                <b>Input Nilai Sendiri</b> Max Length : 2.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nilai_prestasi" class="col-sm-2 control-label">Penilaian Prestasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nilai_prestasi" id="nilai_prestasi" placeholder="Penilaian Prestasi" value="<?= set_value('nilai_prestasi', $laporan_kinerja_staff->nilai_prestasi); ?>">
                                <small class="info help-block">
                                <b>Input Nilai Prestasi</b> Max Length : 2.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nilai_presensi" class="col-sm-2 control-label">Penilaian Presensi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nilai_presensi" id="nilai_presensi" placeholder="Penilaian Presensi" value="<?= set_value('nilai_presensi', $laporan_kinerja_staff->nilai_presensi); ?>">
                                <small class="info help-block">
                                <b>Input Nilai Presensi</b> Max Length : 2.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="rank" class="col-sm-2 control-label">Rank 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="rank" id="rank" placeholder="Rank" value="<?= set_value('rank', $laporan_kinerja_staff->rank); ?>">
                                <small class="info help-block">
                                <b>Input Rank</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tahun_ajaran" class="col-sm-2 control-label">Tahun Ajaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tahun_ajaran" id="tahun_ajaran" placeholder="Tahun Ajaran" value="<?= set_value('tahun_ajaran', $laporan_kinerja_staff->tahun_ajaran); ?>">
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
              window.location.href = BASE_URL + 'administrator/laporan_kinerja_staff';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_laporan_kinerja_staff = $('#form_laporan_kinerja_staff');
        var data_post = form_laporan_kinerja_staff.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_laporan_kinerja_staff.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#laporan_kinerja_staff_image_galery').find('li').attr('qq-file-id');
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