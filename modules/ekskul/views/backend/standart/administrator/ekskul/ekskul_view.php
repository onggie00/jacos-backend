
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Ekstrakurikuler      <small><?= cclang('detail', ['Ekstrakurikuler']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/ekskul'); ?>">Ekstrakurikuler</a></li>
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
                     <h3 class="widget-user-username">Ekstrakurikuler</h3>
                     <h5 class="widget-user-desc">Detail Ekstrakurikuler</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_ekskul" id="form_ekskul" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Ekskul </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->id_ekskul); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Ekstrakurikuler </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Foto </label>
                        <div class="col-sm-8">
                             <?php if (is_image($ekskul->foto)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/ekskul/' . $ekskul->foto; ?>">
                                <img src="<?= BASE_URL . 'uploads/ekskul/' . $ekskul->foto; ?>" class="image-responsive" alt="image ekskul" title="foto ekskul" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/ekskul/' . $ekskul->foto; ?>">
                                 <img src="<?= get_icon_file($ekskul->foto); ?>" class="image-responsive" alt="image ekskul" title="foto <?= $ekskul->foto; ?>" width="40px"> 
                               <?= $ekskul->foto ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Judul </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->judul); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Konten </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->konten); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Hari </label>

                        <div class="col-sm-8">
                          <?= join_multi_select($ekskul->hari, '', '', ''); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jam </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->jam); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Posting </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->tanggal_posting); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nominal Biaya </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->nominal_biaya); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Link Pendaftaran </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->link_daftar); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->jenjang); ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->tahun_ajaran_aktif); ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Semester </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->semester_aktif); ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Bank </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->bank); ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Rekening </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->rekening); ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Atas Nama Rekening </label>

                        <div class="col-sm-8">
                           <?= _ent($ekskul->atas_nama_rekening); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('ekskul_update', function() use ($ekskul){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit ekskul (Ctrl+e)" href="<?= site_url('administrator/ekskul/edit/'.$ekskul->id_ekskul); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Ekskul']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/ekskul/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Ekskul']); ?></a>
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
