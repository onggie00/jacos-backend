
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Link Daftar Lain      <small><?= cclang('detail', ['Link Daftar Lain']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/web_link_daftar_lain'); ?>">Link Daftar Lain</a></li>
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
                     <h3 class="widget-user-username">Link Daftar Lain</h3>
                     <h5 class="widget-user-desc">Detail Link Daftar Lain</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_web_link_daftar_lain" id="form_web_link_daftar_lain" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($web_link_daftar_lain->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Link Lain Pendaftaran </label>

                        <div class="col-sm-8">
                           <?= _ent($web_link_daftar_lain->link_daftar_lain); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tipe Daftar </label>

                        <div class="col-sm-8">
                           <?= _ent($web_link_daftar_lain->tipe_daftar); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('web_link_daftar_lain_update', function() use ($web_link_daftar_lain){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit web_link_daftar_lain (Ctrl+e)" href="<?= site_url('administrator/web_link_daftar_lain/edit/'.$web_link_daftar_lain->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Web Link Daftar Lain']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/web_link_daftar_lain/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Web Link Daftar Lain']); ?></a>
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
