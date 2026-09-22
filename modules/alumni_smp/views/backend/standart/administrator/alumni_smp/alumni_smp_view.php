
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Alumni Smp      <small><?= cclang('detail', ['Alumni Smp']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/alumni_smp'); ?>">Alumni Smp</a></li>
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
                     <h3 class="widget-user-username">Alumni Smp</h3>
                     <h5 class="widget-user-desc">Detail Alumni Smp</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_alumni_smp" id="form_alumni_smp" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Alumni </label>

                        <div class="col-sm-8">
                           <?= _ent($alumni_smp->id_alumni); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Siswa Aktif </label>

                        <div class="col-sm-8">
                           <?= _ent($alumni_smp->siswa_smp_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> File Ijazah </label>
                        <div class="col-sm-8">
                             <?php if (is_image($alumni_smp->file_ijazah)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/alumni_smp/' . $alumni_smp->file_ijazah; ?>">
                                <img src="<?= BASE_URL . 'uploads/alumni_smp/' . $alumni_smp->file_ijazah; ?>" class="image-responsive" alt="image alumni_smp" title="file_ijazah alumni_smp" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/alumni_smp/' . $alumni_smp->file_ijazah; ?>">
                                 <img src="<?= get_icon_file($alumni_smp->file_ijazah); ?>" class="image-responsive" alt="image alumni_smp" title="file_ijazah <?= $alumni_smp->file_ijazah; ?>" width="40px"> 
                               <?= $alumni_smp->file_ijazah ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                      
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('alumni_smp_update', function() use ($alumni_smp){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit alumni_smp (Ctrl+e)" href="<?= site_url('administrator/alumni_smp/edit/'.$alumni_smp->id_alumni); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Alumni Smp']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/alumni_smp/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Alumni Smp']); ?></a>
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
