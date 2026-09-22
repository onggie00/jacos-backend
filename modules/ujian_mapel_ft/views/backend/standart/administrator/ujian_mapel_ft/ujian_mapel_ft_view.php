
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Ujian Mapel FT      <small><?= cclang('detail', ['Ujian Mapel FT']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/ujian_mapel_ft'); ?>">Ujian Mapel FT</a></li>
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
                     <h3 class="widget-user-username">Ujian Mapel FT</h3>
                     <h5 class="widget-user-desc">Detail Ujian Mapel FT</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_ujian_mapel_ft" id="form_ujian_mapel_ft" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Mapel </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_mapel_ft->id_mapel); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mata Pelajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_mapel_ft->nama_mapel); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kode Mapel Ujian </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_mapel_ft->kode_mapel_ujian); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('ujian_mapel_ft_update', function() use ($ujian_mapel_ft){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit ujian_mapel_ft (Ctrl+e)" href="<?= site_url('administrator/ujian_mapel_ft/edit/'.$ujian_mapel_ft->id_mapel); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Ujian Mapel Ft']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/ujian_mapel_ft/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Ujian Mapel Ft']); ?></a>
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
