
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Ruang Kelas      <small><?= cclang('detail', ['Ruang Kelas']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/ujian_ruang_kelas'); ?>">Ruang Kelas</a></li>
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
                     <h3 class="widget-user-username">Ruang Kelas</h3>
                     <h5 class="widget-user-desc">Detail Ruang Kelas</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_ujian_ruang_kelas" id="form_ujian_ruang_kelas" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Ruang Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_ruang_kelas->id_ruang_kelas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Ruangan </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_ruang_kelas->ujian_ruang_nama_ruang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kapasitas </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_ruang_kelas->kapasitas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_ruang_kelas->kelas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_ruang_kelas->jenjang_kelas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nomor Peserta Awal </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_ruang_kelas->nomor_peserta_awal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nomor Peserta Akhir </label>

                        <div class="col-sm-8">
                           <?= _ent($ujian_ruang_kelas->nomor_peserta_akhir); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('ujian_ruang_kelas_update', function() use ($ujian_ruang_kelas){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit ujian_ruang_kelas (Ctrl+e)" href="<?= site_url('administrator/ujian_ruang_kelas/edit/'.$ujian_ruang_kelas->id_ruang_kelas); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Ujian Ruang Kelas']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/ujian_ruang_kelas/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Ujian Ruang Kelas']); ?></a>
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
