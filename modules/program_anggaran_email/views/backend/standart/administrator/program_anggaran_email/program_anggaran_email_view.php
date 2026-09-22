
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Program Anggaran Email (Penerima)      <small><?= cclang('detail', ['Program Anggaran Email (Penerima)']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/program_anggaran_email'); ?>">Program Anggaran Email (Penerima)</a></li>
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
                     <h3 class="widget-user-username">Program Anggaran Email (Penerima)</h3>
                     <h5 class="widget-user-desc">Detail Program Anggaran Email (Penerima)</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_program_anggaran_email" id="form_program_anggaran_email" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Program Anggaran Email </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_email->id_program_anggaran_email); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Email </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_email->email); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_email->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran_email->created_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('program_anggaran_email_update', function() use ($program_anggaran_email){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit program_anggaran_email (Ctrl+e)" href="<?= site_url('administrator/program_anggaran_email/edit/'.$program_anggaran_email->id_program_anggaran_email); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Program Anggaran Email']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/program_anggaran_email/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Program Anggaran Email']); ?></a>
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
