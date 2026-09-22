
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Posisi Guru      <small><?= cclang('detail', ['Posisi Guru']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/posisi_guru'); ?>">Posisi Guru</a></li>
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
                     <h3 class="widget-user-username">Posisi Guru</h3>
                     <h5 class="widget-user-desc">Detail Posisi Guru</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_posisi_guru" id="form_posisi_guru" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Posisi </label>

                        <div class="col-sm-8">
                           <?= _ent($posisi_guru->id_posisi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Posisi </label>

                        <div class="col-sm-8">
                           <?= _ent($posisi_guru->nama_posisi); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('posisi_guru_update', function() use ($posisi_guru){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit posisi_guru (Ctrl+e)" href="<?= site_url('administrator/posisi_guru/edit/'.$posisi_guru->id_posisi); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Posisi Guru']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/posisi_guru/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Posisi Guru']); ?></a>
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
