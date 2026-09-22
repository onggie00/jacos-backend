
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Sejarah Sekolah      <small><?= cclang('detail', ['Sejarah Sekolah']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/sejarah_sekolah'); ?>">Sejarah Sekolah</a></li>
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
                     <h3 class="widget-user-username">Sejarah Sekolah</h3>
                     <h5 class="widget-user-desc">Detail Sejarah Sekolah</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_sejarah_sekolah" id="form_sejarah_sekolah" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($sejarah_sekolah->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Judul </label>

                        <div class="col-sm-8">
                           <?= _ent($sejarah_sekolah->judul); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Konten </label>

                        <div class="col-sm-8">
                           <?= _ent($sejarah_sekolah->konten); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('sejarah_sekolah_update', function() use ($sejarah_sekolah){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit sejarah_sekolah (Ctrl+e)" href="<?= site_url('administrator/sejarah_sekolah/edit/'.$sejarah_sekolah->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Sejarah Sekolah']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/sejarah_sekolah/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Sejarah Sekolah']); ?></a>
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
