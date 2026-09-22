

<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Web Pengaturan Homepage        <small>Edit Web Pengaturan Homepage</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/web_pengaturan_homepage'); ?>">Web Pengaturan Homepage</a></li>
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
                            <h3 class="widget-user-username">Web Pengaturan Homepage</h3>
                            <h5 class="widget-user-desc">Edit Web Pengaturan Homepage</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/web_pengaturan_homepage/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_web_pengaturan_homepage', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_web_pengaturan_homepage', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="link_youtube" class="col-sm-2 control-label">Link Youtube 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="link_youtube" id="link_youtube" placeholder="Link Youtube" value="<?= set_value('link_youtube', $web_pengaturan_homepage->link_youtube); ?>">
                                <small class="info help-block">
                                <b>Input Link Youtube</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="judul" class="col-sm-2 control-label">Judul 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="judul" id="judul" placeholder="Judul" value="<?= set_value('judul', $web_pengaturan_homepage->judul); ?>">
                                <small class="info help-block">
                                <b>Input Judul</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="sub_judul" class="col-sm-2 control-label">Sub Judul 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="sub_judul" id="sub_judul" placeholder="Sub Judul" value="<?= set_value('sub_judul', $web_pengaturan_homepage->sub_judul); ?>">
                                <small class="info help-block">
                                <b>Input Sub Judul</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="link_whatsapp" class="col-sm-2 control-label">Link Whatsapp 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="link_whatsapp" id="link_whatsapp" placeholder="Link Whatsapp" value="<?= set_value('link_whatsapp', $web_pengaturan_homepage->link_whatsapp); ?>">
                                <small class="info help-block">
                                <b>Input Link Whatsapp</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="footer_alamat" class="col-sm-2 control-label">Alamat 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="footer_alamat" id="footer_alamat" placeholder="Alamat" value="<?= set_value('footer_alamat', $web_pengaturan_homepage->footer_alamat); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="footer_notelp" class="col-sm-2 control-label">Nomor Telepon 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="footer_notelp" id="footer_notelp" placeholder="Nomor Telepon" value="<?= set_value('footer_notelp', $web_pengaturan_homepage->footer_notelp); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="footer_email" class="col-sm-2 control-label">Email 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="footer_email" id="footer_email" placeholder="Email" value="<?= set_value('footer_email', $web_pengaturan_homepage->footer_email); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="footer_instagram" class="col-sm-2 control-label">Instagram 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="footer_instagram" id="footer_instagram" placeholder="Instagram" value="<?= set_value('footer_instagram', $web_pengaturan_homepage->footer_instagram); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="footer_facebook" class="col-sm-2 control-label">Facebook 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="footer_facebook" id="footer_facebook" placeholder="Facebook" value="<?= set_value('footer_facebook', $web_pengaturan_homepage->footer_facebook); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="footer_twitter" class="col-sm-2 control-label">Twitter 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="footer_twitter" id="footer_twitter" placeholder="Twitter" value="<?= set_value('footer_twitter', $web_pengaturan_homepage->footer_twitter); ?>">
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
              window.location.href = BASE_URL + 'administrator/web_pengaturan_homepage';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_web_pengaturan_homepage = $('#form_web_pengaturan_homepage');
        var data_post = form_web_pengaturan_homepage.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_web_pengaturan_homepage.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#web_pengaturan_homepage_image_galery').find('li').attr('qq-file-id');
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