

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Ucapan Pengumuman        <small>Edit Ucapan Pengumuman</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/pengumuman_ucapan'); ?>">Ucapan Pengumuman</a></li>
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
                            <h3 class="widget-user-username">Ucapan Pengumuman</h3>
                            <h5 class="widget-user-desc">Edit Ucapan Pengumuman</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/pengumuman_ucapan/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_pengumuman_ucapan', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_pengumuman_ucapan', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="ucapan_lulus" class="col-sm-2 control-label">Ucapan Lulus 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="ucapan_lulus" name="ucapan_lulus" rows="5" class="textarea form-control"><?= set_value('ucapan_lulus', $pengumuman_ucapan->ucapan_lulus); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="ucapan_tidak_lulus" class="col-sm-2 control-label">Ucapan Tidak Lulus 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="ucapan_tidak_lulus" name="ucapan_tidak_lulus" rows="5" class="textarea form-control"><?= set_value('ucapan_tidak_lulus', $pengumuman_ucapan->ucapan_tidak_lulus); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="ucapan_cadangan" class="col-sm-2 control-label">Ucapan Cadangan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="ucapan_cadangan" name="ucapan_cadangan" rows="5" class="textarea form-control"><?= set_value('ucapan_cadangan', $pengumuman_ucapan->ucapan_cadangan); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="ucapan_belum_tersedia" class="col-sm-2 control-label">Ucapan Belum Tersedia 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="ucapan_belum_tersedia" name="ucapan_belum_tersedia" rows="5" class="textarea form-control"><?= set_value('ucapan_belum_tersedia', $pengumuman_ucapan->ucapan_belum_tersedia); ?></textarea>
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
              window.location.href = BASE_URL + 'administrator/pengumuman_ucapan';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_pengumuman_ucapan = $('#form_pengumuman_ucapan');
        var data_post = form_pengumuman_ucapan.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_pengumuman_ucapan.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#pengumuman_ucapan_image_galery').find('li').attr('qq-file-id');
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