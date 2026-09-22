
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Laporan Tka Indikator      <small><?= cclang('detail', ['Laporan Tka Indikator']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/laporan_tka_indikator'); ?>">Laporan Tka Indikator</a></li>
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
                     <h3 class="widget-user-username">Laporan Tka Indikator</h3>
                     <h5 class="widget-user-desc">Detail Laporan Tka Indikator</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_laporan_tka_indikator" id="form_laporan_tka_indikator" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Indikator </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_tka_indikator->id_indikator); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kategori </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_tka_indikator->judul_kategori); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Soal </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_tka_indikator->soal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">No Urut </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_tka_indikator->no_urut); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun </label>

                        <div class="col-sm-8">
                           <?= _ent($laporan_tka_indikator->tahun); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('laporan_tka_indikator_update', function() use ($laporan_tka_indikator){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit laporan_tka_indikator (Ctrl+e)" href="<?= site_url('administrator/laporan_tka_indikator/edit/'.$laporan_tka_indikator->id_indikator); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Laporan Tka Indikator']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/laporan_tka_indikator/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Laporan Tka Indikator']); ?></a>
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
