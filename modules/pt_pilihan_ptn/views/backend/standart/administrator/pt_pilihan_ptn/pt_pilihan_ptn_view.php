
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pilihan PTN      <small><?= cclang('detail', ['Pilihan PTN']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pt_pilihan_ptn'); ?>">Pilihan PTN</a></li>
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
                     <h3 class="widget-user-username">Pilihan PTN</h3>
                     <h5 class="widget-user-desc">Detail Pilihan PTN</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pt_pilihan_ptn" id="form_pt_pilihan_ptn" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_pilihan_ptn->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_pilihan_ptn->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Siswa </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_pilihan_ptn->siswa_ft_aktif_nama_lengkap); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Perguruan Tinggi </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_pilihan_ptn->pt_perguruan_tinggi_nama_pt); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jurusan </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_pilihan_ptn->pt_jurusan_jurusan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Created At </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_pilihan_ptn->created_at); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Updated At </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_pilihan_ptn->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pt_pilihan_ptn_update', function() use ($pt_pilihan_ptn){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pt_pilihan_ptn (Ctrl+e)" href="<?= site_url('administrator/pt_pilihan_ptn/edit/'.$pt_pilihan_ptn->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pt Pilihan Ptn']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pt_pilihan_ptn/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pt Pilihan Ptn']); ?></a>
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
