
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Kontak Apps      <small><?= cclang('detail', ['Kontak Apps']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/apps_kontak'); ?>">Kontak Apps</a></li>
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
                     <h3 class="widget-user-username">Kontak Apps</h3>
                     <h5 class="widget-user-desc">Detail Kontak Apps</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_apps_kontak" id="form_apps_kontak" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kontak Apps </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_kontak->id_apps_kontak); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tipe Kontak </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_kontak->tipe_kontak_tipe); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Kontak </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_kontak->nama_kontak); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kontak </label>

                        <div class="col-sm-8">
                           <?= _ent($apps_kontak->kontak); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('apps_kontak_update', function() use ($apps_kontak){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit apps_kontak (Ctrl+e)" href="<?= site_url('administrator/apps_kontak/edit/'.$apps_kontak->id_apps_kontak); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Kontak Apps']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/apps_kontak/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Kontak Apps']); ?></a>
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
