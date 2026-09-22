
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Presensi Office Submission      <small><?= cclang('detail', ['Presensi Office Submission']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/presensi_office_submission'); ?>">Presensi Office Submission</a></li>
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
                     <h3 class="widget-user-username">Presensi Office Submission</h3>
                     <h5 class="widget-user-desc">Detail Presensi Office Submission</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_presensi_office_submission" id="form_presensi_office_submission" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Submission </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->id_submission); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">NPP </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->npp); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Lengkap </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->presensi_submission_type_name); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->notes); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File </label>
                        <div class="col-sm-8">
                             <?php if (is_image($presensi_office_submission->file_submission)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/presensi_office_submission/' . $presensi_office_submission->file_submission; ?>">
                                <img src="<?= BASE_URL . 'uploads/presensi_office_submission/' . $presensi_office_submission->file_submission; ?>" class="image-responsive" alt="image presensi_office_submission" title="file_submission presensi_office_submission" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/presensi_office_submission/' . $presensi_office_submission->file_submission; ?>">
                                 <img src="<?= get_icon_file($presensi_office_submission->file_submission); ?>" class="image-responsive" alt="image presensi_office_submission" title="file_submission <?= $presensi_office_submission->file_submission; ?>" width="40px"> 
                               <?= $presensi_office_submission->file_submission ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Approval </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->submission_status); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->date_start); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->date_end); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Role </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->presensi_setting_role_nama_role); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Head Role (optional) </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->head_role); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Diajukan </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Diubah </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->updated_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Diubah Oleh </label>

                        <div class="col-sm-8">
                           <?= _ent($presensi_office_submission->updated_by); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('presensi_office_submission_update', function() use ($presensi_office_submission){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit presensi_office_submission (Ctrl+e)" href="<?= site_url('administrator/presensi_office_submission/edit/'.$presensi_office_submission->id_submission); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Presensi Office Submission']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/presensi_office_submission/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Presensi Office Submission']); ?></a>
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
