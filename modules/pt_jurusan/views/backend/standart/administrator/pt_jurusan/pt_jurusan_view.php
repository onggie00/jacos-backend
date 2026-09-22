
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Jurusan      <small><?= cclang('detail', ['Jurusan']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pt_jurusan'); ?>">Jurusan</a></li>
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
                     <h3 class="widget-user-username">Jurusan</h3>
                     <h5 class="widget-user-desc">Detail Jurusan</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pt_jurusan" id="form_pt_jurusan" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_jurusan->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Perguruan Tinggi </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_jurusan->pt_perguruan_tinggi_nama_pt); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jurusan </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_jurusan->jurusan); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Passing Grade </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_jurusan->passing_grade); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Last Update </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_jurusan->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pt_jurusan_update', function() use ($pt_jurusan){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pt_jurusan (Ctrl+e)" href="<?= site_url('administrator/pt_jurusan/edit/'.$pt_jurusan->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pt Jurusan']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pt_jurusan/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pt Jurusan']); ?></a>
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
