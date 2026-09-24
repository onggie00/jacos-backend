
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Spp KB      <small><?= cclang('detail', ['Spp KB']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/spp_kb'); ?>">Spp KB</a></li>
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
                     <h3 class="widget-user-username">Spp KB</h3>
                     <h5 class="widget-user-desc">Detail Spp KB</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_spp_kb" id="form_spp_kb" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->siswa_kb_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->kelas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->tahun_ajaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nominal </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->nominal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Juli </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->juli); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Agustus </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->agustus); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">September </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->september); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Oktober </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->oktober); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">November </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->november); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Desember </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->desember); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Januari </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->januari); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Februari </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->februari); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Maret </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->maret); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">April </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->april); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mei </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->mei); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Juni </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_kb->juni); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('spp_kb_update', function() use ($spp_kb){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit spp_kb (Ctrl+e)" href="<?= site_url('administrator/spp_kb/edit/'.$spp_kb->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Spp KB']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/spp_kb/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Spp KB']); ?></a>
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
