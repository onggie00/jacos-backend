
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pangkat Riwayat      <small><?= cclang('detail', ['Pangkat Riwayat']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pangkat_riwayat'); ?>">Pangkat Riwayat</a></li>
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
                     <h3 class="widget-user-username">Pangkat Riwayat</h3>
                     <h5 class="widget-user-desc">Detail Pangkat Riwayat</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pangkat_riwayat" id="form_pangkat_riwayat" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Pangkat </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->id_pangkat); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Unit </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->unit); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Role </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->presensi_setting_role_nama_role); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">User </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->pegawai_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">NPP </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nomor </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->nomor); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pangkat </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->pangkat); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Golongan </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->golongan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">TMT </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->tmt); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">SK Calon? </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->is_sk_calon); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Sudah Maksimal? </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->is_cant_promoted); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->updated_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Deleted At </label>

                        <div class="col-sm-8">
                           <?= _ent($pangkat_riwayat->deleted_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pangkat_riwayat_update', function() use ($pangkat_riwayat){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pangkat_riwayat (Ctrl+e)" href="<?= site_url('administrator/pangkat_riwayat/edit/'.$pangkat_riwayat->id_pangkat); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pangkat Riwayat']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pangkat_riwayat/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pangkat Riwayat']); ?></a>
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
