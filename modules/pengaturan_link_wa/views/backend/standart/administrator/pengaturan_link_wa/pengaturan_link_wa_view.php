
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Pengaturan Link Wa      <small><?= cclang('detail', ['Pengaturan Link Wa']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pengaturan_link_wa'); ?>">Pengaturan Link Wa</a></li>
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
                     <h3 class="widget-user-username">Pengaturan Link Wa</h3>
                     <h5 class="widget-user-desc">Detail Pengaturan Link Wa</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pengaturan_link_wa" id="form_pengaturan_link_wa" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_link_wa->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_link_wa->jenjang); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Link </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_link_wa->link); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pengaturan_link_wa_update', function() use ($pengaturan_link_wa){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pengaturan_link_wa (Ctrl+e)" href="<?= site_url('administrator/pengaturan_link_wa/edit/'.$pengaturan_link_wa->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pengaturan Link Wa']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pengaturan_link_wa/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pengaturan Link Wa']); ?></a>
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
