
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Ketentuan Pendaftaran Ft      <small><?= cclang('detail', ['Ketentuan Pendaftaran Ft']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/ketentuan_pendaftaran_ft'); ?>">Ketentuan Pendaftaran Ft</a></li>
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
                     <h3 class="widget-user-username">Ketentuan Pendaftaran Ft</h3>
                     <h5 class="widget-user-desc">Detail Ketentuan Pendaftaran Ft</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_ketentuan_pendaftaran_ft" id="form_ketentuan_pendaftaran_ft" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Ketentuan Pendaftaran </label>

                        <div class="col-sm-8">
                           <?= _ent($ketentuan_pendaftaran_ft->id_ketentuan_pendaftaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Judul </label>

                        <div class="col-sm-8">
                           <?= _ent($ketentuan_pendaftaran_ft->judul); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Sub Judul </label>

                        <div class="col-sm-8">
                           <?= _ent($ketentuan_pendaftaran_ft->sub_judul); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Deskripsi </label>

                        <div class="col-sm-8">
                           <?= _ent($ketentuan_pendaftaran_ft->ketentuan_pendaftaran); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('ketentuan_pendaftaran_ft_update', function() use ($ketentuan_pendaftaran_ft){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit ketentuan_pendaftaran_ft (Ctrl+e)" href="<?= site_url('administrator/ketentuan_pendaftaran_ft/edit/'.$ketentuan_pendaftaran_ft->id_ketentuan_pendaftaran); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Ketentuan Pendaftaran Ft']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/ketentuan_pendaftaran_ft/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Ketentuan Pendaftaran Ft']); ?></a>
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
