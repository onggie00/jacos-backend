
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Web Periode Pengumuman      <small><?= cclang('detail', ['Web Periode Pengumuman']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/web_periode_pengumuman'); ?>">Web Periode Pengumuman</a></li>
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
                     <h3 class="widget-user-username">Web Periode Pengumuman</h3>
                     <h5 class="widget-user-desc">Detail Web Periode Pengumuman</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_web_periode_pengumuman" id="form_web_periode_pengumuman" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Web Periode Pengumuman </label>

                        <div class="col-sm-8">
                           <?= _ent($web_periode_pengumuman->id_web_periode_pengumuman); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Periode Pengumuman Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($web_periode_pengumuman->periode_pengumuman_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Periode Pengumuman Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($web_periode_pengumuman->periode_pengumuman_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($web_periode_pengumuman->kelas); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('web_periode_pengumuman_update', function() use ($web_periode_pengumuman){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit web_periode_pengumuman (Ctrl+e)" href="<?= site_url('administrator/web_periode_pengumuman/edit/'.$web_periode_pengumuman->id_web_periode_pengumuman); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Web Periode Pengumuman']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/web_periode_pengumuman/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Web Periode Pengumuman']); ?></a>
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
