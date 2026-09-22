
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Web Periode Daftar      <small><?= cclang('detail', ['Web Periode Daftar']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/web_periode_daftar'); ?>">Web Periode Daftar</a></li>
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
                     <h3 class="widget-user-username">Web Periode Daftar</h3>
                     <h5 class="widget-user-desc">Detail Web Periode Daftar</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_web_periode_daftar" id="form_web_periode_daftar" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Web Periode Daftar </label>

                        <div class="col-sm-8">
                           <?= _ent($web_periode_daftar->id_web_periode_daftar); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Periode Mulai Pendaftaran </label>

                        <div class="col-sm-8">
                           <?= _ent($web_periode_daftar->periode_pendaftaran_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Periode Selesai Pendaftaran </label>

                        <div class="col-sm-8">
                           <?= _ent($web_periode_daftar->periode_pendaftaran_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($web_periode_daftar->kelas); ?>
                        </div>
                    </div>
                    
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Gelombang</label>

                        <div class="col-sm-8">
                           <?= _ent($web_periode_daftar->gelombang); ?>
                        </div>
                  </div>
                  
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('web_periode_daftar_update', function() use ($web_periode_daftar){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit web_periode_daftar (Ctrl+e)" href="<?= site_url('administrator/web_periode_daftar/edit/'.$web_periode_daftar->id_web_periode_daftar); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Web Periode Daftar']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/web_periode_daftar/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Web Periode Daftar']); ?></a>
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
