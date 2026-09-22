
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Catatan Kartu Peserta      <small><?= cclang('detail', ['Catatan Kartu Peserta']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/catatan_kartu_peserta'); ?>">Catatan Kartu Peserta</a></li>
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
                     <h3 class="widget-user-username">Catatan Kartu Peserta</h3>
                     <h5 class="widget-user-desc">Detail Catatan Kartu Peserta</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_catatan_kartu_peserta" id="form_catatan_kartu_peserta" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Catatan Kartu Peserta </label>

                        <div class="col-sm-8">
                           <?= _ent($catatan_kartu_peserta->id_catatan_kartu_peserta); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Catatan Persiapan </label>

                        <div class="col-sm-8">
                           <?= _ent($catatan_kartu_peserta->catatan_persiapan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Catatan Perhatikan </label>

                        <div class="col-sm-8">
                           <?= _ent($catatan_kartu_peserta->catatan_perhatikan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan Ujian </label>

                        <div class="col-sm-8">
                           <?= _ent($catatan_kartu_peserta->keterangan_ujian); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($catatan_kartu_peserta->jenjang); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('catatan_kartu_peserta_update', function() use ($catatan_kartu_peserta){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit catatan_kartu_peserta (Ctrl+e)" href="<?= site_url('administrator/catatan_kartu_peserta/edit/'.$catatan_kartu_peserta->id_catatan_kartu_peserta); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Catatan Kartu Peserta']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/catatan_kartu_peserta/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Catatan Kartu Peserta']); ?></a>
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
