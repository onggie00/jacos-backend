
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Waktu & Materi Ujian      <small><?= cclang('detail', ['Waktu & Materi Ujian']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/waktu_materi_tes'); ?>">Waktu & Materi Ujian</a></li>
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
                     <h3 class="widget-user-username">Waktu & Materi Ujian</h3>
                     <h5 class="widget-user-desc">Detail Waktu & Materi Ujian</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_waktu_materi_tes" id="form_waktu_materi_tes" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Waktu Materi Tes </label>

                        <div class="col-sm-8">
                           <?= _ent($waktu_materi_tes->id_waktu_materi_tes); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Ujian </label>

                        <div class="col-sm-8">
                           <?= _ent($waktu_materi_tes->tgl_ujian); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Waktu Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($waktu_materi_tes->waktu_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Waktu Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($waktu_materi_tes->waktu_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Materi / Agenda </label>

                        <div class="col-sm-8">
                           <?= _ent($waktu_materi_tes->materi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($waktu_materi_tes->jenjang); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('waktu_materi_tes_update', function() use ($waktu_materi_tes){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit waktu_materi_tes (Ctrl+e)" href="<?= site_url('administrator/waktu_materi_tes/edit/'.$waktu_materi_tes->id_waktu_materi_tes); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Waktu Materi Tes']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/waktu_materi_tes/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Waktu Materi Tes']); ?></a>
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
