
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pengaturan Akun      <small><?= cclang('detail', ['Pengaturan Akun']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pengaturan_akun'); ?>">Pengaturan Akun</a></li>
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
                     <h3 class="widget-user-username">Pengaturan Akun</h3>
                     <h5 class="widget-user-desc">Detail Pengaturan Akun</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pengaturan_akun" id="form_pengaturan_akun" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Pengaturan Akun </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_akun->id_pengaturan_akun); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Pengaturan </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_akun->name_setting); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Value </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_akun->value); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pengaturan_akun_update', function() use ($pengaturan_akun){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pengaturan_akun (Ctrl+e)" href="<?= site_url('administrator/pengaturan_akun/edit/'.$pengaturan_akun->id_pengaturan_akun); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pengaturan Akun']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pengaturan_akun/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pengaturan Akun']); ?></a>
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
