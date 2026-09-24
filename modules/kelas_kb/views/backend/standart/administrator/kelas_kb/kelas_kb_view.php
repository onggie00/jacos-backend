
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Kelas Kb      <small><?= cclang('detail', ['Kelas Kb']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/kelas_kb'); ?>">Kelas Kb</a></li>
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
                     <h3 class="widget-user-username">Kelas Kb</h3>
                     <h5 class="widget-user-desc">Detail Kelas Kb</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_kelas_kb" id="form_kelas_kb" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Kelas Kb </label>

                        <div class="col-sm-8">
                           <?= _ent($kelas_kb->id_kelas_kb); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tingkatan </label>

                        <div class="col-sm-8">
                           <?= _ent($kelas_kb->tingkatan_kb_label); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Kelas </label>

                        <div class="col-sm-8">
                           <?= _ent($kelas_kb->nama_kelas); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('kelas_kb_update', function() use ($kelas_kb){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit kelas_kb (Ctrl+e)" href="<?= site_url('administrator/kelas_kb/edit/'.$kelas_kb->id_kelas_kb); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Kelas Kb']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/kelas_kb/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Kelas Kb']); ?></a>
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
