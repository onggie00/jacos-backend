
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      UTBK Siswa      <small><?= cclang('detail', ['UTBK Siswa']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pt_to_siswa'); ?>">UTBK Siswa</a></li>
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
                     <h3 class="widget-user-username">UTBK Siswa</h3>
                     <h5 class="widget-user-desc">Detail UTBK Siswa</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pt_to_siswa" id="form_pt_to_siswa" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_to_siswa->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_to_siswa->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_to_siswa->siswa_ft_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_to_siswa->kelas_ft_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nilai UTBK 1 </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_to_siswa->nilai_utbk1); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nilai UTBK 2 </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_to_siswa->nilai_utbk2); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nilai UTBK 3 </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_to_siswa->nilai_utbk3); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nilai UTBK 4 </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_to_siswa->nilai_utbk4); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nilai Nasional </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_to_siswa->nilai_nasional); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_to_siswa->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pt_to_siswa_update', function() use ($pt_to_siswa){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pt_to_siswa (Ctrl+e)" href="<?= site_url('administrator/pt_to_siswa/edit/'.$pt_to_siswa->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pt To Siswa']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pt_to_siswa/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pt To Siswa']); ?></a>
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
