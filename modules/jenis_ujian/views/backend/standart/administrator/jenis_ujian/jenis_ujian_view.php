
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Jenis Ujian      <small><?= cclang('detail', ['Jenis Ujian']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/jenis_ujian'); ?>">Jenis Ujian</a></li>
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
                     <h3 class="widget-user-username">Jenis Ujian</h3>
                     <h5 class="widget-user-desc">Detail Jenis Ujian</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_jenis_ujian" id="form_jenis_ujian" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Jenis Ujian </label>

                        <div class="col-sm-8">
                           <?= _ent($jenis_ujian->id_jenis_ujian); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Ujian </label>

                        <div class="col-sm-8">
                           <?= _ent($jenis_ujian->nama_ujian); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('jenis_ujian_update', function() use ($jenis_ujian){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit jenis_ujian (Ctrl+e)" href="<?= site_url('administrator/jenis_ujian/edit/'.$jenis_ujian->id_jenis_ujian); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Jenis Ujian']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/jenis_ujian/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Jenis Ujian']); ?></a>
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
