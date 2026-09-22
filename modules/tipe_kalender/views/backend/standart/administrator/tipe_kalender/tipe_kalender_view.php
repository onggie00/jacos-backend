
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Tipe Agenda      <small><?= cclang('detail', ['Tipe Agenda']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/tipe_kalender'); ?>">Tipe Agenda</a></li>
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
                     <h3 class="widget-user-username">Tipe Agenda</h3>
                     <h5 class="widget-user-desc">Detail Tipe Agenda</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_tipe_kalender" id="form_tipe_kalender" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Tipe </label>

                        <div class="col-sm-8">
                           <?= _ent($tipe_kalender->id_tipe); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Tipe </label>

                        <div class="col-sm-8">
                           <?= _ent($tipe_kalender->nama_tipe); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Warna Hexcode </label>

                        <div class="col-sm-8">
                           <?= _ent($tipe_kalender->warna_hexcode); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('tipe_kalender_update', function() use ($tipe_kalender){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit tipe_kalender (Ctrl+e)" href="<?= site_url('administrator/tipe_kalender/edit/'.$tipe_kalender->id_tipe); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Tipe Kalender']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/tipe_kalender/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Tipe Kalender']); ?></a>
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
