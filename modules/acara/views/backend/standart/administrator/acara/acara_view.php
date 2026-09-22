
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Acara      <small><?= cclang('detail', ['Acara']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/acara'); ?>">Acara</a></li>
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
                     <h3 class="widget-user-username">Acara</h3>
                     <h5 class="widget-user-desc">Detail Acara</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_acara" id="form_acara" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Acara </label>

                        <div class="col-sm-8">
                           <?= _ent($acara->id_acara); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Acara Untuk </label>

                        <div class="col-sm-8">
                          <?= $acara->peserta_acara;/* join_multi_select($acara->peserta_acara, '', '', '') */; ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama </label>

                        <div class="col-sm-8">
                           <?= _ent($acara->nama_acara); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Code In</label>

                        <div class="col-sm-8">
                           <?= _ent($acara->unique_code); ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Code Out</label>

                        <div class="col-sm-8">
                           <?= _ent($acara->unique_code_finish); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Narasumber </label>

                        <div class="col-sm-8">
                           <?= _ent($acara->narasumber); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Keterangan </label>

                        <div class="col-sm-8">
                           <?= _ent($acara->keterangan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> QR Code In</label>
                        <div class="col-sm-8">
                             <?php if (is_image($acara->qr_code)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code; ?>">
                                <img src="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code; ?>" class="image-responsive" alt="image acara" title="qr_code acara" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/acara/' . $acara->qr_code; ?>">
                                 <img src="<?= get_icon_file($acara->qr_code); ?>" class="image-responsive" alt="image acara" title="qr_code <?= $acara->qr_code; ?>" width="40px"> 
                               <?= $acara->qr_code ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> QR Code Out</label>
                        <div class="col-sm-8">
                             <?php if (is_image($acara->qr_code_finish)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code_finish; ?>">
                                <img src="<?= BASE_URL . 'uploads/acara/' . $acara->qr_code_finish; ?>" class="image-responsive" alt="image acara" title="qr_code_finish acara" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/acara/' . $acara->qr_code_finish; ?>">
                                 <img src="<?= get_icon_file($acara->qr_code_finish); ?>" class="image-responsive" alt="image acara" title="qr_code_finish <?= $acara->qr_code_finish; ?>" width="40px"> 
                               <?= $acara->qr_code_finish ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Waktu Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($acara->waktu_mulai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Waktu Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($acara->waktu_selesai); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Lokasi </label>

                        <div class="col-sm-8">
                           <?= _ent($acara->lokasi); ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Evaluation URL </label>

                        <div class="col-sm-8">
                           <?php if (!empty($acara->evaluation_url)): ?>
                           <a href="<?= _ent($acara->evaluation_url); ?>" target="_blank"><i class="fa fa-external-link"></i> <?= _ent($acara->evaluation_url); ?></a>
                           <?php else: ?>
                           <span class="text-muted">-</span>
                           <?php endif; ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Certificated? </label>

                        <div class="col-sm-8">
                           <?= (!empty($acara->is_certificated)) ? '<span class="label label-success">Ya</span>' : '<span class="label label-default">Tidak</span>'; ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nomor Sertifikat </label>

                        <div class="col-sm-8">
                           <?= _ent($acara->no_certificate); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Certificate Template </label>
                        <div class="col-sm-8">
                             <?php if (is_image($acara->file_certificate)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/acara/' . $acara->file_certificate; ?>">
                                <img src="<?= BASE_URL . 'uploads/acara/' . $acara->file_certificate; ?>" class="image-responsive" alt="image acara" title="file_certificate acara" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/acara/' . $acara->file_certificate; ?>">
                                 <img src="<?= get_icon_file($acara->file_certificate); ?>" class="image-responsive" alt="image acara" title="file_certificate <?= $acara->file_certificate; ?>" width="40px"> 
                               <?= $acara->file_certificate ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Certificate Template Back </label>
                        <div class="col-sm-8">
                             <?php if (is_image($acara->file_certificate_back)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/acara/' . $acara->file_certificate_back; ?>">
                                <img src="<?= BASE_URL . 'uploads/acara/' . $acara->file_certificate_back; ?>" class="image-responsive" alt="image acara" title="file_certificate_back acara" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/acara/' . $acara->file_certificate_back; ?>">
                                 <img src="<?= get_icon_file($acara->file_certificate_back); ?>" class="image-responsive" alt="image acara" title="file_certificate_back <?= $acara->file_certificate_back; ?>" width="40px"> 
                               <?= $acara->file_certificate_back ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($acara->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Deleted At </label>

                        <div class="col-sm-8">
                           <?= _ent($acara->deleted_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('acara_update', function() use ($acara){?>
                        <a class="btn btn-flat btn-info btn_action" id="btn_edit" data-stype='back' title="edit acara (Ctrl+e)" href="<?= site_url('administrator/acara/edit/'.$acara->id_acara); ?>"><i class="fa fa-edit"></i> <?= cclang('update', ['Acara']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/acara/'); ?>"><i class="fa fa-undo"></i> <?= cclang('go_list_button', ['Acara']); ?></a>
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
