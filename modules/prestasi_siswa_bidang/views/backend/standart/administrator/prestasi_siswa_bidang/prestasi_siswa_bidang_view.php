
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Bidang Prestasi Siswa      <small><?= cclang('detail', ['Bidang Prestasi Siswa']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/prestasi_siswa_bidang'); ?>">Bidang Prestasi Siswa</a></li>
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
                     <h3 class="widget-user-username">Bidang Prestasi Siswa</h3>
                     <h5 class="widget-user-desc">Detail Bidang Prestasi Siswa</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_prestasi_siswa_bidang" id="form_prestasi_siswa_bidang" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Prestasi Bidang </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_bidang->id_prestasi_bidang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Bidang </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_bidang->nama_bidang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($prestasi_siswa_bidang->created_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('prestasi_siswa_bidang_update', function() use ($prestasi_siswa_bidang){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit prestasi_siswa_bidang (Ctrl+e)" href="<?= site_url('administrator/prestasi_siswa_bidang/edit/'.$prestasi_siswa_bidang->id_prestasi_bidang); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Prestasi Siswa Bidang']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/prestasi_siswa_bidang/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Prestasi Siswa Bidang']); ?></a>
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
