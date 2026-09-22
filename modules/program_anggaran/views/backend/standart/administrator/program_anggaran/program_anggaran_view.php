
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Program Anggaran      <small><?= cclang('detail', ['Program Anggaran']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/program_anggaran'); ?>">Program Anggaran</a></li>
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
                     <h3 class="widget-user-username">Program Anggaran</h3>
                     <h5 class="widget-user-desc">Detail Program Anggaran</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_program_anggaran" id="form_program_anggaran" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nomor Program </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran->nomor_program); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Program </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran->nama_program); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tahun Ajaran </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran->tahun_ajaran); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenis Kegiatan </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran->jenis_kegiatan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nominal OKR </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran->nominal_okr); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Aktif? </label>

                        <div class="col-sm-8">
                           <?= _ent($program_anggaran->is_active); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('program_anggaran_update', function() use ($program_anggaran){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit program_anggaran (Ctrl+e)" href="<?= site_url('administrator/program_anggaran/edit/'.$program_anggaran->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Program Anggaran']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/program_anggaran' . (!empty($jenjang_context) ? '/' . $jenjang_context : '') . '/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Program Anggaran']); ?></a>
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
