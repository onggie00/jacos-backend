
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Web Pengaturan Homepage      <small><?= cclang('detail', ['Web Pengaturan Homepage']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/web_pengaturan_homepage'); ?>">Web Pengaturan Homepage</a></li>
      <li class="active"><?= cclang('detail'); ?></li>
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
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/view.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username">Web Pengaturan Homepage</h3>
                     <h5 class="widget-user-desc">Detail Web Pengaturan Homepage</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_web_pengaturan_homepage" id="form_web_pengaturan_homepage" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Web Pengaturan Homepage </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengaturan_homepage->id_web_pengaturan_homepage); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Link Youtube </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengaturan_homepage->link_youtube); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Judul </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengaturan_homepage->judul); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Sub Judul </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengaturan_homepage->sub_judul); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Link Whatsapp </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengaturan_homepage->link_whatsapp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Alamat </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengaturan_homepage->footer_alamat); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nomor Telepon </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengaturan_homepage->footer_notelp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Email </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengaturan_homepage->footer_email); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Instagram </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengaturan_homepage->footer_instagram); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Facebook </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengaturan_homepage->footer_facebook); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Twitter </label>

                        <div class="col-sm-8">
                           <?= _ent($web_pengaturan_homepage->footer_twitter); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('web_pengaturan_homepage_update', function() use ($web_pengaturan_homepage){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit web_pengaturan_homepage (Ctrl+e)" href="<?= site_url('administrator/web_pengaturan_homepage/edit/'.$web_pengaturan_homepage->id_web_pengaturan_homepage); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Web Pengaturan Homepage']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/web_pengaturan_homepage/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Web Pengaturan Homepage']); ?></a>
                     </div>
                    
                  </div>
               </div>
            </div>
            <!--/box body -->
         </div>
         <!--/box -->

      </div>
   </div>
</section>
<!-- /.content -->
