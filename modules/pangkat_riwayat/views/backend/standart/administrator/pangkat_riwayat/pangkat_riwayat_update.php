

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Pangkat Riwayat        <small>Edit Pangkat Riwayat</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/pangkat_riwayat'); ?>">Pangkat Riwayat</a></li>
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
                            <h3 class="widget-user-username">Pangkat Riwayat</h3>
                            <h5 class="widget-user-desc">Edit Pangkat Riwayat</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/pangkat_riwayat/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_pangkat_riwayat', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_pangkat_riwayat', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="unit" class="col-sm-2 control-label">Unit 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="unit" id="unit" data-placeholder="Select Unit" >
                                    <option value=""></option>
                                    <option <?= $pangkat_riwayat->unit == "sd" ? 'selected' :''; ?> value="sd">SD</option>
                                    <option <?= $pangkat_riwayat->unit == "smp" ? 'selected' :''; ?> value="smp">SMP</option>
                                    <option <?= $pangkat_riwayat->unit == "sma" ? 'selected' :''; ?> value="sma">SMA</option>
                                    <option <?= $pangkat_riwayat->unit == "-" ? 'selected' :''; ?> value="-">Non Unit</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Unit</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nama_tabel" class="col-sm-2 control-label">Role 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="nama_tabel" id="nama_tabel" data-placeholder="Select Role" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('presensi_setting_role') as $row): ?>
                                    <option <?=  $row->nama_tabel ==  $pangkat_riwayat->nama_tabel ? 'selected' : ''; ?> value="<?= $row->nama_tabel ?>"><?= $row->nama_role; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Nama Tabel</b> Max Length : 30.</small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="id_user" class="col-sm-2 control-label">User 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="id_user" id="id_user" data-placeholder="Select User" >
                                    <option value=""></option>
                                    <?php 
                                    // Load users based on current nama_tabel
                                    $current_nama_tabel = $pangkat_riwayat->nama_tabel;
                                    $table_pk_map = [
                                        'pegawai' => 'id_pegawai',
                                        'guru_sd' => 'id_guru',
                                        'guru_smp' => 'id_guru',
                                        'guru_sma' => 'id_guru',
                                        'pimpinan_sd' => 'id_pimpinan',
                                        'pimpinan_smp' => 'id_pimpinan',
                                        'pimpinan_sma' => 'id_pimpinan',
                                        'pramubhakti' => 'id_pramubhakti',
                                        'security' => 'id_security',
                                    ];
                                    if (!empty($current_nama_tabel) && isset($table_pk_map[$current_nama_tabel])) {
                                        $pk = $table_pk_map[$current_nama_tabel];
                                        $users = $this->db->select("{$pk} as id, nama_lengkap")
                                            ->where('deleted_at IS NULL', null, false)
                                            ->or_where('deleted_at', '0000-00-00 00:00:00')
                                            ->order_by('nama_lengkap', 'ASC')
                                            ->get($current_nama_tabel)
                                            ->result();
                                        foreach ($users as $row):
                                    ?>
                                    <option <?= $row->id == $pangkat_riwayat->id_user ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->nama_lengkap; ?></option>
                                    <?php 
                                        endforeach;
                                    } 
                                    ?>  
                                </select>
                                <small class="info help-block">
                                <b>Pilih Role untuk mengubah daftar user</b></small>
                            </div>
                        </div>


                                                 
                                                <div class="form-group ">
                            <label for="npp" class="col-sm-2 control-label">NPP 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npp" id="npp" placeholder="NPP" value="<?= set_value('npp', $pangkat_riwayat->npp); ?>">
                                <small class="info help-block">
                                <b>Input Npp</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nomor" class="col-sm-2 control-label">Nomor 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomor" id="nomor" placeholder="Nomor" value="<?= set_value('nomor', $pangkat_riwayat->nomor); ?>">
                                <small class="info help-block">
                                <b>Input Nomor</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="pangkat" class="col-sm-2 control-label">Pangkat 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="pangkat" id="pangkat" placeholder="Pangkat" value="<?= set_value('pangkat', $pangkat_riwayat->pangkat); ?>">
                                <small class="info help-block">
                                <b>Input Pangkat</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="golongan" class="col-sm-2 control-label">Golongan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="golongan" id="golongan" placeholder="Golongan" value="<?= set_value('golongan', $pangkat_riwayat->golongan); ?>">
                                <small class="info help-block">
                                <b>Input Golongan</b> Max Length : 20.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tmt" class="col-sm-2 control-label">TMT 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="tmt"  placeholder="TMT" id="tmt" value="<?= set_value('pangkat_riwayat_tmt_name', $pangkat_riwayat->tmt); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                       
                                                 
                                                <div class="form-group ">
                            <label for="is_sk_calon" class="col-sm-2 control-label">SK Calon? 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="is_sk_calon" id="is_sk_calon" data-placeholder="Select SK Calon?" >
                                    <option value=""></option>
                                    <option <?= $pangkat_riwayat->is_sk_calon == "ya" ? 'selected' :''; ?> value="ya">Ya</option>
                                    <option <?= $pangkat_riwayat->is_sk_calon == "tidak" ? 'selected' :''; ?> value="tidak">Tidak</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Is Sk Calon</b> Max Length : 1.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="is_cant_promoted" class="col-sm-2 control-label">Sudah Maksimal? 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="is_cant_promoted" id="is_cant_promoted" data-placeholder="Select Sudah Maksimal?" >
                                    <option value=""></option>
                                    <option <?= $pangkat_riwayat->is_cant_promoted == "ya" ? 'selected' :''; ?> value="ya">Ya</option>
                                    <option <?= $pangkat_riwayat->is_cant_promoted == "tidak" ? 'selected' :''; ?> value="tidak">Tidak</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Is Cant Promoted</b> Max Length : 1.</small>
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
              window.location.href = BASE_URL + 'administrator/pangkat_riwayat';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_pangkat_riwayat = $('#form_pangkat_riwayat');
        var data_post = form_pangkat_riwayat.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_pangkat_riwayat.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#pangkat_riwayat_image_galery').find('li').attr('qq-file-id');
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

      // Dynamic dropdown for id_user based on nama_tabel selection
      $('#nama_tabel').on('change', function() {
        var nama_tabel = $(this).val();
        var $id_user = $('#id_user');
        
        // Clear current options
        $id_user.empty().append('<option value=""></option>');
        
        if (nama_tabel) {
          // Show loading
          $id_user.prop('disabled', true);
          
          $.ajax({
            url: BASE_URL + '/administrator/pangkat_riwayat/get_users_by_table',
            type: 'GET',
            dataType: 'json',
            data: { nama_tabel: nama_tabel },
          })
          .done(function(res) {
            if (res && res.length > 0) {
              $.each(res, function(index, item) {
                $id_user.append('<option value="' + item.id + '">' + item.nama_lengkap + '</option>');
              });
            } else {
              $id_user.append('<option value="" disabled>Tidak ada data user</option>');
            }
          })
          .fail(function() {
            $id_user.append('<option value="" disabled>Error loading data</option>');
          })
          .always(function() {
            $id_user.prop('disabled', false);
            $id_user.trigger('chosen:updated');
          });
        } else {
          $id_user.trigger('chosen:updated');
        }
      });
      
      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>