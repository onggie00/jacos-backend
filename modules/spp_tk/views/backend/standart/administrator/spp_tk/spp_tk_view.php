
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Spp TK      <small><?= cclang('detail', ['Spp TK']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/spp_tk'); ?>">Spp TK</a></li>
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
                     <h3 class="widget-user-username">Spp TK</h3>
                     <h5 class="widget-user-desc">Detail Spp TK</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_spp_tk" id="form_spp_tk" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->siswa_tk_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->nama); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->kelas); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->tahun_ajaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nominal </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->nominal); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Juli </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->juli); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Agustus </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->agustus); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">September </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->september); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Oktober </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->oktober); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">November </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->november); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Desember </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->desember); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Januari </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->januari); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Februari </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->februari); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Maret </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->maret); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">April </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->april); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Mei </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->mei); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Juni </label>

                        <div class="col-sm-8">
                           <?= _ent($spp_tk->juni); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('spp_tk_update', function() use ($spp_tk){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit spp_tk (Ctrl+e)" href="<?= site_url('administrator/spp_tk/edit/'.$spp_tk->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Spp TK']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/spp_tk/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Spp TK']); ?></a>
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
