
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Evaluasi Acara Form      <small><?= cclang('detail', ['Evaluasi Acara Form']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/acara_evaluasi_form'); ?>">Evaluasi Acara Form</a></li>
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
                     <h3 class="widget-user-username">Evaluasi Acara Form</h3>
                     <h5 class="widget-user-desc">Detail Evaluasi Acara Form</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_acara_evaluasi_form" id="form_acara_evaluasi_form" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Evaluasi Form </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_evaluasi_form->id_evaluasi_form); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Acara </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_evaluasi_form->acara_nama_acara); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Pertanyaan </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_evaluasi_form->pertanyaan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tipe Pertanyaan </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_evaluasi_form->tipe_pertanyaan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Wajib Diisi? </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_evaluasi_form->is_required); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jawaban Pertanyaan </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_evaluasi_form->jawaban_pertanyaan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nomor Pertanyaan </label>

                        <div class="col-sm-8">
                           <?= _ent($acara_evaluasi_form->no_urut); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('acara_evaluasi_form_update', function() use ($acara_evaluasi_form){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit acara_evaluasi_form (Ctrl+e)" href="<?= site_url('administrator/acara_evaluasi_form/edit/'.$acara_evaluasi_form->id_evaluasi_form); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Acara Evaluasi Form']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/acara_evaluasi_form/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Acara Evaluasi Form']); ?></a>
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
