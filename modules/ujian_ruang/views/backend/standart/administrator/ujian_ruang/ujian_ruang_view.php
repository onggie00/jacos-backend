
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Ruang Ujian      <small><?= cclang('detail', ['Ruang Ujian']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/ujian_ruang'); ?>">Ruang Ujian</a></li>
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
                     <h3 class="widget-user-username">Ruang Ujian</h3>
                     <h5 class="widget-user-desc">Detail Ruang Ujian</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_ujian_ruang" id="form_ujian_ruang" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Ruang </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_ruang->id_ruang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Ruangan </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_ruang->nama_ruang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Penanggung Jawab </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_ruang->penanggung_jawab); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kategori Ruang </label>

                        <div class="col-sm-8">
                          <?= join_multi_select($ujian_ruang->kategori_ruang, '', '', ''); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('ujian_ruang_update', function() use ($ujian_ruang){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit ujian_ruang (Ctrl+e)" href="<?= site_url('administrator/ujian_ruang/edit/'.$ujian_ruang->id_ruang); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Ujian Ruang']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/ujian_ruang/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Ujian Ruang']); ?></a>
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
