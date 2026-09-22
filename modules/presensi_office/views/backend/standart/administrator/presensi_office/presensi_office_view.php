
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Presensi Office      <small><?= cclang('detail', ['Presensi Office']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/presensi_office'); ?>">Presensi Office</a></li>
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
                     <h3 class="widget-user-username">Presensi Office</h3>
                     <h5 class="widget-user-desc">Detail Presensi Office</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_presensi_office" id="form_presensi_office" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Presensi </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->id_presensi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">NPP </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Date </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->presensi_date); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Hari </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->presensi_hari); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Check In </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->check_in); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Check Out </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->check_out); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Role </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->role); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Device </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->presensi_device); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->status_presensi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Presensi Pulang </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->status_presensi_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Report </label>
                        <div class="col-sm-8">
                             <?php if (is_image($presensi_office->file_report)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/presensi_office/' . $presensi_office->file_report; ?>">
                                <img src="<?= BASE_URL . 'uploads/presensi_office/' . $presensi_office->file_report; ?>" class="image-responsive" alt="image presensi_office" title="file_report presensi_office" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/presensi_office/' . $presensi_office->file_report; ?>">
                                 <img src="<?= get_icon_file($presensi_office->file_report); ?>" class="image-responsive" alt="image presensi_office" title="file_report <?= $presensi_office->file_report; ?>" width="40px"> 
                               <?= $presensi_office->file_report ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Report Status </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->report_status); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Izin </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->id_izin); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->updated_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated By </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->updated_by); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Deleted At </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office->deleted_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('presensi_office_update', function() use ($presensi_office){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit presensi_office (Ctrl+e)" href="<?= site_url('administrator/presensi_office/edit/'.$presensi_office->id_presensi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Presensi Office']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/presensi_office/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Presensi Office']); ?></a>
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
