
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Program Anggaran Smp      <small><?= cclang('detail', ['Program Anggaran Smp']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/program_anggaran_smp'); ?>">Program Anggaran Smp</a></li>
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
                     <h3 class="widget-user-username">Program Anggaran Smp</h3>
                     <h5 class="widget-user-desc">Detail Program Anggaran Smp</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_program_anggaran_smp" id="form_program_anggaran_smp" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nomor Program </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->program_anggaran_nomor_program); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->tahun_ajaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Program </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->nama_program); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Program </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->tanggal_program); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Kegiatan </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->jenis_kegiatan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Sub Jenis Kegiatan </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->sub_jenis_kegiatan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nominal OKR </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->nominal_okr); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nominal Pengajuan </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->nominal_pengajuan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Proposal Pengajuan </label>
                        <div class="col-sm-8">
                             <?php if (is_image($program_anggaran_smp->file_proposal_pengajuan)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/program_anggaran_smp/' . $program_anggaran_smp->file_proposal_pengajuan; ?>">
                                <img src="<?= BASE_URL . 'uploads/program_anggaran_smp/' . $program_anggaran_smp->file_proposal_pengajuan; ?>" class="image-responsive" alt="image program_anggaran_smp" title="file_proposal_pengajuan program_anggaran_smp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/program_anggaran_smp/' . $program_anggaran_smp->file_proposal_pengajuan; ?>">
                                 <img src="<?= get_icon_file($program_anggaran_smp->file_proposal_pengajuan); ?>" class="image-responsive" alt="image program_anggaran_smp" title="file_proposal_pengajuan <?= $program_anggaran_smp->file_proposal_pengajuan; ?>" width="40px"> 
                               <?= $program_anggaran_smp->file_proposal_pengajuan ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Proposal Keuangan </label>
                        <div class="col-sm-8">
                             <?php if (is_image($program_anggaran_smp->file_proposal_keuangan)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/program_anggaran_smp/' . $program_anggaran_smp->file_proposal_keuangan; ?>">
                                <img src="<?= BASE_URL . 'uploads/program_anggaran_smp/' . $program_anggaran_smp->file_proposal_keuangan; ?>" class="image-responsive" alt="image program_anggaran_smp" title="file_proposal_keuangan program_anggaran_smp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/program_anggaran_smp/' . $program_anggaran_smp->file_proposal_keuangan; ?>">
                                 <img src="<?= get_icon_file($program_anggaran_smp->file_proposal_keuangan); ?>" class="image-responsive" alt="image program_anggaran_smp" title="file_proposal_keuangan <?= $program_anggaran_smp->file_proposal_keuangan; ?>" width="40px"> 
                               <?= $program_anggaran_smp->file_proposal_keuangan ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Pengajuan </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->program_anggaran_status_pengajuan_status); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Pencairan Dana </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->tanggal_pencairan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Catatan Pengajuan </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->catatan_pengajuan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Laporan Kegiatan </label>
                        <div class="col-sm-8">
                             <?php if (is_image($program_anggaran_smp->file_laporan_kegiatan)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/program_anggaran_smp/' . $program_anggaran_smp->file_laporan_kegiatan; ?>">
                                <img src="<?= BASE_URL . 'uploads/program_anggaran_smp/' . $program_anggaran_smp->file_laporan_kegiatan; ?>" class="image-responsive" alt="image program_anggaran_smp" title="file_laporan_kegiatan program_anggaran_smp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/program_anggaran_smp/' . $program_anggaran_smp->file_laporan_kegiatan; ?>">
                                 <img src="<?= get_icon_file($program_anggaran_smp->file_laporan_kegiatan); ?>" class="image-responsive" alt="image program_anggaran_smp" title="file_laporan_kegiatan <?= $program_anggaran_smp->file_laporan_kegiatan; ?>" width="40px"> 
                               <?= $program_anggaran_smp->file_laporan_kegiatan ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Laporan Keuangan </label>
                        <div class="col-sm-8">
                             <?php if (!empty($program_anggaran_smp->file_laporan_keuangan)): ?>
                             <?php foreach (explode(',', $program_anggaran_smp->file_laporan_keuangan) as $filename): ?>
                               <?php if (is_image($program_anggaran_smp->file_laporan_keuangan)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/program_anggaran_smp/' . $filename; ?>">
                                  <img src="<?= BASE_URL . 'uploads/program_anggaran_smp/' . $filename; ?>" class="image-responsive" alt="image program_anggaran_smp" title="file_laporan_keuangan program_anggaran_smp" width="40px">
                                </a>
                                <?php else: ?>
                                <label>
                                  <a href="<?= BASE_URL . 'administrator/file/download/program_anggaran_smp/' . $filename; ?>">
                                   <img src="<?= get_icon_file($filename); ?>" class="image-responsive" alt="image program_anggaran_smp" title="file_laporan_keuangan <?= $filename; ?>" width="40px"> 
                                 <?= $filename ?>
                               </a>
                               </label>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </div>
                    </div>
                  
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status Laporan </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->program_anggaran_status_laporan_status); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Catatan Laporan </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_smp->catatan_laporan); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('program_anggaran_smp_update', function() use ($program_anggaran_smp){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit program_anggaran_smp (Ctrl+e)" href="<?= site_url('administrator/program_anggaran_smp/edit/'.$program_anggaran_smp->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Program Anggaran Smp']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/program_anggaran_smp/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Program Anggaran Smp']); ?></a>
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
