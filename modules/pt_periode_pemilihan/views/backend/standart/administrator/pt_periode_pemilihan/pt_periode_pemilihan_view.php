
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Periode Pemilihan PTN      <small><?= cclang('detail', ['Periode Pemilihan PTN']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pt_periode_pemilihan'); ?>">Periode Pemilihan PTN</a></li>
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
                     <h3 class="widget-user-username">Periode Pemilihan PTN</h3>
                     <h5 class="widget-user-desc">Detail Periode Pemilihan PTN</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pt_periode_pemilihan" id="form_pt_periode_pemilihan" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_periode_pemilihan->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Mulai </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_periode_pemilihan->start_date); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tanggal Selesai </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_periode_pemilihan->end_date); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pt_periode_pemilihan_update', function() use ($pt_periode_pemilihan){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pt_periode_pemilihan (Ctrl+e)" href="<?= site_url('administrator/pt_periode_pemilihan/edit/'.$pt_periode_pemilihan->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pt Periode Pemilihan']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pt_periode_pemilihan/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pt Periode Pemilihan']); ?></a>
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
